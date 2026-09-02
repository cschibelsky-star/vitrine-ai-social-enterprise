<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientBalanceProvisioningService
{
    public function provision(
        Client $client,
        string $planCode,
        array $allowances,
        CarbonInterface $periodStart,
        ?CarbonInterface $periodEnd = null,
        string $source = 'admin',
    ): void {
        DB::transaction(function () use ($client, $planCode, $allowances, $periodStart, $periodEnd, $source): void {
            $matchingSubscription = ClientSubscription::query()
                ->where('client_id', $client->getKey())
                ->where('plan_code', $planCode)
                ->where('status', 'active')
                ->first();

            if (! $matchingSubscription) {
                ClientSubscription::query()
                    ->where('client_id', $client->getKey())
                    ->where('status', 'active')
                    ->update(['status' => 'replaced']);

                ClientSubscription::query()->create([
                    'client_id' => $client->getKey(),
                    'plan_code' => $planCode,
                    'status' => 'active',
                    'starts_at' => $periodStart,
                    'ends_at' => null,
                    'source' => $source,
                ]);
            }

            foreach ($allowances as $balanceType => $granted) {
                if (! is_numeric($granted)) {
                    throw ValidationException::withMessages([
                        'allowances' => "O saldo inicial de {$balanceType} precisa ser numérico.",
                    ]);
                }

                $granted = (float) $granted;

                if (! is_finite($granted) || $granted < 0 || $granted > 999999999999.99) {
                    throw ValidationException::withMessages([
                        'allowances' => "O saldo inicial de {$balanceType} está fora do limite suportado.",
                    ]);
                }

                $granted = round($granted, 2);

                ClientBalance::query()->firstOrCreate(
                    [
                        'client_id' => $client->getKey(),
                        'balance_type' => $balanceType,
                        'period_start' => $periodStart,
                    ],
                    [
                        'granted' => $granted,
                        'consumed' => 0,
                        'available' => $granted,
                        'period_end' => $periodEnd,
                    ],
                );
            }
        });
    }

    public function renewCurrentPeriod(Client $client, string $source = 'scheduler'): void
    {
        $periodStart = now()->startOfMonth();

        $this->provisionPeriod($client, $periodStart, $source);
    }

    public function provisionNextPeriod(Client $client, string $source = 'scheduler'): void
    {
        $periodStart = now()->addMonthNoOverflow()->startOfMonth();

        $this->provisionPeriod($client, $periodStart, $source);
    }

    private function provisionPeriod(Client $client, CarbonInterface $periodStart, string $source): void
    {
        $subscription = ClientSubscription::query()
            ->where('client_id', $client->getKey())
            ->where('status', 'active')
            ->latest('id')
            ->first();

        $latestContentBalance = ClientBalance::query()
            ->where('client_id', $client->getKey())
            ->where('balance_type', 'content_credit')
            ->orderByDesc('period_start')
            ->orderByDesc('id')
            ->first();

        $this->provision(
            client: $client,
            planCode: $subscription?->plan_code ?? 'essencial',
            allowances: [
                'content_credit' => $latestContentBalance ? (float) $latestContentBalance->granted : 1.00,
            ],
            periodStart: $periodStart,
            periodEnd: $periodStart->copy()->endOfMonth(),
            source: $source,
        );
    }
}
