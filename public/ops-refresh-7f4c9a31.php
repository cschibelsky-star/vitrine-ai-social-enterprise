<?php

clearstatcache(true);
$opcache = function_exists('opcache_reset') ? opcache_reset() : null;

header('Content-Type: application/json');
echo json_encode([
    'ok' => true,
    'opcache_reset' => $opcache,
], JSON_UNESCAPED_SLASHES);
