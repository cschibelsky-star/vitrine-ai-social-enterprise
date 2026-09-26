<x-filament-panels::page>
<style>
    .fi-header{display:none!important}.fi-main{padding-top:0!important}.fi-main-ctn{max-width:none!important}
    .lab{min-height:100vh;margin:-24px;background:#050914;color:#f8fafc;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
    .lab *{box-sizing:border-box}.lab-shell{display:grid;grid-template-columns:242px minmax(0,1fr);min-height:100vh}
    .lab-side{position:sticky;top:0;height:100vh;padding:22px 15px;border-right:1px solid rgba(99,102,241,.22);background:linear-gradient(180deg,#070b19,#040711);z-index:20}
    .lab-brand{display:flex;align-items:center;gap:10px;margin:0 4px 22px}.lab-bolt{font-size:2rem;color:#21dcff;text-shadow:0 0 18px #713cff}.lab-brand b{display:block;font-size:1.25rem;font-style:italic}.lab-brand span{display:block;color:#ffc400;font-size:.7rem;font-weight:900;letter-spacing:.08em}
    .lab-nav{display:grid;gap:5px}.lab-nav button,.lab-more button{width:100%;min-height:42px;padding:0 12px;border:1px solid transparent;border-radius:10px;background:transparent;color:#aeb8cf;text-align:left;font:inherit;font-size:.78rem;font-weight:760;cursor:pointer}.lab-nav button.active,.lab-nav button:hover,.lab-more button:hover{color:#fff;background:linear-gradient(90deg,rgba(55,68,245,.58),rgba(109,24,230,.42));border-color:rgba(120,80,255,.5)}
    .lab-test{margin-top:18px;padding:13px;border:1px solid rgba(255,196,0,.32);border-radius:12px;background:rgba(255,196,0,.06);font-size:.69rem;color:#fde68a;line-height:1.45}
    .lab-main{padding:18px 22px 84px;min-width:0}.lab-top{display:flex;align-items:center;justify-content:space-between;gap:12px;min-height:48px;border-bottom:1px solid rgba(148,163,184,.1);margin-bottom:14px}.lab-top small{color:#94a3b8}.lab-back{display:inline-flex;align-items:center;min-height:36px;padding:0 12px;border-radius:9px;border:1px solid rgba(148,163,184,.18);color:#dbeafe;text-decoration:none;font-size:.76rem;font-weight:800}
    .lab-page{display:none}.lab-page.active{display:block}.lab-hero{display:grid;grid-template-columns:minmax(0,2fr) minmax(240px,.7fr);gap:12px;margin-bottom:12px}.lab-hero-main,.lab-team,.lab-panel,.lab-card{border:1px solid rgba(86,73,165,.32);background:linear-gradient(180deg,#0b1228,#070d1d);border-radius:15px}.lab-hero-main{min-height:196px;padding:24px;background:radial-gradient(circle at 78% 40%,rgba(126,38,255,.32),transparent 25%),linear-gradient(145deg,#080b1c,#070b1a)}.lab-hero-main h1{max-width:700px;margin:8px 0;font-size:clamp(2.2rem,4vw,4rem);line-height:.94;font-weight:950;letter-spacing:-.05em;text-transform:uppercase}.lab-hero-main h1 span{display:block;color:#ffc400}.lab-hero-main p{color:#b8c1d7}.lab-team{padding:20px}.lab-team p{color:#9aa6bf;font-size:.82rem;line-height:1.55}
    .lab-attention{margin-bottom:12px;border-color:rgba(255,191,0,.38);background:linear-gradient(180deg,rgba(37,28,8,.88),#080d1d)}.lab-panel{padding:16px;margin-bottom:12px}.lab-panel h2{font-size:.92rem;margin:0 0 12px}.lab-list{display:grid;gap:8px}.lab-row{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:12px;align-items:center;padding:11px 12px;border:1px solid rgba(148,163,184,.12);background:rgba(15,23,42,.55);border-radius:10px}.lab-row b{display:block;font-size:.78rem}.lab-row span{display:block;margin-top:3px;color:#94a3b8;font-size:.68rem}.lab-cta{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:0 10px;border-radius:8px;border:1px solid rgba(99,102,241,.42);background:rgba(76,29,149,.22);color:#e9d5ff;font-size:.68rem;font-weight:850;cursor:pointer}
    .lab-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;margin-bottom:12px}.lab-card{padding:14px}.lab-card small{display:block;color:#9aa6bf;text-transform:uppercase;font-size:.57rem;font-weight:800}.lab-card strong{display:block;margin:6px 0 4px;font-size:1.5rem}.lab-card em{font-style:normal;color:#8ea0bd;font-size:.62rem}.lab-grid2{display:grid;grid-template-columns:1.15fr 1fr;gap:10px}.lab-grid3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.lab-chip{display:inline-flex;padding:4px 7px;border-radius:999px;border:1px solid rgba(34,211,238,.25);background:rgba(34,211,238,.08);color:#a5f3fc;font-size:.62rem}.lab-chip.warn{border-color:rgba(245,158,11,.35);color:#fde68a}.lab-chip.ok{border-color:rgba(16,185,129,.35);color:#86efac}.lab-chip.off{border-color:rgba(148,163,184,.25);color:#cbd5e1}
    .lab-columns{display:grid;grid-template-columns:repeat(6,minmax(210px,1fr));gap:10px;overflow-x:auto;padding-bottom:5px}.lab-col{min-height:260px;padding:10px;border:1px solid rgba(148,163,184,.12);border-radius:12px;background:#080e1d}.lab-col h3{font-size:.72rem;margin:0 0 9px}.lab-item{padding:10px;border-radius:9px;border:1px solid rgba(99,102,241,.18);background:#0d1426;margin-bottom:8px}.lab-item b{font-size:.72rem}.lab-item p{margin:4px 0 0;color:#94a3b8;font-size:.64rem}
    .lab-day{padding:12px;border-left:2px solid #6d4cff;margin-bottom:10px;background:rgba(15,23,42,.45);border-radius:0 10px 10px 0}.lab-time{display:grid;grid-template-columns:72px 1fr;gap:10px;padding:8px 0;border-top:1px solid rgba(148,163,184,.08)}.lab-time:first-of-type{border-top:0}.lab-time strong{font-size:.76rem;color:#c4b5fd}.lab-time b{font-size:.75rem}.lab-time span{color:#94a3b8;font-size:.66rem}
    .lab-meter{height:10px;border-radius:999px;background:#111827;overflow:hidden;margin:10px 0}.lab-meter i{display:block;height:100%;width:65%;background:linear-gradient(90deg,#17d3ff,#7c3aed,#e335ff)}
    .lab-mobile-nav{display:none}.lab-more-sheet{display:none}
    @media(max-width:1024px){.lab-shell{grid-template-columns:76px 1fr}.lab-side{padding:18px 9px}.lab-brand div:last-child,.lab-nav button span{display:none}.lab-nav button{text-align:center;padding:0}.lab-kpis{grid-template-columns:repeat(2,1fr)}.lab-hero{grid-template-columns:1fr}.lab-team{display:none}}
    @media(max-width:760px){.lab{margin:-16px}.lab-shell{display:block}.lab-side{display:none}.lab-main{padding:12px 12px 90px}.lab-top{position:sticky;top:0;z-index:15;background:rgba(5,9,20,.94);backdrop-filter:blur(16px)}.lab-hero-main{min-height:225px;padding:18px}.lab-hero-main h1{font-size:2.35rem}.lab-kpis{grid-template-columns:repeat(2,1fr)}.lab-grid2,.lab-grid3{grid-template-columns:1fr}.lab-columns{grid-template-columns:repeat(6,82vw)}.lab-mobile-nav{position:fixed;display:grid;grid-template-columns:repeat(5,1fr);left:10px;right:10px;bottom:10px;z-index:50;padding:7px;border:1px solid rgba(99,102,241,.45);border-radius:17px;background:rgba(5,9,20,.96);backdrop-filter:blur(18px)}.lab-mobile-nav button{min-height:48px;border:0;border-radius:10px;background:transparent;color:#aeb8cf;font-size:.62rem;font-weight:800}.lab-mobile-nav button.active{background:rgba(82,45,170,.44);color:#fff}.lab-more-sheet.open{display:block;position:fixed;inset:auto 10px 76px;z-index:60;padding:12px;border:1px solid rgba(99,102,241,.42);border-radius:16px;background:#080d1b;box-shadow:0 20px 60px rgba(0,0,0,.45)}.lab-more{display:grid;grid-template-columns:1fr 1fr;gap:8px}.lab-more button{text-align:center;background:#0d1426}}
</style>

<div class="lab" id="socialLab">
    <div class="lab-shell">
        <aside class="lab-side">
            <div class="lab-brand"><div class="lab-bolt">ϟ</div><div><b>VITRINE</b><span>SOCIAL MÍDIA · LAB</span></div></div>
            <nav class="lab-nav" aria-label="Navegação do laboratório">
                <button class="active" data-page="painel">⌂ <span>Painel</span></button>
                <button data-page="conteudos">▤ <span>Conteúdos</span></button>
                <button data-page="calendario">▣ <span>Calendário</span></button>
                <button data-page="aprovacoes">✓ <span>Aprovações</span></button>
                <button data-page="desempenho">▥ <span>Desempenho</span></button>
                <button data-page="solicitacoes">☷ <span>Solicitações</span></button>
                <button data-page="canais">⌯ <span>Canais</span></button>
                <button data-page="arquivos">▱ <span>Arquivos</span></button>
                <button data-page="plano">◇ <span>Plano e uso</span></button>
                <button data-page="conta">● <span>Conta</span></button>
            </nav>
            <div class="lab-test"><b>AMBIENTE DE TESTE</b><br>Dados desta página são demonstrativos. Nenhuma alteração aqui afeta o painel real.</div>
        </aside>

        <main class="lab-main">
            <header class="lab-top">
                <div><b>Vitrine Social Mídia · Laboratório</b><br><small>Protótipo isolado para validação de UX e arquitetura</small></div>
                <a class="lab-back" href="{{ \App\Filament\Client\Pages\ClientDashboard::getUrl() }}">← Voltar ao painel real</a>
            </header>

            <section class="lab-page active" data-view="painel">
                <div class="lab-hero">
                    <div class="lab-hero-main"><small>Olá! 👋</small><h1>Sua presença digital <span>em um só lugar</span></h1><p>Acompanhe entregas, aprove conteúdos e veja resultados sem precisar entender a infraestrutura por trás.</p></div>
                    <div class="lab-team"><h3>Seu time por trás dos resultados</h3><p>Planejamento, criação, revisão e publicação trabalhando em um único fluxo.</p><div class="lab-chip ok">Operação ativa</div></div>
                </div>
                <section class="lab-panel lab-attention"><h2>Precisa da sua atenção</h2><div class="lab-list">
                    <div class="lab-row"><div><b>3 conteúdos aguardando sua aprovação</b><span>Revise para liberar a próxima etapa.</span></div><button class="lab-cta" data-go="aprovacoes">Ver aprovações</button></div>
                    <div class="lab-row"><div><b>1 solicitação aguardando retorno</b><span>A equipe precisa de uma decisão sua.</span></div><button class="lab-cta" data-go="solicitacoes">Ver solicitação</button></div>
                    <div class="lab-row"><div><b>Instagram precisa ser reconectado</b><span>Exemplo demonstrativo de estado de integração.</span></div><button class="lab-cta" data-go="canais">Revisar canais</button></div>
                </div></section>
                <div class="lab-kpis">
                    <div class="lab-card"><small>Conteúdos pendentes</small><strong>6</strong><em>Produção + revisão + aprovação</em></div>
                    <div class="lab-card"><small>Aprovações</small><strong>3</strong><em>Aguardando sua ação</em></div>
                    <div class="lab-card"><small>Posts agendados</small><strong>18</strong><em>Próximos 7 dias</em></div>
                    <div class="lab-card"><small>Resultados</small><strong>—</strong><em>Exibir somente com analytics real</em></div>
                </div>
                <div class="lab-grid2">
                    <section class="lab-panel"><h2>Próximas publicações</h2><div class="lab-list">
                        <div class="lab-row"><div><b>Hoje · 09:30 — Dica rápida</b><span>Instagram · Feed</span></div><span class="lab-chip">Agendado</span></div>
                        <div class="lab-row"><div><b>Hoje · 14:00 — Case de sucesso</b><span>Facebook · Feed</span></div><span class="lab-chip">Agendado</span></div>
                        <div class="lab-row"><div><b>Qui · 19:00 — Bastidores</b><span>TikTok · Vídeo</span></div><span class="lab-chip">Agendado</span></div>
                    </div></section>
                    <section class="lab-panel"><h2>Aprovações</h2><div class="lab-list">
                        <div class="lab-row"><div><b>Tendências que movem marcas</b><span>Instagram · Carrossel · v2</span></div><span class="lab-chip warn">Pendente</span></div>
                        <div class="lab-row"><div><b>3 dicas para mais engajamento</b><span>Facebook · Imagem · v1</span></div><span class="lab-chip warn">Pendente</span></div>
                    </div></section>
                </div>
            </section>

            <section class="lab-page" data-view="conteudos"><section class="lab-panel"><h2>Conteúdos — ciclo editorial por status</h2><div class="lab-columns">
                <div class="lab-col"><h3>Em produção · 2</h3><div class="lab-item"><b>Enquete de sábado</b><p>Stories · Instagram</p></div><div class="lab-item"><b>Reel de bastidores</b><p>Vídeo · TikTok</p></div></div>
                <div class="lab-col"><h3>Em revisão · 1</h3><div class="lab-item"><b>Post institucional</b><p>Feed · Facebook</p></div></div>
                <div class="lab-col"><h3>Aguardando aprovação · 3</h3><div class="lab-item"><b>Tendências</b><p>Carrossel · Instagram</p></div><div class="lab-item"><b>3 dicas</b><p>Imagem · Facebook</p></div></div>
                <div class="lab-col"><h3>Aprovados · 2</h3><div class="lab-item"><b>Case de sucesso</b><p>Feed · Facebook</p></div></div>
                <div class="lab-col"><h3>Agendados · 18</h3><div class="lab-item"><b>Dica rápida</b><p>Feed · Instagram</p></div></div>
                <div class="lab-col"><h3>Publicados · 42</h3><div class="lab-item"><b>Lançamento</b><p>Stories · Instagram</p></div></div>
            </div></section></section>

            <section class="lab-page" data-view="calendario"><section class="lab-panel"><h2>Calendário — horário real</h2>
                <div class="lab-day"><b>Segunda-feira</b><div class="lab-time"><strong>09:30</strong><div><b>Dica rápida</b><br><span>Instagram · Feed</span></div></div><div class="lab-time"><strong>18:15</strong><div><b>Enquete</b><br><span>Instagram · Stories</span></div></div></div>
                <div class="lab-day"><b>Terça-feira</b><div class="lab-time"><strong>14:00</strong><div><b>Case de sucesso</b><br><span>Facebook · Feed</span></div></div></div>
                <div class="lab-day"><b>Quinta-feira</b><div class="lab-time"><strong>19:00</strong><div><b>Bastidores</b><br><span>TikTok · Vídeo</span></div></div></div>
            </section></section>

            <section class="lab-page" data-view="aprovacoes"><section class="lab-panel"><h2>Aprovações</h2><div class="lab-grid3">
                <div class="lab-card"><span class="lab-chip warn">v2</span><h3>Tendências que movem marcas</h3><p>Instagram · Carrossel</p><button class="lab-cta">Aprovar</button> <button class="lab-cta">Pedir ajuste</button></div>
                <div class="lab-card"><span class="lab-chip warn">v1</span><h3>3 dicas para mais engajamento</h3><p>Facebook · Imagem</p><button class="lab-cta">Aprovar</button> <button class="lab-cta">Pedir ajuste</button></div>
                <div class="lab-card"><span class="lab-chip warn">v3</span><h3>Bastidores</h3><p>TikTok · Vídeo</p><button class="lab-cta">Aprovar</button> <button class="lab-cta">Pedir ajuste</button></div>
            </div></section></section>

            <section class="lab-page" data-view="desempenho"><section class="lab-panel"><h2>Desempenho</h2><div class="lab-kpis">
                <div class="lab-card"><small>Engajamento</small><strong>—</strong><em>Depende de analytics real</em></div>
                <div class="lab-card"><small>Alcance</small><strong>—</strong><em>Depende de analytics real</em></div>
                <div class="lab-card"><small>Crescimento</small><strong>—</strong><em>Depende de analytics real</em></div>
                <div class="lab-card"><small>Performance</small><strong>—</strong><em>Sem score decorativo</em></div>
            </div><div class="lab-test">No produto real, esta área só exibirá números quando a integração de analytics fornecer fonte, período e valor.</div></section></section>

            <section class="lab-page" data-view="solicitacoes"><section class="lab-panel"><h2>Solicitações</h2><div class="lab-list">
                <div class="lab-row"><div><b>Alteração de legenda</b><span>Campanha promocional</span></div><span class="lab-chip warn">Aguardando cliente</span></div>
                <div class="lab-row"><div><b>Novo post institucional</b><span>Sobre nossos serviços</span></div><span class="lab-chip">Em produção</span></div>
                <div class="lab-row"><div><b>Campanha de fim de ano</b><span>Briefing em análise</span></div><span class="lab-chip">Em análise</span></div>
                <div class="lab-row"><div><b>Revisão de identidade</b><span>Cores e tipografia</span></div><span class="lab-chip ok">Concluída</span></div>
            </div></section></section>

            <section class="lab-page" data-view="canais"><section class="lab-panel"><h2>Canais — estado real de integração</h2><div class="lab-list">
                <div class="lab-row"><div><b>Instagram</b><span>Conta demonstrativa</span></div><span class="lab-chip warn">Token expirado</span></div>
                <div class="lab-row"><div><b>Facebook</b><span>Conta demonstrativa</span></div><span class="lab-chip ok">Conectado</span></div>
                <div class="lab-row"><div><b>LinkedIn</b><span>Sem autorização</span></div><span class="lab-chip off">Integração pendente</span></div>
                <div class="lab-row"><div><b>YouTube</b><span>Conteúdo planejado, sem executor</span></div><span class="lab-chip off">Somente planejamento</span></div>
            </div><div class="lab-test">Nesta página de laboratório os estados são demonstrativos. A página real deve usar apenas o status retornado pelas integrações.</div></section></section>

            <section class="lab-page" data-view="arquivos"><section class="lab-panel"><h2>Arquivos — biblioteca da marca</h2><div class="lab-grid3">
                <div class="lab-card"><small>Identidade visual</small><strong>12</strong><em>logos, cores e guias</em></div>
                <div class="lab-card"><small>Peças aprovadas</small><strong>34</strong><em>criativos finais</em></div>
                <div class="lab-card"><small>Referências</small><strong>8</strong><em>inspirações e exemplos</em></div>
            </div></section></section>

            <section class="lab-page" data-view="plano"><section class="lab-panel"><h2>Plano e uso</h2><div class="lab-grid2">
                <div class="lab-card"><small>Plano atual</small><strong>Vitrine Pro</strong><em>Dado demonstrativo</em></div>
                <div class="lab-card"><small>Franquia</small><strong>40 conteúdos</strong><em>26 utilizados · 14 disponíveis</em><div class="lab-meter"><i></i></div></div>
            </div></section></section>

            <section class="lab-page" data-view="conta"><section class="lab-panel"><h2>Conta</h2><div class="lab-list">
                <div class="lab-row"><div><b>Nome</b><span>Cliente demonstrativo</span></div></div>
                <div class="lab-row"><div><b>Marca</b><span>Marca de demonstração</span></div></div>
                <div class="lab-row"><div><b>Notificações</b><span>E-mail + Painel</span></div></div>
            </div></section></section>
        </main>
    </div>

    <nav class="lab-mobile-nav" aria-label="Navegação mobile">
        <button class="active" data-page="painel">Painel</button><button data-page="conteudos">Conteúdos</button><button data-page="calendario">Calendário</button><button data-page="aprovacoes">Aprovações</button><button id="labMoreButton">Mais</button>
    </nav>
    <div class="lab-more-sheet" id="labMore"><div class="lab-more">
        <button data-page="desempenho">Desempenho</button><button data-page="solicitacoes">Solicitações</button><button data-page="canais">Canais</button><button data-page="arquivos">Arquivos</button><button data-page="plano">Plano e uso</button><button data-page="conta">Conta</button>
    </div></div>
</div>

<script>
(() => {
    const root = document.getElementById('socialLab');
    if (!root) return;
    const pages = [...root.querySelectorAll('.lab-page')];
    const buttons = [...root.querySelectorAll('[data-page]')];
    const more = document.getElementById('labMore');
    const moreButton = document.getElementById('labMoreButton');

    const show = (name) => {
        pages.forEach(p => p.classList.toggle('active', p.dataset.view === name));
        buttons.forEach(b => b.classList.toggle('active', b.dataset.page === name));
        if (more) more.classList.remove('open');
        window.scrollTo({top: 0, behavior: 'smooth'});
    };

    buttons.forEach(button => button.addEventListener('click', () => show(button.dataset.page)));
    root.querySelectorAll('[data-go]').forEach(button => button.addEventListener('click', () => show(button.dataset.go)));
    if (moreButton && more) moreButton.addEventListener('click', () => more.classList.toggle('open'));
})();
</script>
</x-filament-panels::page>