<?php

namespace App\Services\AI;

/**
 * Deterministic, offline editorial preview for a Reel. Never approves or publishes.
 * Scores are heuristic drafting aids, not predictions of Instagram performance.
 */
final class ReelDirectorService
{
    public function preview(array $context): array
    {
        $idea = trim(preg_replace('/\s+/u', ' ', (string) ($context['idea'] ?? '')) ?? '');
        $caption = trim((string) ($context['caption'] ?? ''));
        $cta = trim((string) ($context['cta'] ?? ''));
        $target = max(15, min(45, (int) ($context['target_seconds'] ?? 30)));
        $warnings = [];

        if ($this->wordCount($idea) < 3) {
            return [
                'status' => 'briefing_required',
                'hooks' => [],
                'beats' => [],
                'duration_seconds' => 0,
                'target_seconds' => $target,
                'warnings' => ['Descreva um tema concreto com pelo menos três palavras antes da análise.'],
                'human_approval_required' => true,
            ];
        }

        $topic = rtrim($idea, " .!?\t\n\r");
        $candidates = [
            ['formula' => '#18 Cold Open Demo', 'spoken' => 'Veja de perto: '.$topic.'.'],
            ['formula' => '#16 Verbatim Question', 'spoken' => 'Você já conhece '.$topic.'?'],
            ['formula' => '#24 The Reveal', 'spoken' => 'Aqui está: '.$topic.'.'],
        ];

        $hooks = [];
        foreach ($candidates as $candidate) {
            $words = $this->wordCount($candidate['spoken']);
            $score = max(0, min(100, 45 + min(25, $this->wordCount($topic) * 3) - max(0, $words - 12) * 5));
            $hooks[] = $candidate + [
                'heuristic_score' => $score,
                'estimated_seconds' => round($words * 60 / 150, 1),
            ];
        }
        usort($hooks, static fn (array $a, array $b): int => $b['heuristic_score'] <=> $a['heuristic_score']);

        if ($caption === '') {
            $warnings[] = 'Inclua uma legenda ou texto-base revisado para analisar os beats do roteiro.';
        }
        if ($cta === '') {
            $warnings[] = 'Defina uma única chamada para ação antes da gravação.';
        }

        $forbidden = (array) ($context['forbidden_words'] ?? []);
        foreach ($forbidden as $word) {
            $word = trim((string) $word);
            if ($word !== '' && preg_match('/'.preg_quote($word, '/').'/iu', $idea.' '.$caption.' '.$cta)) {
                $warnings[] = 'Expressão bloqueada pelo Brand Kit: '.$word;
            }
        }

        $lines = [['type' => 'hook', 'text' => $hooks[0]['spoken']]];
        $captionWords = preg_split('/\s+/u', $caption, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        foreach (array_slice(array_chunk($captionWords, 8), 0, 8) as $chunk) {
            $lines[] = ['type' => 'body', 'text' => implode(' ', $chunk)];
        }
        if ($cta !== '') {
            $lines[] = ['type' => 'cta', 'text' => $cta];
        }
        if (count($captionWords) > 64) {
            $warnings[] = 'Texto-base extenso: esta prévia exibe somente as primeiras 64 palavras.';
        }

        $beats = [];
        $elapsed = 0.0;
        foreach ($lines as $line) {
            $seconds = round($this->wordCount($line['text']) * 60 / 150, 1);
            $beats[] = $line + ['start_seconds' => $elapsed, 'duration_seconds' => $seconds];
            if ($line['type'] === 'hook' && $seconds > 3) {
                $warnings[] = 'O gancho ultrapassa 3 segundos; encurte antes de gravar.';
            }
            if ($seconds > 4) {
                $warnings[] = 'Um beat ultrapassa 4 segundos; altere o enquadramento ou encurte.';
            }
            $elapsed = round($elapsed + $seconds, 1);
        }
        if ($elapsed > $target) {
            $warnings[] = 'A duração estimada ultrapassa a meta de '.$target.' segundos.';
        }
        $warnings[] = 'Confirme fatos, promessas e direitos de uso antes da aprovação humana.';

        return [
            'status' => 'review_required',
            'method' => 'offline_heuristic_v1',
            'hooks' => $hooks,
            'beats' => $beats,
            'duration_seconds' => $elapsed,
            'target_seconds' => $target,
            'warnings' => array_values(array_unique($warnings)),
            'human_approval_required' => true,
        ];
    }

    private function wordCount(string $value): int
    {
        return count(preg_split('/\s+/u', trim($value), -1, PREG_SPLIT_NO_EMPTY) ?: []);
    }
}
