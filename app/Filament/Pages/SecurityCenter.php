<?php

namespace App\Filament\Pages;

use App\Models\IntegrityCheck;
use App\Models\IpBlocklist;
use App\Models\User;
use App\Services\AccountLockoutService;
use App\Services\IntegrityMonitorService;
use App\Support\Package;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

/**
 * Pusat Keamanan (paket Gold): akun terkunci, status pemantau defacement,
 * dan peristiwa keamanan terbaru.
 */
class SecurityCenter extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Pusat Keamanan & Audit';

    protected static ?string $title = 'Pusat Keamanan Portal';

    protected static string | \UnitEnum | null $navigationGroup = 'Keamanan & Pengguna';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'pusat-keamanan';

    protected string $view = 'filament.pages.security-center';

    public static function canAccess(): bool
    {
        return Package::has('security_hardening')
            && (bool) auth()->user()?->hasRole('Super Admin');
    }

    public function getLockedUsers(): Collection
    {
        return User::query()
            ->where('locked_until', '>', now())
            ->orderBy('locked_until')
            ->get(['id', 'name', 'email', 'locked_until']);
    }

    public function getLatestCheck(): ?IntegrityCheck
    {
        return app(IntegrityMonitorService::class)->latest();
    }

    public function hasBaseline(): bool
    {
        return app(IntegrityMonitorService::class)->hasBaseline();
    }

    public function getActiveBlockCount(): int
    {
        return IpBlocklist::query()->active()->count();
    }

    public function getFailedLoginCount24h(): int
    {
        return Activity::query()
            ->where('log_name', 'auth')
            ->where('event', 'login_failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();
    }

    public function getRecentEvents(): Collection
    {
        return Activity::query()
            ->where(function ($q) {
                $q->where('log_name', 'security')
                    ->orWhere(fn ($q) => $q->where('log_name', 'auth')->where('event', 'login_failed'));
            })
            ->latest()
            ->limit(15)
            ->get(['id', 'log_name', 'event', 'description', 'properties', 'created_at']);
    }

    public function unlockUser(int $userId): void
    {
        abort_unless(static::canAccess(), 403);

        $user = User::query()->findOrFail($userId);
        app(AccountLockoutService::class)->unlock($user, auth()->user());

        Notification::make()
            ->title("Kunci akun {$user->email} telah dibuka")
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runIntegrityCheck')
                ->label('Periksa Integritas Sekarang')
                ->icon('heroicon-o-magnifying-glass-circle')
                ->color('primary')
                ->disabled(fn () => ! $this->hasBaseline())
                ->action(function () {
                    $check = app(IntegrityMonitorService::class)->check();

                    $check->status === IntegrityCheck::STATUS_CLEAN
                        ? Notification::make()->title('Aman: tidak ada perubahan berkas')->body("{$check->files_checked} berkas diperiksa.")->success()->send()
                        : Notification::make()->title("Peringatan: {$check->totalChanges()} berkas berubah")->body('Email peringatan telah dikirim ke Super Admin.')->danger()->persistent()->send();
                }),

            Action::make('updateBaseline')
                ->label('Perbarui Baseline')
                ->icon('heroicon-o-finger-print')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Perbarui baseline integritas?')
                ->modalDescription('Kondisi berkas saat ini akan dianggap sah. Lakukan hanya setelah memastikan seluruh perubahan berasal dari pembaruan resmi.')
                ->action(function () {
                    $check = app(IntegrityMonitorService::class)->createBaseline(auth()->user());

                    Notification::make()
                        ->title('Baseline integritas diperbarui')
                        ->body("{$check->files_checked} berkas tercatat sebagai kondisi tepercaya.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
