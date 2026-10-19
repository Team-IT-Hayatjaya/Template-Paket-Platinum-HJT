<?php

namespace App\Filament\Widgets;

use App\Models\PermohonanInformasi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PpidChartWidget extends ChartWidget
{
    protected ?string $heading = 'Statistik Tren Permohonan Informasi Publik';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->translatedFormat('F Y');
            $months[] = $monthLabel;

            $count = PermohonanInformasi::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $counts[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permohonan Masuk',
                    'data' => $counts,
                    'borderColor' => '#14304F',
                    'backgroundColor' => 'rgba(20, 48, 79, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
