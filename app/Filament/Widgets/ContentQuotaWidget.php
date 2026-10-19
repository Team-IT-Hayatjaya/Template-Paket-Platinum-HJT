<?php

namespace App\Filament\Widgets;

use App\Models\Berita;
use App\Models\DokumenInformasi;
use App\Models\Halaman;
use App\Services\ContentQuotaService;
use App\Support\Package;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentQuotaWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $quota = app(ContentQuotaService::class)->getQuotaStatus();
        $limitVal = Package::limit('content_updates_per_month');
        $limitText = $limitVal !== null ? (string) $limitVal : 'Tanpa Batas';
        $display = "{$quota['used']} dari {$limitText}";

        $totalBerita = Berita::count();
        $totalHalaman = Halaman::count();
        $totalDokumen = DokumenInformasi::count();

        $quotaStat = Stat::make('Pembaruan Konten Bulan Ini', $display)
            ->description("Paket " . ucfirst($quota['package']) . " (Batas: {$limitText})")
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color($quota['is_exceeded'] ? 'warning' : 'primary');

        // Paket Gold: pemantau kuota (sisa, persentase, dan progres visual)
        if (Package::has('content_quota_monitor') && $limitVal !== null) {
            $quotaStat
                ->value("{$quota['used']} / {$limitText}")
                ->description($quota['is_exceeded']
                    ? "Kuota terlampaui ({$quota['percentage']}%) · Paket " . ucfirst($quota['package'])
                    : "Sisa {$quota['remaining']} pembaruan · {$quota['percentage']}% terpakai")
                ->descriptionIcon($quota['is_exceeded'] ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-chart-bar')
                ->chart([0, $quota['percentage'], 100])
                ->color($quota['is_exceeded'] ? 'danger' : ($quota['is_near_limit'] ? 'warning' : 'success'));
        }

        return [
            $quotaStat,

            Stat::make('Publikasi Berita', (string) $totalBerita)
                ->description('Total berita terbit & draft')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('success'),

            Stat::make('Halaman & Dokumen', ($totalHalaman + $totalDokumen) . " Berkas")
                ->description("{$totalHalaman} Halaman, {$totalDokumen} Dokumen")
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),
        ];
    }
}
