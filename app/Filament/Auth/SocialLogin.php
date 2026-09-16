<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login;

class SocialLogin extends Login
{
    protected string $view = 'filament.auth.social-login';
}
