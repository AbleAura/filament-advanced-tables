<?php

namespace Ableaura\FilamentAdvancedTables\Http\Livewire;

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ViewManager extends Component
{
    public string $resource;
    public bool $showModal = false;
    public string $viewName = '';
    public bool $isPublic = false;
    public bool $isFavorite = false;
    public ?string $selectedIcon = null;
    public ?string $selectedColor = null;

    // For editing an existing view
    public ?int $editingViewId = null;

    public function mount(string $resource): void
    {
        $this->resource = $resource;
    }

    public function openSaveModal(?int $viewId = null): void
    {
        if ($viewId) {
            $view = UserView::find($viewId);
            if ($view && $view->user_id === Auth::id()) {
                $this->editingViewId = $viewId;
                $this->viewName = $view->name;
                $this->isPublic = $view->is_public;
                $this->isFavorite = $view->is_favorite;
                $this->selectedIcon = $view->icon;
                $this->selectedColor = $view->color;
            }
        } else {
            $this->reset(['editingViewId', 'viewName', 'isPublic', 'isFavorite', 'selectedIcon', 'selectedColor']);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function saveView(): void
    {
        $this->validate([
            'viewName' => 'required|string|max:255',
        ]);

        $this->dispatch('advanced-tables::save-view', [
            'name'       => $this->viewName,
            'isPublic'   => $this->isPublic,
            'isFavorite' => $this->isFavorite,
            'icon'       => $this->selectedIcon,
            'color'      => $this->selectedColor,
            'viewId'     => $this->editingViewId,
        ]);

        $this->closeModal();
    }

    public function getUserViews(): array
    {
        if (! Auth::check()) {
            return [];
        }

        return UserView::query()
            ->where('resource', $this->resource)
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function deleteView(int $viewId): void
    {
        $view = UserView::find($viewId);

        if ($view && $view->user_id === Auth::id()) {
            $view->delete();
            $this->dispatch('advanced-tables::view-deleted', viewId: $viewId);
        }
    }

    public function toggleFavorite(int $viewId): void
    {
        $view = UserView::find($viewId);

        if ($view && $view->user_id === Auth::id()) {
            $view->update(['is_favorite' => ! $view->is_favorite]);
        }
    }

    public function setDefault(int $viewId): void
    {
        // Remove existing defaults for this user + resource
        UserView::where('resource', $this->resource)
            ->where('user_id', Auth::id())
            ->update(['is_default' => false]);

        UserView::find($viewId)?->update(['is_default' => true]);
    }

    public function render()
    {
        return view('filament-advanced-tables::livewire.view-manager', [
            'userViews' => $this->getUserViews(),
        ]);
    }
}
