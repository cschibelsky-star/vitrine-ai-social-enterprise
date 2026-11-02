<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Publishing\AccountPublicationCapabilityPolicy;
use PHPUnit\Framework\TestCase;

final class AccountPublicationCapabilityPolicyTest extends TestCase
{
    private const NOW = 1804158000;

    private function destination(): array
    {
        return ['project_id' => 'vitrine-ai-social-enterprise', 'client_id' => 1,
            'account_id' => 'ig-1', 'channel' => 'instagram'];
    }

    private function snapshot(): array
    {
        return $this->destination() + [
            'contract_version' => 2, 'connected' => true, 'durable_idempotency' => true,
            'checked_at' => self::NOW - 10, 'expires_at' => self::NOW + 290,
            'capabilities' => ['instagram.feed.image', 'instagram.reels.video'],
        ];
    }

    private function content(): array
    {
        return ['placement' => 'reels', 'type' => 'video', 'mime_type' => 'video/mp4'];
    }

    private function inspect(array $changes = [], ?array $content = null): array
    {
        return (new AccountPublicationCapabilityPolicy)->inspect(
            array_replace($this->snapshot(), $changes), $this->destination(),
            $content ?? $this->content(), self::NOW
        );
    }

    public function test_known_reel_is_compatible_for_the_exact_account(): void
    {
        self::assertSame(['status' => 'ALLOWED', 'reason' => 'ACCOUNT_FORMAT_COMPATIBLE',
            'capability' => 'instagram.reels.video'], $this->inspect());
    }

    public function test_cross_tenant_account_project_and_channel_are_rejected(): void
    {
        foreach (['client_id' => 2, 'account_id' => 'ig-2', 'project_id' => 'other', 'channel' => 'facebook'] as $key => $value) {
            self::assertSame('ACCOUNT_IDENTITY_MISMATCH', $this->inspect([$key => $value])['reason']);
        }
    }

    public function test_legacy_status_without_account_identity_cannot_authorize(): void
    {
        $result = (new AccountPublicationCapabilityPolicy)->inspect(
            ['connected' => true, 'contract_version' => 2, 'durable_idempotency' => true,
                'capabilities' => ['instagram.reels.video']],
            $this->destination(), $this->content(), self::NOW
        );
        self::assertSame('BLOCKED', $result['status']);
    }

    public function test_stale_expired_future_or_overlong_snapshots_are_blocked(): void
    {
        foreach ([
            ['checked_at' => self::NOW - 301],
            ['expires_at' => self::NOW],
            ['checked_at' => self::NOW + 1],
            ['expires_at' => self::NOW + 301],
            ['checked_at' => (string) self::NOW],
        ] as $changes) {
            self::assertSame('CAPABILITIES_STALE', $this->inspect($changes)['reason']);
        }
    }

    public function test_revoked_connection_or_receiver_without_guarantees_is_blocked(): void
    {
        foreach (['connected' => false, 'contract_version' => 1, 'durable_idempotency' => false] as $key => $value) {
            self::assertSame('RECEIVER_NOT_READY', $this->inspect([$key => $value])['reason']);
        }
        self::assertSame('RECEIVER_NOT_READY', $this->inspect(['connected' => 'true'])['reason']);
    }

    public function test_same_network_does_not_imply_reels_support(): void
    {
        self::assertSame('FORMAT_NOT_AUTHORIZED', $this->inspect(['capabilities' => ['instagram.feed.image']])['reason']);
    }

    public function test_unsupported_options_are_not_silently_discarded(): void
    {
        foreach ([
            ['first_comment' => 'Comentário'],
            ['mentions' => ['reference-1']],
            ['unknown' => true],
            null,
        ] as $options) {
            self::assertSame('OPTIONS_NOT_IMPLEMENTED',
                $this->inspect([], $this->content() + ['options' => $options])['reason']);
        }
    }

    public function test_carousel_and_wrong_mime_are_blocked_even_when_advertised(): void
    {
        self::assertSame('FORMAT_NOT_AUTHORIZED', $this->inspect(
            ['capabilities' => ['instagram.feed.carousel']],
            ['placement' => 'feed', 'type' => 'carousel', 'mime_type' => 'image/jpeg']
        )['reason']);
        self::assertSame('FORMAT_NOT_AUTHORIZED',
            $this->inspect([], array_replace($this->content(), ['mime_type' => 'image/png']))['reason']);
    }

    public function test_invalid_capability_shapes_are_rejected(): void
    {
        foreach ([null, 'instagram.reels.video', ['key' => 'instagram.reels.video'], [true], [['name' => 'instagram.reels.video']]] as $value) {
            self::assertSame('CAPABILITIES_INVALID', $this->inspect(['capabilities' => $value])['reason']);
        }
    }

    public function test_invalid_destination_and_incomplete_content_are_rejected(): void
    {
        $policy = new AccountPublicationCapabilityPolicy;
        self::assertSame('DESTINATION_INVALID', $policy->inspect($this->snapshot(),
            array_replace($this->destination(), ['client_id' => 0]), $this->content(), self::NOW)['reason']);
        self::assertSame('CONTENT_INVALID', $this->inspect([], [])['reason']);
    }
}
