<?php

namespace Ableaura\FilamentAdvancedTables\Support;

class QuickFilter
{
    public string $label;
    public ?string $icon = null;
    public ?string $color = null;
    public array $filters = [];

    public static function make(string $label): static
    {
        $instance = new static();
        $instance->label = $label;
        return $instance;
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

    public function filters(array $filters): static
    {
        $this->filters = $filters;
        return $this;
    }
}
