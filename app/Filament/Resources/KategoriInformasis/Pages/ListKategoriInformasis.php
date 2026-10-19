<?php

namespace App\Filament\Resources\KategoriInformasis\Pages;

use App\Filament\Resources\KategoriInformasis\KategoriInformasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKategoriInformasis extends ListRecords
{
    protected static string $resource = KategoriInformasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Kategori'),
        ];
    }
}
