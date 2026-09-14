<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oferta', function () {
    return view('oferta');
})->name('oferta');

Route::get('/checkout/{plan}', function (string $plan) {
    $url = config('services.asaas.checkout_links.'.$plan);

    if (! $url) {
        return redirect()->route('oferta')->with('checkout_unavailable', $plan);
    }

    return redirect()->away($url);
})->where('plan', 'essencial|pro|premium')->name('checkout.start');

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
