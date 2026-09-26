<x-filament-panels::page>
<style>
:root{--lab-bg:#030712;--lab-panel:#081020;--lab-panel2:#0b1226;--lab-line:rgba(112,86,255,.34);--lab-purple:#8b2cff;--lab-magenta:#e337ff;--lab-blue:#0b7cff;--lab-cyan:#1fe4ff;--lab-gold:#ffbf00;--lab-green:#18e49a;--lab-text:#f8f9ff;--lab-muted:#97a3c0}
.fi-header,.fi-sidebar,.fi-topbar{display:none!important}.fi-main,.fi-main-ctn{padding:0!important;margin:0!important;max-width:none!important;width:100%!important}
.lab{min-height:100vh;background:linear-gradient(180deg,#040712,#030610 66%,#02040b);color:var(--lab-text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
.lab *{box-sizing:border-box}.lab-shell{display:grid;grid-template-columns:72px minmax(0,1fr);min-height:100vh;transition:grid-template-columns .24s ease}
.lab-shell:has(.lab-side:hover),.lab-shell:has(.lab-side:focus-within){grid-template-columns:242px minmax(0,1fr)}
.lab-side{position:sticky;top:0;height:100vh;padding:20px 10px 14px;border-right:1px solid rgba(100,76,255,.22);background:linear-gradient(180deg,#050917,#030712 72%,#05081a);display:flex;flex-direction:column;overflow:hidden;z-index:30;transition:padding .24s ease,box-shadow .24s ease;box-shadow:8px 0 24px rgba(0,0,0,.08)}
.lab-side:hover,.lab-side:focus-within{padding:24px 16px 18px;box-shadow:12px 0 34px rgba(20,6,62,.34)}
.lab-brand{display:flex;align-items:center;justify-content:center;gap:0;margin:4px 4px 24px;white-space:nowrap}
.lab-brand-mark{width:48px;height:58px;min-width:48px;display:grid;place-items:center;filter:drop-shadow(0 0 14px rgba(120,57,255,.65))}
.lab-brand-mark svg{width:47px;height:56px}.lab-brand-copy{min-width:0;width:0;opacity:0;overflow:hidden;transform:translateX(-7px);transition:opacity .18s ease,transform .24s ease,width .24s ease}
.lab-side:hover .lab-brand,.lab-side:focus-within .lab-brand{justify-content:flex-start;gap:10px}.lab-side:hover .lab-brand-copy,.lab-side:focus-within .lab-brand-copy{min-width:138px;width:138px;opacity:1;transform:none}
.lab-brand-copy b{display:block;color:#fff;font-family:Impact,Haettenschweiler,"Arial Narrow Bold",sans-serif;font-size:2rem;font-weight:900;font-style:italic;letter-spacing:-.035em;text-shadow:0 0 14px rgba(255,255,255,.08)}
.lab-brand-copy span{display:block;margin-top:8px;color:#ffc400;font-size:.76rem;font-weight:1000;letter-spacing:.055em}
.lab-nav{display:grid;gap:5px}.lab-nav button{min-height:43px;padding:0 9px;display:flex;align-items:center;justify-content:center;gap:0;border-radius:10px;color:#b7c0d7;background:transparent;border:1px solid transparent;font:inherit;font-size:0;font-weight:760;cursor:pointer;white-space:nowrap;overflow:hidden;transition:all .2s ease}
.lab-nav button svg{width:19px;height:19px;min-width:19px}.lab-side:hover .lab-nav button,.lab-side:focus-within .lab-nav button{justify-content:flex-start;gap:10px;padding:0 12px;font-size:.78rem}
.lab-nav button.active{color:#fff;background:linear-gradient(90deg,rgba(40,56,245,.62),rgba(109,24,230,.58));border-color:#7536ff;box-shadow:0 0 18px rgba(81,55,255,.35),inset 3px 0 0 #1be0ff}
.lab-nav button.active svg{color:#21dcff}.lab-badge{margin-left:auto;min-width:20px;height:20px;border-radius:999px;display:none;place-items:center;background:#6d28d9;color:#fff;font-size:.58rem;font-weight:900}.lab-side:hover .lab-badge,.lab-side:focus-within .lab-badge{display:grid}
.lab-support{margin-top:auto;min-height:48px;padding:10px 8px;border:1px solid #6339ff;border-radius:16px;background:radial-gradient(circle at 82% 18%,rgba(192,42,255,.48),transparent 24%),linear-gradient(155deg,#120936,#071127);box-shadow:0 0 25px rgba(73,39,255,.28),inset 0 0 28px rgba(94,39,255,.08);display:grid;place-items:center;overflow:hidden}
.lab-support strong,.lab-support b,.lab-support p,.lab-support a{display:none}.lab-side:hover .lab-support,.lab-side:focus-within .lab-support{display:block;padding:15px 13px 16px}.lab-side:hover .lab-support strong,.lab-side:hover .lab-support b,.lab-side:hover .lab-support p,.lab-side:hover .lab-support a,.lab-side:focus-within .lab-support strong,.lab-side:focus-within .lab-support b,.lab-side:focus-within .lab-support p,.lab-side:focus-within .lab-support a{display:block}
.lab-support strong{font-size:.7rem}.lab-support b{margin-top:4px;font-size:.82rem}.lab-support p{color:#b8c0d6;font-size:.68rem;line-height:1.45}.lab-support a{display:inline-flex!important;width:max-content;min-height:34px;align-items:center;padding:0 14px;border-radius:999px;background:#ffc400;color:#241600!important;text-decoration:none;font-size:.65rem;font-weight:900;box-shadow:0 0 18px rgba(255,196,0,.35)}
.lab-main{min-width:0;padding:12px 18px 86px}.lab-top{display:flex;align-items:center;justify-content:space-between;gap:14px;min-height:48px;border-bottom:1px solid rgba(122,116,190,.10);margin-bottom:10px}.lab-top-left small{display:block;color:#8f9bb7;font-size:.63rem}.lab-top-actions{display:flex;align-items:center;gap:10px}.lab-adjust{display:inline-flex;align-items:center;min-height:34px;padding:0 14px;border-radius:7px;border:1px solid rgba(204,72,255,.6);background:linear-gradient(135deg,#6d12ec,#ad0fff);box-shadow:0 0 24px rgba(162,31,255,.28);color:#fff;text-decoration:none;font-size:.68rem;font-weight:900}.lab-bell{width:34px;height:34px;display:grid;place-items:center;border-left:1px solid rgba(255,255,255,.1);border-right:1px solid rgba(255,255,255,.1)}.lab-bell svg{width:18px;height:18px}.lab-profile{display:flex;align-items:center;gap:8px}.lab-avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(145deg,#50236d,#0b7cff);border:2px solid #fff;font-size:.6rem;font-weight:900}.lab-profile b{display:block;font-size:.7rem}.lab-profile span{display:block;color:#99a6c2;font-size:.58rem}
.lab-page{display:none}.lab-page.active{display:block}.lab-hero-row{display:grid;grid-template-columns:minmax(0,2.18fr) minmax(248px,.68fr);gap:10px;margin-bottom:10px}.lab-hero,.lab-team,.lab-panel,.lab-card{border-radius:12px;border:1px solid rgba(77,86,167,.35);background:linear-gradient(180deg,#091126,#060c1b);box-shadow:0 12px 30px rgba(0,0,0,.18)}
.lab-hero{position:relative;overflow:hidden;min-height:205px;padding:18px;background:radial-gradient(circle at 70% 48%,rgba(114,18,255,.28),transparent 24%),radial-gradient(circle at 86% 58%,rgba(236,46,255,.17),transparent 28%),linear-gradient(135deg,#050817 0%,#06091b 62%,#050715 100%)}
.lab-copy{position:relative;z-index:3;width:52%;padding-top:6px}.lab-greeting{font-size:.78rem;margin-bottom:7px}.lab-copy h1{margin:0;font-family:Impact,Haettenschweiler,"Arial Narrow Bold",sans-serif;font-size:clamp(2.5rem,3.8vw,4rem);line-height:.88;letter-spacing:.005em;font-style:italic;font-weight:900;text-transform:uppercase;text-shadow:0 3px 14px rgba(0,0,0,.65)}.lab-copy h1 span{display:block;margin-top:4px;color:#ffc400}.lab-copy p{max-width:430px;color:#d8ddec;font-size:.82rem}
.lab-hero-art{position:absolute;right:-1%;top:0;width:52%;height:100%;display:flex;align-items:center;justify-content:center;pointer-events:none}.lab-hero-svg{width:100%;height:100%;max-height:300px;overflow:visible}
.lab-team{min-height:205px;padding:18px;background:radial-gradient(circle at 88% 43%,rgba(202,45,255,.2),transparent 28%),linear-gradient(155deg,#0b1028,#12092d);border-color:rgba(182,60,255,.44)}.lab-team h3{font-size:.82rem;margin:0 0 9px}.lab-team p{font-size:.72rem;color:#b8c0d6;line-height:1.45}.lab-team-avatars{display:flex;margin-top:18px}.lab-team-avatar{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;margin-right:-5px;border:2px solid #10152b;background:linear-gradient(145deg,#172b5f,#9c31cf);font-size:.58rem;font-weight:950}
.lab-attention{border-color:rgba(255,191,0,.34);background:linear-gradient(180deg,rgba(35,27,8,.86),rgba(11,14,28,.96))}
.lab-panel{padding:12px;margin-bottom:10px}.lab-panel-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px}.lab-panel h2{margin:0;font-size:.78rem;text-transform:uppercase}.lab-link{display:inline-flex;align-items:center;min-height:28px;padding:0 9px;border:1px solid rgba(144,161,208,.35);border-radius:7px;color:#eef1ff;text-decoration:none;font-size:.55rem;font-weight:850}
.lab-list{display:grid;gap:7px}.lab-row{display:grid;grid-template-columns:28px minmax(0,1fr) auto;gap:8px;align-items:center;min-height:38px;padding:7px 8px;border:1px solid rgba(103,116,164,.18);border-radius:8px;background:#0a1122}.lab-mini{width:26px;height:26px;border-radius:7px;display:grid;place-items:center;background:linear-gradient(145deg,#8f20ff,#d11dff);font-size:.58rem;font-weight:1000}.lab-row b{display:block;font-size:.6rem}.lab-row span{display:block;color:#929eb8;font-size:.52rem;margin-top:2px}.lab-status{display:inline-flex;align-items:center;padding:5px 7px;border-radius:5px;border:1px solid rgba(137,47,255,.5);color:#d8adff;background:rgba(97,27,155,.28);font-size:.5rem}.lab-status.ok{color:#79ffc1;border-color:rgba(0,210,118,.5);background:rgba(0,96,58,.34)}.lab-status.warn{color:#ffe59a;border-color:rgba(255,185,45,.48);background:rgba(119,74,0,.28)}
.lab-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;margin-bottom:10px}.lab-card{padding:12px}.lab-card small{display:block;color:#c7ccdd;font-size:.52rem;font-weight:900;text-transform:uppercase}.lab-card strong{display:block;color:#fff;font-size:1.45rem;line-height:1;margin-top:6px}.lab-card em{display:block;color:#929db6;font-size:.54rem;font-style:normal;margin-top:5px}
.lab-grid2{display:grid;grid-template-columns:1.12fr 1fr;gap:9px}.lab-grid3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px}.lab-columns{display:grid;grid-template-columns:repeat(6,minmax(205px,1fr));gap:9px;overflow-x:auto;padding-bottom:6px}.lab-col{min-height:260px;padding:10px;border:1px solid rgba(148,163,184,.12);border-radius:11px;background:#080e1d}.lab-col h3{font-size:.68rem;margin:0 0 9px}.lab-item{padding:10px;border-radius:9px;border:1px solid rgba(99,102,241,.18);background:#0d1426;margin-bottom:8px}.lab-item b{font-size:.7rem}.lab-item p{margin:4px 0 0;color:#94a3b8;font-size:.62rem}
.lab-day{padding:12px;border-left:2px solid #6d4cff;margin-bottom:9px;background:rgba(15,23,42,.45);border-radius:0 10px 10px 0}.lab-time{display:grid;grid-template-columns:70px 1fr;gap:10px;padding:8px 0;border-top:1px solid rgba(148,163,184,.08)}.lab-time strong{font-size:.74rem;color:#c4b5fd}.lab-time b{font-size:.72rem}.lab-time span{color:#94a3b8;font-size:.64rem}
.lab-chip{display:inline-flex;padding:4px 7px;border-radius:999px;border:1px solid rgba(34,211,238,.25);background:rgba(34,211,238,.08);color:#a5f3fc;font-size:.58rem}.lab-chip.warn{border-color:rgba(245,158,11,.35);color:#fde68a}.lab-chip.ok{border-color:rgba(16,185,129,.35);color:#86efac}.lab-chip.off{border-color:rgba(148,163,184,.25);color:#cbd5e1}
.lab-action{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:0 10px;border-radius:7px;border:1px solid rgba(144,161,208,.35);background:#091126;color:#eef1ff;font-size:.58rem;font-weight:850;cursor:pointer}.lab-action.ok{border-color:#0be897;color:#baffdf;background:rgba(0,90,66,.38)}.lab-action.adjust{border-color:#a42cff;color:#f0c5ff;background:rgba(90,20,133,.28)}
.lab-meter{height:10px;border-radius:999px;background:#111827;overflow:hidden;margin:10px 0}.lab-meter i{display:block;height:100%;width:65%;background:linear-gradient(90deg,#17d3ff,#7c3aed,#e335ff)}
.lab-testnote{padding:12px;border-radius:10px;border:1px dashed rgba(255,196,0,.34);background:rgba(255,196,0,.05);color:#fde68a;font-size:.62rem;line-height:1.45}
.lab-mobile-nav,.lab-more-sheet{display:none}
@media(max-width:1024px){.lab-shell{grid-template-columns:68px minmax(0,1fr)}.lab-shell:has(.lab-side:hover),.lab-shell:has(.lab-side:focus-within){grid-template-columns:218px minmax(0,1fr)}.lab-hero-row{grid-template-columns:1fr}.lab-team{display:none}.lab-kpis{grid-template-columns:repeat(2,1fr)}}
@media(max-width:760px){.lab-shell{display:block}.lab-side{display:none}.lab-main{padding:8px 12px 92px}.lab-top{position:sticky;top:0;z-index:20;background:rgba(4,8,20,.94);backdrop-filter:blur(18px)}.lab-adjust{display:none}.lab-profile div:last-child{display:none}.lab-hero{min-height:290px;padding:17px 16px}.lab-copy{width:82%}.lab-copy h1{font-size:2.35rem}.lab-copy p{font-size:.78rem;max-width:82%}.lab-hero-art{right:-18%;top:42%;width:78%;height:58%;opacity:.92}.lab-kpis{grid-template-columns:repeat(2,1fr)}.lab-grid2,.lab-grid3{grid-template-columns:1fr}.lab-columns{grid-template-columns:repeat(6,82vw)}.lab-mobile-nav{position:fixed;display:grid;grid-template-columns:repeat(5,1fr);left:10px;right:10px;bottom:10px;z-index:60;padding:7px;border-radius:18px;border:1px solid rgba(115,44,255,.48);background:rgba(4,8,20,.96);box-shadow:0 0 24px rgba(80,25,180,.2);backdrop-filter:blur(18px)}.lab-mobile-nav button{display:grid;place-items:center;min-height:48px;border:0;border-radius:12px;background:transparent;color:#b8c2db;font-size:.61rem;font-weight:850}.lab-mobile-nav button.active{color:#fff;background:linear-gradient(145deg,rgba(54,61,145,.55),rgba(68,23,115,.62));border:1px solid rgba(136,48,255,.55)}.lab-more-sheet.open{display:block;position:fixed;left:10px;right:10px;bottom:78px;z-index:65;padding:12px;border:1px solid rgba(115,44,255,.48);border-radius:16px;background:#070c1b;box-shadow:0 20px 60px rgba(0,0,0,.48)}.lab-more-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}.lab-more-grid button{min-height:44px;border:1px solid rgba(99,102,241,.18);border-radius:10px;background:#0d1426;color:#eef1ff;font-size:.68rem;font-weight:800}}
</style>

<div class="lab" id="socialLab">
<div class="lab-shell">
<aside class="lab-side">
    <div class="lab-brand">
        <div class="lab-brand-copy"><b>VITRINE</b><span>SOCIAL MÍDIA</span></div>
        <div class="lab-brand-mark">
            <svg viewBox="0 0 48 48" fill="none" aria-hidden="true"><path d="M29 3 12 25h10l-3 20 17-24H26l3-18Z" fill="url(#labBolt)"/><defs><linearGradient id="labBolt" x1="11" y1="5" x2="39" y2="42"><stop stop-color="#19E7FF"/><stop offset=".55" stop-color="#8C2CFF"/><stop offset="1" stop-color="#FF41D0"/></linearGradient></defs></svg>
        </div>
    </div>
    <nav class="lab-nav" aria-label="Navegação do laboratório">
        <button class="active" data-page="painel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 11 12 4l9 7v9H4v-9Z"/><path d="M9 20v-6h6v6"/></svg>Painel</button>
        <button data-page="conteudos"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>Conteúdos</button>
        <button data-page="calendario"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg>Calendário</button>
        <button data-page="aprovacoes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.2 2.2 4.8-5"/></svg>Aprovações <span class="lab-badge">3</span></button>
        <button data-page="desempenho"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V9M10 19V5M16 19v-7M22 19V3"/></svg>Desempenho</button>
        <button data-page="solicitacoes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16v12H8l-4 4V5Z"/></svg>Solicitações</button>
        <button data-page="canais"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12a7 7 0 0 1 7-7M5 17a12 12 0 0 1 12-12"/><circle cx="6" cy="18" r="2"/></svg>Canais</button>
        <button data-page="arquivos"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h7l2 2h9v10H3V7Z"/></svg>Arquivos</button>
        <button data-page="plano"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/></svg>Plano e uso</button>
        <button data-page="conta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c.7-4.5 3.3-7 8-7s7.3 2.5 8 7"/></svg>Conta</button>
    </nav>
    <div class="lab-support"><strong>PRECISA DE AJUDA?</strong><b>Fale com nossa equipe</b><p>Envie uma solicitação e acompanhe o andamento.</p><a href="#" data-go="solicitacoes">Nova solicitação →</a></div>
</aside>

<main class="lab-main">
<header class="lab-top">
    <div class="lab-top-left"><b>Vitrine Social Mídia · Hybrid Lab v2</b><small>Ambiente isolado — nenhuma ação afeta o painel real</small></div>
    <div class="lab-top-actions"><a class="lab-adjust" href="#" data-go="solicitacoes">✎ Solicitar ajuste</a><div class="lab-bell"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg></div><div class="lab-profile"><div class="lab-avatar">JM</div><div><b>Julia Martins</b><span>Cliente · demonstração</span></div></div></div>
</header>

<section class="lab-page active" data-view="painel">
    <div class="lab-hero-row">
        <section class="lab-hero">
            <div class="lab-copy"><div class="lab-greeting">Olá, Julia! 👋</div><h1>Sua presença digital <span>em um só lugar</span></h1><p>Acompanhe entregas, aprove conteúdos e veja resultados.</p></div>
            <div class="lab-hero-art" aria-hidden="true">
<svg class="lab-hero-svg" viewBox="0 0 640 360" role="img" aria-label="Ecossistema social Vitrine">
<defs>
<linearGradient id="labDevice" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#139dff"/><stop offset=".4" stop-color="#183cff"/><stop offset=".72" stop-color="#6f28ff"/><stop offset="1" stop-color="#ff38d0"/></linearGradient>
<linearGradient id="labGlass" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#0e49ff" stop-opacity=".55"/><stop offset=".5" stop-color="#0a1237" stop-opacity=".92"/><stop offset="1" stop-color="#cb2cff" stop-opacity=".45"/></linearGradient>
<linearGradient id="labIg" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#792cff"/><stop offset=".55" stop-color="#ff36be"/><stop offset="1" stop-color="#ff7a28"/></linearGradient>
<linearGradient id="labYt" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#ff244f"/><stop offset="1" stop-color="#ff6735"/></linearGradient>
<linearGradient id="labStat" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#09aaff"/><stop offset="1" stop-color="#2250ff"/></linearGradient>
<linearGradient id="labHeart" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#c72dff"/><stop offset="1" stop-color="#ff49b9"/></linearGradient>
<filter id="labGlow" x="-80%" y="-80%" width="260%" height="260%"><feGaussianBlur stdDeviation="9" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
<filter id="labSoftGlow" x="-80%" y="-80%" width="260%" height="260%"><feGaussianBlur stdDeviation="4" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
</defs>
<g opacity=".7" filter="url(#labSoftGlow)"><path d="M55 245C140 175 207 281 320 218S500 203 594 144" fill="none" stroke="#6d35ff" stroke-width="3"/><path d="M64 263C175 198 240 302 352 231S512 217 604 165" fill="none" stroke="#18bfff" stroke-width="2"/><path d="M156 83 171 101 159 105 174 122" fill="none" stroke="#973cff" stroke-width="3"/><path d="M497 80 484 99 498 101 484 119" fill="none" stroke="#238aff" stroke-width="3"/></g>
<ellipse cx="332" cy="269" rx="228" ry="56" fill="none" stroke="#2d7cff" stroke-width="4" opacity=".8" filter="url(#labGlow)"/><ellipse cx="332" cy="269" rx="258" ry="73" fill="none" stroke="#b138ff" stroke-width="2.5" opacity=".55"/>
<g transform="translate(159 166) skewX(-10)" filter="url(#labGlow)"><rect width="360" height="122" rx="28" fill="url(#labDevice)" opacity=".98"/><rect x="11" y="8" width="338" height="96" rx="21" fill="#061128"/><rect x="20" y="15" width="320" height="80" rx="18" fill="url(#labGlass)" stroke="#23e5ff" stroke-width="2"/><path d="M28 91h303" stroke="#6de9ff" stroke-width="3" opacity=".9"/><path d="M321 103h13" stroke="#ff5ad9" stroke-width="4" stroke-linecap="round"/></g>
<g transform="translate(130 92)" filter="url(#labGlow)"><rect width="103" height="95" rx="22" fill="url(#labIg)" stroke="#43d8ff" stroke-width="3"/><rect x="26" y="19" width="50" height="50" rx="14" fill="none" stroke="white" stroke-width="7"/><circle cx="51" cy="44" r="13" fill="none" stroke="white" stroke-width="6"/><circle cx="69" cy="27" r="4" fill="white"/><path d="M47 95 56 109 66 95" fill="url(#labIg)"/></g>
<g transform="translate(342 83)" filter="url(#labGlow)"><rect width="110" height="96" rx="22" fill="url(#labYt)" stroke="#ff74dc" stroke-width="3"/><path d="M45 29 78 48 45 67Z" fill="white"/><path d="M50 96 61 111 70 96" fill="url(#labYt)"/></g>
<g transform="translate(476 101)" filter="url(#labGlow)"><rect width="98" height="88" rx="21" fill="url(#labStat)" stroke="#35dfff" stroke-width="3"/><rect x="24" y="48" width="10" height="22" rx="3" fill="white"/><rect x="43" y="35" width="10" height="35" rx="3" fill="white"/><rect x="62" y="21" width="10" height="49" rx="3" fill="white"/><path d="M43 88 54 102 64 88" fill="url(#labStat)"/></g>
<g transform="translate(276 28)" filter="url(#labGlow)"><circle cx="40" cy="40" r="38" fill="url(#labHeart)" stroke="#ff95e9" stroke-width="3"/><path d="M40 60S15 45 15 30c0-11 15-16 25-4 10-12 25-7 25 4 0 15-25 30-25 30Z" fill="white"/><path d="M36 78h8l-4 21Z" fill="#ff45b6"/></g>
<g transform="translate(282 150)" filter="url(#labGlow)"><rect width="75" height="70" rx="18" fill="#23b9e9" stroke="#73eaff" stroke-width="3"/><path d="M20 24h35v24H34l-10 8v-8h-4Z" fill="white"/><path d="M31 70 38 80 46 70" fill="#23b9e9"/></g>
<g fill="#fff" filter="url(#labSoftGlow)"><path d="m103 177 6 13 13 6-13 6-6 13-6-13-13-6 13-6Z"/><path d="m471 55 5 11 11 5-11 5-5 11-5-11-11-5 11-5Z"/><circle cx="89" cy="132" r="4"/><circle cx="533" cy="74" r="4"/><circle cx="238" cy="44" r="3"/></g>
</svg>
</div>
        </section>
        <aside class="lab-team"><h3>Seu time por trás dos resultados</h3><p>Nosso time está trabalhando todos os dias para fazer a sua marca crescer.</p><div class="lab-team-avatars"><div class="lab-team-avatar">A</div><div class="lab-team-avatar">B</div><div class="lab-team-avatar">C</div><div class="lab-team-avatar">+1</div></div></aside>
    </div>

    <section class="lab-panel lab-attention"><div class="lab-panel-head"><h2>⚠ Precisa da sua atenção</h2></div><div class="lab-list">
        <div class="lab-row"><div class="lab-mini">✓</div><div><b>3 conteúdos aguardando sua aprovação</b><span>Revise as peças para liberar a próxima etapa.</span></div><button class="lab-action" data-go="aprovacoes">Revisar</button></div>
        <div class="lab-row"><div class="lab-mini">✎</div><div><b>1 solicitação aguardando retorno</b><span>A equipe precisa de uma decisão sua.</span></div><button class="lab-action" data-go="solicitacoes">Responder</button></div>
        <div class="lab-row"><div class="lab-mini">!</div><div><b>Instagram requer reconexão</b><span>Exemplo demonstrativo de integração expirada.</span></div><button class="lab-action" data-go="canais">Reconectar</button></div>
    </div></section>

    <div class="lab-kpis"><div class="lab-card"><small>Conteúdos pendentes</small><strong>6</strong><em>Produção + revisão + aprovação</em></div><div class="lab-card"><small>Aprovações</small><strong>3</strong><em>Aguardando sua ação</em></div><div class="lab-card"><small>Posts agendados</small><strong>18</strong><em>Próximos 7 dias</em></div><div class="lab-card"><small>Resultados</small><strong>—</strong><em>Somente com analytics real</em></div></div>

    <div class="lab-grid2">
        <section class="lab-panel"><div class="lab-panel-head"><h2>Próximas publicações</h2><button class="lab-link" data-go="calendario">Ver calendário ›</button></div><div class="lab-list">
            <div class="lab-row"><div class="lab-mini">09:30</div><div><b>Dica rápida</b><span>Hoje · Instagram · Feed</span></div><span class="lab-status">Agendado</span></div>
            <div class="lab-row"><div class="lab-mini">14:00</div><div><b>Case de sucesso</b><span>Hoje · Facebook · Feed</span></div><span class="lab-status">Agendado</span></div>
            <div class="lab-row"><div class="lab-mini">19:00</div><div><b>Bastidores</b><span>Qui · TikTok · Vídeo</span></div><span class="lab-status">Agendado</span></div>
        </div></section>
        <section class="lab-panel"><div class="lab-panel-head"><h2>Aprovações</h2><button class="lab-link" data-go="aprovacoes">Ver todas ›</button></div><div class="lab-grid3">
            <div class="lab-card"><span class="lab-chip warn">v2</span><h3>Tendências que movem marcas</h3><p>Instagram · Carrossel</p><button class="lab-action ok">Aprovar</button> <button class="lab-action adjust">Ajuste</button></div>
            <div class="lab-card"><span class="lab-chip warn">v1</span><h3>3 dicas para mais engajamento</h3><p>Facebook · Imagem</p><button class="lab-action ok">Aprovar</button> <button class="lab-action adjust">Ajuste</button></div>
            <div class="lab-card"><span class="lab-chip warn">v3</span><h3>Bastidores</h3><p>TikTok · Vídeo</p><button class="lab-action ok">Aprovar</button> <button class="lab-action adjust">Ajuste</button></div>
        </div></section>
    </div>
</section>

<section class="lab-page" data-view="conteudos"><section class="lab-panel"><div class="lab-panel-head"><h2>Conteúdos</h2><span class="lab-chip">Ciclo editorial por status</span></div><div class="lab-columns">
<div class="lab-col"><h3>Em produção · 2</h3><div class="lab-item"><b>Enquete de sábado</b><p>Stories · Instagram</p></div><div class="lab-item"><b>Reel de bastidores</b><p>Vídeo · TikTok</p></div></div>
<div class="lab-col"><h3>Em revisão · 1</h3><div class="lab-item"><b>Post institucional</b><p>Feed · Facebook</p></div></div>
<div class="lab-col"><h3>Aguardando aprovação · 3</h3><div class="lab-item"><b>Tendências 2025</b><p>Carrossel · Instagram</p></div><div class="lab-item"><b>3 dicas</b><p>Imagem · Facebook</p></div></div>
<div class="lab-col"><h3>Aprovados · 2</h3><div class="lab-item"><b>Case de sucesso</b><p>Feed · Facebook</p></div></div>
<div class="lab-col"><h3>Agendados · 18</h3><div class="lab-item"><b>Dica rápida</b><p>Feed · Instagram</p></div></div>
<div class="lab-col"><h3>Publicados · 42</h3><div class="lab-item"><b>Lançamento</b><p>Stories · Instagram</p></div></div>
</div></section></section>

<section class="lab-page" data-view="calendario"><section class="lab-panel"><div class="lab-panel-head"><h2>Calendário</h2><span class="lab-chip">Horário real de publicação</span></div>
<div class="lab-day"><b>Segunda-feira</b><div class="lab-time"><strong>09:30</strong><div><b>Dica rápida</b><br><span>Instagram · Feed</span></div></div><div class="lab-time"><strong>18:15</strong><div><b>Enquete</b><br><span>Instagram · Stories</span></div></div></div>
<div class="lab-day"><b>Terça-feira</b><div class="lab-time"><strong>14:00</strong><div><b>Case de sucesso</b><br><span>Facebook · Feed</span></div></div></div>
<div class="lab-day"><b>Quinta-feira</b><div class="lab-time"><strong>19:00</strong><div><b>Bastidores</b><br><span>TikTok · Vídeo</span></div></div></div>
</section></section>

<section class="lab-page" data-view="aprovacoes"><section class="lab-panel"><div class="lab-panel-head"><h2>Aprovações</h2><span class="lab-chip warn">3 pendentes</span></div><div class="lab-grid3">
<div class="lab-card"><span class="lab-chip warn">v2</span><h3>Tendências que movem marcas</h3><p>Instagram · Carrossel · publica dia 16</p><button class="lab-action ok">Aprovar</button> <button class="lab-action adjust">Pedir ajuste</button></div>
<div class="lab-card"><span class="lab-chip warn">v1</span><h3>3 dicas para mais engajamento</h3><p>Facebook · Imagem · publica dia 17</p><button class="lab-action ok">Aprovar</button> <button class="lab-action adjust">Pedir ajuste</button></div>
<div class="lab-card"><span class="lab-chip warn">v3</span><h3>Bastidores</h3><p>TikTok · Vídeo · publica dia 14</p><button class="lab-action ok">Aprovar</button> <button class="lab-action adjust">Pedir ajuste</button></div>
</div></section></section>

<section class="lab-page" data-view="desempenho"><section class="lab-panel"><div class="lab-panel-head"><h2>Desempenho</h2><span class="lab-chip off">Analytics não conectado no laboratório</span></div><div class="lab-kpis">
<div class="lab-card"><small>Engajamento</small><strong>—</strong><em>Últimos 30 dias</em></div><div class="lab-card"><small>Alcance</small><strong>—</strong><em>Últimos 30 dias</em></div><div class="lab-card"><small>Crescimento</small><strong>—</strong><em>Últimos 30 dias</em></div><div class="lab-card"><small>Performance</small><strong>—</strong><em>Sem score decorativo</em></div>
</div><div class="lab-testnote">Na versão real, números só aparecem quando a integração de analytics fornecer fonte, período e valor.</div></section></section>

<section class="lab-page" data-view="solicitacoes"><section class="lab-panel"><div class="lab-panel-head"><h2>Solicitações</h2><button class="lab-link">+ Nova solicitação</button></div><div class="lab-list">
<div class="lab-row"><div class="lab-mini">✎</div><div><b>Alteração de legenda</b><span>Campanha promocional</span></div><span class="lab-status warn">Aguardando cliente</span></div>
<div class="lab-row"><div class="lab-mini">✦</div><div><b>Novo post institucional</b><span>Sobre nossos serviços</span></div><span class="lab-status">Em produção</span></div>
<div class="lab-row"><div class="lab-mini">?</div><div><b>Campanha de fim de ano</b><span>Briefing em análise</span></div><span class="lab-status">Em análise</span></div>
<div class="lab-row"><div class="lab-mini">✓</div><div><b>Revisão de identidade</b><span>Cores e tipografia</span></div><span class="lab-status ok">Concluída</span></div>
</div></section></section>

<section class="lab-page" data-view="canais"><section class="lab-panel"><div class="lab-panel-head"><h2>Canais</h2><span class="lab-chip">Estado real da integração</span></div><div class="lab-list">
<div class="lab-row"><div class="lab-mini">IG</div><div><b>Instagram</b><span>Conta demonstrativa</span></div><span class="lab-status warn">Token expirado</span></div>
<div class="lab-row"><div class="lab-mini">f</div><div><b>Facebook</b><span>Conta demonstrativa</span></div><span class="lab-status ok">Conectado</span></div>
<div class="lab-row"><div class="lab-mini">in</div><div><b>LinkedIn</b><span>Sem autorização</span></div><span class="lab-status">Integração pendente</span></div>
<div class="lab-row"><div class="lab-mini">▶</div><div><b>YouTube</b><span>Conteúdo planejado, sem executor</span></div><span class="lab-status">Somente planejamento</span></div>
</div><div class="lab-testnote" style="margin-top:10px">Estados desta página são demonstrativos. No produto real, “Conectado” deve vir exclusivamente da integração autorizada.</div></section></section>

<section class="lab-page" data-view="arquivos"><section class="lab-panel"><div class="lab-panel-head"><h2>Arquivos</h2><span class="lab-chip">Biblioteca da marca</span></div><div class="lab-grid3">
<div class="lab-card"><small>Identidade visual</small><strong>12</strong><em>logos, cores e guias</em></div><div class="lab-card"><small>Peças aprovadas</small><strong>34</strong><em>criativos finais</em></div><div class="lab-card"><small>Referências</small><strong>8</strong><em>inspirações e exemplos</em></div>
</div></section></section>

<section class="lab-page" data-view="plano"><section class="lab-panel"><div class="lab-panel-head"><h2>Plano e uso</h2><span class="lab-chip">Ciclo vigente</span></div><div class="lab-grid2">
<div class="lab-card"><small>Plano atual</small><strong>Vitrine Pro</strong><em>Dado demonstrativo</em></div><div class="lab-card"><small>Franquia</small><strong>40 conteúdos</strong><em>26 utilizados · 14 disponíveis</em><div class="lab-meter"><i></i></div></div>
</div></section></section>

<section class="lab-page" data-view="conta"><section class="lab-panel"><div class="lab-panel-head"><h2>Conta</h2></div><div class="lab-list">
<div class="lab-row"><div class="lab-mini">JM</div><div><b>Julia Martins</b><span>Nome demonstrativo</span></div><span class="lab-chip off">Cliente</span></div>
<div class="lab-row"><div class="lab-mini">V</div><div><b>Sua Empresa</b><span>Marca demonstrativa</span></div><span class="lab-chip off">Marca</span></div>
<div class="lab-row"><div class="lab-mini">✉</div><div><b>Notificações</b><span>E-mail + Painel</span></div><span class="lab-chip ok">Ativas</span></div>
</div></section></section>

</main>
</div>

<nav class="lab-mobile-nav" aria-label="Navegação mobile"><button class="active" data-page="painel">Painel</button><button data-page="conteudos">Conteúdos</button><button data-page="calendario">Calendário</button><button data-page="aprovacoes">Aprovações</button><button id="labMoreButton">Mais</button></nav>
<div class="lab-more-sheet" id="labMore"><div class="lab-more-grid"><button data-page="desempenho">Desempenho</button><button data-page="solicitacoes">Solicitações</button><button data-page="canais">Canais</button><button data-page="arquivos">Arquivos</button><button data-page="plano">Plano e uso</button><button data-page="conta">Conta</button></div></div>
</div>

<script>
(() => {
 const root=document.getElementById('socialLab'); if(!root) return;
 const pages=[...root.querySelectorAll('.lab-page')], buttons=[...root.querySelectorAll('[data-page]')], more=document.getElementById('labMore'), moreButton=document.getElementById('labMoreButton');
 const show=(name)=>{pages.forEach(p=>p.classList.toggle('active',p.dataset.view===name));buttons.forEach(b=>b.classList.toggle('active',b.dataset.page===name));if(more)more.classList.remove('open');window.scrollTo({top:0,behavior:'smooth'});};
 buttons.forEach(b=>b.addEventListener('click',()=>show(b.dataset.page)));root.querySelectorAll('[data-go]').forEach(b=>b.addEventListener('click',e=>{e.preventDefault();show(b.dataset.go)}));if(moreButton&&more)moreButton.addEventListener('click',()=>more.classList.toggle('open'));
})();
</script>
</x-filament-panels::page>