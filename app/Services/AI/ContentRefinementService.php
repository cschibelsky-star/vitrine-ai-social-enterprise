<?php

namespace App\Services\AI;

use App\Models\ContentGeneration;
use App\Models\ContentProject;
use Illuminate\Support\Str;

class ContentRefinementService
{
    public function refine(ContentProject $project, string $action): ContentProject
    {
        $project->refresh();
        $before = $this->snapshot($project);

        $caption = (string) $project->caption;
        $cta = (string) $project->cta;

        [$caption, $cta] = match ($action) {
            'shorten' => [$this->shorten($caption), $cta],
            'expand' => [$this->expand($caption, $project), $cta],
            'persuasive' => [$this->persuasive($caption), $this->persuasiveCta($cta)],
            'emotional' => [$this->emotional($caption), $cta],
            'professional' => [$this->professional($caption), $cta],
            default => [$this->improve($caption), $cta],
        };

        $project->update([
            'caption' => $caption,
            'cta' => $cta,
            'status' => 'editing',
            'score' => min(10, ((float) $project->score) + 0.2),
        ]);

        $project->refresh();

        ContentGeneration::create([
            'content_project_id' => $project->id,
            'provider' => 'local',
            'model' => 'studio-refinement-v1',
            'input_data' => [
                'action' => $action,
                'before' => $before,
            ],
            'output_data' => $this->snapshot($project),
            'metadata' => [
                'type' => 'revision',
                'action' => $action,
                'version' => $project->generations()->count() + 1,
            ],
            'latency_ms' => 1,
        ]);

        return $project;
    }

    public function restore(ContentProject $project, ContentGeneration $generation): ContentProject
    {
        $data = $generation->output_data ?? [];

        $project->update([
            'title' => $data['title'] ?? $project->title,
            'caption' => $data['caption'] ?? $project->caption,
            'cta' => $data['cta'] ?? $project->cta,
            'hashtags' => $data['hashtags'] ?? $project->hashtags,
            'score' => $data['score'] ?? $project->score,
            'status' => 'editing',
        ]);

        return $project->refresh();
    }

    private function snapshot(ContentProject $project): array
    {
        return [
            'title' => $project->title,
            'caption' => $project->caption,
            'cta' => $project->cta,
            'hashtags' => $project->hashtags,
            'score' => $project->score,
            'status' => $project->status,
        ];
    }

    private function improve(string $text): string
    {
        $text = trim($text);
        return $text . "\n\nUma comunicação mais clara, relevante e orientada à ação fortalece a conexão com o público.";
    }

    private function shorten(string $text): string
    {
        return Str::limit(preg_replace('/\s+/', ' ', trim($text)), 420, '…');
    }

    private function expand(string $text, ContentProject $project): string
    {
        return trim($text) . "\n\nAprofundando o tema: {$project->idea}. Considere o contexto, os benefícios e o impacto para o público antes de tomar a próxima decisão.";
    }

    private function persuasive(string $text): string
    {
        return "Existe uma oportunidade importante aqui.\n\n" . trim($text) . "\n\nO melhor momento para avançar é agora.";
    }

    private function persuasiveCta(string $cta): string
    {
        return trim($cta) . ' Dê o próximo passo hoje.';
    }

    private function emotional(string $text): string
    {
        return "Toda transformação começa com uma escolha. 💙\n\n" . trim($text);
    }

    private function professional(string $text): string
    {
        return "Análise objetiva:\n\n" . trim($text) . "\n\nConclusão: uma abordagem estruturada aumenta a clareza e a efetividade da comunicação.";
    }
}
