<x-filament-widgets::widget>
    <style>
        .ac-wrap{display:grid;gap:16px}.ac-hero{padding:26px;border-radius:24px;background:radial-gradient(circle at 86% 18%,rgba(255,79,216,.18),transparent 26%),linear-gradient(135deg,rgba(37,217,255,.10),rgba(75,124,255,.12),rgba(139,92,246,.14));border:1px solid rgba(37,217,255,.15)}.ac-hero small{color:#a8b3cf;text-transform:uppercase;letter-spacing:.12em;font-weight:800}.ac-hero h2{font-size:2rem;font-weight:900;letter-spacing:-.04em;margin:6px 0;color:#fff}.ac-hero p{margin:0;color:#cbd5e1}.ac-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px}.ac-card{padding:16px;border-radius:18px;background:linear-gradient(180deg,rgba(16,23,45,.9),rgba(8,13,27,.96));border:1px solid rgba(148,163,184,.12)}.ac-card small{color:#8f9bb8}.ac-card strong{display:block;margin-top:5px;font-size:1.55rem;color:#fff}.ac-panel{padding:20px;border-radius:20px;background:rgba(12,18,36,.88);border:1px solid rgba(148,163,184,.12)}.ac-panel h3{margin:0 0 14px;font-weight:850;color:#fff}.ac-row{display:grid;grid-template-columns:1.5fr .8fr .8fr .7fr;gap:12px;padding:12px 0;border-bottom:1px solid rgba(148,163,184,.08)}.ac-row:last-child{border-bottom:0}.ac-row b{color:#f8fafc}.ac-row span{color:#94a3b8;font-size:.88rem}@media(max-width:1100px){.ac-grid{grid-template-columns:repeat(3,1fr)}}@media(max-width:680px){.ac-grid{grid-template-columns:repeat(2,1fr)}.ac-row{grid-template-columns:1fr 1fr}}
    </style>
    <div class="ac-wrap">
        <section class="ac-hero">
            <small>Vitrine Social Mídia · Administração</small>
            <h2>COCKPIT OPERACIONAL</h2>
            <p>Clientes, produção, aprovações, agenda e resultados em uma visão única.</p>
        </section>
        <div class="ac-grid">
            <div class="ac-card"><small>Clientes</small><strong>{{ $clients }}</strong></div>
            <div class="ac-card"><small>Em produção</small><strong>{{ $pending }}</strong></div>
            <div class="ac-card"><small>Aprovações</small><strong>{{ $approvals }}</strong></div>
            <div class="ac-card"><small>Agendados</small><strong>{{ $scheduled }}</strong></div>
            <div class="ac-card"><small>Publicados no mês</small><strong>{{ $publishedMonth }}</strong></div>
            <div class="ac-card"><small>Leads VIP</small><strong>{{ $leads }}</strong></div>
        </div>
        <section class="ac-panel">
            <h3>Operação recente</h3>
            @forelse($recent as $item)
                <div class="ac-row">
                    <b>{{ $item->title }}</b>
                    <span>{{ $item->client?->name ?? 'Cliente não informado' }}</span>
                    <span>{{ $item->channel ?: 'Canal' }}</span>
                    <span>{{ $item->status ?: 'sem status' }}</span>
                </div>
            @empty
                <div class="ac-row"><span>Nenhum conteúdo registrado.</span></div>
            @endforelse
        </section>
    </div>
</x-filament-widgets::widget>
