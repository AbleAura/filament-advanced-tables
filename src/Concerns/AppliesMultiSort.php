<?php

namespace Ableaura\FilamentAdvancedTables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait AppliesMultiSort
{
    /**
     * Apply multi-sort columns to the Eloquent query.
     *
     * Call this inside getTableQuery() or modifyTableQueryUsing():
     *
     *   protected function getTableQuery(): Builder
     *   {
     *       return parent::getTableQuery()->tap(fn ($q) => $this->applyMultiSortToQuery($q));
     *   }
     */
    public function applyMultiSortToQuery(Builder $query): Builder
    {
        foreach ($this->multiSortColumns ?? [] as $entry) {
            [$column, $direction] = explode(':', $entry, 2);
            $query->orderBy($column, $direction);
        }

        return $query;
    }
}
