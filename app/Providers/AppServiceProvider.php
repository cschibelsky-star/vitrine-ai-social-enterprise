<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        if (request()->is('app', 'app/*')) {
            $webUser = Auth::guard('web')->user();

            if ($webUser && ($webUser->role !== 'client' || $webUser->client_id === null)) {
                Auth::guard('web')->logout();
            }

            Filament::setCurrentPanel(Filament::getPanel('client'));

            return;
        }

        if (request()->is('admin', 'admin/*')) {
            $adminUser = Auth::guard('admin')->user();

            if ($adminUser && ! in_array($adminUser->role, ['admin', 'operator'], true)) {
                Auth::guard('admin')->logout();
            }

            Filament::setCurrentPanel(Filament::getPanel('admin'));
        }
    }
}
