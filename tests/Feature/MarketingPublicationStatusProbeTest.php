<?php

namespace Tests\Feature;

use App\Services\Publishing\MarketingPublicationStatusProbe;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MarketingPublicationStatusProbeTest extends TestCase
{
    private const KEY = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    private const BASE = 'http://vitrine_marketing_web_internal_hml';

    private function configureLookup(): void
    {
        config()->set('social_hub_publishing.lookup_enabled', true);
        config()->set('social_hub_publishing.enabled', false);
        config()->set('services.marketing_engine.base_url', self::BASE);
        config()->set('services.marketing_engine.token', 'dummy-unit-test-token');
        config()->set('services.marketing_engine.project_id', 'vitrine-ai-social-enterprise');
    }

    public function test_disabled_lookup_never_sends_http(): void
    {
        config()->set('social_hub_publishing.lookup_enabled', false);
        Http::fake();
        $this->assertSame(
            ['status' => 'BLOCKED', 'reason' => 'LOOKUP_DISABLED'],
            app(MarketingPublicationStatusProbe::class)->lookup(1, 2, self::KEY)
        );
        Http::assertNothingSent();
    }

    public function test_confirmed_receiver_is_observed_without_publishing(): void
    {
        $this->configureLookup();
        Http::fake([
            self::BASE.'/api/internal/marketing/publisher/meta/publication-status'
                => Http::response([
                    'ok' => true, 'status' => 'PUBLISHED',
                    'idempotency_key' => self::KEY, 'external_id' => 'meta-test-123',
                ], 200),
        ]);

        $this->assertSame(
            ['status' => 'RECEIVER_CONFIRMED', 'external_id' => 'meta-test-123'],
            app(MarketingPublicationStatusProbe::class)->lookup(1, 2, self::KEY)
        );
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) =>
            $request->method() === 'POST'
            && str_ends_with($request->url(), '/publication-status')
            && $request['project_id'] === 'vitrine-ai-social-enterprise'
            && $request['client_id'] === 1
            && $request['content_project_id'] === 2
            && $request['idempotency_key'] === self::KEY
        );
    }

    public function test_mismatched_key_never_confirms_publication(): void
    {
        $this->configureLookup();
        Http::fake(['*' => Http::response([
            'ok' => true, 'status' => 'PUBLISHED',
            'idempotency_key' => str_repeat('b', 64), 'external_id' => 'meta-test-123',
        ])]);
        $this->assertSame(
            ['status' => 'BLOCKED', 'reason' => 'LOOKUP_UNCONFIRMED'],
            app(MarketingPublicationStatusProbe::class)->lookup(1, 2, self::KEY)
        );
    }

    public function test_external_http_host_is_rejected_without_network(): void
    {
        $this->configureLookup();
        config()->set('services.marketing_engine.base_url', 'http://outside.example');
        Http::fake();

        $this->assertSame(
            ['status' => 'BLOCKED', 'reason' => 'LOOKUP_CONFIG_INVALID'],
            app(MarketingPublicationStatusProbe::class)->lookup(1, 2, self::KEY)
        );
        Http::assertNothingSent();
    }

    public function test_processing_result_is_not_publication_confirmation(): void
    {
        $this->configureLookup();
        Http::fake(['*' => Http::response([
            'ok' => true, 'status' => 'PROCESSING', 'idempotency_key' => self::KEY,
        ])]);
        $this->assertSame(
            ['status' => 'BLOCKED', 'reason' => 'RECONCILIATION_REQUIRED'],
            app(MarketingPublicationStatusProbe::class)->lookup(1, 2, self::KEY)
        );
    }
}
