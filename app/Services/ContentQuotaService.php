<?php

namespace App\Services;

use App\Models\Berita;
use App\Models\DokumenInformasi;
use App\Models\Halaman;
use App\Support\Package;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class ContentQuotaService
{
    /**
     * Hitung jumlah pembaruan konten (pembuatan/perubahan berita, halaman, dokumen) pada bulan berjalan.
     */
    public function getMonthlyUpdatesCount(?Carbon $date = null): int
    {
        $date = $date ?? now();
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        // 1. Coba hitung dari log aktivitas spatie
        $contentTypes = [
            Berita::class,
            Halaman::class,
            DokumenInformasi::class,
            'App\Models\Berita',
            'App\Models\Halaman',
            'App\Models\DokumenInformasi',
        ];

        $activityCount = Activity::query()
            ->whereIn('subject_type', $contentTypes)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        if ($activityCount > 0) {
            return $activityCount;
        }

        // 2. Fallback jika model diupdate langsung: hitung record yang dibuat/diubah bulan ini
        $beritaCount = Berita::whereBetween('updated_at', [$start, $end])->count();
        $halamanCount = Halaman::whereBetween('updated_at', [$start, $end])->count();
        $dokumenCount = DokumenInformasi::whereBetween('updated_at', [$start, $end])->count();

        return $beritaCount + $halamanCount + $dokumenCount;
    }

    /**
     * Dapatkan status kuota konten bulan ini sesuai paket langganan.
     */
    public function getQuotaStatus(): array
    {
        $used = $this->getMonthlyUpdatesCount();
        $limit = Package::limit('content_updates_per_month');
        $limitLabel = $limit === null ? 'Tanpa Batas' : (string) $limit;

        $percentage = $limit ? (int) min(100, round(($used / max(1, $limit)) * 100)) : 0;

        return [
            'used' => $used,
            'limit' => $limit,
            'limit_label' => $limitLabel,
            'display' => "{$used} dari {$limitLabel}",
            'package' => Package::current(),
            'is_exceeded' => $limit !== null && $used > $limit,
            'remaining' => $limit === null ? null : max(0, $limit - $used),
            'percentage' => $percentage,
            'is_near_limit' => $limit !== null && $used >= (int) ceil($limit * 0.8),
            'period_label' => now()->translatedFormat('F Y'),
        ];
    }
}
