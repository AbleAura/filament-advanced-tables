<?php

/**
 * ============================================================
 * EXAMPLE: Filament Resource using filament-advanced-tables
 * ============================================================
 *
 * This file is NOT part of the package itself.
 * Drop it into your Laravel project as:
 *   app/Filament/Resources/UserResource.php
 *
 * It demonstrates every feature of the plugin wired together.
 */

namespace App\Filament\Resources;

use Ableaura\FilamentAdvancedTables\Concerns\AppliesAdvancedSearch;
use Ableaura\FilamentAdvancedTables\Concerns\AppliesMultiSort;
use Ableaura\FilamentAdvancedTables\Concerns\HasAdvancedTables;
use Ableaura\FilamentAdvancedTables\Filters\AdvancedFilterBuilder;
use Ableaura\FilamentAdvancedTables\Support\PresetView;
use Ableaura\FilamentAdvancedTables\Support\QuickFilter;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    // ── Form ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\Select::make('status')
                ->options(['active' => 'Active', 'inactive' => 'Inactive', 'banned' => 'Banned'])
                ->required(),
        ]);
    }

    // ── Table ─────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable()->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger'  => 'banned',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive', 'banned' => 'Banned']),

                Tables\Filters\Filter::make('created_this_month')
                    ->label('Created This Month')
                    ->query(fn (Builder $query) => $query->whereMonth('created_at', now()->month)),

                // ✨ Advanced Filter Builder — add AND/OR multi-condition queries
                AdvancedFilterBuilder::make()
                    ->columns([
                        ['column' => 'name',       'label' => 'Name',       'type' => 'text'],
                        ['column' => 'email',      'label' => 'Email',      'type' => 'text'],
                        ['column' => 'status',     'label' => 'Status',     'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive', 'banned' => 'Banned']],
                        ['column' => 'created_at', 'label' => 'Created At', 'type' => 'date'],
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// List Page — this is where all the Advanced Tables magic goes
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Filament\Resources\UserResource\Pages;

use Ableaura\FilamentAdvancedTables\Concerns\AppliesAdvancedSearch;
use Ableaura\FilamentAdvancedTables\Concerns\AppliesMultiSort;
use Ableaura\FilamentAdvancedTables\Concerns\HasAdvancedTables;
use Ableaura\FilamentAdvancedTables\Support\PresetView;
use Ableaura\FilamentAdvancedTables\Support\QuickFilter;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    // ✨ Step 1 — add the trait
    use HasAdvancedTables;
    use AppliesMultiSort;
    use AppliesAdvancedSearch;

    // ✨ Step 2 — define preset views (developer-defined, deployed to all users)
    protected function getPresetViews(): array
    {
        return [
            PresetView::make('all')
                ->label('All Users')
                ->icon('heroicon-o-users')
                ->default(),

            PresetView::make('active')
                ->label('Active')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->filters(['status' => ['value' => 'active']])
                ->sortBy('name', 'asc')
                ->favorite(),

            PresetView::make('inactive')
                ->label('Inactive')
                ->icon('heroicon-o-pause-circle')
                ->color('warning')
                ->filters(['status' => ['value' => 'inactive']]),

            PresetView::make('banned')
                ->label('Banned')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->filters(['status' => ['value' => 'banned']])
                ->badge('Review', 'danger'),

            PresetView::make('recent')
                ->label('Recent')
                ->icon('heroicon-o-clock')
                ->sortBy('created_at', 'desc')
                ->filters(['created_this_month' => ['isActive' => true]])
                ->badge('New', 'info'),
        ];
    }

    // ✨ Step 3 — define quick filters (one-click shortcuts)
    protected function getQuickFilters(): array
    {
        return [
            QuickFilter::make('Active')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->filters(['status' => ['value' => 'active']]),

            QuickFilter::make('Inactive')
                ->icon('heroicon-o-pause-circle')
                ->filters(['status' => ['value' => 'inactive']]),

            QuickFilter::make('Banned')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->filters(['status' => ['value' => 'banned']]),

            QuickFilter::make('This Month')
                ->icon('heroicon-o-calendar')
                ->filters(['created_this_month' => ['isActive' => true]]),
        ];
    }

    // ✨ Step 4 — apply multi-sort and advanced search to the query
    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->tap(fn ($q) => $this->applyMultiSortToQuery($q))
            ->tap(fn ($q) => $this->applyAdvancedSearchToQuery($q, ['name', 'email']));
    }
}
