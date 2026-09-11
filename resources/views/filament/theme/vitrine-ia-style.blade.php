<style>
    :root {
        --vitrine-cyan: #25d9ff;
        --vitrine-blue: #4b7cff;
        --vitrine-purple: #8b5cf6;
        --vitrine-magenta: #ff4fd8;
        --vitrine-gold: #ffc928;
        --vitrine-bg: #070b17;
        --vitrine-panel: #0d1324;
        --vitrine-line: rgba(255,255,255,.09);
        --vitrine-text: #f7f9ff;
        --vitrine-muted: #a8b3cf;
    }

    html, body { background: var(--vitrine-bg) !important; }

    body {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: var(--vitrine-text);
        background:
            radial-gradient(circle at 12% 8%, rgba(37,217,255,.08), transparent 26%),
            radial-gradient(circle at 90% 10%, rgba(255,79,216,.07), transparent 24%),
            var(--vitrine-bg) !important;
    }

    .fi-sidebar, .fi-topbar {
        background: rgba(7,11,23,.9) !important;
        border-color: var(--vitrine-line) !important;
        backdrop-filter: blur(18px);
    }

    .fi-sidebar-header { border-bottom: 1px solid var(--vitrine-line); }

    .fi-sidebar-header a, .fi-logo {
        color: var(--vitrine-text) !important;
        font-weight: 900 !important;
        letter-spacing: -.04em;
    }

    .fi-sidebar-item a, .fi-sidebar-item-label, .fi-sidebar-group-label { color: #c7d0e4 !important; }

    .fi-sidebar-item-active a, .fi-sidebar-item a:hover {
        color: #fff !important;
        border-radius: 14px !important;
        background: linear-gradient(135deg, rgba(37,217,255,.13), rgba(139,92,246,.16)) !important;
        box-shadow: inset 0 0 0 1px rgba(37,217,255,.08);
    }

    .fi-btn-color-primary {
        color: #fff !important;
        border: 0 !important;
        background: linear-gradient(135deg, var(--vitrine-cyan), var(--vitrine-blue) 48%, var(--vitrine-purple)) !important;
        box-shadow: 0 14px 34px rgba(75,124,255,.22) !important;
    }

    .fi-section, .fi-wi-stats-overview-stat, .fi-ta-ctn, .fi-simple-main {
        color: var(--vitrine-text) !important;
        border: 1px solid var(--vitrine-line) !important;
        border-radius: 22px !important;
        background: linear-gradient(180deg, rgba(17,26,49,.94), rgba(11,18,36,.96)) !important;
        box-shadow: 0 20px 70px rgba(0,0,0,.24) !important;
    }

    .fi-header-heading, .fi-section-header-heading, .fi-wi-stats-overview-stat-value, .fi-simple-header-heading {
        color: var(--vitrine-text) !important;
        letter-spacing: -.035em;
    }

    .fi-header-subheading, .fi-section-header-description, .fi-wi-stats-overview-stat-description {
        color: var(--vitrine-muted) !important;
    }

    .fi-main { position: relative; }

    body:not(.fi-body-has-top-navigation) .fi-main::before {
        content: "SUA PRESENÇA DIGITAL EM UM SÓ LUGAR";
        display: block;
        margin-bottom: 10px;
        padding: 26px 92px 26px 26px;
        min-height: 118px;
        border-radius: 24px;
        border: 1px solid rgba(37,217,255,.16);
        color: #fff;
        font-size: clamp(1.55rem,3vw,2.8rem);
        font-weight: 900;
        line-height: 1;
        letter-spacing: -.055em;
        background:
            radial-gradient(circle at 88% 20%, rgba(255,79,216,.16), transparent 28%),
            linear-gradient(135deg, rgba(37,217,255,.12), rgba(75,124,255,.11) 48%, rgba(139,92,246,.14));
        box-shadow: 0 20px 70px rgba(0,0,0,.22);
    }

    .fi-main::after {
        content: "Acompanhe entregas, aprove conteúdos e veja resultados.  •  VIA integrada";
        display: block;
        margin-top: -64px;
        margin-bottom: 44px;
        padding-left: 27px;
        color: var(--vitrine-muted);
        font-size: .92rem;
        pointer-events: none;
    }

    .fi-simple-layout {
        min-height: 100vh;
        background:
            radial-gradient(circle at 16% 18%, rgba(37,217,255,.24), transparent 26%),
            radial-gradient(circle at 78% 76%, rgba(139,92,246,.22), transparent 30%),
            radial-gradient(circle at 90% 14%, rgba(255,79,216,.12), transparent 22%),
            linear-gradient(135deg, #070b17 0%, #0b1832 48%, #10112b 100%) !important;
        position: relative;
        overflow: hidden;
    }

    .fi-simple-layout::before {
        content: "Vitrine Social Mídia";
        position: fixed;
        left: 7vw;
        top: 22vh;
        max-width: 520px;
        color: #fff;
        font-size: clamp(2.4rem,4vw,4.7rem);
        font-weight: 900;
        line-height: .96;
        letter-spacing: -.065em;
        text-shadow: 0 22px 60px rgba(0,0,0,.28);
        pointer-events: none;
    }

    .fi-simple-layout::after {
        content: "Sua empresa visível, relevante e lembrada.";
        position: fixed;
        left: 7vw;
        top: calc(22vh + 150px);
        max-width: 460px;
        color: rgba(255,255,255,.76);
        font-size: 1.12rem;
        line-height: 1.6;
        pointer-events: none;
    }

    .fi-simple-main {
        margin-left: auto !important;
        margin-right: 8vw !important;
        background: rgba(13,19,36,.9) !important;
        backdrop-filter: blur(20px);
    }

    @media (max-width: 900px) {
        .fi-simple-layout::before, .fi-simple-layout::after { display: none; }
        .fi-simple-main { margin: auto !important; }
        .fi-main::before { padding-right: 24px; }
    }
</style>
