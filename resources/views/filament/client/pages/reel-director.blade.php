<x-filament-panels::page>
    <div class="space-y-5">
        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <h2 class="text-lg font-semibold">Análise editorial de Reels</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300">Prévia local sem custo de IA. As pontuações são heurísticas de revisão, não previsão de alcance. Nenhuma ação aqui aprova, agenda ou publica conteúdos.</p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="font-semibold">Selecione um Reel do seu cliente</h3>
            @forelse ($projects as $item)
                <a href="{{ \App\Filament\Client\Pages\ReelDirector::getUrl(['project_id' => $item->id]) }}" class="mt-2 block rounded-lg border p-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-800" @if ($selected?->id === $item->id) aria-current="page" @endif>
                    {{ $item->title ?: $item->idea }} <span class="text-gray-500">(#{{ $item->id }})</span>
                </a>
            @empty
                <p class="mt-2 text-sm">Ainda não há projetos de Reels. Crie um Reel em Conteúdos e Criativos para começar.</p>
            @endforelse
        </div>

        @if ($preview)
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <h3 class="font-semibold">Três aberturas para revisão</h3>
                <p class="text-sm text-gray-500">A geração automática usa duas fórmulas de referência e uma variação própria. As 26 fórmulas originais estão disponíveis para consulta editorial, mas não são preenchidas automaticamente sem dados verificados.</p>
                @foreach ($preview['hooks'] as $hook)
                    <div class="mt-3 rounded-lg border p-3">
                        <p class="text-xs text-gray-500">{{ $hook['formula'] }} · indicador heurístico {{ $hook['heuristic_score'] }}/100 · aprox. {{ $hook['estimated_seconds'] }}s</p>
                        <p class="font-medium">{{ $hook['spoken'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <h3 class="font-semibold">Catálogo de {{ count($preview['catalog']) }} fórmulas de referência</h3>
                <p class="text-sm text-gray-500">Fonte: Jake Schincariol, MIT. Algumas fórmulas dependem de provas, métricas ou perguntas reais e exigem briefing complementar.</p>
                <details class="mt-3"><summary class="cursor-pointer font-medium">Consultar fórmulas e cuidados de uso</summary>
                    @foreach ($preview['catalog'] as $formula)
                        <p class="mt-2 text-sm"><strong>#{{ $formula['id'] }} — {{ $formula['name'] }}</strong>: {{ $formula['best_for'] }} Atenção: {{ $formula['trap'] }}</p>
                    @endforeach
                </details>
            </div>

            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <h3 class="font-semibold">Prévia de tempo — {{ $preview['duration_seconds'] }}s (meta {{ $preview['target_seconds'] }}s)</h3>
                @foreach ($preview['beats'] as $beat)
                    <p class="mt-2 text-sm"><span class="font-semibold">{{ $beat['start_seconds'] }}s · {{ $beat['type'] }} · {{ $beat['duration_seconds'] }}s:</span> {{ $beat['text'] }}</p>
                @endforeach
                <h4 class="mt-4 font-semibold">Pendências antes da aprovação humana</h4>
                @foreach ($preview['warnings'] as $warning)
                    <p class="mt-2 text-sm">• {{ $warning }}</p>
                @endforeach
                <p class="mt-4 text-sm font-semibold">Status: revisão obrigatória. O roteiro não foi aprovado.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
