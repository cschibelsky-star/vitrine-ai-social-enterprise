<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#070b17">
    <meta name="description" content="Vitrine Social Mídia: presença digital organizada, conteúdo consistente e acompanhamento em um só lugar.">
    <title>Vitrine Social Mídia</title>
    <style>
        :root{
            --bg:#070b17;--surface:#0d1324;--surface-2:#111a31;--line:rgba(255,255,255,.09);
            --text:#f7f9ff;--muted:#a8b3cf;--cyan:#25d9ff;--blue:#4b7cff;--violet:#8b5cf6;
            --magenta:#ff4fd8;--gold:#ffc928;--radius:24px;--shadow:0 24px 80px rgba(0,0,0,.38)
        }
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:
            radial-gradient(circle at 12% 8%,rgba(37,217,255,.14),transparent 28%),
            radial-gradient(circle at 88% 12%,rgba(255,79,216,.12),transparent 24%),
            linear-gradient(180deg,#070b17 0%,#090f1e 46%,#070b17 100%);color:var(--text);
            font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;line-height:1.5}
        a{color:inherit;text-decoration:none}.wrap{width:min(1160px,calc(100% - 40px));margin:auto}
        .nav{position:sticky;top:0;z-index:20;background:rgba(7,11,23,.72);backdrop-filter:blur(18px);border-bottom:1px solid var(--line)}
        .nav-inner{height:76px;display:flex;align-items:center;justify-content:space-between;gap:24px}.brand{font-weight:900;letter-spacing:-.04em;font-size:1.18rem}.brand span{background:linear-gradient(90deg,var(--cyan),var(--violet),var(--magenta));-webkit-background-clip:text;color:transparent}
        .nav-links{display:flex;gap:24px;color:var(--muted);font-size:.92rem}.nav-actions{display:flex;gap:10px}
        .btn{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 18px;border-radius:14px;border:1px solid var(--line);font-weight:800;transition:.2s ease}.btn:hover{transform:translateY(-1px);border-color:rgba(37,217,255,.42)}
        .btn-primary{border:0;background:linear-gradient(135deg,var(--cyan),var(--blue) 45%,var(--violet));box-shadow:0 14px 36px rgba(75,124,255,.26);color:white}.btn-gold{border:0;background:linear-gradient(135deg,#ffdf5a,var(--gold));color:#171102;box-shadow:0 14px 38px rgba(255,201,40,.2)}
        .hero{padding:94px 0 70px;overflow:hidden}.hero-grid{display:grid;grid-template-columns:1.03fr .97fr;align-items:center;gap:56px}
        .eyebrow{display:inline-flex;gap:8px;align-items:center;padding:8px 12px;border-radius:999px;background:rgba(37,217,255,.08);border:1px solid rgba(37,217,255,.22);color:#b7f4ff;font-weight:800;font-size:.78rem;letter-spacing:.08em;text-transform:uppercase}
        h1{font-size:clamp(3rem,6vw,5.5rem);line-height:.94;letter-spacing:-.065em;margin:22px 0}.gradient{background:linear-gradient(92deg,var(--cyan),#8cb0ff 40%,var(--violet) 70%,var(--magenta));-webkit-background-clip:text;color:transparent}
        .lead{font-size:1.15rem;color:var(--muted);max-width:640px}.hero-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px}.trust{display:flex;gap:20px;flex-wrap:wrap;margin-top:26px;color:#c4cce0;font-size:.9rem}.trust b{color:white}
        .dashboard{position:relative;padding:18px;border-radius:32px;background:linear-gradient(155deg,rgba(18,28,53,.95),rgba(8,13,27,.96));border:1px solid rgba(255,255,255,.11);box-shadow:var(--shadow)}
        .dashboard:before{content:"";position:absolute;inset:-2px;border-radius:34px;background:linear-gradient(135deg,rgba(37,217,255,.28),transparent 38%,rgba(255,79,216,.18));z-index:-1;filter:blur(18px)}
        .dash-top{display:flex;justify-content:space-between;align-items:center;padding:8px 4px 18px}.dots{display:flex;gap:6px}.dots i{width:8px;height:8px;border-radius:50%;background:#35415d}.online{font-size:.78rem;color:#8fffc5}
        .dash-hero{padding:20px;border-radius:20px;background:linear-gradient(135deg,rgba(37,217,255,.1),rgba(139,92,246,.13));border:1px solid rgba(37,217,255,.16)}.dash-hero strong{display:block;font-size:1.3rem;line-height:1.15}.dash-hero span{display:block;color:var(--muted);margin-top:8px;font-size:.88rem}
        .mini-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:10px}.mini{padding:16px;border-radius:17px;background:#0b1224;border:1px solid var(--line)}.mini small{color:var(--muted)}.mini b{display:block;font-size:1.55rem;margin-top:6px}.mini.cyan{box-shadow:inset 0 0 30px rgba(37,217,255,.05)}.mini.gold{box-shadow:inset 0 0 30px rgba(255,201,40,.05)}
        .section{padding:84px 0}.section-head{max-width:760px;margin-bottom:32px}.section-head h2{font-size:clamp(2rem,4vw,3.5rem);letter-spacing:-.05em;line-height:1.02;margin:12px 0}.section-head p{color:var(--muted);font-size:1.05rem}
        .cards{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.card{padding:26px;border-radius:var(--radius);background:linear-gradient(180deg,rgba(17,26,49,.86),rgba(11,18,36,.88));border:1px solid var(--line);box-shadow:0 18px 60px rgba(0,0,0,.18)}.card .icon{width:44px;height:44px;display:grid;place-items:center;border-radius:14px;background:linear-gradient(135deg,rgba(37,217,255,.18),rgba(139,92,246,.18));font-size:1.25rem}.card h3{margin:18px 0 8px;font-size:1.08rem}.card p{margin:0;color:var(--muted);font-size:.94rem}
        .flow{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.step{position:relative;padding:22px;border-radius:20px;background:#0b1224;border:1px solid var(--line)}.step em{font-style:normal;font-size:.75rem;color:var(--cyan);font-weight:900}.step h3{margin:10px 0 8px}.step p{color:var(--muted);margin:0;font-size:.9rem}
        .vip{padding:34px;border-radius:30px;background:linear-gradient(135deg,rgba(37,217,255,.12),rgba(139,92,246,.16) 55%,rgba(255,79,216,.11));border:1px solid rgba(139,92,246,.25);display:grid;grid-template-columns:1fr auto;gap:28px;align-items:center}.vip h2{font-size:clamp(2rem,4vw,3.4rem);letter-spacing:-.05em;margin:0 0 10px}.vip p{margin:0;color:var(--muted);max-width:700px}.vip-actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end}
        .footer{padding:34px 0 46px;border-top:1px solid var(--line);color:var(--muted);font-size:.88rem}.footer-inner{display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap}.footer strong{color:white}
        @media(max-width:900px){.nav-links{display:none}.hero{padding-top:64px}.hero-grid{grid-template-columns:1fr}.dashboard{max-width:680px}.cards{grid-template-columns:1fr 1fr}.flow{grid-template-columns:1fr 1fr}.vip{grid-template-columns:1fr}.vip-actions{justify-content:flex-start}}
        @media(max-width:620px){.wrap{width:min(100% - 26px,1160px)}.nav-inner{height:68px}.nav-actions .btn:first-child{display:none}.hero{padding:54px 0 48px}h1{font-size:clamp(2.7rem,15vw,4.2rem)}.cards,.flow,.mini-grid{grid-template-columns:1fr}.section{padding:62px 0}.vip{padding:24px}.hero-actions .btn,.vip-actions .btn{width:100%}}
    </style>
</head>
<body>
<header class="nav">
    <div class="wrap nav-inner">
        <a class="brand" href="/">Vitrine <span>Social Mídia</span></a>
        <nav class="nav-links" aria-label="Navegação principal">
            <a href="#solucao">Solução</a><a href="#como-funciona">Como funciona</a><a href="#painel">Painel</a><a href="#vip">Lista VIP</a>
        </nav>
        <div class="nav-actions"><a class="btn" href="/app/login">Área do cliente</a><a class="btn btn-primary" href="#vip">Entrar na Lista VIP</a></div>
    </div>
</header>

<main>
<section class="hero" id="painel">
    <div class="wrap hero-grid">
        <div>
            <span class="eyebrow">Presença digital com estratégia + IA</span>
            <h1>Sua empresa <span class="gradient">visível, relevante e lembrada.</span></h1>
            <p class="lead">Planejamento, conteúdo, aprovações, calendário e acompanhamento de resultados em uma experiência única — com operação organizada e visão clara do que está acontecendo.</p>
            <div class="hero-actions"><a class="btn btn-gold" href="#vip">Quero entrar na Lista VIP</a><a class="btn" href="/app/login">Já sou cliente</a></div>
            <div class="trust"><span><b>Conteúdo</b> com consistência</span><span><b>Aprovação</b> sem confusão</span><span><b>Resultados</b> visíveis</span></div>
        </div>
        <div class="dashboard" aria-label="Prévia do painel Vitrine Social Mídia">
            <div class="dash-top"><div class="dots"><i></i><i></i><i></i></div><span class="online">● operação ativa</span></div>
            <div class="dash-hero"><strong>SUA PRESENÇA DIGITAL<br>EM UM SÓ LUGAR</strong><span>Acompanhe entregas, aprove conteúdos e veja resultados.</span></div>
            <div class="mini-grid">
                <div class="mini cyan"><small>Conteúdos pendentes</small><b>08</b></div>
                <div class="mini"><small>Aprovações</small><b>03</b></div>
                <div class="mini gold"><small>Posts agendados</small><b>12</b></div>
                <div class="mini"><small>Plano / saldo</small><b>Ativo</b></div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="solucao">
    <div class="wrap">
        <div class="section-head"><span class="eyebrow">Tudo conectado</span><h2>Menos improviso. Mais presença digital com método.</h2><p>A Vitrine Social Mídia organiza a rotina de conteúdo para que sua empresa saiba o que será publicado, o que precisa ser aprovado e como os canais estão performando.</p></div>
        <div class="cards">
            <article class="card"><div class="icon">✦</div><h3>Calendário Editorial</h3><p>Visão clara do mês, entregas planejadas e cadência de publicação sem depender de mensagens soltas.</p></article>
            <article class="card"><div class="icon">✓</div><h3>Aprovações</h3><p>Conteúdos centralizados para revisar, aprovar ou solicitar ajustes com rastreabilidade.</p></article>
            <article class="card"><div class="icon">↗</div><h3>Desempenho do Mês</h3><p>Acompanhe a evolução das publicações e os principais sinais de alcance e presença.</p></article>
            <article class="card"><div class="icon">◎</div><h3>Canais Conectados</h3><p>Uma visão consolidada dos canais sociais vinculados à operação da sua empresa.</p></article>
            <article class="card"><div class="icon">⚡</div><h3>Solicitações</h3><p>Peça campanhas, ajustes e demandas especiais dentro do fluxo, sem perder contexto.</p></article>
            <article class="card"><div class="icon">◈</div><h3>VIA integrada</h3><p>Suporte contextual dentro da experiência para orientar, explicar e facilitar sua jornada.</p></article>
        </div>
    </div>
</section>

<section class="section" id="como-funciona">
    <div class="wrap">
        <div class="section-head"><span class="eyebrow">Fluxo simples</span><h2>Da estratégia à publicação, com você no controle.</h2></div>
        <div class="flow">
            <article class="step"><em>01</em><h3>Planejamento</h3><p>Objetivos, posicionamento e calendário orientam a produção.</p></article>
            <article class="step"><em>02</em><h3>Criação</h3><p>Conteúdo desenvolvido com apoio de IA e revisão operacional.</p></article>
            <article class="step"><em>03</em><h3>Aprovação</h3><p>Você acompanha e aprova dentro do painel.</p></article>
            <article class="step"><em>04</em><h3>Publicação + visão</h3><p>Agenda, canais e indicadores ficam centralizados.</p></article>
        </div>
    </div>
</section>

<section class="section" id="vip">
    <div class="wrap vip">
        <div><span class="eyebrow">Lista VIP</span><h2>Entre primeiro na Vitrine Social Mídia.</h2><p>Receba prioridade nas próximas vagas, novidades do lançamento e condições de entrada da fase inicial.</p></div>
        <div class="vip-actions"><a class="btn btn-gold" href="mailto:vitrineiapro@gmail.com?subject=Lista%20VIP%20-%20Vitrine%20Social%20M%C3%ADdia&body=Quero%20entrar%20na%20Lista%20VIP%20da%20Vitrine%20Social%20M%C3%ADdia.">Quero entrar na Lista VIP</a><a class="btn" href="/app/login">Acessar painel</a></div>
    </div>
</section>
</main>

<footer class="footer"><div class="wrap footer-inner"><span><strong>Vitrine Social Mídia</strong> · Vitrine IA Pro</span><span>social.vitrineaipro.com.br</span></div></footer>
</body>
</html>
