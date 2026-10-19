<?php

namespace App\Filament\Pages;

use App\Services\MonthlyReportService;
use Filament\Actions\Action;
use Filament\Pages\Page;

class LaporanBulanan extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Laporan Rekapitulasi PPID';

    protected static ?string $title = 'Laporan Rekapitulasi PPID & Statistik';

    protected static string | \UnitEnum | null $navigationGroup = 'Layanan PPID';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.laporan-bulanan';

    public int $year;

    public int $month;

    public function mount(): void
    {
        $this->year = (int) date('Y');
        $this->month = (int) date('n');
    }

    public function getReportData(): array
    {
        return app(MonthlyReportService::class)->getReportData($this->year, $this->month);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Unduh Laporan PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn (): string => route('admin.laporan-bulanan.pdf', [
                    'year' => $this->year,
                    'month' => $this->month,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
