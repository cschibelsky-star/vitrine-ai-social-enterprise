<x-filament-widgets::widget>
    <style>
        .vsm-client{display:grid;gap:16px}
        .vsm-client-top{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(280px,.65fr);gap:16px}
        .vsm-client-hero{position:relative;overflow:hidden;min-height:236px;padding:30px 32px;border-radius:24px;border:1px solid rgba(93,115,255,.24);background:radial-gradient(circle at 78% 35%,rgba(255,57,210,.27),transparent 22%),radial-gradient(circle at 90% 10%,rgba(37,217,255,.2),transparent 18%),linear-gradient(135deg,#170b3b 0%,#0d1533 55%,#07101f 100%);box-shadow:0 22px 60px rgba(0,0,0,.24)}
        .vsm-client-hero:before{content:"";position:absolute;right:7%;bottom:-26%;width:250px;height:190px;border-radius:42px;transform:skewY(-8deg) rotate(-8deg);background:linear-gradient(145deg,rgba(23,49,101,.9),rgba(56,21,91,.88));border:1px solid rgba(103,232,249,.22);box-shadow:0 0 54px rgba(96,72,255,.2)}
        .vsm-client-hero:after{content:"✦";position:absolute;right:15%;top:18%;font-size:4.6rem;color:#df50ff;text-shadow:0 0 38px rgba(196,76,255,.75)}
        .vsm-client-kicker{font-size:.8rem;font-weight:900;color:#d4cbff;letter-spacing:.04em}
        .vsm-client-hero h2{position:relative;z-index:2;margin:10px 0 10px;max-width:640px;font-size:clamp(2.1rem,4vw,4rem);line-height:.92;letter-spacing:-.055em;font-weight:950;text-transform:uppercase;color:#fff}
        .vsm-client-hero h2 span{display:block;color:#ffc928}
        .vsm-client-hero p{position:relative;z-index:2;margin:0;max-width:580px;color:#c8d0e3;font-size:1rem}
        .vsm-client-team{position:relative;overflow:hidden;padding:24px;border-radius:24px;border:1px solid rgba(147,91,255,.24);background:radial-gradient(circle at 92% 94%,rgba(255,201,40,.14),transparent 24%),linear-gradient(160deg,rgba(37,18,72,.92),rgba(8,15,34,.98));box-shadow:0 22px 60px rgba(0,0,0,.2)}
        .vsm-client-team small,.vsm-card small{display:block;color:#96a3bf;font-size:.7rem;font-weight:850;letter-spacing:.08em;text-transform:uppercase}
        .vsm-client-team h3{margin:10px 0 9px;color:#fff;font-size:1.12rem;font-weight:900}
        .vsm-client-team p{margin:0;color:#aeb8d1;line-height:1.5;font-size:.9rem}
        .vsm-team-avatars{display:flex;align-items:center;margin-top:20px}
        .vsm-team-avatar{width:38px;height:38px;margin-right:-7px;border-radius:999px;display:grid;place-items:center;background:linear-gradient(145deg,#273d78,#7a2ba9);border:2px solid #0b1122;color:#fff;font-size:.72rem;font-weight:900;box-shadow:0 0 20px rgba(94,98,255,.2)}
        .vsm-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
        .vsm-card{padding:18px 20px;border-radius:19px;border:1px solid rgba(148,163,184,.13);background:linear-gradient(180deg,rgba(16,23,44,.96),rgba(8,13,29,.99));box-shadow:0 15px 42px rgba(0,0,0,.18)}
        .vsm-card strong{display:block;margin-top:7px;color:#fff;font-size:1.9rem;line-height:1;font-weight:950}
        .vsm-card em{display:block;margin-top:8px;color:#8f9bb8;font-size:.78rem;font-style:normal;line-height:1.35}
        .vsm-card .vsm-plan{color:#ffc928}
        .vsm-main{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(310px,.65fr);gap:16px}
        .vsm-panel{padding:20px;border-radius:22px;border:1px solid rgba(148,163,184,.12);background:linear-gradient(180deg,rgba(13,20,40,.95),rgba(7,12,27,.98));box-shadow:0 18px 50px rgba(0,0,0,.18)}
        .vsm-panel-head{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:15px}
        .vsm-panel-head h3{margin:0;color:#fff;font-size:1rem;font-weight:900;letter-spacing:.015em;text-transform:uppercase}
        .vsm-panel-head a{color:#c4b5fd;font-size:.76rem;font-weight:800}
        .vsm-period{display:inline-flex;padding:6px 10px;margin-bottom:12px;border-radius:999px;background:rgba(79,70,229,.11);border:1px solid rgba(99,102,241,.18);color:#cbd5ff;font-size:.76rem;font-weight:800}
        .vsm-week{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:8px}
        .vsm-day{min-height:128px;padding:10px;border-radius:14px;border:1px solid rgba(148,163,184,.09);background:rgba(15,23,45,.72)}
        .vsm-day-head{display:flex;justify-content:space-between;gap:5px;margin-bottom:8px;color:#dde5f6;font-size:.7rem;font-weight:850;text-transform:uppercase}
        .vsm-day-head span:last-child{color:#7d8aa7}
        .vsm-event{padding:7px;margin-top:6px;border-radius:9px;border:1px solid rgba(99,102,241,.18);background:rgba(79,70,229,.12)}
        .vsm-event b{display:block;color:#f8fafc;font-size:.68rem;line-height:1.25}
        .vsm-event span{display:block;margin-top:3px;color:#94a3b8;font-size:.62rem}
        .vsm-empty{color:#66758f;font-size:.68rem}
        .vsm-legend{display:flex;flex-wrap:wrap;gap:14px;margin-top:14px;color:#8491aa;font-size:.7rem}
        .vsm-dot{width:7px;height:7px;border-radius:50%;display:inline-block;margin-right:5px}.vsm-dot.s{background:#8b5cf6}.vsm-dot.p{background:#22c55e}.vsm-dot.d{background:#64748b}
        .vsm-approvals{position:relative;min-height:100%}
        .vsm-pending-count{padding:5px 9px;border-radius:999px;background:rgba(255,201,40,.1);border:1px solid rgba(255,201,40,.22);color:#fbd45d;font-size:.7rem;font-weight:850}
        .vsm-approval-list{display:grid;gap:10px}
        .vsm-approval-item{padding:13px;border-radius:14px;border:1px solid rgba(168,85,247,.16);background:linear-gradient(145deg,rgba(68,25,113,.34),rgba(9,16,34,.9))}
        .vsm-approval-item b{display:block;color:#fff;font-size:.86rem}
        .vsm-approval-item span{display:block;color:#8d99b3;font-size:.73rem;margin-top:4px}
        .vsm-via-orb{position:absolute;right:20px;bottom:18px;width:58px;height:58px;border-radius:50%;display:grid;place-items:center;background:radial-gradient(circle at 35% 30%,#fff 0 3%,#6ee7ff 7%,#5b39d7 34%,#18092e 70%);box-shadow:0 0 30px rgba(87,72,255,.5),0 0 18px rgba(255,57,210,.22);border:1px solid rgba(147,197,253,.35);color:white;font-size:.66rem;font-weight:950}
        .vsm-bottom{display:grid;grid-template-columns:1.05fr .95fr .8fr;gap:16px}
        .vsm-list{display:grid}.vsm-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid rgba(148,163,184,.08)}.vsm-row:last-child{border-bottom:0}.vsm-row b{color:#f8fafc;font-size:.82rem}.vsm-row span{color:#8e9ab3;font-size:.76rem}.vsm-channel{display:flex;justify-content:space-between;gap:8px;padding:10px 0;border-bottom:1px solid rgba(148,163,184,.08)}.vsm-channel:last-child{border-bottom:0}.vsm-channel b{color:#fff;font-size:.82rem}.vsm-channel span{color:#6ee7b7;font-size:.72rem}
        @media(max-width:1180px){.vsm-client-top,.vsm-main{grid-template-columns:1fr}.vsm-kpis{grid-template-columns:repeat(2,1fr)}.vsm-week{grid-template-columns:repeat(4,1fr)}.vsm-bottom{grid-template-columns:1fr 1fr}.vsm-bottom .vsm-panel:last-child{grid-column:1/-1}}
        @media(max-width:720px){.vsm-client-hero{padding:24px;min-height:210px}.vsm-kpis,.vsm-bottom{grid-template-columns:1fr}.vsm-bottom .vsm-panel:last-child{grid-column:auto}.vsm-week{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:480px){.vsm-week{grid-template-columns:1fr}}
    </style>

    @if(! $clientId)
        <section class="vsm-client-hero">
            <div class="vsm-client-kicker">Vitrine Social Mídia</div>
            <h2>SUA PRESENÇA DIGITAL <span>EM UM SÓ LUGAR</span></h2>
            <p>Seu usuário precisa estar vinculado a um cliente para carregar o painel.</p>
        </section>
    @else
        <div class="vsm-client">
            <div class="vsm-client-top">
                <section class="vsm-client-hero">
                    <div class="vsm-client-kicker">Olá, {{ $userName }}! 👋</div>
                    <h2>SUA PRESENÇA DIGITAL <span>EM UM SÓ LUGAR</span></h2>
                    <p>Acompanhe entregas, aprove conteúdos e veja resultados.</p>
                </section>

                <aside class="vsm-client-team">
                    <small>Seu time por trás dos resultados</small>
                    <h3>Estratégia, criação e acompanhamento</h3>
                    <p>Nosso time está trabalhando todos os dias para fazer sua marca crescer.</p>
                    <div class="vsm-team-avatars">
                        <div class="vsm-team-avatar">AN</div>
                        <div class="vsm-team-avatar">CR</div>
                        <div class="vsm-team-avatar">MM</div>
                        <div class="vsm-team-avatar">+3</div>
                    </div>
                </aside>
            </div>

            <div class="vsm-kpis">
                <div class="vsm-card">
                    <small>Conteúdos pendentes</small>
                    <strong>{{ $pending }}</strong>
                    <em>{{ number_format((float) ($contentBalance?->available ?? 0), 2, ',', '.') }} créditos disponíveis</em>
                </div>
                <div class="vsm-card">
                    <small>Aprovações</small>
                    <strong>{{ $approvals }}</strong>
                    <em>Aguardando sua decisão</em>
                </div>
                <div class="vsm-card">
                    <small>Posts agendados</small>
                    <strong>{{ $scheduled }}</strong>
                    <em>Próximas publicações</em>
                </div>
                <div class="vsm-card">
                    <small>Plano / Saldo</small>
                    <strong class="vsm-plan">{{ $subscription?->plan_code ? ucfirst($subscription->plan_code) : 'Não informado' }}</strong>
                    <em>{{ number_format((float) ($videoBalance?->available ?? 0), 2, ',', '.') }}s vídeo • {{ number_format((float) ($avatarBalance?->available ?? 0), 2, ',', '.') }}s avatar</em>
                </div>
            </div>

            <div class="vsm-main">
                <section class="vsm-panel">
                    <div class="vsm-panel-head">
                        <h3>Calendário Editorial</h3>
                        <a href="{{ \App\Filament\Client\Pages\CalendarPage::getUrl() }}">Ver calendário completo</a>
                    </div>
                    <div class="vsm-period">{{ $weekStart->format('d/m') }} – {{ $weekEnd->format('d/m/Y') }}</div>
                    <div class="vsm-week">
                        @foreach($calendarDays as $day)
                            <div class="vsm-day">
                                <div class="vsm-day-head">
                                    <span>{{ strtoupper($day['date']->locale('pt_BR')->isoFormat('ddd')) }}</span>
                                    <span>{{ $day['date']->format('d') }}</span>
                                </div>
                                @forelse($day['items'] as $item)
                                    <div class="vsm-event">
                                        <b>{{ $item->title }}</b>
                                        <span>{{ $item->published_at ? 'Publicado' : ($item->scheduled_at ? 'Agendado' : 'Rascunho') }}{{ $item->channel ? ' · '.ucfirst($item->channel) : '' }}</span>
                                    </div>
                                @empty
                                    <div class="vsm-empty">Sem publicação</div>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                    <div class="vsm-legend">
                        <span><i class="vsm-dot s"></i>Agendado</span>
                        <span><i class="vsm-dot p"></i>Publicado</span>
                        <span><i class="vsm-dot d"></i>Rascunho</span>
                    </div>
                </section>

                <section class="vsm-panel vsm-approvals">
                    <div class="vsm-panel-head">
                        <h3>Aprovações</h3>
                        <span class="vsm-pending-count">{{ $approvals }} pendente(s)</span>
                    </div>
                    <div class="vsm-approval-list">
                        @forelse($approvalItems as $item)
                            <div class="vsm-approval-item">
                                <b>{{ $item->title }}</b>
                                <span>{{ $item->channel ? ucfirst($item->channel) : 'Canal não informado' }}</span>
                            </div>
                        @empty
                            <div class="vsm-approval-item">
                                <b>Nenhum conteúdo aguardando aprovação no momento.</b>
                                <span>Quando houver uma nova aprovação, ela aparecerá aqui.</span>
                            </div>
                        @endforelse
                    </div>
                    <div class="vsm-via-orb">VIA</div>
                </section>
            </div>

            <div class="vsm-bottom">
                <section class="vsm-panel">
                    <div class="vsm-panel-head">
                        <h3>Desempenho do Mês</h3>
                        <a href="{{ \App\Filament\Client\Pages\Performance::getUrl() }}">Detalhes</a>
                    </div>
                    <div class="vsm-list">
                        <div class="vsm-row"><b>Publicados no mês</b><span>{{ $publishedMonth }}</span></div>
                        <div class="vsm-row"><b>Score médio</b><span>{{ $scoreAverage }}</span></div>
                        <div class="vsm-row"><b>Plano</b><span>{{ $subscription?->plan_code ? ucfirst($subscription->plan_code) : 'Não informado' }}</span></div>
                    </div>
                </section>

                <section class="vsm-panel">
                    <div class="vsm-panel-head">
                        <h3>Solicitações</h3>
                        <a href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">Ver todas</a>
                    </div>
                    <div class="vsm-list">
                        @forelse($requests as $item)
                            <div class="vsm-row">
                                <b>{{ $item->title }}</b>
                                <span>{{ $item->status }}</span>
                            </div>
                        @empty
                            <div class="vsm-row"><span>Nenhuma solicitação em andamento.</span></div>
                        @endforelse
                    </div>
                </section>

                <section class="vsm-panel">
                    <div class="vsm-panel-head"><h3>Canais Conectados</h3></div>
                    @forelse($channels as $channel)
                        <div class="vsm-channel">
                            <b>{{ ucfirst($channel->channel) }}</b>
                            <span>Conectado · {{ $channel->total }}</span>
                        </div>
                    @empty
                        <div class="vsm-channel"><b>Nenhum canal</b><span>Sem atividade</span></div>
                    @endforelse
                </section>
            </div>
        </div>
    @endif
</x-filament-widgets::widget>

