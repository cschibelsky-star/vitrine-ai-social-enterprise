@php
    $panelId = filament()->getCurrentPanel()?->getId();
    $isAdmin = $panelId === 'admin';
    $area = $isAdmin ? 'admin' : 'client';
    $areaTitle = $isAdmin ? 'Administração' : 'Área do Cliente';
    $areaSubtitle = $isAdmin
        ? 'Acesso técnico de contingência da Vitrine Social Mídia'
        : 'Acompanhe entregas, aprovações e resultados da sua marca';
    $googleReady = filled(config('services.social_login.google.client_id')) && filled(config('services.social_login.google.client_secret'));
    $facebookReady = filled(config('services.social_login.facebook.client_id')) && filled(config('services.social_login.facebook.client_secret'));
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
                <span>{{ $googleReady ? 'Entre com Google ou use seu e-mail e senha. Se precisar, recupere sua senha pelo link de acesso.' : 'Entre com seu e-mail e senha. Se precisar, use a recuperação de senha ou fale com o suporte da Vitrine IA Pro.' }}</span>
            </div>
        </section>

        <section class="vsm-login-access">
            <div class="vsm-login-card">
                <div class="vsm-login-badge">{{ $areaTitle }}</div>
                <h2>Acessar plataforma</h2>
                <p>{{ $areaSubtitle }}</p>

                @if (session('social_auth_error'))
                    <div class="vsm-social-error">{{ session('social_auth_error') }}</div>
                @endif

                @if (! $isAdmin && ($googleReady || $facebookReady))
                    <div class="vsm-social-login">
                        @if ($googleReady)
                            <a class="vsm-social-btn vsm-google" href="{{ route('social-auth.redirect', ['area' => $area, 'provider' => 'google']) }}">
                                <span class="vsm-social-icon">G</span><span>Continuar com Google</span>
                            </a>
                        @endif
                        @if ($facebookReady)
                            <a class="vsm-social-btn vsm-facebook" href="{{ route('social-auth.redirect', ['area' => $area, 'provider' => 'facebook']) }}">
                                <span class="vsm-social-icon">f</span><span>Continuar com Facebook</span>
                            </a>
                        @endif
                    </div>
                    <div class="vsm-login-divider"><span>ou entre com e-mail</span></div>
                @endif

                <div class="vsm-login-form">
                    <x-filament-panels::form wire:submit="authenticate">
                        {{ $this->form }}

                        <x-filament-panels::form.actions
                            :actions="$this->getFormActions()"
                            :full-width="$this->hasFullWidthFormActions()"
                        />
                    </x-filament-panels::form>
                </div>
            </div>
        </section>
    </div>

    <style>
        .vsm-social-login{display:grid;gap:12px;margin:24px 0 18px}.vsm-social-btn{min-height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;gap:11px;text-decoration:none;font-weight:800;border:1px solid rgba(255,255,255,.14);transition:.18s ease;background:rgba(255,255,255,.96);color:#172033}.vsm-social-btn:hover{transform:translateY(-1px);box-shadow:0 10px 28px rgba(0,0,0,.18)}.vsm-social-icon{width:24px;height:24px;display:grid;place-items:center;border-radius:50%;font-weight:950}.vsm-facebook{background:#1877f2;color:#fff;border-color:#1877f2}.vsm-facebook .vsm-social-icon{font-family:Arial,sans-serif;font-size:21px}.vsm-login-divider{display:flex;align-items:center;gap:12px;margin:18px 0;color:#8993ad;font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em}.vsm-login-divider:before,.vsm-login-divider:after{content:"";height:1px;flex:1;background:rgba(255,255,255,.1)}.vsm-social-error{margin:16px 0;padding:12px 14px;border-radius:12px;background:rgba(239,68,68,.12);border:1px solid rgba(248,113,113,.25);color:#fecaca;font-size:.88rem;line-height:1.45}
    </style>
</x-filament-panels::page.simple>
