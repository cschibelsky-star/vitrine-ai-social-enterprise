<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ContentProject;
use App\Services\Publishing\IdempotentMetaPublicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

class SocialHubPublishingDisabledTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabled_publisher_has_no_external_or_persistence_side_effects(): void
    {
        config()->set('social_hub_publishing.enabled', false);
        Http::fake();
        Queue::fake();

        $client = Client::create(['name' => 'Isolated disabled-publisher test']);
        $project = ContentProject::create([
            'client_id' => $client->id,
            'idea' => 'No real publication',
            'channel' => 'instagram',
            'status' => 'ready',
        ]);

        try {
            app(IdempotentMetaPublicationService::class)->publish($project);
            $this->fail('Disabled publishing must fail closed.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('desativada até homologação', $exception->getMessage());
        }

        $this->assertDatabaseCount('social_publication_operations', 0);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
        $this->assertNull($project->fresh()->published_at);
    }
}
