<div
    x-data="{ open: @entangle('isOpen').live }"
    class="filament-advanced-tables-filter-builder relative"
>
    {{-- Trigger Button --}}
    <button
        @click="open = !open"
        type="button"
        @class([
            'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium border transition',
            'bg-primary-50 dark:bg-primary-900/30 border-primary-300 dark:border-primary-700 text-primary-700 dark:text-primary-300' => $this->hasActiveFilters(),
            'bg-white dark:bg-gray-800 border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' => ! $this->hasActiveFilters(),
        ])
    >
        <x-heroicon-o-funnel class="w-4 h-4" />
        {{ __('filament-advanced-tables::advanced-tables.filter_builder') }}
        @if ($this->hasActiveFilters())
            <span class="ml-0.5 inline-flex items-center justify-center w-4 h-4 rounded-full bg-primary-600 text-white text-[10px] font-bold">
                {{ $this->getActiveConditionCount() }}
            </span>
        @endif
    </button>

    {{-- Filter Builder Panel --}}
    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        class="absolute top-full left-0 mt-2 w-[640px] max-w-[90vw] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-white/10 z-30"
        style="display: none;"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-white/10">
            <div class="flex items-center gap-2">
                <x-heroicon-o-funnel class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                <span class="text-sm font-semibold text-gray-800 dark:text-white">
                    {{ __('filament-advanced-tables::advanced-tables.filter_builder') }}
                </span>
            </div>
            @if ($this->hasActiveFilters())
                <button
                    wire:click="clearFilters"
                    type="button"
                    class="text-xs text-danger-500 hover:text-danger-700 font-medium"
                >
                    {{ __('filament-advanced-tables::advanced-tables.clear_all_filters') }}
                </button>
            @endif
        </div>

        {{-- Groups --}}
        <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
            @forelse ($groups as $groupIndex => $group)
                <div class="rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">

                    {{-- Group Header --}}
                    <div class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-white/10">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('filament-advanced-tables::advanced-tables.match_all') }}
                            </span>
                            <select
                                wire:model.live="groups.{{ $groupIndex }}.logic"
                                class="rounded border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-xs text-gray-700 dark:text-gray-200 px-2 py-0.5 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                                <option value="and">{{ __('filament-advanced-tables::advanced-tables.match_all') }}</option>
                                <option value="or">{{ __('filament-advanced-tables::advanced-tables.match_any') }}</option>
                            </select>
                        </div>
                        <button
                            wire:click="removeGroup({{ $groupIndex }})"
                            type="button"
                            class="p-1 rounded text-gray-400 hover:text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition"
                        >
                            <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    {{-- Conditions --}}
                    <div class="p-2 space-y-2">
                        @foreach ($group['conditions'] as $conditionIndex => $condition)
                            <div class="flex items-center gap-2">

                                {{-- Column --}}
                                <select
                                    wire:model.live="groups.{{ $groupIndex }}.conditions.{{ $conditionIndex }}.column"
                                    class="flex-1 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 min-w-0"
                                >
                                    <option value="">{{ __('filament-advanced-tables::advanced-tables.select_column') }}</option>
                                    @foreach ($filterableColumns as $col)
                                        <option value="{{ $col['column'] }}">{{ $col['label'] }}</option>
                                    @endforeach
                                </select>

                                {{-- Operator --}}
                                <select
                                    wire:model.live="groups.{{ $groupIndex }}.conditions.{{ $conditionIndex }}.operator"
                                    class="w-36 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 flex-shrink-0"
                                >
                                    @foreach ($operators as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>

                                {{-- Value (hide for is_null / is_not_null) --}}
                                @if (! in_array($condition['operator'] ?? '', ['is_null', 'is_not_null']))
                                    <input
                                        wire:model.live.debounce.300ms="groups.{{ $groupIndex }}.conditions.{{ $conditionIndex }}.value"
                                        type="text"
                                        placeholder="{{ __('filament-advanced-tables::advanced-tables.enter_value') }}"
                                        class="flex-1 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-400 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 min-w-0"
                                    />
                                @else
                                    <div class="flex-1"></div>
                                @endif

                                {{-- Remove condition --}}
                                <button
                                    wire:click="removeCondition({{ $groupIndex }}, {{ $conditionIndex }})"
                                    type="button"
                                    class="p-1.5 rounded text-gray-400 hover:text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition flex-shrink-0"
                                >
                                    <x-heroicon-o-minus-circle class="w-4 h-4" />
                                </button>

                            </div>
                        @endforeach

                        {{-- Add condition --}}
                        <button
                            wire:click="addCondition({{ $groupIndex }})"
                            type="button"
                            class="flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 hover:underline mt-1"
                        >
                            <x-heroicon-o-plus class="w-3.5 h-3.5" />
                            {{ __('filament-advanced-tables::advanced-tables.add_condition') }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <x-heroicon-o-funnel class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
                    <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('filament-advanced-tables::advanced-tables.no_conditions') }}</p>
                    <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">{{ __('Click "Add Group" to start building your filter') }}</p>
                </div>
            @endforelse

            {{-- Add group --}}
            <button
                wire:click="addGroup"
                type="button"
                class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-dashed border-gray-300 dark:border-white/20 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-700 dark:hover:text-gray-300 transition"
            >
                <x-heroicon-o-plus class="w-4 h-4" />
                {{ __('filament-advanced-tables::advanced-tables.add_group') }}
            </button>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between gap-2 px-4 py-3 border-t border-gray-100 dark:border-white/10">
            <button
                wire:click="clearFilters"
                type="button"
                class="px-3 py-1.5 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            >
                {{ __('filament-advanced-tables::advanced-tables.clear_all_filters') }}
            </button>
            <x-filament::button
                wire:click="applyFilters"
                color="primary"
                size="sm"
            >
                {{ __('filament-advanced-tables::advanced-tables.apply_filters') }}
            </x-filament::button>
        </div>
    </div>
</div>
