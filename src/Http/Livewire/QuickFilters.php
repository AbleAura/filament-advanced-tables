<?php

namespace Ableaura\FilamentAdvancedTables\Http\Livewire;

use Livewire\Component;

class QuickFilters extends Component
{
    public array $quickFilters = [];
    public ?int $activeIndex = null;

    public function mount(array $quickFilters = []): void
    {
        $this->quickFilters = $quickFilters;
    }

    public function applyFilter(int $index): void
    {
        if ($this->activeIndex === $index) {
            // Toggle off
            $this->activeIndex = null;
            $this->dispatch('advanced-tables::clear-quick-filter');
        } else {
            $this->activeIndex = $index;
            $this->dispatch('advanced-tables::apply-quick-filter', index: $index, filters: $this->quickFilters[$index]['filters'] ?? []);
        }
    }

    public function clearFilters(): void
    {
        $this->activeIndex = null;
        $this->dispatch('advanced-tables::clear-quick-filter');
    }

    public function render()
    {
        return view('filament-advanced-tables::livewire.quick-filters');
    }
}
