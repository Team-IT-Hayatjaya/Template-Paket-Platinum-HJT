<?php

namespace App\Services;

use App\Mail\AccountLockedMail;
use App\Models\IpBlocklist;
use App\Models\User;
use App\Support\Package;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Penguncian akun setelah percobaan login gagal berturut-turut dan
 * pemblokiran IP otomatis (fitur Keamanan Aktif paket Gold).
 *
 * Seluruh metode bersifat no-op bila Package::has('security_hardening') false,
 * sehingga perilaku paket Silver tidak berubah.
 */
class AccountLockoutService
{
    public function isEnabled(): bool
    {
        return Package::has('security_hardening');
    }

    public function maxAttempts(): int
    {
        return max(1, (int) config('security.lockout.max_attempts', 5));
    }

    public function decayMinutes(): int
    {
        return max(1, (int) config('security.lockout.decay_minutes', 15));
    }

    public function findUser(?string $email): ?User
    {
        if (blank($email)) {
            return null;
        }

        return User::query()->where('email', $email)->first();
    }

    /**
     * Akun dengan email ini sedang terkunci?
     */
    public function isLocked(?string $email): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        return (bool) $this->findUser($email)?->isLocked();
    }

    /**
     * Sisa waktu penguncian dalam menit (dibulatkan ke atas).
     */
    public function remainingMinutes(?string $email): int
    {
        $user = $this->findUser($email);

        if (! $user?->isLocked()) {
            return 0;
        }

        return (int) max(1, ceil(now()->diffInSeconds($user->locked_until, true) / 60));
    }

    /**
     * Catat satu percobaan login gagal. Mengunci akun bila ambang terlampaui
     * dan memblokir IP secara otomatis bila IP tersebut menyerang banyak akun.
     */
    public function recordFailedAttempt(?string $email, ?string $ip = null): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $ip ??= request()->ip();

        $user = $this->findUser($email);

        if ($user && ! $user->isLocked()) {
            $attempts = (int) $user->failed_login_attempts + 1;
            $attributes = ['failed_login_attempts' => $attempts];

            if ($attempts >= $this->maxAttempts()) {
                $attributes['locked_until'] = now()->addMinutes($this->decayMinutes());
                $attributes['failed_login_attempts'] = 0;
            }

            $user->forceFill($attributes)->saveQuietly();

            if (isset($attributes['locked_until'])) {
                activity('security')
                    ->event('account_locked')
                    ->performedOn($user)
                    ->withProperties([
                        'email' => $user->email,
                        'ip' => $ip,
                        'locked_until' => $attributes['locked_until']->toDateTimeString(),
                        'attempts' => $attempts,
                    ])
                    ->log("Akun {$user->email} dikunci sementara selama {$this->decayMinutes()} menit setelah {$attempts} kali gagal login");

                $this->notifySuperAdminAccountLocked($user, $attempts, $this->decayMinutes(), $ip);
            }
        }

        $this->trackIpFailure($ip, $email);
    }

    /**
     * Kirim email pemberitahuan ke Super Admin saat akun dikunci.
     */
    protected function notifySuperAdminAccountLocked(User $user, int $attempts, int $decayMinutes, ?string $ip): void
    {
        try {
            $adminEmail = config('security.alert_email')
                ?? config('security.integrity.alert_email')
                ?? User::role('Super Admin')->latest()->value('email')
                ?? env('SUPER_ADMIN_EMAIL')
                ?? config('mail.from.address');

            if ($adminEmail) {
                Mail::to($adminEmail)->send(new AccountLockedMail($user, $attempts, $decayMinutes, $ip));
            }
        } catch (\Throwable $e) {
            Log::error("[Keamanan] Gagal mengirim email penguncian akun: {$e->getMessage()}");
        }
    }

    /**
     * Reset penghitung setelah login berhasil.
     */
    public function clear(User $user): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        if ((int) $user->failed_login_attempts === 0 && $user->locked_until === null) {
            return;
        }

        $user->forceFill([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ])->saveQuietly();
    }

    /**
     * Buka kunci akun secara manual oleh administrator.
     */
    public function unlock(User $user, ?User $by = null): void
    {
        $user->forceFill([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ])->saveQuietly();

        activity('security')
            ->event('account_unlocked')
            ->causedBy($by)
            ->performedOn($user)
            ->withProperties(['email' => $user->email])
            ->log("Kunci akun {$user->email} dibuka secara manual oleh administrator");
    }

    /**
     * Hitung kegagalan per-IP dalam jendela waktu; blokir otomatis bila melewati ambang.
     */
    protected function trackIpFailure(?string $ip, ?string $email): void
    {
        if (blank($ip) || ! config('security.ip_auto_block.enabled', true)) {
            return;
        }

        $window = max(1, (int) config('security.ip_auto_block.window_minutes', 60));
        $threshold = max(1, (int) config('security.ip_auto_block.max_failures', 20));
        $key = 'security:ip_failures:'.sha1($ip);

        Cache::add($key, 0, now()->addMinutes($window));
        $failures = (int) Cache::increment($key);

        if ($failures < $threshold || IpBlocklist::isBlocked($ip)) {
            return;
        }

        $until = Carbon::now()->addHours(max(1, (int) config('security.ip_auto_block.block_hours', 24)));

        IpBlocklist::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason' => "Otomatis: {$failures} percobaan login gagal dalam {$window} menit (terakhir: {$email})",
                'blocked_until' => $until,
                'is_automatic' => true,
            ],
        );

        Cache::forget($key);

        activity('security')
            ->event('ip_auto_blocked')
            ->withProperties(['ip' => $ip, 'failures' => $failures, 'blocked_until' => $until->toDateTimeString()])
            ->log("IP {$ip} diblokir otomatis hingga {$until->format('d/m/Y H:i')} karena {$failures} percobaan login gagal");
    }
}
