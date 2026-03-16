<?php

namespace Ableaura\FilamentAdvancedTables;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class FilamentAdvancedTablesPlugin implements Plugin
{
    protected bool $hasUserViews         = true;
    protected bool $hasPresetViews       = true;
    protected bool $hasFavoritesBar      = true;
    protected bool $hasQuickSave         = true;
    protected bool $hasViewManager       = true;
    protected bool $hasManagedDefaultViews = true;
    protected bool $hasMultiSort         = true;
    protected bool $hasQuickFilters      = true;
    protected bool $hasAdvancedSearch    = true;
    protected bool $hasAdvancedFilterBuilder = true;
    protected bool $hasUserViewsResource = false;  // opt-in — adds a nav item
    protected bool $hasLoadingSkeleton   = true;

    protected string $theme        = 'filament';
    protected ?string $userModel   = null;
    protected string $authGuard    = 'web';
    protected bool $approvalRequired  = false;
    protected bool $allowPublicViews  = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function getId(): string
    {
        return 'filament-advanced-tables';
    }

    public function register(Panel $panel): void
    {
        if ($this->hasUserViewsResource) {
            $panel->resources([
                \Ableaura\FilamentAdvancedTables\Resources\UserViewResource::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        // Inject the favorites bar above the table on all list-record pages.
        // The blade component checks whether the current page uses HasAdvancedTables
        // and silently renders nothing if not.
        if ($this->hasFavoritesBar) {
            $panel->renderHook(
                PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE,
                fn (): string => Blade::render('<x-filament-advanced-tables::favorites-bar-hook />')
            );
        }
    }

    // ─── Fluent Setters ──────────────────────────────────────────────────────────

    public function userViews(bool $condition = true): static
    {
        $this->hasUserViews = $condition;
        return $this;
    }

    public function presetViews(bool $condition = true): static
    {
        $this->hasPresetViews = $condition;
        return $this;
    }

    public function favoritesBar(bool $condition = true): static
    {
        $this->hasFavoritesBar = $condition;
        return $this;
    }

    public function quickSave(bool $condition = true): static
    {
        $this->hasQuickSave = $condition;
        return $this;
    }

    public function viewManager(bool $condition = true): static
    {
        $this->hasViewManager = $condition;
        return $this;
    }

    public function managedDefaultViews(bool $condition = true): static
    {
        $this->hasManagedDefaultViews = $condition;
        return $this;
    }

    public function multiSort(bool $condition = true): static
    {
        $this->hasMultiSort = $condition;
        return $this;
    }

    public function quickFilters(bool $condition = true): static
    {
        $this->hasQuickFilters = $condition;
        return $this;
    }

    public function advancedSearch(bool $condition = true): static
    {
        $this->hasAdvancedSearch = $condition;
        return $this;
    }

    public function advancedFilterBuilder(bool $condition = true): static
    {
        $this->hasAdvancedFilterBuilder = $condition;
        return $this;
    }

    public function userViewsResource(bool $condition = true): static
    {
        $this->hasUserViewsResource = $condition;
        return $this;
    }

    public function loadingSkeleton(bool $condition = true): static
    {
        $this->hasLoadingSkeleton = $condition;
        return $this;
    }

    public function theme(string $theme): static
    {
        $this->theme = $theme;
        return $this;
    }

    public function userModel(string $model): static
    {
        $this->userModel = $model;
        return $this;
    }

    public function authGuard(string $guard): static
    {
        $this->authGuard = $guard;
        return $this;
    }

    public function requireApproval(bool $condition = true): static
    {
        $this->approvalRequired = $condition;
        return $this;
    }

    public function allowPublicViews(bool $condition = true): static
    {
        $this->allowPublicViews = $condition;
        return $this;
    }

    // ─── Getters ─────────────────────────────────────────────────────────────────

    public function hasUserViews(): bool              { return $this->hasUserViews; }
    public function hasPresetViews(): bool            { return $this->hasPresetViews; }
    public function hasFavoritesBar(): bool           { return $this->hasFavoritesBar; }
    public function hasQuickSave(): bool              { return $this->hasQuickSave; }
    public function hasViewManager(): bool            { return $this->hasViewManager; }
    public function hasManagedDefaultViews(): bool    { return $this->hasManagedDefaultViews; }
    public function hasMultiSort(): bool              { return $this->hasMultiSort; }
    public function hasQuickFilters(): bool           { return $this->hasQuickFilters; }
    public function hasAdvancedSearch(): bool         { return $this->hasAdvancedSearch; }
    public function hasAdvancedFilterBuilder(): bool  { return $this->hasAdvancedFilterBuilder; }
    public function hasUserViewsResource(): bool      { return $this->hasUserViewsResource; }
    public function hasLoadingSkeleton(): bool        { return $this->hasLoadingSkeleton; }
    public function getTheme(): string                { return $this->theme; }
    public function getUserModel(): string            { return $this->userModel ?? config('auth.providers.users.model', \App\Models\User::class); }
    public function getAuthGuard(): string            { return $this->authGuard; }
    public function isApprovalRequired(): bool        { return $this->approvalRequired; }
    public function allowsPublicViews(): bool         { return $this->allowPublicViews; }
}
