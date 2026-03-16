<div
    x-data="{ open: false }"
    class="filament-advanced-tables-multi-sort relative"
>
    {{-- Trigger --}}
    <button
        @click="open = !open"
        type="button"
        @class([
            'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium border transition',
            'bg-primary-50 dark:bg-primary-900/30 border-primary-300 dark:border-primary-700 text-primary-700 dark:text-primary-300' => count($sorts) > 0,
            'bg-white dark:bg-gray-800 border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' => count($sorts) === 0,
        ])
    >
        <x-heroicon-o-bars-arrow-up class="w-4 h-4" />
        {{ __('filament-advanced-tables::advanced-tables.multi_sort') }}
        @if (count($sorts) > 0)
            <span class="ml-0.5 inline-flex items-center justify-center w-4 h-4 rounded-full bg-primary-600 text-white text-[10px] font-bold">
                {{ count($sorts) }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        class="absolute top-full left-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-white/10 z-20 overflow-hidden"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-white/10">
            <span class="text-sm font-semibold text-gray-800 dark:text-white">
                {{ __('filament-advanced-tables::advanced-tables.multi_sort') }}
            </span>
            @if (count($sorts) > 0)
                <button
                    wire:click="clearSort"
                    type="button"
                    class="text-xs text-danger-500 hover:text-danger-700 font-medium"
                >
                    {{ __('filament-advanced-tables::advanced-tables.clear_sort') }}
                </button>
            @endif
        </div>

        {{-- Sort Rows --}}
        <div class="p-3 space-y-2 max-h-72 overflow-y-auto">
            @forelse ($sorts as $index => $sort)
                <div class="flex items-center gap-2">
                    {{-- Column selector --}}
                    <select
                        wire:model.live="sorts.{{ $index }}.column"
                        wire:change="applySort"
                        class="flex-1 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        <option value="">{{ __('filament-advanced-tables::advanced-tables.sort_column') }}</option>
                        @foreach ($availableColumns as $col)
                            <option value="{{ $col['value'] }}">{{ $col['label'] }}</option>
                        @endforeach
                    </select>

                    {{-- Direction --}}
                    <select
                        wire:model.live="sorts.{{ $index }}.direction"
                        wire:change="applySort"
                        class="w-28 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        <option value="asc">↑ {{ __('filament-advanced-tables::advanced-tables.sort_asc') }}</option>
                        <option value="desc">↓ {{ __('filament-advanced-tables::advanced-tables.sort_desc') }}</option>
                    </select>

                    {{-- Remove --}}
                    <button
                        wire:click="removeSort({{ $index }})"
                        type="button"
                        class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-danger-500 transition"
                    >
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                    </button>
                </div>
            @empty
                <p class="text-center text-sm text-gray-400 dark:text-gray-500 py-4">
                    {{ __('filament-advanced-tables::advanced-tables.no_sorts_applied') }}
                </p>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-3 pb-3">
            <button
                wire:click="addSort"
                type="button"
                class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-dashed border-gray-300 dark:border-white/20 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-700 dark:hover:text-gray-300 transition"
            >
                <x-heroicon-o-plus class="w-4 h-4" />
                {{ __('filament-advanced-tables::advanced-tables.add_sort') }}
            </button>
        </div>
    </div>
</div>
