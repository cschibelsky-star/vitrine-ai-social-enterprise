<?php

namespace App\DTO\AI;

use InvalidArgumentException;

final readonly class GeneratedContent
{
    /**
     * @param array<int, array{slide_number:int,title:string,body:string,visual_instruction:string,layout_type:string}> $slides
     */
    public function __construct(
        public string $title,
        public string $caption,
        public string $cta,
        public string $hashtags,
        public float $score,
        public array $slides = [],
    ) {
        if ($this->title === '' || $this->caption === '' || $this->cta === '') {
            throw new InvalidArgumentException('Generated content requires title, caption and CTA.');
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: trim((string) ($data['title'] ?? '')),
            caption: trim((string) ($data['caption'] ?? '')),
            cta: trim((string) ($data['cta'] ?? '')),
            hashtags: trim((string) ($data['hashtags'] ?? '')),
            score: (float) ($data['score'] ?? 0),
            slides: array_values($data['slides'] ?? []),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'caption' => $this->caption,
            'cta' => $this->cta,
            'hashtags' => $this->hashtags,
            'score' => $this->score,
            'slides' => $this->slides,
        ];
    }
}