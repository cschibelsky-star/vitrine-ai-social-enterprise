<x-filament-panels::page>
    <style>
        .vsm-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:22px}.vsm-card,.vsm-panel{border:1px solid rgba(148,163,184,.16);background:linear-gradient(180deg,rgba(15,23,42,.88),rgba(8,15,30,.94));border-radius:20px;padding:18px;box-shadow:0 16px 40px rgba(2,6,23,.16)}.vsm-card small{display:block;color:#94a3b8;font-size:.76rem;text-transform:uppercase;letter-spacing:.08em}.vsm-card strong{display:block;margin-top:7px;font-size:1.35rem;color:#f8fafc}.vsm-panel h2{font-size:1.15rem;font-weight:800;margin:0 0 14px;color:#f8fafc}.vsm-list{display:grid;gap:10px}.vsm-row{display:grid;grid-template-columns:minmax(0,1.5fr) repeat(3,minmax(0,.7fr));gap:14px;align-items:center;padding:14px;border-radius:14px;background:rgba(15,23,42,.58);border:1px solid rgba(148,163,184,.1)}.vsm-row b{color:#f8fafc}.vsm-row span{color:#94a3b8;font-size:.88rem}.vsm-badge{display:inline-flex;width:max-content;padding:5px 9px;border-radius:999px;background:rgba(34,211,238,.1);border:1px solid rgba(34,211,238,.22);color:#a5f3fc!important}.vsm-actions{display:flex;gap:8px;flex-wrap:wrap}.vsm-action{display:inline-flex;padding:8px 11px;border-radius:10px;border:1px solid rgba(148,163,184,.18);background:rgba(8,15,30,.78);color:#e5e7eb;font-size:.8rem;font-weight:800}.vsm-action.ok{border-color:rgba(16,185,129,.32);color:#6ee7b7}.vsm-action.adjust{border-color:rgba(168,85,247,.35);color:#d8b4fe}.vsm-empty{padding:28px;text-align:center;color:#94a3b8;border:1px dashed rgba(148,163,184,.2);border-radius:16px}.vsm-note{padding:14px 16px;border-radius:14px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.22);color:#fde68a;margin-bottom:16px}@media(max-width:900px){.vsm-grid{grid-template-columns:repeat(2,1fr)}.vsm-row{grid-template-columns:1fr 1fr}}@media(max-width:560px){.vsm-grid,.vsm-row{grid-template-columns:1fr}}
        .vsm-generator{margin-bottom:18px;padding:20px;border-radius:20px;border:1px solid rgba(139,92,246,.28);background:radial-gradient(circle at 90% 10%,rgba(168,85,247,.16),transparent 28%),linear-gradient(180deg,rgba(17,24,39,.96),rgba(7,12,26,.98));box-shadow:0 18px 50px rgba(2,6,23,.28)}.vsm-generator h2{margin:0;color:#fff;font-size:1.2rem}.vsm-generator p{margin:6px 0 18px;color:#94a3b8}.vsm-form-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.vsm-field{display:grid;gap:6px}.vsm-field.idea{grid-column:1/-1}.vsm-field label{font-size:.78rem;font-weight:800;color:#cbd5e1}.vsm-field input,.vsm-field select,.vsm-field textarea{width:100%;border:1px solid rgba(148,163,184,.2);border-radius:12px;background:#070d1b;color:#f8fafc;padding:11px 12px;outline:none}.vsm-field textarea{min-height:110px;resize:vertical}.vsm-field input:focus,.vsm-field select:focus,.vsm-field textarea:focus{border-color:#22d3ee;box-shadow:0 0 0 3px rgba(34,211,238,.08)}.vsm-generate{display:inline-flex;align-items:center;justify-content:center;gap:8px;margin-top:14px;min-height:44px;padding:0 18px;border:0;border-radius:12px;background:linear-gradient(135deg,#14b8a6,#22d3ee);color:#031018;font-weight:900;cursor:pointer}.vsm-generate[disabled]{opacity:.55;cursor:wait}.vsm-content-card{display:grid;gap:11px;padding:16px;border-radius:16px;background:rgba(7,13,27,.92);border:1px solid rgba(148,163,184,.14)}.vsm-content-card h3{margin:0;color:#fff;font-size:1rem}.vsm-content-meta{display:flex;gap:8px;flex-wrap:wrap}.vsm-chip{display:inline-flex;padding:5px 8px;border-radius:999px;background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.18);color:#a5f3fc;font-size:.72rem}.vsm-copy-block{display:grid;gap:5px}.vsm-copy-block small{color:#64748b;text-transform:uppercase;letter-spacing:.06em}.vsm-copy-block p{margin:0;color:#dbe4f0;white-space:pre-wrap;line-height:1.5}.vsm-slides{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px}.vsm-slide{padding:12px;border-radius:12px;background:rgba(15,23,42,.75);border:1px solid rgba(99,102,241,.16)}.vsm-slide b{display:block;color:#fff;font-size:.82rem}.vsm-slide span{display:block;margin-top:5px;color:#94a3b8;font-size:.75rem;line-height:1.35}.vsm-card-actions{display:flex;gap:8px;flex-wrap:wrap}.vsm-schedule{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.vsm-schedule input{min-height:38px;padding:0 10px;border-radius:9px;border:1px solid rgba(148,163,184,.18);background:#070d1b;color:#f8fafc}.vsm-primary{border-color:rgba(34,211,238,.32)!important;color:#a5f3fc!important}.vsm-danger{border-color:rgba(244,114,182,.32)!important;color:#f9a8d4!important}.vsm-generated{margin-bottom:18px;border-color:rgba(16,185,129,.34)}@media(max-width:900px){.vsm-form-grid{grid-template-columns:1fr 1fr}.vsm-slides{grid-template-columns:1fr}}@media(max-width:560px){.vsm-form-grid{grid-template-columns:1fr}}
    </style>

    @if(! $clientId)
        <div class="vsm-empty">Seu usuário ainda não está vinculado a um cliente. Contate o suporte.</div>
    @else
        @if($section === 'contents')
            <section class="vsm-generator">
                <h2>Criar conteúdo com IA</h2>
                <p>Descreva a ideia. A plataforma gera o conteúdo usando o Brand Kit da sua marca e prepara o material para revisão.</p>

                <div class="vsm-form-grid">
                    <div class="vsm-field">
                        <label>Marca</label>
                        <select wire:model="brandId">
                            <option value="">Selecione</option>
                            @foreach(($meta['brands'] ?? []) as $brandIdOption => $brandName)
                                <option value="{{ $brandIdOption }}">{{ $brandName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="vsm-field">
                        <label>Rede social</label>
                        <select wire:model="channel">
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="tiktok">TikTok</option>
                            <option value="threads">Threads</option>
                            <option value="whatsapp">WhatsApp</option>
                        </select>
                    </div>

                    <div class="vsm-field">
                        <label>Formato</label>
                        <select wire:model="format">
                            <option value="post_portrait">Post para feed</option>
                            <option value="carousel_portrait">Carrossel</option>
                            <option value="stories">Story</option>
                            <option value="reels">Reels</option>
                            <option value="facebook_post">Post para Facebook</option>
                            <option value="linkedin_post">Post para LinkedIn</option>
                        </select>
                    </div>

                    <div class="vsm-field">
                        <label>Objetivo</label>
                        <select wire:model="objective">
                            <option value="sales">Vender</option>
                            <option value="engagement">Gerar engajamento</option>
                            <option value="authority">Construir autoridade</option>
                            <option value="education">Educar o público</option>
                            <option value="community">Fortalecer comunidade</option>
                            <option value="institutional">Institucional</option>
                            <option value="event">Divulgar evento</option>
                            <option value="launch">Divulgar lançamento</option>
                        </select>
                    </div>

                    <div class="vsm-field idea">
                        <label>O que você quer criar?</label>
                        <textarea wire:model="idea" placeholder="Ex.: Crie um post para divulgar nossa promoção de setembro, destacando o benefício principal e convidando o cliente a falar conosco."></textarea>
                    </div>
                </div>

                <button type="button" class="vsm-generate" wire:click="generateContent" wire:loading.attr="disabled" wire:target="generateContent">
                    <span wire:loading.remove wire:target="generateContent">✦ Gerar conteúdo</span>
                    <span wire:loading wire:target="generateContent">Gerando conteúdo...</span>
                </button>
            </section>

            @if(!empty($meta['generatedProject']))
                @php($generated = $meta['generatedProject'])
                <section class="vsm-content-card vsm-generated">
                    <div class="vsm-content-meta">
                        <span class="vsm-chip">Gerado agora</span>
                        <span class="vsm-chip">{{ ucfirst((string) $generated->channel) }}</span>
                        <span class="vsm-chip">{{ str_replace('_', ' ', (string) $generated->format) }}</span>
                        <span class="vsm-chip">Score {{ $generated->score ?? '—' }}</span>
                    </div>
                    <h3>{{ $generated->title ?: 'Conteúdo gerado' }}</h3>
                    <div class="vsm-copy-block"><small>Legenda</small><p>{{ $generated->caption }}</p></div>
                    <div class="vsm-copy-block"><small>CTA</small><p>{{ $generated->cta }}</p></div>
                    <div class="vsm-copy-block"><small>Hashtags</small><p>{{ $generated->hashtags }}</p></div>

                    @if($generated->slides->isNotEmpty())
                        <div class="vsm-slides">
                            @foreach($generated->slides as $slide)
                                <div class="vsm-slide">
                                    <b>{{ $slide->slide_number }}. {{ $slide->title }}</b>
                                    <span>{{ $slide->body }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="vsm-card-actions">
                        @if($generated->status !== 'ready')
                            <button type="button" wire:click="approveContent({{ $generated->id }})" class="vsm-action ok">Aprovar</button>
                            <button type="button" wire:click="requestAdjustment({{ $generated->id }})" class="vsm-action adjust">Pedir ajuste</button>
                        @else
                            <button type="button" wire:click="publishContentNow({{ $generated->id }})" class="vsm-action ok">Publicar agora</button>
                            <button type="button" wire:click="saveToGallery({{ $generated->id }})" class="vsm-action">Salvar na galeria</button>
                        @endif
                    </div>

                    @if($generated->status === 'ready')
                        <div class="vsm-schedule">
                            <input type="datetime-local" wire:model="scheduleInputs.{{ $generated->id }}">
                            <button type="button" wire:click="scheduleContent({{ $generated->id }})" class="vsm-action vsm-primary">Adicionar ao calendário</button>
                        </div>
                    @endif
                </section>
            @endif
        @endif

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
                    <h2>Conteúdos e Criativos</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <article class="vsm-content-card">
                                <div class="vsm-content-meta">
                                    <span class="vsm-chip">{{ ucfirst((string) ($item->channel ?: 'canal')) }}</span>
                                    <span class="vsm-chip">{{ str_replace('_', ' ', (string) ($item->format ?: 'formato')) }}</span>
                                    <span class="vsm-chip">{{ $item->status ?: 'sem status' }}</span>
                                    @if($item->score !== null)<span class="vsm-chip">Score {{ $item->score }}</span>@endif
                                </div>

                                <h3>{{ $item->title ?: 'Conteúdo em produção' }}</h3>

                                @if($item->caption)
                                    <div class="vsm-copy-block"><small>Legenda</small><p>{{ $item->caption }}</p></div>
                                @endif

                                @if($item->cta)
                                    <div class="vsm-copy-block"><small>CTA</small><p>{{ $item->cta }}</p></div>
                                @endif

                                @if($item->hashtags)
                                    <div class="vsm-copy-block"><small>Hashtags</small><p>{{ $item->hashtags }}</p></div>
                                @endif

                                @if($item->slides->isNotEmpty())
                                    <div class="vsm-slides">
                                        @foreach($item->slides as $slide)
                                            <div class="vsm-slide">
                                                <b>{{ $slide->slide_number }}. {{ $slide->title }}</b>
                                                <span>{{ $slide->body }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="vsm-card-actions">
                                    @if(!in_array($item->status, ['ready', 'scheduled', 'published'], true))
                                        <button type="button" wire:click="approveContent({{ $item->id }})" class="vsm-action ok">Aprovar</button>
                                        <button type="button" wire:click="requestAdjustment({{ $item->id }})" class="vsm-action adjust">Pedir ajuste</button>
                                    @elseif(in_array($item->status, ['ready', 'scheduled'], true))
                                        <button type="button" wire:click="publishContentNow({{ $item->id }})" class="vsm-action ok">Publicar agora</button>
                                        <button type="button" wire:click="saveToGallery({{ $item->id }})" class="vsm-action">Salvar na galeria</button>
                                        <button type="button" wire:click="requestAdjustment({{ $item->id }})" class="vsm-action adjust">Pedir ajuste</button>
                                    @endif
                                </div>

                                @if(in_array($item->status, ['ready', 'scheduled'], true))
                                    <div class="vsm-schedule">
                                        <input type="datetime-local" wire:model="scheduleInputs.{{ $item->id }}">
                                        <button type="button" wire:click="scheduleContent({{ $item->id }})" class="vsm-action vsm-primary">Adicionar ao calendário</button>
                                        @if($item->scheduled_at)
                                            <span class="vsm-chip">Agendado: {{ $item->scheduled_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </article>
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

                @case('approvals')
                    <h2>Aprovações</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <div class="vsm-row"><b>{{ $item->title }}</b><span>{{ $item->channel ?: 'Canal' }}</span><span class="vsm-badge">{{ $item->status ?: 'aguardando' }}</span><div class="vsm-actions"><button type="button" wire:click="approveContent({{ $item->id }})" class="vsm-action ok">Aprovar</button><button type="button" wire:click="requestAdjustment({{ $item->id }})" class="vsm-action adjust">Pedir ajuste</button></div></div>
                        @empty <div class="vsm-empty">Nenhum conteúdo aguardando aprovação.</div> @endforelse
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

                @case('requests')
                    <h2>Solicitações</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <div class="vsm-row"><b>{{ $item->title }}</b><span>{{ $item->channel ?: 'Canal' }}</span><span class="vsm-badge">{{ $item->status ?: 'em andamento' }}</span><span>{{ $item->updated_at?->format('d/m/Y H:i') }}</span></div>
                        @empty <div class="vsm-empty">Nenhuma solicitação em andamento.</div> @endforelse
                    </div>
                    @break

                @case('channels')
                    <h2>Conta publicadora</h2>

                    @if(session('publisher_success'))
                        <div class="vsm-note">{{ session('publisher_success') }}</div>
                    @endif
                    @if(session('publisher_error'))
                        <div class="vsm-note">{{ session('publisher_error') }}</div>
                    @endif

                    @if(!empty($meta['publisher']['connected']))
                        <div class="vsm-content-card" style="margin-bottom:16px">
                            <div class="vsm-content-meta">
                                <span class="vsm-chip">Meta conectada</span>
                                @if(!empty($meta['publisher']['page_name']))<span class="vsm-chip">Facebook: {{ $meta['publisher']['page_name'] }}</span>@endif
                                @if(!empty($meta['publisher']['instagram_username']))<span class="vsm-chip">Instagram: @{{ $meta['publisher']['instagram_username'] }}</span>@endif
                            </div>
                            <p style="margin:0;color:#cbd5e1">Esta conta será usada somente quando você clicar em Publicar agora ou quando chegar o horário de uma publicação agendada.</p>
                            <form method="POST" action="{{ route('publisher.meta.disconnect') }}">
                                @csrf
                                <button type="submit" class="vsm-action adjust">Desconectar conta Meta</button>
                            </form>
                        </div>
                    @else
                        <div class="vsm-content-card" style="margin-bottom:16px">
                            <h3>Conectar Instagram e Facebook</h3>
                            <p style="margin:0;color:#cbd5e1">Autorize sua Página Meta. O token fica criptografado no servidor e não aparece no painel.</p>
                            <div class="vsm-card-actions">
                                <a href="{{ route('publisher.meta.connect') }}" class="vsm-action ok">Conectar conta Meta</a>
                            </div>
                        </div>
                    @endif

                    <h2>Canais com atividade</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <div class="vsm-row"><b>{{ ucfirst($item->channel) }}</b><span>{{ $item->total }} conteúdos</span><span>Última atividade</span><span>{{ \Illuminate\Support\Carbon::parse($item->last_activity)->format('d/m/Y') }}</span></div>
                        @empty <div class="vsm-empty">Nenhum canal com atividade registrada ainda.</div> @endforelse
                    </div>
                    @break

                @case('files')
                    <h2>Arquivos e Materiais</h2>
                    <div class="vsm-list">
                        @forelse($items as $item)
                            <div class="vsm-row"><b>{{ $item->title }}</b><span>{{ $item->content_type ?: 'Conteúdo' }}</span><span>{{ $item->format ?: 'Formato não informado' }}</span><span>{{ $item->updated_at?->format('d/m/Y') }}</span></div>
                        @empty <div class="vsm-empty">Nenhum material disponível.</div> @endforelse
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
