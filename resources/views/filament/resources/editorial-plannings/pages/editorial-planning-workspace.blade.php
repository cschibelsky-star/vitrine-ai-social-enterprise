<x-filament-panels::page>
    @php
        $brand = $this->record->client?->activeBrand;
        $wallet = $this->record->client?->aiCreditWallet;
        $stageLabels = [
            'ready' => 'Pronto para gerar',
            'searching_library' => 'Consultando biblioteca',
            'checking_cache' => 'Verificando cache',
            'generating_ai' => 'Gerando com IA',
            'completed' => 'Conteúdo concluído',
            'failed' => 'Falha na geração',
        ];
        $sourceLabels = [
            'library' => 'Biblioteca Inteligente',
            'cache' => 'Cache inteligente',
            'ai' => 'Inteligência artificial',
            'manual' => 'Edição manual',
        ];
    @endphp

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900 xl:col-span-2">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Conteúdo em produção</p>
                <h2 class="mt-1 text-lg font-bold">{{ $this->record->theme }}</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $this->record->objective ?: 'Objetivo ainda não informado.' }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-medium uppercase text-gray-500">Canal</p>
                <p class="mt-1 font-semibold">{{ str($this->record->channel)->replace('_', ' ')->title() }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ str($this->record->format)->replace('_', ' ')->title() }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-medium uppercase text-gray-500">Créditos de texto</p>
                <p class="mt-1 text-2xl font-bold">{{ number_format((int) ($wallet?->text_balance ?? 0), 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-500">Imagens: {{ number_format((int) ($wallet?->image_balance ?? 0), 0, ',', '.') }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-medium uppercase text-gray-500">Status</p>
                <p class="mt-1 font-semibold">{{ $stageLabels[$generationStage] ?? 'Pronto' }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ optional($this->record->planned_for)->format('d/m/Y') ?: 'Sem data definida' }}</p>
            </div>
        </div>

        @if (! $this->record->contentProject)
            <div class="flex flex-col gap-4 rounded-xl border border-primary-200 bg-primary-50 p-5 dark:border-primary-500/20 dark:bg-primary-500/10 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-semibold text-primary-900 dark:text-primary-100">Planejamento pronto para produção</h3>
                    <p class="mt-1 text-sm text-primary-700 dark:text-primary-300">Inicie o projeto para liberar o editor, o salvamento e a geração assistida.</p>
                </div>
                <x-filament::button wire:click="startProduction" wire:loading.attr="disabled" wire:target="startProduction" icon="heroicon-o-play">
                    <span wire:loading.remove wire:target="startProduction">Iniciar produção</span>
                    <span wire:loading wire:target="startProduction">Iniciando...</span>
                </x-filament::button>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="font-semibold">Brand Kit</h3>
                        @if ($brand)
                            <span class="rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">Ativo</span>
                        @endif
                    </div>

                    @if ($brand)
                        <div class="mt-4 space-y-4 text-sm">
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-500">Marca</p>
                                <p class="mt-1 font-semibold">{{ $brand->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-500">Tom de voz</p>
                                <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $brand->tone_of_voice ?: 'Não definido' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-500">Público</p>
                                <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $brand->target_audience ?: 'Não definido' }}</p>
                            </div>

                            @if (filled($brand->preferred_words))
                                <div>
                                    <p class="text-xs font-medium uppercase text-gray-500">Palavras preferidas</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($brand->preferred_words as $word)
                                            <span class="rounded-full bg-success-50 px-2.5 py-1 text-xs text-success-700 dark:bg-success-500/10 dark:text-success-400">{{ $word }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (filled($brand->forbidden_words))
                                <div>
                                    <p class="text-xs font-medium uppercase text-gray-500">Evitar</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($brand->forbidden_words as $word)
                                            <span class="rounded-full bg-danger-50 px-2.5 py-1 text-xs text-danger-700 dark:bg-danger-500/10 dark:text-danger-400">{{ $word }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-4 rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500 dark:border-white/20">
                            Nenhum Brand Kit ativo. A IA usará apenas os dados do planejamento.
                        </div>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <h3 class="font-semibold">Biblioteca Inteligente</h3>
                    <p class="mt-1 text-sm text-gray-500">Referências semelhantes encontradas para este conteúdo.</p>

                    <div class="mt-4 space-y-3">
                        @forelse ($libraryMatches as $match)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-semibold">{{ $match['title'] }}</p>
                                    <span class="rounded-full bg-primary-50 px-2 py-1 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">{{ $match['similarity'] }}%</span>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    {{ str($match['channel'])->replace('_', ' ')->title() }}
                                    @if ($match['format'])
                                        · {{ str($match['format'])->replace('_', ' ')->title() }}
                                    @endif
                                </p>
                                <p class="mt-2 text-xs leading-5 text-gray-600 dark:text-gray-300">{{ $match['caption'] }}</p>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500 dark:border-white/20">
                                Ainda não há conteúdo semelhante para sugerir.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-6">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-semibold">Editor de conteúdo</h3>
                            <p class="mt-1 text-sm text-gray-500">Gere, revise e finalize a publicação.</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ mb_strlen($caption ?? '') }} caracteres</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Legenda</label>
                            <textarea wire:model.live.debounce.400ms="caption" rows="12" class="w-full rounded-lg border-gray-300 shadow-sm dark:border-white/10 dark:bg-gray-950" placeholder="A legenda gerada aparecerá aqui."></textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Chamada para ação</label>
                            <input wire:model.live.debounce.400ms="cta" type="text" class="w-full rounded-lg border-gray-300 shadow-sm dark:border-white/10 dark:bg-gray-950" placeholder="Ex.: Fale conosco pelo WhatsApp">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Hashtags</label>
                            <textarea wire:model.live.debounce.400ms="hashtags" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm dark:border-white/10 dark:bg-gray-950" placeholder="#marca #conteudo #negocios"></textarea>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <x-filament::button
                            wire:click="generateWithAI"
                            wire:loading.attr="disabled"
                            wire:target="generateWithAI"
                            icon="heroicon-o-sparkles"
                        >
                            <span wire:loading.remove wire:target="generateWithAI">Gerar com IA</span>
                            <span wire:loading wire:target="generateWithAI">Gerando conteúdo...</span>
                        </x-filament::button>

                        <x-filament::button wire:click="saveDraft" wire:loading.attr="disabled" wire:target="saveDraft" icon="heroicon-o-check" color="gray" :disabled="! $this->record->contentProject">
                            <span wire:loading.remove wire:target="saveDraft">Salvar rascunho</span>
                            <span wire:loading wire:target="saveDraft">Salvando...</span>
                        </x-filament::button>

                        <x-filament::button color="gray" icon="heroicon-o-photo" disabled>
                            Gerar imagem
                        </x-filament::button>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="font-semibold">Pré-visualização</h3>
                        <span class="text-xs font-medium text-gray-500">{{ str($this->record->channel)->replace('_', ' ')->title() }}</span>
                    </div>

                    <div class="mx-auto mt-5 max-w-lg overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-950">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-3 dark:border-white/10">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-500/20 dark:text-primary-300">
                                {{ str($brand?->name ?? $this->record->client?->name ?? 'VP')->substr(0, 2)->upper() }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold">{{ $brand?->name ?? $this->record->client?->name ?? 'Perfil social' }}</p>
                                <p class="text-xs text-gray-500">Publicação em preparação</p>
                            </div>
                        </div>

                        <div class="flex aspect-square items-center justify-center bg-gray-100 px-6 text-center text-sm text-gray-500 dark:bg-gray-900">
                            A imagem gerada será exibida aqui na próxima sprint.
                        </div>

                        <div class="p-4">
                            <p class="whitespace-pre-line text-sm leading-6">{{ $caption ?: 'A prévia da legenda aparecerá aqui.' }}</p>
                            @if ($cta)
                                <p class="mt-3 text-sm font-semibold">{{ $cta }}</p>
                            @endif
                            @if ($hashtags)
                                <p class="mt-3 text-sm text-primary-600 dark:text-primary-400">{{ $hashtags }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <h3 class="font-semibold">Fluxo da geração</h3>
                    <div class="mt-4 space-y-3 text-sm">
                        @foreach ([
                            ['key' => 'searching_library', 'label' => 'Biblioteca Inteligente'],
                            ['key' => 'checking_cache', 'label' => 'Cache inteligente'],
                            ['key' => 'generating_ai', 'label' => 'Geração com IA'],
                            ['key' => 'completed', 'label' => 'Conteúdo salvo'],
                        ] as $step)
                            @php
                                $stageOrder = ['ready' => 0, 'searching_library' => 1, 'checking_cache' => 2, 'generating_ai' => 3, 'completed' => 4, 'failed' => -1];
                                $stepOrder = $stageOrder[$step['key']] ?? 0;
                                $currentOrder = $stageOrder[$generationStage] ?? 0;
                                $isComplete = $generationStage === 'completed' || $currentOrder > $stepOrder;
                                $isCurrent = $generationStage === $step['key'];
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $isComplete ? 'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-400' : ($isCurrent ? 'bg-primary-100 text-primary-700 dark:bg-primary-500/20 dark:text-primary-400' : 'bg-gray-100 text-gray-400 dark:bg-white/10') }}">
                                    @if ($isComplete)
                                        ✓
                                    @elseif ($isCurrent)
                                        •
                                    @else
                                        {{ $loop->iteration }}
                                    @endif
                                </span>
                                <span class="{{ $isCurrent ? 'font-semibold text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-300' }}">{{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($generationMessage)
                        <div class="mt-5 rounded-lg {{ $generationStage === 'failed' ? 'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400' : 'bg-gray-50 text-gray-700 dark:bg-white/5 dark:text-gray-300' }} p-3 text-sm">
                            {{ $generationMessage }}
                        </div>
                    @endif

                    @if ($generationSource)
                        <div class="mt-3 text-xs text-gray-500">
                            Origem: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $sourceLabels[$generationSource] ?? $generationSource }}</span>
                        </div>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <h3 class="font-semibold">Resumo do planejamento</h3>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase text-gray-500">Cliente</dt>
                            <dd class="mt-1 font-medium">{{ $this->record->client?->name ?? 'Não informado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase text-gray-500">Notas</dt>
                            <dd class="mt-1 text-gray-600 dark:text-gray-300">{{ $this->record->notes ?: 'Sem observações adicionais.' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase text-gray-500">Projeto</dt>
                            <dd class="mt-1">
                                @if ($this->record->contentProject)
                                    <span class="font-medium text-success-700 dark:text-success-400">Vinculado e pronto</span>
                                @else
                                    <span class="font-medium text-warning-700 dark:text-warning-400">Aguardando início</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
