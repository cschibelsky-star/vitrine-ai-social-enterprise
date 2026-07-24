<?php

namespace App\Services\AI;

use App\Models\ContentProject;
use App\Models\PromptTemplate;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class PromptBuilder
{
    /**
     * @return array{system:string,user:string,context:array<string,mixed>,template_id:int|null}
     */
    public function build(ContentProject $project, ?PromptTemplate $template = null): array
    {
        $project->loadMissing('brand');

        $context = $this->context($project);
        $template ??= $this->resolveTemplate($project);

        return [
            'system' => $this->systemPrompt(),
            'user' => $this->renderUserPrompt($project, $context, $template),
            'context' => $context,
            'template_id' => $template?->id,
        ];
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Você é o motor editorial do Vitrine IA Studio PRO.
Crie conteúdo profissional, claro, persuasivo e adequado ao público informado.
Não invente fatos, preços, garantias, depoimentos ou dados não fornecidos.
Responda somente com JSON válido, sem markdown, comentários ou texto adicional.

Estrutura obrigatória:
{
  "title": "string",
  "caption": "string",
  "cta": "string",
  "hashtags": "string com hashtags separadas por espaço",
  "score": 0.0,
  "slides": [
    {
      "slide_number": 1,
      "title": "string",
      "body": "string",
      "visual_instruction": "string",
      "layout_type": "cover|content|cta"
    }
  ]
}

Regras:
- score deve estar entre 0 e 10;
- title deve ser objetivo e específico;
- caption deve estar pronta para publicação;
- CTA deve combinar com o objetivo;
- hashtags não devem conter vírgulas;
- para formatos sem carrossel, retorne slides como lista vazia;
- para carrossel, gere entre 3 e 10 slides;
- preserve português do Brasil.
PROMPT;
    }

    /**
     * @param array<string,mixed> $context
     */
    private function renderUserPrompt(
        ContentProject $project,
        array $context,
        ?PromptTemplate $template,
    ): string {
        $base = $template?->prompt_text ?: <<<'PROMPT'
Crie um conteúdo sobre {{idea}} para {{channel}}, no formato {{format}}.
Objetivo principal: {{objective}}.
Público-alvo: {{target_audience}}.
Tom de voz: {{tone_of_voice}}.
Marca: {{brand_name}}.
PROMPT;

        $rendered = Str::of($base)->replaceMatches(
            '/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/',
            fn (array $matches): string => (string) Arr::get($context, $matches[1], ''),
        )->trim();

        return $rendered->append("\n\nDados estruturados:\n", json_encode(
            $context,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ))->toString();
    }

    /**
     * @return array<string,mixed>
     */
    private function context(ContentProject $project): array
    {
        $brand = $project->brand;

        return [
            'project_id' => $project->id,
            'idea' => trim((string) $project->idea),
            'objective' => (string) $project->objective,
            'format' => (string) $project->format,
            'channel' => (string) $project->channel,
            'content_type' => (string) $project->content_type,
            'generation_method' => (string) $project->generation_method,
            'brand_name' => $brand?->name ?: 'Marca não informada',
            'tone_of_voice' => $brand?->tone_of_voice ?: 'profissional, claro e próximo',
            'target_audience' => $brand?->target_audience ?: 'público interessado no tema',
        ];
    }

    private function resolveTemplate(ContentProject $project): ?PromptTemplate
    {
        return PromptTemplate::query()
            ->where('is_active', true)
            ->where(function ($query) use ($project): void {
                $query->where('objective', $project->objective)
                    ->orWhere('format', $project->format)
                    ->orWhereNull('objective');
            })
            ->orderByRaw('CASE WHEN objective = ? THEN 0 WHEN format = ? THEN 1 ELSE 2 END', [
                $project->objective,
                $project->format,
            ])
            ->first();
    }
}