<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Traits\BelongsToUnit;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class DokumenInformasi extends Model
{
    use HasFactory, LogsActivity, BelongsToUnit;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    protected $table = 'dokumen_informasi';

    protected $fillable = [
        'unit_id',
        'kategori_id',
        'judul',
        'slug',
        'ringkasan',
        'tahun',
        'file_path',
        'tipe_file',
        'ukuran_file',
        'dilihat',
        'diunduh',
        'is_published',
        'tanggal_terbit',
        'created_by',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'dilihat' => 'integer',
        'diunduh' => 'integer',
        'is_published' => 'boolean',
        'tanggal_terbit' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (DokumenInformasi $model) {
            if (empty($model->slug)) {
                $model->slug = \Illuminate\Support\Str::slug($model->judul);
            }

            if ($model->isDirty('file_path') && !empty($model->file_path)) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($model->file_path)) {
                    $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($model->file_path);
                    if ($bytes < 1048576) {
                        $model->ukuran_file = round($bytes / 1024, 1) . ' KB';
                    } else {
                        $model->ukuran_file = round($bytes / 1048576, 2) . ' MB';
                    }
                }
            }
        });
    }

    /**
     * Relasi ke kategori informasi.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriInformasi::class, 'kategori_id');
    }

    /**
     * Relasi ke pengguna/admin yang mengunggah dokumen.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
