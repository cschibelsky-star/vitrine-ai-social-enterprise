<?php

namespace Database\Seeders;

use App\Models\PromptTemplate;
use Illuminate\Database\Seeder;

class PromptTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['Venda', 'Oferta direta', 'sales', 'post_portrait', 'Crie uma legenda de venda destacando benefício, urgência e CTA direto.'],
            ['Educacional', 'Carrossel educativo', 'education', 'carousel_portrait', 'Crie um carrossel educativo em 3 a 5 slides com explicação clara e CTA para salvar.'],
            ['Autoridade', 'Dica de especialista', 'authority', 'post_portrait', 'Crie um conteúdo que posicione a marca como especialista no assunto.'],
            ['Engajamento', 'Pergunta para comentários', 'engagement', 'post_portrait', 'Crie uma legenda curta com pergunta simples para gerar comentários.'],
            ['Comunidade', 'Mensagem de pertencimento', 'community', 'stories', 'Crie uma sequência de stories com linguagem próxima e acolhedora.'],
        ];

        foreach ($templates as [$category, $name, $objective, $format, $prompt]) {
            PromptTemplate::updateOrCreate(
                ['name' => $name],
                [
                    'category' => $category,
                    'objective' => $objective,
                    'format' => $format,
                    'prompt_text' => $prompt,
                    'variables' => ['brand_name', 'target_audience', 'idea'],
                    'is_active' => true,
                ]
            );
        }
    }
}
