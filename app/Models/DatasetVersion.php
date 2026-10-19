<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatasetVersion extends Model
{
    use HasFactory;

    protected $table = 'dataset_versions';

    protected $fillable = [
        'dataset_id',
        'version',
        'berkas_csv',
        'catatan_perubahan',
        'row_count',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'row_count' => 'integer',
        ];
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class, 'dataset_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
