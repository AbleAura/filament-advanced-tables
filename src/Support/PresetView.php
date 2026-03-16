<?php

namespace Ableaura\FilamentAdvancedTables\Support;

class PresetView
{
    public string $key;
    public string $label;
    public ?string $icon = null;
    public ?string $color = null;
    public ?string $badge = null;
    public ?string $badgeColor = null;
    public array $filters = [];
    public ?string $sortColumn = null;
    public string $sortDirection = 'asc';
    public array $toggledColumns = [];
    public array $columnOrder = [];
    public bool $default = false;
    public bool $favorite = false;

    public static function make(string $key): static
    {
        $instance = new static();
        $instance->key = $key;
        return $instance;
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function badge(string $badge, ?string $color = null): static
    {
        $this->badge = $badge;
        $this->badgeColor = $color;
        return $this;
    }

    public function filters(array $filters): static
    {
        $this->filters = $filters;
        return $this;
    }

    public function sortBy(string $column, string $direction = 'asc'): static
    {
        $this->sortColumn = $column;
        $this->sortDirection = $direction;
        return $this;
    }

    public function toggleColumns(array $columns): static
    {
        $this->toggledColumns = $columns;
        return $this;
    }

    public function columnOrder(array $order): static
    {
        $this->columnOrder = $order;
        return $this;
    }

    public function default(bool $condition = true): static
    {
        $this->default = $condition;
        return $this;
    }

    public function favorite(bool $condition = true): static
    {
        $this->favorite = $condition;
        return $this;
    }
}
