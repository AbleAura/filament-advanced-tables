<?php

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Ableaura\FilamentAdvancedTables\Support\PresetView;
use Ableaura\FilamentAdvancedTables\Support\QuickFilter;
use Ableaura\FilamentAdvancedTables\Filters\AdvancedFilterBuilder;
use Illuminate\Support\Facades\Auth;

// ─── PresetView ──────────────────────────────────────────────────────────────

describe('PresetView', function () {

    it('can be created with a key', function () {
        $view = PresetView::make('active');
        expect($view->key)->toBe('active');
    });

    it('supports fluent chaining', function () {
        $view = PresetView::make('recent')
            ->label('Recent')
            ->icon('heroicon-o-clock')
            ->color('success')
            ->sortBy('created_at', 'desc')
            ->default()
            ->favorite()
            ->badge('New', 'warning')
            ->filters(['status' => ['value' => 'active']])
            ->toggleColumns(['email'])
            ->columnOrder(['name', 'email']);

        expect($view->label)->toBe('Recent')
            ->and($view->icon)->toBe('heroicon-o-clock')
            ->and($view->color)->toBe('success')
            ->and($view->sortColumn)->toBe('created_at')
            ->and($view->sortDirection)->toBe('desc')
            ->and($view->default)->toBeTrue()
            ->and($view->favorite)->toBeTrue()
            ->and($view->badge)->toBe('New')
            ->and($view->badgeColor)->toBe('warning')
            ->and($view->filters)->toBe(['status' => ['value' => 'active']])
            ->and($view->toggledColumns)->toBe(['email'])
            ->and($view->columnOrder)->toBe(['name', 'email']);
    });

});

// ─── QuickFilter ─────────────────────────────────────────────────────────────

describe('QuickFilter', function () {

    it('can be created with a label', function () {
        $filter = QuickFilter::make('Active');
        expect($filter->label)->toBe('Active');
    });

    it('supports fluent chaining', function () {
        $filter = QuickFilter::make('Active')
            ->icon('heroicon-o-check')
            ->color('success')
            ->filters(['status' => ['value' => 'active']]);

        expect($filter->icon)->toBe('heroicon-o-check')
            ->and($filter->color)->toBe('success')
            ->and($filter->filters)->toBe(['status' => ['value' => 'active']]);
    });

});

// ─── UserView Model ───────────────────────────────────────────────────────────

describe('UserView', function () {

    beforeEach(function () {
        $this->user = \App\Models\User::factory()->create();
    });

    it('can create a user view', function () {
        $view = UserView::create([
            'user_id'    => $this->user->id,
            'resource'   => 'App\\Filament\\Resources\\UserResource',
            'name'       => 'My View',
            'state'      => [
                'filters'        => ['status' => ['value' => 'active']],
                'sort_column'    => 'name',
                'sort_direction' => 'asc',
            ],
            'is_approved' => true,
        ]);

        expect($view->name)->toBe('My View')
            ->and($view->getSortColumn())->toBe('name')
            ->and($view->getSortDirection())->toBe('asc')
            ->and($view->getFilters())->toBe(['status' => ['value' => 'active']]);
    });

    it('soft deletes correctly', function () {
        $view = UserView::create([
            'user_id'  => $this->user->id,
            'resource' => 'App\\Filament\\Resources\\UserResource',
            'name'     => 'To Delete',
            'state'    => [],
        ]);

        $view->delete();

        expect(UserView::find($view->id))->toBeNull()
            ->and(UserView::withTrashed()->find($view->id))->not->toBeNull();
    });

    it('returns defaults for missing state keys', function () {
        $view = UserView::make(['state' => []]);

        expect($view->getFilters())->toBe([])
            ->and($view->getToggledColumns())->toBe([])
            ->and($view->getSortColumn())->toBeNull()
            ->and($view->getSortDirection())->toBe('asc')
            ->and($view->getSearch())->toBe('')
            ->and($view->getColumnOrder())->toBe([]);
    });

});

// ─── AdvancedFilterBuilder ────────────────────────────────────────────────────

describe('AdvancedFilterBuilder', function () {

    it('can be instantiated', function () {
        $filter = AdvancedFilterBuilder::make();
        expect($filter)->toBeInstanceOf(AdvancedFilterBuilder::class);
    });

    it('accepts filterable columns', function () {
        $filter = AdvancedFilterBuilder::make()
            ->columns([
                ['column' => 'name',   'label' => 'Name',   'type' => 'text'],
                ['column' => 'status', 'label' => 'Status', 'type' => 'select'],
            ]);

        expect($filter->getFilterableColumns())->toHaveCount(2);
    });

});
