<?php

namespace App\Models;

use App\Models\Traits\BelongsToUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Banner extends Model
{
    use HasFactory, LogsActivity, BelongsToUnit;

    protected $table = 'banners';

    protected $fillable = [
        'unit_id',
        'judul',
        'deskripsi',
        'gambar',
        'alt_text',
        'tautan',
        'label_tautan',
        'tipe',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Banner $model) {
            if ($model->isDirty('gambar') && !empty($model->gambar)) {
                $model->gambar = app(\App\Services\ImageConversionService::class)
                    ->convertStorageImageToWebp($model->gambar);
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
