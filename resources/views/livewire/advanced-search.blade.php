<div class="filament-advanced-tables-advanced-search flex items-center gap-2 flex-wrap">

    {{-- Column Selector --}}
    @if (count($searchableColumns) > 0)
        <select
            wire:model.live="column"
            class="rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
            <option value="">{{ __('filament-advanced-tables::advanced-tables.all_columns') }}</option>
            @foreach ($searchableColumns as $col)
                <option value="{{ $col['value'] }}">{{ $col['label'] }}</option>
            @endforeach
        </select>
    @endif

    {{-- Operator Selector --}}
    <select
        wire:model.live="operator"
        class="rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500"
    >
        @foreach ($operators as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>

    {{-- Search Input --}}
    <div class="relative flex-1 min-w-48">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400" />
        </div>
        <input
            wire:model.live.debounce.300ms="query"
            type="text"
            placeholder="{{ __('filament-advanced-tables::advanced-tables.search_placeholder') }}"
            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 pl-9 pr-8 py-1.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500"
        />
        @if ($query)
            <button
                wire:click="clearSearch"
                type="button"
                class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
                <x-heroicon-o-x-mark class="w-4 h-4" />
            </button>
        @endif
    </div>

</div>
