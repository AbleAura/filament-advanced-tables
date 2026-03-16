<?php

namespace Ableaura\FilamentAdvancedTables\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Add to your ListRecords page alongside HasAdvancedTables.
 *
 * Option A — automatic (recommended): just use the trait, it hooks in via bootAppliesMultiSort()
 * Option B — manual: call $this->applyMultiSortToQuery($query) inside modifyTableQueryUsing()
 */
trait AppliesMultiSort
{
    public function bootAppliesMultiSort(): void
    {
        // Filament 3: hook into the table query automatically
        $this->modifyTableQueryUsing(function (Builder $query) {
            return $this->applyMultiSortToQuery($query);
        });
    }

    public function applyMultiSortToQuery(Builder $query): Builder
    {
        foreach ($this->multiSortColumns ?? [] as $entry) {
            if (! str_contains($entry, ':')) {
                continue;
            }
            [$column, $direction] = explode(':', $entry, 2);
            $query->orderBy($column, $direction);
        }

        return $query;
    }
}
