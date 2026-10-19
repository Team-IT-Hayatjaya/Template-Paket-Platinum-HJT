<?php

namespace App\Filament\Resources\WhatsappFaqs\Pages;

use App\Filament\Resources\WhatsappFaqs\WhatsappFaqResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWhatsappFaq extends EditRecord
{
    protected static string $resource = WhatsappFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
