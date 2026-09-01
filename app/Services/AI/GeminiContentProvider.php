<?php

namespace App\Services\AI;

use App\Models\ContentProject;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class GeminiContentProvider
{
    public function generate(ContentProject $project, $brand = null, $template = null): array
    {
        $apiKey = (string) config('services.gemini.key');
        $model = (string) config('services.gemini.model', 'gemini-3.7-flash');
        $timeout = (int) config('services.gemini.timeout', 45);

        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $prompt = $this->buildPrompt($project, $brand, $template);
        $endpoint = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            rawurlencode($model)
        );

        try {
            $response = Http::acceptJson()
                ->withHeaders(['x-goog-api-key' => $apiKey])
                ->timeout($timeout)
                ->retry(2, 400, throw: false)
                ->post($endpoint, [
                    'contents' => [[
                        'role' => 'user',
                        'parts' => [['text' => $prompt]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'responseMimeType' => 'application/json',
                    ],
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Could not connect to Gemini API.', previous: $exception);
        } catch (Throwable $exception) {
            throw new RuntimeException('Unexpected Gemini API error.', previous: $exception);
        }

        if ($response->failed()) {
            try {
                $response->throw();
            } catch (RequestException $exception) {
                throw new RuntimeException(
                    'Gemini API returned HTTP '.$response->status().'.',
                    previous: $exception
                );
            }
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini API returned no usable text candidate.');
        }

        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Gemini API returned invalid JSON content.');
        }

        return $this->normalize($decoded);
    }

    public function model(): string
    {
        return (string) config('services.gemini.model', 'gemini-3.7-flash');
    }

    private function buildPrompt(ContentProject $project, $brand = null, $template = null): string
    {
        $templatePrompt = trim((string) ($template?->prompt_text ?? ''));

        return trim(<<<PROMPT
Você é o motor de conteúdo da área de Marketing da Vitrine IA Pro.
Crie conteúdo em português brasileiro, objetivo, útil, persuasivo sem exageros e coerente com a marca.

CONTEXTO DO PROJETO
- Ideia: {$project->idea}
- Objetivo: {$project->objective}
- Canal: {$project->channel}
- Formato: {$project->format}
- Marca: {$brand?->name}
- Público-alvo: {$brand?->target_audience}
- Tom de voz: {$brand?->tone_of_voice}

TEMPLATE CADASTRADO
{$templatePrompt}

REGRAS
- Não invente fatos, números, depoimentos, preços, prazos ou garantias que não estejam no briefing.
- Adapte linguagem, CTA e estrutura ao canal e ao objetivo.
- Hashtags devem ser relevantes, sem repetição e sem excesso.
- Para slides, entregue uma sequência lógica com capa, desenvolvimento e CTA quando o formato comportar carrossel.
- O score deve ser um número de 0 a 10 refletindo clareza, aderência ao objetivo e adequação ao canal.
- Retorne SOMENTE JSON válido, sem markdown, sem comentários e sem texto fora do JSON.

FORMATO EXATO DA RESPOSTA
{
  "title": "string",
  "caption": "string",
  "cta": "string",
  "hashtags": "#tag1 #tag2",
  "score": 8.5,
  "slides": [
    {
      "slide_number": 1,
      "title": "string",
      "body": "string",
      "visual_instruction": "string",
      "layout_type": "cover"
    }
  ]
}
PROMPT);
    }

    private function normalize(array $data): array
    {
        foreach (['title', 'caption', 'cta', 'hashtags', 'score', 'slides'] as $required) {
            if (! array_key_exists($required, $data)) {
                throw new RuntimeException("Gemini response is missing required field: {$required}.");
            }
        }

        if (! is_array($data['slides'])) {
            throw new RuntimeException('Gemini response field slides must be an array.');
        }

        $slides = [];

        foreach ($data['slides'] as $index => $slide) {
            if (! is_array($slide)) {
                continue;
            }

            $slides[] = [
                'slide_number' => (int) ($slide['slide_number'] ?? ($index + 1)),
                'title' => trim((string) ($slide['title'] ?? '')),
                'body' => trim((string) ($slide['body'] ?? '')),
                'visual_instruction' => trim((string) ($slide['visual_instruction'] ?? '')),
                'layout_type' => trim((string) ($slide['layout_type'] ?? 'content')),
            ];
        }

        if ($slides === []) {
            throw new RuntimeException('Gemini response did not contain valid slides.');
        }

        return [
            'title' => trim((string) $data['title']),
            'caption' => trim((string) $data['caption']),
            'cta' => trim((string) $data['cta']),
            'hashtags' => trim((string) $data['hashtags']),
            'score' => max(0, min(10, (float) $data['score'])),
            'slides' => $slides,
        ];
    }
}
