<?php

namespace App\Filament\Resources\Halamen\Pages;

use App\Filament\Resources\Halamen\HalamanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHalaman extends CreateRecord
{
    protected static string $resource = HalamanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
