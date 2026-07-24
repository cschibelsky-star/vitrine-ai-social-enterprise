<?php

namespace App\Services;

use App\Models\EditorialPlanning;

class SocialContentPromptBuilder
{
    public function caption(EditorialPlanning $planning): array
    {
        $planning->loadMissing('client');

        return [
            'system' => implode("\n", [
                'Você é um estrategista de conteúdo para redes sociais.',
                'Escreva em português do Brasil, com linguagem natural, clara e comercial.',
                'Não invente informações, promoções, preços, endereços ou contatos.',
                'Entregue somente JSON válido, sem markdown e sem comentários adicionais.',
                'Use exatamente as chaves: caption, cta, hashtags.',
                'hashtags deve ser uma string com hashtags separadas por espaço.',
            ]),
            'user' => implode("\n", array_filter([
                'Crie um conteúdo social com os dados abaixo:',
                'Cliente: '.($planning->client?->name ?? 'Não informado'),
                'Tema: '.$planning->theme,
                'Objetivo: '.$planning->objective,
                'Canal: '.$planning->channel,
                'Formato: '.$planning->format,
                $planning->notes ? 'Observações: '.$planning->notes : null,
                'A legenda deve ser adequada ao canal, ter abertura forte, desenvolvimento objetivo e chamada para ação coerente.',
                'Gere entre 5 e 12 hashtags relevantes e não repetitivas.',
            ])),
        ];
    }

    public function cacheKey(EditorialPlanning $planning, string $operation = 'caption'): string
    {
        return 'social-content:'.hash('sha256', json_encode([
            'operation' => $operation,
            'client_id' => $planning->client_id,
            'theme' => $planning->theme,
            'objective' => $planning->objective,
            'channel' => $planning->channel,
            'format' => $planning->format,
            'notes' => $planning->notes,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
