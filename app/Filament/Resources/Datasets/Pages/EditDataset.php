<?php

namespace App\Filament\Resources\Datasets\Pages;

use App\Filament\Resources\Datasets\DatasetResource;
use App\Models\DatasetVersion;
use App\Services\OpenDataService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditDataset extends EditRecord
{
    protected static string $resource = DatasetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $original = $this->getRecord();

        // Jika berkas CSV diubah / diunggah ulang
        if (! empty($data['berkas_csv']) && $data['berkas_csv'] !== $original->berkas_csv) {
            // Catat versi lama ke dataset_versions
            DatasetVersion::create([
                'dataset_id' => $original->id,
                'version' => $original->version,
                'berkas_csv' => $original->berkas_csv,
                'catatan_perubahan' => "Pembaruan dataset ke versi " . ($original->version + 1) . ".0",
                'row_count' => $original->row_count,
                'created_by' => auth()->id(),
            ]);

            // Naikkan nomor versi
            $data['version'] = $original->version + 1;
            $data['tanggal_pembaruan'] = now()->toDateString();

            try {
                $service = app(OpenDataService::class);
                $filePath = Storage::disk('public')->path($data['berkas_csv']);
                $parsed = $service->validateAndParseCsv($filePath);

                $data['row_count'] = $parsed['row_count'];
                $data['schema_columns'] = $parsed['headers'];
            } catch (\Throwable $e) {}
        }

        return $data;
    }
}
