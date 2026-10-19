<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class PesanKontak extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'pesan_kontak';

    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'topik',
        'pesan',
        'status',
        'catatan_admin',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('kontak');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'belum_dibaca');
    }
}
