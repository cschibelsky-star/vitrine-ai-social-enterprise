<?php

namespace App\Data\AI;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class GeneratedContent
{
    public function __construct(
        public string $title,
        public string $hook,
        public string $caption,
        public string $cta,
        public array $hashtags,
        public array $keywords,
        public string $imagePrompt,
        public string $imageStyle,
        public string $bestTime,
    ) {
    }

    public static function fromArray(array $data): self
    {
        foreach (['title', 'hook', 'caption', 'cta', 'hashtags', 'keywords', 'image_prompt', 'image_style', 'best_time'] as $field) {
            if (! Arr::has($data, $field)) {
                throw new InvalidArgumentException("Campo obrigatório ausente: {$field}");
            }
        }

        if (! is_array($data['hashtags']) || ! is_array($data['keywords'])) {
            throw new InvalidArgumentException('Hashtags e keywords devem ser arrays.');
        }

        return new self(
            title: self::cleanText($data['title']),
            hook: self::cleanText($data['hook']),
            caption: trim((string) $data['caption']),
            cta: self::cleanText($data['cta']),
            hashtags: self::normalizeHashtags($data['hashtags']),
            keywords: self::normalizeList($data['keywords']),
            imagePrompt: trim((string) $data['image_prompt']),
            imageStyle: self::cleanText($data['image_style']),
            bestTime: self::cleanText($data['best_time']),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'hook' => $this->hook,
            'caption' => $this->caption,
            'cta' => $this->cta,
            'hashtags' => $this->hashtags,
            'keywords' => $this->keywords,
            'image_prompt' => $this->imagePrompt,
            'image_style' => $this->imageStyle,
            'best_time' => $this->bestTime,
        ];
    }

    private static function cleanText(mixed $value): string
    {
        return Str::squish((string) $value);
    }

    private static function normalizeHashtags(array $hashtags): array
    {
        return collect($hashtags)
            ->map(fn (mixed $hashtag): string => '#' . ltrim(Str::of((string) $hashtag)->trim()->replace(' ', '')->toString(), '#'))
            ->filter(fn (string $hashtag): bool => $hashtag !== '#')
            ->unique(fn (string $hashtag): string => Str::lower($hashtag))
            ->take(15)
            ->values()
            ->all();
    }

    private static function normalizeList(array $items): array
    {
        return collect($items)
            ->map(fn (mixed $item): string => self::cleanText($item))
            ->filter()
            ->unique(fn (string $item): string => Str::lower($item))
            ->take(12)
            ->values()
            ->all();
    }
}
