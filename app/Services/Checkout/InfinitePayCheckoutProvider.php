<?php

namespace App\Services\Checkout;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class InfinitePayCheckoutProvider implements CheckoutProvider
{
    public function createCheckout(array $order): string
    {
        $handle = (string) config('services.infinitepay.handle');

        if ($handle === '') {
            throw new RuntimeException('InfinitePay handle is not configured.');
        }

        $plan = (string) ($order['plan'] ?? '');
        $planConfig = config('services.checkout.plans.'.$plan);

        if (! is_array($planConfig)) {
            throw new RuntimeException('Invalid checkout plan.');
        }

        $orderNsu = (string) ($order['order_nsu'] ?? Str::ulid());

        $payload = [
            'handle' => ltrim($handle, '$'),
            'order_nsu' => $orderNsu,
            'items' => [[
                'quantity' => 1,
                'price' => (int) $planConfig['price'],
                'description' => (string) $planConfig['description'],
            ]],
        ];

        if ($redirectUrl = config('services.infinitepay.redirect_url')) {
            $payload['redirect_url'] = $redirectUrl;
        }

        if ($webhookUrl = config('services.infinitepay.webhook_url')) {
            $payload['webhook_url'] = $webhookUrl;
        }

        if (! empty($order['customer'])) {
            $payload['customer'] = $order['customer'];
        }

        $response = Http::acceptJson()
            ->asJson()
            ->timeout((int) config('services.infinitepay.timeout', 15))
            ->post(
                (string) config('services.infinitepay.links_url', 'https://api.checkout.infinitepay.io/links'),
                $payload,
            )
            ->throw();

        $url = $response->json('url');

        if (! is_string($url) || $url === '') {
            throw new RuntimeException('InfinitePay did not return a checkout URL.');
        }

        return $url;
    }

    public function verifyPayment(array $reference): array
    {
        $handle = (string) config('services.infinitepay.handle');

        if ($handle === '') {
            throw new RuntimeException('InfinitePay handle is not configured.');
        }

        $payload = [
            'handle' => ltrim($handle, '$'),
            'order_nsu' => (string) ($reference['order_nsu'] ?? ''),
            'transaction_nsu' => (string) ($reference['transaction_nsu'] ?? ''),
            'slug' => (string) ($reference['slug'] ?? ''),
        ];

        if (in_array('', $payload, true)) {
            throw new RuntimeException('Incomplete InfinitePay payment reference.');
        }

        return Http::acceptJson()
            ->asJson()
            ->timeout((int) config('services.infinitepay.timeout', 15))
            ->post(
                (string) config('services.infinitepay.payment_check_url', 'https://api.checkout.infinitepay.io/payment_check'),
                $payload,
            )
            ->throw()
            ->json();
    }
}
