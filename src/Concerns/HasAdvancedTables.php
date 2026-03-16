<?php

namespace Ableaura\FilamentAdvancedTables\Concerns;

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Ableaura\FilamentAdvancedTables\Support\PresetView;
use Ableaura\FilamentAdvancedTables\Support\QuickFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Add this trait to any Filament ListRecords page to enable:
 *  - Preset Views (developer-defined, shown in favorites bar)
 *  - User Views (user-saved filter/sort/column state)
 *  - Quick Filters (one-click filter shortcuts)
 *  - Quick Save / View Manager
 *  - Multi-Sort  (via AppliesMultiSort)
 *  - Advanced Search (via AppliesAdvancedSearch)
 */
trait HasAdvancedTables
{
    // ─── Livewire public state ────────────────────────────────────────────────────

    public ?int $activeUserViewId = null;
    public ?string $activePresetViewKey = null;
    public bool $showFavoritesBar = true;

    // Quick Save modal
    public bool $showSaveViewModal = false;
    public string $saveViewName = '';
    public bool $saveViewFavorite = false;
    public bool $saveViewPublic = false;

    // ─── Lifecycle ────────────────────────────────────────────────────────────────

    public function bootHasAdvancedTables(): void
    {
        // Apply default view on first load
    }

    public function mountHasAdvancedTables(): void
    {
        $this->showFavoritesBar = true;

        // Apply user's default view if one exists
        $default = $this->getManagedDefaultView();
        if ($default) {
            $this->doApplyUserView($default);
        }
    }

    // ─── Preset Views ─────────────────────────────────────────────────────────────

    /**
     * Override in your ListRecords page to define preset views.
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

    /**
     * Called when user clicks a preset view pill.
     * Uses Filament 3's public table API.
     */
    public function applyPresetView(string $key): void
    {
        $preset = collect($this->getPresetViews())->first(fn (PresetView $p) => $p->key === $key);

        if (! $preset) {
            return;
        }

        $this->activePresetViewKey = $key;
        $this->activeUserViewId    = null;

        // Apply filters using Filament 3 API
        if (! empty($preset->filters)) {
            $this->resetTableFiltersForm();
            foreach ($preset->filters as $filterName => $filterData) {
                $this->setTableFilterState($filterName, $filterData);
            }
        }

        // Apply sort
        if ($preset->sortColumn) {
            $this->sortTable($preset->sortColumn, $preset->sortDirection ?? 'asc');
        }

        // Apply toggled columns
        if (! empty($preset->toggledColumns)) {
            foreach ($preset->toggledColumns as $column) {
                $this->toggleTableColumn($column);
            }
        }

        $this->resetPage();
    }

    public function clearActiveView(): void
    {
        $this->activePresetViewKey = null;
        $this->activeUserViewId    = null;
        $this->resetTableFiltersForm();
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

        $this->doApplyUserView($view);
    }

    private function doApplyUserView(UserView $view): void
    {
        $this->activeUserViewId    = $view->id;
        $this->activePresetViewKey = null;

        $state = $view->state ?? [];

        // Apply filters
        if (! empty($state['filters'])) {
            $this->resetTableFiltersForm();
            foreach ($state['filters'] as $filterName => $filterData) {
                $this->setTableFilterState($filterName, $filterData);
            }
        }

        // Apply sort
        if (! empty($state['sort_column'])) {
            $this->sortTable($state['sort_column'], $state['sort_direction'] ?? 'asc');
        }

        // Apply search
        if (! empty($state['search'])) {
            $this->tableSearch = $state['search'];
        }

        // Apply toggled columns
        if (! empty($state['toggled_columns'])) {
            foreach ($state['toggled_columns'] as $column) {
                $this->toggleTableColumn($column);
            }
        }

        $this->resetPage();
    }

    // ─── Quick Save ───────────────────────────────────────────────────────────────

    /**
     * If a user view is active, update it in-place.
     * Otherwise open the save modal.
     */
    public function quickSaveCurrentView(): void
    {
        if ($this->activeUserViewId) {
            $view = UserView::find($this->activeUserViewId);
            if ($view && $this->canEditView($view)) {
                $view->update(['state' => $this->captureTableState()]);
                $this->sendNotification('success', __('filament-advanced-tables::advanced-tables.view_updated'));
                return;
            }
        }

        // No active view — open modal to name it
        $this->saveViewName      = '';
        $this->saveViewFavorite  = false;
        $this->saveViewPublic    = false;
        $this->showSaveViewModal = true;
    }

