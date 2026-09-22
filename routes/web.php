<?php

use App\Http\Controllers\SocialAuthController;
use App\Services\Checkout\InfinitePayCheckoutProvider;
use App\Services\Launch\LaunchOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
    return view('oferta', ['offerMode' => 'vip']);
})->name('oferta');

Route::get('/oferta/regular/{lead}', function (int $lead, Request $request) {
    $record = DB::table('waitlist_leads')->where('id', $lead)->first();

    if (! $record) {
        abort(404);
    }

    $token = (string) $request->query('token', '');
    $expected = substr(hash('sha256', 'regular|'.$record->id.'|'.$record->email), 0, 24);

    if ($token === '' || ! hash_equals($expected, $token)) {
        abort(404);
    }

    $clientId = DB::table('clients')->where('contact_email', $record->email)->value('id');
    if ($clientId && DB::table('client_subscriptions')->where('client_id', $clientId)->where('status', 'active')->exists()) {
        return redirect('/app/login');
    }

    $source = (string) $record->source;
    $alreadyRegular = $source === 'vip_expired_regular_offer'
        || str_starts_with($source, 'checkout_started_regular_');

    if (! $alreadyRegular) {
        if (! str_starts_with($source, 'checkout_started_vip_')) {
            abort(404);
        }

        $recoveryHours = max(1, (int) config('services.checkout.vip_recovery_after_hours', 24));
        $eligibleAt = \Illuminate\Support\Carbon::parse($record->updated_at)->addHours($recoveryHours);

        if (now()->lt($eligibleAt)) {
            return redirect()->route('oferta')->with('vip_still_available', true);
        }

        DB::table('waitlist_leads')->where('id', $record->id)->update([
            'source' => 'vip_expired_regular_offer',
            'updated_at' => now(),
        ]);
    }

    return view('oferta', [
        'offerMode' => 'regular',
        'recoveryLead' => $record,
        'recoveryToken' => $expected,
    ]);
})->middleware('throttle:20,1')->name('offer.regular');

Route::get('/checkout/{plan}', function (string $plan, Request $request, InfinitePayCheckoutProvider $checkout) {
    $leadId = (int) $request->query('lead', 0);
    $lead = $leadId > 0 ? DB::table('waitlist_leads')->where('id', $leadId)->first() : null;

    if (! $lead) {
        return redirect()->route('oferta')->with('checkout_requires_lead', $plan);
    }

    try {
        $fingerprint = substr(hash('sha256', $plan.'|'.$lead->id.'|'.$lead->email), 0, 16);
        $orderNsu = 'vsm-'.$plan.'-'.$lead->id.'-'.$fingerprint;
        $checkoutUrl = $checkout->createCheckout([
            'plan' => $plan,
            'billing' => 'vip',
            'order_nsu' => $orderNsu,
            'customer' => [
                'name' => $lead->name,
                'email' => $lead->email,
            ],
        ]);

        DB::table('waitlist_leads')->where('id', $lead->id)->update([
            'source' => 'checkout_started_vip_'.$plan,
            'updated_at' => now(),
        ]);

        $recoveryHours = max(1, (int) config('services.checkout.vip_recovery_after_hours', 24));
        dispatch(function () use ($leadId, $plan) {
            $record = DB::table('waitlist_leads')->where('id', $leadId)->first();
            if (! $record || (string) $record->source !== 'checkout_started_vip_'.$plan) {
                return;
            }

            $clientId = DB::table('clients')->where('contact_email', $record->email)->value('id');
            if ($clientId && DB::table('client_subscriptions')->where('client_id', $clientId)->where('status', 'active')->exists()) {
                return;
            }

            $token = substr(hash('sha256', 'regular|'.$record->id.'|'.$record->email), 0, 24);
            $url = route('offer.regular', ['lead' => $record->id, 'token' => $token]);

            Mail::raw(
                "Olá {$record->name},\n\nVimos que você conheceu a condição VIP da Vitrine Social Mídia, mas não concluiu a contratação.\n\nSe preferir começar com mais flexibilidade, agora você pode escolher um dos nossos planos mensais normais.\n\nVer planos mensais: {$url}\n\nVitrine IA Pro",
                function ($message) use ($record) {
                    $message->to($record->email, $record->name)
                        ->subject('Vitrine Social Mídia: conheça os planos mensais');
                }
            );

            DB::table('waitlist_leads')->where('id', $record->id)->update([
                'source' => 'vip_expired_regular_offer',
                'updated_at' => now(),
            ]);
        })->delay(now()->addHours($recoveryHours));

        return redirect()->away($checkoutUrl);
    } catch (Throwable $exception) {
        report($exception);

        return redirect()->route('oferta')->with('checkout_unavailable', $plan);
    }
})->where('plan', 'essencial|pro|premium')->middleware('throttle:10,1')->name('checkout.start');

