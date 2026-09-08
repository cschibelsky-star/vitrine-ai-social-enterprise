<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientBalance;
use App\Services\ClientConsumptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ClientConsumptionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_debits_active_balance_and_writes_ledger(): void
    {
        $client = Client::query()->create([
            'name' => 'Cliente Consumo',
            'status' => 'active',
        ]);

        ClientBalance::query()->create([
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'granted' => 5,
            'consumed' => 0,
            'available' => 5,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        $ledger = app(ClientConsumptionService::class)->consume(
            client: $client,
            balanceType: 'content_credit',
            amount: 1,
            unit: 'content',
            description: 'Teste de consumo',
            metadata: ['operation' => 'test_generation'],
            operation: static function (): void {
                // Successful protected operation.
            },
        );

        $balance = ClientBalance::query()->where('client_id', $client->id)->firstOrFail();

        $this->assertSame('4.00', $balance->available);
        $this->assertSame('1.00', $balance->consumed);
        $this->assertSame('1.00', $ledger->amount);
        $this->assertSame('5.00', $ledger->balance_before);
        $this->assertSame('4.00', $ledger->balance_after);
        $this->assertSame('content', $ledger->unit);
        $this->assertSame('test_generation', $ledger->metadata['operation']);
    }

    public function test_it_rolls_back_balance_and_ledger_when_protected_operation_fails(): void
    {
        $client = Client::query()->create([
            'name' => 'Cliente Rollback',
            'status' => 'active',
        ]);

        ClientBalance::query()->create([
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'granted' => 3,
            'consumed' => 0,
            'available' => 3,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        try {
            app(ClientConsumptionService::class)->consume(
                client: $client,
                balanceType: 'content_credit',
                amount: 1,
                unit: 'content',
                operation: static function (): void {
                    throw new RuntimeException('Falha simulada do provedor.');
                },
            );

            $this->fail('A exceção simulada deveria ter sido propagada.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Falha simulada do provedor.', $exception->getMessage());
        }

        $balance = ClientBalance::query()->where('client_id', $client->id)->firstOrFail();

        $this->assertSame('3.00', $balance->available);
        $this->assertSame('0.00', $balance->consumed);
        $this->assertDatabaseCount('consumption_ledgers', 0);
    }
}
