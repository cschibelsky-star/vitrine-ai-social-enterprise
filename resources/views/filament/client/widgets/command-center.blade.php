<x-filament-widgets::widget>
    <style>
        .cc-wrap{display:grid;gap:16px}.cc-hero{padding:24px;border-radius:24px;background:linear-gradient(135deg,rgba(34,211,238,.14),rgba(99,102,241,.15) 52%,rgba(217,70,239,.12));border:1px solid rgba(34,211,238,.18)}.cc-hero h2{font-size:1.7rem;font-weight:900;letter-spacing:-.03em;margin:0;color:#f8fafc}.cc-hero p{margin:8px 0 0;color:#cbd5e1}.cc-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.cc-card{padding:18px;border-radius:18px;background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.14)}.cc-card small{color:#94a3b8}.cc-card strong{display:block;font-size:1.8rem;margin-top:5px;color:#f8fafc}.cc-panels{display:grid;grid-template-columns:1.35fr .65fr;gap:16px}.cc-panel{padding:20px;border-radius:20px;background:rgba(15,23,42,.72);border:1px solid rgba(148,163,184,.14)}.cc-panel h3{font-weight:800;margin:0 0 14px;color:#f8fafc}.cc-list{display:grid;gap:9px}.cc-row{display:flex;justify-content:space-between;gap:14px;padding:12px 0;border-bottom:1px solid rgba(148,163,184,.1)}.cc-row:last-child{border-bottom:0}.cc-row b{color:#f8fafc}.cc-row span{color:#94a3b8;font-size:.88rem}.cc-links{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:2px}.cc-link{display:block;padding:13px;border-radius:14px;background:rgba(8,15,30,.78);border:1px solid rgba(148,163,184,.12);color:#cbd5e1;text-align:center}.cc-link:hover{border-color:rgba(34,211,238,.36);color:#f8fafc}@media(max-width:900px){.cc-grid{grid-template-columns:repeat(2,1fr)}.cc-panels{grid-template-columns:1fr}}@media(max-width:560px){.cc-grid,.cc-links{grid-template-columns:1fr}}
    </style>

    @if(! $clientId)
        <div class="cc-hero"><h2>SUA PRESENCA DIGITAL EM UM SO LUGAR</h2><p>Seu usuario precisa ser vinculado a um cliente para carregar a operacao.</p></div>
    @else
        <div class="cc-wrap">
            <div class="cc-hero">
                <h2>SUA PRESENCA DIGITAL EM UM SO LUGAR</h2>
                <p>Acompanhe entregas, aprove conteudos e veja resultados.</p>
            </div>

            <div class="cc-grid">
                <div class="cc-card"><small>Conteudos pendentes</small><strong>{{ $pending }}</strong></div>
                <div class="cc-card"><small>Aprovacoes</small><strong>{{ $approvals }}</strong></div>
                <div class="cc-card"><small>Posts agendados</small><strong>{{ $scheduled }}</strong></div>
                <div class="cc-card"><small>Plano / saldo</small><strong>{{ $subscription?->status ?: 'Ativo' }}</strong></div>
            </div>

            <div class="cc-panels">
                <section class="cc-panel">
                    <h3>Operacao recente</h3>
                    <div class="cc-list">
                        @forelse($recent as $item)
                            <div class="cc-row"><div><b>{{ $item->title }}</b><br><span>{{ $item->channel ?: 'Canal nao definido' }}</span></div><span>{{ $item->status ?: 'sem status' }}</span></div>
                        @empty
                            <div class="cc-row"><span>Nenhum conteudo registrado ainda.</span></div>
                        @endforelse
                    </div>
                </section>

                <section class="cc-panel">
                    <h3>Resumo do mes</h3>
                    <div class="cc-list">
                        <div class="cc-row"><b>Publicados</b><span>{{ $publishedMonth }}</span></div>
                        <div class="cc-row"><b>Plano</b><span>{{ $subscription?->plan_code ?: 'Nao informado' }}</span></div>
                        <div class="cc-row"><b>Credito de conteudo</b><span>{{ $contentBalance?->available ?? 0 }}</span></div>
                    </div>
                </section>
            </div>

            <div class="cc-links">
                <a class="cc-link" href="{{ \App\Filament\Client\Pages\Contents::getUrl() }}">Conteudos</a>
                <a class="cc-link" href="{{ \App\Filament\Client\Pages\CalendarPage::getUrl() }}">Calendario Editorial</a>
                <a class="cc-link" href="{{ \App\Filament\Client\Pages\Performance::getUrl() }}">Desempenho</a>
                <a class="cc-link" href="{{ \App\Filament\Client\Pages\Channels::getUrl() }}">Canais</a>
                <a class="cc-link" href="{{ \App\Filament\Client\Pages\Balance::getUrl() }}">Consumo e Saldo</a>
                <a class="cc-link" href="{{ \App\Filament\Client\Pages\Affiliates::getUrl() }}">Afiliados</a>
            </div>
        </div>
    @endif
</x-filament-widgets::widget>
