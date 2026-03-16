<div class="filament-advanced-tables-view-manager">

    {{-- Trigger Button --}}
    <x-filament::button
        wire:click="openSaveModal"
        icon="heroicon-o-bookmark-square"
        color="gray"
        size="sm"
        outlined
    >
        {{ __('filament-advanced-tables::advanced-tables.manage_views') }}
    </x-filament::button>

    {{-- View Manager Modal --}}
    <x-filament::modal
        id="advanced-tables-view-manager"
        wire:model.live="showModal"
        width="2xl"
        :heading="__('filament-advanced-tables::advanced-tables.view_manager_heading')"
    >
        <div class="space-y-4">

            {{-- Existing views list --}}
            @if (count($userViews) > 0)
                <ul class="divide-y divide-gray-100 dark:divide-white/10 rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
                    @foreach ($userViews as $view)
                        <li class="flex items-center justify-between gap-3 px-4 py-2.5 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition group">

                            {{-- Icon + Name --}}
                            <div class="flex items-center gap-2 min-w-0">
                                @if ($view['icon'])
                                    <x-dynamic-component :component="$view['icon']" class="w-4 h-4 text-gray-400 flex-shrink-0" />
                                @else
                                    <x-heroicon-o-bookmark class="w-4 h-4 text-gray-400 flex-shrink-0" />
                                @endif
                                <span class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $view['name'] }}</span>

                                {{-- Badges --}}
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    @if ($view['is_default'])
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300">
                                            {{ __('filament-advanced-tables::advanced-tables.default_badge') }}
                                        </span>
                                    @endif
                                    @if ($view['is_public'])
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">
                                            {{ __('filament-advanced-tables::advanced-tables.public_badge') }}
                                        </span>
                                    @endif
                                    @if (! $view['is_approved'])
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300">
                                            {{ __('filament-advanced-tables::advanced-tables.pending_badge') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition">

                                {{-- Favorite toggle --}}
                                <button
                                    wire:click="toggleFavorite({{ $view['id'] }})"
                                    type="button"
                                    title="{{ $view['is_favorite'] ? __('filament-advanced-tables::advanced-tables.remove_favorite') : __('filament-advanced-tables::advanced-tables.make_favorite') }}"
                                    class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                >
                                    @if ($view['is_favorite'])
                                        <x-heroicon-s-star class="w-4 h-4 text-amber-400" />
                                    @else
                                        <x-heroicon-o-star class="w-4 h-4 text-gray-400" />
                                    @endif
                                </button>

                                {{-- Set default --}}
                                <button
                                    wire:click="setDefault({{ $view['id'] }})"
                                    type="button"
                                    title="{{ __('filament-advanced-tables::advanced-tables.set_default') }}"
                                    class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                >
                                    <x-heroicon-o-home class="w-4 h-4 {{ $view['is_default'] ? 'text-primary-500' : 'text-gray-400' }}" />
                                </button>

                                {{-- Edit --}}
                                <button
                                    wire:click="openSaveModal({{ $view['id'] }})"
                                    type="button"
                                    title="{{ __('filament-advanced-tables::advanced-tables.edit_view') }}"
                                    class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                >
                                    <x-heroicon-o-pencil-square class="w-4 h-4 text-gray-400" />
                                </button>

                                {{-- Delete --}}
                                <button
                                    wire:click="deleteView({{ $view['id'] }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this view?') }}"
                                    type="button"
                                    title="{{ __('filament-advanced-tables::advanced-tables.delete_view') }}"
                                    class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                >
                                    <x-heroicon-o-trash class="w-4 h-4 text-gray-400 hover:text-danger-500" />
                                </button>

                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                    <x-heroicon-o-bookmark class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
                    {{ __('filament-advanced-tables::advanced-tables.no_saved_views') }}
                </div>
            @endif

        </div>

        <x-slot name="footerActions">
            <x-filament::button
                wire:click="openSaveModal"
                icon="heroicon-o-plus"
                color="primary"
            >
                {{ __('filament-advanced-tables::advanced-tables.save_view') }}
            </x-filament::button>
        </x-slot>

    </x-filament::modal>

    {{-- Save / Edit View Modal --}}
    <x-filament::modal
        id="advanced-tables-save-view"
        :heading="$editingViewId ? __('filament-advanced-tables::advanced-tables.edit_view') : __('filament-advanced-tables::advanced-tables.save_view')"
        width="lg"
    >
        <div class="space-y-4">
            {{-- View Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('filament-advanced-tables::advanced-tables.view_name') }}
                </label>
                <input
                    wire:model.live="viewName"
                    type="text"
                    placeholder="{{ __('filament-advanced-tables::advanced-tables.view_name_placeholder') }}"
                    class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
            </div>

            {{-- Favorite toggle --}}
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" wire:model.live="isFavorite" class="rounded border-gray-300 dark:border-white/10 text-primary-600 focus:ring-primary-500" />
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ __('filament-advanced-tables::advanced-tables.mark_as_favorite') }}
                </span>
            </label>

            {{-- Public toggle --}}
            @if (config('filament-advanced-tables.allow_public_views', true))
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model.live="isPublic" class="rounded border-gray-300 dark:border-white/10 text-primary-600 focus:ring-primary-500" />
                    <div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ __('filament-advanced-tables::advanced-tables.mark_as_public') }}
                        </span>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            {{ __('filament-advanced-tables::advanced-tables.mark_as_public_hint') }}
                        </p>
                    </div>
                </label>
            @endif

        </div>

        <x-slot name="footerActions">
            <x-filament::button wire:click="saveView" color="primary">
                {{ $editingViewId ? __('Update View') : __('filament-advanced-tables::advanced-tables.save_view') }}
            </x-filament::button>
            <x-filament::button wire:click="closeModal" color="gray">
                {{ __('Cancel') }}
            </x-filament::button>
        </x-slot>

    </x-filament::modal>

</div>
