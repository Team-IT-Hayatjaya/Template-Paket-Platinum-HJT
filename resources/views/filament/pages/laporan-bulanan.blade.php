<x-filament-panels::page>
    @php
        $report = $this->getReportData();
        $currentYear = (int) date('Y');
        $namaBulan = \App\Services\MonthlyReportService::NAMA_BULAN;
    @endphp

    <!-- Filter Periode Bulan & Tahun -->
    <div class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow-xs border border-gray-200 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pilih Periode:</span>
            <select wire:model.live="month" class="text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                @foreach($namaBulan as $mNum => $mName)
                    <option value="{{ $mNum }}">{{ $mName }}</option>
                @endforeach
            </select>

            <select wire:model.live="year" class="text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500">
                @for($y = $currentYear; $y >= $currentYear - 4; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400">
            Laporan Kinerja Periode: <strong class="text-primary-600 dark:text-primary-400">{{ $report['period_label'] }}</strong>
        </div>
    </div>

    <!-- 1. KARTU STATISTIK PENGUNJUNG -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Kunjungan</div>
            <div class="text-2xl font-black text-primary-600 dark:text-primary-400 mt-2">{{ number_format($report['total_views'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Halaman dilihat</div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Desktop</div>
            <div class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ number_format($report['views_by_device']['desktop'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">PC / Laptop</div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Mobile</div>
            <div class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ number_format($report['views_by_device']['mobile'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Smartphone</div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tablet</div>
            <div class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ number_format($report['views_by_device']['tablet'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Layar Tablet</div>
        </div>
    </div>

    <!-- 2. HALAMAN TERPOPULER & PEMBARUAN KONTEN -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Halaman Terpopuler -->
        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span>Halaman Terpopuler Diakses</span>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="py-2.5 px-3">Halaman (URL)</th>
                            <th class="py-2.5 px-3 text-right">Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($report['top_pages'] as $page)
                            <tr>
                                <td class="py-2.5 px-3 font-mono text-xs text-gray-800 dark:text-gray-200">{{ $page['halaman'] }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-primary-600 dark:text-primary-400">{{ number_format($page['views'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-4 text-center text-xs text-gray-500">Belum ada kunjungan pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Aktivitas Pembaruan Konten -->
        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span>Aktivitas Pembaruan Konten</span>
            </h3>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div class="text-xs text-gray-500">Berita & Warta</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $report['content_updates']['berita'] }} kali</div>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div class="text-xs text-gray-500">Halaman Statis</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $report['content_updates']['halaman'] }} kali</div>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div class="text-xs text-gray-500">Dokumen PDF</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $report['content_updates']['dokumen'] }} berkas</div>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div class="text-xs text-gray-500">Galeri Foto</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $report['content_updates']['galeri'] }} item</div>
                </div>
            </div>

            <div class="pt-2 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                <span>Total Pembaruan: <strong>{{ $report['content_updates']['total'] }}</strong></span>
                <span>Batas Kuota Paket: <strong>{{ $report['content_updates']['quota_limit'] ?? 'Unlimited' }}</strong></span>
            </div>
            @if($report['content_updates']['quota_limit'])
                <div class="text-xs text-gray-600 dark:text-gray-400">
                    Pemakaian Kuota (log aktivitas):
                    <strong class="{{ $report['content_updates']['quota_used'] > $report['content_updates']['quota_limit'] ? 'text-danger-600' : 'text-primary-600 dark:text-primary-400' }}">
                        {{ $report['content_updates']['quota_used'] }} / {{ $report['content_updates']['quota_limit'] }}
                    </strong>
                </div>
            @endif
        </div>
    </div>

    <!-- 3. PERMOHONAN INFORMASI PPID -->
    <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                Permohonan Informasi Publik (PPID)
            </h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                Total Masuk: {{ $report['permohonan']['total'] }} | Dijawab: {{ $report['permohonan']['dijawab'] }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="py-2.5 px-3">Status Pelayanan</th>
                        <th class="py-2.5 px-3 text-right">Jumlah</th>
                        <th class="py-2.5 px-3 text-right">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($report['permohonan']['breakdown'] as $st)
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-gray-800 dark:text-gray-200">{{ $st['nama'] }}</td>
                            <td class="py-2.5 px-3 text-right font-bold">{{ $st['jumlah'] }}</td>
                            <td class="py-2.5 px-3 text-right text-gray-500">{{ $st['persentase'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(!empty($report['security']))
        @php $sec = $report['security']; @endphp
        <!-- 4. KEAMANAN SISTEM (PAKET GOLD) -->
        <section aria-labelledby="judul-keamanan" class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 id="judul-keamanan" class="text-base font-bold text-gray-900 dark:text-white">Keamanan Sistem &amp; Ketahanan Portal</h3>
                <span @class([
                    'text-xs font-semibold px-2.5 py-1 rounded-full',
                    'bg-danger-50 text-danger-700' => $sec['integrity_last_status_code'] === 'modified',
                    'bg-success-50 text-success-700' => $sec['integrity_last_status_code'] !== 'modified',
                ])>
                    Integritas: {{ $sec['integrity_last_status'] }}
                </span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach([
                    ['Login Gagal', $sec['failed_logins'], 'percobaan'],
                    ['Akun Dikunci', $sec['accounts_locked'], 'kali'],
                    ['IP Diblokir (baru)', $sec['ip_blocked_new'], 'otomatis: '.$sec['ip_blocked_auto']],
                    ['Blokir IP Aktif', $sec['ip_blocked_active'], 'alamat IP'],
                    ['Pemeriksaan Integritas', $sec['integrity_checks'], 'kali (SHA-256)'],
                    ['Insiden Defacement', $sec['integrity_incidents'], 'terdeteksi'],
                    ['Akun Terkunci Saat Ini', $sec['currently_locked'], 'akun'],
                    ['Backup Otomatis', $sec['backup_frequency'], 'jadwal'],
                ] as [$label, $value, $hint])
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500">{{ $label }}</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $value }}</div>
                        <div class="text-[11px] text-gray-500">{{ $hint }}</div>
                    </div>
                @endforeach
            </div>

            @if(count($sec['top_failed_ips']))
                <div class="text-xs text-gray-600 dark:text-gray-400">
                    IP dengan login gagal terbanyak:
                    @foreach($sec['top_failed_ips'] as $row)
                        <code class="font-mono">{{ $row['ip'] }}</code> ({{ $row['jumlah'] }}x){{ !$loop->last ? ',' : '' }}
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</x-filament-panels::page>
