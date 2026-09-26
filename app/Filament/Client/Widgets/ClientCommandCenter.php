<?php

namespace App\Filament\Client\Widgets;

use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use App\Models\ContentProject;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class ClientCommandCenter extends Widget
{
    protected string $view = 'filament.client.widgets.command-center';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public function approveContent(int $projectId): void
    {
        $clientId = auth()->user()?->client_id;

        if (! $clientId) {
            return;
        }

        $project = ContentProject::query()
            ->where('client_id', $clientId)
            ->findOrFail($projectId);

        $project->forceFill(['status' => 'ready'])->save();

        Notification::make()
            ->title('Conteúdo aprovado')
            ->body('O conteúdo foi liberado para a próxima etapa.')
            ->success()
            ->send();
    }

    public function requestAdjustment(int $projectId): void
    {
        $clientId = auth()->user()?->client_id;

        if (! $clientId) {
            return;
        }

        $project = ContentProject::query()
            ->where('client_id', $clientId)
            ->findOrFail($projectId);

        $project->forceFill(['status' => 'editing'])->save();

        Notification::make()
            ->title('Ajuste solicitado')
            ->body('O conteúdo voltou para revisão.')
            ->warning()
            ->send();
    }

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

        $approvalItems = (clone $base)
            ->with(['slides' => fn ($query) => $query->orderBy('slide_number')])
            ->whereIn('status', ['review', 'pending_approval', 'approval_pending'])
            ->latest('updated_at')
            ->limit(3)
            ->get();

        $requests = (clone $base)
            ->whereIn('status', ['editing', 'adjustment_requested', 'changes_requested', 'revision_requested'])
            ->latest('updated_at')
            ->limit(3)
            ->get();

        $channels = (clone $base)
            ->whereNotNull('channel')
            ->selectRaw('channel, count(*) as total, max(updated_at) as last_activity')
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

        $weekStart = now()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEnd = (clone $weekStart)->addDays(6)->endOfDay();

        $weekProjects = (clone $base)
            ->where(function ($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('scheduled_at', [$weekStart, $weekEnd])
                    ->orWhereBetween('published_at', [$weekStart, $weekEnd]);
            })
            ->orderByRaw('COALESCE(scheduled_at, published_at) asc')
            ->get();

        $upcomingItems = $weekProjects->take(5);

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

        $pending = (clone $base)
            ->whereIn('status', ['draft', 'review', 'pending_approval', 'approval_pending'])
            ->count();

        $approvals = (clone $base)
            ->whereIn('status', ['review', 'pending_approval', 'approval_pending'])
            ->count();

        $scheduledNext7 = (clone $base)
            ->whereNotNull('scheduled_at')
            ->whereNull('published_at')
            ->whereBetween('scheduled_at', [now(), now()->addDays(7)])
            ->count();

        $publishedMonth = (clone $base)
            ->whereBetween('published_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $scoreRaw = (float) ((clone $base)->whereNotNull('score')->avg('score') ?? 0);

        return [
            'clientId' => $clientId,
            'userName' => $user?->name ?: 'Cliente',
            'pending' => $pending,
            'approvals' => $approvals,
            'scheduledNext7' => $scheduledNext7,
            'publishedMonth' => $publishedMonth,
            'scoreAverage' => number_format($scoreRaw, 1, ',', '.'),
            'scorePercent' => max(0, min(100, (int) round($scoreRaw))),
            'approvalItems' => $approvalItems,
            'calendarDays' => $calendarDays,
            'upcomingItems' => $upcomingItems,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'channels' => $channels,
            'requests' => $requests,
            'subscription' => $subscription,
            'contentBalance' => $contentBalance,
            'reachValue' => null,
            'engagementValue' => null,
            'growthValue' => null,
        ];
    }
}

