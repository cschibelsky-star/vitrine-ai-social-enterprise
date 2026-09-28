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

    public function test_admin_client_can_access_both_panels(): void
    {
        $client = Client::query()->create([
            'name' => 'Cliente Admin',
            'contact_email' => 'admin@example.com',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'client_id' => $client->id,
            'role' => 'admin_client',
            'status' => 'active',
        ]);

        $this->assertTrue($user->canAccessPanel(Panel::make()->id('admin')));
        $this->assertTrue($user->canAccessPanel(Panel::make()->id('client')));
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

    public function test_client_recovery_email_is_independent_from_login_email(): void
    {
        $client = Client::query()->create([
            'name' => 'Cliente Recovery',
            'contact_email' => 'responsavel@example.com',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'client_id' => $client->id,
            'email' => 'login-tecnico@example.test',
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->assertSame('responsavel@example.com', $user->recoveryEmail());
        $this->assertNotSame($user->email, $user->recoveryEmail());
    }

    public function test_user_without_client_contact_falls_back_to_login_email(): void
    {
        $user = User::factory()->create([
            'email' => 'usuario@example.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->assertSame('usuario@example.com', $user->recoveryEmail());
    }
}
