<?php

namespace App\Filament\Resources\PesanKontaks\Pages;

use App\Filament\Resources\PesanKontaks\PesanKontakResource;
use Filament\Resources\Pages\ListRecords;

class ListPesanKontaks extends ListRecords
{
    protected static string $resource = PesanKontakResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
