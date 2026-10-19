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

class Galeri extends Model
{
    use HasFactory, LogsActivity, BelongsToUnit;

    protected $table = 'galeri';

    protected $fillable = [
        'unit_id',
        'judul',
        'slug',
        'tipe',
        'kategori',
        'deskripsi',
        'gambar',
        'alt_text',
        'tanggal',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Galeri $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul);
            }

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

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderByDesc('tanggal');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
