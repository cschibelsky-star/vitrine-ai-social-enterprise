<x-filament-widgets::widget>
    <div class="studio-hero">
        <div class="studio-hero-copy">
            <span class="studio-eyebrow">Vitrine IA Studio Pro</span>
            <h2>Olá! Que conteúdo vamos criar hoje?</h2>
            <p>Escolha uma opção abaixo. A VIA cria o restante para você.</p>
        </div>

        <a href="{{ \App\Filament\Resources\ContentProjects\ContentProjectResource::getUrl('create') }}" class="studio-primary-action">
            <span>✨</span>
            Criar outro conteúdo
        </a>
    </div>

    <div class="studio-format-grid">
        @php
            $items = [
                ['label' => 'Fazer uma promoção', 'description' => 'Divulgue uma oferta ou condição especial', 'icon' => '🏷️', 'category' => 'promocao'],
                ['label' => 'Apresentar produto ou serviço', 'description' => 'Mostre o que sua empresa oferece', 'icon' => '🛍️', 'category' => 'produto_servico'],
                ['label' => 'Divulgar um evento', 'description' => 'Convide o público e informe os detalhes', 'icon' => '🎉', 'category' => 'evento'],
                ['label' => 'Compartilhar uma dica', 'description' => 'Ajude seu público com uma orientação útil', 'icon' => '💡', 'category' => 'dica'],
                ['label' => 'Publicar uma novidade', 'description' => 'Conte algo novo sobre sua empresa', 'icon' => '📰', 'category' => 'novidade'],
                ['label' => 'Homenagear uma data especial', 'description' => 'Crie conteúdo para uma ocasião importante', 'icon' => '❤️', 'category' => 'data_especial'],
                ['label' => 'Fazer um comunicado', 'description' => 'Transmita uma informação com clareza', 'icon' => '📢', 'category' => 'comunicado'],
                ['label' => 'Criar outro conteúdo', 'description' => 'Comece com uma ideia diferente', 'icon' => '✨', 'category' => 'outro'],
            ];
        @endphp

        @foreach ($items as $item)
            <a href="{{ \App\Filament\Resources\ContentProjects\ContentProjectResource::getUrl('create', ['category' => $item['category']]) }}" class="studio-format-card">
                <span class="studio-format-icon">{{ $item['icon'] }}</span>
                <strong>{{ $item['label'] }}</strong>
                <small>{{ $item['description'] }}</small>
            </a>
        @endforeach
    </div>
</x-filament-widgets::widget>

<style>
    .studio-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 30px;
        border-radius: 24px;
        color: #fff;
        background:
            radial-gradient(circle at 10% 10%, rgba(255,255,255,.22), transparent 28%),
            linear-gradient(135deg, #0f172a 0%, #075985 50%, #7c3aed 100%);
        box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
    }

    .studio-eyebrow {
        display: inline-block;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: .15em;
        font-size: .72rem;
        font-weight: 800;
        color: #67e8f9;
    }

    .studio-hero h2 {
        margin: 0;
        font-size: clamp(1.8rem, 3vw, 3rem);
        line-height: 1.05;
        letter-spacing: -.055em;
        font-weight: 900;
    }

    .studio-hero p {
        max-width: 620px;
        margin: 14px 0 0;
        color: rgba(255,255,255,.78);
        line-height: 1.6;
    }

    .studio-primary-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-width: 230px;
        padding: 16px 22px;
        border-radius: 16px;
        background: #fff;
        color: #0f172a;
        font-weight: 800;
        box-shadow: 0 15px 35px rgba(0,0,0,.18);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .studio-primary-action:hover,
    .studio-format-card:hover {
        transform: translateY(-3px);
    }

    .studio-format-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-top: 18px;
    }

    .studio-format-card {
        display: flex;
        min-height: 170px;
        flex-direction: column;
        justify-content: center;
        padding: 22px;
        border: 1px solid rgba(15,23,42,.07);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 14px 40px rgba(15,23,42,.07);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .studio-format-card strong {
        margin-top: 14px;
        color: #0f172a;
        font-size: 1rem;
        line-height: 1.35;
    }

    .studio-format-card small {
        margin-top: 5px;
        color: #64748b;
        line-height: 1.45;
    }

    .studio-format-icon {
        font-size: 1.8rem;
    }

    @media (max-width: 900px) {
        .studio-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .studio-primary-action {
            width: 100%;
        }

        .studio-format-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 520px) {
        .studio-format-grid {
            grid-template-columns: 1fr;
        }

        .studio-format-card {
            min-height: 140px;
        }
    }
</style>
