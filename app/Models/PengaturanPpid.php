<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanPpid extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_ppid';

    protected $fillable = [
        'kode_instansi',
        'nama_instansi',
        'sub_nama_instansi',
        'nama_ppid',
        'warna_utama',
        'warna_sekunder',
        'logo_path',
        'favicon_path',
        'alamat',
        'telepon',
        'email',
        'email_atasan_ppid',
        'jam_layanan',
        'maklumat_pelayanan',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'kode_instansi' => 'LK',
                'nama_instansi' => 'Pemerintah Kecamatan Loa Kulu',
                'sub_nama_instansi' => 'Kecamatan Loa Kulu',
                'nama_ppid' => 'PPID Utama Loa Kulu',
                'warna_utama' => '#14304F',
                'warna_sekunder' => '#C9A227',
                'email' => 'ppid@loakulu.go.id',
            ]
        );
    }

    /**
     * Dapatkan URL berkas logo atau null jika belum ada.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path && file_exists(public_path($this->logo_path))) {
            return asset($this->logo_path);
        }

        return null;
    }

    /**
     * Dapatkan URL favicon atau null jika belum ada.
     */
    public function getFaviconUrlAttribute(): ?string
    {
        if ($this->favicon_path && file_exists(public_path($this->favicon_path))) {
            return asset($this->favicon_path);
        }

        return asset('favicon.ico');
    }
}
