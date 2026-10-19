<?php

namespace App\Models;

use App\Models\Traits\BelongsToUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Dataset extends Model
{
    use HasFactory, LogsActivity, BelongsToUnit;

    protected $table = 'datasets';

    protected $fillable = [
        'unit_id',
        'judul',
        'slug',
        'deskripsi',
        'kategori',
        'sumber',
        'periode',
        'penanggung_jawab',
        'berkas_csv',
        'tanggal_pembaruan',
        'chart_type',
        'chart_x_column',
        'chart_y_column',
        'row_count',
        'schema_columns',
        'version',
        'total_unduhan',
        'total_views',
        'is_published',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'schema_columns' => 'array',
            'tanggal_pembaruan' => 'date',
            'is_published' => 'boolean',
            'row_count' => 'integer',
            'version' => 'integer',
            'total_unduhan' => 'integer',
            'total_views' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Dataset $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul);
            }
            if (empty($model->tanggal_pembaruan)) {
                $model->tanggal_pembaruan = now()->toDateString();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('open_data')
            ->setDescriptionForEvent(fn (string $eventName) => "Dataset {$this->judul} {$eventName}");
    }

    /**
     * Scope dataset terbit publik.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Relasi ke riwayat versi dataset.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(DatasetVersion::class, 'dataset_id')->orderByDesc('version');
    }

    /**
     * Relasi ke pembuat dataset.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
