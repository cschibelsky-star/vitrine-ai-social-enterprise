<?php

return [
    // Independent opt-in. Never infer permission from an existing Meta connection.
    'enabled' => (bool) env('SOCIAL_HUB_PUBLISHING_ENABLED', false),
    // Read-only receiver status lookup; independent from publishing permissions.
    'lookup_enabled' => (bool) env('SOCIAL_HUB_MARKETING_LOOKUP_ENABLED', false),
];
