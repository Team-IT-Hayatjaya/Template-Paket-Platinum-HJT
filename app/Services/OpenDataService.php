<?php

namespace App\Services;

use App\Models\Dataset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class OpenDataService
{
    /**
     * Validasi berkas CSV dan ekstrak informasi kolom, jumlah baris, serta data pratinjau.
     */
    public function validateAndParseCsv(string|UploadedFile $file): array
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : (Storage::disk('public')->exists($file) ? Storage::disk('public')->path($file) : $file);

        if (! file_exists($path) || ! is_readable($path)) {
            throw new \InvalidArgumentException('Berkas CSV tidak ditemukan atau tidak dapat dibaca.');
        }

        $handle = fopen($path, 'r');
        if (! $handle) {
            throw new \RuntimeException('Gagal membuka berkas CSV.');
        }

        // Baca baris pertama untuk deteksi delimiter dan bersihkan UTF-8 BOM
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new \InvalidArgumentException('Berkas CSV kosong.');
        }

        // Hapus UTF-8 BOM jika ada
        $bom = pack('H*', 'EFBBBF');
        $firstLine = preg_replace("/^{$bom}/", '', $firstLine);

        // Deteksi delimiter (, atau ;)
        $commaCount = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');
        $delimiter = $semicolonCount > $commaCount ? ';' : ',';

        // Reset pointer
        rewind($handle);

        $headers = [];
        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (! $rawHeaders || empty($rawHeaders)) {
            fclose($handle);
            throw new \InvalidArgumentException('Header berkas CSV tidak valid.');
        }

        foreach ($rawHeaders as $idx => $h) {
            $cleaned = trim(preg_replace("/^{$bom}/", '', (string) $h));
            $headers[] = $cleaned ?: 'Kolom_' . ($idx + 1);
        }

        $rows = [];
        $rowCount = 0;
        $numericStats = array_fill_keys($headers, 0);

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            // Lewati baris kosong
            if (count($row) === 1 && trim((string) $row[0]) === '') {
                continue;
            }

            $rowCount++;
            $assocRow = [];
            foreach ($headers as $colIdx => $colName) {
                $val = trim((string) ($row[$colIdx] ?? ''));
                $assocRow[$colName] = $val;

                // Cek apakah numerik
                if ($val !== '' && is_numeric(str_replace([',', '.'], '', $val))) {
                    $numericStats[$colName]++;
                }
            }

            if ($rowCount <= 100) {
                $rows[] = $assocRow;
            }
        }

        fclose($handle);

        // Kolom yang mayoritas barisnya berisi angka dikategorikan sebagai numerik
        $numericColumns = [];
        $labelColumns = [];

        foreach ($headers as $col) {
            if ($rowCount > 0 && ($numericStats[$col] / $rowCount) >= 0.7) {
                $numericColumns[] = $col;
            } else {
                $labelColumns[] = $col;
            }
        }

        return [
            'delimiter' => $delimiter,
            'headers' => $headers,
            'row_count' => $rowCount,
            'numeric_columns' => $numericColumns,
            'label_columns' => $labelColumns,
            'preview' => array_slice($rows, 0, 10),
            'data' => $rows,
        ];
    }

    /**
     * Baca data penuh dari dataset yang tersimpan.
     */
    public function getDatasetData(Dataset $dataset, ?int $limit = null): array
    {
        $filePath = Storage::disk('public')->path($dataset->berkas_csv);
        if (! file_exists($filePath)) {
            return [];
        }

        $parsed = $this->validateAndParseCsv($filePath);
        $data = $parsed['data'];

        if ($limit !== null && $limit > 0) {
            return array_slice($data, 0, $limit);
        }

        return $data;
    }

    /**
     * Konversi data dataset ke format JSON array untuk ekspor/API.
     */
    public function exportJson(Dataset $dataset): array
    {
        return [
            'metadata' => [
                'id' => $dataset->id,
                'judul' => $dataset->judul,
                'slug' => $dataset->slug,
                'kategori' => $dataset->kategori,
                'sumber' => $dataset->sumber,
                'periode' => $dataset->periode,
                'penanggung_jawab' => $dataset->penanggung_jawab,
                'tanggal_pembaruan' => $dataset->tanggal_pembaruan?->toDateString(),
                'version' => $dataset->version,
                'unit' => $dataset->unit?->nama ?? 'Portal Utama',
                'row_count' => $dataset->row_count,
            ],
            'schema' => $dataset->schema_columns ?? [],
            'data' => $this->getDatasetData($dataset),
        ];
    }

    /**
     * Siapkan konfigurasi data untuk visualisasi Chart.js.
     */
    public function getChartConfig(Dataset $dataset): array
    {
        $data = $this->getDatasetData($dataset, 50);
        if (empty($data)) {
            return [
                'type' => $dataset->chart_type ?? 'bar',
                'data' => ['labels' => [], 'datasets' => []],
                'options' => ['responsive' => true],
            ];
        }

        $xCol = $dataset->chart_x_column;
        $yCol = $dataset->chart_y_column;

        // Auto-detect jika belum diset
        $headers = array_keys($data[0] ?? []);
        if (empty($xCol) && count($headers) >= 1) {
            $xCol = $headers[0];
        }
        if (empty($yCol) && count($headers) >= 2) {
            $yCol = $headers[1];
        }

        $labels = [];
        $values = [];

        foreach ($data as $row) {
            $labels[] = (string) ($row[$xCol] ?? '');
            $rawVal = $row[$yCol] ?? 0;
            // Bersihkan format ribuan jika ada
            $cleanedVal = (float) str_replace(['.', ','], '', (string) $rawVal);
            $values[] = $cleanedVal;
        }

        $type = in_array($dataset->chart_type, ['bar', 'line', 'pie'], true) ? $dataset->chart_type : 'bar';

        $palette = [
            '#1e40af', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe',
            '#059669', '#10b981', '#34d399', '#f59e0b', '#ef4444',
            '#8b5cf6', '#ec4899', '#14b8a6', '#6366f1', '#f97316'
        ];

        $backgroundColor = $type === 'pie' ? array_slice($palette, 0, count($values)) : '#3b82f6';
        $borderColor = $type === 'pie' ? '#ffffff' : '#1e40af';

        return [
            'type' => $type,
            'x_label' => $xCol,
            'y_label' => $yCol,
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $yCol ?: $dataset->judul,
                        'data' => $values,
                        'backgroundColor' => $backgroundColor,
                        'borderColor' => $borderColor,
                        'borderWidth' => 1.5,
                        'fill' => $type === 'line' ? false : true,
                        'tension' => 0.3,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => $type === 'pie',
                        'position' => 'bottom',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => $dataset->judul . ' (' . ($yCol ?: 'Nilai') . ')',
                        'font' => ['size' => 14, 'weight' => 'bold'],
                    ],
                ],
            ],
        ];
    }
}
