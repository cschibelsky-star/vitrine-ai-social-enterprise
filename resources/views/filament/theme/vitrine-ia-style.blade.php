<style>
    :root {
        --vitrine-cyan: #06b6d4;
        --vitrine-blue: #0f172a;
        --vitrine-purple: #7c3aed;
        --vitrine-bg: #f8fafc;
    }

    body {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background: var(--vitrine-bg);
    }

    .fi-sidebar,
    .fi-topbar {
        backdrop-filter: blur(18px);
    }

    .fi-sidebar-header {
        border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .fi-sidebar-header a,
    .fi-logo {
        font-weight: 800 !important;
        letter-spacing: -.04em;
        color: var(--vitrine-blue) !important;
    }

    .fi-sidebar-item-active a,
    .fi-sidebar-item a:hover {
        background: linear-gradient(135deg, rgba(6, 182, 212, .16), rgba(124, 58, 237, .12)) !important;
        color: #0f172a !important;
        border-radius: 14px !important;
    }

    .fi-btn-color-primary {
        background: linear-gradient(135deg, var(--vitrine-cyan), var(--vitrine-purple)) !important;
        box-shadow: 0 14px 30px rgba(6, 182, 212, .22);
        border: none !important;
    }

    .fi-section,
    .fi-wi-stats-overview-stat,
    .fi-ta-ctn,
    .fi-simple-main {
        border-radius: 22px !important;
        box-shadow: 0 18px 60px rgba(15, 23, 42, .08) !important;
        border: 1px solid rgba(15, 23, 42, .06) !important;
    }

    .fi-wi-stats-overview-stat {
        background: linear-gradient(180deg, #ffffff, #f8fafc) !important;
    }

    .fi-header-heading {
        letter-spacing: -.04em;
        color: var(--vitrine-blue);
    }

    .fi-simple-layout {
        min-height: 100vh;
        background:
            radial-gradient(circle at 18% 20%, rgba(6, 182, 212, .35), transparent 30%),
            radial-gradient(circle at 70% 70%, rgba(124, 58, 237, .32), transparent 28%),
            linear-gradient(135deg, #0f172a 0%, #0b3b75 48%, #0ea5e9 100%) !important;
        position: relative;
        overflow: hidden;
    }

    .fi-simple-layout::before {
        content: "Vitrine IA STUDIO PRO";
        position: fixed;
        left: 7vw;
        top: 20vh;
        max-width: 460px;
        color: #fff;
        font-size: clamp(2.2rem, 4vw, 4.6rem);
        font-weight: 900;
        line-height: .95;
        letter-spacing: -.07em;
        text-shadow: 0 20px 60px rgba(0, 0, 0, .22);
        pointer-events: none;
    }

    .fi-simple-layout::after {
        content: "Crie posts, carrosseis, reels, stories e campanhas com inteligencia artificial em um unico ambiente.";
        position: fixed;
        left: 7vw;
        top: calc(20vh + 170px);
        max-width: 440px;
        color: rgba(255, 255, 255, .82);
        font-size: 1.1rem;
        line-height: 1.55;
        pointer-events: none;
    }

    .fi-simple-main {
        background: rgba(255, 255, 255, .9) !important;
        backdrop-filter: blur(18px);
        margin-left: auto !important;
        margin-right: 8vw !important;
    }

    .fi-simple-main .fi-logo,
    .fi-simple-main h1 {
        color: var(--vitrine-blue) !important;
    }

    @media (max-width: 900px) {
        .fi-simple-layout::before,
        .fi-simple-layout::after {
            display: none;
        }

        .fi-simple-main {
            margin: auto !important;
        }
    }
</style>
