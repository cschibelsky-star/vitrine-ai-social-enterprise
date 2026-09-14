<?php

namespace App\Filament\Client\Widgets;

use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use App\Models\ContentProject;
use Filament\Widgets\Widget;

class ClientCommandCenter extends Widget
{
    protected string $view = 'filament.client.widgets.command-center';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $clientId = auth()->user()?->client_id;

        if (! $clientId) {
            return ['clientId' => null];
        }

        $base = ContentProject::query()->where('client_id', $clientId);
        $recent = (clone $base)->latest('updated_at')->limit(5)->get();
        $subscription = ClientSubscription::query()->where('client_id', $clientId)->latest('id')->first();
        $balances = ClientBalance::query()->where('client_id', $clientId)->get()->keyBy('balance_type');

        return [
            'clientId' => $clientId,
            'pending' => (clone $base)->whereIn('status', ['draft', 'review', 'pending_approval', 'approval_pending'])->count(),
            'approvals' => (clone $base)->whereIn('status', ['review', 'pending_approval', 'approval_pending'])->count(),
            'scheduled' => (clone $base)->whereNotNull('scheduled_at')->whereNull('published_at')->count(),
            'publishedMonth' => (clone $base)->whereBetween('published_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'recent' => $recent,
            'subscription' => $subscription,
            'contentBalance' => $balances->get('content_credit'),
        ];
    }
}
