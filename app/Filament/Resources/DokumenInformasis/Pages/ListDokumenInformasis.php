<?php

namespace App\Filament\Resources\DokumenInformasis\Pages;

use App\Filament\Resources\DokumenInformasis\DokumenInformasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDokumenInformasis extends ListRecords
{
    protected static string $resource = DokumenInformasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
