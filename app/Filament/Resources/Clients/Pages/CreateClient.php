<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Services\ClientBalanceProvisioningService;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    private string $initialPlanCode = 'essencial';

    private float $initialContentCredits = 1.00;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->initialPlanCode = (string) ($data['initial_plan_code'] ?? 'essencial');
        $this->initialContentCredits = round((float) ($data['initial_content_credits'] ?? 1), 2);

        unset($data['initial_plan_code'], $data['initial_content_credits']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $periodStart = now()->startOfMonth();

        app(ClientBalanceProvisioningService::class)->provision(
            client: $this->record,
            planCode: $this->initialPlanCode,
            allowances: [
                'content_credit' => $this->initialContentCredits,
            ],
            periodStart: $periodStart,
            periodEnd: $periodStart->copy()->endOfMonth(),
            source: 'admin',
        );
    }
}
