<x-filament-widgets::widget>
    <style>
        .cc-wrap{display:grid;gap:16px}.cc-hero{position:relative;overflow:hidden;padding:28px;border-radius:24px;background:radial-gradient(circle at 78% 30%,rgba(255,79,216,.24),transparent 24%),radial-gradient(circle at 88% 12%,rgba(37,217,255,.23),transparent 20%),linear-gradient(135deg,rgba(39,12,84,.95),rgba(10,18,47,.96) 55%,rgba(7,10,25,.98));border:1px solid rgba(124,92,255,.24);min-height:150px}.cc-hero:after{content:"✦";position:absolute;right:8%;top:14%;font-size:5rem;color:#c84cff;text-shadow:0 0 38px #7c3cff}.cc-kicker{color:#c8bcff;font-size:.82rem;font-weight:850}.cc-hero h2{font-size:clamp(2rem,3.5vw,3.4rem);font-weight:950;line-height:.94;letter-spacing:-.055em;margin:8px 0;color:#fff;text-transform:uppercase;max-width:720px}.cc-hero h2 span{color:#ffc928}.cc-hero p{margin:10px 0 0;color:#d1d5e7}.cc-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.cc-card{padding:18px;border-radius:18px;background:linear-gradient(180deg,rgba(16,22,43,.94),rgba(8,13,28,.98));border:1px solid rgba(148,163,184,.13);box-shadow:0 12px 38px rgba(0,0,0,.18)}.cc-card small{color:#9aa5c1;text-transform:uppercase;font-size:.72rem;letter-spacing:.06em}.cc-card strong{display:block;font-size:1.85rem;margin-top:6px;color:#fff}.cc-card em{display:block;margin-top:4px;color:#6ee7b7;font-style:normal;font-size:.78rem}.cc-columns{display:grid;grid-template-columns:1.25fr 1fr;gap:16px}.cc-panel{padding:20px;border-radius:20px;background:rgba(11,17,35,.9);border:1px solid rgba(148,163,184,.12)}.cc-panel h3{font-weight:850;margin:0 0 14px;color:#fff;display:flex;justify-content:space-between;align-items:center}.cc-panel h3 a{font-size:.78rem;color:#c4b5fd}.cc-list{display:grid;gap:9px}.cc-row{display:flex;justify-content:space-between;gap:14px;align-items:center;padding:11px 0;border-bottom:1px solid rgba(148,163,184,.08)}.cc-row:last-child{border-bottom:0}.cc-row b{color:#f8fafc}.cc-row span{color:#94a3b8;font-size:.84rem}.cc-badge{padding:5px 9px;border-radius:999px;background:rgba(139,92,246,.12);border:1px solid rgba(139,92,246,.22);color:#d8b4fe!important}.cc-calendar{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px}.cc-slot{padding:11px;border-radius:13px;background:rgba(17,24,50,.8);border:1px solid rgba(148,163,184,.09)}.cc-slot b{display:block;color:#fff;font-size:.82rem}.cc-slot span{display:block;color:#8f9bb8;font-size:.75rem;margin-top:5px}.cc-approvals{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.cc-approval{padding:14px;border-radius:16px;background:linear-gradient(145deg,rgba(72,25,120,.54),rgba(8,16,35,.94));border:1px solid rgba(168,85,247,.2)}.cc-approval b{display:block;color:#fff;min-height:38px}.cc-approval span{display:block;color:#9ca3af;font-size:.78rem;margin:8px 0}.cc-actions{display:flex;gap:6px}.cc-actions i{font-style:normal;font-size:.72rem;font-weight:800;padding:6px 8px;border-radius:9px;border:1px solid rgba(148,163,184,.18)}.cc-actions .ok{color:#6ee7b7;border-color:rgba(16,185,129,.35)}.cc-actions .adjust{color:#d8b4fe;border-color:rgba(168,85,247,.35)}.cc-bottom{display:grid;grid-template-columns:1.05fr .95fr .8fr;gap:16px}.cc-help{padding:20px;border-radius:20px;background:radial-gradient(circle at 90% 20%,rgba(255,201,40,.18),transparent 25%),linear-gradient(145deg,rgba(74,15,117,.72),rgba(11,17,35,.96));border:1px solid rgba(168,85,247,.24)}.cc-help h3{margin:0;color:#fff}.cc-help p{color:#a8b3cf}.cc-help a{display:inline-flex;padding:9px 14px;border-radius:999px;background:#ffc928;color:#171717;font-weight:900;font-size:.82rem}.cc-channel{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(148,163,184,.08)}.cc-channel:last-child{border-bottom:0}.cc-channel b{color:#fff}.cc-channel span{color:#6ee7b7;font-size:.8rem}@media(max-width:1100px){.cc-grid{grid-template-columns:repeat(2,1fr)}.cc-columns,.cc-bottom{grid-template-columns:1fr}.cc-calendar{grid-template-columns:repeat(2,1fr)}}@media(max-width:650px){.cc-grid,.cc-calendar,.cc-approvals{grid-template-columns:1fr}}
    </style>

    @if(! $clientId)
        <div class="cc-hero"><div class="cc-kicker">Vitrine Social Mídia</div><h2>SUA PRESENÇA DIGITAL <span>EM UM SÓ LUGAR</span></h2><p>Seu usuário precisa ser vinculado a um cliente para carregar a operação.</p></div>
    @else
        <div class="cc-wrap">
            <section class="cc-hero">
                <div class="cc-kicker">Olá, {{ $userName }}! 👋</div>
                <h2>SUA PRESENÇA DIGITAL <span>EM UM SÓ LUGAR</span></h2>
                <p>Acompanhe entregas, aprove conteúdos e veja resultados.</p>
            </section>

            <div class="cc-grid">
                <div class="cc-card"><small>Conteúdos pendentes</small><strong>{{ $pending }}</strong><em>Aguardando produção/revisão</em></div>
                <div class="cc-card"><small>Aprovações</small><strong>{{ $approvals }}</strong><em>Aguardando sua aprovação</em></div>
                <div class="cc-card"><small>Posts agendados</small><strong>{{ $scheduled }}</strong><em>Próximas publicações</em></div>
                <div class="cc-card"><small>Desempenho</small><strong>{{ $scoreAverage }}</strong><em>Score médio atual</em></div>
            </div>

            <div class="cc-columns">
                <section class="cc-panel">
                    <h3>Calendário Editorial <a href="{{ \App\Filament\Client\Pages\CalendarPage::getUrl() }}">Ver completo</a></h3>
                    <div class="cc-calendar">
                        @forelse($calendarItems as $item)
                            <div class="cc-slot"><b>{{ optional($item->scheduled_at)->format('d/m H:i') }}</b><span>{{ $item->title }}</span><span>{{ $item->channel ?: 'Canal' }}</span></div>
                        @empty
                            <div class="cc-slot"><b>Sem agenda</b><span>Nenhum conteúdo agendado.</span></div>
                        @endforelse
                    </div>
                </section>

                <section class="cc-panel">
                    <h3>Aprovações <a href="{{ \App\Filament\Client\Pages\Approvals::getUrl() }}">Ver todas</a></h3>
                    <div class="cc-approvals">
                        @forelse($approvalItems as $item)
                            <div class="cc-approval"><b>{{ $item->title }}</b><span>{{ $item->channel ?: 'Canal' }}</span><div class="cc-actions"><i class="ok">Aprovar</i><i class="adjust">Pedir ajuste</i></div></div>
                        @empty
                            <div class="cc-approval"><b>Tudo em dia</b><span>Nenhum conteúdo aguardando aprovação.</span></div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="cc-bottom">
                <section class="cc-panel">
                    <h3>Desempenho do Mês <a href="{{ \App\Filament\Client\Pages\Performance::getUrl() }}">Detalhes</a></h3>
                    <div class="cc-list">
                        <div class="cc-row"><b>Publicados no mês</b><span>{{ $publishedMonth }}</span></div>
                        <div class="cc-row"><b>Score médio</b><span>{{ $scoreAverage }}</span></div>
                        <div class="cc-row"><b>Plano</b><span>{{ $subscription?->plan_code ?: 'Não informado' }}</span></div>
                        <div class="cc-row"><b>Crédito de conteúdo</b><span>{{ $contentBalance?->available ?? 0 }}</span></div>
                    </div>
                </section>

                <section class="cc-panel">
                    <h3>Solicitações <a href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">Ver todas</a></h3>
                    <div class="cc-list">
                        @forelse($requests as $item)
                            <div class="cc-row"><div><b>{{ $item->title }}</b><br><span>{{ $item->channel ?: 'Canal' }}</span></div><span class="cc-badge">{{ $item->status }}</span></div>
                        @empty
                            <div class="cc-row"><span>Nenhuma solicitação em andamento.</span></div>
                        @endforelse
                    </div>
                </section>

                <section class="cc-panel">
                    <h3>Canais Conectados</h3>
                    @forelse($channels as $channel)
                        <div class="cc-channel"><b>{{ ucfirst($channel->channel) }}</b><span>Conectado · {{ $channel->total }}</span></div>
                    @empty
                        <div class="cc-channel"><b>Nenhum canal</b><span>Sem atividade</span></div>
                    @endforelse
                </section>
            </div>

            <section class="cc-help">
                <h3>Dúvidas? Fale com a gente.</h3>
                <p>Seu time acompanha a operação e pode orientar sobre conteúdos, aprovações e calendário.</p>
                <a href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">Abrir solicitação</a>
            </section>
        </div>
    @endif
</x-filament-widgets::widget>
