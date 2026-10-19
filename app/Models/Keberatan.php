<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keberatan extends Model
{
    use HasFactory;

    protected $table = 'keberatan';

    protected $fillable = [
        'nomor_keberatan',
        'permohonan_id',
        'nomor_registrasi_permohonan',
        'nama_pemohon',
        'telepon',
        'email',
        'alasan_keberatan',
        'uraian_keberatan',
        'status',
        'catatan_internal',
    ];

    /**
     * Relasi ke permohonan informasi terkait.
     */
    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_id');
    }
}