    public function confirmSaveView(): void
    {
        $this->validate(['saveViewName' => 'required|string|max:255']);

        $view = UserView::create([
            'user_id'     => Auth::id(),
            'resource'    => static::class,
            'name'        => $this->saveViewName,
            'state'       => $this->captureTableState(),
            'is_public'   => $this->saveViewPublic,
            'is_favorite' => $this->saveViewFavorite,
            'is_approved' => ! config('filament-advanced-tables.approval_required', false),
        ]);

        $this->activeUserViewId  = $view->id;
        $this->showSaveViewModal = false;

        $this->sendNotification('success', __('filament-advanced-tables::advanced-tables.view_saved'));
    }

    public function cancelSaveView(): void
    {
        $this->showSaveViewModal = false;
    }

    public function deleteUserView(int $viewId): void
    {
        $view = UserView::find($viewId);

        if ($view && $this->canDeleteView($view)) {
            if ($this->activeUserViewId === $viewId) {
                $this->activeUserViewId = null;
            }
            $view->delete();
            $this->sendNotification('success', __('filament-advanced-tables::advanced-tables.view_deleted'));
        }
    }

    public function toggleFavoriteView(int $viewId): void
    {
        $view = UserView::find($viewId);

        if ($view && $this->canEditView($view)) {
            $view->update(['is_favorite' => ! $view->is_favorite]);
        }
    }

    public function setDefaultView(int $viewId): void
    {
        // Clear existing defaults for this resource + user
        UserView::where('resource', static::class)
            ->where('user_id', Auth::id())
            ->update(['is_default' => false]);

        UserView::find($viewId)?->update(['is_default' => true]);
        $this->sendNotification('success', __('filament-advanced-tables::advanced-tables.view_saved'));
    }

    // ─── Quick Filters ────────────────────────────────────────────────────────────

    /**
     * Override in your ListRecords page to define quick filter shortcuts.
     *
     * @return array<QuickFilter>
     */
    protected function getQuickFilters(): array
    {
        return [];
    }

    /**
     * Called when user clicks a quick filter button.
     * Merges the quick filter's filter state on top of existing filters.
     */
    public function applyQuickFilter(int $index): void
    {
        $filters = $this->getQuickFilters();

        if (! isset($filters[$index])) {
            return;
        }

        $quickFilter = $filters[$index];
        $filterData  = ($quickFilter instanceof QuickFilter) ? $quickFilter->filters : ($quickFilter['filters'] ?? []);

        foreach ($filterData as $filterName => $filterState) {
            $this->setTableFilterState($filterName, $filterState);
        }

        $this->resetPage();
    }

    public function clearQuickFilters(): void
    {
        $this->resetTableFiltersForm();
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

    // ─── Authorization ────────────────────────────────────────────────────────────

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

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    /**
     * Capture the current table state using Filament 3's public API.
     */
    protected function captureTableState(): array
    {
        $table = $this->getTable();

        return [
            'filters'         => $this->getTableFilterState() ?? [],
            'sort_column'     => $table->getSortColumn(),
            'sort_direction'  => $table->getSortDirection(),
            'toggled_columns' => $this->getToggledTableColumns() ?? [],
            'search'          => $this->getTableSearch() ?? '',
        ];
    }

    /**
     * Get current filter state across all filters.
     */
    protected function getTableFilterState(): array
    {
        try {
            $state = [];
            foreach ($this->getTable()->getFilters() as $filter) {
                $filterState = $this->getFilterState($filter->getName());
                if (! empty($filterState)) {
                    $state[$filter->getName()] = $filterState;
                }
            }
            return $state;
        } catch (\Throwable) {
            return [];
        }
    }

    protected function getTableSearch(): string
    {
        return $this->tableSearch ?? '';
    }

    protected function sendNotification(string $type, string $message): void
    {
        \Filament\Notifications\Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }
}
