<?php

use Illuminate\Support\Facades\Route;

Route::get('/checkout/retorno', fn () => response()->json([
    'status' => 'not_active',
    'provider' => 'infinitepay',
], 503))->name('checkout.return');

Route::post('/api/integrations/infinitepay/events', fn () => response()->json([
    'status' => 'not_active',
    'provider' => 'infinitepay',
], 503))->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->name('infinitepay.events');
