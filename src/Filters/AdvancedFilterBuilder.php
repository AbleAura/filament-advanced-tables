<?php

namespace Ableaura\FilamentAdvancedTables\Filters;

use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Advanced Filter Builder — allows users to construct
 * multi-condition queries at runtime using AND / OR logic.
 *
 * Usage in table():
 *
 *   ->filters([
 *       AdvancedFilterBuilder::make(),
 *   ])
 */
class AdvancedFilterBuilder extends BaseFilter
{
    protected string $view = 'filament-advanced-tables::filters.advanced-filter-builder';

    /**
     * Column definitions available in the builder.
     * Each entry: ['column' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => [...]]
     */
    protected array $filterableColumns = [];

    public static function make(?string $name = 'advanced_filter_builder'): static
    {
        return parent::make($name);
    }

    public function columns(array $columns): static
    {
        $this->filterableColumns = $columns;
        return $this;
    }

    public function getFilterableColumns(): array
    {
        return $this->filterableColumns;
    }

    public function apply(Builder $query, array $data = []): Builder
    {
        $groups = $data['groups'] ?? [];

        foreach ($groups as $group) {
            $logic = $group['logic'] ?? 'and'; // 'and' | 'or'
            $conditions = $group['conditions'] ?? [];

            $method = $logic === 'or' ? 'orWhere' : 'where';

            $query->{$method}(function (Builder $sub) use ($conditions) {
                foreach ($conditions as $condition) {
                    $column   = $condition['column'] ?? null;
                    $operator = $condition['operator'] ?? 'equals';
                    $value    = $condition['value'] ?? null;

                    if (! $column) {
                        continue;
                    }

                    match ($operator) {
                        'equals'        => $sub->where($column, '=', $value),
                        'not_equals'    => $sub->where($column, '!=', $value),
                        'contains'      => $sub->where($column, 'like', "%{$value}%"),
                        'not_contains'  => $sub->where($column, 'not like', "%{$value}%"),
                        'starts_with'   => $sub->where($column, 'like', "{$value}%"),
                        'ends_with'     => $sub->where($column, 'like', "%{$value}"),
                        'greater_than'  => $sub->where($column, '>', $value),
                        'less_than'     => $sub->where($column, '<', $value),
                        'is_null'       => $sub->whereNull($column),
                        'is_not_null'   => $sub->whereNotNull($column),
                        'in'            => $sub->whereIn($column, (array) $value),
                        'not_in'        => $sub->whereNotIn($column, (array) $value),
                        default         => null,
                    };
                }
            });
        }

        return $query;
    }
}
