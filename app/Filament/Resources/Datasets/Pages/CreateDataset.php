<?php

namespace App\Filament\Resources\Datasets\Pages;

use App\Filament\Resources\Datasets\DatasetResource;
use App\Services\OpenDataService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateDataset extends CreateRecord
{
    protected static string $resource = DatasetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        // Validasi dan parsing berkas CSV yang baru diunggah
        if (! empty($data['berkas_csv'])) {
            try {
                $service = app(OpenDataService::class);
                $filePath = Storage::disk('public')->path($data['berkas_csv']);
                $parsed = $service->validateAndParseCsv($filePath);

                $data['row_count'] = $parsed['row_count'];
                $data['schema_columns'] = $parsed['headers'];

                if (empty($data['chart_x_column']) && ! empty($parsed['label_columns'])) {
                    $data['chart_x_column'] = $parsed['label_columns'][0];
                } elseif (empty($data['chart_x_column']) && ! empty($parsed['headers'])) {
                    $data['chart_x_column'] = $parsed['headers'][0];
                }

                if (empty($data['chart_y_column']) && ! empty($parsed['numeric_columns'])) {
                    $data['chart_y_column'] = $parsed['numeric_columns'][0];
                } elseif (empty($data['chart_y_column']) && count($parsed['headers']) > 1) {
                    $data['chart_y_column'] = $parsed['headers'][1];
                }
            } catch (\Throwable $e) {
                // Abaikan jika berkas belum tersimpan sempurna
            }
        }

        return $data;
    }
}
