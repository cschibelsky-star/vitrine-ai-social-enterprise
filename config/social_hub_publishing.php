<?php

return [
    // Independent opt-in. Never infer permission from an existing Meta connection.
    'enabled' => (bool) env('SOCIAL_HUB_PUBLISHING_ENABLED', false),
];
