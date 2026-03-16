<?php

namespace Ableaura\FilamentAdvancedTables\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Add to your ListRecords page alongside HasAdvancedTables.
 *
 * Define which columns are searchable by overriding getAdvancedSearchColumns():
 *
 *   protected function getAdvancedSearchColumns(): array
 *   {
 *       return ['name', 'email', 'phone'];
 *   }
 */
trait AppliesAdvancedSearch
{
    public function bootAppliesAdvancedSearch(): void
    {
        $this->modifyTableQueryUsing(function (Builder $query) {
            return $this->applyAdvancedSearchToQuery($query, $this->getAdvancedSearchColumns());
        });
    }

    /**
     * Override in your ListRecords page to define searchable columns.
     * @return array<string>
     */
    protected function getAdvancedSearchColumns(): array
    {
        return [];
    }

    public function applyAdvancedSearchToQuery(Builder $query, array $searchableColumns = []): Builder
    {
        $searchQuery = $this->advancedSearchQuery ?? '';

        if (blank($searchQuery)) {
            return $query;
        }

        $column   = $this->advancedSearchColumn ?? null;
        $operator = $this->advancedSearchOperator ?? 'contains';

        if ($column) {
            return $this->applySearchOperator($query, $column, $operator, $searchQuery);
        }

        if (empty($searchableColumns)) {
            return $query;
        }

        return $query->where(function (Builder $sub) use ($searchableColumns, $operator, $searchQuery) {
            foreach ($searchableColumns as $col) {
                $sub->orWhere(function (Builder $inner) use ($col, $operator, $searchQuery) {
                    $this->applySearchOperator($inner, $col, $operator, $searchQuery);
                });
            }
        });
    }

    private function applySearchOperator(Builder $query, string $column, string $operator, string $value): Builder
    {
        return match ($operator) {
            'not_contains' => $query->where($column, 'not like', "%{$value}%"),
            'starts_with'  => $query->where($column, 'like', "{$value}%"),
            'ends_with'    => $query->where($column, 'like', "%{$value}"),
            'equals'       => $query->where($column, '=', $value),
            default        => $query->where($column, 'like', "%{$value}%"), // contains
        };
    }
}
