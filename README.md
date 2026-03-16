# Filament Advanced Tables

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ableaura/filament-advanced-tables.svg?style=flat-square)](https://packagist.org/packages/ableaura/filament-advanced-tables)
[![Total Downloads](https://img.shields.io/packagist/dt/ableaura/filament-advanced-tables.svg?style=flat-square)](https://packagist.org/packages/ableaura/filament-advanced-tables)
[![License](https://img.shields.io/packagist/l/ableaura/filament-advanced-tables.svg?style=flat-square)](https://packagist.org/packages/ableaura/filament-advanced-tables)

> Supercharge your Filament tables with user-customizable views, quick filters, multi-column sorting, advanced search, a favorites bar, view manager, and more — open-source and free.

---

## Features

| Feature | Description |
|---|---|
| **User Views** | Users save their own filter + sort + column configurations |
| **Preset Views** | Developers define views in code, deployed for all users |
| **Favorites Bar** | Pin views above the table for one-click access |
| **Quick Save** | Save the current state in one click |
| **View Manager** | Modal to manage, rename, favorite, and delete views |
| **Managed Default Views** | Set a default view per resource per user |
| **Multi-Sort** | Sort by multiple columns simultaneously |
| **Quick Filters** | Pre-configured filter shortcut buttons |
| **Advanced Search** | Search with operators: contains, starts_with, equals, etc. |
| **Advanced Filter Builder** | Build AND/OR multi-condition queries at runtime |
| **User Views Resource** | Admin panel to approve, manage, and pin global views |
| **Loading Skeleton** | Animated table overlay during Livewire reloads |
| **Approval System** | Optional admin approval gate for public views |
| **Global Favorites** | Admins pin views for all users |
| **Policy Integration** | Full Laravel policy support |

---

## Requirements

- PHP 8.2+
- Laravel 11+
- Filament 3.x or 4.x
- MySQL 5.7.8+ or PostgreSQL

---

## Installation

```bash
composer require ableaura/filament-advanced-tables
```

Publish config and run migrations:

```bash
php artisan vendor:publish --tag="filament-advanced-tables-config"
php artisan vendor:publish --tag="filament-advanced-tables-migrations"
php artisan migrate
```

Optionally publish views for customization:

```bash
php artisan vendor:publish --tag="filament-advanced-tables-views"
```

---

## Register the Plugin

In your Filament Panel provider:

```php
use Ableaura\FilamentAdvancedTables\FilamentAdvancedTablesPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentAdvancedTablesPlugin::make()
                ->theme('filament')           // filament | github | links | links-simple | tabs | tabs-simple
                ->multiSort()
                ->quickFilters()
                ->advancedSearch()
                ->advancedFilterBuilder()
                ->favoritesBar()
                ->quickSave()
                ->viewManager()
                ->userViewsResource()
                ->requireApproval(false)
                ->allowPublicViews(),
        ]);
}
```

---

## Add to a Resource

### 1. Add the trait

```php
use Ableaura\FilamentAdvancedTables\Concerns\HasAdvancedTables;

class ListUsers extends ListRecords
{
    use HasAdvancedTables;
}
```

### 2. Define Preset Views (optional)

```php
use Ableaura\FilamentAdvancedTables\Support\PresetView;

protected function getPresetViews(): array
{
    return [
        PresetView::make('active')
            ->label('Active Users')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->filters(['status' => ['value' => 'active']])
            ->sortBy('name', 'asc')
            ->default(),

        PresetView::make('recent')
            ->label('Recent Signups')
            ->icon('heroicon-o-clock')
            ->sortBy('created_at', 'desc')
            ->badge('New', 'warning'),
    ];
}
```

### 3. Define Quick Filters (optional)

```php
use Ableaura\FilamentAdvancedTables\Support\QuickFilter;

protected function getQuickFilters(): array
{
    return [
        QuickFilter::make('Active')->icon('heroicon-o-check')->filters(['status' => ['value' => 'active']]),
        QuickFilter::make('Inactive')->icon('heroicon-o-x-mark')->filters(['status' => ['value' => 'inactive']]),
        QuickFilter::make('This Month')->icon('heroicon-o-calendar')->filters(['created_this_month' => ['isActive' => true]]),
    ];
}
```

### 4. Add the Advanced Filter Builder to your table

```php
use Ableaura\FilamentAdvancedTables\Filters\AdvancedFilterBuilder;

public static function table(Table $table): Table
{
    return $table
        ->filters([
            // ... your regular filters ...
            AdvancedFilterBuilder::make()
                ->columns([
                    ['column' => 'name',       'label' => 'Name',       'type' => 'text'],
                    ['column' => 'status',     'label' => 'Status',     'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
                    ['column' => 'created_at', 'label' => 'Created At', 'type' => 'date'],
                ]),
        ]);
}
```

### 5. Use Multi-Sort

```php
use Ableaura\FilamentAdvancedTables\Concerns\AppliesMultiSort;

class ListUsers extends ListRecords
{
    use HasAdvancedTables, AppliesMultiSort;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->tap(fn ($q) => $this->applyMultiSortToQuery($q));
    }
}
```

### 6. Use Advanced Search

```php
use Ableaura\FilamentAdvancedTables\Concerns\AppliesAdvancedSearch;

class ListUsers extends ListRecords
{
    use HasAdvancedTables, AppliesAdvancedSearch;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->tap(
            fn ($q) => $this->applyAdvancedSearchToQuery($q, ['name', 'email', 'phone'])
        );
    }
}
```

---

## Including Components in Your Views

Add these Livewire components to your resource list view or table header hooks:

```blade
{{-- Favorites bar (above the table) --}}
<livewire:filament-advanced-tables::favorites-bar :resource="static::class" />

{{-- View manager button --}}
<livewire:filament-advanced-tables::view-manager :resource="static::class" />

{{-- Multi-sort dropdown --}}
<livewire:filament-advanced-tables::multi-sort :available-columns="[...]" />

{{-- Quick filters --}}
<livewire:filament-advanced-tables::quick-filters :quick-filters="$this->getQuickFilters()" />

{{-- Advanced search --}}
<livewire:filament-advanced-tables::advanced-search :searchable-columns="[...]" />

{{-- Advanced filter builder --}}
<livewire:filament-advanced-tables::advanced-filter-builder :filterable-columns="[...]" />

{{-- Loading skeleton overlay --}}
<x-filament-advanced-tables::loading-skeleton />
```

---

## Artisan Commands

```bash
# Scaffold a preset view class
php artisan advanced-tables:make-preset-view ActiveUsers --resource=UserResource

# Prune soft-deleted views older than 30 days (dry run)
php artisan advanced-tables:prune-views --days=30 --dry-run

# Prune including unapproved views
php artisan advanced-tables:prune-views --unapproved
```

---

## Configuration Reference

```php
// config/filament-advanced-tables.php

return [
    'user_model'       => \App\Models\User::class,
    'auth_guard'       => 'web',
    'approval_required' => false,
    'allow_public_views' => true,
    'theme'            => 'filament', // filament|github|links|links-simple|tabs|tabs-simple

    'features' => [
        'user_views'              => true,
        'preset_views'            => true,
        'favorites_bar'           => true,
        'quick_save'              => true,
        'view_manager'            => true,
        'managed_default_views'   => true,
        'multi_sort'              => true,
        'quick_filters'           => true,
        'advanced_search'         => true,
        'advanced_filter_builder' => true,
        'user_views_resource'     => true,
        'loading_skeleton'        => true,
    ],
];
```

---

## Authorization

Register the policy in your `AuthServiceProvider`:

```php
use Ableaura\FilamentAdvancedTables\Models\UserView;
use Ableaura\FilamentAdvancedTables\Policies\UserViewPolicy;

protected $policies = [
    UserView::class => UserViewPolicy::class,
];
```

---

## Multi-Tenancy

Enable in config:

```php
'tenancy' => [
    'enabled' => true,
    'model'   => \App\Models\Team::class,
    'column'  => 'team_id',
],
```

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

---

## License

MIT — free to use in any project. See [LICENSE](LICENSE.md).
