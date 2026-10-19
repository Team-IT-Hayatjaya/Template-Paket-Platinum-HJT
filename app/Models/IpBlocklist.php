<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class IpBlocklist extends Model
{
    use LogsActivity;

    public const CACHE_KEY = 'security:ip_blocklist:active';

    protected $table = 'ip_blocklists';

    protected $fillable = [
        'ip_address',
        'reason',
        'blocked_until',
        'is_automatic',
        'created_by',
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
        'is_automatic' => 'boolean',
    ];

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget(self::CACHE_KEY);

        static::saved($flush);
        static::deleted($flush);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Blokir yang masih berlaku (permanen atau belum kedaluwarsa).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('blocked_until')->orWhere('blocked_until', '>', now());
        });
    }

    public function isActive(): bool
    {
        return $this->blocked_until === null || $this->blocked_until->isFuture();
    }

    /**
     * Periksa apakah IP sedang diblokir. Daftar aktif di-cache 5 menit
     * agar middleware tidak membebani database pada setiap permintaan.
     */
    public static function isBlocked(?string $ip): bool
    {
        if (blank($ip)) {
            return false;
        }

        $active = Cache::remember(self::CACHE_KEY, now()->addMinutes(5), function () {
            return static::query()
                ->active()
                ->get(['ip_address', 'blocked_until'])
                ->mapWithKeys(fn (self $row) => [
                    $row->ip_address => $row->blocked_until?->getTimestamp(),
                ])
                ->all();
        });

        if (! array_key_exists($ip, $active)) {
            return false;
        }

        $until = $active[$ip];

        return $until === null || $until > now()->getTimestamp();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['ip_address', 'reason', 'blocked_until', 'is_automatic'])
            ->logOnlyDirty()
            ->useLogName('security')
            ->setDescriptionForEvent(fn (string $eventName) => "Daftar blokir IP {$this->ip_address} {$eventName}");
    }
}
