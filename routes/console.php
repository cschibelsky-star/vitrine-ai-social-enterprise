<?php

use App\Models\Client;
use App\Services\ClientBalanceProvisioningService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('social:renew-client-balances', function (ClientBalanceProvisioningService $provisioningService) {
    Client::query()
        ->where('status', 'active')
        ->chunkById(100, function ($clients) use ($provisioningService): void {
            foreach ($clients as $client) {
                $provisioningService->renewCurrentPeriod($client);
            }
        });

    $this->info('Client balances renewed for the current period.');
})->purpose('Ensure active clients have a current-period content balance');

Schedule::command('social:renew-client-balances')
    ->dailyAt('00:10')
    ->withoutOverlapping();
