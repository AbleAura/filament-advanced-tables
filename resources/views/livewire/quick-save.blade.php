<div class="filament-advanced-tables-quick-save">

    {{-- Quick Save Button --}}
    <button
        wire:click="quickSave"
        type="button"
        @class([
            'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium border transition',
            'bg-primary-600 border-primary-600 text-white hover:bg-primary-700 shadow-sm' => $activeViewId !== null,
            'bg-white dark:bg-gray-800 border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' => $activeViewId === null,
        ])
        title="{{ $activeViewId ? __('filament-advanced-tables::advanced-tables.view_updated') : __('filament-advanced-tables::advanced-tables.save_view') }}"
    >
        <x-heroicon-o-bookmark class="w-4 h-4" />
        {{ __('filament-advanced-tables::advanced-tables.quick_save') }}
    </button>

    {{-- Name Modal (when no view is active yet) --}}
    <x-filament::modal
        id="advanced-tables-quick-save-name"
        wire:model.live="showNameModal"
        :heading="__('filament-advanced-tables::advanced-tables.save_view')"
        width="sm"
    >
        <div class="space-y-3">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('filament-advanced-tables::advanced-tables.save_modal_subheading') }}
            </p>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('filament-advanced-tables::advanced-tables.view_name') }}
                </label>
                <input
                    wire:model.live="newViewName"
                    wire:keydown.enter="confirmSaveNew"
                    type="text"
                    autofocus
                    placeholder="{{ __('filament-advanced-tables::advanced-tables.view_name_placeholder') }}"
                    class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
            </div>
        </div>

        <x-slot name="footerActions">
            <x-filament::button wire:click="confirmSaveNew" color="primary">
                {{ __('filament-advanced-tables::advanced-tables.save_view') }}
            </x-filament::button>
            <x-filament::button wire:click="cancelNameModal" color="gray">
                {{ __('Cancel') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

</div>
