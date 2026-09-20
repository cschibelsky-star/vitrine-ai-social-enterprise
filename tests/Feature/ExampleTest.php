<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Client;
use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use App\Models\ContentProject;
use App\Services\AI\AiContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_checkout_redirects_to_configured_asaas_url(): void
    {
        config()->set('services.asaas.checkout_links.pro', 'https://checkout.example.test/pro');

        $this->get('/checkout/pro')
            ->assertRedirect('https://checkout.example.test/pro');
    }

    public function test_checkout_falls_back_to_offer_when_plan_url_is_not_configured(): void
    {
        config()->set('services.asaas.checkout_links.essencial', null);

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
}
