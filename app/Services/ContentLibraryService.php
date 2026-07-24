<?php

namespace App\Services;

use App\Models\ContentProject;
use App\Models\EditorialPlanning;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ContentLibraryService
{
    /**
     * @return array{project: ContentProject, similarity: float}|null
     */
    public function bestReusableMatch(EditorialPlanning $planning, float $minimumSimilarity = 0.90): ?array
    {
        return $this->matches($planning, 10)
            ->first(fn (array $match): bool => $match['similarity'] >= $minimumSimilarity);
    }

    /**
     * @return Collection<int, array{project: ContentProject, similarity: float}>
     */
    public function matches(EditorialPlanning $planning, int $limit = 5): Collection
    {
        $planningTokens = $this->tokens($this->planningText($planning));

        if ($planningTokens === []) {
            return collect();
        }

        return ContentProject::query()
            ->where('client_id', $planning->client_id)
            ->whereNotNull('caption')
            ->where('caption', '!=', '')
            ->when(
                $planning->content_project_id,
                fn ($query, $projectId) => $query->whereKeyNot($projectId),
            )
            ->latest('updated_at')
            ->limit(100)
            ->get()
            ->map(function (ContentProject $project) use ($planning, $planningTokens): array {
                $similarity = $this->jaccard(
                    $planningTokens,
                    $this->tokens($this->projectText($project)),
                );

                if ($project->channel === $planning->channel) {
                    $similarity += 0.05;
                }

                if ($project->format === $planning->format) {
                    $similarity += 0.05;
                }

                return [
                    'project' => $project,
                    'similarity' => min(1, round($similarity, 4)),
                ];
            })
            ->filter(fn (array $match): bool => $match['similarity'] > 0)
            ->sortByDesc('similarity')
            ->take(max(1, $limit))
            ->values();
    }

    public function reuse(ContentProject $source, ContentProject $target): ContentProject
    {
        $target->update([
            'caption' => $source->caption,
            'cta' => $source->cta,
            'hashtags' => $source->hashtags,
            'generation_method' => 'library_reuse',
            'status' => 'writing',
        ]);

        return $target->refresh();
    }

    private function planningText(EditorialPlanning $planning): string
    {
        return implode(' ', array_filter([
            $planning->theme,
            $planning->objective,
            $planning->notes,
            $planning->channel,
            $planning->format,
        ]));
    }

    private function projectText(ContentProject $project): string
    {
        return implode(' ', array_filter([
            $project->title,
            $project->idea,
            $project->objective,
            $project->caption,
            $project->cta,
            $project->hashtags,
            $project->channel,
            $project->format,
        ]));
    }

    /** @return array<int, string> */
    private function tokens(string $text): array
    {
        $stopWords = [
            'a', 'ao', 'aos', 'as', 'com', 'como', 'da', 'das', 'de', 'do', 'dos',
            'e', 'em', 'essa', 'esse', 'esta', 'este', 'para', 'por', 'que', 'se',
            'sem', 'um', 'uma', 'uns', 'umas', 'na', 'nas', 'no', 'nos', 'o', 'os',
        ];

        return collect(preg_split('/[^\pL\pN]+/u', Str::lower(Str::ascii($text))) ?: [])
            ->map(fn (string $token): string => trim($token))
            ->filter(fn (string $token): bool => mb_strlen($token) >= 3 && ! in_array($token, $stopWords, true))
            ->unique()
            ->values()
            ->all();
    }

    /** @param array<int, string> $left @param array<int, string> $right */
    private function jaccard(array $left, array $right): float
    {
        if ($left === [] || $right === []) {
            return 0;
        }

        $intersection = count(array_intersect($left, $right));
        $union = count(array_unique(array_merge($left, $right)));

        return $union > 0 ? $intersection / $union : 0;
    }
}
