<?php

namespace Ableaura\FilamentAdvancedTables\Resources\Pages;

use Ableaura\FilamentAdvancedTables\Resources\UserViewResource;
use Filament\Resources\Pages\EditRecord;

class EditUserView extends EditRecord
{
    protected static string $resource = UserViewResource::class;
}
