<?php

namespace App\Models;

use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use SensitiveParameter;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'unit_id',
        'name',
        'email',
        'password',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_id' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
            'locked_until' => 'datetime',
            'failed_login_attempts' => 'integer',
        ];
    }

    /**
     * Determine if user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole([
                'Super Admin',
                'Admin Konten',
                'Admin Unit',
                'Petugas PPID',
                'Atasan PPID',
            ]);
        }

        return false;
    }

    /**
     * Akun sedang dikunci sementara akibat percobaan login gagal (paket Gold).
     */
    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    /**
     * HasAppAuthentication contract implementation.
     */
    public function getAppAuthenticationSecret(): ?string
    {
        return $this->two_factor_secret;
    }

    public function saveAppAuthenticationSecret(#[SensitiveParameter] ?string $secret): void
    {
        $this->forceFill([
            'two_factor_secret' => $secret,
        ])->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->email;
    }

    /**
     * Spatie activitylog configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->useLogName('user_management')
            ->setDescriptionForEvent(fn (string $eventName) => "Pengguna {$this->email} {$eventName}");
    }

    /**
     * Relasi ORM: Berita yang ditulis / diunggah oleh pengguna ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Berita>
     */
    public function berita(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Berita::class, 'author_id');
    }

    /**
     * Relasi ORM: Daftar IP yang diblokir oleh pengguna ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\IpBlocklist>
     */
    public function ipBlocklists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IpBlocklist::class, 'created_by');
    }

    /**
     * Relasi ORM: Log aktivitas audit trail yang dilakukan oleh pengguna ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Spatie\Activitylog\Models\Activity>
     */
    public function activityLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\Spatie\Activitylog\Models\Activity::class, 'causer_id')
            ->where('causer_type', static::class);
    }

    /**
     * Helper: Pengguna merupakan Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Helper: Pengguna merupakan Admin Unit / UPTD.
     */
    public function isAdminUnit(): bool
    {
        return $this->hasRole('Admin Unit');
    }

    /**
     * Helper: Pengguna memiliki hak penanganan PPID.
     */
    public function isPetugasPpid(): bool
    {
        return $this->hasAnyRole(['Petugas PPID', 'Atasan PPID', 'Super Admin']);
    }

    /**
     * Relasi ke Unit / UPTD tempat pengguna ditugaskan (khusus Admin Unit).
     */
    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
