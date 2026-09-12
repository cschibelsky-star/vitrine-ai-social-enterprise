<?php

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

Route::post('/lista-vip', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:190'],
        'whatsapp' => ['required', 'string', 'max:30'],
        'company' => ['nullable', 'string', 'max:160'],
        'consent' => ['accepted'],
    ]);

    DB::table('waitlist_leads')->insert([
        'name' => $validated['name'],
        'email' => mb_strtolower($validated['email']),
        'whatsapp' => $validated['whatsapp'],
        'company' => $validated['company'] ?? null,
        'source' => 'landing_lista_vip',
        'consent' => true,
        'joined_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('oferta')->with('waitlist_success', true);
})->middleware('throttle:10,1')->name('waitlist.store');
