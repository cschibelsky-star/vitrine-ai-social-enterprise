<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_panel_but_not_client_panel(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->assertTrue($user->canAccessPanel(Panel::make()->id('admin')));
        $this->assertFalse($user->canAccessPanel(Panel::make()->id('client')));
    }

    public function test_client_with_client_id_can_access_client_panel_but_not_admin_panel(): void
    {
        $client = Client::query()->create([
            'name' => 'Cliente Teste',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'client_id' => $client->id,
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->assertTrue($user->canAccessPanel(Panel::make()->id('client')));
        $this->assertFalse($user->canAccessPanel(Panel::make()->id('admin')));
    }

    public function test_inactive_user_cannot_access_any_panel(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'inactive',
        ]);

        $this->assertFalse($user->canAccessPanel(Panel::make()->id('admin')));
        $this->assertFalse($user->canAccessPanel(Panel::make()->id('client')));
    }
}
