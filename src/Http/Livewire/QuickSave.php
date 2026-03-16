<?php

namespace Ableaura\FilamentAdvancedTables\Http\Livewire;

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class QuickSave extends Component
{
    public string $resource;
    public ?int $activeViewId = null;

    public bool $showNameModal = false;
    public string $newViewName = '';

    protected $listeners = [
        'advanced-tables::view-applied' => 'onViewApplied',
    ];

    public function mount(string $resource, ?int $activeViewId = null): void
    {
        $this->resource     = $resource;
        $this->activeViewId = $activeViewId;
    }

    public function onViewApplied(?int $viewId, ?string $presetKey): void
    {
        $this->activeViewId = $viewId;
    }

    /**
     * If a named view is already active, update its state.
     * Otherwise, open the name modal to create a new one.
     */
    public function quickSave(): void
    {
        if ($this->activeViewId) {
            $view = UserView::find($this->activeViewId);
            if ($view && $view->user_id === Auth::id()) {
                $this->dispatch('advanced-tables::do-quick-save', viewId: $this->activeViewId);
                return;
            }
        }

        // No active view — ask for a name
        $this->newViewName = '';
        $this->showNameModal = true;
    }

    public function confirmSaveNew(): void
    {
        $this->validate(['newViewName' => 'required|string|max:255']);

        $this->dispatch('advanced-tables::do-quick-save-new', name: $this->newViewName);
        $this->showNameModal = false;
    }

    public function cancelNameModal(): void
    {
        $this->showNameModal = false;
    }

    public function render()
    {
        return view('filament-advanced-tables::livewire.quick-save');
    }
}
