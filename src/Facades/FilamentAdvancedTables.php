<?php

namespace Ableaura\FilamentAdvancedTables\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ableaura\FilamentAdvancedTables\FilamentAdvancedTables
 */
class FilamentAdvancedTables extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ableaura\FilamentAdvancedTables\FilamentAdvancedTablesPlugin::class;
    }
}
