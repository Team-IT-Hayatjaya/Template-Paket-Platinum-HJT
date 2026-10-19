<?php

namespace App\Filament\Resources\PermohonanInformasis\Pages;

use App\Filament\Resources\PermohonanInformasis\PermohonanInformasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPermohonanInformasi extends EditRecord
{
    protected static string $resource = PermohonanInformasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $record->loadMissing('status');
        $statusSlug = $record->status?->slug;

        if (in_array($statusSlug, ['dijawab', 'ditolak', 'selesai']) && !$record->tanggal_dijawab) {
            $record->update(['tanggal_dijawab' => now()]);
        }

        app(\App\Services\PpidService::class)->kirimNotifikasiStatus($record, $record->catatan_internal);
    }
}
