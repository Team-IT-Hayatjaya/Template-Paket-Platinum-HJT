<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriInformasi extends Model
{
    use HasFactory;

    protected $table = 'kategori_informasi';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke dokumen informasi yang termasuk dalam kategori ini.
     */
    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenInformasi::class, 'kategori_id');
    }
}
