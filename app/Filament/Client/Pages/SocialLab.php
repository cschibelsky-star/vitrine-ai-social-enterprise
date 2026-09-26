<?php

namespace App\Filament\Client\Pages;

use Filament\Pages\Page;

class SocialLab extends Page
{
    protected static ?string $title = 'Laboratório Vitrine Social Mídia';

    protected static ?string $slug = 'lab-social';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.client.pages.social-lab';
}
