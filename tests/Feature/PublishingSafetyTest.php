<?php
namespace Tests\Feature;
use App\Models\ContentProject;
use App\Services\Publishing\{ApprovedAssetService, MetaPublisherService, PublicationContract, PublicationScheduleService};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Http, Schema, Storage, URL};
use Tests\TestCase;
class PublishingSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'publishing.enabled' => true, 'services.marketing_engine.token' => 'test-only',
            'services.marketing_engine.base_url' => 'https://publisher.test', 'app.url' => 'https://social.test',
            'app.key' => 'base64:'.base64_encode(str_repeat('x', 32))]);
        DB::purge('sqlite'); URL::forceRootUrl('https://social.test'); URL::forceScheme('https');
        Http::preventStrayRequests(); Storage::fake('local');
        Schema::create('content_projects', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('client_id'); $t->string('channel'); $t->string('format');
            $t->string('status'); $t->text('caption')->nullable(); $t->text('cta')->nullable(); $t->text('hashtags')->nullable();
            $t->timestamp('scheduled_at')->nullable(); $t->timestamp('published_at')->nullable(); $t->timestamps();
        });
        (require base_path('database/migrations/2026_10_12_210000_add_publishing_safety.php'))->up();
    }
    private function project(string $format = 'feed', string $mime = 'image/jpeg'): ContentProject
    {
        $p = new ContentProject;
        $p->forceFill(['client_id' => 1, 'channel' => 'instagram', 'format' => $format, 'status' => 'review',
            'caption' => 'Approved caption', 'publishing_asset' => ['path' => 'publisher/assets/a.jpg',
                'mime' => $mime, 'sha256' => hash('sha256', 'final bytes')]])->save();
        Storage::disk('local')->put('publisher/assets/a.jpg', 'final bytes');
        (new ApprovedAssetService)->approve($p, 1); return $p->refresh();
    }
    private function fake(string $placement = 'feed', string $type = 'image', array $response = []): void
    {
        Http::fake([
            'https://publisher.test/*/status' => Http::response(['connected' => true, 'contract_version' => 2,
                'durable_idempotency' => true, 'capabilities' => ['instagram.'.$placement.'.'.$type]]),
            'https://publisher.test/*/publish' => Http::response($response ?: ['ok' => true, 'status' => 'PUBLISHED', 'external_id' => 'meta-1']),
        ]);
    }
    public function test_feed_publishes_only_once(): void
    {
        $p = $this->project(); $this->fake(); $service = new MetaPublisherService;
        $service->publish($p); $service->publish($p);
        Http::assertSentCount(2); $this->assertSame('published', $p->refresh()->status);
        $this->assertSame(1, DB::table('publication_attempts')->count());
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/publish')
            && $request['placement'] === 'feed' && strlen($request['idempotency_key']) === 64);
    }
    public function test_reels_uses_video_contract(): void
    {
        $p = $this->project('reels', 'video/mp4'); $this->fake('reels', 'video');
        (new MetaPublisherService)->publish($p);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/publish') && $request['type'] === 'video'
            && $request['placement'] === 'reels' && $request['mime_type'] === 'video/mp4');
    }
    public function test_stories_do_not_send_feed_caption(): void
    {
        $p = $this->project('stories'); $this->fake('stories'); (new MetaPublisherService)->publish($p);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/publish') && $request['caption'] === ''
            && $request['placement'] === 'stories');
    }
    public function test_disabled_hml_makes_no_requests(): void
    {
        config(['publishing.enabled' => false]);
        try { (new MetaPublisherService)->publish($this->project()); $this->fail('Must block'); }
        catch (\RuntimeException) { Http::assertNothingSent(); }
    }
    public function test_changed_caption_requires_new_approval(): void
    {
        $p = $this->project(); $p->caption = 'Changed'; $p->save(); $this->fake();
        try { (new MetaPublisherService)->publish($p); $this->fail('Must block'); }
        catch (\RuntimeException) { $this->assertSame(0, DB::table('publication_attempts')->count()); }
    }
    public function test_changed_file_blocks_publish(): void
    {
        $p = $this->project(); Storage::disk('local')->put('publisher/assets/a.jpg', 'tampered');
        try { (new MetaPublisherService)->publish($p); $this->fail('Must block'); }
        catch (\RuntimeException) { Http::assertNothingSent(); }
    }
    public function test_legacy_receiver_is_blocked(): void
    {
        $p = $this->project(); Http::fake(['*' => Http::response(['connected' => true])]);
        try { (new MetaPublisherService)->publish($p); $this->fail('Must block'); }
        catch (\RuntimeException) { Http::assertSentCount(1); $this->assertSame(0, DB::table('publication_attempts')->count()); }
    }
    public function test_unconfirmed_response_cannot_be_retried(): void
    {
        $p = $this->project(); $this->fake('feed', 'image', ['ok' => false, 'status' => 'ERROR']);
        $service = new MetaPublisherService;
        for ($i = 0; $i < 2; $i++) {
            try { $service->publish($p); $this->fail('Must block'); } catch (\RuntimeException) {}
        }
        Http::assertSentCount(2); $this->assertSame('unknown', DB::table('publication_attempts')->value('status'));
    }
    public function test_transport_failure_does_not_retry_submission(): void
    {
        $p = $this->project();
        Http::fake(function ($request) {
            if (str_ends_with($request->url(), '/status')) {
                return Http::response(['connected' => true, 'contract_version' => 2,
                    'durable_idempotency' => true, 'capabilities' => ['instagram.feed.image']]);
            }
            throw new \Illuminate\Http\Client\ConnectionException('simulated timeout');
        });
        try { (new MetaPublisherService)->publish($p); $this->fail('Must block'); } catch (\RuntimeException) {}
        $this->assertSame('unknown', DB::table('publication_attempts')->value('status'));
        $this->assertSame('publication_unknown', $p->refresh()->status);
    }
    public function test_pending_is_not_marked_published(): void
    {
        $p = $this->project('reels', 'video/mp4'); $this->fake('reels', 'video', ['ok' => true, 'status' => 'PROCESSING']);
        try { (new MetaPublisherService)->publish($p); $this->fail('Must wait'); } catch (\RuntimeException) {}
        $this->assertNull($p->refresh()->published_at);
        $this->assertSame('processing', DB::table('publication_attempts')->value('status'));
    }
    public function test_future_schedule_is_not_sent_early(): void
    {
        $p = $this->project(); (new PublicationScheduleService)->schedule($p, now()->addHour());
        $this->fake();
        try { (new MetaPublisherService)->publish($p); $this->fail('Must wait'); } catch (\RuntimeException) {}
        $this->assertSame(0, DB::table('publication_attempts')->count());
    }
    public function test_image_cannot_be_used_as_reel(): void
    {
        $this->expectException(\RuntimeException::class); $this->project('reels', 'image/jpeg');
    }
    public function test_png_cannot_bypass_instagram_preparation(): void
    {
        $this->expectException(\RuntimeException::class); $this->project('stories', 'image/png');
    }
    public function test_cancelled_schedule_job_is_noop(): void
    {
        $p = $this->project(); (new \App\Jobs\PublishScheduledContent($p->id))->handle(new MetaPublisherService);
        Http::assertNothingSent();
    }
    public function test_reschedule_generation_invalidates_old_jobs_even_when_time_returns_to_original(): void
    {
        $p = $this->project();
        $scheduler = new PublicationScheduleService;
        $original = now()->addHours(2);
        $scheduler->schedule($p, $original);
        $firstGeneration = (int) $p->refresh()->schedule_generation;

        $scheduler->schedule($p, now()->addHours(3));
        $scheduler->schedule($p, $original);
        $latestGeneration = (int) $p->refresh()->schedule_generation;
        $this->assertGreaterThan($firstGeneration, $latestGeneration);

        (new \App\\Jobs\\PublishScheduledContent($p->id, $firstGeneration))
            ->handle(new MetaPublisherService);
        Http::assertNothingSent();

        $scheduler->withdraw($p);
        (new \App\\Jobs\\PublishScheduledContent($p->id, $latestGeneration))
            ->handle(new MetaPublisherService);
        Http::assertNothingSent();
    }

    public function test_duplicate_attempt_has_database_constraint(): void
    {
        $p = $this->project(); $this->fake(); (new MetaPublisherService)->publish($p);
        $this->expectException(\Illuminate\Database\QueryException::class);
        DB::table('publication_attempts')->insert(['content_project_id' => $p->id, 'idempotency_key' => str_repeat('a', 64),
            'status' => 'submitted', 'created_at' => now(), 'updated_at' => now()]);
    }
    public function test_campaign_manifest_has_thirty_usable_contract_cases(): void
    {
        $assets = json_decode(file_get_contents(base_path('tests/Fixtures/publishing/campaign-manifest.json')), true, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(30, $assets);
        $counts = [];
        foreach ($assets as $asset) {
            $counts[$asset['placement']] = ($counts[$asset['placement']] ?? 0) + 1;
            foreach (['instagram', 'facebook'] as $channel) {
                $p = new ContentProject;
                $p->forceFill(['id' => 1, 'client_id' => 1, 'channel' => $channel, 'format' => $asset['placement'],
                    'publishing_asset' => ['path' => 'local-final', 'sha256' => $asset['sha256'],
                        'mime' => $channel === 'instagram' && $asset['mime'] === 'image/png' ? 'image/jpeg' : $asset['mime']]]);
                $payload = (new PublicationContract)->payload($p);
                $this->assertSame($asset['placement'], $payload['placement']);
            }
            if ($asset['placement'] === 'reels') {
                $this->assertSame('h264', $asset['codec']); $this->assertSame(['aac'], $asset['audio']);
                $this->assertGreaterThanOrEqual(4, $asset['duration']); $this->assertLessThanOrEqual(60, $asset['duration']);
            }
        }
        $this->assertSame(['feed' => 10, 'reels' => 10, 'stories' => 10], $counts);
    }
    public function test_reconciliation_reads_without_resubmission(): void
    {
        $p = $this->project(); $this->fake('feed', 'image', ['ok' => true, 'status' => 'PROCESSING']);
        $publisher = new MetaPublisherService;
        try { $publisher->publish($p); } catch (\RuntimeException) {}
        $key = DB::table('publication_attempts')->value('idempotency_key');
        Http::fake(['*publication-status' => Http::response(['ok' => true, 'status' => 'PUBLISHED',
            'external_id' => 'reconciled-1', 'idempotency_key' => $key])]);
        $publisher->reconcile($p);
        $this->assertSame('published', $p->refresh()->status);
        $this->assertSame('reconciled-1', DB::table('publication_attempts')->value('external_id'));
    }
    public function test_unsigned_asset_url_is_rejected(): void
    {
        $p = $this->project();
        $this->get('/publisher/approved-assets/'.$p->id.'/'.$p->publishing_asset['sha256'])->assertForbidden();
    }
    public function test_local_png_import_requires_review_and_instagram_jpeg(): void
    {
        $p = $this->project(); $image = imagecreatetruecolor(1080, 1080);
        $file = tempnam(sys_get_temp_dir(), 'publisher-'); imagepng($image, $file); imagedestroy($image);
        try {
            (new ApprovedAssetService)->attach($p, $file);
            $p->refresh();
            $this->assertSame('review', $p->status); $this->assertNull($p->approved_at);
            $this->assertSame('image/jpeg', $p->publishing_asset['mime']);
            (new ApprovedAssetService)->verify($p); Http::assertNothingSent();
        } finally { unlink($file); }
    }
    public function test_asset_change_during_preflight_cannot_send_stale_payload(): void
    {
        $p = $this->project();
        Http::fake(function () use ($p) {
            $p->caption = 'Changed during capability lookup'; $p->save();
            (new ApprovedAssetService)->approve($p, 1);
            return Http::response(['connected' => true, 'contract_version' => 2,
                'durable_idempotency' => true, 'capabilities' => ['instagram.feed.image']]);
        });
        try { (new MetaPublisherService)->publish($p); $this->fail('Must block stale snapshot'); } catch (\RuntimeException) {}
        Http::assertSentCount(1); $this->assertSame(0, DB::table('publication_attempts')->count());
    }
    public function test_withdraw_cannot_change_submitted_publication(): void
    {
        $p = $this->project(); $this->fake(); (new MetaPublisherService)->publish($p);
        $this->expectException(\RuntimeException::class);
        (new PublicationScheduleService)->withdraw($p, true);
    }
    public function test_failed_scheduled_job_stops_scanner_retry(): void
    {
        $p = $this->project(); (new PublicationScheduleService)->schedule($p, now()->addHour());
        (new \App\Jobs\PublishScheduledContent($p->id))->failed(new \RuntimeException('configuration'));
        $this->assertSame('publication_blocked', $p->refresh()->status);
    }
}
