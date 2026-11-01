<?php

namespace App\Services\Publishing;

/**
 * Pure state policy for staging the Vitrine Social Hub publication lifecycle.
 * This class never writes to storage, queues work, or calls the Meta API.
 * It is not an authorization to publish: all transport actions remain disabled
 * until the shared transactional dispatch contract is approved in isolated HML.
 */
final class DualLedgerPublicationStatePolicy
{
    /** @return array{status: string, reason: string} */
    public function inspect(
        string $attemptStatus,
        string $operationStatus,
        int $operationAttempts,
        ?string $attemptExternalId,
        ?string $operationExternalId,
        ?string $containerId,
        bool $projectPublished
    ): array {
        if ($attemptStatus === 'published'
            && $operationStatus === 'published'
            && $projectPublished
            && ! empty($attemptExternalId)
            && hash_equals($attemptExternalId, (string) $operationExternalId)) {
            return ['status' => 'PUBLISHED', 'reason' => 'BOTH_CONFIRMED'];
        }

        if ($attemptStatus === 'reserved'
            && $operationStatus === 'queued'
            && $operationAttempts === 0
            && empty($attemptExternalId)
            && empty($operationExternalId)
            && empty($containerId)
            && ! $projectPublished) {
            return ['status' => 'RESERVED', 'reason' => 'NO_SUBMISSION_RECORDED'];
        }

        // A mismatched or partially confirmed result must never be interpreted
        // as safe to resend. The Marketing transport must be checked separately.
        return ['status' => 'BLOCKED', 'reason' => 'RECONCILIATION_REQUIRED'];
    }

    /**
     * Explicit local lifecycle design only. In particular an unknown outcome
     * can never move back to queued/submitting via automatic retry.
     */
    public function allowsTransition(string $from, string $to): bool
    {
        $allowed = [
            'reserved' => ['cancelled', 'submitting'],
            'submitting' => ['processing', 'unknown', 'published'],
            'processing' => ['unknown', 'published'],
            'unknown' => ['published', 'blocked'],
            'blocked' => [],
            'cancelled' => [],
            'published' => [],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }
}
