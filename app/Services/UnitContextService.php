<?php

namespace App\Services;

use App\Models\Unit;
use App\Support\Package;
use Illuminate\Http\Request;

class UnitContextService
{
    protected ?Unit $currentUnit = null;

    /**
     * Dapatkan unit aktif saat ini (null jika di Portal Utama atau paket bukan Platinum).
     */
    public function current(): ?Unit
    {
        if (! Package::has('subdomains')) {
            return null;
        }

        return $this->currentUnit;
    }

    /**
     * Set unit aktif secara manual (misal pada middleware atau unit test).
     */
    public function set(?Unit $unit): void
    {
        $this->currentUnit = $unit;
    }

    /**
     * Cek apakah saat ini berada dalam konteks unit / UPTD.
     */
    public function hasUnit(): bool
    {
        return $this->current() !== null;
    }

    /**
     * Dapatkan ID unit aktif saat ini.
     */
    public function unitId(): ?int
    {
        return $this->current()?->id;
    }

    /**
     * Reset konteks unit ke Portal Utama.
     */
    public function reset(): void
    {
        $this->currentUnit = null;
    }

    /**
     * Deteksi dan resolusi unit dari objek Request (Host, Header, atau Query Param).
     */
    public function resolveFromRequest(Request $request): ?Unit
    {
        if (! Package::has('subdomains')) {
            return null;
        }

        try {
            if (! Schema::hasTable('units')) {
                return null;
            }

            // 1. Dukungan pengujian via header eksplisit (e.g. X-Unit-Subdomain: dispora)
            if ($request->hasHeader('X-Unit-Subdomain')) {
                $subdomain = strtolower((string) $request->header('X-Unit-Subdomain'));
                return Unit::active()->where('subdomain', $subdomain)->first();
            }

            // 2. Dukungan environment lokal / testing via query parameter ?_unit=dispora
            if (app()->environment('local', 'testing') && $request->filled('_unit')) {
                $subdomain = strtolower((string) $request->query('_unit'));
                return Unit::active()->where('subdomain', $subdomain)->first();
            }

            // 3. Resolusi dari Hostname / Subdomain HTTP
            $host = strtolower($request->getHost());

            // Abaikan IP address murni
            if (filter_var($host, FILTER_VALIDATE_IP)) {
                return null;
            }

            $parts = explode('.', $host);

            // Jika setidaknya memiliki satu subdomain prefix (e.g. dispora.pemprov.go.id atau dispora.localhost)
            // Abaikan domain deployment Vercel (*.vercel.app) sebagai portal utama
            if (str_ends_with($host, '.vercel.app')) {
                return null;
            }

            if (count($parts) >= 2) {
                $candidate = $parts[0];

                // Abaikan prefix umum seperti www atau admin
                if (in_array($candidate, ['www', 'mail', 'webmail', 'cpanel'], true)) {
                    return null;
                }

                $unit = Unit::active()->where('subdomain', $candidate)->first();
                if ($unit) {
                    return $unit;
                }
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }
}
