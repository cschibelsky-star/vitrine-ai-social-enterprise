<?php

namespace App\Services\AI;

use App\Models\ContentGeneration;
use App\Models\ContentProject;
use App\Models\ContentSlide;
use App\Models\PromptTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class AiContentService
{
    public function __construct(
        private readonly GeminiContentProvider $gemini,
    ) {}

    public function generateProject(ContentProject $project): array
    {
        $startedAt = microtime(true);
        $brand = $project->brand;
        $template = $this->findTemplate($project);
        $fallbackReason = null;
        $executionId = null;

        $hubResult = $this->generateWithCentroIa($project, $brand, $template);

        if ($hubResult !== null) {
            $output = $hubResult['output'];
            $provider = 'centro-ia';
            $model = (string) ($hubResult['model'] ?: 'hub-routed');
            $executionId = $hubResult['execution_id'] ?? null;
        } else {
            try {
                $output = $this->gemini->generate($project, $brand, $template);
                $provider = 'gemini';
                $model = $this->gemini->model();
                $fallbackReason = 'Centro IA indisponível ou sem resposta válida.';
            } catch (Throwable $exception) {
                $provider = 'local';
                $model = 'local-content-engine-v1';
                $fallbackReason = 'Centro IA indisponível; Gemini falhou: '.$exception->getMessage();
                $output = $this->generateLocally($project, $brand);
            }
        }

        $project->update([
            'title' => $output['title'],
            'caption' => $output['caption'],
            'cta' => $output['cta'],
            'hashtags' => $output['hashtags'],
            'score' => $output['score'],
            'status' => 'editing',
        ]);

        $project->slides()->delete();

        foreach ($output['slides'] as $slide) {
            ContentSlide::create([
                'content_project_id' => $project->id,
                'slide_number' => $slide['slide_number'],
                'title' => $slide['title'],
                'body' => $slide['body'],
                'visual_instruction' => $slide['visual_instruction'],
                'layout_type' => $slide['layout_type'],
            ]);
        }

        ContentGeneration::create([
            'content_project_id' => $project->id,
            'provider' => $provider,
            'model' => $model,
            'input_data' => [
                'idea' => $project->idea,
                'objective' => $project->objective,
                'format' => $project->format,
                'channel' => $project->channel,
                'brand_id' => $project->brand_id,
            ],
            'output_data' => $output,
            'metadata' => array_filter([
                'template_id' => $template?->id,
                'brand_tone' => $brand?->tone_of_voice,
                'target_audience' => $brand?->target_audience,
                'centro_ia_execution_id' => $executionId,
                'centro_ia_enabled' => $provider === 'centro-ia',
                'fallback_reason' => $fallbackReason,
            ], static fn ($value) => $value !== null && $value !== ''),
            'latency_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return $output;
    }

    private function generateWithCentroIa(ContentProject $project, $brand, ?PromptTemplate $template): ?array
    {
        $url = trim((string) config('services.centro_ia.url', ''));
        $token = trim((string) config('services.centro_ia.token', ''));
        $projectId = trim((string) config('services.centro_ia.project_id', 'vitrine-ai-social-enterprise'));
        $capability = trim((string) config('services.centro_ia.capability', 'social_content_generation'));
        $timeout = max(5, (int) config('services.centro_ia.timeout', 30));

        if ($url === '' || $token === '' || $projectId === '' || $capability === '') {
            return null;
        }

        $system = 'Você é o agente de Marketing IA do Hub da Vitrine IA Pro. '
            . 'Gere conteúdo de social mídia pronto para edição humana. '
            . 'Responda SOMENTE JSON válido com: title, caption, cta, hashtags, score e slides. '
            . 'hashtags deve ser string. score deve ser número de 0 a 10. '
            . 'slides deve ser array com exatamente 3 itens contendo slide_number, title, body, visual_instruction e layout_type. '
            . 'Não invente dados factuais que não estejam na solicitação.';

        $user = json_encode([
            'idea' => $project->idea,
            'objective' => $project->objective,
            'format' => $project->format,
            'channel' => $project->channel,
            'brand' => [
                'name' => $brand?->name,
                'tone_of_voice' => $brand?->tone_of_voice,
                'target_audience' => $brand?->target_audience,
            ],
            'template' => $template ? [
                'name' => $template->name ?? null,
                'prompt' => $template->prompt ?? null,
            ] : null,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withToken($token)
                ->withHeaders(['X-Vitrine-Project' => $projectId])
                ->timeout($timeout)
                ->post($url, [
                    'project_id' => $projectId,
                    'capability' => $capability,
                    'input' => [
                        'system' => $system,
                        'user' => $user,
                        'response_format' => 'json',
                        'temperature' => 0.45,
                    ],
                ]);

            if (! $response->successful() || ! $response->json('ok')) {
                return null;
            }

            $decoded = $this->decodeHubOutput((string) $response->json('output_text', ''));

            if ($decoded === null) {
                return null;
            }

            return [
                'output' => $decoded,
                'model' => $response->json('model'),
                'execution_id' => $response->json('execution_id'),
            ];
        } catch (Throwable) {
            return null;
        }
    }

    private function decodeHubOutput(string $raw): ?array
    {
        $raw = trim($raw);
        $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw) ?? $raw;
        $raw = preg_replace('/\s*```$/', '', $raw) ?? $raw;
        $data = json_decode($raw, true);

        if (! is_array($data)) {
            return null;
        }

        foreach (['title', 'caption', 'cta', 'hashtags', 'score', 'slides'] as $key) {
            if (! array_key_exists($key, $data)) {
                return null;
            }
        }

        if (! is_array($data['slides']) || count($data['slides']) !== 3) {
            return null;
        }

        $slides = [];

        foreach (array_values($data['slides']) as $index => $slide) {
            if (! is_array($slide)) {
                return null;
            }

            $slides[] = [
                'slide_number' => (int) ($slide['slide_number'] ?? ($index + 1)),
                'title' => trim((string) ($slide['title'] ?? '')),
                'body' => trim((string) ($slide['body'] ?? '')),
                'visual_instruction' => trim((string) ($slide['visual_instruction'] ?? '')),
                'layout_type' => trim((string) ($slide['layout_type'] ?? 'content')),
            ];
        }

        return [
            'title' => trim((string) $data['title']),
            'caption' => trim((string) $data['caption']),
            'cta' => trim((string) $data['cta']),
            'hashtags' => is_array($data['hashtags']) ? implode(' ', $data['hashtags']) : trim((string) $data['hashtags']),
            'score' => min(10, max(0, (float) $data['score'])),
            'slides' => $slides,
        ];
    }

    private function findTemplate(ContentProject $project): ?PromptTemplate
    {
        return PromptTemplate::query()
            ->where('is_active', true)
            ->where(function ($query) use ($project) {
                $query->where('objective', $project->objective)
                    ->orWhere('format', $project->format)
                    ->orWhereNull('objective');
            })
            ->first();
    }

    private function generateLocally(ContentProject $project, $brand = null): array
    {
        $title = $this->buildTitle($project);
        $caption = $this->buildCaption($project, $brand);
        $cta = $this->buildCta($project);
        $hashtags = $this->buildHashtags($project, $brand);
        $score = $this->score($project);
        $slides = $this->buildSlides($project);

        return compact('title', 'caption', 'cta', 'hashtags', 'score', 'slides');
    }

    private function buildTitle(ContentProject $project): string
    {
        return match ($project->objective) {
            'sales' => 'Oferta especial para você',
            'education' => 'Aprenda isso antes de decidir',
            'authority' => 'O que quase ninguém te explica',
            'community' => 'Uma mensagem para nossa comunidade',
            default => 'Conteúdo criado com IA',
        };
    }

    private function buildCaption(ContentProject $project, $brand = null): string
    {
        $tone = $brand?->tone_of_voice ?: 'profissional, claro e próximo';
        $audience = $brand?->target_audience ?: 'público interessado no tema';

        return "Você pediu um conteúdo sobre: {$project->idea}\n\n"
            . "Pensando em {$audience}, criamos uma mensagem com tom {$tone}, focada em {$project->objective}. "
            . "A ideia é comunicar valor de forma simples, gerar interesse e conduzir o público para a próxima ação.\n\n"
            . "Este conteúdo pode ser usado em {$project->channel} no formato {$project->format}.";
    }

    private function buildCta(ContentProject $project): string
    {
        return match ($project->objective) {
            'sales' => 'Chame no WhatsApp e saiba como aproveitar.',
            'education' => 'Salve este conteúdo para consultar depois.',
            'authority' => 'Siga o perfil para receber mais orientações.',
            'community' => 'Compartilhe com alguém que precisa ver isso.',
            default => 'Comente sua opinião aqui embaixo.',
        };
    }

    private function buildHashtags(ContentProject $project, $brand = null): string
    {
        $base = ['#VitrineAI', '#ConteudoComIA', '#MarketingDigital'];

        if ($project->channel === 'instagram') {
            $base[] = '#InstagramMarketing';
        }

        if ($brand?->name) {
            $base[] = '#' . Str::studly($brand->name);
        }

        return implode(' ', array_unique($base));
    }

    private function buildSlides(ContentProject $project): array
    {
        return [
            [
                'slide_number' => 1,
                'title' => 'Gancho principal',
                'body' => 'Comece chamando atenção para o problema ou desejo do público.',
                'visual_instruction' => 'Use título grande, fundo limpo e elemento visual forte.',
                'layout_type' => 'cover',
            ],
            [
                'slide_number' => 2,
                'title' => 'Desenvolvimento',
                'body' => 'Explique o valor da ideia de forma simples e objetiva.',
                'visual_instruction' => 'Use blocos curtos de texto e ícones de apoio.',
                'layout_type' => 'content',
            ],
            [
                'slide_number' => 3,
                'title' => 'Chamada para ação',
                'body' => $this->buildCta($project),
                'visual_instruction' => 'Destaque o CTA com botão ou área de contraste.',
                'layout_type' => 'cta',
            ],
        ];
    }

    private function score(ContentProject $project): float
    {
        return match ($project->objective) {
            'sales' => 8.6,
            'education' => 8.8,
            'authority' => 8.7,
            default => 8.4,
        };
    }
}
