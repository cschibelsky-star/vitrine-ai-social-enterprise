<?php

namespace App\Services;

use App\Models\Client;
use App\Models\EditorialPlanning;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CampaignPlanner
{
    private const TOPICS = [
        'restaurante' => [
            ['theme' => 'Prato ou combo em destaque', 'format' => 'post', 'objective' => 'conversion'],
            ['theme' => 'Bastidores da cozinha', 'format' => 'reel', 'objective' => 'engagement'],
            ['theme' => 'Delivery e facilidade de pedido', 'format' => 'story', 'objective' => 'conversion'],
            ['theme' => 'Ingrediente ou diferencial', 'format' => 'carousel', 'objective' => 'authority'],
            ['theme' => 'Cliente satisfeito', 'format' => 'post', 'objective' => 'trust'],
        ],
        'clinica' => [
            ['theme' => 'Dica de prevenção e cuidado', 'format' => 'carousel', 'objective' => 'education'],
            ['theme' => 'Especialidade em destaque', 'format' => 'post', 'objective' => 'authority'],
            ['theme' => 'Conheça a equipe', 'format' => 'reel', 'objective' => 'trust'],
            ['theme' => 'Pergunta frequente', 'format' => 'story', 'objective' => 'education'],
        ],
        'loja' => [
            ['theme' => 'Produto em destaque', 'format' => 'post', 'objective' => 'conversion'],
            ['theme' => 'Novidade ou lançamento', 'format' => 'reel', 'objective' => 'engagement'],
            ['theme' => 'Oferta da semana', 'format' => 'story', 'objective' => 'conversion'],
            ['theme' => 'Como usar ou combinar', 'format' => 'carousel', 'objective' => 'education'],
        ],
        'default' => [
            ['theme' => 'Oferta em destaque', 'format' => 'post', 'objective' => 'conversion'],
            ['theme' => 'Bastidores da marca', 'format' => 'reel', 'objective' => 'engagement'],
            ['theme' => 'Dica útil para o público', 'format' => 'carousel', 'objective' => 'authority'],
            ['theme' => 'Depoimento ou prova social', 'format' => 'story', 'objective' => 'trust'],
        ],
    ];

    public function generate(Client $client, string $startDate, string $endDate, int $quantity, string $channel = 'instagram'): Collection
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages(['quantity' => 'A quantidade deve ser maior que zero.']);
        }

        $start = CarbonImmutable::parse($startDate)->startOfDay();
        $end = CarbonImmutable::parse($endDate)->startOfDay();

        if ($end->lt($start)) {
            throw ValidationException::withMessages(['period' => 'A data final deve ser igual ou posterior à data inicial.']);
        }

        $days = max(1, $start->diffInDays($end));
        $interval = $quantity === 1 ? 0 : $days / ($quantity - 1);
        $topics = $this->topicsFor($client->segment);

        return DB::transaction(function () use ($client, $start, $end, $quantity, $channel, $interval, $topics) {
            return collect(range(0, $quantity - 1))->map(function (int $index) use ($client, $start, $end, $channel, $interval, $topics) {
                $topic = $topics[$index % count($topics)];
                $date = $start->addDays((int) round($interval * $index));

                if ($date->gt($end)) {
                    $date = $end;
                }

                return EditorialPlanning::create([
                    'client_id' => $client->getKey(),
                    'theme' => $topic['theme'],
                    'objective' => $topic['objective'],
                    'channel' => $channel,
                    'format' => $topic['format'],
                    'planned_for' => $date->toDateString(),
                    'priority' => 3,
                    'status' => 'planned',
                ]);
            });
        });
    }

    private function topicsFor(?string $segment): array
    {
        $normalized = mb_strtolower(trim((string) $segment));

        foreach (self::TOPICS as $key => $topics) {
            if ($key !== 'default' && str_contains($normalized, $key)) {
                return $topics;
            }
        }

        return self::TOPICS['default'];
    }
}
