<?php

namespace App\Models;

use App\Models\Traits\BelongsToUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    use HasFactory, BelongsToUnit;

    protected $table = 'page_views';

    protected $fillable = [
        'unit_id',
        'halaman',
        'tanggal',
        'perangkat',
        'views',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'views' => 'integer',
    ];

    /**
     * Deteksi tipe perangkat dari User-Agent tanpa pihak ketiga / eksternal.
     */
    public static function detectDevice(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'desktop';
        }

        $ua = strtolower($userAgent);

        // Deteksi Tablet
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            return 'tablet';
        }

        // Deteksi Mobile
        if (preg_match('/(mobile|iphone|ipod|android|blackberry|opera mini|iemobile|wpdesktop)/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Catat kunjungan halaman publik (tanpa menyimpan IP mentah).
     */
    public static function recordView(string $path, ?string $userAgent = null): self
    {
        $normalizedPath = '/' . ltrim(parse_url($path, PHP_URL_PATH) ?? '/', '/');
        $normalizedPath = substr($normalizedPath, 0, 255);
        $today = now()->toDateString();
        $device = static::detectDevice($userAgent);

        // Update or create aggregate view per halaman, tanggal, perangkat, dan unit
        $record = static::firstOrCreate(
            [
                'unit_id' => \App\Facades\UnitContext::unitId(),
                'halaman' => $normalizedPath,
                'tanggal' => $today,
                'perangkat' => $device,
            ],
            [
                'views' => 0,
            ]
        );

        $record->increment('views');

        return $record;
    }
}
