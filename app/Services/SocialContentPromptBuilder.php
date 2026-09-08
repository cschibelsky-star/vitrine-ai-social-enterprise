<?php

namespace App\Services;

use App\Models\EditorialPlanning;

class SocialContentPromptBuilder
{
    public function caption(EditorialPlanning $planning): array
    {
        $planning->loadMissing('client.activeBrand');

        $client = $planning->client;
        $brand = $client?->activeBrand;

        return [
            'system' => implode("\n", array_filter([
                'Você é um estrategista de conteúdo para redes sociais.',
                'Escreva em português do Brasil, com linguagem natural, clara e adequada ao posicionamento da marca.',
                'Não invente informações, promoções, preços, endereços ou contatos.',
                'Respeite obrigatoriamente as diretrizes do Brand Kit quando elas forem informadas.',
                'Nunca utilize palavras ou expressões listadas como proibidas.',
                'Entregue somente JSON válido, sem markdown e sem comentários adicionais.',
                'Use exatamente as chaves: caption, cta, hashtags.',
                'hashtags deve ser uma string com hashtags separadas por espaço.',
            ])),
            'user' => implode("\n", array_filter([
                'Crie um conteúdo social com os dados abaixo:',
                'Cliente: '.($client?->name ?? 'Não informado'),
                $brand?->name ? 'Marca: '.$brand->name : null,
                $brand?->tone_of_voice ? 'Tom de voz: '.$brand->tone_of_voice : null,
                $brand?->target_audience ? 'Público-alvo: '.$brand->target_audience : null,
                filled($brand?->preferred_words) ? 'Palavras preferidas: '.implode(', ', $brand->preferred_words) : null,
                filled($brand?->forbidden_words) ? 'Palavras proibidas: '.implode(', ', $brand->forbidden_words) : null,
                $brand?->notes ? 'Diretrizes adicionais da marca: '.$brand->notes : null,
                'Tema: '.$planning->theme,
                'Objetivo: '.$planning->objective,
                'Canal: '.$planning->channel,
                'Formato: '.$planning->format,
                $planning->notes ? 'Observações do planejamento: '.$planning->notes : null,
                'A legenda deve ser adequada ao canal, ter abertura forte, desenvolvimento objetivo e chamada para ação coerente.',
                'Gere entre 5 e 12 hashtags relevantes e não repetitivas.',
            ])),
        ];
    }

    public function cacheKey(EditorialPlanning $planning, string $operation = 'caption'): string
    {
        $planning->loadMissing('client.activeBrand');
        $brand = $planning->client?->activeBrand;

        return 'social-content:'.hash('sha256', json_encode([
            'operation' => $operation,
            'client_id' => $planning->client_id,
            'brand_id' => $brand?->getKey(),
            'brand_updated_at' => $brand?->updated_at?->toJSON(),
            'theme' => $planning->theme,
            'objective' => $planning->objective,
            'channel' => $planning->channel,
            'format' => $planning->format,
            'notes' => $planning->notes,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
