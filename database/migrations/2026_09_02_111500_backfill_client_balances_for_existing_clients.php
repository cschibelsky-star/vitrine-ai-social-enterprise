<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $periodStart = now()->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        $now = now();

        DB::table('clients')
            ->where('status', 'active')
            ->orderBy('id')
            ->chunkById(100, function ($clients) use ($periodStart, $periodEnd, $now): void {
                foreach ($clients as $client) {
                    $activeSubscription = DB::table('client_subscriptions')
                        ->where('client_id', $client->id)
                        ->where('status', 'active')
                        ->orderByDesc('id')
                        ->first();

                    if (! $activeSubscription) {
                        DB::table('client_subscriptions')->insert([
                            'client_id' => $client->id,
                            'plan_code' => 'essencial',
                            'status' => 'active',
                            'starts_at' => $periodStart,
                            'ends_at' => null,
                            'core_subscription_id' => null,
                            'source' => 'build-1-1-backfill',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }

                    $currentBalanceExists = DB::table('client_balances')
                        ->where('client_id', $client->id)
                        ->where('balance_type', 'content_credit')
                        ->where('period_start', $periodStart)
                        ->exists();

                    if ($currentBalanceExists) {
                        continue;
                    }

                    $latestGranted = DB::table('client_balances')
                        ->where('client_id', $client->id)
                        ->where('balance_type', 'content_credit')
                        ->orderByDesc('period_start')
                        ->orderByDesc('id')
                        ->value('granted');

                    $granted = $latestGranted !== null ? round((float) $latestGranted, 2) : 1.00;

                    DB::table('client_balances')->insert([
                        'client_id' => $client->id,
                        'balance_type' => 'content_credit',
                        'granted' => $granted,
                        'consumed' => 0,
                        'available' => $granted,
                        'period_start' => $periodStart,
                        'period_end' => $periodEnd,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Data backfill is intentionally not reversed to preserve consumption history.
    }
};
