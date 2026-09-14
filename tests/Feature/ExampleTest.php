<?php

namespace Tests\Feature;

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
}
