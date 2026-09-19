<x-filament-widgets::widget>
<style>
    :root{--vsm-bg:#030712;--vsm-panel:#081020;--vsm-panel2:#0b1226;--vsm-line:rgba(112,86,255,.34);--vsm-purple:#8b2cff;--vsm-magenta:#e337ff;--vsm-blue:#0b7cff;--vsm-cyan:#1fe4ff;--vsm-gold:#ffbf00;--vsm-green:#18e49a;--vsm-text:#f8f9ff;--vsm-muted:#97a3c0}
    .fi-page-header{display:none!important}.fi-main-ctn{max-width:none!important}.fi-main{padding-top:12px!important}.fi-page{gap:0!important}.fi-wi{width:100%!important}.fi-wi-widget{padding:0!important}
    @media(min-width:1024px){.fi-topbar{display:none!important}}
    .fi-sidebar{background:linear-gradient(180deg,#050916,#030711 72%,#040813)!important;border-right:1px solid rgba(91,72,255,.28)!important}
    .fi-sidebar-header{min-height:106px!important;padding:22px 18px!important;background:transparent!important;border:0!important}
    .fi-sidebar-header .fi-logo{font-size:0!important;display:grid!important;line-height:1!important;gap:3px!important}
    .fi-sidebar-header .fi-logo:before{content:"VITRINE ⚡";font-size:1.75rem!important;font-weight:1000!important;font-style:italic!important;letter-spacing:-.07em!important;color:#fff!important;text-shadow:0 0 20px #6d42ff}
    .fi-sidebar-header .fi-logo:after{content:"SOCIAL MÍDIA";font-size:.82rem!important;font-weight:1000!important;letter-spacing:.04em!important;color:#ffc400!important}
    .fi-sidebar-nav{padding:8px 12px 18px!important}
    .fi-sidebar-item a{min-height:46px!important;padding:0 13px!important;border-radius:11px!important}
    .fi-sidebar-item-active a{background:linear-gradient(90deg,rgba(88,16,210,.66),rgba(104,15,234,.28))!important;border:1px solid rgba(178,64,255,.55)!important;box-shadow:0 0 20px rgba(120,24,255,.22),inset 3px 0 0 #1fd8ff!important}
    .fi-sidebar-item-label{font-weight:750!important;color:#eef1ff!important}
    .fi-sidebar-item a:before{display:grid;place-items:center;width:22px;min-width:22px;color:#aeb8d5;font-size:.95rem;font-weight:900}
    .fi-sidebar-item:nth-of-type(1) a:before{content:"⌂"}.fi-sidebar-item:nth-of-type(2) a:before{content:"▤"}.fi-sidebar-item:nth-of-type(3) a:before{content:"▣"}.fi-sidebar-item:nth-of-type(4) a:before{content:"✓"}.fi-sidebar-item:nth-of-type(5) a:before{content:"▥"}.fi-sidebar-item:nth-of-type(6) a:before{content:"☷"}.fi-sidebar-item:nth-of-type(7) a:before{content:"⌯"}.fi-sidebar-item:nth-of-type(8) a:before{content:"▱"}.fi-sidebar-item:nth-of-type(9) a:before{content:"◉"}.fi-sidebar-item:nth-of-type(10) a:before{content:"◇"}.fi-sidebar-item:nth-of-type(11) a:before{content:"●"}
    .fi-sidebar-item-active a:before{color:#26dcff;text-shadow:0 0 10px rgba(38,220,255,.65)}
    .fi-sidebar-nav:after{content:"💬\A\A DÚVIDAS?\A Fale com a gente!\A\A Resposta em até\A 1h útil\A\A      Abrir chat  →";white-space:pre-line;display:block;margin:28px 4px 10px;padding:18px 16px 20px;border-radius:16px;border:1px solid rgba(127,51,255,.7);background:radial-gradient(circle at 78% 10%,rgba(107,24,255,.4),transparent 32%),linear-gradient(160deg,#120c3d,#071128);color:#e9e9ff;font-size:.74rem;line-height:1.35;font-weight:800;box-shadow:0 0 28px rgba(97,35,255,.22)}
    .vsm-dashboard{display:grid;gap:12px;color:var(--vsm-text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;padding:0 4px 16px}
    .vsm-topline{display:flex;align-items:center;justify-content:space-between;gap:18px;min-height:48px}
    .vsm-greeting{font-size:.92rem;color:#f6f7ff}.vsm-actions{display:flex;align-items:center;gap:12px}
    .vsm-adjust{display:inline-flex;align-items:center;gap:8px;padding:10px 17px;border-radius:9px;border:1px solid rgba(204,72,255,.6);background:linear-gradient(135deg,#6d12ec,#ad0fff);box-shadow:0 0 24px rgba(162,31,255,.28);color:#fff;font-size:.78rem;font-weight:900;text-decoration:none}
    .vsm-bell{width:38px;height:38px;border-left:1px solid rgba(255,255,255,.1);border-right:1px solid rgba(255,255,255,.1);display:grid;place-items:center;color:#fff}.vsm-bell svg{width:18px;height:18px;filter:drop-shadow(0 0 6px rgba(255,255,255,.18))}
    .vsm-profile{display:flex;align-items:center;gap:9px}.vsm-avatar{width:36px;height:36px;border-radius:50%;display:grid;place-items:center;background:radial-gradient(circle at 50% 32%,#ffd8bd 0 15%,transparent 16%),radial-gradient(circle at 50% 78%,#2c173f 0 33%,transparent 34%),linear-gradient(145deg,#50236d,#0b7cff);border:2px solid #fff;overflow:hidden;box-shadow:0 0 18px rgba(102,74,255,.22)}
    .vsm-profile-meta{display:grid;gap:1px}.vsm-profile-meta b{font-size:.76rem;color:#fff}.vsm-profile-meta span{font-size:.66rem;color:#99a6c2}
    .vsm-hero-row{display:grid;grid-template-columns:minmax(0,1.9fr) minmax(270px,.72fr);gap:12px}
    .vsm-hero{position:relative;overflow:hidden;min-height:190px;border-radius:15px;padding:10px 18px 16px;background:radial-gradient(circle at 68% 38%,rgba(65,25,255,.28),transparent 22%),radial-gradient(circle at 84% 55%,rgba(227,35,255,.22),transparent 25%),linear-gradient(145deg,#050817,#050a1b 70%,#050716);border:1px solid rgba(71,52,188,.18)}
    .vsm-copy{position:relative;z-index:3;width:58%;padding-top:4px}.vsm-copy h1{margin:5px 0 6px;font-size:clamp(2.35rem,4.2vw,4.45rem);line-height:.88;letter-spacing:-.07em;font-weight:1000;font-style:italic;text-transform:uppercase;color:#fff}.vsm-copy h1 span{display:block;color:#ffc500;text-shadow:0 0 18px rgba(255,190,0,.12)}.vsm-copy p{margin:0;color:#d6dbed;font-size:.9rem}
    .vsm-hero-art{position:absolute;right:2%;top:2%;width:42%;height:96%;display:grid;place-items:center}
    .vsm-phone{position:relative;width:178px;height:92px;border-radius:24px;transform:perspective(400px) rotateX(58deg) rotateZ(-10deg);background:linear-gradient(145deg,#1735ff,#151047 58%,#6727f8);border:3px solid #0ee7ff;box-shadow:0 0 15px #00b7ff,0 0 34px #482eff,0 0 58px rgba(208,34,255,.5),inset 0 0 18px rgba(255,255,255,.25)}
    .vsm-phone:after{content:"";position:absolute;inset:12px;border-radius:16px;background:radial-gradient(circle at 50% 50%,rgba(225,48,255,.6),transparent 54%),#070821}
    .vsm-orbit{position:absolute;width:260px;height:118px;border:2px solid rgba(102,60,255,.65);border-radius:50%;transform:rotate(-8deg);box-shadow:0 0 20px rgba(81,65,255,.4)}
    .vsm-social{position:absolute;width:48px;height:48px;border-radius:12px;display:grid;place-items:center;color:#fff;border:1px solid rgba(255,255,255,.34);box-shadow:0 0 20px currentColor;z-index:4}.vsm-social svg{width:25px;height:25px}.vsm-social.ig{left:8%;top:24%;background:linear-gradient(145deg,#7725ff,#ff247e)}.vsm-social.yt{right:22%;top:28%;background:linear-gradient(145deg,#ff164b,#d90852)}.vsm-social.stat{right:3%;top:18%;background:linear-gradient(145deg,#0877ff,#6a22ff)}.vsm-social.like{left:46%;top:2%;background:linear-gradient(145deg,#8c21ff,#ff24bd)}.vsm-social.chat{left:48%;bottom:9%;background:linear-gradient(145deg,#006fff,#5f2cff)}
    .vsm-team{position:relative;overflow:hidden;padding:17px 18px;border-radius:15px;border:1px solid rgba(179,51,255,.46);background:radial-gradient(circle at 92% 32%,rgba(194,31,255,.22),transparent 24%),linear-gradient(150deg,#0d1029,#130a30);box-shadow:0 0 30px rgba(87,27,220,.1)}.vsm-team h3{margin:0 0 10px;color:#fff;font-size:.82rem}.vsm-team p{margin:0;color:#b8c0d6;font-size:.78rem;line-height:1.45;max-width:190px}.vsm-star{position:absolute;right:18px;top:54px;color:#e35cff;font-size:2.5rem;text-shadow:0 0 20px #792cff}.vsm-team-avatars{display:flex;margin-top:15px}.vsm-team-avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;margin-right:-5px;border:2px solid #10152b;background:linear-gradient(145deg,#172b5f,#9c31cf);font-size:.62rem;font-weight:950}
    .vsm-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.vsm-kpi{display:grid;grid-template-columns:50px 1fr;gap:10px;align-items:center;min-height:82px;padding:11px 13px;border-radius:12px;background:linear-gradient(180deg,#0b1229,#070d1d);border:1px solid rgba(74,73,165,.38);box-shadow:inset 0 0 18px rgba(52,52,135,.08)}.vsm-kpi-icon{width:43px;height:43px;border-radius:10px;display:grid;place-items:center;font-size:1.35rem;background:linear-gradient(145deg,#5512d9,#8d14ff);box-shadow:0 0 18px rgba(111,20,255,.35)}.vsm-kpi.blue .vsm-kpi-icon{background:linear-gradient(145deg,#0034df,#087dff)}.vsm-kpi.gold{border-color:rgba(224,158,18,.4)}.vsm-kpi.gold .vsm-kpi-icon{background:linear-gradient(145deg,#795000,#d99900)}.vsm-kpi small{display:block;color:#c7ccdd;font-size:.58rem;font-weight:900;text-transform:uppercase}.vsm-kpi strong{display:block;color:#fff;font-size:1.55rem;line-height:1;margin-top:4px}.vsm-kpi em{display:block;color:#929db6;font-size:.62rem;font-style:normal;margin-top:5px}.vsm-kpi em.good{color:#19ec8f}
    .vsm-grid-main{display:grid;grid-template-columns:minmax(0,1.23fr) minmax(0,1fr);gap:10px}.vsm-panel{border-radius:12px;border:1px solid rgba(77,86,167,.35);background:linear-gradient(180deg,#091126,#060c1b);padding:12px;box-shadow:0 12px 30px rgba(0,0,0,.18)}.vsm-panel-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px}.vsm-panel-title{display:flex;align-items:center;gap:8px;margin:0;color:#fff;font-size:.82rem;font-weight:950;text-transform:uppercase}.vsm-panel-title i{color:#bc2fff;font-style:normal}.vsm-link{display:inline-flex;align-items:center;padding:7px 11px;border:1px solid rgba(144,161,208,.35);border-radius:7px;color:#eef1ff;text-decoration:none;font-size:.62rem;font-weight:850}
    .vsm-weekline{display:flex;align-items:center;gap:18px;color:#d2d7e8;font-size:.72rem;margin:0 0 9px 4px}.vsm-weekline b{font-weight:750}.vsm-calendar{display:grid;grid-template-columns:42px repeat(7,minmax(76px,1fr));border-top:1px solid rgba(113,127,170,.18);border-left:1px solid rgba(113,127,170,.12);overflow-x:auto}.vsm-cal-cell{min-height:41px;padding:5px;border-right:1px solid rgba(113,127,170,.12);border-bottom:1px solid rgba(113,127,170,.12);font-size:.6rem;color:#aab3ca}.vsm-cal-day{min-height:auto;text-align:center;font-weight:900;color:#eef1ff;padding:7px 3px}.vsm-cal-time{display:flex;align-items:center;color:#a3adc4}.vsm-calendar-event{padding:5px 6px;border-radius:6px;background:linear-gradient(145deg,rgba(90,22,168,.9),rgba(63,20,112,.85));border:1px solid rgba(187,54,255,.45);color:#fff;font-size:.56rem;line-height:1.2}.vsm-calendar-event.blue{background:linear-gradient(145deg,rgba(0,66,168,.85),rgba(17,45,99,.9));border-color:rgba(31,116,255,.45)}.vsm-calendar-event.green{background:linear-gradient(145deg,rgba(0,92,73,.9),rgba(9,52,49,.9));border-color:rgba(17,197,146,.38)}.vsm-calendar-event.red{background:linear-gradient(145deg,rgba(142,13,54,.85),rgba(70,20,48,.9));border-color:rgba(255,40,96,.42)}.vsm-calendar-event b{display:block;font-size:.57rem}.vsm-calendar-event span{display:block;color:#d5dcf0;font-size:.51rem;margin-top:2px}.vsm-legend{display:flex;gap:16px;margin-top:9px;color:#a0abc0;font-size:.58rem}.vsm-dot{width:7px;height:7px;border-radius:50%;display:inline-block;margin-right:5px}.vsm-dot.purple{background:#a62cff}.vsm-dot.green{background:#19d89b}.vsm-dot.gray{background:#6d7792}
    .vsm-approval-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px}.vsm-approval{overflow:hidden;border-radius:10px;border:1px solid rgba(89,77,175,.4);background:#080d1c}.vsm-thumb{height:118px;padding:12px;display:flex;align-items:flex-end;position:relative;overflow:hidden;background-size:cover;background-position:center}.vsm-thumb.has-media:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(3,5,15,.04),rgba(3,5,15,.82))}.vsm-thumb.has-media:before{display:none}.vsm-thumb:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 80% 28%,rgba(219,46,255,.46),transparent 30%),linear-gradient(145deg,#160625,#160c45 58%,#0c0a28)}.vsm-thumb.blue:before{background:radial-gradient(circle at 76% 34%,rgba(0,171,255,.45),transparent 31%),linear-gradient(145deg,#07162c,#06103e)}.vsm-thumb.gold:before{background:radial-gradient(circle at 78% 38%,rgba(255,160,0,.28),transparent 34%),linear-gradient(145deg,#161006,#211407)}.vsm-thumb-text{position:relative;z-index:2;color:#fff;font-size:.86rem;font-weight:1000;font-style:italic;line-height:.96;text-transform:uppercase;max-width:90%}.vsm-thumb-text span{color:#ffc400}.vsm-platform{position:absolute;right:8px;bottom:8px;z-index:2;width:23px;height:23px;border-radius:7px;display:grid;place-items:center;background:#7c22ff;color:#fff;font-size:.6rem;font-weight:1000}.vsm-approval-body{padding:7px}.vsm-approval-meta{color:#b5bed1;font-size:.58rem;margin-bottom:7px}.vsm-approval-actions{display:grid;grid-template-columns:1fr 1fr;gap:6px}.vsm-approve,.vsm-request{min-height:31px;border-radius:6px;font-size:.61rem;font-weight:900;cursor:pointer}.vsm-approve{color:#baffdf;border:1px solid #0be897;background:linear-gradient(180deg,rgba(0,111,80,.48),rgba(0,65,49,.4));box-shadow:0 0 12px rgba(0,225,145,.15)}.vsm-request{color:#f0c5ff;border:1px solid #a42cff;background:linear-gradient(180deg,rgba(90,20,133,.4),rgba(57,12,86,.32))}
    .vsm-grid-bottom{display:grid;grid-template-columns:1.45fr .8fr .78fr;gap:10px}.vsm-performance-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px}.vsm-performance{min-height:130px;padding:10px;border-radius:9px;border:1px solid rgba(75,83,154,.38);background:linear-gradient(180deg,#0a132b,#071023);position:relative;overflow:hidden}.vsm-performance small{display:block;color:#aeb9d2;font-size:.57rem;font-weight:900}.vsm-performance strong{display:block;color:#fff;font-size:1.45rem;margin-top:9px}.vsm-performance em{display:block;color:#23e987;font-size:.55rem;font-style:normal;margin-top:5px}.vsm-spark{position:absolute;left:8px;right:8px;bottom:8px;height:43px}.vsm-spark svg{width:100%;height:100%;overflow:visible}.vsm-spark polyline{fill:none;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;filter:drop-shadow(0 0 4px currentColor)}.vsm-score{display:grid;place-items:center;margin:7px auto 0;width:66px;height:66px;border-radius:50%;background:radial-gradient(circle,#081126 55%,transparent 57%),conic-gradient(#0ee7ff,#9931ff,#ff42d0,#0ee7ff);box-shadow:0 0 17px rgba(114,53,255,.2)}.vsm-score b{font-size:1.1rem}.vsm-score span{font-size:.46rem;color:#b7bfd3}
    .vsm-request-list,.vsm-channel-list{display:grid;gap:7px}.vsm-request-row,.vsm-channel-row{display:grid;align-items:center;gap:7px;border:1px solid rgba(103,116,164,.18);border-radius:8px;background:#0a1122;padding:7px 8px}.vsm-request-row{grid-template-columns:28px 1fr auto}.vsm-channel-row{grid-template-columns:25px 1fr auto}.vsm-mini-icon{width:26px;height:26px;border-radius:7px;display:grid;place-items:center;background:linear-gradient(145deg,#8f20ff,#d11dff);font-size:.62rem;font-weight:1000}.vsm-row-copy{min-width:0}.vsm-row-copy b{display:block;color:#fff;font-size:.62rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.vsm-row-copy span{display:block;color:#929eb8;font-size:.54rem;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.vsm-status{padding:5px 7px;border-radius:5px;border:1px solid rgba(137,47,255,.5);color:#d8adff;background:rgba(97,27,155,.28);font-size:.5rem}.vsm-status.open{color:#53d6ff;border-color:rgba(0,141,255,.5);background:rgba(0,78,148,.25)}.vsm-status.done,.vsm-connected{color:#79ffc1;border-color:rgba(0,210,118,.5);background:rgba(0,96,58,.34)}.vsm-channel-platform{width:23px;height:23px;border-radius:6px;display:grid;place-items:center;font-size:.56rem;font-weight:1000;background:linear-gradient(145deg,#7d2aff,#f42686)}.vsm-connected{padding:4px 6px;border-radius:5px;font-size:.49rem}
    .vsm-footer{display:flex;align-items:center;justify-content:center;gap:18px;padding:8px 0 0;color:#7f89a6;font-size:.55rem}.vsm-footer i{color:#be30ff;font-style:normal}
    .vsm-mobile-nav{display:none}
    @media(max-width:1180px){.vsm-hero-row{grid-template-columns:1fr}.vsm-team{display:none}.vsm-grid-main{grid-template-columns:1fr}.vsm-grid-bottom{grid-template-columns:1fr 1fr}.vsm-grid-bottom .vsm-panel:first-child{grid-column:1/-1}.vsm-copy{width:62%}.vsm-performance-grid{grid-template-columns:repeat(4,1fr)}}
    @media(max-width:760px){.fi-main{padding:8px 0 96px!important}.vsm-dashboard{padding:0 12px 16px;gap:13px}.vsm-topline{min-height:42px}.vsm-greeting{font-size:.8rem}.vsm-bell,.vsm-profile-meta{display:none}.vsm-adjust{padding:9px 11px;font-size:.68rem}.vsm-hero{min-height:245px;padding:17px 16px}.vsm-copy{width:86%;padding-top:0}.vsm-copy h1{font-size:2.65rem;line-height:.9}.vsm-copy p{font-size:.8rem;max-width:75%}.vsm-hero-art{right:-18%;top:44%;width:70%;height:58%;opacity:.85}.vsm-phone{width:150px;height:75px}.vsm-orbit{width:205px;height:90px}.vsm-social{width:36px;height:36px;font-size:.9rem}.vsm-kpis{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.vsm-kpi{grid-template-columns:38px 1fr;min-height:78px;padding:10px}.vsm-kpi-icon{width:35px;height:35px;font-size:1rem}.vsm-kpi strong{font-size:1.25rem}.vsm-panel{padding:13px}.vsm-calendar{grid-template-columns:38px repeat(7,120px);overflow-x:auto;scroll-snap-type:x proximity}.vsm-approval-grid{grid-template-columns:1fr}.vsm-thumb{height:160px}.vsm-grid-bottom{grid-template-columns:1fr}.vsm-grid-bottom .vsm-panel:first-child{grid-column:auto}.vsm-performance-grid{grid-template-columns:repeat(2,1fr)}.vsm-footer{padding-bottom:8px}.vsm-mobile-nav{position:fixed;display:grid;grid-template-columns:repeat(5,1fr);left:10px;right:10px;bottom:10px;z-index:60;padding:7px;border-radius:18px;border:1px solid rgba(115,44,255,.48);background:rgba(4,8,20,.96);box-shadow:0 0 24px rgba(80,25,180,.2);backdrop-filter:blur(18px)}.vsm-mobile-nav a{display:grid;place-items:center;min-height:48px;color:#b8c2db;text-decoration:none;font-size:.62rem;font-weight:850;border-radius:12px}.vsm-mobile-nav a.active{color:#fff;background:linear-gradient(145deg,rgba(54,61,145,.55),rgba(68,23,115,.62));border:1px solid rgba(136,48,255,.55)}}
    @media(max-width:430px){.vsm-copy h1{font-size:2.25rem}.vsm-copy p{max-width:90%}.vsm-kpis{grid-template-columns:1fr 1fr}.vsm-kpi{grid-template-columns:1fr;gap:4px}.vsm-kpi-icon{display:none}.vsm-performance-grid{grid-template-columns:1fr 1fr}}

    /* Canonical client shell */
    .fi-sidebar,.fi-topbar{display:none!important}
    .fi-main,.fi-main-ctn{padding:0!important;margin:0!important;max-width:none!important;width:100%!important}
    .vsm-shell{min-height:100vh;display:grid;grid-template-columns:245px minmax(0,1fr);background:linear-gradient(180deg,#040712,#030610 66%,#02040b)}
    .vsm-side{position:sticky;top:0;height:100vh;padding:24px 16px 18px;border-right:1px solid rgba(100,76,255,.22);background:linear-gradient(180deg,#050917,#030712 72%,#05081a);display:flex;flex-direction:column}
    .vsm-brand{display:flex;align-items:center;gap:10px;margin:2px 6px 26px}.vsm-brand-mark{width:44px;height:44px;display:grid;place-items:center;filter:drop-shadow(0 0 12px rgba(120,57,255,.55))}.vsm-brand-mark svg{width:38px;height:38px}.vsm-brand-copy{line-height:.88}.vsm-brand-copy b{display:block;color:#fff;font-size:1.22rem;font-weight:1000;font-style:italic;letter-spacing:-.06em}.vsm-brand-copy span{display:block;margin-top:7px;color:#ffc400;font-size:.64rem;font-weight:1000;letter-spacing:.08em}
    .vsm-own-nav{display:grid;gap:5px}.vsm-own-nav a{min-height:42px;padding:0 12px;display:flex;align-items:center;gap:10px;border-radius:9px;color:#b7c0d7;text-decoration:none;font-size:.76rem;font-weight:760;border:1px solid transparent}.vsm-own-nav a.active{color:#fff;background:linear-gradient(90deg,rgba(90,27,199,.82),rgba(81,28,177,.28));border-color:rgba(166,54,255,.65);box-shadow:inset 3px 0 0 #2bdfff,0 0 18px rgba(115,40,255,.16)}.vsm-own-nav .vsm-badge{margin-left:auto}
    .vsm-side-support{margin-top:auto;position:relative;overflow:hidden;border-radius:16px;padding:18px 15px;border:1px solid rgba(145,54,255,.65);background:radial-gradient(circle at 80% 7%,rgba(119,40,255,.42),transparent 34%),linear-gradient(155deg,#130d3a,#071228)}.vsm-side-support:after{content:"";position:absolute;right:-14px;top:6px;width:68px;height:68px;border-radius:50%;background:radial-gradient(circle,#d94bff 0 5%,#6d2bff 28%,transparent 64%)}.vsm-side-support strong{display:block;color:#fff;font-size:.72rem}.vsm-side-support b{display:block;color:#fff;font-size:.95rem;margin-top:4px}.vsm-side-support p{margin:8px 0 13px;color:#aeb8d1;font-size:.64rem;line-height:1.35}.vsm-side-support a{display:inline-flex;align-items:center;min-height:31px;padding:0 12px;border-radius:7px;background:#ffc400;color:#171100;text-decoration:none;font-size:.61rem;font-weight:950}
    .vsm-shell>.vsm-dashboard{padding:18px 20px 15px;gap:10px}
    .vsm-shell .vsm-hero{min-height:185px;border-radius:14px}.vsm-shell .vsm-team{min-height:185px;border-radius:14px}.vsm-shell .vsm-kpis{gap:9px}.vsm-shell .vsm-panel{border-radius:10px;padding:11px}.vsm-shell .vsm-grid-main,.vsm-shell .vsm-grid-bottom{gap:9px}
    @media(max-width:1180px){.vsm-shell{grid-template-columns:210px minmax(0,1fr)}}
    @media(max-width:820px){.vsm-shell{display:block}.vsm-side{display:none}.vsm-shell>.vsm-dashboard{padding:10px 11px 88px}.vsm-mobile-nav{display:grid!important}}
</style>

@if(! $clientId)
    <div class="vsm-panel" style="padding:32px;text-align:center">Seu usuário precisa estar vinculado a um cliente para carregar o painel.</div>
@else
<div class="vsm-shell">
    <aside class="vsm-side">
        <div class="vsm-brand">
            <div class="vsm-brand-mark">
                <svg viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <path d="M29 3 12 25h10l-3 20 17-24H26l3-18Z" fill="url(#vsmBolt)"/>
                    <defs><linearGradient id="vsmBolt" x1="11" y1="5" x2="39" y2="42"><stop stop-color="#19E7FF"/><stop offset=".55" stop-color="#8C2CFF"/><stop offset="1" stop-color="#FF41D0"/></linearGradient></defs>
                </svg>
            </div>
            <div class="vsm-brand-copy"><b>VITRINE</b><span>SOCIAL MÍDIA</span></div>
        </div>
        <nav class="vsm-own-nav">
            <a class="active" href="{{ \App\Filament\Client\Pages\ClientDashboard::getUrl() }}">⌂ Painel</a>
            <a href="{{ \App\Filament\Client\Pages\Contents::getUrl() }}">▤ Conteúdos</a>
            <a href="{{ \App\Filament\Client\Pages\CalendarPage::getUrl() }}">▣ Calendário</a>
            <a href="{{ \App\Filament\Client\Pages\Approvals::getUrl() }}">✓ Aprovações @if($approvals > 0)<span class="vsm-badge">{{ $approvals }}</span>@endif</a>
            <a href="{{ \App\Filament\Client\Pages\Performance::getUrl() }}">▥ Desempenho</a>
            <a href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">☷ Solicitações</a>
            <a href="{{ \App\Filament\Client\Pages\Channels::getUrl() }}">⌯ Canais</a>
            <a href="{{ \App\Filament\Client\Pages\Files::getUrl() }}">▱ Arquivos</a>
            <a href="{{ \App\Filament\Client\Pages\Balance::getUrl() }}">◉ Consumo e Saldo</a>
            <a href="{{ \App\Filament\Client\Pages\Affiliates::getUrl() }}">◇ Programa de Afiliados</a>
            <a href="{{ \App\Filament\Client\Pages\Account::getUrl() }}">● Conta</a>
        </nav>
        <div class="vsm-side-support">
            <strong>DÚVIDAS?</strong><b>Fale com a gente!</b>
            <p>Resposta em até<br>1h útil</p>
            <a href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">Abrir chat →</a>
        </div>
    </aside>
    <div class="vsm-dashboard">
    <div class="vsm-topline">
        <div class="vsm-greeting">Olá, {{ $userName }}! 👋</div>
        <div class="vsm-actions">
            <a class="vsm-adjust" href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">✎ Solicitar ajuste</a>
            <div class="vsm-bell" aria-label="Notificações"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg></div>
            <div class="vsm-profile">
                <div class="vsm-avatar" aria-hidden="true"></div>
                <div class="vsm-profile-meta"><b>{{ $userName }}</b><span>Cliente</span></div>
            </div>
        </div>
    </div>

    <div class="vsm-hero-row">
        <section class="vsm-hero">
            <div class="vsm-copy">
                <h1>Sua presença digital <span>em um só lugar</span></h1>
                <p>Acompanhe entregas, aprove conteúdos e veja resultados.</p>
            </div>
            <div class="vsm-hero-art" aria-hidden="true">
                <div class="vsm-orbit"></div>
                <div class="vsm-phone"></div>
                <div class="vsm-social ig"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></div>
                <div class="vsm-social yt"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 7.3a2.8 2.8 0 0 0-2-2C17.2 4.8 12 4.8 12 4.8s-5.2 0-7 .5a2.8 2.8 0 0 0-2 2A29 29 0 0 0 2.5 12 29 29 0 0 0 3 16.7a2.8 2.8 0 0 0 2 2c1.8.5 7 .5 7 .5s5.2 0 7-.5a2.8 2.8 0 0 0 2-2 29 29 0 0 0 .5-4.7 29 29 0 0 0-.5-4.7ZM10 15.5v-7l6 3.5-6 3.5Z"/></svg></div>
                <div class="vsm-social stat"><svg viewBox="0 0 24 24" fill="currentColor"><rect x="4" y="12" width="3" height="8" rx="1"/><rect x="10.5" y="8" width="3" height="12" rx="1"/><rect x="17" y="4" width="3" height="16" rx="1"/></svg></div>
                <div class="vsm-social like"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7-4.6-9.2-8.7C.8 8.5 3 5 6.5 5c2 0 3.4 1.1 4.3 2.3C11.7 6.1 13.1 5 15.1 5c3.5 0 5.7 3.5 3.7 7.3C19 16.4 12 21 12 21Z"/></svg></div>
                <div class="vsm-social chat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 5h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 3v-3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/></svg></div>
            </div>
        </section>
        <aside class="vsm-team">
            <h3>Seu time por trás dos resultados</h3>
            <p>Nosso time está trabalhando todos os dias para fazer a sua marca crescer.</p>
            <div class="vsm-star">✦</div>
            <div class="vsm-team-avatars"><div class="vsm-team-avatar">AN</div><div class="vsm-team-avatar">CR</div><div class="vsm-team-avatar">MM</div><div class="vsm-team-avatar">+1</div></div>
        </aside>
    </div>

    <div class="vsm-kpis">
        <div class="vsm-kpi"><div class="vsm-kpi-icon">▣</div><div><small>Conteúdos pendentes</small><strong>{{ $pending }}</strong><em>Aguardando produção</em></div></div>
        <div class="vsm-kpi blue"><div class="vsm-kpi-icon">✓</div><div><small>Aprovações</small><strong>{{ $approvals }}</strong><em>Aguardando sua aprovação</em></div></div>
        <div class="vsm-kpi blue"><div class="vsm-kpi-icon">▦</div><div><small>Posts agendados</small><strong>{{ $scheduledNext7 }}</strong><em>Próximos 7 dias</em></div></div>
        <div class="vsm-kpi gold"><div class="vsm-kpi-icon">▥</div><div><small>Alcance do mês</small><strong>{{ $reachValue ?? '—' }}</strong><em class="{{ $reachValue ? 'good' : '' }}">{{ $reachValue ? 'Dados sincronizados' : 'Aguardando integração de analytics' }}</em></div></div>
    </div>

    <div class="vsm-grid-main">
        <section class="vsm-panel">
            <div class="vsm-panel-head">
                <h3 class="vsm-panel-title"><i>▣</i> Calendário Editorial</h3>
                <a class="vsm-link" href="{{ \App\Filament\Client\Pages\CalendarPage::getUrl() }}">Ver calendário completo ›</a>
            </div>
            <div class="vsm-weekline"><span>‹</span><b>{{ $weekStart->format('d') }} – {{ $weekEnd->format('d \d\e M \d\e Y') }}</b><span>›</span></div>
            <div class="vsm-calendar">
                <div class="vsm-cal-cell vsm-cal-day"></div>
                @foreach($calendarDays as $day)<div class="vsm-cal-cell vsm-cal-day">{{ strtoupper($day['date']->locale('pt_BR')->isoFormat('ddd D')) }}</div>@endforeach
                @foreach(['09:00','12:00','18:00'] as $time)
                    <div class="vsm-cal-cell vsm-cal-time">{{ $time }}</div>
                    @foreach($calendarDays as $day)
                        <div class="vsm-cal-cell">
                            @foreach($day['items']->take(2) as $item)
                                @php $tone = $item->published_at ? 'green' : (str_contains(strtolower((string)$item->channel),'youtube') ? 'red' : (str_contains(strtolower((string)$item->channel),'facebook') || str_contains(strtolower((string)$item->channel),'linkedin') ? 'blue' : '')); @endphp
                                <div class="vsm-calendar-event {{ $tone }}"><b>{{ $item->title }}</b><span>{{ $item->channel ? ucfirst($item->channel) : ($item->format ?: 'Conteúdo') }}</span></div>
                            @endforeach
                        </div>
                    @endforeach
                @endforeach
            </div>
            <div class="vsm-legend"><span><i class="vsm-dot purple"></i>Agendado</span><span><i class="vsm-dot green"></i>Publicado</span><span><i class="vsm-dot gray"></i>Rascunho</span></div>
        </section>

        <section class="vsm-panel">
            <div class="vsm-panel-head"><h3 class="vsm-panel-title"><i>♙</i> Aprovações</h3><a class="vsm-link" href="{{ \App\Filament\Client\Pages\Approvals::getUrl() }}">Ver todas ›</a></div>
            <div class="vsm-approval-grid">
                @forelse($approvalItems as $index => $item)
                    @php
                        $slideImage = $item->slides->first()?->image_path;
                        $slideUrl = $slideImage ? asset('storage/'.ltrim($slideImage, '/')) : null;
                    @endphp
                    <article class="vsm-approval">
                        <div class="vsm-thumb {{ $slideUrl ? 'has-media' : ($index === 1 ? 'blue' : ($index === 2 ? 'gold' : '')) }}" @if($slideUrl) style="background-image:url('{{ $slideUrl }}')" @endif>
                            <div class="vsm-thumb-text">{{ $item->title }}</div>
                            <div class="vsm-platform">{{ strtoupper(mb_substr($item->channel ?: 'C',0,1)) }}</div>
                        </div>
                        <div class="vsm-approval-body">
                            <div class="vsm-approval-meta">{{ $item->channel ? ucfirst($item->channel) : 'Canal' }} • {{ $item->format ?: ($item->content_type ?: 'Conteúdo') }}</div>
                            <div class="vsm-approval-actions">
                                <button type="button" wire:click="approveContent({{ $item->id }})" wire:loading.attr="disabled" class="vsm-approve">✓ Aprovar</button>
                                <button type="button" wire:click="requestAdjustment({{ $item->id }})" wire:loading.attr="disabled" class="vsm-request">Pedir ajuste</button>
                            </div>
                        </div>
                    </article>
                @empty
                    <article class="vsm-approval"><div class="vsm-thumb"><div class="vsm-thumb-text">Tudo em dia</div></div><div class="vsm-approval-body"><div class="vsm-approval-meta">Nenhum conteúdo aguardando aprovação.</div></div></article>
                @endforelse
            </div>
        </section>
    </div>

    <div class="vsm-grid-bottom">
        <section class="vsm-panel">
            <div class="vsm-panel-head"><h3 class="vsm-panel-title"><i>▥</i> Desempenho do mês</h3></div>
            <div class="vsm-performance-grid">
                <div class="vsm-performance"><small>ENGAJAMENTO</small><strong>{{ $engagementValue ?? '—' }}</strong><em>{{ $engagementValue ? 'Dados sincronizados' : 'Analytics pendente' }}</em><div class="vsm-spark" style="color:#b72cff"><svg viewBox="0 0 100 36" preserveAspectRatio="none"><polyline points="2,29 13,20 24,25 35,13 47,17 59,8 71,12 84,5 98,9"/></svg></div></div>
                <div class="vsm-performance"><small>ALCANCE</small><strong>{{ $reachValue ?? '—' }}</strong><em>{{ $reachValue ? 'Dados sincronizados' : 'Analytics pendente' }}</em><div class="vsm-spark" style="color:#16b8ff"><svg viewBox="0 0 100 36" preserveAspectRatio="none"><polyline points="2,31 12,21 23,24 34,12 45,18 57,9 69,14 81,6 98,3"/></svg></div></div>
                <div class="vsm-performance"><small>CRESCIMENTO</small><strong>{{ $growthValue ?? '—' }}</strong><em>{{ $growthValue ? 'Dados sincronizados' : 'Analytics pendente' }}</em><div class="vsm-spark" style="color:#ff35c9"><svg viewBox="0 0 100 36" preserveAspectRatio="none"><polyline points="2,28 14,24 25,18 37,22 49,10 61,15 72,7 84,11 98,4"/></svg></div></div>
                <div class="vsm-performance"><small>PERFORMANCE</small><div class="vsm-score"><div><b>{{ $scorePercent }}</b><br><span>/100</span></div></div><em>Score médio {{ $scoreAverage }}</em></div>
            </div>
        </section>

        <section class="vsm-panel">
            <div class="vsm-panel-head"><h3 class="vsm-panel-title"><i>☷</i> Solicitações</h3><a class="vsm-link" href="{{ \App\Filament\Client\Pages\Requests::getUrl() }}">Ver todas ›</a></div>
            <div class="vsm-request-list">
                @forelse($requests as $item)
                    @php $statusClass = in_array($item->status,['ready','published','completed']) ? 'done' : (in_array($item->status,['editing','adjustment_requested']) ? '' : 'open'); @endphp
                    <div class="vsm-request-row"><div class="vsm-mini-icon">✎</div><div class="vsm-row-copy"><b>{{ $item->title }}</b><span>{{ $item->objective ?: ($item->channel ?: 'Solicitação de conteúdo') }}</span></div><span class="vsm-status {{ $statusClass }}">{{ $item->status ?: 'Aberta' }}</span></div>
                @empty
                    <div class="vsm-request-row"><div class="vsm-mini-icon">✓</div><div class="vsm-row-copy"><b>Nenhuma solicitação em andamento</b><span>Seu fluxo está em dia.</span></div><span class="vsm-status done">Em dia</span></div>
                @endforelse
            </div>
        </section>

        <section class="vsm-panel">
            <div class="vsm-panel-head"><h3 class="vsm-panel-title"><i>♧</i> Canais conectados</h3></div>
            <div class="vsm-channel-list">
                @forelse($channels as $channel)
                    <div class="vsm-channel-row"><div class="vsm-channel-platform">{{ strtoupper(mb_substr($channel->channel,0,1)) }}</div><div class="vsm-row-copy"><b>{{ ucfirst($channel->channel) }}</b><span>{{ $channel->total }} conteúdo(s) vinculados</span></div><span class="vsm-connected">Ativo</span></div>
                @empty
                    <div class="vsm-channel-row"><div class="vsm-channel-platform">•</div><div class="vsm-row-copy"><b>Nenhum canal com atividade</b><span>Conecte seus canais para começar.</span></div><span class="vsm-status">Pendente</span></div>
                @endforelse
            </div>
        </section>
    </div>

    <footer class="vsm-footer"><span>Vitrine Social Mídia © {{ now()->year }}</span><span>•</span><span>Todos os direitos reservados</span><i>ϟ</i><span>Transparência</span><span>•</span><span>Estratégia</span><span>•</span><span>Resultados</span></footer>
    </div>
</div>

<nav class="vsm-mobile-nav" aria-label="Navegação rápida">
    <a class="active" href="{{ \App\Filament\Client\Pages\ClientDashboard::getUrl() }}">Painel</a>
    <a href="{{ \App\Filament\Client\Pages\Contents::getUrl() }}">Conteúdos</a>
    <a href="{{ \App\Filament\Client\Pages\CalendarPage::getUrl() }}">Calendário</a>
    <a href="{{ \App\Filament\Client\Pages\Approvals::getUrl() }}">Aprovações</a>
    <a href="{{ \App\Filament\Client\Pages\Performance::getUrl() }}">Desempenho</a>
</nav>
@endif
</x-filament-widgets::widget>

