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
                ->where('starts_at', $periodStart)
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
                    'ends_at' => $periodEnd,
                    'source' => $source,
                ]);
            }

            foreach ($allowances as $balanceType => $granted) {
                if (! is_numeric($granted)) {
                    throw ValidationException::withMessages([
                        'allowances' => "O saldo inicial de {$balanceType} precisa ser numérico.",
                    ]);
                }

                $granted = round((float) $granted, 2);

                if ($granted < 0) {
                    throw ValidationException::withMessages([
                        'allowances' => "O saldo inicial de {$balanceType} não pode ser negativo.",
                    ]);
                }

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
}
