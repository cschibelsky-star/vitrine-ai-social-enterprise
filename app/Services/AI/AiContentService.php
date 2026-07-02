<?php

namespace App\Services\AI;

use App\Models\ContentGeneration;
use App\Models\ContentProject;
use App\Models\ContentSlide;
use App\Models\PromptTemplate;
use Illuminate\Support\Str;

class AiContentService
{
    public function generateProject(ContentProject $project): array
    {
        $startedAt = microtime(true);
        $brand = $project->brand;
        $template = $this->findTemplate($project);

        $title = $this->buildTitle($project);
        $caption = $this->buildCaption($project, $brand);
        $cta = $this->buildCta($project);
        $hashtags = $this->buildHashtags($project, $brand);
        $score = $this->score($project);
        $slides = $this->buildSlides($project);

        $output = compact('title', 'caption', 'cta', 'hashtags', 'score', 'slides');

        $project->update([
            'title' => $title,
            'caption' => $caption,
            'cta' => $cta,
            'hashtags' => $hashtags,
            'score' => $score,
            'status' => 'editing',
        ]);

        $project->slides()->delete();

        foreach ($slides as $slide) {
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
            'provider' => 'local',
            'model' => 'fake-content-engine-v1',
            'input_data' => [
                'idea' => $project->idea,
                'objective' => $project->objective,
                'format' => $project->format,
                'channel' => $project->channel,
                'brand_id' => $project->brand_id,
            ],
            'output_data' => $output,
            'metadata' => [
                'template_id' => $template?->id,
                'brand_tone' => $brand?->tone_of_voice,
                'target_audience' => $brand?->target_audience,
            ],
            'latency_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return $output;
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
