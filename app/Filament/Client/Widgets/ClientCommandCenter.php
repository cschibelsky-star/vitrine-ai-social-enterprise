<?php

namespace App\Filament\Client\Widgets;

use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use App\Models\ContentProject;
use Carbon\Carbon;
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
            return [
                'clientId' => null,
                'userName' => $user?->name ?: 'Cliente',
            ];
        }

        $base = ContentProject::query()->where('client_id', $clientId);
        $recent = (clone $base)->latest('updated_at')->limit(5)->get();
        $approvalItems = (clone $base)
            ->whereIn('status', ['review', 'pending_approval', 'approval_pending'])
            ->latest('updated_at')
            ->limit(3)
            ->get();
        $requests = (clone $base)
            ->whereIn('status', ['adjustment_requested', 'changes_requested', 'revision_requested'])
            ->latest('updated_at')
            ->limit(3)
            ->get();
        $channels = (clone $base)
            ->whereNotNull('channel')
            ->selectRaw('channel, count(*) as total')
            ->groupBy('channel')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $subscription = ClientSubscription::query()
            ->where('client_id', $clientId)
            ->latest('id')
            ->first();

        $balances = ClientBalance::query()
            ->where('client_id', $clientId)
            ->get()
            ->keyBy('balance_type');

        $contentBalance = $balances->get('content_credit');
        $videoBalance = collect(['video_seconds', 'video_credit', 'video'])
            ->map(fn (string $key) => $balances->get($key))
            ->first(fn ($balance) => $balance !== null);
        $avatarBalance = collect(['avatar_seconds', 'avatar_credit', 'avatar'])
            ->map(fn (string $key) => $balances->get($key))
            ->first(fn ($balance) => $balance !== null);

        $weekStart = now()->startOfWeek(Carbon::SUNDAY)->startOfDay();
        $weekEnd = (clone $weekStart)->addDays(6)->endOfDay();

        $weekProjects = (clone $base)
            ->where(function ($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('scheduled_at', [$weekStart, $weekEnd])
                    ->orWhereBetween('published_at', [$weekStart, $weekEnd]);
            })
            ->orderByRaw('COALESCE(scheduled_at, published_at) asc')
            ->get();

        $calendarDays = collect(range(0, 6))->map(function (int $offset) use ($weekStart, $weekProjects) {
            $date = (clone $weekStart)->addDays($offset);
            $items = $weekProjects->filter(function (ContentProject $project) use ($date) {
                $moment = $project->scheduled_at ?: $project->published_at;

                return $moment?->isSameDay($date) ?? false;
            })->values();

            return [
                'date' => $date,
                'items' => $items,
            ];
        });

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
            'calendarDays' => $calendarDays,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'channels' => $channels,
            'requests' => $requests,
            'subscription' => $subscription,
            'contentBalance' => $contentBalance,
            'videoBalance' => $videoBalance,
            'avatarBalance' => $avatarBalance,
        ];
    }
}
