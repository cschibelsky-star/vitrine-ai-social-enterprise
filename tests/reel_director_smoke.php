<?php

require __DIR__.'/../app/Services/AI/ReelDirectorService.php';

use App\Services\AI\ReelDirectorService;

$director = new ReelDirectorService();
$assert = static function (bool $condition, string $description): void {
    if (! $condition) {
        throw new RuntimeException('FAIL: '.$description);
    }
    echo "PASS: {$description}\n";
};

$preview = $director->preview([
    'idea' => 'combos de sexta-feira da hamburgueria',
    'caption' => 'Confira os ingredientes e escolha o seu pedido. Temos opções para diferentes gostos.',
    'cta' => 'Conheça o cardápio.',
    'target_seconds' => 30,
]);
$assert(count($preview['hooks']) === 3, 'retorna três ganchos');
$assert(count(array_unique(array_column($preview['hooks'], 'spoken'))) === 3, 'ganchos distintos');
$assert($preview['human_approval_required'] === true && $preview['status'] === 'review_required', 'não aprova automaticamente');
$assert($preview['duration_seconds'] > 0 && $preview['duration_seconds'] <= 30, 'estima duração');
$assert(count($preview['beats']) >= 3, 'estrutura beats de hook, corpo e CTA');

$invalid = $director->preview(['idea' => 'promoção']);
$assert($invalid['status'] === 'briefing_required' && $invalid['hooks'] === [], 'briefing insuficiente é bloqueado');

$forbidden = $director->preview([
    'idea' => 'combos especiais desta semana',
    'caption' => 'Venha conhecer nosso melhor lanche.',
    'cta' => 'Conheça.',
    'forbidden_words' => ['melhor'],
]);
$assert(str_contains(implode(' ', $forbidden['warnings']), 'melhor'), 'respeita termos proibidos pelo Brand Kit');
$assert($forbidden['human_approval_required'] === true, 'mantém gate de aprovação');

$long = $director->preview(['idea' => str_repeat('combo artesanal ', 25), 'caption' => str_repeat('Confira os detalhes do prato. ', 40), 'target_seconds' => 15]);
$assert(count($long['warnings']) >= 3, 'alerta sobre excessos de duração e de texto');
