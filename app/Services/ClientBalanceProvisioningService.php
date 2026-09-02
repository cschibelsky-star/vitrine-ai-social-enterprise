<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

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

            foreach ($allowances as $balanceType => $granted) {
                $granted = round((float) $granted, 2);

                if ($granted < 0) {
                    continue;
                }

                ClientBalance::query()->updateOrCreate(
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
