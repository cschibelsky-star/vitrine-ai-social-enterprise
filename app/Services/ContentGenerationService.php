<?php

namespace App\Services;

use App\Models\ContentProject;
use App\Models\User;
use App\Services\AI\AiContentService;
use Illuminate\Validation\ValidationException;

class ContentGenerationService
{
    public function __construct(
        private readonly AiContentService $aiContentService,
        private readonly ClientConsumptionService $consumptionService,
    ) {
    }

    public function generate(ContentProject $project, ?User $actor = null): array
    {
        $client = $project->client()->first();

        if (! $client) {
            throw ValidationException::withMessages([
                'client' => 'O projeto de conteúdo precisa estar vinculado a um cliente válido.',
            ]);
        }

        $output = [];

        $this->consumptionService->consume(
            client: $client,
            balanceType: 'content_credit',
            amount: 1.00,
            unit: 'content',
            reference: $project,
            description: 'Geração de conteúdo com IA',
            actor: $actor,
            metadata: [
                'operation' => 'content_generation',
                'content_project_id' => $project->getKey(),
            ],
            operation: function () use ($project, &$output): void {
                $output = $this->aiContentService->generateProject($project);
            },
        );

        return $output;
    }
}
