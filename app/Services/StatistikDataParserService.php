<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StatistikDataParserService
{
    /**
     * Parse spreadsheet berkas (xlsx, xls, csv) menjadi array data grafik dan tabel.
     *
     * @param string $storageRelativePath (contoh: 'statistik-data/contoh.xlsx' di disk public)
     * @param string $disk
     * @return array{data_grafik: array|null, data_tabel: array|null}
     */
    public function parseFile(string $storageRelativePath, string $disk = 'public'): array
    {
        $fullPath = Storage::disk($disk)->path($storageRelativePath);

        if (!file_exists($fullPath)) {
            return [
                'data_grafik' => null,
                'data_tabel' => null,
            ];
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rawRows = $sheet->toArray(null, true, true, false);

            return $this->processRawRows($rawRows);
        } catch (\Throwable $e) {
            // Fallback manual CSV jika PhpSpreadsheet mengalami kendala
            if (str_ends_with(strtolower($fullPath), '.csv')) {
                return $this->parseCsvFallback($fullPath);
            }

            return [
                'data_grafik' => null,
                'data_tabel' => null,
            ];
        }
    }

    /**
     * Olah baris mentah spreadsheet menjadi format grafik & tabel.
     */
    public function processRawRows(array $rawRows): array
    {
        // Bersihkan baris kosong
        $cleanRows = [];
        foreach ($rawRows as $row) {
            if (empty($row)) {
                continue;
            }
            $nonEmpty = array_filter($row, fn($v) => !is_null($v) && trim((string)$v) !== '');
            if (!empty($nonEmpty)) {
                $cleanRows[] = array_map(fn($v) => is_null($v) ? '' : trim((string)$v), $row);
            }
        }

        if (count($cleanRows) < 2) {
            return [
                'data_grafik' => null,
                'data_tabel' => null,
            ];
        }

        // Baris 1 sebagai Headers
        $headers = array_shift($cleanRows);
        $headers = array_values($headers);

        $labels = [];
        $numericSeries = [];

        // Inisialisasi seri untuk tiap kolom numerik setelah kolom pertama
        for ($col = 1; $col < count($headers); $col++) {
            $numericSeries[$col] = [
                'label' => $headers[$col] ?: 'Data Seri ' . $col,
                'data' => [],
            ];
        }

        $allRows = [];
        foreach ($cleanRows as $row) {
            $label = $row[0] ?? '';
            $labels[] = (string) $label;
            $allRows[] = $row;

            for ($col = 1; $col < count($headers); $col++) {
                $val = $row[$col] ?? 0;
                // Bersihkan format angka seperti 1.000 atau 1,5
                $cleanVal = str_replace(['.', ','], ['', '.'], (string)$val);
                if (is_numeric($cleanVal)) {
                    $numericSeries[$col]['data'][] = (float) $cleanVal;
                } else {
                    $numericSeries[$col]['data'][] = 0;
                }
            }
        }

        // Palet warna elegan untuk chart instansi (Navy, Gold, Emas, Cyan, Zamrud, dsb.)
        $colorPalette = [
            'rgba(30, 58, 138, 0.85)',   // Navy Primary
            'rgba(217, 119, 6, 0.85)',   // Amber Gold
            'rgba(5, 150, 105, 0.85)',   // Emerald Hijau
            'rgba(2, 132, 199, 0.85)',   // Sky Blue
            'rgba(124, 58, 237, 0.85)',  // Violet
            'rgba(225, 29, 72, 0.85)',   // Rose
            'rgba(202, 138, 4, 0.85)',   // Yellow Dark
        ];

        $borderPalette = [
            'rgb(30, 58, 138)',
            'rgb(217, 119, 6)',
            'rgb(5, 150, 105)',
            'rgb(2, 132, 199)',
            'rgb(124, 58, 237)',
            'rgb(225, 29, 72)',
            'rgb(202, 138, 4)',
        ];

        $datasets = [];
        $colorIdx = 0;
        foreach ($numericSeries as $series) {
            $c = $colorPalette[$colorIdx % count($colorPalette)];
            $b = $borderPalette[$colorIdx % count($borderPalette)];

            $datasets[] = [
                'label' => $series['label'],
                'data' => $series['data'],
                'backgroundColor' => $c,
                'borderColor' => $b,
                'borderWidth' => 2,
                'borderRadius' => 6,
            ];
            $colorIdx++;
        }

        // Hitung ringkasan statistik
        $primaryNumbers = $datasets[0]['data'] ?? [];
        $total = array_sum($primaryNumbers);
        $avg = count($primaryNumbers) > 0 ? round($total / count($primaryNumbers), 2) : 0;
        $max = count($primaryNumbers) > 0 ? max($primaryNumbers) : 0;
        $min = count($primaryNumbers) > 0 ? min($primaryNumbers) : 0;

        $dataGrafik = [
            'labels' => $labels,
            'datasets' => $datasets,
            'summary' => [
                'total' => $total,
                'rata_rata' => $avg,
                'tertinggi' => $max,
                'terendah' => $min,
                'jumlah_baris' => count($labels),
            ],
        ];

        $dataTabel = [
            'headers' => $headers,
            'rows' => $allRows,
        ];

        return [
            'data_grafik' => $dataGrafik,
            'data_tabel' => $dataTabel,
        ];
    }

    /**
     * Fallback parser untuk berkas CSV sederhana.
     */
    protected function parseCsvFallback(string $fullPath): array
    {
        $handle = fopen($fullPath, 'r');
        if (!$handle) {
            return ['data_grafik' => null, 'data_tabel' => null];
        }

        $rawRows = [];
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $rawRows[] = $data;
        }
        fclose($handle);

        return $this->processRawRows($rawRows);
    }
}
