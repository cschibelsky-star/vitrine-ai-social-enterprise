<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Client;
use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use App\Filament\Client\Pages\Contents;
use App\Models\ContentProject;
use App\Models\User;
use App\Services\AI\AiContentService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_offer_page_shows_vip_waitlist_form(): void
    {
        $this->get('/oferta')
            ->assertStatus(200)
            ->assertSee('Lista VIP')
            ->assertSee('Quero entrar na Lista VIP');
    }

    public function test_vip_waitlist_stores_lead(): void
    {
        $response = $this->post('/lista-vip', [
            'name' => 'Lead Teste',
            'email' => 'lead.teste@example.com',
            'whatsapp' => '19999999999',
            'company' => 'Empresa Teste',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('oferta'));
        $this->assertDatabaseHas('waitlist_leads', [
            'email' => 'lead.teste@example.com',
            'whatsapp' => '19999999999',
            'consent' => 1,
        ]);
    }

    public function test_checkout_redirects_to_infinitepay_url(): void
    {
        config()->set('services.infinitepay.handle', 'vitrine-test');
        config()->set('services.infinitepay.links_url', 'https://api.checkout.example.test/links');

        Http::fake([
            'https://api.checkout.example.test/links' => Http::response([
                'url' => 'https://checkout.example.test/pro',
            ], 200),
        ]);

        $this->get('/checkout/pro')
            ->assertRedirect('https://checkout.example.test/pro');
    }

    public function test_checkout_falls_back_to_offer_when_infinitepay_is_not_configured(): void
    {
        config()->set('services.infinitepay.handle', '');

        $this->get('/checkout/essencial')
            ->assertRedirect(route('oferta'))
            ->assertSessionHas('checkout_unavailable', 'essencial');
    }

    public function test_checkout_rejects_unknown_plan(): void
    {
        $this->get('/checkout/inexistente')->assertNotFound();
    }

    public function test_content_generation_consumes_credit_and_writes_ledger(): void
    {
        config()->set('services.centro_ia.url', '');

        $client = Client::create([
            'name' => 'Cliente Entitlement Teste',
            'status' => 'active',
        ]);

        $brand = Brand::create([
            'client_id' => $client->id,
            'name' => 'Marca Teste',
            'status' => 'active',
        ]);

        ClientSubscription::create([
            'client_id' => $client->id,
            'plan_code' => 'test-plan',
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'source' => 'test',
        ]);

        ClientBalance::create([
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'granted' => 3,
            'consumed' => 0,
            'available' => 3,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->addMonth()->startOfMonth(),
        ]);

        $project = ContentProject::create([
            'client_id' => $client->id,
            'brand_id' => $brand->id,
            'idea' => 'Teste de consumo automático',
            'objective' => 'education',
            'format' => 'post_portrait',
            'channel' => 'instagram',
            'status' => 'draft',
        ]);

        app(AiContentService::class)->generateProject($project);

        $this->assertDatabaseHas('client_balances', [
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'consumed' => 1,
            'available' => 2,
        ]);

        $this->assertDatabaseHas('consumption_ledgers', [
            'client_id' => $client->id,
            'brand_id' => $brand->id,
            'balance_type' => 'content_credit',
            'movement_type' => 'debit',
            'amount' => 1,
        ]);

        $this->assertDatabaseCount('content_generations', 1);
    }

    public function test_client_can_generate_content_from_client_contents_page(): void
    {
        config()->set('services.centro_ia.url', '');

        $client = Client::create([
            'name' => 'Cliente Interface Teste',
            'status' => 'active',
        ]);

        $brand = Brand::create([
            'client_id' => $client->id,
            'name' => 'Marca Interface Teste',
            'tone_of_voice' => 'Profissional e claro',
            'target_audience' => 'Pequenos negócios',
            'status' => 'active',
        ]);

        ClientSubscription::create([
            'client_id' => $client->id,
            'plan_code' => 'test-plan',
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'source' => 'test',
        ]);

        ClientBalance::create([
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'granted' => 3,
            'consumed' => 0,
            'available' => 3,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->addMonth()->startOfMonth(),
        ]);

        $user = User::factory()->create([
            'client_id' => $client->id,
            'role' => 'client',
            'status' => 'active',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('client'));

        Livewire::actingAs($user)
            ->test(Contents::class)
            ->assertSee('Criar conteúdo com IA')
            ->set('brandId', $brand->id)
            ->set('idea', 'Crie um post institucional sobre organização de conteúdo com inteligência artificial.')
            ->set('objective', 'institutional')
            ->set('format', 'post_portrait')
            ->set('channel', 'instagram')
            ->call('generateContent')
            ->assertHasNoErrors();

        $project = ContentProject::query()
            ->where('client_id', $client->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($project);
        $this->assertSame('editing', $project->status);
        $this->assertNotEmpty($project->title);
        $this->assertNotEmpty($project->caption);
        $this->assertDatabaseHas('client_balances', [
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'consumed' => 1,
            'available' => 2,
        ]);
    }
}
