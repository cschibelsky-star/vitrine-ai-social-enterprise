<?php
declare(strict_types=1);

namespace Vitrine\CentralAgentes;

use InvalidArgumentException;

final class RelativePerformance
{
    /**
     * Compara visualizações com a mediana do mesmo criador.
     * Retorna sinal apenas quando a razão é estritamente maior que 3.
     * Não calcula probabilidade de viralização.
     */
    public static function evaluate(int $views, array $creatorHistory): array
    {
        if ($views < 0) {
            throw new InvalidArgumentException('views must be nonnegative');
        }
        if ($creatorHistory === []) {
            return ['status' => 'insufficient_data', 'ratio' => null, 'signal' => false];
        }
        foreach ($creatorHistory as $value) {
            if (!is_int($value) || $value < 0) {
                throw new InvalidArgumentException('history must contain nonnegative integers');
            }
        }
        sort($creatorHistory, SORT_NUMERIC);
        $n = count($creatorHistory);
        $median = $n % 2
            ? $creatorHistory[intdiv($n, 2)]
            : ($creatorHistory[$n / 2 - 1] + $creatorHistory[$n / 2]) / 2;
        if ($median <= 0) {
            return ['status' => 'undefined_baseline', 'median' => $median, 'ratio' => null, 'signal' => false];
        }
        $ratio = $views / $median;
        return ['status' => 'ok', 'median' => $median, 'ratio' => $ratio, 'signal' => $ratio > 3];
    }
}
