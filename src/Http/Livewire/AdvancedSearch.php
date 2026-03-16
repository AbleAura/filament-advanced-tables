<?php

namespace Ableaura\FilamentAdvancedTables\Http\Livewire;

use Livewire\Component;

class AdvancedSearch extends Component
{
    public string $query = '';
    public ?string $column = null;
    public string $operator = 'contains';
    public array $searchableColumns = [];

    public function mount(array $searchableColumns = [], string $query = '', ?string $column = null, string $operator = 'contains'): void
    {
        $this->searchableColumns = $searchableColumns;
        $this->query = $query;
        $this->column = $column;
        $this->operator = $operator;
    }

    public function updatedQuery(): void
    {
        $this->applySearch();
    }

    public function updatedColumn(): void
    {
        $this->applySearch();
    }

    public function updatedOperator(): void
    {
        $this->applySearch();
    }

    public function applySearch(): void
    {
        $this->dispatch('advanced-tables::apply-advanced-search', [
            'query'    => $this->query,
            'column'   => $this->column,
            'operator' => $this->operator,
        ]);
    }

    public function clearSearch(): void
    {
        $this->query = '';
        $this->column = null;
        $this->operator = 'contains';
        $this->applySearch();
    }

    public function getOperators(): array
    {
        return [
            'contains'     => __('filament-advanced-tables::advanced-tables.operator_contains'),
            'not_contains' => __('filament-advanced-tables::advanced-tables.operator_not_contains'),
            'starts_with'  => __('filament-advanced-tables::advanced-tables.operator_starts_with'),
            'ends_with'    => __('filament-advanced-tables::advanced-tables.operator_ends_with'),
            'equals'       => __('filament-advanced-tables::advanced-tables.operator_equals'),
        ];
    }

    public function render()
    {
        return view('filament-advanced-tables::livewire.advanced-search', [
            'operators' => $this->getOperators(),
        ]);
    }
}
