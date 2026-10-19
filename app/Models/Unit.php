<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Unit extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'units';

    protected $fillable = [
        'nama',
        'slug',
        'subdomain',
        'singkatan',
        'deskripsi',
        'alamat',
        'email',
        'telepon',
        'logo',
        'warna_tema',
        'is_active',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'subdomain', 'is_active'])
            ->logOnlyDirty()
            ->useLogName('unit_management')
            ->setDescriptionForEvent(fn (string $eventName) => "Unit {$this->nama} ({$this->subdomain}) {$eventName}");
    }

    /**
     * Scope unit aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relasi ke Pengguna unit ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'unit_id');
    }

    /**
     * Relasi ke Berita unit ini.
     */
    public function berita(): HasMany
    {
        return $this->hasMany(Berita::class, 'unit_id');
    }

    /**
     * Relasi ke Galeri unit ini.
     */
    public function galeri(): HasMany
    {
        return $this->hasMany(Galeri::class, 'unit_id');
    }

    /**
     * Relasi ke Layanan unit ini.
     */
    public function layanan(): HasMany
    {
        return $this->hasMany(Layanan::class, 'unit_id');
    }

    /**
     * Relasi ke Dokumen Informasi unit ini.
     */
    public function dokumenInformasi(): HasMany
    {
        return $this->hasMany(DokumenInformasi::class, 'unit_id');
    }

    /**
     * Relasi ke Halaman statis unit ini.
     */
    public function halaman(): HasMany
    {
        return $this->hasMany(Halaman::class, 'unit_id');
    }

    /**
     * Relasi ke Banner unit ini.
     */
    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'unit_id');
    }

    /**
     * Relasi ke Menu Navigasi unit ini.
     */
    public function navigationMenus(): HasMany
    {
        return $this->hasMany(NavigationMenu::class, 'unit_id');
    }

    /**
     * Relasi ke Page View analitik unit ini.
     */
    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class, 'unit_id');
    }
}
