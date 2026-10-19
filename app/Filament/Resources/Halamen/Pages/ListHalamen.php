<?php

namespace App\Filament\Resources\Halamen\Pages;

use App\Filament\Resources\Halamen\HalamanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHalamen extends ListRecords
{
    protected static string $resource = HalamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
