<div class="filament-advanced-tables-quick-filters flex flex-wrap items-center gap-1.5">
    @foreach ($quickFilters as $index => $filter)
        <button
            wire:click="applyFilter({{ $index }})"
            type="button"
            @class([
                'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition',
                'bg-primary-600 border-primary-600 text-white shadow-sm' => $activeIndex === $index,
                'bg-white dark:bg-gray-800 border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' => $activeIndex !== $index,
            ])
        >
            @if (isset($filter['icon']))
                <x-dynamic-component :component="$filter['icon']" class="w-3.5 h-3.5" />
            @endif
            {{ $filter['label'] }}
        </button>
    @endforeach

    @if ($activeIndex !== null)
        <button
            wire:click="clearFilters"
            type="button"
            class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 hover:text-danger-500 ml-1"
        >
            <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
            {{ __('filament-advanced-tables::advanced-tables.clear_filters') }}
        </button>
    @endif
</div>
