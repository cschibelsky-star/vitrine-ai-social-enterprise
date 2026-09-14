<x-filament-panels::page>
    <style>
        .vsm-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:22px}.vsm-card,.vsm-panel{border:1px solid rgba(148,163,184,.16);background:linear-gradient(180deg,rgba(15,23,42,.88),rgba(8,15,30,.94));border-radius:20px;padding:18px;box-shadow:0 16px 40px rgba(2,6,23,.16)}.vsm-card small{display:block;color:#94a3b8;font-size:.76rem;text-transform:uppercase;letter-spacing:.08em}.vsm-card strong{display:block;margin-top:7px;font-size:1.35rem;color:#f8fafc}.vsm-panel h2{font-size:1.15rem;font-weight:800;margin:0 0 14px;color:#f8fafc}.vsm-list{display:grid;gap:10px}.vsm-row{display:grid;grid-template-columns:minmax(0,1.5fr) repeat(3,minmax(0,.7fr));gap:14px;align-items:center;padding:14px;border-radius:14px;background:rgba(15,23,42,.58);border:1px solid rgba(148,163,184,.1)}.vsm-row b{color:#f8fafc}.vsm-row span{color:#94a3b8;font-size:.88rem}.vsm-badge{display:inline-flex;width:max-content;padding:5px 9px;border-radius:999px;background:rgba(34,211,238,.1);border:1px solid rgba(34,211,238,.22);color:#a5f3fc!important}.vsm-empty{padding:28px;text-align:center;color:#94a3b8;border:1px dashed rgba(148,163,184,.2);border-radius:16px}.vsm-note{padding:14px 16px;border-radius:14px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.22);color:#fde68a;margin-bottom:16px}@media(max-width:900px){.vsm-grid{grid-template-columns:repeat(2,1fr)}.vsm-row{grid-template-columns:1fr 1fr}}@media(max-width:560px){.vsm-grid,.vsm-row{grid-template-columns:1fr}}
    </style>

    @if(! $clientId)
        <div class="vsm-empty">Seu usuário ainda não está vinculado a um cliente. Contate o suporte.</div>
    @else
        <div class="vsm-grid">
            @foreach($stats as $label => $value)
                <div class="vsm-card"><small>{{ $label }}</small><strong>{{ $value }}</strong></div>
            @endforeach
        </div>

        @if(!empty($meta['notice']))
            <div class="vsm-note">{{ $meta['notice'] }}</div>
        @endif

        <section class="vsm-panel">
            @switch($section)
                @case('contents')
                    <h2>Conteúdos</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <div class="vsm-row"><b>{{ $item->title }}</b><span class="vsm-badge">{{ $item->status ?: 'sem status' }}</span><span>{{ $item->channel ?: 'Canal não definido' }}</span><span>{{ optional($item->scheduled_at)->format('d/m/Y H:i') ?: 'Sem agenda' }}</span></div>
                        @empty <div class="vsm-empty">Nenhum conteúdo disponível para este cliente.</div> @endforelse
                    </div>
                    @break

                @case('calendar')
                    <h2>Calendário Editorial</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <div class="vsm-row"><b>{{ $item->title }}</b><span>{{ optional($item->scheduled_at)->format('d/m/Y H:i') }}</span><span>{{ $item->channel ?: 'Canal não definido' }}</span><span class="vsm-badge">{{ $item->status ?: 'planejado' }}</span></div>
                        @empty <div class="vsm-empty">Nenhum conteúdo agendado.</div> @endforelse
                    </div>
                    @break

                @case('performance')
                    <h2>Desempenho do Mês</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <div class="vsm-row"><b>{{ $item->title }}</b><span>{{ $item->channel ?: 'Canal' }}</span><span>Score {{ $item->score ?? '—' }}</span><span>{{ optional($item->published_at)->format('d/m/Y') }}</span></div>
                        @empty <div class="vsm-empty">Ainda não há publicações com dados para exibir.</div> @endforelse
                    </div>
                    @break

                @case('channels')
                    <h2>Canais Conectados / com atividade</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <div class="vsm-row"><b>{{ ucfirst($item->channel) }}</b><span>{{ $item->total }} conteúdos</span><span>Última atividade</span><span>{{ \Illuminate\Support\Carbon::parse($item->last_activity)->format('d/m/Y') }}</span></div>
                        @empty <div class="vsm-empty">Nenhum canal com atividade registrada ainda.</div> @endforelse
                    </div>
                    @break

                @case('balance')
                    <h2>Consumo e Saldo</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <div class="vsm-row"><b>{{ $item->balance_type }}</b><span>Concedido: {{ $item->granted ?? 0 }}</span><span>Consumido: {{ $item->consumed ?? 0 }}</span><span class="vsm-badge">Disponível: {{ $item->available ?? 0 }}</span></div>
                        @empty <div class="vsm-empty">Nenhum saldo configurado para este cliente.</div> @endforelse
                    </div>
                    @break

                @case('affiliates')
                    <h2>Programa de Afiliados</h2>
                    <div class="vsm-empty">A área está preparada para o lançamento do programa. Regras financeiras e rastreamento serão habilitados somente após configuração operacional.</div>
                    @break

                @case('account')
                    <h2>Conta</h2>
                    <div class="vsm-empty">Dados da conta exibidos acima. Alterações sensíveis permanecem protegidas pelo fluxo autenticado do painel.</div>
                    @break
            @endswitch
        </section>
    @endif
</x-filament-panels::page>
