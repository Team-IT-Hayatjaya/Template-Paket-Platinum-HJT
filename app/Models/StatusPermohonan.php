<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusPermohonan extends Model
{
    use HasFactory;

    protected $table = 'status_permohonan';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'warna',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke permohonan informasi yang memiliki status ini.
     */
    public function permohonan(): HasMany
    {
        return $this->hasMany(PermohonanInformasi::class, 'status_id');
    }
}
