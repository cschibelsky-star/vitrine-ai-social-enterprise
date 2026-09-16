<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(Request $request, string $area, string $provider): RedirectResponse
    {
        $this->assertAreaProvider($area, $provider);

        $config = config("services.social_login.{$provider}");
        if (blank($config['client_id'] ?? null) || blank($config['client_secret'] ?? null)) {
            return $this->backToLogin($area, 'Login social ainda não configurado para este provedor.');
        }

        $state = Str::random(64);
        $request->session()->put("social_oauth.{$area}.{$provider}.state", $state);

        $callback = route('social-auth.callback', ['area' => $area, 'provider' => $provider]);

        if ($provider === 'google') {
            $query = http_build_query([
                'client_id' => $config['client_id'],
                'redirect_uri' => $callback,
                'response_type' => 'code',
                'scope' => 'openid email profile',
                'state' => $state,
                'access_type' => 'online',
                'prompt' => 'select_account',
            ]);

            return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
        }

        $query = http_build_query([
            'client_id' => $config['client_id'],
            'redirect_uri' => $callback,
            'response_type' => 'code',
            'scope' => 'email,public_profile',
            'state' => $state,
        ]);

        return redirect()->away('https://www.facebook.com/'.($config['graph_version'] ?? 'v23.0').'/dialog/oauth?'.$query);
    }

    public function callback(Request $request, string $area, string $provider): RedirectResponse
    {
        $this->assertAreaProvider($area, $provider);

        if ($request->filled('error')) {
            return $this->backToLogin($area, 'O acesso social foi cancelado ou recusado.');
        }

        $expectedState = (string) $request->session()->pull("social_oauth.{$area}.{$provider}.state", '');
        if ($expectedState === '' || ! hash_equals($expectedState, (string) $request->query('state', ''))) {
            return $this->backToLogin($area, 'Não foi possível validar esta tentativa de login. Tente novamente.');
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            return $this->backToLogin($area, 'Código de autorização não recebido.');
        }

        try {
            $email = $provider === 'google'
                ? $this->googleEmail($area, $provider, $code)
                : $this->facebookEmail($area, $provider, $code);
        } catch (Throwable $exception) {
            report($exception);
            return $this->backToLogin($area, 'Não foi possível validar sua conta social agora.');
        }

        $user = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])->first();
        if (! $user || ! $this->canEnterArea($user, $area)) {
            return $this->backToLogin($area, 'Esta conta social não possui acesso autorizado a esta área.');
        }

        $guard = $area === 'admin' ? 'admin' : 'web';
        Auth::guard($guard)->login($user, true);
        $request->session()->regenerate();

        return redirect()->to($area === 'admin' ? '/admin' : '/app');
    }

    private function googleEmail(string $area, string $provider, string $code): string
    {
        $config = config('services.social_login.google');
        $callback = route('social-auth.callback', ['area' => $area, 'provider' => $provider]);

        $token = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $callback,
        ])->throw()->json();

        $profile = Http::withToken($token['access_token'])->timeout(15)
            ->get('https://openidconnect.googleapis.com/v1/userinfo')
            ->throw()->json();

        if (empty($profile['email']) || ($profile['email_verified'] ?? false) !== true) {
            throw new \RuntimeException('Google email is missing or not verified.');
        }

        return mb_strtolower((string) $profile['email']);
    }

    private function facebookEmail(string $area, string $provider, string $code): string
    {
        $config = config('services.social_login.facebook');
        $version = $config['graph_version'] ?? 'v23.0';
        $callback = route('social-auth.callback', ['area' => $area, 'provider' => $provider]);

        $token = Http::timeout(15)->get("https://graph.facebook.com/{$version}/oauth/access_token", [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri' => $callback,
            'code' => $code,
        ])->throw()->json();

        $profile = Http::withToken($token['access_token'])->timeout(15)
            ->get("https://graph.facebook.com/{$version}/me", ['fields' => 'id,name,email'])
            ->throw()->json();

        if (empty($profile['email'])) {
            throw new \RuntimeException('Facebook did not return an email address.');
        }

        return mb_strtolower((string) $profile['email']);
    }

    private function assertAreaProvider(string $area, string $provider): void
    {
        abort_unless(in_array($area, ['client', 'admin'], true), 404);
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);
        abort_if($area === 'admin' && $provider === 'facebook', 404);
    }

    private function canEnterArea(User $user, string $area): bool
    {
        if ($user->status !== 'active') {
            return false;
        }

        return $area === 'admin'
            ? in_array($user->role, ['admin', 'operator'], true)
            : $user->role === 'client' && $user->client_id !== null;
    }

    private function backToLogin(string $area, string $message): RedirectResponse
    {
        $path = $area === 'admin' ? '/admin/login' : '/app/login';

        return redirect($path)->with('social_auth_error', $message);
    }
}
