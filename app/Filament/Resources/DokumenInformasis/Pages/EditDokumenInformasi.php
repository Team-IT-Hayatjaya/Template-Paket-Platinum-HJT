<?php

namespace App\Filament\Resources\DokumenInformasis\Pages;

use App\Filament\Resources\DokumenInformasis\DokumenInformasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDokumenInformasi extends EditRecord
{
    protected static string $resource = DokumenInformasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
