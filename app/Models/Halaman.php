<?php

namespace App\Models;

use App\Models\Traits\BelongsToUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Halaman extends Model
{
    use HasFactory, LogsActivity, BelongsToUnit;

    protected $table = 'halaman';

    protected $fillable = [
        'unit_id',
        'judul',
        'slug',
        'ringkasan',
        'isi',
        'gambar_utama',
        'berkas_pdf',
        'alt_text',
        'urutan',
        'is_active',
        'show_in_menu',
        'meta_title',
        'meta_description',
        'created_by',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Halaman $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul);
            }

            if ($model->isDirty('gambar_utama') && !empty($model->gambar_utama)) {
                $model->gambar_utama = app(\App\Services\ImageConversionService::class)
                    ->convertStorageImageToWebp($model->gambar_utama);
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
