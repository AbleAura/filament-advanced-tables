<?php

namespace Ableaura\FilamentAdvancedTables\Http\Livewire;

use Livewire\Component;

class MultiSort extends Component
{
    /** @var array<array{column: string, direction: string}> */
    public array $sorts = [];

    public array $availableColumns = [];

    protected $listeners = [
        'advanced-tables::multi-sort-updated' => 'onSortUpdated',
    ];

    public function mount(array $availableColumns = [], array $currentSorts = []): void
    {
        $this->availableColumns = $availableColumns;
        $this->sorts = $currentSorts;
    }

    public function addSort(): void
    {
        $this->sorts[] = ['column' => '', 'direction' => 'asc'];
    }

    public function removeSort(int $index): void
    {
        array_splice($this->sorts, $index, 1);
        $this->sorts = array_values($this->sorts);
        $this->applySort();
    }

    public function clearSort(): void
    {
        $this->sorts = [];
        $this->dispatch('advanced-tables::apply-multi-sort', sorts: []);
    }

    public function applySort(): void
    {
        $validSorts = array_values(array_filter($this->sorts, fn ($s) => ! blank($s['column'] ?? '')));
        $this->dispatch('advanced-tables::apply-multi-sort', sorts: $validSorts);
    }

    public function onSortUpdated(array $sorts): void
    {
        $this->sorts = $sorts;
    }

    public function render()
    {
        return view('filament-advanced-tables::livewire.multi-sort');
    }
}
