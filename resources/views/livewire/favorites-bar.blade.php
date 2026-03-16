<div
    x-data="{
        activeId: @entangle('activeViewId'),
        activePreset: @entangle('activePresetKey'),
    }"
    class="filament-advanced-tables-favorites-bar w-full"
>
    @if ($favoriteViews || $presetViews ?? false)
        <div class="flex flex-wrap items-center gap-1 px-4 py-2 border-b border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-900/40">

            {{-- "All Records" pill --}}
            <button
                wire:click="clearActiveView"
                type="button"
                @class([
                    'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition',
                    'bg-primary-600 text-white shadow-sm' => ! $activeViewId && ! $activePresetKey,
                    'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-gray-700' => $activeViewId || $activePresetKey,
                ])
            >
                <x-heroicon-o-table-cells class="w-3.5 h-3.5" />
                {{ __('filament-advanced-tables::advanced-tables.all_records') }}
            </button>

            {{-- Preset Views --}}
            @foreach ($presetViews ?? [] as $preset)
                <button
                    wire:click="applyPresetView('{{ $preset->key }}')"
                    type="button"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition',
                        'bg-primary-600 text-white shadow-sm' => $activePresetKey === $preset->key,
                        'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-gray-700' => $activePresetKey !== $preset->key,
                    ])
                >
                    @if ($preset->icon)
                        <x-dynamic-component :component="$preset->icon" class="w-3.5 h-3.5" />
                    @endif
                    {{ $preset->label }}
                    @if ($preset->badge)
                        <span @class([
                            'ml-1 px-1.5 py-0.5 rounded text-[10px] font-semibold',
                            'bg-white/20 text-white' => $activePresetKey === $preset->key,
                            'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' => $activePresetKey !== $preset->key,
                        ])>{{ $preset->badge }}</span>
                    @endif
                </button>
            @endforeach

            {{-- Divider if both preset and user views --}}
            @if (($presetViews ?? false) && count($favoriteViews) > 0)
                <div class="h-4 w-px bg-gray-300 dark:bg-white/10 mx-1"></div>
            @endif

            {{-- User Favorite Views --}}
            @foreach ($favoriteViews as $view)
                <button
                    wire:click="applyUserView({{ $view['id'] }})"
                    type="button"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition',
                        'bg-primary-600 text-white shadow-sm' => $activeViewId === $view['id'],
                        'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-gray-700' => $activeViewId !== $view['id'],
                    ])
                >
                    @if ($view['icon'])
                        <x-dynamic-component :component="$view['icon']" class="w-3.5 h-3.5" />
                    @else
                        <x-heroicon-o-bookmark class="w-3.5 h-3.5" />
                    @endif
                    {{ $view['name'] }}
                    @if ($view['is_global_favorite'])
                        <x-heroicon-s-star class="w-3 h-3 text-amber-400" />
                    @endif
                </button>
            @endforeach

        </div>
    @endif
</div>
