<?php

namespace App\Filament\Resources\Halamen\Pages;

use App\Filament\Resources\Halamen\HalamanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHalaman extends EditRecord
{
    protected static string $resource = HalamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
