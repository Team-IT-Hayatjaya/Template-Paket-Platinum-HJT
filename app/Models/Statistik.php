<?php

namespace App\Models;

use App\Services\ImageConversionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Statistik extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'statistik';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'periode',
        'tahun',
        'sumber',
        'isi',
        'gambar_infografis',
        'alt_text',
        'berkas_pdf',
        'berkas_spreadsheet',
        'tipe_grafik',
        'data_grafik',
        'data_tabel',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'is_active' => 'boolean',
        'urutan' => 'integer',
        'data_grafik' => 'array',
        'data_tabel' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Statistik $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul);
            }

            if ($model->isDirty('gambar_infografis') && !empty($model->gambar_infografis)) {
                $model->gambar_infografis = app(ImageConversionService::class)
                    ->convertStorageImageToWebp($model->gambar_infografis);
            }

            if (!empty($model->berkas_spreadsheet) && ($model->isDirty('berkas_spreadsheet') || empty($model->data_grafik))) {
                $parsed = app(\App\Services\StatistikDataParserService::class)->parseFile($model->berkas_spreadsheet);
                if (!empty($parsed['data_grafik'])) {
                    $model->data_grafik = $parsed['data_grafik'];
                    $model->data_tabel = $parsed['data_tabel'];
                }
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
