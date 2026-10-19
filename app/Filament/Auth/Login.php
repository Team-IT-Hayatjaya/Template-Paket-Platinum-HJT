<?php

namespace App\Filament\Auth;

use App\Models\User;
use App\Services\AccountLockoutService;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;

/**
 * Halaman login panel admin berstandar instansi pemerintah (Paket Gold).
 *
 * Menggunakan layout kenegaraan resmi (filament.layouts.auth) dan backend
 * autentikasi berbasis Eloquent ORM murni dengan relasi peran & izin,
 * proteksi penguncian akun (account lockout), serta audit log forensik digital.
 */
class Login extends BaseLogin
{

    public function authenticate(): ?LoginResponse
    {
        $email = $this->data['email'] ?? null;
        $lockout = app(AccountLockoutService::class);

        // 1. Validasi Keberadaan & Hak Akses Pengguna via Eloquent ORM
        $user = User::query()
            ->with(['roles.permissions', 'activityLogs'])
            ->where('email', $email)
            ->first();

        // 2. Proteksi Penguncian Akun (Account Lockout) Paket Gold
        if ($lockout->isLocked($email) || ($user && $user->isLocked())) {
            activity('security')
                ->event('locked_login_blocked')
                ->when($user, fn ($act) => $act->performedOn($user))
                ->withProperties([
                    'email' => $email,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log("Percobaan login ke akun terkunci ditolak: {$email}");

            throw ValidationException::withMessages([
                'data.email' => sprintf(
                    'Akun ini dikunci sementara karena terlalu banyak percobaan login gagal. Silakan coba lagi dalam %d menit atau hubungi Super Admin.',
                    $lockout->remainingMinutes($email),
                ),
            ]);
        }

        // 3. Verifikasi Otorisasi Peran Panel via ORM
        if ($user && ! $user->canAccessPanel(Filament::getCurrentPanel())) {
            activity('security')
                ->event('unauthorized_panel_access')
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties([
                    'email' => $email,
                    'ip' => request()->ip(),
                    'roles' => $user->roles->pluck('name')->all(),
                ])
                ->log("Upaya akses panel admin tanpa wewenang peran oleh {$email}");

            throw ValidationException::withMessages([
                'data.email' => 'Akun ini tidak memiliki hak akses operasional ke Panel Kendali Admin.',
            ]);
        }

        // 4. Eksekusi Autentikasi Standar Filament (termasuk 2FA Challenge bila aktif)
        $response = parent::authenticate();

        // 5. Pencatatan Jejak Audit Trail & Reset Penghitung via ORM
        if ($user) {
            $lockout->clear($user);

            activity('auth')
                ->event('admin_login_success')
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'roles' => $user->roles->pluck('name')->all(),
                    'package' => config('packages.default', 'gold'),
                ])
                ->log("Pengguna {$user->email} berhasil login ke Panel Admin Gold");
        }

        return $response;
    }
}

