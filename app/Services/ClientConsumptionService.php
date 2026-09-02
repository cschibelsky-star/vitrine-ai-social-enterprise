<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientBalance;
use App\Models\ConsumptionLedger;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientConsumptionService
{
    private const MAX_AMOUNT = 999999999999.99;

    private const MAX_UNIT_PRICE = 9999999999.9999;

    private const MAX_CHARGED_AMOUNT = 999999999999.99;

    public function consume(
        Client $client,
        string $balanceType,
        float $amount,
        string $unit,
        ?float $unitPrice = null,
        ?Model $reference = null,
        ?string $description = null,
        ?User $actor = null,
        array $metadata = [],
        ?Closure $operation = null,
    ): ConsumptionLedger {
        if (! is_finite($amount)) {
            throw ValidationException::withMessages([
                'amount' => 'O consumo deve ser um valor finito.',
            ]);
        }

        if ($unitPrice !== null && (! is_finite($unitPrice) || $unitPrice < 0)) {
            throw ValidationException::withMessages([
                'unit_price' => 'O preço unitário deve ser finito e não negativo.',
            ]);
        }

        $amount = round($amount, 2);
        $unitPrice = $unitPrice !== null ? round($unitPrice, 4) : null;

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'O consumo deve ser maior que zero após normalização para 2 casas decimais.',
            ]);
        }

        if ($amount > self::MAX_AMOUNT) {
            throw ValidationException::withMessages([
                'amount' => 'O consumo excede o limite suportado para armazenamento.',
            ]);
        }

        if ($unitPrice !== null && $unitPrice > self::MAX_UNIT_PRICE) {
            throw ValidationException::withMessages([
                'unit_price' => 'O preço unitário excede o limite suportado para armazenamento.',
            ]);
        }

        $chargedAmount = $unitPrice !== null ? round($amount * $unitPrice, 2) : null;

        if ($chargedAmount !== null && (! is_finite($chargedAmount) || $chargedAmount > self::MAX_CHARGED_AMOUNT)) {
            throw ValidationException::withMessages([
                'charged_amount' => 'O valor cobrado excede o limite suportado para armazenamento.',
            ]);
        }

        if ($reference) {
            $referenceClientId = $reference->getAttribute('client_id');

            if ($referenceClientId !== null && (int) $referenceClientId !== (int) $client->getKey()) {
                throw ValidationException::withMessages([
                    'reference' => 'A referência informada não pertence ao cliente debitado.',
                ]);
            }
        }

        if ($actor && $actor->role === 'client' && (int) $actor->client_id !== (int) $client->getKey()) {
            throw ValidationException::withMessages([
                'actor' => 'O usuário não pode registrar consumo para outro cliente.',
            ]);
        }

        return DB::transaction(function () use (
            $client,
            $balanceType,
            $amount,
            $unit,
            $unitPrice,
            $chargedAmount,
            $reference,
            $description,
            $actor,
            $metadata,
            $operation,
        ) {
            $balance = ClientBalance::query()
                ->where('client_id', $client->getKey())
                ->where('balance_type', $balanceType)
                ->where(function ($query) {
                    $query->whereNull('period_start')->orWhere('period_start', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('period_end')->orWhere('period_end', '>=', now());
                })
                ->orderByDesc('period_start')
                ->lockForUpdate()
                ->first();

            if (! $balance) {
                throw ValidationException::withMessages([
                    'balance' => "Nenhum saldo ativo foi encontrado para {$balanceType}.",
                ]);
            }

            $before = round((float) $balance->available, 2);
            if ($before < $amount) {
                throw ValidationException::withMessages([
                    'balance' => "Saldo insuficiente para {$balanceType}.",
                ]);
            }

            if ($operation) {
                $operation();
            }

            $after = round($before - $amount, 2);

            $balance->forceFill([
                'consumed' => round((float) $balance->consumed + $amount, 2),
                'available' => $after,
            ])->save();

            $ledger = new ConsumptionLedger([
                'client_id' => $client->getKey(),
                'brand_id' => $reference?->getAttribute('brand_id'),
                'balance_type' => $balanceType,
                'movement_type' => 'consumption',
                'amount' => $amount,
                'unit' => $unit,
                'unit_price' => $unitPrice,
                'charged_amount' => $chargedAmount,
                'description' => $description,
                'balance_before' => $before,
                'balance_after' => $after,
                'created_by' => $actor?->getKey(),
                'metadata' => $metadata,
            ]);

            if ($reference) {
                $ledger->reference()->associate($reference);
            }

            $ledger->save();

            return $ledger->fresh();
        });
    }
}
