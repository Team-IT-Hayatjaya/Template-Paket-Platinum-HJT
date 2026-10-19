<?php

namespace App\Listeners;

use App\Services\AccountLockoutService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class LogAuthenticationActivity
{
    /**
     * Handle user login event.
     */
    public function handleLogin(Login $event): void
    {
        activity('auth')
            ->causedBy($event->user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard' => $event->guard,
            ])
            ->log("Pengguna {$event->user->email} berhasil login ke panel");

        // Paket Gold: reset penghitung gagal login (no-op pada Silver)
        if ($event->user instanceof \App\Models\User) {
            app(AccountLockoutService::class)->clear($event->user);
        }
    }

    /**
     * Handle failed login attempt.
     */
    public function handleFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? 'tidak diketahui';

        activity('auth')
            ->event('login_failed')
            ->withProperties([
                'email' => $email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard' => $event->guard,
            ])
            ->log("Percobaan login gagal untuk email: {$email}");

        // Paket Gold: hitung kegagalan untuk penguncian akun & blokir IP otomatis
        app(AccountLockoutService::class)->recordFailedAttempt(
            $event->credentials['email'] ?? null,
            request()->ip(),
        );
    }

    /**
     * Handle user logout event.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            activity('auth')
                ->causedBy($event->user)
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'guard' => $event->guard,
                ])
                ->log("Pengguna {$event->user->email} telah logout dari panel");
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleLogin',
            Failed::class => 'handleFailed',
            Logout::class => 'handleLogout',
        ];
    }
}
