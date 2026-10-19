<?php

namespace App\Services;

use App\Models\IntegrityCheck;
use App\Models\IpBlocklist;
use App\Models\User;
use App\Support\Package;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

/**
 * Ringkasan keamanan untuk laporan bulanan & Pusat Keamanan (paket Gold).
 */
class SecurityReportService
{
    public function isEnabled(): bool
    {
        return Package::has('security_hardening');
    }

    /**
     * Data keamanan pada rentang tanggal tertentu, atau null bila fitur tidak aktif.
     */
    public function summary(Carbon $start, Carbon $end): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $failedLogins = Activity::query()
            ->where('log_name', 'auth')
            ->where('event', 'login_failed')
            ->whereBetween('created_at', [$start, $end]);

        $topIps = (clone $failedLogins)
            ->get(['properties'])
            ->map(fn (Activity $a) => $a->properties['ip'] ?? null)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->map(fn (int $count, string $ip) => ['ip' => $ip, 'jumlah' => $count])
            ->values()
            ->all();

        $securityEvents = fn (string $event) => Activity::query()
            ->where('log_name', 'security')
            ->where('event', $event)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $checks = IntegrityCheck::query()->whereBetween('checked_at', [$start, $end]);
        $latestCheck = IntegrityCheck::query()
            ->where('checked_at', '<=', $end)
            ->latest('checked_at')
            ->latest('id')
            ->first();

        return [
            'failed_logins' => (clone $failedLogins)->count(),
            'top_failed_ips' => $topIps,
            'accounts_locked' => $securityEvents('account_locked'),
            'locked_login_blocked' => $securityEvents('locked_login_blocked'),
            'currently_locked' => User::query()->where('locked_until', '>', now())->count(),
            'ip_blocked_new' => IpBlocklist::query()->whereBetween('created_at', [$start, $end])->count(),
            'ip_blocked_auto' => IpBlocklist::query()->where('is_automatic', true)->whereBetween('created_at', [$start, $end])->count(),
            'ip_blocked_active' => IpBlocklist::query()->active()->count(),
            'integrity_checks' => (clone $checks)->whereIn('status', [IntegrityCheck::STATUS_CLEAN, IntegrityCheck::STATUS_MODIFIED])->count(),
            'integrity_incidents' => (clone $checks)->where('status', IntegrityCheck::STATUS_MODIFIED)->count(),
            'integrity_last_status' => $latestCheck?->statusLabel() ?? 'Belum Pernah Diperiksa',
            'integrity_last_status_code' => $latestCheck?->status,
            'integrity_last_checked_at' => $latestCheck?->checked_at?->format('d/m/Y H:i'),
            'backup_frequency' => match (Package::limit('backup_frequency')) {
                'daily' => 'Harian',
                'weekly' => 'Mingguan (Senin 02.00)',
                default => 'Bulanan',
            },
        ];
    }
}
