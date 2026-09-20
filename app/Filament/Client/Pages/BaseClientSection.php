<?php

namespace App\Filament\Client\Pages;

use App\Models\Brand;
use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use App\Models\ContentProject;
use App\Services\AI\AiContentService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Throwable;

abstract class BaseClientSection extends Page
{
    protected string $view = 'filament.client.pages.section';

    public static string $sectionKey = 'overview';

    public string $idea = '';
    public string $objective = 'engagement';
    public string $format = 'post_portrait';
    public string $channel = 'instagram';
    public ?int $brandId = null;
    public ?int $generatedProjectId = null;
    public array $scheduleInputs = [];

    public function mount(): void
    {
        $clientId = auth()->user()?->client_id;

        if ($clientId && static::$sectionKey === 'contents') {
            $this->brandId = Brand::query()
                ->where('client_id', $clientId)
                ->where('status', 'active')
                ->orderBy('id')
                ->value('id');
        }
    }

    public function generateContent(): void
    {
        $user = auth()->user();
        $clientId = $user?->client_id;

        if (! $clientId) {
            return;
        }

        $data = validator([
            'brand_id' => $this->brandId,
            'idea' => $this->idea,
            'objective' => $this->objective,
            'format' => $this->format,
            'channel' => $this->channel,
        ], [
            'brand_id' => ['required', 'integer'],
            'idea' => ['required', 'string', 'min:10', 'max:2000'],
            'objective' => ['required', 'in:sales,engagement,authority,education,community,institutional,event,launch'],
            'format' => ['required', 'in:post_portrait,carousel_portrait,stories,reels,facebook_post,linkedin_post'],
            'channel' => ['required', 'in:instagram,facebook,linkedin,tiktok,threads,whatsapp'],
        ])->validate();

        $brand = Brand::query()
            ->where('client_id', $clientId)
            ->where('status', 'active')
            ->findOrFail((int) $data['brand_id']);

        $project = ContentProject::create([
            'client_id' => $clientId,
            'brand_id' => $brand->id,
            'idea' => trim((string) $data['idea']),
            'content_type' => 'social',
            'generation_method' => 'from_scratch',
            'objective' => $data['objective'],
            'format' => $data['format'],
            'channel' => $data['channel'],
            'status' => 'draft',
            'created_by' => $user?->id,
        ]);

        try {
            app(AiContentService::class)->generateProject($project);
            $project->refresh();

            $this->generatedProjectId = $project->id;
            $this->idea = '';

            Notification::make()
                ->title('Conteúdo criado')
                ->body('A IA gerou título, legenda, CTA, hashtags e estrutura visual. Revise e aprove quando estiver pronto.')
                ->success()
                ->send();
        } catch (Throwable $exception) {
            $project->delete();

            Notification::make()
                ->title('Não foi possível gerar o conteúdo')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function scheduleContent(int $projectId): void
    {
        $clientId = auth()->user()?->client_id;

        if (! $clientId) {
            return;
        }

        $project = ContentProject::query()
            ->where('client_id', $clientId)
            ->findOrFail($projectId);

        $raw = trim((string) ($this->scheduleInputs[$projectId] ?? ''));

        if ($raw === '') {
            Notification::make()
                ->title('Informe data e horário')
                ->warning()
                ->send();

            return;
        }

        $scheduledAt = Carbon::parse($raw);

        if ($scheduledAt->isPast()) {
            Notification::make()
                ->title('Escolha uma data futura')
                ->warning()
                ->send();

            return;
        }

        $project->forceFill([
            'scheduled_at' => $scheduledAt,
            'status' => 'scheduled',
        ])->save();

        Notification::make()
            ->title('Conteúdo agendado')
            ->body('A data foi registrada no calendário editorial.')
            ->success()
            ->send();
    }

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
                $items = (clone $base)
                    ->with(['brand', 'slides', 'generations'])
                    ->latest('updated_at')
                    ->limit(20)
                    ->get();

                $stats = [
                    'Total' => (clone $base)->count(),
                    'Em aprovação' => (clone $base)->whereIn('status', ['review', 'pending_approval', 'approval_pending'])->count(),
                    'Agendados' => (clone $base)->whereNotNull('scheduled_at')->whereNull('published_at')->count(),
                    'Publicados' => (clone $base)->whereNotNull('published_at')->count(),
                ];

                $meta['brands'] = Brand::query()
                    ->where('client_id', $clientId)
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->pluck('name', 'id');

                $meta['generatedProject'] = $this->generatedProjectId
                    ? (clone $base)->with(['brand', 'slides', 'generations'])->find($this->generatedProjectId)
                    : null;
                break;

            case 'calendar':
                $items = (clone $base)->whereNotNull('scheduled_at')->orderBy('scheduled_at')->limit(30)->get();
                $stats = [
                    'Próximos 7 dias' => (clone $base)->whereBetween('scheduled_at', [now(), now()->addDays(7)])->count(),
                    'Próximos 30 dias' => (clone $base)->whereBetween('scheduled_at', [now(), now()->addDays(30)])->count(),
                    'Já publicados' => (clone $base)->whereNotNull('published_at')->count(),
                ];
                break;

            case 'approvals':
                $items = (clone $base)->whereIn('status', ['review', 'pending_approval', 'approval_pending'])->latest('updated_at')->limit(24)->get();
                $stats = [
                    'Aguardando você' => $items->count(),
                    'Em revisão' => (clone $base)->where('status', 'review')->count(),
                    'Pendentes' => (clone $base)->whereIn('status', ['pending_approval', 'approval_pending'])->count(),
                ];
                $meta['notice'] = 'Revise os conteúdos pendentes. Você pode aprovar ou devolver para ajustes sem sair desta área.';
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

            case 'requests':
                $items = (clone $base)->whereIn('status', ['editing', 'adjustment_requested', 'changes_requested', 'revision_requested'])->latest('updated_at')->limit(20)->get();
                $stats = [
                    'Em andamento' => $items->count(),
                    'Conteúdos ativos' => (clone $base)->whereNull('published_at')->count(),
                    'Últimos 30 dias' => (clone $base)->where('updated_at', '>=', now()->subDays(30))->count(),
                ];
                $meta['notice'] = 'Acompanhe aqui os conteúdos que estão em ajuste ou revisão pela equipe.';
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

            case 'files':
                $items = (clone $base)->latest('updated_at')->limit(30)->get();
                $stats = [
                    'Projetos disponíveis' => $items->count(),
                    'Com slides' => (clone $base)->whereHas('slides')->count(),
                    'Publicados' => (clone $base)->whereNotNull('published_at')->count(),
                ];
                $meta['notice'] = 'Consulte os materiais e conteúdos produzidos para sua marca.';
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
