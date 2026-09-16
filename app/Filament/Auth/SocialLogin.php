<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login;

class SocialLogin extends Login
{
    protected static string $view = 'filament.auth.social-login';
}
