<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-medium uppercase text-gray-500">Cliente</p>
                <p class="mt-1 font-semibold">{{ $this->record->client?->name ?? 'Não informado' }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-medium uppercase text-gray-500">Canal</p>
                <p class="mt-1 font-semibold">{{ ucfirst($this->record->channel) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-medium uppercase text-gray-500">Formato</p>
                <p class="mt-1 font-semibold">{{ ucfirst($this->record->format) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-medium uppercase text-gray-500">Status</p>
                <p class="mt-1 font-semibold">{{ str($this->record->status)->replace('_', ' ')->title() }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase text-gray-500">Planejamento</p>
                    <h2 class="mt-1 text-xl font-bold">{{ $this->record->theme }}</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $this->record->objective }}</p>
                    <p class="mt-3 text-xs text-gray-500">Previsto para {{ optional($this->record->planned_for)->format('d/m/Y') }}</p>
                </div>

                @if (! $this->record->contentProject)
                    <x-filament::button wire:click="startProduction" wire:loading.attr="disabled" wire:target="startProduction" icon="heroicon-o-play">
                        <span wire:loading.remove wire:target="startProduction">Iniciar produção</span>
                        <span wire:loading wire:target="startProduction">Iniciando...</span>
                    </x-filament::button>
                @else
                    <span class="rounded-full bg-success-50 px-3 py-1 text-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                        Projeto vinculado
                    </span>
                @endif
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h3 class="font-semibold">Biblioteca inteligente</h3>
                <p class="mt-2 text-sm text-gray-500">Conteúdos reutilizáveis, modelos, CTAs e hashtags aparecerão aqui antes de qualquer uso de IA.</p>
                <div class="mt-4 rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-white/20">
                    Nenhum conteúdo semelhante carregado.
                </div>
            </div>

            <div class="space-y-4 xl:col-span-2">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold">Editor de conteúdo</h3>
                        <span class="text-xs text-gray-500">{{ mb_strlen($caption ?? '') }} caracteres</span>
                    </div>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Legenda</label>
                            <textarea wire:model.live.debounce.400ms="caption" rows="10" class="w-full rounded-lg border-gray-300 shadow-sm dark:border-white/10 dark:bg-gray-950"></textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Chamada para ação</label>
                            <input wire:model.live.debounce.400ms="cta" type="text" class="w-full rounded-lg border-gray-300 shadow-sm dark:border-white/10 dark:bg-gray-950">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Hashtags</label>
                            <textarea wire:model.live.debounce.400ms="hashtags" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm dark:border-white/10 dark:bg-gray-950"></textarea>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <x-filament::button wire:click="saveDraft" wire:loading.attr="disabled" wire:target="saveDraft" icon="heroicon-o-check" :disabled="! $this->record->contentProject">
                            <span wire:loading.remove wire:target="saveDraft">Salvar rascunho</span>
                            <span wire:loading wire:target="saveDraft">Salvando...</span>
                        </x-filament::button>

                        <x-filament::button
                            wire:click="generateWithAI"
                            wire:loading.attr="disabled"
                            wire:target="generateWithAI"
                            icon="heroicon-o-sparkles"
                        >
                            <span wire:loading.remove wire:target="generateWithAI">Gerar com IA</span>
                            <span wire:loading wire:target="generateWithAI">Gerando conteúdo...</span>
                        </x-filament::button>

                        <x-filament::button color="gray" icon="heroicon-o-photo" disabled>
                            Gerar imagem
                        </x-filament::button>
                    </div>

                    <p wire:loading wire:target="generateWithAI" class="mt-3 text-sm text-primary-600 dark:text-primary-400">
                        Consultando a biblioteca, verificando créditos e produzindo o conteúdo.
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <h3 class="font-semibold">Pré-visualização</h3>
                    <div class="mt-4 rounded-xl border border-gray-200 p-4 dark:border-white/10">
                        <p class="text-sm font-semibold">{{ $this->record->client?->name ?? 'Perfil social' }}</p>
                        <p class="mt-3 whitespace-pre-line text-sm">{{ $caption ?: 'A prévia da legenda aparecerá aqui.' }}</p>
                        @if ($cta)
                            <p class="mt-3 text-sm font-medium">{{ $cta }}</p>
                        @endif
                        @if ($hashtags)
                            <p class="mt-3 text-sm text-primary-600 dark:text-primary-400">{{ $hashtags }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
