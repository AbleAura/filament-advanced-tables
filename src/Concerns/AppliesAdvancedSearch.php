<?php

namespace Ableaura\FilamentAdvancedTables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait AppliesAdvancedSearch
{
    /**
     * Apply advanced search to the Eloquent query.
     * Call inside getTableQuery() or modifyTableQueryUsing().
     *
     * @param array<string> $searchableColumns Column names to search across when no specific column is chosen.
     */
    public function applyAdvancedSearchToQuery(Builder $query, array $searchableColumns = []): Builder
    {
        $searchQuery = $this->advancedSearchQuery ?? '';

        if (blank($searchQuery)) {
            return $query;
        }

        $column   = $this->advancedSearchColumn ?? null;
        $operator = $this->advancedSearchOperator ?? 'contains';

        // If a specific column is selected, search only that column.
        if ($column) {
            return $this->applySearchOperator($query, $column, $operator, $searchQuery);
        }

        // Otherwise, search across all searchable columns with OR logic.
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
            'contains'     => $query->where($column, 'like', "%{$value}%"),
            'not_contains' => $query->where($column, 'not like', "%{$value}%"),
            'starts_with'  => $query->where($column, 'like', "{$value}%"),
            'ends_with'    => $query->where($column, 'like', "%{$value}"),
            'equals'       => $query->where($column, '=', $value),
            default        => $query->where($column, 'like', "%{$value}%"),
        };
    }
}
