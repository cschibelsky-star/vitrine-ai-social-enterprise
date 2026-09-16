<?php

if (! hash_equals('vsm-7f4c9a31-20260916', (string) ($_GET['k'] ?? ''))) {
    http_response_code(403);
    exit('forbidden');
}

clearstatcache(true);
$opcache = function_exists('opcache_reset') ? opcache_reset() : null;

header('Content-Type: application/json');
echo json_encode([
    'ok' => true,
    'opcache_reset' => $opcache,
], JSON_UNESCAPED_SLASHES);
