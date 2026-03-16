{{--
    Rendered via PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE.
    $livewire is passed explicitly from the plugin's boot() closure.
    Only rendered on pages that use HasAdvancedTables.
--}}
@if ($livewire->showFavoritesBar ?? false)

    <div class="filament-advanced-tables-bar w-full border-b border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-900/40">
        <div class="flex flex-wrap items-center gap-1.5 px-4 py-2">

            {{-- All Records pill --}}
            <button
                wire:click="clearActiveView"
                type="button"
                @class([
                    'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition-colors',
                    'bg-primary-600 text-white shadow-sm'
                        => ! $livewire->activePresetViewKey && ! $livewire->activeUserViewId,
                    'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-gray-700'
                        => $livewire->activePresetViewKey || $livewire->activeUserViewId,
                ])
            >
                <x-heroicon-o-table-cells class="w-3.5 h-3.5" />
                All Records
            </button>

            {{-- Preset view pills --}}
            @foreach ($livewire->getPresetViewsCollection() as $preset)
                <button
                    wire:click="applyPresetView('{{ $preset->key }}')"
                    type="button"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition-colors',
                        'bg-primary-600 text-white shadow-sm'
                            => $livewire->activePresetViewKey === $preset->key,
                        'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-gray-700'
                            => $livewire->activePresetViewKey !== $preset->key,
                    ])
                >
                    @if ($preset->icon)
                        <x-dynamic-component :component="$preset->icon" class="w-3.5 h-3.5" />
                    @endif
                    {{ $preset->label }}
                    @if ($preset->badge)
                        <span @class([
                            'ml-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold',
                            'bg-white/20 text-white'
                                => $livewire->activePresetViewKey === $preset->key,
                            'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300'
                                => $livewire->activePresetViewKey !== $preset->key,
                        ])>{{ $preset->badge }}</span>
                    @endif
                </button>
            @endforeach

            {{-- Divider between presets and user favorites --}}
            @if ($livewire->getPresetViewsCollection()->isNotEmpty() && $livewire->getFavoriteViews()->isNotEmpty())
                <div class="h-4 w-px bg-gray-300 dark:bg-white/10 mx-1"></div>
            @endif

            {{-- User favorite view pills --}}
            @foreach ($livewire->getFavoriteViews() as $view)
                <button
                    wire:click="applyUserView({{ $view->id }})"
                    type="button"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition-colors',
                        'bg-primary-600 text-white shadow-sm'
                            => $livewire->activeUserViewId === $view->id,
                        'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-gray-700'
                            => $livewire->activeUserViewId !== $view->id,
                    ])
                >
                    @if ($view->icon)
                        <x-dynamic-component :component="$view->icon" class="w-3.5 h-3.5" />
                    @else
                        <x-heroicon-o-bookmark class="w-3.5 h-3.5" />
                    @endif
                    {{ $view->name }}
                    @if ($view->is_global_favorite)
                        <x-heroicon-s-star class="w-3 h-3 text-amber-400" />
                    @endif
                </button>
            @endforeach

            <div class="flex-1"></div>

            {{-- Quick Filter buttons --}}
            @foreach ($livewire->getQuickFilters() as $index => $qf)
                @php
                    $qfLabel = $qf instanceof \Ableaura\FilamentAdvancedTables\Support\QuickFilter ? $qf->label : ($qf['label'] ?? '');
                    $qfIcon  = $qf instanceof \Ableaura\FilamentAdvancedTables\Support\QuickFilter ? $qf->icon  : ($qf['icon']  ?? null);
                @endphp
                <button
                    wire:click="applyQuickFilter({{ $index }})"
                    type="button"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    @if ($qfIcon)
                        <x-dynamic-component :component="$qfIcon" class="w-3.5 h-3.5" />
                    @endif
                    {{ $qfLabel }}
                </button>
            @endforeach

            {{-- Quick Save button --}}
            <button
                wire:click="quickSaveCurrentView"
                type="button"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                title="Save current view"
            >
                <x-heroicon-o-bookmark class="w-3.5 h-3.5" />
                Save View
            </button>

            {{-- View Manager button --}}
            <button
                x-on:click="$dispatch('open-modal', { id: 'advanced-tables-view-manager' })"
                type="button"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                title="Manage views"
            >
                <x-heroicon-o-squares-2x2 class="w-3.5 h-3.5" />
                Views
            </button>

        </div>
    </div>

    {{-- Quick Save Modal --}}
    <x-filament::modal
        id="advanced-tables-quick-save"
        wire:model.live="showSaveViewModal"
        heading="Save View"
        width="sm"
    >
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">View Name</label>
                <input
                    wire:model.live="saveViewName"
                    wire:keydown.enter="confirmSaveView"
                    type="text"
                    placeholder="e.g. Paid This Month"
                    class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="saveViewFavorite" class="rounded border-gray-300 text-primary-600" />
                <span class="text-sm text-gray-700 dark:text-gray-300">Add to Favorites Bar</span>
            </label>
            @if (config('filament-advanced-tables.allow_public_views', true))
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="saveViewPublic" class="rounded border-gray-300 text-primary-600" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">Make Public</span>
                </label>
            @endif
        </div>
        <x-slot name="footerActions">
            <x-filament::button wire:click="confirmSaveView" color="primary">Save</x-filament::button>
            <x-filament::button wire:click="cancelSaveView" color="gray">Cancel</x-filament::button>
        </x-slot>
    </x-filament::modal>

    {{-- View Manager Modal --}}
    <x-filament::modal
        id="advanced-tables-view-manager"
        heading="My Views"
        width="xl"
    >
        @php $userViews = $livewire->getUserViews(); @endphp

        @if ($userViews->isEmpty())
            <div class="text-center py-8 text-sm text-gray-400 dark:text-gray-500">
                <x-heroicon-o-bookmark class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
                No saved views yet. Use "Save View" to create one.
            </div>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-white/10 rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
                @foreach ($userViews as $view)
                    <li class="group flex items-center justify-between gap-3 px-4 py-2.5 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-center gap-2 min-w-0">
                            <x-heroicon-o-bookmark class="w-4 h-4 text-gray-400 flex-shrink-0" />
                            <span class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $view->name }}</span>
                            @if ($view->is_default)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300">Default</span>
                            @endif
                            @if ($view->is_public && ! $view->is_approved)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-700">Pending</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                            <button wire:click="applyUserView({{ $view->id }})" type="button" title="Apply"
                                class="p-1 rounded hover:bg-primary-50 dark:hover:bg-primary-900/20 text-gray-400 hover:text-primary-600">
                                <x-heroicon-o-arrow-right-circle class="w-4 h-4" />
                            </button>
                            <button wire:click="toggleFavoriteView({{ $view->id }})" type="button"
                                title="{{ $view->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}"
                                class="p-1 rounded hover:bg-amber-50 dark:hover:bg-amber-900/20">
                                @if ($view->is_favorite)
                                    <x-heroicon-s-star class="w-4 h-4 text-amber-400" />
                                @else
                                    <x-heroicon-o-star class="w-4 h-4 text-gray-400" />
                                @endif
                            </button>
                            <button wire:click="setDefaultView({{ $view->id }})" type="button" title="Set as default"
                                class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                <x-heroicon-o-home class="w-4 h-4 {{ $view->is_default ? 'text-primary-500' : 'text-gray-400' }}" />
                            </button>
                            <button wire:click="deleteUserView({{ $view->id }})"
                                wire:confirm="Delete this view?"
                                type="button" title="Delete"
                                class="p-1 rounded hover:bg-danger-50 dark:hover:bg-danger-900/20 text-gray-400 hover:text-danger-500">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::modal>

@endif
