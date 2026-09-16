<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\ContentProject;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class AdminCommandCenter extends Widget
{
    protected string $view = 'filament.widgets.admin-command-center';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $content = ContentProject::query();

        return [
            'clients' => Client::query()->count(),
            'pending' => (clone $content)->whereIn('status', ['draft', 'review', 'pending_approval', 'approval_pending'])->count(),
            'approvals' => (clone $content)->whereIn('status', ['review', 'pending_approval', 'approval_pending'])->count(),
            'scheduled' => (clone $content)->whereNotNull('scheduled_at')->whereNull('published_at')->count(),
            'publishedMonth' => (clone $content)->whereBetween('published_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'leads' => DB::table('waitlist_leads')->count(),
            'recent' => (clone $content)->with('client')->latest('updated_at')->limit(8)->get(),
        ];
    }
}
