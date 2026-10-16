<?php
namespace Tests\Fixtures;

use Illuminate\Contracts\Queue\ShouldQueue;

class PublicationRuntimeProbe implements ShouldQueue
{
    public int $tries = 2;
    public function __construct(public string $path) {}
    public function handle(): void
    {
        $previous = is_file($this->path) ? json_decode(file_get_contents($this->path), true) : [];
        $attempt = (int) ($previous['attempts'] ?? 0) + 1;
        file_put_contents($this->path, json_encode(['attempts' => $attempt, 'completed' => $attempt === 2], JSON_THROW_ON_ERROR), LOCK_EX);
        if ($attempt === 1) { throw new \RuntimeException('Synthetic validation retry'); }
    }
}
