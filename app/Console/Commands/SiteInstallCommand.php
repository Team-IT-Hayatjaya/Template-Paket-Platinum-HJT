<?php

namespace App\Console\Commands;

use App\Models\PengaturanPpid;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\HalamanSeeder;
use Database\Seeders\KategoriInformasiSeeder;
use Database\Seeders\PengaturanPpidSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusPermohonanSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class SiteInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:install
        {--package= : Pilihan paket langganan klien (silver, gold, platinum)}
        {--institution= : Nama lengkap instansi pemerintah}
        {--acronym= : Singkatan nama instansi}
        {--domain= : Domain atau alamat URL website}
        {--admin-name= : Nama lengkap administrator pertama}
        {--admin-email= : Alamat email administrator pertama}
        {--admin-password= : Password administrator pertama}
        {--force : Jalankan instalasi tanpa konfirmasi interaktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instalasi dan inisialisasi website instansi pemerintah untuk klien baru dari nol';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        intro('🚀 Inisialisasi & Instalasi Portal Resmi Instansi Pemerintah — PT Hayat Jaya Teknologi');

        $isInteractive = !$this->option('force') && $this->input->isInteractive();

        // 1. Tentukan Paket
        $package = $this->option('package');
        if (empty($package)) {
            if ($isInteractive) {
                $package = select(
                    label: 'Pilih Paket Layanan Klien:',
                    options: [
                        'silver' => 'SILVER (PPID Dasar, Formulir, Kuota 5 update/bln, Backup bulanan)',
                        'gold' => 'GOLD (PPID Tracking, Aksesibilitas, Kuota 20 update/bln, Backup mingguan)',
                        'platinum' => 'PLATINUM (Full Features, Bot WA, Open Data, Tanpa batas)',
                    ],
                    default: 'silver'
                );
            } else {
                $package = config('packages.default', 'silver');
            }
        }
        $package = strtolower(trim($package));
        if (!in_array($package, ['silver', 'gold', 'platinum'])) {
            $package = 'silver';
        }

        // 2. Nama Instansi
        $institution = $this->option('institution');
        if (empty($institution)) {
            if ($isInteractive) {
                $institution = text(
                    label: 'Nama Resmi Instansi Pemerintah:',
                    placeholder: 'Contoh: Pemerintah Kecamatan Loa Kulu',
                    default: 'Pemerintah Kecamatan Loa Kulu',
                    required: true
                );
            } else {
                $institution = 'Pemerintah Kecamatan Loa Kulu';
            }
        }

        // 3. Singkatan Instansi / Kode
        $acronym = $this->option('acronym');
        if (empty($acronym)) {
            // Auto generate dari nama instansi
            $words = explode(' ', str_replace(['Pemerintah', 'Kabupaten', 'Kota', 'Kecamatan'], '', $institution));
            $cleanWords = array_filter(array_map('trim', $words));
            $acronym = !empty($cleanWords) ? strtoupper(substr(implode('-', $cleanWords), 0, 10)) : 'KEC-LK';
            $acronym = preg_replace('/[^A-Za-z0-9\-]/', '', $acronym);
            if (empty($acronym)) {
                $acronym = 'PPID';
            }
        }

        // 4. Domain / URL
        $domain = $this->option('domain');
        if (empty($domain)) {
            if ($isInteractive) {
                $domain = text(
                    label: 'Domain / Alamat URL Website:',
                    placeholder: 'Contoh: https://loakulu.kutaiterkini.go.id',
                    default: config('app.url', 'http://localhost:8000')
                );
            } else {
                $domain = config('app.url', 'http://localhost:8000');
            }
        }

        // 5. Administrator Pertama
        $adminName = $this->option('admin-name');
        if (empty($adminName)) {
            if ($isInteractive) {
                $adminName = text(
                    label: 'Nama Lengkap Administrator Pertama:',
                    default: 'Administrator Portal',
                    required: true
                );
            } else {
                $adminName = 'Super Administrator';
            }
        }

        $adminEmail = $this->option('admin-email');
        if (empty($adminEmail)) {
            if ($isInteractive) {
                $adminEmail = text(
                    label: 'Email Administrator Utama:',
                    placeholder: 'admin@instansi.go.id',
                    default: 'admin@hayatjaya.com',
                    required: true
                );
            } else {
                $adminEmail = env('SUPER_ADMIN_EMAIL', 'admin@hayatjaya.com');
            }
        }

        $adminPassword = $this->option('admin-password');
        if (empty($adminPassword)) {
            if ($isInteractive) {
                $adminPassword = password(
                    label: 'Password Administrator Utama:',
                    placeholder: 'Minimal 8 karakter',
                    required: true
                );
            } else {
                $adminPassword = env('SUPER_ADMIN_PASSWORD', 'HayatJaya#2026!Secure');
            }
        }

        $this->info("Menyiapkan konfigurasi instalasi untuk paket [{$package}]...");

        // LANGKAH 1: Update .env file
        $this->updateEnvironmentVariables([
            'SITE_PACKAGE' => $package,
            'APP_NAME' => "\"{$institution}\"",
            'APP_URL' => $domain,
            'SUPER_ADMIN_EMAIL' => $adminEmail,
        ]);
        config(['packages.default' => $package]);

        // LANGKAH 2: Pastikan Application Key ada
        if (empty(config('app.key'))) {
            $this->info("Menghasilkan application encryption key...");
            Artisan::call('key:generate', ['--force' => true]);
        }

        // LANGKAH 3: Jalankan Migrasi Database
        $this->info("Menjalankan migrasi database...");
        Artisan::call('migrate', ['--force' => true]);

        // LANGKAH 4: Jalankan Seeder Dasar
        $this->info("Menyemai data awal (peran, klasifikasi, status, halaman)...");
        Artisan::call('db:seed', ['--class' => RolePermissionSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => KategoriInformasiSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => StatusPermohonanSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => PengaturanPpidSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => HalamanSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\NavigationMenuSeeder::class, '--force' => true]);

        // LANGKAH 5: Konfigurasi Identitas Instansi
        $this->info("Menyimpan data identitas instansi...");
        $setting = SiteSetting::first();
        if ($setting) {
            $setting->update([
                'institution_name' => $institution,
                'institution_acronym' => $acronym,
                'email' => $adminEmail,
            ]);
        } else {
            SiteSetting::create([
                'institution_name' => $institution,
                'institution_acronym' => $acronym,
                'email' => $adminEmail,
                'primary_color' => '#1b3b6f',
            ]);
        }

        // Konfigurasi Pengaturan PPID
        $pengaturanPpid = PengaturanPpid::first();
        if ($pengaturanPpid) {
            $pengaturanPpid->update([
                'nama_badan_publik' => $institution,
                'kode_instansi' => $acronym,
                'email' => $adminEmail,
            ]);
        }

        // LANGKAH 6: Buat atau Perbarui Administrator Pertama
        $this->info("Membuat akun Super Admin...");
        $user = User::firstOrNew(['email' => $adminEmail]);
        $user->name = $adminName;
        $user->password = Hash::make($adminPassword);
        $user->email_verified_at = now();
        $user->save();

        if (!$user->hasRole('Super Admin')) {
            $user->assignRole('Super Admin');
        }

        // LANGKAH 7: Symbolic Link Storage (jika belum ada)
        try {
            Artisan::call('storage:link');
        } catch (\Throwable $e) {
            // Ignored if link already exists
        }

        // LANGKAH 8: Bersihkan Cache Konfigurasi
        try {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
        } catch (\Throwable $e) {
            // Ignored in test environment
        }

        $this->newLine();
        $this->info('================================================================');
        $this->info('  🎉 INSTALASI KLIEN BARU BERHASIL DISELESAIKAN (1-COMMAND)!');
        $this->info('================================================================');
        $this->line("  Instansi       : <comment>{$institution}</comment>");
        $this->line("  Paket Aktif    : <comment>" . strtoupper($package) . "</comment>");
        $this->line("  Alamat URL     : <comment>{$domain}</comment>");
        $this->line("  Panel Admin    : <comment>{$domain}/admin</comment>");
        $this->line("  Email Admin    : <comment>{$adminEmail}</comment>");
        $this->line("  Peran          : <comment>Super Admin</comment>");
        $this->info('================================================================');
        $this->info('Sistem siap dioperasikan di lingkungan produksi.');

        return self::SUCCESS;
    }

    /**
     * Perbarui variabel di berkas .env secara aman.
     */
    protected function updateEnvironmentVariables(array $data): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $examplePath = base_path('.env.example');
            if (File::exists($examplePath)) {
                File::copy($examplePath, $envPath);
            } else {
                return;
            }
        }

        $content = File::get($envPath);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $newLine = "{$key}={$value}";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $newLine, $content);
            } else {
                $content .= "\n{$newLine}";
            }
        }

        File::put($envPath, $content);
    }
}
