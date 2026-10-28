<?php

namespace Tests\Unit;

use App\Models\SocialPublicationOperation;
use PHPUnit\Framework\TestCase;

class SocialPublicationOperationTest extends TestCase
{
    public function test_idempotency_key_is_deterministic_for_same_operation(): void
    {
        $first = SocialPublicationOperation::idempotencyKey(1, 27, 'instagram', 'v1');
        $second = SocialPublicationOperation::idempotencyKey(1, 27, 'instagram', 'v1');

        $this->assertSame($first, $second);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $first);
    }

    public function test_idempotency_key_is_scoped_to_client_project_channel_and_version(): void
    {
        $baseline = SocialPublicationOperation::idempotencyKey(1, 27, 'instagram', 'v1');

        foreach ([
            [2, 27, 'instagram', 'v1'],
            [1, 28, 'instagram', 'v1'],
            [1, 27, 'facebook', 'v1'],
            [1, 27, 'instagram', 'v2'],
        ] as [$clientId, $projectId, $channel, $version]) {
            $this->assertNotSame(
                $baseline,
                SocialPublicationOperation::idempotencyKey($clientId, $projectId, $channel, $version)
            );
        }
    }

    public function test_publishing_and_uncertain_outcome_have_distinct_states(): void
    {
        $this->assertNotSame(
            SocialPublicationOperation::PUBLISHING,
            SocialPublicationOperation::PENDING_CONFIRMATION
        );
        $this->assertNotSame(
            SocialPublicationOperation::RETRYABLE_ERROR,
            SocialPublicationOperation::PENDING_CONFIRMATION
        );
        $this->assertNotSame(
            SocialPublicationOperation::PUBLISHED,
            SocialPublicationOperation::QUEUED
        );
    }
}
