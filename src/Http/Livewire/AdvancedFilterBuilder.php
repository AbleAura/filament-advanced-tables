<?php

namespace Ableaura\FilamentAdvancedTables\Http\Livewire;

use Livewire\Component;

class AdvancedFilterBuilder extends Component
{
    /** @var array<array{logic: string, conditions: array}> */
    public array $groups = [];

    public array $filterableColumns = [];
    public bool $isOpen = false;

    public function mount(array $filterableColumns = []): void
    {
        $this->filterableColumns = $filterableColumns;
    }

    // ─── Groups ───────────────────────────────────────────────────────────────────

    public function addGroup(): void
    {
        $this->groups[] = [
            'logic'      => 'and',
            'conditions' => [
                ['column' => '', 'operator' => 'contains', 'value' => ''],
            ],
        ];
    }

    public function removeGroup(int $groupIndex): void
    {
        array_splice($this->groups, $groupIndex, 1);
        $this->groups = array_values($this->groups);
    }

    // ─── Conditions ───────────────────────────────────────────────────────────────

    public function addCondition(int $groupIndex): void
    {
        $this->groups[$groupIndex]['conditions'][] = [
            'column'   => '',
            'operator' => 'contains',
            'value'    => '',
        ];
    }

    public function removeCondition(int $groupIndex, int $conditionIndex): void
    {
        array_splice($this->groups[$groupIndex]['conditions'], $conditionIndex, 1);
        $this->groups[$groupIndex]['conditions'] = array_values($this->groups[$groupIndex]['conditions']);

        // Remove group if no conditions left
        if (count($this->groups[$groupIndex]['conditions']) === 0) {
            $this->removeGroup($groupIndex);
        }
    }

    // ─── Apply / Clear ────────────────────────────────────────────────────────────

    public function applyFilters(): void
    {
        $this->dispatch('advanced-tables::apply-filter-builder', groups: $this->groups);
        $this->isOpen = false;
    }

    public function clearFilters(): void
    {
        $this->groups = [];
        $this->dispatch('advanced-tables::apply-filter-builder', groups: []);
        $this->isOpen = false;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public function getOperators(): array
    {
        return [
            'contains'     => __('filament-advanced-tables::advanced-tables.op_contains'),
            'not_contains' => __('filament-advanced-tables::advanced-tables.op_not_contains'),
            'equals'       => __('filament-advanced-tables::advanced-tables.op_equals'),
            'not_equals'   => __('filament-advanced-tables::advanced-tables.op_not_equals'),
            'starts_with'  => __('filament-advanced-tables::advanced-tables.op_starts_with'),
            'ends_with'    => __('filament-advanced-tables::advanced-tables.op_ends_with'),
            'greater_than' => __('filament-advanced-tables::advanced-tables.op_greater_than'),
            'less_than'    => __('filament-advanced-tables::advanced-tables.op_less_than'),
            'is_null'      => __('filament-advanced-tables::advanced-tables.op_is_null'),
            'is_not_null'  => __('filament-advanced-tables::advanced-tables.op_is_not_null'),
        ];
    }

    public function hasActiveFilters(): bool
    {
        return count($this->groups) > 0;
    }

    public function getActiveConditionCount(): int
    {
        return collect($this->groups)->sum(fn ($g) => count($g['conditions'] ?? []));
    }

    public function render()
    {
        return view('filament-advanced-tables::livewire.advanced-filter-builder', [
            'operators' => $this->getOperators(),
        ]);
    }
}
