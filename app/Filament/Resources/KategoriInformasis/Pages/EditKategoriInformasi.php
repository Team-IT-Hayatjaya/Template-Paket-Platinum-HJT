<?php

namespace App\Filament\Resources\KategoriInformasis\Pages;

use App\Filament\Resources\KategoriInformasis\KategoriInformasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKategoriInformasi extends EditRecord
{
    protected static string $resource = KategoriInformasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
