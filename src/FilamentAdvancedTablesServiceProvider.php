<?php

namespace Ableaura\FilamentAdvancedTables;

use Ableaura\FilamentAdvancedTables\Commands\MakePresetViewCommand;
use Ableaura\FilamentAdvancedTables\Commands\PruneUserViewsCommand;
use Ableaura\FilamentAdvancedTables\Http\Livewire\AdvancedFilterBuilder;
use Ableaura\FilamentAdvancedTables\Http\Livewire\AdvancedSearch;
use Ableaura\FilamentAdvancedTables\Http\Livewire\FavoritesBar;
use Ableaura\FilamentAdvancedTables\Http\Livewire\MultiSort;
use Ableaura\FilamentAdvancedTables\Http\Livewire\QuickFilters;
use Ableaura\FilamentAdvancedTables\Http\Livewire\QuickSave;
use Ableaura\FilamentAdvancedTables\Http\Livewire\ViewManager;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAdvancedTablesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-advanced-tables')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_user_views_table',
            ])
            ->hasTranslations()
            ->hasCommands([
                MakePresetViewCommand::class,
                PruneUserViewsCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(FilamentAdvancedTablesPlugin::class);
    }

    public function packageBooted(): void
    {
        // Register Livewire components under the plugin namespace
        Livewire::component('filament-advanced-tables::favorites-bar',       FavoritesBar::class);
        Livewire::component('filament-advanced-tables::view-manager',        ViewManager::class);
        Livewire::component('filament-advanced-tables::multi-sort',          MultiSort::class);
        Livewire::component('filament-advanced-tables::advanced-search',     AdvancedSearch::class);
        Livewire::component('filament-advanced-tables::quick-filters',       QuickFilters::class);
        Livewire::component('filament-advanced-tables::quick-save',         QuickSave::class);
        Livewire::component('filament-advanced-tables::advanced-filter-builder', AdvancedFilterBuilder::class);
    }
}
