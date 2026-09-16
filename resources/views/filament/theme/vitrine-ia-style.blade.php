<style>
    :root {
        --vitrine-cyan:#25d9ff;--vitrine-blue:#4b7cff;--vitrine-purple:#8b5cf6;--vitrine-magenta:#ff4fd8;--vitrine-gold:#ffc928;
        --vitrine-bg:#070b17;--vitrine-panel:#0d1324;--vitrine-line:rgba(255,255,255,.09);--vitrine-text:#f7f9ff;--vitrine-muted:#a8b3cf;
    }
    html,body{background:var(--vitrine-bg)!important}
    body{font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:var(--vitrine-text);background:radial-gradient(circle at 12% 8%,rgba(37,217,255,.08),transparent 26%),radial-gradient(circle at 90% 10%,rgba(255,79,216,.07),transparent 24%),var(--vitrine-bg)!important}
    .fi-sidebar,.fi-topbar{background:rgba(7,11,23,.94)!important;border-color:var(--vitrine-line)!important;backdrop-filter:blur(18px)}
    .fi-sidebar-header{border-bottom:1px solid var(--vitrine-line)}
    .fi-sidebar-header a,.fi-logo{color:var(--vitrine-text)!important;font-weight:900!important;letter-spacing:-.04em}
    .fi-sidebar-item a,.fi-sidebar-item-label,.fi-sidebar-group-label{color:#c7d0e4!important}
    .fi-sidebar-item-active a,.fi-sidebar-item a:hover{color:#fff!important;border-radius:14px!important;background:linear-gradient(135deg,rgba(37,217,255,.13),rgba(139,92,246,.16))!important;box-shadow:inset 0 0 0 1px rgba(37,217,255,.08)}
    .fi-btn-color-primary{color:#fff!important;border:0!important;background:linear-gradient(135deg,var(--vitrine-cyan),var(--vitrine-blue) 48%,var(--vitrine-purple))!important;box-shadow:0 14px 34px rgba(75,124,255,.22)!important}
    .fi-section,.fi-wi-stats-overview-stat,.fi-ta-ctn{color:var(--vitrine-text)!important;border:1px solid var(--vitrine-line)!important;border-radius:22px!important;background:linear-gradient(180deg,rgba(17,26,49,.94),rgba(11,18,36,.96))!important;box-shadow:0 20px 70px rgba(0,0,0,.24)!important}
    .fi-header-heading,.fi-section-header-heading,.fi-wi-stats-overview-stat-value{color:var(--vitrine-text)!important;letter-spacing:-.035em}.fi-header-subheading,.fi-section-header-description,.fi-wi-stats-overview-stat-description{color:var(--vitrine-muted)!important}

    /* Login Enterprise, derivado do padrão homologado da Factory. */
    .fi-simple-layout{padding:0!important;min-height:100vh;background:#020817!important}
    .fi-simple-main{padding:0!important;margin:0!important;max-width:none!important;width:100%!important;background:transparent!important;border:0!important;box-shadow:none!important}
    .fi-simple-header{display:none!important}.fi-simple-page{max-width:none!important;width:100%!important}
    .vsm-login-shell{min-height:100vh;display:grid;grid-template-columns:minmax(420px,1.06fr) minmax(420px,.94fr);background:radial-gradient(circle at 12% 12%,rgba(73,0,255,.42),transparent 28%),radial-gradient(circle at 82% 20%,rgba(255,20,210,.24),transparent 25%),linear-gradient(135deg,#020817 0%,#090a28 50%,#13053a 100%)}
    .vsm-login-brand{position:relative;padding:58px 64px 42px;display:flex;flex-direction:column;justify-content:space-between;overflow:hidden;border-right:1px solid rgba(115,92,255,.18)}
    .vsm-login-brand:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(2,8,24,.92),rgba(8,8,34,.62));pointer-events:none}.vsm-login-brand>*{position:relative}
    .vsm-brand-kicker{font-size:15px;font-weight:900;letter-spacing:.18em;color:#c8bcff;margin-bottom:22px}.vsm-login-brand h1{margin:0;font-size:clamp(3rem,5vw,5.6rem);line-height:.92;letter-spacing:-.065em;font-weight:950;text-transform:uppercase}.vsm-login-brand h1 span{display:block;color:var(--vitrine-gold);text-shadow:0 0 34px rgba(255,201,40,.16)}
    .vsm-login-brand p{max-width:620px;color:#d9dcf4;font-size:1.15rem;line-height:1.55;margin:22px 0 0}
    .vsm-login-orbit{height:270px;position:relative;margin:30px 0 22px;display:grid;place-items:center}.vsm-orbit-core{width:155px;height:155px;border-radius:38px;display:grid;place-items:center;font-size:78px;font-weight:950;background:linear-gradient(145deg,#0b1744,#18082f);color:#fff;border:1px solid rgba(72,182,255,.35);box-shadow:0 0 85px rgba(80,61,255,.38),0 0 50px rgba(255,54,213,.18)}
    .vsm-orbit-item{position:absolute;width:112px;height:72px;border-radius:18px;display:grid;place-items:center;text-align:center;padding:8px;background:linear-gradient(145deg,rgba(40,18,93,.92),rgba(9,23,58,.95));border:1px solid rgba(128,102,255,.3);font-size:.78rem;font-weight:850;color:#eef1ff;box-shadow:0 0 30px rgba(76,70,255,.22)}.vsm-orbit-item.i1{top:0;left:50%;transform:translateX(-50%)}.vsm-orbit-item.i2{right:5%;top:38%}.vsm-orbit-item.i3{right:24%;bottom:0}.vsm-orbit-item.i4{left:24%;bottom:0}.vsm-orbit-item.i5{left:5%;top:38%}
    .vsm-login-help{display:grid;gap:6px;padding:20px 22px;border-radius:18px;background:rgba(8,13,38,.72);border:1px solid rgba(126,104,255,.22);max-width:650px}.vsm-login-help strong{font-size:1.05rem}.vsm-login-help span{color:#b9c0dc;line-height:1.5}
    .vsm-login-access{display:grid;place-items:center;padding:52px}.vsm-login-card{width:min(620px,100%);padding:48px;border-radius:24px;background:rgba(5,8,25,.78);border:1px solid rgba(116,96,255,.28);box-shadow:0 32px 110px rgba(0,0,0,.45);backdrop-filter:blur(20px)}
    .vsm-login-badge{display:inline-flex;padding:7px 12px;border-radius:999px;background:rgba(91,73,255,.14);border:1px solid rgba(116,96,255,.32);color:#d8d0ff;font-size:.75rem;font-weight:900;letter-spacing:.08em;text-transform:uppercase}.vsm-login-card h2{font-size:2.25rem;letter-spacing:-.04em;margin:18px 0 8px;color:#fff}.vsm-login-card>p{color:#b9c0dc;margin:0 0 28px;line-height:1.55}.vsm-login-form .fi-fo-field-wrp-label,.vsm-login-form label{color:#e6e8f5!important}.vsm-login-form input{background:rgba(8,12,30,.9)!important;border-color:rgba(150,160,205,.25)!important;color:#fff!important}.vsm-login-form input:focus{border-color:#775cff!important;box-shadow:0 0 0 3px rgba(119,92,255,.15)!important}

    @media(max-width:980px){.vsm-login-shell{grid-template-columns:1fr}.vsm-login-brand{padding:34px 24px 20px}.vsm-login-orbit{display:none}.vsm-login-access{padding:24px 18px 48px}.vsm-login-card{padding:32px 24px}.vsm-login-brand h1{font-size:3rem}}
</style>
