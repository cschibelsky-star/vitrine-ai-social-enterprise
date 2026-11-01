<?php

namespace App\Services\Publishing;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Read-only Marketing IA ledger probe. No publish, upload, queue or retry.
 * An explicit opt-in only enables lookup; it NEVER enables Meta publishing.
 */
final class MarketingPublicationStatusProbe
{
    public function lookup(int $clientId, int $contentProjectId, string $key): array
    {
        if (! config('social_hub_publishing.lookup_enabled', false)) {
            return ['status' => 'BLOCKED', 'reason' => 'LOOKUP_DISABLED'];
        }

        $base = rtrim((string) config('services.marketing_engine.base_url', ''), '/');
        $token = trim((string) config('services.marketing_engine.token', ''));
        $project = (string) config('services.marketing_engine.project_id', 'vitrine-ai-social-enterprise');

        if ($clientId < 1 || $contentProjectId < 1
            || ! preg_match('/^[a-f0-9]{64}$/', $key)
            || $token === '' || $base === ''
            || ! filter_var($base, FILTER_VALIDATE_URL)
            || ! in_array(parse_url($base, PHP_URL_SCHEME), ['https', 'http'], true)
            || parse_url($base, PHP_URL_USER) !== null
            || parse_url($base, PHP_URL_PASS) !== null
            || $project !== 'vitrine-ai-social-enterprise') {
            return ['status' => 'BLOCKED', 'reason' => 'LOOKUP_CONFIG_INVALID'];
        }

        // Only explicitly configured internal HTTP hosts may use plaintext.
        if (parse_url($base, PHP_URL_SCHEME) === 'http'
            && parse_url($base, PHP_URL_HOST) !== 'vitrine_marketing_web_internal_hml') {
            return ['status' => 'BLOCKED', 'reason' => 'LOOKUP_CONFIG_INVALID'];
        }

        try {
            $response = Http::withToken($token)->acceptJson()
                ->connectTimeout(3)->timeout(10)
                ->post($base.'/api/internal/marketing/publisher/meta/publication-status', [
                    'project_id' => $project,
                    'client_id' => $clientId,
                    'content_project_id' => $contentProjectId,
                    'idempotency_key' => $key,
                ]);
            if (! $response->successful()
                || $response->json('ok') !== true
                || ! hash_equals($key, (string) $response->json('idempotency_key', ''))) {
                return ['status' => 'BLOCKED', 'reason' => 'LOOKUP_UNCONFIRMED'];
            }

            $state = strtoupper((string) $response->json('status', ''));
            if ($state === 'PUBLISHED'
                && is_string($response->json('external_id'))
                && $response->json('external_id') !== '') {
                // Observation only. Does not mark local ledgers published.
                return ['status' => 'RECEIVER_CONFIRMED', 'external_id' => $response->json('external_id')];
            }

            return ['status' => 'BLOCKED', 'reason' => 'RECONCILIATION_REQUIRED'];
        } catch (\Throwable) {
            return ['status' => 'BLOCKED', 'reason' => 'LOOKUP_UNAVAILABLE'];
        }
    }
}
