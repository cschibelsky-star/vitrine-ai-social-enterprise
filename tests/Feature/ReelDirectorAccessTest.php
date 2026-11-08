<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Client;
use App\Models\ContentProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HML candidate: verify Filament client authorization and tenant isolation.
 * No publication, balance debit, API calls, or external providers.
 */
class ReelDirectorAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_read_reel_director(): void
    {
        $response = $this->get('/app/diretor-reels');
        $this->assertNotEquals(200, $response->status());
    }

    public function test_client_sees_only_owned_reel_projects(): void
    {
        $alpha = Client::create(['name' => 'Cliente Alfa', 'status' => 'active']);
        $beta = Client::create(['name' => 'Cliente Beta', 'status' => 'active']);
        $brandAlpha = Brand::create(['client_id' => $alpha->id, 'name' => 'Marca Alfa', 'status' => 'active']);
        $brandBeta = Brand::create(['client_id' => $beta->id, 'name' => 'Marca Beta', 'status' => 'active']);

        $own = ContentProject::create([
            'client_id' => $alpha->id, 'brand_id' => $brandAlpha->id,
            'idea' => 'Conheça o nosso catálogo de produtos', 'format' => 'reels',
            'title' => 'Reel exclusivo Alfa', 'status' => 'draft',
        ]);
        $foreign = ContentProject::create([
            'client_id' => $beta->id, 'brand_id' => $brandBeta->id,
            'idea' => 'Conheça uma promoção exclusiva para clientes Beta', 'format' => 'reels',
            'title' => 'Reel confidencial Beta', 'status' => 'draft',
        ]);

        $user = User::factory()->create([
            'client_id' => $alpha->id, 'role' => 'client', 'status' => 'active',
        ]);
        $this->actingAs($user)
            ->get('/app/diretor-reels?project_id='.$own->id)
            ->assertOk()
            ->assertSee('Reel exclusivo Alfa')
            ->assertDontSee('Reel confidencial Beta');

        $this->actingAs($user)
            ->get('/app/diretor-reels?project_id='.$foreign->id)
            ->assertOk()
            ->assertSee('Reel exclusivo Alfa')
            ->assertDontSee('Reel confidencial Beta')
            ->assertDontSee('Conheça uma promoção exclusiva');

        $this->assertSame('draft', $own->fresh()->status);
        $this->assertSame('draft', $foreign->fresh()->status);
    }
}
