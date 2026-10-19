<?php

namespace App\Filament\Resources\DokumenInformasis\Pages;

use App\Filament\Resources\DokumenInformasis\DokumenInformasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDokumenInformasi extends CreateRecord
{
    protected static string $resource = DokumenInformasiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
