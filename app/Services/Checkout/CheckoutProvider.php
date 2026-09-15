<?php

namespace App\Services\Checkout;

interface CheckoutProvider
{
    public function createCheckout(array $order): string;

    public function verifyPayment(array $reference): array;
}