Route::get('/checkout/mensal/{plan}', function (string $plan, Request $request, InfinitePayCheckoutProvider $checkout) {
    $leadId = (int) $request->query('lead', 0);
    $lead = $leadId > 0 ? DB::table('waitlist_leads')->where('id', $leadId)->first() : null;

    if (! $lead) {
        abort(404);
    }

    $token = (string) $request->query('token', '');
    $expected = substr(hash('sha256', 'regular|'.$lead->id.'|'.$lead->email), 0, 24);
    if ($token === '' || ! hash_equals($expected, $token)) {
        abort(404);
    }

    try {
        $fingerprint = substr(hash('sha256', 'monthly|'.$plan.'|'.$lead->id.'|'.$lead->email), 0, 16);
        $orderNsu = 'vsm-monthly-'.$plan.'-'.$lead->id.'-'.$fingerprint;
        $checkoutUrl = $checkout->createCheckout([
            'plan' => $plan,
            'billing' => 'regular',
            'order_nsu' => $orderNsu,
            'customer' => [
                'name' => $lead->name,
                'email' => $lead->email,
            ],
        ]);

        DB::table('waitlist_leads')->where('id', $lead->id)->update([
            'source' => 'checkout_started_regular_'.$plan,
            'updated_at' => now(),
        ]);

        return redirect()->away($checkoutUrl);
    } catch (Throwable $exception) {
        report($exception);

        return redirect()->route('offer.regular', ['lead' => $lead->id, 'token' => $expected])
            ->with('checkout_unavailable', $plan);
    }
})->where('plan', 'essencial|pro|premium')->middleware('throttle:10,1')->name('checkout.regular');

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

    $record = $orchestrator->captureLead($validated + ['source' => $source]);

    try {
        $salesAddress = (string) config('mail.from.address');
        if ($salesAddress !== '') {
            $planLabel = $validated['plan'] ?? 'não selecionado';
            $company = $record->company ?: 'não informada';
            Mail::raw(
                "Novo lead - Vitrine Social Mídia\n\nNome: {$record->name}\nE-mail: {$record->email}\nWhatsApp: {$record->whatsapp}\nEmpresa: {$company}\nPlano: {$planLabel}\nOrigem: {$source}",
                function ($message) use ($salesAddress, $record) {
                    $message->to($salesAddress)
                        ->replyTo($record->email, $record->name)
                        ->subject('Novo lead: Vitrine Social Mídia');
                }
            );
        }
    } catch (Throwable $exception) {
        report($exception);
    }

    if (! empty($validated['plan'])) {
        return redirect()->route('checkout.start', [
            'plan' => $validated['plan'],
            'lead' => $record->id,
        ]);
    }

    return redirect()->route('oferta')->with('waitlist_success', true);
})->middleware('throttle:10,1')->name('waitlist.store');

if (file_exists(__DIR__.'/infinitepay_hml.php')) {
    require __DIR__.'/infinitepay_hml.php';
}
