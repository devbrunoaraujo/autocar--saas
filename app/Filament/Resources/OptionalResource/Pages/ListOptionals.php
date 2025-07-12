<?php

namespace App\Filament\Resources\OptionalResource\Pages;

use App\Filament\Resources\OptionalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOptionals extends ListRecords
{
    protected static string $resource = OptionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
