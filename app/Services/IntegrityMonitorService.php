<?php

namespace App\Services;

use App\Mail\DefacementAlertMail;
use App\Models\IntegrityCheck;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Package;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Pemantau defacement berbasis checksum SHA-256 (paket Gold).
 *
 * Alur: buat baseline -> pemeriksaan terjadwal membandingkan hash berkas saat ini
 * dengan baseline -> bila berbeda, catat insiden, log aktivitas, dan kirim email peringatan.
 */
class IntegrityMonitorService
{
    public function isEnabled(): bool
    {
        return Package::has('security_hardening');
    }

    /**
     * Hitung SHA-256 seluruh berkas yang dipantau.
     *
     * @return array<string, string> path relatif => hash
     */
    public function computeChecksums(): array
    {
        $checksums = [];
        $extensions = (array) config('security.integrity.extensions', []);

        foreach ((array) config('security.integrity.paths', []) as $relative) {
            $absolute = base_path($relative);

            if (File::isFile($absolute)) {
                $checksums[$this->normalize($relative)] = hash_file('sha256', $absolute);

                continue;
            }

            if (! File::isDirectory($absolute)) {
                continue;
            }

            foreach (File::allFiles($absolute) as $file) {
                if (! $this->hasMonitoredExtension($file->getFilename(), $extensions)) {
                    continue;
                }

                $path = $this->normalize($relative.'/'.$file->getRelativePathname());
                $checksums[$path] = hash_file('sha256', $file->getRealPath());
            }
        }

        ksort($checksums);

        return $checksums;
    }

    public function hasBaseline(): bool
    {
        return $this->disk()->exists($this->baselineFile());
    }

    /**
     * @return array<string, string>
     */
    public function loadBaseline(): array
    {
        if (! $this->hasBaseline()) {
            return [];
        }

        $payload = json_decode((string) $this->disk()->get($this->baselineFile()), true);

        return (array) ($payload['files'] ?? []);
    }

    /**
     * Simpan kondisi berkas saat ini sebagai baseline tepercaya.
     */
    public function createBaseline(?User $by = null): IntegrityCheck
    {
        $checksums = $this->computeChecksums();

        $this->disk()->put($this->baselineFile(), json_encode([
            'algorithm' => 'sha256',
            'generated_at' => now()->toIso8601String(),
            'files' => $checksums,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        activity('security')
            ->event('integrity_baseline')
            ->causedBy($by)
            ->withProperties(['files' => count($checksums)])
            ->log('Baseline checksum integritas berkas diperbarui ('.count($checksums).' berkas)');

        return IntegrityCheck::create([
            'status' => IntegrityCheck::STATUS_BASELINE,
            'files_checked' => count($checksums),
            'checked_at' => now(),
        ]);
    }

    /**
     * Bandingkan kondisi berkas saat ini dengan baseline.
     */
    public function check(bool $notify = true): IntegrityCheck
    {
        if (! $this->hasBaseline()) {
            throw new RuntimeException('Baseline integritas belum dibuat. Jalankan: php artisan security:integrity --baseline');
        }

        $baseline = $this->loadBaseline();
        $current = $this->computeChecksums();

        $modified = [];
        foreach ($current as $path => $hash) {
            if (isset($baseline[$path]) && ! hash_equals($baseline[$path], $hash)) {
                $modified[] = $path;
            }
        }

        $missing = array_values(array_diff(array_keys($baseline), array_keys($current)));
        $new = array_values(array_diff(array_keys($current), array_keys($baseline)));

        $hasChanges = $modified || $missing || $new;

        $check = IntegrityCheck::create([
            'status' => $hasChanges ? IntegrityCheck::STATUS_MODIFIED : IntegrityCheck::STATUS_CLEAN,
            'files_checked' => count($current),
            'modified_files' => $modified,
            'missing_files' => $missing,
            'new_files' => $new,
            'checked_at' => now(),
        ]);

        if ($hasChanges) {
            activity('security')
                ->event('integrity_violation')
                ->withProperties([
                    'modified' => $modified,
                    'missing' => $missing,
                    'new' => $new,
                ])
                ->log("Peringatan defacement: {$check->totalChanges()} berkas berubah dari baseline");

            Log::warning('[Keamanan] Perubahan berkas terdeteksi oleh pemantau integritas', [
                'modified' => $modified,
                'missing' => $missing,
                'new' => $new,
            ]);

            if ($notify) {
                $this->sendAlert($check);
            }
        }

        return $check;
    }

    public function latest(): ?IntegrityCheck
    {
        return IntegrityCheck::query()->latest('checked_at')->latest('id')->first();
    }

    /**
     * Kirim email peringatan ke Super Admin dan alamat peringatan (bila dikonfigurasi).
     */
    public function sendAlert(IntegrityCheck $check): void
    {
        $recipients = collect([config('security.integrity.alert_email')])
            ->merge(
                User::role('Super Admin')->pluck('email')
            )
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            $fallback = SiteSetting::query()->value('email')
                ?: env('SUPER_ADMIN_EMAIL')
                ?: config('mail.from.address');

            if ($fallback && filter_var($fallback, FILTER_VALIDATE_EMAIL)) {
                $recipients->push($fallback);
            }
        }

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new DefacementAlertMail($check));
            } catch (\Throwable $e) {
                Log::error('[Keamanan] Gagal mengirim email peringatan defacement', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function disk()
    {
        return Storage::disk(config('security.integrity.disk', 'local'));
    }

    protected function baselineFile(): string
    {
        return (string) config('security.integrity.baseline_file', 'security/integrity-baseline.json');
    }

    protected function normalize(string $path): string
    {
        return trim(str_replace('\\', '/', $path), '/');
    }

    protected function hasMonitoredExtension(string $filename, array $extensions): bool
    {
        if ($extensions === []) {
            return true;
        }

        foreach ($extensions as $ext) {
            if (Str::endsWith(strtolower($filename), '.'.ltrim(strtolower($ext), '.'))
                || strtolower($filename) === '.'.ltrim(strtolower($ext), '.')) {
                return true;
            }
        }

        return false;
    }
}
