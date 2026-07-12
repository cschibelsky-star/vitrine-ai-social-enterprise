<x-filament-widgets::widget>
    <div class="recent-content-shell">
        <div class="recent-content-head">
            <div>
                <span>Produção recente</span>
                <h3>Continue de onde parou</h3>
            </div>

            <a href="{{ \App\Filament\Resources\ContentProjects\ContentProjectResource::getUrl('index') }}">
                Ver biblioteca
            </a>
        </div>

        @if ($projects->isEmpty())
            <div class="recent-content-empty">
                <strong>Seu primeiro conteúdo começa aqui.</strong>
                <p>Crie um projeto, escolha o Brand Kit e deixe o Studio montar a primeira versão.</p>
                <a href="{{ \App\Filament\Resources\ContentProjects\ContentProjectResource::getUrl('create') }}">Criar agora</a>
            </div>
        @else
            <div class="recent-content-grid">
                @foreach ($projects as $project)
                    <a class="recent-content-card" href="{{ \App\Filament\Resources\ContentProjects\ContentProjectResource::getUrl('edit', ['record' => $project]) }}">
                        <div class="recent-content-meta">
                            <span>{{ strtoupper($project->channel ?: 'social') }}</span>
                            <span class="status status-{{ $project->status }}">{{ $project->status }}</span>
                        </div>

                        <h4>{{ $project->title ?: \Illuminate\Support\Str::limit($project->idea, 64) }}</h4>
                        <p>{{ $project->client?->name ?: 'Sem cliente' }} · {{ $project->brand?->name ?: 'Sem Brand Kit' }}</p>
                        <small>{{ $project->updated_at?->diffForHumans() }}</small>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>

<style>
    .recent-content-shell {
        padding: 26px;
        border: 1px solid rgba(15,23,42,.07);
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 18px 55px rgba(15,23,42,.07);
    }

    .recent-content-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .recent-content-head span {
        color: #0891b2;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .13em;
    }

    .recent-content-head h3 {
        margin: 5px 0 0;
        color: #0f172a;
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -.035em;
    }

    .recent-content-head > a,
    .recent-content-empty a {
        color: #0891b2;
        font-weight: 700;
    }

    .recent-content-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .recent-content-card {
        display: block;
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: linear-gradient(180deg, #fff, #f8fafc);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .recent-content-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(15,23,42,.09);
    }

    .recent-content-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        color: #64748b;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .08em;
    }

    .recent-content-card h4 {
        margin: 16px 0 8px;
        color: #0f172a;
        font-weight: 800;
        line-height: 1.3;
    }

    .recent-content-card p,
    .recent-content-card small,
    .recent-content-empty p {
        color: #64748b;
    }

    .recent-content-card small {
        display: block;
        margin-top: 16px;
    }

    .status {
        padding: 4px 8px;
        border-radius: 999px;
        background: #e2e8f0;
    }

    .status-ready,
    .status-published {
        color: #166534;
        background: #dcfce7;
    }

    .status-editing,
    .status-scheduled {
        color: #075985;
        background: #e0f2fe;
    }

    .recent-content-empty {
        padding: 34px;
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        text-align: center;
    }

    .recent-content-empty p {
        margin: 8px auto 14px;
        max-width: 520px;
    }

    @media (max-width: 900px) {
        .recent-content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
