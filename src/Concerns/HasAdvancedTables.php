<?php

namespace Ableaura\FilamentAdvancedTables\Concerns;

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Ableaura\FilamentAdvancedTables\Support\PresetView;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait HasAdvancedTables
{
    // ─── State ───────────────────────────────────────────────────────────────────

    public ?int $activeUserViewId = null;
    public ?string $activePresetViewKey = null;

    /** @var array<string, mixed> */
    public array $advancedFilterValues = [];

    /** @var array<string> */
    public array $multiSortColumns = [];

    public string $advancedSearchQuery = '';
    public ?string $advancedSearchColumn = null;
    public string $advancedSearchOperator = 'contains';

    public bool $showFavoritesBar = true;

    // ─── Lifecycle ────────────────────────────────────────────────────────────────

    public function mountHasAdvancedTables(): void
    {
        $this->showFavoritesBar = true;
    }

    // ─── Preset Views ─────────────────────────────────────────────────────────────

    /**
     * Override this method in your resource to define preset views.
     *
     * @return array<PresetView>
     */
    protected function getPresetViews(): array
    {
        return [];
    }

    public function getPresetViewsCollection(): Collection
    {
        return collect($this->getPresetViews());
    }

    public function applyPresetView(string $key): void
    {
        $preset = collect($this->getPresetViews())->firstWhere('key', $key);

        if (! $preset) {
            return;
        }

        $this->activePresetViewKey = $key;
        $this->activeUserViewId = null;

        if ($preset->filters) {
            $this->tableFilters = $preset->filters;
        }

        if ($preset->sortColumn) {
            $this->tableSortColumn = $preset->sortColumn;
            $this->tableSortDirection = $preset->sortDirection ?? 'asc';
        }

        if ($preset->toggledColumns) {
            $this->toggledTableColumns = $preset->toggledColumns;
        }

        if ($preset->columnOrder) {
            // stored for later use
        }

        $this->resetPage();
    }

    // ─── User Views ───────────────────────────────────────────────────────────────

    public function getUserViews(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        return UserView::query()
            ->where('resource', static::class)
            ->where(function ($q) {
                $q->where('user_id', Auth::id())
                    ->orWhere('is_global_favorite', true);
            })
            ->orderBy('name')
            ->get();
    }

    public function applyUserView(int $viewId): void
    {
        $view = UserView::find($viewId);

        if (! $view || ! $this->canUseView($view)) {
            return;
        }

        $this->activeUserViewId = $viewId;
        $this->activePresetViewKey = null;

        $state = $view->state ?? [];

        if (! empty($state['filters'])) {
            $this->tableFilters = $state['filters'];
        }

        if (! empty($state['sort_column'])) {
            $this->tableSortColumn = $state['sort_column'];
            $this->tableSortDirection = $state['sort_direction'] ?? 'asc';
        }

        if (! empty($state['toggled_columns'])) {
            $this->toggledTableColumns = $state['toggled_columns'];
        }

        if (! empty($state['search'])) {
            $this->tableSearch = $state['search'];
        }

        $this->resetPage();
    }

    public function saveCurrentView(string $name, bool $isPublic = false, bool $isFavorite = false, ?string $icon = null, ?string $color = null): void
    {
        if (! Auth::check()) {
            return;
        }

        $state = [
            'filters'         => $this->tableFilters ?? [],
            'sort_column'     => $this->tableSortColumn ?? null,
            'sort_direction'  => $this->tableSortDirection ?? null,
            'toggled_columns' => $this->toggledTableColumns ?? [],
            'search'          => $this->tableSearch ?? '',
        ];

        $view = UserView::create([
            'user_id'           => Auth::id(),
            'resource'          => static::class,
            'name'              => $name,
            'state'             => $state,
            'is_public'         => $isPublic,
            'is_favorite'       => $isFavorite,
            'is_approved'       => ! config('filament-advanced-tables.approval_required', false),
            'icon'              => $icon,
            'color'             => $color,
        ]);

        $this->activeUserViewId = $view->id;

        $this->notify('success', __('filament-advanced-tables::advanced-tables.view_saved'));
    }

    public function quickSaveView(): void
    {
        if ($this->activeUserViewId) {
            // Update existing view
            $view = UserView::find($this->activeUserViewId);

            if ($view && $this->canEditView($view)) {
                $view->update([
                    'state' => [
                        'filters'         => $this->tableFilters ?? [],
                        'sort_column'     => $this->tableSortColumn ?? null,
                        'sort_direction'  => $this->tableSortDirection ?? null,
                        'toggled_columns' => $this->toggledTableColumns ?? [],
                        'search'          => $this->tableSearch ?? '',
                    ],
                ]);

                $this->notify('success', __('filament-advanced-tables::advanced-tables.view_updated'));
            }
        } else {
            // Prompt for name via modal — handled in the Livewire component
            $this->dispatch('open-modal', id: 'advanced-tables-quick-save');
        }
    }

    public function deleteUserView(int $viewId): void
    {
        $view = UserView::find($viewId);

        if ($view && $this->canDeleteView($view)) {
            if ($this->activeUserViewId === $viewId) {
                $this->activeUserViewId = null;
            }
            $view->delete();
            $this->notify('success', __('filament-advanced-tables::advanced-tables.view_deleted'));
        }
    }

    public function toggleFavorite(int $viewId): void
    {
        $view = UserView::find($viewId);

        if ($view && $this->canEditView($view)) {
            $view->update(['is_favorite' => ! $view->is_favorite]);
        }
    }

    // ─── Authorization Helpers ────────────────────────────────────────────────────

    protected function canUseView(UserView $view): bool
    {
        return $view->user_id === Auth::id()
            || $view->is_public
            || $view->is_global_favorite;
    }

    protected function canEditView(UserView $view): bool
    {
        return $view->user_id === Auth::id();
    }

    protected function canDeleteView(UserView $view): bool
    {
        if (Auth::user()?->can('deleteAny', UserView::class)) {
            return true;
        }

        return $view->user_id === Auth::id();
    }

    // ─── Multi-Sort ───────────────────────────────────────────────────────────────

    public function addSortColumn(string $column, string $direction = 'asc'): void
    {
        $this->multiSortColumns = array_filter(
            $this->multiSortColumns,
            fn ($c) => ! str_starts_with($c, $column . ':')
        );

        $this->multiSortColumns[] = "{$column}:{$direction}";
    }

    public function removeSortColumn(string $column): void
    {
        $this->multiSortColumns = array_values(array_filter(
            $this->multiSortColumns,
            fn ($c) => ! str_starts_with($c, $column . ':')
        ));
    }

    public function clearMultiSort(): void
    {
        $this->multiSortColumns = [];
    }

    public function getMultiSortAsArray(): array
    {
        $result = [];

        foreach ($this->multiSortColumns as $entry) {
            [$col, $dir] = explode(':', $entry, 2);
            $result[] = ['column' => $col, 'direction' => $dir];
        }

        return $result;
    }

    // ─── Advanced Search ──────────────────────────────────────────────────────────

    public function applyAdvancedSearch(string $query, ?string $column = null, string $operator = 'contains'): void
    {
        $this->advancedSearchQuery = $query;
        $this->advancedSearchColumn = $column;
        $this->advancedSearchOperator = $operator;
        $this->resetPage();
    }

    public function clearAdvancedSearch(): void
    {
        $this->advancedSearchQuery = '';
        $this->advancedSearchColumn = null;
        $this->advancedSearchOperator = 'contains';
        $this->resetPage();
    }

    // ─── Quick Filters ────────────────────────────────────────────────────────────

    /**
     * Define quick filter shortcuts.
     *
     * @return array<array{label: string, filters: array<string, mixed>}>
     */
    protected function getQuickFilters(): array
    {
        return [];
    }

    public function applyQuickFilter(int $index): void
    {
        $quickFilters = $this->getQuickFilters();

        if (! isset($quickFilters[$index])) {
            return;
        }

        $this->tableFilters = array_merge(
            $this->tableFilters ?? [],
            $quickFilters[$index]['filters'] ?? []
        );

        $this->resetPage();
    }

    // ─── Advanced Filter Builder ──────────────────────────────────────────────────

    public function applyAdvancedFilters(array $filters): void
    {
        $this->advancedFilterValues = $filters;
        $this->resetPage();
    }

    public function clearAdvancedFilters(): void
    {
        $this->advancedFilterValues = [];
        $this->resetPage();
    }

    // ─── Managed Default Views ────────────────────────────────────────────────────

    public function getManagedDefaultView(): ?UserView
    {
        if (! Auth::check()) {
            return null;
        }

        return UserView::query()
            ->where('resource', static::class)
            ->where('is_default', true)
            ->where(function ($q) {
                $q->where('user_id', Auth::id())
                    ->orWhere('is_global_favorite', true);
            })
            ->first();
    }

    // ─── Favorites Bar ────────────────────────────────────────────────────────────

    public function getFavoriteViews(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        return UserView::query()
            ->where('resource', static::class)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('user_id', Auth::id())
                        ->where('is_favorite', true);
                })->orWhere('is_global_favorite', true);
            })
            ->orderBy('name')
            ->get();
    }

    public function toggleFavoritesBar(): void
    {
        $this->showFavoritesBar = ! $this->showFavoritesBar;
    }

    // ─── Helper ───────────────────────────────────────────────────────────────────

    protected function notify(string $type, string $message): void
    {
        \Filament\Notifications\Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }
}
