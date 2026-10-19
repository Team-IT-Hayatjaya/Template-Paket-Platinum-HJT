<?php

namespace App\Console\Commands;

use App\Services\IntegrityMonitorService;
use Illuminate\Console\Command;

class SecurityIntegrityCommand extends Command
{
    protected $signature = 'security:integrity
                            {--baseline : Simpan kondisi berkas saat ini sebagai baseline tepercaya}
                            {--no-notify : Jangan kirim email peringatan meskipun ada perubahan}';

    protected $description = 'Pemantau defacement: periksa integritas berkas inti dengan checksum SHA-256 (paket Gold)';

    public function handle(IntegrityMonitorService $monitor): int
    {
        if (! $monitor->isEnabled()) {
            $this->warn('Pemantau integritas hanya tersedia pada paket Gold ke atas. Perintah dilewati.');

            return self::SUCCESS;
        }

        if ($this->option('baseline') || ! $monitor->hasBaseline()) {
            $check = $monitor->createBaseline();
            $this->info("Baseline integritas disimpan: {$check->files_checked} berkas.");

            return self::SUCCESS;
        }

        $check = $monitor->check(notify: ! $this->option('no-notify'));

        if ($check->status === \App\Models\IntegrityCheck::STATUS_CLEAN) {
            $this->info("Aman: {$check->files_checked} berkas diperiksa, tidak ada perubahan.");

            return self::SUCCESS;
        }

        $this->error("PERINGATAN: {$check->totalChanges()} berkas berubah dari baseline.");

        foreach (['Diubah' => $check->modified_files, 'Hilang' => $check->missing_files, 'Baru' => $check->new_files] as $label => $files) {
            foreach ((array) $files as $file) {
                $this->line("  [{$label}] {$file}");
            }
        }

        return self::FAILURE;
    }
}
