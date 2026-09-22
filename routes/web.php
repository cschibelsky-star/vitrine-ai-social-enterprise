<?php

use App\Http\Controllers\SocialAuthController;
use App\Services\Checkout\InfinitePayCheckoutProvider;
use App\Services\Launch\LaunchOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'ok',
            'product' => 'Vitrine AI Social Enterprise',
            'database' => DB::connection()->getDriverName(),
        ]);
    } catch (Throwable $exception) {
        report($exception);

        return response()->json([
            'status' => 'degraded',
            'product' => 'Vitrine AI Social Enterprise',
            'database' => 'unavailable',
        ], 503);
    }
});

Route::get('/service-identity/public-key', function () {
    if (! function_exists('sodium_crypto_sign_seed_keypair')) {
        return response()->json([
            'ok' => false,
            'error' => 'ed25519_unavailable',
        ], 503);
    }

    $projectId = trim((string) config('services.centro_ia.project_id', 'vitrine-ai-social-enterprise'));
    $appKey = (string) config('app.key', '');

    if ($projectId === '' || $appKey === '') {
        return response()->json([
            'ok' => false,
            'error' => 'service_identity_unavailable',
        ], 503);
    }

    $seed = hash('sha256', 'vitrine-service-identity|' . $projectId . '|' . $appKey, true);
    $keyPair = sodium_crypto_sign_seed_keypair($seed);
    $publicKey = sodium_crypto_sign_publickey($keyPair);

    return response()->json([
        'ok' => true,
        'project_id' => $projectId,
        'algorithm' => 'Ed25519',
        'key_id' => hash('sha256', $publicKey),
        'public_key' => base64_encode($publicKey),
    ])->header('Cache-Control', 'public, max-age=3600');
})->name('service-identity.public-key');

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('web')->group(function () {
    Route::get('/auth/{area}/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('area', ['client', 'admin'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social-auth.redirect');

    Route::get('/auth/{area}/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('area', ['client', 'admin'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social-auth.callback');
});

Route::get('/oferta', function () {
    return view('oferta');
})->name('oferta');

Route::get('/checkout/{plan}', function (string $plan, InfinitePayCheckoutProvider $checkout) {
    try {
        $provider = (string) config('services.checkout.provider', 'infinitepay');
        $asaasUrl = trim((string) config('services.asaas.checkout_links.'.$plan, ''));

        if ($asaasUrl !== '') {
            return redirect()->away($asaasUrl);
        }

        if ($provider === 'asaas') {
            throw new RuntimeException('Asaas checkout link is not configured for this plan.');
        }

        return redirect()->away($checkout->createCheckout(['plan' => $plan]));
    } catch (Throwable $exception) {
        report($exception);

        return redirect()->route('oferta')->with('checkout_unavailable', $plan);
    }
})->where('plan', 'essencial|pro|premium')->middleware('throttle:10,1')->name('checkout.start');

Route::post('/lista-vip', function (Request $request, LaunchOrchestrator $orchestrator) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:190'],
        'whatsapp' => ['required', 'string', 'max:30'],
        'company' => ['nullable', 'string', 'max:160'],
        'consent' => ['accepted'],
        'utm_source' => ['nullable', 'string', 'max:120'],
        'utm_medium' => ['nullable', 'string', 'max:120'],
        'utm_campaign' => ['nullable', 'string', 'max:160'],
        'utm_content' => ['nullable', 'string', 'max:160'],
        'plan' => ['nullable', 'in:essencial,pro,premium'],
    ]);

    $source = isset($validated['plan'])
        ? 'oferta_vip_'.$validated['plan']
        : 'landing_lista_vip';

    $orchestrator->captureLead($validated + ['source' => $source]);

    return redirect()->route('oferta')->with('waitlist_success', true);
})->middleware('throttle:10,1')->name('waitlist.store');

if (file_exists(__DIR__.'/infinitepay_hml.php')) {
    require __DIR__.'/infinitepay_hml.php';
}
