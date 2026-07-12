<x-filament-widgets::widget>
    @if ($project)
        <style>
            .studio-grid{display:grid;grid-template-columns:minmax(220px,.8fr) minmax(320px,1.35fr) minmax(300px,1fr);gap:18px}
            .studio-card{background:#fff;border:1px solid rgba(15,23,42,.08);border-radius:22px;padding:20px;box-shadow:0 16px 45px rgba(15,23,42,.07)}
            .studio-title{font-weight:800;color:#0f172a;font-size:1rem;margin-bottom:12px}
            .studio-label{font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin-top:12px}
            .studio-value{font-weight:700;color:#0f172a;margin-top:3px}
            .social-preview{border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;background:#fff}
            .social-head{display:flex;align-items:center;gap:10px;padding:14px}
            .social-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#06b6d4,#7c3aed)}
            .social-image{aspect-ratio:4/5;background:linear-gradient(145deg,#082f49,#0ea5e9 55%,#7c3aed);display:grid;place-items:center;color:#fff;text-align:center;padding:28px;font-size:1.45rem;font-weight:900}
            .social-copy{padding:16px;color:#334155;white-space:pre-line;font-size:.9rem;line-height:1.55}
            .score{display:flex;align-items:end;gap:8px;margin:14px 0}.score strong{font-size:2rem;color:#0f172a}.score span{color:#0ea5e9;font-weight:800}
            .slides{display:flex;gap:12px;overflow:auto;padding-bottom:6px}.slide{min-width:210px;border:1px solid #e2e8f0;border-radius:16px;padding:14px;background:#f8fafc}.slide b{color:#0f172a}.slide p{font-size:.82rem;color:#64748b;margin-top:8px}
            .versions{display:flex;flex-direction:column;gap:8px}.version{padding:10px 12px;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0;font-size:.82rem;color:#475569}
            .pill{display:inline-flex;padding:5px 9px;border-radius:999px;background:#ecfeff;color:#0e7490;font-weight:800;font-size:.72rem}
            @media(max-width:1100px){.studio-grid{grid-template-columns:1fr}.social-preview{max-width:520px;margin:auto}}
        </style>

        <div class="studio-grid">
            <section class="studio-card">
                <div class="studio-title">Contexto do projeto</div>
                <span class="pill">{{ strtoupper($project->status ?? 'draft') }}</span>
                <div class="studio-label">Cliente</div>
                <div class="studio-value">{{ $project->client?->name ?? 'Não informado' }}</div>
                <div class="studio-label">Brand Kit</div>
                <div class="studio-value">{{ $project->brand?->name ?? 'Não informado' }}</div>
                <div class="studio-label">Objetivo</div>
                <div class="studio-value">{{ ucfirst($project->objective ?? '-') }}</div>
                <div class="studio-label">Canal / formato</div>
                <div class="studio-value">{{ ucfirst($project->channel ?? '-') }} · {{ str_replace('_', ' ', ucfirst($project->format ?? '-')) }}</div>
                <div class="studio-label">Ideia</div>
                <div style="color:#475569;line-height:1.55;margin-top:5px">{{ $project->idea }}</div>

                <div class="studio-title" style="margin-top:22px">Histórico de versões</div>
                <div class="versions">
                    @forelse($versions as $index => $version)
                        <div class="version">
                            <strong>Versão {{ $versions->count() - $index }}</strong><br>
                            {{ $version->metadata['action'] ?? ($version->model ?? 'geração') }} · {{ $version->created_at?->format('d/m H:i') }}
                        </div>
                    @empty
                        <div class="version">Nenhuma versão registrada.</div>
                    @endforelse
                </div>
            </section>

            <section class="studio-card">
                <div class="studio-title">Conteúdo em edição</div>
                <h2 style="font-size:1.6rem;font-weight:900;color:#0f172a;line-height:1.15">{{ $project->title ?: 'Título ainda não gerado' }}</h2>
                <div class="score"><strong>{{ number_format((float) $project->score, 1, ',', '.') }}</strong><span>/ 10 · Score IA</span></div>
                <div class="studio-label">Legenda</div>
                <div style="white-space:pre-line;color:#334155;line-height:1.65;margin-top:8px">{{ $project->caption ?: 'A legenda aparecerá aqui.' }}</div>
                <div class="studio-label">CTA</div>
                <div class="studio-value">{{ $project->cta ?: 'Sem CTA' }}</div>
                <div class="studio-label">Hashtags</div>
                <div style="color:#0e7490;font-weight:700;line-height:1.6;margin-top:6px">{{ $project->hashtags ?: 'Sem hashtags' }}</div>

                <div class="studio-title" style="margin-top:24px">Slides</div>
                <div class="slides">
                    @forelse($project->slides as $slide)
                        <article class="slide">
                            <span class="pill">Slide {{ $slide->slide_number }}</span>
                            <div style="margin-top:10px"><b>{{ $slide->title }}</b></div>
                            <p>{{ $slide->body }}</p>
                        </article>
                    @empty
                        <article class="slide"><b>Nenhum slide</b><p>Gere um carrossel para visualizar os slides.</p></article>
                    @endforelse
                </div>
            </section>

            <section class="studio-card">
                <div class="studio-title">Prévia social</div>
                <div class="social-preview">
                    <div class="social-head">
                        <div class="social-avatar"></div>
                        <div><strong style="color:#0f172a">{{ $project->brand?->name ?? 'Sua marca' }}</strong><br><small style="color:#64748b">Conteúdo patrocinado</small></div>
                    </div>
                    <div class="social-image">{{ $project->title ?: 'Sua criação aparecerá aqui' }}</div>
                    <div class="social-copy"><strong>{{ $project->brand?->name ?? 'marca' }}</strong> {{ $project->caption ?: 'Legenda do conteúdo' }}

                        <div style="margin-top:12px;color:#0e7490;font-weight:700">{{ $project->hashtags }}</div>
                    </div>
                </div>
            </section>
        </div>
    @endif
</x-filament-widgets::widget>
