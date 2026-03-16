<?php

namespace Ableaura\FilamentAdvancedTables\Concerns;

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Ableaura\FilamentAdvancedTables\Support\PresetView;
use Ableaura\FilamentAdvancedTables\Support\QuickFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Add this trait to any Filament ListRecords page to enable:
 *  - Preset Views  (developer-defined, shown as pills in the favorites bar)
 *  - User Views    (user-saved filter/sort/column state)
 *  - Quick Filters (one-click filter shortcuts)
 *  - Quick Save / View Manager
 *  - Multi-Sort    (via AppliesMultiSort)
 *  - Advanced Search (via AppliesAdvancedSearch)
 *
 * All property/method names are chosen to avoid clashing with Filament's own
 * InteractsWithTable traits (HasFilters, CanSortRecords, CanToggleColumns, etc.)
 */
trait HasAdvancedTables
{
    // ─── Public Livewire state ────────────────────────────────────────────────────

    public ?int    $activeUserViewId    = null;
    public ?string $activePresetViewKey = null;
    public bool    $showFavoritesBar    = true;

    // Save-view modal state
    public bool   $showSaveViewModal = false;
    public string $saveViewName      = '';
    public bool   $saveViewFavorite  = false;
    public bool   $saveViewPublic    = false;

    // ─── Lifecycle ────────────────────────────────────────────────────────────────

    public function mountHasAdvancedTables(): void
    {
        $this->showFavoritesBar = true;

        // Apply the user's stored default view on first load
        $default = $this->getAdvancedDefaultView();
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
     * Called when a preset view pill is clicked.
     * Writes directly to $this->tableFilters (Filament's real Livewire property).
     */
    public function applyPresetView(string $key): void
    {
        $preset = collect($this->getPresetViews())
            ->first(fn (PresetView $p) => $p->key === $key);

        if (! $preset) {
            return;
        }

        $this->activePresetViewKey = $key;
        $this->activeUserViewId    = null;

        // Apply filters — write directly to the Filament Livewire property
        if (! empty($preset->filters)) {
            $this->tableFilters = array_merge($this->tableFilters ?? [], $preset->filters);
        }

        // Apply sort
        if ($preset->sortColumn) {
            $this->tableSortColumn    = $preset->sortColumn;
            $this->tableSortDirection = $preset->sortDirection ?? 'asc';
        }

        // Apply toggled columns
        if (! empty($preset->toggledColumns)) {
            foreach ($preset->toggledColumns as $column => $visible) {
                $this->toggledTableColumns[$column] = $visible;
            }
        }

        $this->resetPage();
    }

    /**
     * Reset to "all records" — clear active preset/view and all filters.
     */
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

        if (! $view || ! $this->canUseAdvancedView($view)) {
            return;
        }

        $this->doApplyUserView($view);
    }

    private function doApplyUserView(UserView $view): void
    {
        $this->activeUserViewId    = $view->id;
        $this->activePresetViewKey = null;

        $state = $view->state ?? [];

        // Filters — write directly to $this->tableFilters
        if (! empty($state['filters'])) {
            $this->tableFilters = $state['filters'];
        }

        // Sort
        if (! empty($state['sort_column'])) {
            $this->tableSortColumn    = $state['sort_column'];
            $this->tableSortDirection = $state['sort_direction'] ?? 'asc';
        }

        // Search
        if (! empty($state['search'])) {
            $this->tableSearch = $state['search'];
        }

        // Toggled columns
        if (! empty($state['toggled_columns'])) {
            $this->toggledTableColumns = $state['toggled_columns'];
        }

        $this->resetPage();
    }

    // ─── Quick Save ───────────────────────────────────────────────────────────────

    /**
     * If a named user view is active, update its state in-place.
     * Otherwise open the name modal.
     */
    public function quickSaveCurrentView(): void
    {
        if ($this->activeUserViewId) {
            $view = UserView::find($this->activeUserViewId);
            if ($view && $this->canEditAdvancedView($view)) {
                $view->update(['state' => $this->captureAdvancedTableState()]);
                $this->sendAdvancedNotification('success', __('filament-advanced-tables::advanced-tables.view_updated'));
                return;
            }
        }

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
            'state'       => $this->captureAdvancedTableState(),
            'is_public'   => $this->saveViewPublic,
            'is_favorite' => $this->saveViewFavorite,
            'is_approved' => ! config('filament-advanced-tables.approval_required', false),
        ]);

        $this->activeUserViewId  = $view->id;
        $this->showSaveViewModal = false;

        $this->sendAdvancedNotification('success', __('filament-advanced-tables::advanced-tables.view_saved'));
    }

    public function cancelSaveView(): void
    {
        $this->showSaveViewModal = false;
    }

    public function deleteUserView(int $viewId): void
    {
        $view = UserView::find($viewId);

        if ($view && $this->canDeleteAdvancedView($view)) {
            if ($this->activeUserViewId === $viewId) {
                $this->activeUserViewId = null;
            }
            $view->delete();
            $this->sendAdvancedNotification('success', __('filament-advanced-tables::advanced-tables.view_deleted'));
        }
    }

    public function toggleFavoriteView(int $viewId): void
    {
        $view = UserView::find($viewId);

        if ($view && $this->canEditAdvancedView($view)) {
            $view->update(['is_favorite' => ! $view->is_favorite]);
        }
    }

    public function setDefaultView(int $viewId): void
    {
        UserView::where('resource', static::class)
            ->where('user_id', Auth::id())
            ->update(['is_default' => false]);

        UserView::find($viewId)?->update(['is_default' => true]);
        $this->sendAdvancedNotification('success', __('filament-advanced-tables::advanced-tables.view_saved'));
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

    public function applyQuickFilter(int $index): void
    {
        $filters = $this->getQuickFilters();

        if (! isset($filters[$index])) {
            return;
        }

        $qf         = $filters[$index];
        $filterData = ($qf instanceof QuickFilter) ? $qf->filters : ($qf['filters'] ?? []);

        // Merge into Filament's $tableFilters property
        $this->tableFilters = array_merge($this->tableFilters ?? [], $filterData);

        $this->resetPage();
    }

    public function clearQuickFilters(): void
    {
        $this->resetTableFiltersForm();
        $this->resetPage();
    }

    // ─── Managed Default Views ────────────────────────────────────────────────────

    public function getAdvancedDefaultView(): ?UserView
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

    protected function canUseAdvancedView(UserView $view): bool
    {
        return $view->user_id === Auth::id()
            || $view->is_public
            || $view->is_global_favorite;
    }

    protected function canEditAdvancedView(UserView $view): bool
    {
        return $view->user_id === Auth::id();
    }

    protected function canDeleteAdvancedView(UserView $view): bool
    {
        if (Auth::user()?->can('deleteAny', UserView::class)) {
            return true;
        }

        return $view->user_id === Auth::id();
    }

    // ─── State capture ────────────────────────────────────────────────────────────

    /**
     * Snapshot the current table state using Filament's actual Livewire properties.
     */
    protected function captureAdvancedTableState(): array
    {
        return [
            'filters'         => $this->tableFilters ?? [],
            'sort_column'     => $this->tableSortColumn ?? null,
            'sort_direction'  => $this->tableSortDirection ?? null,
            'toggled_columns' => $this->toggledTableColumns ?? [],
            'search'          => $this->tableSearch ?? '',
        ];
    }

    // ─── Notification helper ──────────────────────────────────────────────────────

    protected function sendAdvancedNotification(string $type, string $message): void
    {
        \Filament\Notifications\Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }
}
