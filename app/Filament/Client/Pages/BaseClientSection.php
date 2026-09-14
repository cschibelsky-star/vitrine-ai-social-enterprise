<?php

namespace App\Filament\Client\Pages;

use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use App\Models\ContentProject;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

abstract class BaseClientSection extends Page
{
    protected string $view = 'filament.client.pages.section';

    public static string $sectionKey = 'overview';

    public function getViewData(): array
    {
        $user = auth()->user();
        $clientId = $user?->client_id;

        if (! $clientId) {
            return [
                'section' => static::$sectionKey,
                'clientId' => null,
                'items' => collect(),
                'stats' => [],
                'meta' => [],
            ];
        }

        $base = ContentProject::query()->where('client_id', $clientId);
        $section = static::$sectionKey;
        $items = collect();
        $stats = [];
        $meta = [];

        switch ($section) {
            case 'contents':
                $items = (clone $base)->latest('updated_at')->limit(20)->get();
                $stats = [
                    'Total' => (clone $base)->count(),
                    'Em aprovação' => (clone $base)->whereIn('status', ['review', 'pending_approval', 'approval_pending'])->count(),
                    'Agendados' => (clone $base)->whereNotNull('scheduled_at')->whereNull('published_at')->count(),
                    'Publicados' => (clone $base)->whereNotNull('published_at')->count(),
                ];
                break;

            case 'calendar':
                $items = (clone $base)->whereNotNull('scheduled_at')->orderBy('scheduled_at')->limit(30)->get();
                $stats = [
                    'Próximos 7 dias' => (clone $base)->whereBetween('scheduled_at', [now(), now()->addDays(7)])->count(),
                    'Próximos 30 dias' => (clone $base)->whereBetween('scheduled_at', [now(), now()->addDays(30)])->count(),
                    'Já publicados' => (clone $base)->whereNotNull('published_at')->count(),
                ];
                break;

            case 'performance':
                $monthStart = now()->startOfMonth();
                $monthEnd = now()->endOfMonth();
                $items = (clone $base)->whereNotNull('published_at')->latest('published_at')->limit(12)->get();
                $stats = [
                    'Publicados no mês' => (clone $base)->whereBetween('published_at', [$monthStart, $monthEnd])->count(),
                    'Score médio' => number_format((float) ((clone $base)->whereNotNull('score')->avg('score') ?? 0), 1, ',', '.'),
                    'Total publicado' => (clone $base)->whereNotNull('published_at')->count(),
                ];
                break;

            case 'channels':
                $items = (clone $base)
                    ->whereNotNull('channel')
                    ->selectRaw('channel, count(*) as total, max(updated_at) as last_activity')
                    ->groupBy('channel')
                    ->orderByDesc('total')
                    ->get();
                $stats = [
                    'Canais com atividade' => $items->count(),
                    'Conteúdos vinculados' => (clone $base)->whereNotNull('channel')->count(),
                ];
                break;

            case 'balance':
                $items = ClientBalance::query()->where('client_id', $clientId)->orderBy('balance_type')->get();
                $subscription = ClientSubscription::query()->where('client_id', $clientId)->latest('id')->first();
                $stats = [
                    'Plano' => $subscription?->plan_code ?: 'Não informado',
                    'Status' => $subscription?->status ?: 'Não informado',
                    'Saldos ativos' => $items->count(),
                ];
                $meta['subscription'] = $subscription;
                break;

            case 'affiliates':
                $stats = [
                    'Status' => 'Programa em preparação',
                    'Comissão' => 'A definir',
                    'Indicações' => 0,
                ];
                $meta['notice'] = 'O programa de afiliados está visível no painel, mas ainda não possui backend financeiro ativo nesta recovery.';
                break;

            case 'account':
                $stats = [
                    'Nome' => $user?->name ?: '—',
                    'E-mail' => $user?->email ?: '—',
                    'Cliente' => (string) $clientId,
                ];
                break;
        }

        return compact('section', 'clientId', 'items', 'stats', 'meta');
    }
}
