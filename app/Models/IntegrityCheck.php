<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrityCheck extends Model
{
    public const STATUS_BASELINE = 'baseline';

    public const STATUS_CLEAN = 'clean';

    public const STATUS_MODIFIED = 'modified';

    protected $table = 'integrity_checks';

    protected $fillable = [
        'status',
        'files_checked',
        'modified_files',
        'missing_files',
        'new_files',
        'checked_at',
    ];

    protected $casts = [
        'modified_files' => 'array',
        'missing_files' => 'array',
        'new_files' => 'array',
        'checked_at' => 'datetime',
        'files_checked' => 'integer',
    ];

    public function totalChanges(): int
    {
        return count($this->modified_files ?? [])
            + count($this->missing_files ?? [])
            + count($this->new_files ?? []);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_BASELINE => 'Baseline Diperbarui',
            self::STATUS_CLEAN => 'Aman (Tidak Ada Perubahan)',
            self::STATUS_MODIFIED => 'Terdeteksi Perubahan',
            default => ucfirst((string) $this->status),
        };
    }
}
