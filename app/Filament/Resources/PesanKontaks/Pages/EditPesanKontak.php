<?php

namespace App\Filament\Resources\PesanKontaks\Pages;

use App\Filament\Resources\PesanKontaks\PesanKontakResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPesanKontak extends EditRecord
{
    protected static string $resource = PesanKontakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
