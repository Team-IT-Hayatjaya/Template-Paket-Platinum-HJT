<?php

namespace App\Filament\Resources\WhatsappTickets\Pages;

use App\Filament\Resources\WhatsappTickets\WhatsappTicketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWhatsappTicket extends EditRecord
{
    protected static string $resource = WhatsappTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['petugas_id'] = auth()->id();
        if (($data['status'] ?? '') === 'selesai' && empty($this->getRecord()->ditanggapi_at)) {
            $data['ditanggapi_at'] = now();
        }

        return $data;
    }
}
