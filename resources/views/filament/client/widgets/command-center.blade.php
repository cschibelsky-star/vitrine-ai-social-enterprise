<x-filament-widgets::widget>
<style>
:root{
  --vsm-bg:#040712;--vsm-bg2:#07101f;--vsm-card:#091126;--vsm-card2:#0d1630;
  --vsm-line:rgba(100,91,255,.26);--vsm-purple:#9326ff;--vsm-purple2:#c236ff;
  --vsm-blue:#087cff;--vsm-cyan:#20ddff;--vsm-pink:#ff3fd1;--vsm-gold:#ffc400;
  --vsm-green:#18e89b;--vsm-text:#f7f8ff;--vsm-muted:#98a3bd
}
.fi-sidebar,.fi-topbar,.fi-page-header{display:none!important}
.fi-main{padding:0!important;margin:0!important}
.fi-main-ctn{max-width:none!important;width:100%!important;padding:0!important}
.fi-page{gap:0!important}
.fi-wi,.fi-wi-widget{width:100%!important;padding:0!important;margin:0!important}
body{background:#030610!important}
.vsm-shell{min-height:100vh;display:grid;grid-template-columns:245px minmax(0,1fr);background:
radial-gradient(circle at 48% 7%,rgba(63,35,186,.08),transparent 26%),
linear-gradient(180deg,#040712,#030610 60%,#02040b);color:var(--vsm-text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
.vsm-side{position:sticky;top:0;height:100vh;padding:24px 16px 18px;border-right:1px solid rgba(99,76,255,.22);
background:linear-gradient(180deg,#050917 0%,#030712 72%,#05081a 100%);display:flex;flex-direction:column;z-index:20}
.vsm-logo{display:flex;align-items:center;gap:10px;margin:2px 6px 27px}
.vsm-logo-mark{position:relative;width:46px;height:46px;border-radius:12px;display:grid;place-items:center;
background:radial-gradient(circle at 52% 45%,rgba(122,61,255,.25),transparent 55%);
filter:drop-shadow(0 0 12px rgba(120,57,255,.52))}
.vsm-logo-mark svg{width:38px;height:38px}
.vsm-logo-copy{line-height:.88}.vsm-logo-copy b{display:block;color:#fff;font-size:1.25rem;font-weight:1000;font-style:italic;letter-spacing:-.06em}
.vsm-logo-copy span{display:block;color:var(--vsm-gold);font-size:.66rem;font-weight:1000;letter-spacing:.08em;margin-top:7px}
.vsm-nav{display:grid;gap:5px}.vsm-nav a{min-height:42px;padding:0 12px;display:flex;align-items:center;gap:11px;border-radius:9px;
color:#b7c0d7;text-decoration:none;font-size:.78rem;font-weight:750;border:1px solid transparent}
.vsm-nav a svg{width:18px;height:18px;opacity:.86}.vsm-nav a.active{color:#fff;background:linear-gradient(90deg,rgba(90,27,199,.8),rgba(81,28,177,.28));
border-color:rgba(166,54,255,.65);box-shadow:inset 3px 0 0 #2bdfff,0 0 20px rgba(115,40,255,.16)}
.vsm-nav a.active svg{color:#2bdfff;filter:drop-shadow(0 0 6px rgba(43,223,255,.55))}
.vsm-badge{margin-left:auto;min-width:22px;height:22px;padding:0 6px;border-radius:999px;display:grid;place-items:center;background:#a830ff;color:#fff;font-size:.58rem;font-weight:950}
.vsm-support{margin-top:auto;position:relative;overflow:hidden;border-radius:16px;padding:18px 15px;border:1px solid rgba(145,54,255,.65);
background:radial-gradient(circle at 80% 7%,rgba(119,40,255,.4),transparent 34%),linear-gradient(155deg,#130d3a,#071228);
box-shadow:0 0 26px rgba(94,37,255,.16)}
.vsm-support:after{content:"";position:absolute;right:-16px;top:7px;width:70px;height:70px;border-radius:50%;background:radial-gradient(circle,#d94bff 0 5%,#6d2bff 28%,transparent 64%);filter:blur(.2px)}
.vsm-support-icon{width:28px;height:28px;border-radius:8px;display:grid;place-items:center;background:linear-gradient(145deg,#9c2aff,#5417c8);margin-bottom:10px;box-shadow:0 0 14px rgba(160,45,255,.4)}
.vsm-support strong{display:block;font-size:.72rem;color:#fff;letter-spacing:.04em}.vsm-support b{display:block;margin-top:4px;font-size:.95rem;color:#fff}
.vsm-support p{margin:8px 0 13px;color:#aeb8d1;font-size:.64rem;line-height:1.35}
.vsm-support a{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:0 13px;border-radius:7px;background:var(--vsm-gold);color:#151000;text-decoration:none;font-size:.62rem;font-weight:950;box-shadow:0 0 14px rgba(255,196,0,.2)}
.vsm-main{min-width:0;padding:18px 20px 15px}
.vsm-top{height:52px;display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:8px}
.vsm-hi{font-size:.82rem;color:#f6f8ff}.vsm-top-actions{display:flex;align-items:center;gap:11px}
.vsm-adjust{display:inline-flex;align-items:center;gap:7px;min-height:36px;padding:0 15px;border-radius:8px;background:linear-gradient(135deg,#6f15e8,#ad18ff);
border:1px solid rgba(210,87,255,.6);color:#fff;text-decoration:none;font-size:.7rem;font-weight:900;box-shadow:0 0 18px rgba(155,34,255,.22)}
.vsm-bell{width:38px;height:38px;display:grid;place-items:center;border-left:1px solid rgba(255,255,255,.08);border-right:1px solid rgba(255,255,255,.08);color:#fff}
.vsm-bell svg{width:18px;height:18px}.vsm-profile{display:flex;align-items:center;gap:9px}.vsm-avatar{width:35px;height:35px;border-radius:50%;border:2px solid #fff;
background:radial-gradient(circle at 50% 30%,#ffd9bd 0 15%,transparent 16%),radial-gradient(circle at 51% 77%,#332040 0 34%,transparent 35%),linear-gradient(145deg,#7343ad,#0e76ff);box-shadow:0 0 16px rgba(100,63,255,.3)}
.vsm-profile b{display:block;color:#fff;font-size:.68rem}.vsm-profile span{display:block;color:#94a0b8;font-size:.56rem;margin-top:2px}
.vsm-hero-row{display:grid;grid-template-columns:minmax(0,1.9fr) minmax(260px,.68fr);gap:10px}
.vsm-hero{position:relative;overflow:hidden;min-height:185px;border-radius:14px;border:1px solid rgba(85,72,188,.22);background:
radial-gradient(circle at 76% 50%,rgba(88,28,255,.23),transparent 23%),
radial-gradient(circle at 90% 70%,rgba(223,43,255,.18),transparent 24%),
linear-gradient(135deg,#050918 0%,#071029 58%,#07051a 100%)}
.vsm-hero-copy{position:relative;z-index:5;width:55%;padding:20px 0 0 19px}
.vsm-hero h1{margin:0;font-size:clamp(2.4rem,4vw,4.15rem);line-height:.88;letter-spacing:-.07em;font-weight:1000;font-style:italic;text-transform:uppercase;color:#fff}
.vsm-hero h1 span{display:block;color:var(--vsm-gold)}.vsm-hero p{margin:10px 0 0;color:#d3d9e9;font-size:.78rem}
.vsm-tech{position:absolute;right:2.5%;top:1%;width:43%;height:98%;perspective:700px}
.vsm-ring{position:absolute;left:10%;top:32%;width:74%;height:46%;border:2px solid rgba(92,70,255,.55);border-radius:50%;transform:rotate(-8deg);box-shadow:0 0 22px rgba(88,62,255,.32)}
.vsm-device{position:absolute;left:19%;top:35%;width:62%;height:44%;border-radius:22px;transform:rotateX(58deg) rotateZ(-10deg);
background:linear-gradient(145deg,#133cff,#15115f 58%,#7027ff);border:3px solid #17e4ff;box-shadow:0 0 14px #14c9ff,0 0 36px #4b2eff,0 0 58px rgba(226,48,255,.48),inset 0 0 20px rgba(255,255,255,.18)}
.vsm-device:after{content:"";position:absolute;inset:11px;border-radius:15px;background:radial-gradient(circle at 52% 50%,rgba(227,53,255,.62),transparent 48%),#060820}
.vsm-orbit-icon{position:absolute;width:44px;height:44px;border-radius:11px;display:grid;place-items:center;color:#fff;border:1px solid rgba(255,255,255,.34);z-index:4}
.vsm-orbit-icon svg{width:23px;height:23px}.vsm-orbit-icon.i1{left:3%;top:27%;background:linear-gradient(145deg,#7d2aff,#ff2c89);box-shadow:0 0 17px #a82cff}
.vsm-orbit-icon.i2{right:21%;top:29%;background:linear-gradient(145deg,#ff123e,#e80c6f);box-shadow:0 0 17px #ff245e}
.vsm-orbit-icon.i3{right:2%;top:18%;background:linear-gradient(145deg,#0874ff,#6b2aff);box-shadow:0 0 17px #3b68ff}
.vsm-orbit-icon.i4{left:46%;top:3%;background:linear-gradient(145deg,#8c20ff,#ff2ab9);box-shadow:0 0 17px #c62dff}
.vsm-orbit-icon.i5{left:49%;bottom:3%;background:linear-gradient(145deg,#006fff,#5b2cff);box-shadow:0 0 17px #1f74ff}
.vsm-team{position:relative;overflow:hidden;min-height:185px;padding:19px 18px;border-radius:14px;border:1px solid rgba(179,59,255,.48);
background:radial-gradient(circle at 87% 27%,rgba(217,55,255,.2),transparent 26%),linear-gradient(155deg,#11122e,#150a31)}
.vsm-team h3{margin:0 0 11px;color:#fff;font-size:.78rem}.vsm-team p{margin:0;color:#b5bfd6;font-size:.7rem;line-height:1.5;max-width:185px}
.vsm-team-star{position:absolute;right:20px;top:55px;color:#e958ff;font-size:2.25rem;text-shadow:0 0 18px #a52bff}
.vsm-faces{display:flex;margin-top:20px}.vsm-face{position:relative;width:35px;height:35px;border-radius:50%;margin-right:-5px;border:2px solid #0c1225;overflow:hidden;background:linear-gradient(145deg,#573163,#19315b)}
.vsm-face:before{content:"";position:absolute;left:50%;top:7px;transform:translateX(-50%);width:12px;height:12px;border-radius:50%;background:#efc7ad}
.vsm-face:after{content:"";position:absolute;left:50%;bottom:-2px;transform:translateX(-50%);width:25px;height:20px;border-radius:14px 14px 4px 4px;background:#374267}
.vsm-face:nth-child(2){background:linear-gradient(145deg,#60492e,#4d205c)}.vsm-face:nth-child(3){background:linear-gradient(145deg,#25476d,#542253)}
.vsm-face.more{display:grid;place-items:center;background:#1a2342;color:#fff;font-size:.6rem;font-weight:950}.vsm-face.more:before,.vsm-face.more:after{display:none}
.vsm-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;margin-top:10px}
.vsm-kpi{min-height:78px;display:grid;grid-template-columns:42px 1fr;gap:10px;align-items:center;padding:10px 12px;border-radius:10px;border:1px solid rgba(72,76,161,.38);
background:linear-gradient(180deg,#0b132b,#071021)}.vsm-kpi-icon{width:39px;height:39px;border-radius:9px;display:grid;place-items:center;background:linear-gradient(145deg,#5d18d8,#9624ff);color:#fff;box-shadow:0 0 14px rgba(117,32,255,.25)}
.vsm-kpi.blue .vsm-kpi-icon{background:linear-gradient(145deg,#053be2,#0a81ff)}.vsm-kpi.gold{border-color:rgba(211,148,18,.42)}.vsm-kpi.gold .vsm-kpi-icon{background:linear-gradient(145deg,#6f4b06,#d38d00)}
.vsm-kpi-icon svg{width:20px;height:20px}.vsm-kpi small{display:block;color:#bdc5d9;font-size:.52rem;font-weight:950;text-transform:uppercase}.vsm-kpi strong{display:block;color:#fff;font-size:1.45rem;line-height:1;margin-top:4px}.vsm-kpi em{display:block;color:#8f9ab3;font-size:.56rem;font-style:normal;margin-top:4px}
.vsm-kpi em.good{color:#28e893}
.vsm-grid-main{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(0,1fr);gap:9px;margin-top:9px}
.vsm-card{border-radius:10px;border:1px solid rgba(75,84,167,.35);background:linear-gradient(180deg,#091126,#060d1c);padding:11px;box-shadow:0 10px 28px rgba(0,0,0,.14)}
.vsm-card-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:9px}.vsm-card-head h3{margin:0;color:#fff;font-size:.72rem;font-weight:950;text-transform:uppercase;display:flex;align-items:center;gap:7px}
.vsm-card-head h3 i{font-style:normal;color:#be37ff}.vsm-link{display:inline-flex;align-items:center;min-height:28px;padding:0 9px;border-radius:6px;border:1px solid rgba(146,158,198,.34);color:#e9edfb;text-decoration:none;font-size:.54rem;font-weight:850}
.vsm-weekline{display:flex;align-items:center;gap:15px;margin:0 0 8px 3px;color:#cfd5e6;font-size:.62rem}.vsm-calendar{display:grid;grid-template-columns:38px repeat(7,minmax(72px,1fr));border-top:1px solid rgba(110,124,170,.14);border-left:1px solid rgba(110,124,170,.1)}
.vsm-cell{min-height:38px;padding:4px;border-right:1px solid rgba(110,124,170,.11);border-bottom:1px solid rgba(110,124,170,.11);font-size:.52rem;color:#a3aec4}.vsm-day{min-height:auto;text-align:center;padding:6px 2px;color:#eef1ff;font-weight:900}
.vsm-time{display:flex;align-items:center}.vsm-event{padding:4px 5px;border-radius:5px;background:linear-gradient(145deg,rgba(98,25,176,.9),rgba(63,18,109,.85));border:1px solid rgba(185,55,255,.4);color:#fff;font-size:.5rem;line-height:1.15}
.vsm-event.blue{background:linear-gradient(145deg,rgba(0,67,170,.9),rgba(13,44,98,.9));border-color:rgba(32,116,255,.42)}.vsm-event.green{background:linear-gradient(145deg,rgba(0,89,70,.9),rgba(8,50,47,.9));border-color:rgba(21,200,148,.38)}.vsm-event.red{background:linear-gradient(145deg,rgba(145,14,56,.9),rgba(71,18,45,.9));border-color:rgba(255,38,94,.42)}
.vsm-event b{display:block}.vsm-event span{display:block;color:#d9dfef;font-size:.46rem;margin-top:2px}.vsm-legend{display:flex;gap:14px;margin-top:8px;color:#97a3ba;font-size:.52rem}.vsm-dot{width:6px;height:6px;border-radius:50%;display:inline-block;margin-right:4px}.vsm-dot.purple{background:#a62cff}.vsm-dot.green{background:#16d495}.vsm-dot.gray{background:#6d7790}
.vsm-approvals{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}.vsm-approval{overflow:hidden;border-radius:8px;border:1px solid rgba(86,76,170,.4);background:#070d1b}
.vsm-thumb{height:115px;position:relative;overflow:hidden;background-size:cover;background-position:center;padding:10px;display:flex;align-items:flex-end}
.vsm-thumb:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 78% 24%,rgba(217,49,255,.42),transparent 29%),linear-gradient(145deg,#160623,#170b46 60%,#0d0a28)}
.vsm-thumb.blue:before{background:radial-gradient(circle at 76% 30%,rgba(0,171,255,.42),transparent 31%),linear-gradient(145deg,#07172f,#06113d)}.vsm-thumb.gold:before{background:radial-gradient(circle at 78% 33%,rgba(255,164,0,.26),transparent 34%),linear-gradient(145deg,#171006,#241506)}
.vsm-thumb.has-media:before{background:linear-gradient(180deg,rgba(3,5,15,.05),rgba(3,5,15,.82))}
.vsm-thumb-title{position:relative;z-index:2;color:#fff;font-size:.76rem;font-weight:1000;font-style:italic;line-height:.95;text-transform:uppercase;max-width:88%}.vsm-platform{position:absolute;right:7px;bottom:7px;z-index:3;width:22px;height:22px;border-radius:6px;display:grid;place-items:center;background:#8426ff;color:#fff;font-size:.52rem;font-weight:1000}
.vsm-approval-body{padding:7px}.vsm-meta{color:#aeb8ce;font-size:.52rem;margin-bottom:6px}.vsm-approval-actions{display:grid;grid-template-columns:1fr 1fr;gap:5px}.vsm-approve,.vsm-request{min-height:29px;border-radius:5px;font-size:.54rem;font-weight:900;cursor:pointer}
.vsm-approve{color:#b9ffdd;border:1px solid #0be79a;background:rgba(0,92,65,.38)}.vsm-request{color:#f0c4ff;border:1px solid #a52dff;background:rgba(77,18,116,.34)}
.vsm-grid-bottom{display:grid;grid-template-columns:1.45fr .78fr .76fr;gap:9px;margin-top:9px}.vsm-performance-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:7px}.vsm-perf{position:relative;overflow:hidden;min-height:125px;padding:9px;border-radius:8px;border:1px solid rgba(72,82,156,.35);background:linear-gradient(180deg,#0a132b,#071022)}
.vsm-perf small{display:block;color:#aeb8d0;font-size:.5rem;font-weight:900}.vsm-perf strong{display:block;color:#fff;font-size:1.32rem;margin-top:8px}.vsm-perf em{display:block;color:#20e58b;font-size:.5rem;font-style:normal;margin-top:4px}.vsm-spark{position:absolute;left:7px;right:7px;bottom:7px;height:39px}.vsm-spark svg{width:100%;height:100%}.vsm-spark polyline{fill:none;stroke:currentColor;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;filter:drop-shadow(0 0 4px currentColor)}
.vsm-score{width:62px;height:62px;margin:7px auto 0;border-radius:50%;display:grid;place-items:center;background:radial-gradient(circle,#081126 55%,transparent 57%),conic-gradient(#13e7ff,#9232ff,#ff42cf,#13e7ff);box-shadow:0 0 15px rgba(113,54,255,.2)}
.vsm-score b{font-size:1rem}.vsm-score span{font-size:.43rem;color:#b7c0d5}
.vsm-list{display:grid;gap:6px}.vsm-row{display:grid;grid-template-columns:25px 1fr auto;gap:7px;align-items:center;padding:7px;border-radius:7px;border:1px solid rgba(99,111,163,.17);background:#091123}.vsm-mini{width:24px;height:24px;border-radius:6px;display:grid;place-items:center;background:linear-gradient(145deg,#8f22ff,#cf20ff);font-size:.55rem}
.vsm-row-copy{min-width:0}.vsm-row-copy b{display:block;color:#fff;font-size:.56rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.vsm-row-copy span{display:block;color:#909bb4;font-size:.48rem;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.vsm-status{padding:4px 6px;border-radius:5px;font-size:.46rem;border:1px solid rgba(137,47,255,.5);color:#dcb4ff;background:rgba(96,27,155,.28)}.vsm-status.done{color:#79ffc0;border-color:rgba(0,209,118,.5);background:rgba(0,96,58,.32)}
.vsm-channel{display:grid;grid-template-columns:24px 1fr auto;gap:7px;align-items:center;padding:7px;border-radius:7px;border:1px solid rgba(99,111,163,.17);background:#091123}.vsm-channel-icon{width:23px;height:23px;border-radius:6px;display:grid;place-items:center;color:#fff}.vsm-channel-icon svg{width:14px;height:14px}
.vsm-connected{padding:4px 6px;border-radius:5px;color:#79ffc0;border:1px solid rgba(0,209,118,.48);background:rgba(0,96,58,.32);font-size:.45rem}
.vsm-footer{display:flex;align-items:center;justify-content:center;gap:15px;padding:9px 0 0;color:#7d88a3;font-size:.5rem}.vsm-footer i{color:#be31ff;font-style:normal}
.vsm-mobile-nav{display:none}
@media(max-width:1180px){.vsm-shell{grid-template-columns:210px minmax(0,1fr)}.vsm-hero-row{grid-template-columns:1fr}.vsm-team{display:none}.vsm-grid-main{grid-template-columns:1fr}.vsm-grid-bottom{grid-template-columns:1fr 1fr}.vsm-grid-bottom>.vsm-card:first-child{grid-column:1/-1}.vsm-performance-grid{grid-template-columns:repeat(4,1fr)}}
@media(max-width:820px){.vsm-shell{display:block}.vsm-side{display:none}.vsm-main{padding:10px 11px 88px}.vsm-top{height:44px}.vsm-profile>div:last-child,.vsm-bell{display:none}.vsm-adjust{min-height:33px;padding:0 11px}.vsm-hero{min-height:235px}.vsm-hero-copy{width:82%;padding-top:18px}.vsm-hero h1{font-size:2.6rem}.vsm-tech{right:-17%;top:45%;width:70%;height:53%}.vsm-kpis{grid-template-columns:repeat(2,1fr)}.vsm-calendar{grid-template-columns:38px repeat(7,118px);overflow-x:auto}.vsm-approvals{grid-template-columns:1fr}.vsm-thumb{height:160px}.vsm-grid-bottom{grid-template-columns:1fr}.vsm-grid-bottom>.vsm-card:first-child{grid-column:auto}.vsm-performance-grid{grid-template-columns:repeat(2,1fr)}.vsm-mobile-nav{position:fixed;display:grid;grid-template-columns:repeat(5,1fr);left:9px;right:9px;bottom:9px;z-index:80;padding:6px;border-radius:16px;border:1px solid rgba(115,44,255,.48);background:rgba(4,8,20,.97);backdrop-filter:blur(16px);box-shadow:0 0 22px rgba(80,25,180,.2)}.vsm-mobile-nav a{display:grid;place-items:center;min-height:46px;border-radius:11px;color:#b8c2db;text-decoration:none;font-size:.57rem;font-weight:850}.vsm-mobile-nav a.active{color:#fff;background:linear-gradient(145deg,rgba(54,61,145,.55),rgba(68,23,115,.62));border:1px solid rgba(136,48,255,.55)}}
</style>

@if(! $clientId)
  <div class="vsm-shell"><main class="vsm-main"><section class="vsm-card" style="padding:30px;text-align:center">Seu usuário precisa estar vinculado a um cliente para carregar o painel.</section></main></div>
@else
<div class="vsm-shell">
  <aside class="vsm-side">
    <div class="vsm-logo">
      <div class="vsm-logo-mark">
        <svg viewBox="0 0 48 48" fill="none"><path d="M29 3 12 25h10l-3 20 17-24H26l3-18Z" fill="url(#lg)"/><defs><linearGradient id="lg" x1="11" y1="5" x2="39" y2="42"><stop stop-color="#19E7FF"/><stop offset=".55" stop-color="#8C2CFF"/><stop offset="1" stop-color="#FF41D0"/></linearGradient></defs></svg>
      </div>
      <div class="vsm-logo-copy"><b>VITRINE</b><span>SOCIAL MÍDIA</span></div>
    </div>
    <nav class="vsm-nav">
      <a class="active" href="{{ \App\Filament\Client\Pages\ClientDashboard::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 11 12 4l9 7v9H4v-9Z"/><path d="M9 20v-6h6v6"/></svg>Painel</a>
      <a href="{{ \App\Filament\Client\Pages\Contents::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>Conteúdos</a>
      <a href="{{ \App\Filament\Client\Pages\CalendarPage::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg>Calendário</a>
      <a href="{{ \App\Filament\Client\Pages\Approvals::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.2 2.2 4.8-5"/></svg>Aprovações@if($approvals>0)<span class="vsm-badge">{{ $approvals }}</span>@endif</a>
      <a href="{{ \App\Filament\Client\Pages\Performance::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V9M10 19V5M16 19v-7M22 19V3"/></svg>Desempenho</a>
      <a href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16v12H8l-4 4V5Z"/></svg>Solicitações</a>
      <a href="{{ \App\Filament\Client\Pages\Channels::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12a7 7 0 0 1 7-7M5 17a12 12 0 0 1 12-12"/><circle cx="6" cy="18" r="2"/></svg>Canais</a>
      <a href="{{ \App\Filament\Client\Pages\Files::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h7l2 2h9v10H3V7Z"/></svg>Arquivos</a>
      <a href="{{ \App\Filament\Client\Pages\Balance::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/></svg>Consumo e Saldo</a>
      <a href="{{ \App\Filament\Client\Pages\Affiliates::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="8" r="3"/><circle cx="17" cy="7" r="2.5"/><path d="M3 20c.5-4 2.5-6 5-6s4.5 2 5 6M14 14c3 0 5 2 5.5 5"/></svg>Programa de Afiliados</a>
      <a href="{{ \App\Filament\Client\Pages\Account::getUrl() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c.7-4.5 3.3-7 8-7s7.3 2.5 8 7"/></svg>Conta</a>
    </nav>
    <div class="vsm-support">
      <div class="vsm-support-icon">💬</div><strong>DÚVIDAS?</strong><b>Fale com a gente!</b><p>Resposta em até<br>1h útil</p>
      <a href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">Abrir chat →</a>
    </div>
  </aside>

  <main class="vsm-main">
    <div class="vsm-top">
      <div class="vsm-hi">Olá, {{ $userName }}! 👋</div>
      <div class="vsm-top-actions">
        <a class="vsm-adjust" href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">✎ Solicitar ajuste</a>
        <div class="vsm-bell"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg></div>
        <div class="vsm-profile"><div class="vsm-avatar"></div><div><b>{{ $userName }}</b><span>Cliente</span></div></div>
      </div>
    </div>

    <div class="vsm-hero-row">
      <section class="vsm-hero">
        <div class="vsm-hero-copy"><h1>Sua presença digital <span>em um só lugar</span></h1><p>Acompanhe entregas, aprove conteúdos e veja resultados.</p></div>
        <div class="vsm-tech" aria-hidden="true">
          <div class="vsm-ring"></div><div class="vsm-device"></div>
          <div class="vsm-orbit-icon i1"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></div>
          <div class="vsm-orbit-icon i2"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 7.3a2.8 2.8 0 0 0-2-2C17.2 4.8 12 4.8 12 4.8s-5.2 0-7 .5a2.8 2.8 0 0 0-2 2A29 29 0 0 0 2.5 12 29 29 0 0 0 3 16.7a2.8 2.8 0 0 0 2 2c1.8.5 7 .5 7 .5s5.2 0 7-.5a2.8 2.8 0 0 0 2-2 29 29 0 0 0 .5-4.7 29 29 0 0 0-.5-4.7ZM10 15.5v-7l6 3.5-6 3.5Z"/></svg></div>
          <div class="vsm-orbit-icon i3"><svg viewBox="0 0 24 24" fill="currentColor"><rect x="4" y="12" width="3" height="8" rx="1"/><rect x="10.5" y="8" width="3" height="12" rx="1"/><rect x="17" y="4" width="3" height="16" rx="1"/></svg></div>
          <div class="vsm-orbit-icon i4"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7-4.6-9.2-8.7C.8 8.5 3 5 6.5 5c2 0 3.4 1.1 4.3 2.3C11.7 6.1 13.1 5 15.1 5c3.5 0 5.7 3.5 3.7 7.3C19 16.4 12 21 12 21Z"/></svg></div>
          <div class="vsm-orbit-icon i5"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 5h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 3v-3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/></svg></div>
        </div>
      </section>
      <aside class="vsm-team"><h3>Seu time por trás dos resultados</h3><p>Nosso time está trabalhando todos os dias para fazer a sua marca crescer.</p><div class="vsm-team-star">✦</div><div class="vsm-faces"><div class="vsm-face"></div><div class="vsm-face"></div><div class="vsm-face"></div><div class="vsm-face more">+1</div></div></aside>
    </div>

    <div class="vsm-kpis">
      <div class="vsm-kpi"><div class="vsm-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg></div><div><small>Conteúdos pendentes</small><strong>{{ $pending }}</strong><em>Aguardando produção</em></div></div>
      <div class="vsm-kpi blue"><div class="vsm-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.2 2.2 4.8-5"/></svg></div><div><small>Aprovações</small><strong>{{ $approvals }}</strong><em>Aguardando sua aprovação</em></div></div>
      <div class="vsm-kpi blue"><div class="vsm-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg></div><div><small>Posts agendados</small><strong>{{ $scheduledNext7 }}</strong><em>Próximos 7 dias</em></div></div>
      <div class="vsm-kpi gold"><div class="vsm-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V9M10 19V5M16 19v-7M22 19V3"/></svg></div><div><small>Alcance do mês</small><strong>{{ $reachValue ?? '—' }}</strong><em class="{{ $reachValue ? 'good' : '' }}">{{ $reachValue ? 'Dados sincronizados' : 'Aguardando analytics' }}</em></div></div>
    </div>

    <div class="vsm-grid-main">
      <section class="vsm-card">
        <div class="vsm-card-head"><h3><i>▣</i> Calendário Editorial</h3><a class="vsm-link" href="{{ \App\Filament\Client\Pages\CalendarPage::getUrl() }}">Ver calendário completo ›</a></div>
        <div class="vsm-weekline"><span>‹</span><b>{{ $weekStart->format('d') }} – {{ $weekEnd->format('d/m/Y') }}</b><span>›</span></div>
        <div class="vsm-calendar">
          <div class="vsm-cell vsm-day"></div>
          @foreach($calendarDays as $day)<div class="vsm-cell vsm-day">{{ strtoupper($day['date']->locale('pt_BR')->isoFormat('ddd D')) }}</div>@endforeach
          @foreach(['09:00','12:00','18:00'] as $time)
            <div class="vsm-cell vsm-time">{{ $time }}</div>
            @foreach($calendarDays as $day)
              <div class="vsm-cell">
                @foreach($day['items']->take(2) as $item)
                  @php $tone = $item->published_at ? 'green' : (str_contains(strtolower((string)$item->channel),'youtube') ? 'red' : (str_contains(strtolower((string)$item->channel),'facebook') || str_contains(strtolower((string)$item->channel),'linkedin') ? 'blue' : '')); @endphp
                  <div class="vsm-event {{ $tone }}"><b>{{ $item->title }}</b><span>{{ $item->channel ? ucfirst($item->channel) : ($item->format ?: 'Conteúdo') }}</span></div>
                @endforeach
              </div>
            @endforeach
          @endforeach
        </div>
        <div class="vsm-legend"><span><i class="vsm-dot purple"></i>Agendado</span><span><i class="vsm-dot green"></i>Publicado</span><span><i class="vsm-dot gray"></i>Rascunho</span></div>
      </section>

      <section class="vsm-card">
        <div class="vsm-card-head"><h3><i>♙</i> Aprovações</h3><a class="vsm-link" href="{{ \App\Filament\Client\Pages\Approvals::getUrl() }}">Ver todas ›</a></div>
        <div class="vsm-approvals">
          @forelse($approvalItems as $index => $item)
            @php $slideImage=$item->slides->first()?->image_path; $slideUrl=$slideImage ? asset('storage/'.ltrim($slideImage,'/')) : null; @endphp
            <article class="vsm-approval">
              <div class="vsm-thumb {{ $slideUrl ? 'has-media' : ($index===1 ? 'blue' : ($index===2 ? 'gold' : '')) }}" @if($slideUrl) style="background-image:url('{{ $slideUrl }}')" @endif>
                <div class="vsm-thumb-title">{{ $item->title }}</div><div class="vsm-platform">{{ strtoupper(mb_substr($item->channel ?: 'C',0,1)) }}</div>
              </div>
              <div class="vsm-approval-body"><div class="vsm-meta">{{ $item->channel ? ucfirst($item->channel) : 'Canal' }} • {{ $item->format ?: ($item->content_type ?: 'Conteúdo') }}</div>
                <div class="vsm-approval-actions"><button type="button" wire:click="approveContent({{ $item->id }})" wire:loading.attr="disabled" class="vsm-approve">✓ Aprovar</button><button type="button" wire:click="requestAdjustment({{ $item->id }})" wire:loading.attr="disabled" class="vsm-request">Pedir ajuste</button></div>
              </div>
            </article>
          @empty
            <article class="vsm-approval"><div class="vsm-thumb"><div class="vsm-thumb-title">Tudo em dia</div></div><div class="vsm-approval-body"><div class="vsm-meta">Nenhum conteúdo aguardando aprovação.</div></div></article>
          @endforelse
        </div>
      </section>
    </div>

    <div class="vsm-grid-bottom">
      <section class="vsm-card">
        <div class="vsm-card-head"><h3><i>▥</i> Desempenho do mês</h3></div>
        <div class="vsm-performance-grid">
          <div class="vsm-perf"><small>ENGAJAMENTO</small><strong>{{ $engagementValue ?? '—' }}</strong><em>{{ $engagementValue ? 'Dados sincronizados' : 'Analytics pendente' }}</em><div class="vsm-spark" style="color:#b72cff"><svg viewBox="0 0 100 36" preserveAspectRatio="none"><polyline points="2,29 13,20 24,25 35,13 47,17 59,8 71,12 84,5 98,9"/></svg></div></div>
          <div class="vsm-perf"><small>ALCANCE</small><strong>{{ $reachValue ?? '—' }}</strong><em>{{ $reachValue ? 'Dados sincronizados' : 'Analytics pendente' }}</em><div class="vsm-spark" style="color:#16b8ff"><svg viewBox="0 0 100 36" preserveAspectRatio="none"><polyline points="2,31 12,21 23,24 34,12 45,18 57,9 69,14 81,6 98,3"/></svg></div></div>
          <div class="vsm-perf"><small>CRESCIMENTO</small><strong>{{ $growthValue ?? '—' }}</strong><em>{{ $growthValue ? 'Dados sincronizados' : 'Analytics pendente' }}</em><div class="vsm-spark" style="color:#ff35c9"><svg viewBox="0 0 100 36" preserveAspectRatio="none"><polyline points="2,28 14,24 25,18 37,22 49,10 61,15 72,7 84,11 98,4"/></svg></div></div>
          <div class="vsm-perf"><small>PERFORMANCE</small><div class="vsm-score"><div><b>{{ $scorePercent }}</b><br><span>/100</span></div></div><em>Score médio {{ $scoreAverage }}</em></div>
        </div>
      </section>

      <section class="vsm-card"><div class="vsm-card-head"><h3><i>☷</i> Solicitações</h3><a class="vsm-link" href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">Ver todas ›</a></div>
        <div class="vsm-list">@forelse($requests as $item)<div class="vsm-row"><div class="vsm-mini">✎</div><div class="vsm-row-copy"><b>{{ $item->title }}</b><span>{{ $item->objective ?: ($item->channel ?: 'Solicitação de conteúdo') }}</span></div><span class="vsm-status">{{ $item->status ?: 'Aberta' }}</span></div>@empty<div class="vsm-row"><div class="vsm-mini">✓</div><div class="vsm-row-copy"><b>Nenhuma solicitação</b><span>Seu fluxo está em dia.</span></div><span class="vsm-status done">Em dia</span></div>@endforelse</div>
      </section>

      <section class="vsm-card"><div class="vsm-card-head"><h3><i>♧</i> Canais conectados</h3></div>
        <div class="vsm-list">@forelse($channels as $channel)
          @php $ch=strtolower((string)$channel->channel); @endphp
          <div class="vsm-channel"><div class="vsm-channel-icon" style="background:{{ str_contains($ch,'youtube') ? '#e31a3d' : (str_contains($ch,'facebook') ? '#1674ea' : (str_contains($ch,'linkedin') ? '#0a66c2' : (str_contains($ch,'tiktok') ? '#111' : 'linear-gradient(145deg,#7d2aff,#f42686)'))) }}">
          @if(str_contains($ch,'youtube'))<svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 7.3a2.8 2.8 0 0 0-2-2C17.2 4.8 12 4.8 12 4.8s-5.2 0-7 .5a2.8 2.8 0 0 0-2 2A29 29 0 0 0 2.5 12 29 29 0 0 0 3 16.7a2.8 2.8 0 0 0 2 2c1.8.5 7 .5 7 .5s5.2 0 7-.5a2.8 2.8 0 0 0 2-2 29 29 0 0 0 .5-4.7 29 29 0 0 0-.5-4.7ZM10 15.5v-7l6 3.5-6 3.5Z"/></svg>
          @elseif(str_contains($ch,'facebook'))<b>f</b>@elseif(str_contains($ch,'linkedin'))<b>in</b>@elseif(str_contains($ch,'tiktok'))<b>♪</b>@else<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/></svg>@endif
          </div><div class="vsm-row-copy"><b>{{ ucfirst($channel->channel) }}</b><span>{{ $channel->total }} conteúdo(s) vinculados</span></div><span class="vsm-connected">Conectado</span></div>
        @empty<div class="vsm-channel"><div class="vsm-channel-icon" style="background:#252c40">•</div><div class="vsm-row-copy"><b>Nenhum canal</b><span>Conecte seus canais para começar.</span></div><span class="vsm-status">Pendente</span></div>@endforelse</div>
      </section>
    </div>

    <footer class="vsm-footer"><span>Vitrine Social Mídia © {{ now()->year }}</span><span>•</span><span>Todos os direitos reservados</span><i>ϟ</i><span>Transparência</span><span>•</span><span>Estratégia</span><span>•</span><span>Resultados</span></footer>
  </main>
</div>

<nav class="vsm-mobile-nav">
  <a class="active" href="{{ \App\Filament\Client\Pages\ClientDashboard::getUrl() }}">Painel</a>
  <a href="{{ \App\Filament\Client\Pages\Contents::getUrl() }}">Conteúdos</a>
  <a href="{{ \App\Filament\Client\Pages\CalendarPage::getUrl() }}">Calendário</a>
  <a href="{{ \App\Filament\Client\Pages\Approvals::getUrl() }}">Aprovações</a>
  <a href="{{ \App\Filament\Client\Pages\Performance::getUrl() }}">Desempenho</a>
</nav>
@endif
</x-filament-widgets::widget>

