<?php
declare(strict_types=1);

require __DIR__ . '/../src/CentralAgentes/RelativePerformance.php';

use Vitrine\CentralAgentes\RelativePerformance;

function check(bool $condition, string $name): void {
    if (!$condition) { throw new RuntimeException("FAIL: " . $name); }
    echo "PASS: " . $name . PHP_EOL;
}
check(RelativePerformance::evaluate(301, [100,100,100])['signal'], 'strictly above three');
check(!RelativePerformance::evaluate(300, [100,100,100])['signal'], 'exactly three is not signal');
check(RelativePerformance::evaluate(90, [10,20,30,40])['median'] === 25.0, 'even median');
check(RelativePerformance::evaluate(5, [])['status'] === 'insufficient_data', 'empty history');
check(RelativePerformance::evaluate(5, [0,0])['status'] === 'undefined_baseline', 'zero median');
