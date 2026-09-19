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
        $user = auth()->user();
        $clientId = $user?->client_id;

        if (! $clientId) {
            return ['clientId' => null, 'userName' => $user?->name ?: 'Cliente'];
        }

        $base = ContentProject::query()->where('client_id', $clientId);
        $recent = (clone $base)->latest('updated_at')->limit(5)->get();
        $approvalItems = (clone $base)->whereIn('status', ['review', 'pending_approval', 'approval_pending'])->latest('updated_at')->limit(3)->get();
        $calendarItems = (clone $base)->whereNotNull('scheduled_at')->whereNull('published_at')->orderBy('scheduled_at')->limit(8)->get();
        $channels = (clone $base)->whereNotNull('channel')->selectRaw('channel, count(*) as total')->groupBy('channel')->orderByDesc('total')->limit(5)->get();
        $requests = (clone $base)->whereIn('status', ['adjustment_requested', 'changes_requested', 'revision_requested'])->latest('updated_at')->limit(3)->get();
        $subscription = ClientSubscription::query()->where('client_id', $clientId)->latest('id')->first();
        $balances = ClientBalance::query()->where('client_id', $clientId)->get()->keyBy('balance_type');

        return [
            'clientId' => $clientId,
            'userName' => $user?->name ?: 'Cliente',
            'pending' => (clone $base)->whereIn('status', ['draft', 'review', 'pending_approval', 'approval_pending'])->count(),
            'approvals' => (clone $base)->whereIn('status', ['review', 'pending_approval', 'approval_pending'])->count(),
            'scheduled' => (clone $base)->whereNotNull('scheduled_at')->whereNull('published_at')->count(),
            'publishedMonth' => (clone $base)->whereBetween('published_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'scoreAverage' => number_format((float) ((clone $base)->whereNotNull('score')->avg('score') ?? 0), 1, ',', '.'),
            'recent' => $recent,
            'approvalItems' => $approvalItems,
            'calendarItems' => $calendarItems,
            'channels' => $channels,
            'requests' => $requests,
            'subscription' => $subscription,
            'contentBalance' => $balances->get('content_credit'),
        ];
    }
}
