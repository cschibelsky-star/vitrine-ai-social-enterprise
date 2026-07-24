<?php

namespace App\Services;

use App\Models\AiCreditTransaction;
use App\Models\AiCreditWallet;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreditService
{
    public function walletFor(Client $client): AiCreditWallet
    {
        return $client->aiCreditWallet()->firstOrCreate([], [
            'text_balance' => 0,
            'image_balance' => 0,
            'text_monthly_limit' => 0,
            'image_monthly_limit' => 0,
        ]);
    }

    public function canConsume(Client $client, string $type, int $amount = 1): bool
    {
        $wallet = $this->walletFor($client);
        $column = $this->balanceColumn($type);

        return $amount > 0 && $wallet->{$column} >= $amount;
    }

    public function reserve(Client $client, string $type, string $operation, string $idempotencyKey, int $amount = 1, array $metadata = []): AiCreditTransaction
    {
        if ($amount < 1) {
            throw ValidationException::withMessages(['amount' => 'A quantidade de créditos deve ser maior que zero.']);
        }

        return DB::transaction(function () use ($client, $type, $operation, $idempotencyKey, $amount, $metadata) {
            $existing = AiCreditTransaction::query()->where('idempotency_key', $idempotencyKey)->first();

            if ($existing) {
                return $existing;
            }

            $wallet = $this->walletFor($client);
            $wallet = AiCreditWallet::query()->whereKey($wallet->getKey())->lockForUpdate()->firstOrFail();
            $column = $this->balanceColumn($type);

            if ($wallet->{$column} < $amount) {
                throw ValidationException::withMessages(['credits' => 'Saldo de créditos insuficiente.']);
            }

            $wallet->decrement($column, $amount);

            return $wallet->transactions()->create([
                'type' => $type,
                'operation' => $operation,
                'amount' => -$amount,
                'idempotency_key' => $idempotencyKey,
                'status' => 'reserved',
                'metadata' => $metadata,
            ]);
        });
    }

    public function confirm(AiCreditTransaction $transaction, ?string $provider = null, ?string $model = null, ?float $estimatedCost = null): AiCreditTransaction
    {
        $transaction->update([
            'status' => 'confirmed',
            'provider' => $provider,
            'model' => $model,
            'estimated_cost' => $estimatedCost,
        ]);

        return $transaction->refresh();
    }

    public function rollback(AiCreditTransaction $transaction, array $metadata = []): AiCreditTransaction
    {
        return DB::transaction(function () use ($transaction, $metadata) {
            $transaction = AiCreditTransaction::query()->whereKey($transaction->getKey())->lockForUpdate()->firstOrFail();

            if ($transaction->status !== 'reserved') {
                return $transaction;
            }

            $wallet = AiCreditWallet::query()->whereKey($transaction->ai_credit_wallet_id)->lockForUpdate()->firstOrFail();
            $wallet->increment($this->balanceColumn($transaction->type), abs($transaction->amount));
            $transaction->update([
                'status' => 'refunded',
                'metadata' => array_merge($transaction->metadata ?? [], $metadata),
            ]);

            return $transaction->refresh();
        });
    }

    private function balanceColumn(string $type): string
    {
        return match ($type) {
            'text' => 'text_balance',
            'image' => 'image_balance',
            default => throw ValidationException::withMessages(['type' => 'Tipo de crédito inválido.']),
        };
    }
}
