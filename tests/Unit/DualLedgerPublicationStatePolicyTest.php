<?php

namespace Tests\Unit;

use App\Services\Publishing\DualLedgerPublicationStatePolicy;
use PHPUnit\Framework\TestCase;

class DualLedgerPublicationStatePolicyTest extends TestCase
{
    public function test_explicit_transitions_are_strict_and_terminal_states_never_retry(): void
    {
        $policy = new DualLedgerPublicationStatePolicy();

        $this->assertTrue($policy->allowsTransition('reserved', 'submitting'));
        $this->assertTrue($policy->allowsTransition('submitting', 'processing'));
        $this->assertTrue($policy->allowsTransition('processing', 'unknown'));
        $this->assertTrue($policy->allowsTransition('unknown', 'published'));

        foreach (['reserved', 'queued', 'submitting', 'processing'] as $to) {
            $this->assertFalse($policy->allowsTransition('unknown', $to));
            $this->assertFalse($policy->allowsTransition('published', $to));
            $this->assertFalse($policy->allowsTransition('cancelled', $to));
            $this->assertFalse($policy->allowsTransition('blocked', $to));
        }
        $this->assertFalse($policy->allowsTransition('unexpected', 'submitting'));
    }

    public function test_partial_confirmation_never_counts_as_published(): void
    {
        $policy = new DualLedgerPublicationStatePolicy();

        $this->assertSame(
            ['status' => 'BLOCKED', 'reason' => 'RECONCILIATION_REQUIRED'],
            $policy->inspect('published', 'queued', 0, 'meta-123', null, null, false)
        );
        $this->assertSame(
            ['status' => 'BLOCKED', 'reason' => 'RECONCILIATION_REQUIRED'],
            $policy->inspect('unknown', 'pending_confirmation', 1, null, null, 'container-1', false)
        );
        $this->assertSame(
            ['status' => 'BLOCKED', 'reason' => 'RECONCILIATION_REQUIRED'],
            $policy->inspect('published', 'published', 1, 'meta-123', 'meta-456', null, true)
        );
    }

    public function test_clean_reservation_and_consistent_confirmation_are_recognized(): void
    {
        $policy = new DualLedgerPublicationStatePolicy();
        $this->assertSame(
            ['status' => 'RESERVED', 'reason' => 'NO_SUBMISSION_RECORDED'],
            $policy->inspect('reserved', 'queued', 0, null, null, null, false)
        );
        $this->assertSame(
            ['status' => 'PUBLISHED', 'reason' => 'BOTH_CONFIRMED'],
            $policy->inspect('published', 'published', 1, 'meta-123', 'meta-123', null, true)
        );
    }
}
