<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientBalance;
use App\Models\ConsumptionLedger;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientConsumptionService
{
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
    ): ConsumptionLedger {
        $amount = round($amount, 2);
        $unitPrice = $unitPrice !== null ? round($unitPrice, 4) : null;

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'O consumo deve ser maior que zero após normalização para 2 casas decimais.',
            ]);
        }

        return DB::transaction(function () use (
            $client,
            $balanceType,
            $amount,
            $unit,
            $unitPrice,
            $reference,
            $description,
            $actor,
            $metadata,
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

            $after = round($before - $amount, 2);
            $chargedAmount = $unitPrice !== null ? round($amount * $unitPrice, 2) : null;

            $balance->forceFill([
                'consumed' => round((float) $balance->consumed + $amount, 2),
                'available' => $after,
            ])->save();

            $ledger = new ConsumptionLedger([
                'client_id' => $client->getKey(),
                'brand_id' => $reference?->brand_id ?? null,
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
