<?php

namespace App\Filament\Resources\OptionalResource\Pages;

use App\Filament\Resources\OptionalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOptional extends EditRecord
{
    protected static string $resource = OptionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
