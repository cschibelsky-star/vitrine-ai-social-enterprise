<?php

use App\Models\Brand;
use App\Models\Client;
use App\Models\ClientBalance;
use App\Models\ClientSubscription;
use App\Models\User;
use App\Services\Checkout\InfinitePayCheckoutProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$parseInfinitePayOrder = static function (string $orderNsu): ?array {
    if (! preg_match('/^vsm-(essencial|pro|premium)-(\d+)-([a-f0-9]{16})$/', $orderNsu, $matches)) {
        return null;
    }

    $plan = $matches[1];
    $leadId = (int) $matches[2];
    $lead = DB::table('waitlist_leads')->where('id', $leadId)->first();

    if (! $lead) {
        return null;
    }

    $expected = substr(hash('sha256', $plan.'|'.$leadId.'|'.$lead->email), 0, 16);

    if (! hash_equals($expected, $matches[3])) {
        return null;
    }

    return ['plan' => $plan, 'lead' => $lead];
};

$activateInfinitePayPurchase = static function (
    string $orderNsu,
    string $transactionNsu,
    string $slug,
    InfinitePayCheckoutProvider $checkout,
) use ($parseInfinitePayOrder): array {
    $order = $parseInfinitePayOrder($orderNsu);

    if (! $order) {
        return ['ok' => false, 'message' => 'Pedido não encontrado'];
    }

    $payment = $checkout->verifyPayment([
        'order_nsu' => $orderNsu,
        'transaction_nsu' => $transactionNsu,
        'slug' => $slug,
    ]);

    $expectedAmount = (int) config('services.checkout.plans.'.$order['plan'].'.price', 0);

    if (! ($payment['success'] ?? false) || ! ($payment['paid'] ?? false)) {
        return ['ok' => false, 'message' => 'Pagamento ainda não confirmado'];
    }

    if ((int) ($payment['amount'] ?? 0) !== $expectedAmount || $expectedAmount <= 0) {
        return ['ok' => false, 'message' => 'Valor do pagamento não corresponde ao plano'];
    }

    $lead = $order['lead'];
    $plan = $order['plan'];
    $quota = match ($plan) {
        'essencial' => 10,
        'pro' => 25,
        'premium' => 50,
    };

    $client = DB::transaction(function () use ($lead, $plan, $quota) {
        $client = Client::query()->where('contact_email', $lead->email)->first();

        if (! $client) {
            $client = Client::create([
                'name' => $lead->company ?: $lead->name,
                'contact_name' => $lead->name,
                'contact_email' => $lead->email,
                'contact_phone' => $lead->whatsapp,
                'status' => 'active',
            ]);
        } else {
            $client->forceFill([
                'contact_name' => $lead->name,
                'contact_phone' => $lead->whatsapp,
                'status' => 'active',
            ])->save();
        }

        $brandName = $lead->company ?: $client->name;
        $brand = Brand::query()->firstOrCreate(
            ['client_id' => $client->id, 'name' => $brandName],
            [
                'tone_of_voice' => 'Profissional, claro, moderno e acessível.',
                'target_audience' => 'Público da marca',
                'status' => 'active',
            ],
        );

        $subscription = ClientSubscription::query()->firstOrNew([
            'client_id' => $client->id,
            'source' => 'infinitepay',
        ]);
        $subscription->forceFill([
            'plan_code' => $plan,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
        ])->save();

        $periodStart = now()->startOfMonth();
        $periodEnd = now()->addMonth()->startOfMonth();
        $balance = ClientBalance::query()->firstOrNew([
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'period_start' => $periodStart,
        ]);
        $consumed = (float) ($balance->consumed ?? 0);
        $granted = max((float) ($balance->granted ?? 0), (float) $quota);
        $balance->forceFill([
            'granted' => $granted,
            'consumed' => $consumed,
            'available' => max(0, $granted - $consumed),
            'period_end' => $periodEnd,
        ])->save();

        $user = User::query()->where('email', $lead->email)->first();
        if (! $user) {
            User::create([
                'name' => $lead->name,
                'email' => $lead->email,
                'password' => Hash::make(Str::random(48)),
                'client_id' => $client->id,
                'brand_id' => $brand->id,
                'role' => 'client',
                'status' => 'active',
            ]);
        } elseif ($user->role === 'client') {
            $user->forceFill([
                'client_id' => $client->id,
                'brand_id' => $brand->id,
                'status' => 'active',
            ])->save();
        }

        return $client;
    });

    return [
        'ok' => true,
        'client_id' => $client->id,
        'plan' => $plan,
    ];
};

Route::get('/checkout/retorno', function (Request $request, InfinitePayCheckoutProvider $checkout) use ($activateInfinitePayPurchase) {
    $validated = $request->validate([
        'order_nsu' => ['required', 'string', 'max:190'],
        'transaction_nsu' => ['required', 'string', 'max:190'],
        'slug' => ['required', 'string', 'max:190'],
    ]);

    try {
        $result = $activateInfinitePayPurchase(
            $validated['order_nsu'],
            $validated['transaction_nsu'],
            $validated['slug'],
            $checkout,
        );

        if (! $result['ok']) {
            return redirect()->route('oferta')->with('payment_error', $result['message']);
        }

        return redirect('/app/login')->with('payment_success', $result['plan']);
    } catch (Throwable $exception) {
        report($exception);

        return redirect()->route('oferta')->with('payment_error', 'Não foi possível confirmar o pagamento agora.');
    }
})->middleware('throttle:20,1')->name('checkout.return');

Route::post('/api/integrations/infinitepay/events', function (Request $request, InfinitePayCheckoutProvider $checkout) use ($activateInfinitePayPurchase) {
    $validated = $request->validate([
        'order_nsu' => ['required', 'string', 'max:190'],
        'transaction_nsu' => ['required', 'string', 'max:190'],
        'invoice_slug' => ['required', 'string', 'max:190'],
    ]);

    try {
        $result = $activateInfinitePayPurchase(
            $validated['order_nsu'],
            $validated['transaction_nsu'],
            $validated['invoice_slug'],
            $checkout,
        );

        if (! $result['ok']) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json(['success' => true, 'message' => null]);
    } catch (Throwable $exception) {
        report($exception);

        return response()->json(['success' => false, 'message' => 'Falha na confirmação do pagamento'], 400);
    }
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->middleware('throttle:60,1')
    ->name('infinitepay.events');
