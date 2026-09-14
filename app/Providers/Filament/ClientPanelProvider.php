<?php

namespace App\Providers\Filament;

use App\Filament\Client\Pages\Account;
use App\Filament\Client\Pages\Affiliates;
use App\Filament\Client\Pages\Balance;
use App\Filament\Client\Pages\CalendarPage;
use App\Filament\Client\Pages\Channels;
use App\Filament\Client\Pages\ClientDashboard;
use App\Filament\Client\Pages\Contents;
use App\Filament\Client\Pages\Performance;
use App\Filament\Client\Widgets\ClientBalanceOverview;
use App\Filament\Client\Widgets\ClientCommandCenter;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('app')
            ->login()
            ->brandName('Vitrine Social Mídia')
            ->colors([
                'primary' => Color::Cyan,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.theme.vitrine-ia-style')->render(),
            )
            ->pages([
                ClientDashboard::class,
                Contents::class,
                CalendarPage::class,
                Performance::class,
                Channels::class,
                Balance::class,
                Affiliates::class,
                Account::class,
            ])
            ->widgets([
                AccountWidget::class,
                ClientCommandCenter::class,
                ClientBalanceOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
