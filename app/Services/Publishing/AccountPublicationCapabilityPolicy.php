<?php

declare(strict_types=1);

namespace App\Services\Publishing;

/**
 * Pure preflight policy for a proposed, trusted per-account capability snapshot.
 * ALLOWED is format compatibility only, never permission to dispatch.
 * Not connected to an executor; the receiver must implement this contract first.
 */
final class AccountPublicationCapabilityPolicy
{
    private const IMPLEMENTED = [
        'instagram.feed.image' => ['image/jpeg'],
        'instagram.stories.image' => ['image/jpeg'],
        'instagram.stories.video' => ['video/mp4'],
        'instagram.reels.video' => ['video/mp4'],
        'facebook.feed.image' => ['image/jpeg', 'image/png'],
    ];

    public function inspect(array $snapshot, array $destination, array $content, int $now): array
    {
        if (! is_int($destination['client_id'] ?? null) || $destination['client_id'] < 1
            || ! is_string($destination['project_id'] ?? null) || trim($destination['project_id']) === ''
            || ! is_string($destination['account_id'] ?? null) || trim($destination['account_id']) === ''
            || ! in_array($destination['channel'] ?? null, ['instagram', 'facebook'], true)) {
            return $this->blocked('DESTINATION_INVALID');
        }

        foreach (['project_id', 'client_id', 'account_id', 'channel'] as $field) {
            if (($snapshot[$field] ?? null) !== $destination[$field]) {
                return $this->blocked('ACCOUNT_IDENTITY_MISMATCH');
            }
        }

        if (($snapshot['contract_version'] ?? null) !== 2
            || ($snapshot['connected'] ?? null) !== true
            || ($snapshot['durable_idempotency'] ?? null) !== true) {
            return $this->blocked('RECEIVER_NOT_READY');
        }

        $checked = $snapshot['checked_at'] ?? null;
        $expires = $snapshot['expires_at'] ?? null;
        if (! is_int($checked) || ! is_int($expires) || $now < 1
            || $checked < 1 || $checked > $now || $now - $checked > 300
            || $expires <= $now || $expires <= $checked || $expires - $checked > 300) {
            return $this->blocked('CAPABILITIES_STALE');
        }

        $capabilities = $snapshot['capabilities'] ?? null;
        if (! is_array($capabilities) || ! array_is_list($capabilities)) {
            return $this->blocked('CAPABILITIES_INVALID');
        }
        foreach ($capabilities as $entry) {
            if (! is_string($entry)) {
                return $this->blocked('CAPABILITIES_INVALID');
            }
        }

        $placement = $content['placement'] ?? null;
        $type = $content['type'] ?? null;
        $mime = $content['mime_type'] ?? null;
        if (! is_string($placement) || ! is_string($type) || ! is_string($mime)) {
            return $this->blocked('CONTENT_INVALID');
        }
        $capability = $destination['channel'].'.'.$placement.'.'.$type;
        if (! isset(self::IMPLEMENTED[$capability])
            || ! in_array($mime, self::IMPLEMENTED[$capability], true)
            || ! in_array($capability, $capabilities, true)) {
            return $this->blocked('FORMAT_NOT_AUTHORIZED');
        }

        // No adapter for first_comment, structured mentions or custom options yet.
        // Never drop requested options silently, even if a receiver advertises them.
        if (array_key_exists('options', $content)
            && (! is_array($content['options']) || $content['options'] !== [])) {
            return $this->blocked('OPTIONS_NOT_IMPLEMENTED');
        }

        return ['status' => 'ALLOWED', 'reason' => 'ACCOUNT_FORMAT_COMPATIBLE', 'capability' => $capability];
    }

    private function blocked(string $reason): array
    {
        return ['status' => 'BLOCKED', 'reason' => $reason];
    }
}
