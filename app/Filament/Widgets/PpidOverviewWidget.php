<?php

namespace App\Filament\Widgets;

use App\Models\DokumenInformasi;
use App\Models\Keberatan;
use App\Models\PermohonanInformasi;
use App\Models\PesanKontak;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PpidOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPermohonan = PermohonanInformasi::count();
        $pendingPermohonan = PermohonanInformasi::whereHas('status', function ($q) {
            $q->whereIn('slug', ['menunggu', 'diproses']);
        })->count();

        $totalKeberatan = Keberatan::count();
        $pendingKeberatan = Keberatan::whereIn('status', ['diterima', 'diproses'])->count();

        $totalDokumen = DokumenInformasi::where('is_active', true)->count();
        $unreadPesan = PesanKontak::where('is_read', false)->count();

        return [
            Stat::make('Permohonan PPID', (string) $totalPermohonan)
                ->description($pendingPermohonan > 0 ? "{$pendingPermohonan} permohonan butuh tindak lanjut" : "Semua permohonan telah ditanggapi")
                ->descriptionIcon($pendingPermohonan > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-badge')
                ->color($pendingPermohonan > 0 ? 'warning' : 'success')
                ->chart([3, 5, 2, 7, 4, $totalPermohonan]),

            Stat::make('Keberatan Informasi', (string) $totalKeberatan)
                ->description($pendingKeberatan > 0 ? "{$pendingKeberatan} keberatan perlu telaah Atasan PPID" : "Nihil keberatan pending")
                ->descriptionIcon($pendingKeberatan > 0 ? 'heroicon-m-scale' : 'heroicon-m-check-circle')
                ->color($pendingKeberatan > 0 ? 'danger' : 'success'),

            Stat::make('Daftar Informasi Publik (DIP)', (string) $totalDokumen)
                ->description('Dokumen publik terbit & siap diunduh')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),

            Stat::make('Aspirasi & Pesan Warga', (string) PesanKontak::count())
                ->description($unreadPesan > 0 ? "{$unreadPesan} pesan baru belum dibaca" : "Kotak masuk bersih")
                ->descriptionIcon($unreadPesan > 0 ? 'heroicon-m-envelope' : 'heroicon-m-envelope-open')
                ->color($unreadPesan > 0 ? 'info' : 'gray'),
        ];
    }
}
