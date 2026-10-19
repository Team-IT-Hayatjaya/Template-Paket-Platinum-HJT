<?php

namespace App\Filament\Resources\WhatsappFaqs\Pages;

use App\Filament\Resources\WhatsappFaqs\WhatsappFaqResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWhatsappFaqs extends ListRecords
{
    protected static string $resource = WhatsappFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah FAQ Baru'),
        ];
    }
}
