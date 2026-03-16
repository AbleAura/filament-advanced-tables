<?php

namespace Ableaura\FilamentAdvancedTables\Resources\Pages;

use Ableaura\FilamentAdvancedTables\Resources\UserViewResource;
use Filament\Resources\Pages\ListRecords;

class ListUserViews extends ListRecords
{
    protected static string $resource = UserViewResource::class;
}
