<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiContentService
{
    /**
     * @return array{caption: string, cta: string, hashtags: string, model: string, estimated_cost: float|null}
     */
    public function generate(array $prompt): array
    {
        $apiKey = (string) config('services.openai.api_key');
        $model = (string) config('services.openai.model');

        if ($apiKey === '' || $model === '') {
            throw new RuntimeException('A integração OpenAI não está configurada.');
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(90)
                ->retry(2, 750)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => $model,
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => [
                                ['type' => 'input_text', 'text' => $prompt['system']],
                            ],
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'input_text', 'text' => $prompt['user']],
                            ],
                        ],
                    ],
                    'text' => [
                        'format' => [
                            'type' => 'json_schema',
                            'name' => 'social_content',
                            'strict' => true,
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'caption' => ['type' => 'string'],
                                    'cta' => ['type' => 'string'],
                                    'hashtags' => ['type' => 'string'],
                                ],
                                'required' => ['caption', 'cta', 'hashtags'],
                                'additionalProperties' => false,
                            ],
                        ],
                    ],
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Não foi possível conectar ao provedor de IA.', previous: $exception);
        }

        if ($response->failed()) {
            throw new RuntimeException(
                $response->json('error.message') ?: 'A geração de conteúdo falhou no provedor de IA.'
            );
        }

        $outputText = $response->json('output_text');

        if (! is_string($outputText) || trim($outputText) === '') {
            $outputText = data_get($response->json(), 'output.0.content.0.text');
        }

        if (! is_string($outputText) || trim($outputText) === '') {
            throw new RuntimeException('O provedor de IA retornou uma resposta vazia.');
        }

        $content = json_decode($outputText, true, flags: JSON_THROW_ON_ERROR);

        return [
            'caption' => trim((string) ($content['caption'] ?? '')),
            'cta' => trim((string) ($content['cta'] ?? '')),
            'hashtags' => trim((string) ($content['hashtags'] ?? '')),
            'model' => $model,
            'estimated_cost' => null,
        ];
    }
}
