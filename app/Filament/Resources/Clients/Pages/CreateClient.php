<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Services\ClientBalanceProvisioningService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    private string $initialPlanCode = 'essencial';

    private float $initialContentCredits = 1.00;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->initialPlanCode = (string) ($data['initial_plan_code'] ?? 'essencial');

        $rawCredits = $data['initial_content_credits'] ?? 1;

        if (! is_numeric($rawCredits)) {
            throw ValidationException::withMessages([
                'initial_content_credits' => 'Os créditos iniciais precisam ser numéricos.',
            ]);
        }

        $credits = (float) $rawCredits;

        if (! is_finite($credits) || $credits < 1 || $credits > 999999999999.99) {
            throw ValidationException::withMessages([
                'initial_content_credits' => 'Os créditos iniciais precisam estar entre 1 e 999999999999,99.',
            ]);
        }

        $this->initialContentCredits = round($credits, 2);

        unset($data['initial_plan_code'], $data['initial_content_credits']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $record = parent::handleRecordCreation($data);
            $periodStart = now()->startOfMonth();

            app(ClientBalanceProvisioningService::class)->provision(
                client: $record,
                planCode: $this->initialPlanCode,
                allowances: [
                    'content_credit' => $this->initialContentCredits,
                ],
                periodStart: $periodStart,
                periodEnd: $periodStart->copy()->endOfMonth(),
                source: 'admin',
            );

            return $record;
        });
    }
}
