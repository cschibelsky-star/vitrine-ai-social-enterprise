@php
    $isAdmin = request()->is('admin', 'admin/*');
    $areaTitle = $isAdmin ? 'Administração' : 'Área do Cliente';
    $areaSubtitle = $isAdmin
        ? 'Acesso técnico de contingência da Vitrine Social Mídia'
        : 'Acompanhe entregas, aprovações e resultados da sua marca';
@endphp

<x-filament-panels::page.simple>
    <div class="vsm-login-shell">
        <section class="vsm-login-brand">
            <div>
                <div class="vsm-brand-kicker">VITRINE IA PRO</div>
                <h1>Vitrine <span>Social Mídia</span></h1>
                <p>Sua presença digital organizada, acompanhada e pronta para crescer.</p>

                <div class="vsm-login-orbit" aria-hidden="true">
                    <div class="vsm-orbit-core">V</div>
                    <div class="vsm-orbit-item i1">Conteúdo</div>
                    <div class="vsm-orbit-item i2">Calendário</div>
                    <div class="vsm-orbit-item i3">Aprovações</div>
                    <div class="vsm-orbit-item i4">Resultados</div>
                    <div class="vsm-orbit-item i5">Canais</div>
                </div>
            </div>

            <div class="vsm-login-help">
                <strong>Precisa de ajuda?</strong>
                <span>Use a recuperação de senha para redefinir seu acesso ou fale com o suporte da Vitrine IA Pro.</span>
            </div>
        </section>

        <section class="vsm-login-access">
            <div class="vsm-login-card">
                <div class="vsm-login-badge">{{ $areaTitle }}</div>
                <h2>Acessar plataforma</h2>
                <p>{{ $areaSubtitle }}</p>
                <div class="vsm-login-form">
                    {{ $this->content }}
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page.simple>
