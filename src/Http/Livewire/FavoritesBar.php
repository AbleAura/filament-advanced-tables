<?php

namespace Ableaura\FilamentAdvancedTables\Http\Livewire;

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class FavoritesBar extends Component
{
    public string $resource;
    public ?int $activeViewId = null;
    public ?string $activePresetKey = null;

    protected $listeners = [
        'advanced-tables::view-applied' => 'onViewApplied',
    ];

    public function mount(string $resource, ?int $activeViewId = null, ?string $activePresetKey = null): void
    {
        $this->resource = $resource;
        $this->activeViewId = $activeViewId;
        $this->activePresetKey = $activePresetKey;
    }

    public function onViewApplied(int|string|null $viewId, ?string $presetKey): void
    {
        $this->activeViewId = is_int($viewId) ? $viewId : null;
        $this->activePresetKey = $presetKey;
    }

    public function getFavoriteViews(): array
    {
        if (! Auth::check()) {
            return [];
        }

        return UserView::query()
            ->where('resource', $this->resource)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('user_id', Auth::id())
                        ->where('is_favorite', true);
                })->orWhere('is_global_favorite', true);
            })
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function selectView(int $viewId): void
    {
        $this->dispatch('advanced-tables::apply-user-view', viewId: $viewId);
    }

    public function render()
    {
        return view('filament-advanced-tables::livewire.favorites-bar', [
            'favoriteViews' => $this->getFavoriteViews(),
        ]);
    }
}
