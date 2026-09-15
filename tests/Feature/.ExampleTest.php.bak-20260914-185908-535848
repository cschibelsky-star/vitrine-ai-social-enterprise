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
}
