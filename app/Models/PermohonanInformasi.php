<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PermohonanInformasi extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'permohonan_informasi';

    protected $fillable = [
        'nomor_registrasi',
        'status_id',
        'kategori_pemohon',
        'nama_pemohon',
        'nik',
        'alamat',
        'telepon',
        'email',
        'pekerjaan',
        'identitas_file_path',
        'surat_kuasa_file_path',
        'akta_badan_hukum_file_path',
        'rincian_informasi',
        'tujuan_penggunaan',
        'cara_memperoleh_informasi',
        'cara_mendapat_salinan',
        'tenggat_jawab',
        'tenggat_diperpanjang',
        'catatan_internal',
        'jawaban_file_path',
        'jawaban_teks',
        'tanggal_dijawab',
    ];

    /**
     * Casts atribut, termasuk NIK wajib dienkripsi otomatis di database.
     */
    protected function casts(): array
    {
        return [
            'nik' => 'encrypted',
            'tenggat_jawab' => 'datetime',
            'tenggat_diperpanjang' => 'datetime',
            'tanggal_dijawab' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (PermohonanInformasi $model) {
            if (empty($model->nomor_registrasi)) {
                $model->nomor_registrasi = static::generateNomorRegistrasi();
            }

            if (empty($model->status_id)) {
                $defaultStatus = StatusPermohonan::where('slug', 'diterima')->first();
                $model->status_id = $defaultStatus ? $defaultStatus->id : 1;
            }

            if (empty($model->tenggat_jawab)) {
                $model->tenggat_jawab = static::calculateDeadline(now(), 10);
            }
        });
    }

    /**
     * Spatie activitylog configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('ppid')
            ->setDescriptionForEvent(fn (string $event) => "Permohonan informasi {$this->nomor_registrasi} {$event}");
    }

    /**
     * Buat nomor registrasi unik format: PPID/KODE-INSTANSI/TAHUN/URUT
     */
    public static function generateNomorRegistrasi(?string $kodeInstansi = null): string
    {
        $tahun = date('Y');

        if (!$kodeInstansi) {
            $kodeInstansi = PengaturanPpid::first()?->kode_instansi
                ?? SiteSetting::first()?->institution_acronym
                ?? config('app.agency_code', 'LK');
        }

        // Format kode instansi rapi (alfanumerik + strip)
        $kode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $kodeInstansi)));
        if (empty($kode)) {
            $kode = 'LK';
        }

        $count = static::whereYear('created_at', $tahun)->count() + 1;
        $urut = str_pad((string) $count, 4, '0', STR_PAD_LEFT);
        $nomor = "PPID/{$kode}/{$tahun}/{$urut}";

        while (static::where('nomor_registrasi', $nomor)->exists()) {
            $count++;
            $urut = str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            $nomor = "PPID/{$kode}/{$tahun}/{$urut}";
        }

        return $nomor;
    }

    /**
     * Hitung tenggat waktu 10 hari kerja (tidak termasuk Sabtu & Minggu).
     */
    public static function calculateDeadline(?Carbon $fromDate = null, int $workingDays = 10): Carbon
    {
        $date = $fromDate ? $fromDate->copy() : now();
        $added = 0;

        while ($added < $workingDays) {
            $date->addDay();
            if ($date->isWeekday()) {
                $added++;
            }
        }

        return $date->endOfDay();
    }

    /**
     * Hitung sisa hari kerja sampai tenggat waktu.
     */
    public function getSisaHariKerjaAttribute(): int
    {
        if (!$this->tenggat_jawab) {
            return 0;
        }

        $now = now()->startOfDay();
        $deadline = $this->tenggat_jawab->copy()->startOfDay();

        if ($now->greaterThan($deadline)) {
            // Sudah lewat: hitung berapa hari kerja terlewat (negatif)
            $passed = 0;
            $current = $deadline->copy();
            while ($current->lessThan($now)) {
                $current->addDay();
                if ($current->isWeekday()) {
                    $passed++;
                }
            }
            return -$passed;
        }

        // Masih ada waktu: hitung sisa hari kerja
        $remaining = 0;
        $current = $now->copy();
        while ($current->lessThan($deadline)) {
            $current->addDay();
            if ($current->isWeekday()) {
                $remaining++;
            }
        }

        return $remaining;
    }

    /**
     * Status tenggat permohonan: selesai, terlewat, mendekati, aman.
     */
    public function getTenggatStatusAttribute(): string
    {
        $statusSlug = $this->status?->slug;
        if (in_array($statusSlug, ['dijawab', 'ditolak', 'selesai'])) {
            return 'selesai';
        }

        if (now()->greaterThan($this->tenggat_jawab)) {
            return 'terlewat';
        }

        if ($this->sisa_hari_kerja <= 3) {
            return 'mendekati';
        }

        return 'aman';
    }

    public function getTenggatBadgeColorAttribute(): string
    {
        return match ($this->tenggat_status) {
            'selesai' => 'success',
            'terlewat' => 'danger',
            'mendekati' => 'warning',
            default => 'info',
        };
    }

    public function getTenggatBadgeLabelAttribute(): string
    {
        return match ($this->tenggat_status) {
            'selesai' => 'Selesai',
            'terlewat' => 'Terlewat (' . abs($this->sisa_hari_kerja) . ' hari kerja)',
            'mendekati' => 'Mendekati Tenggat (Sisa ' . $this->sisa_hari_kerja . ' hari kerja)',
            default => 'Aman (Sisa ' . $this->sisa_hari_kerja . ' hari kerja)',
        };
    }

    /**
     * Relasi ke status permohonan.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusPermohonan::class, 'status_id');
    }

    /**
     * Relasi ke keberatan yang diajukan untuk permohonan ini.
     */
    public function keberatan(): HasMany
    {
        return $this->hasMany(Keberatan::class, 'permohonan_id');
    }
}
