<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'client_id', 'brand_id', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function recoveryEmail(): string
    {
        $clientEmail = trim((string) ($this->client?->contact_email ?? ''));

        return filter_var($clientEmail, FILTER_VALIDATE_EMAIL)
            ? $clientEmail
            : $this->email;
    }

    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = URL::temporarySignedRoute(
            'filament.client.auth.password-reset.reset',
            now()->addMinutes(60),
            ['token' => $token, 'email' => $this->email],
        );

        if (! function_exists('sendVitrineCommercialMail')) {
            throw new \RuntimeException('Central mail service unavailable.');
        }

        sendVitrineCommercialMail(
            $this->recoveryEmail(),
            'Redefinição de senha - Vitrine Social Mídia',
            "Use o link abaixo para redefinir sua senha:\n\n{$resetUrl}\n\nO link expira em 60 minutos."
        );
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => in_array($this->role, ['admin', 'operator', 'admin_client'], true),
            'client' => in_array($this->role, ['client', 'admin_client'], true) && $this->client_id !== null,
            default => false,
        };
    }
}
