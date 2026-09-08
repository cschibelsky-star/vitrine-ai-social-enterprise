<?php

namespace App\Services\AI;

use App\DTO\AI\GeneratedContent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonException;
use UnexpectedValueException;

final class ResponseValidator
{
    /**
     * @param array<string, mixed>|string $response
     */
    public function validate(array|string $response): GeneratedContent
    {
        $payload = is_string($response)
            ? $this->decode($response)
            : $response;

        $normalized = [
            'title' => $this->requiredString($payload, 'title'),
            'caption' => $this->requiredString($payload, 'caption'),
            'cta' => $this->requiredString($payload, 'cta'),
            'hashtags' => $this->normalizeHashtags((string) Arr::get($payload, 'hashtags', '')),
            'score' => $this->normalizeScore(Arr::get($payload, 'score', 0)),
            'slides' => $this->normalizeSlides(Arr::get($payload, 'slides', [])),
        ];

        return GeneratedContent::fromArray($normalized);
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string $response): array
    {
        $response = trim($response);

        if (Str::startsWith($response, '```')) {
            $response = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $response) ?: $response;
        }

        try {
            $decoded = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new UnexpectedValueException(
                'A resposta do provedor de IA não contém JSON válido.',
                previous: $exception,
            );
        }

        if (! is_array($decoded)) {
            throw new UnexpectedValueException('A resposta do provedor de IA deve ser um objeto JSON.');
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function requiredString(array $payload, string $key): string
    {
        $value = trim((string) Arr::get($payload, $key, ''));

        if ($value === '') {
            throw new UnexpectedValueException("O campo obrigatório '{$key}' não foi retornado pela IA.");
        }

        return $value;
    }

    private function normalizeHashtags(string $hashtags): string
    {
        $items = preg_split('/[\s,]+/', trim($hashtags)) ?: [];

        $normalized = collect($items)
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->map(fn (string $item): string => Str::startsWith($item, '#') ? $item : '#'.$item)
            ->map(fn (string $item): string => preg_replace('/[^#\pL\pN_]/u', '', $item) ?: '')
            ->filter(fn (string $item): bool => mb_strlen($item) > 1)
            ->unique(fn (string $item): string => mb_strtolower($item))
            ->values()
            ->all();

        return implode(' ', $normalized);
    }

    private function normalizeScore(mixed $score): float
    {
        if (! is_numeric($score)) {
            return 0.0;
        }

        return round(min(10, max(0, (float) $score)), 2);
    }

    /**
     * @return array<int, array{slide_number:int,title:string,body:string,visual_instruction:string,layout_type:string}>
     */
    private function normalizeSlides(mixed $slides): array
    {
        if (! is_array($slides)) {
            return [];
        }

        $allowedLayouts = ['cover', 'content', 'cta'];
        $normalized = [];

        foreach (array_values($slides) as $index => $slide) {
            if (! is_array($slide)) {
                continue;
            }

            $title = trim((string) Arr::get($slide, 'title', ''));
            $body = trim((string) Arr::get($slide, 'body', ''));

            if ($title === '' && $body === '') {
                continue;
            }

            $layout = trim((string) Arr::get($slide, 'layout_type', 'content'));

            if (! in_array($layout, $allowedLayouts, true)) {
                $layout = $index === 0 ? 'cover' : 'content';
            }

            $normalized[] = [
                'slide_number' => count($normalized) + 1,
                'title' => $title ?: 'Conteúdo',
                'body' => $body,
                'visual_instruction' => trim((string) Arr::get($slide, 'visual_instruction', '')),
                'layout_type' => $layout,
            ];
        }

        if (count($normalized) > 10) {
            $normalized = array_slice($normalized, 0, 10);
        }

        return $normalized;
    }
}
