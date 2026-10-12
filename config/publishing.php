<?php
return [
    // Opt-in only after HML contract and Meta authorization validation.
    'enabled' => (bool) env('SOCIAL_PUBLISHING_ENABLED', false),
    'timezone' => env('SOCIAL_PUBLISHING_TIMEZONE', 'America/Sao_Paulo'),
];
