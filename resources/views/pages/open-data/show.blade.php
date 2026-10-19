@extends('layouts.app')

@section('title', $dataset->judul . ' - Satu Data ' . ($pengaturan->nama_instansi ?? 'Pemerintah'))
@section('meta_description', Str::limit(strip_tags($dataset->deskripsi ?: $dataset->judul), 160))

@section('content')
<main class="min-h-screen bg-slate-50 dark:bg-slate-900 pb-20">
    <!-- Breadcrumb & Header -->
    <section class="bg-gradient-to-br from-blue-900 via-indigo-950 to-slate-900 text-white py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <nav class="flex items-center gap-2 text-xs text-slate-300 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
                <span>/</span>
                <a href="{{ route('open-data.index') }}" class="hover:text-white">Satu Data</a>
                <span>/</span>
                <span class="text-blue-300 truncate max-w-md">{{ $dataset->judul }}</span>
            </nav>

            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="px-3 py-1 bg-blue-500/20 text-blue-300 border border-blue-400/30 rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ $dataset->kategori }}
                </span>
                @if($dataset->unit)
                    <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 rounded-full text-xs font-bold uppercase tracking-wider">
                        {{ $dataset->unit->nama }}
                    </span>
                @endif
                <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 rounded-full text-xs font-semibold">
                    Versi {{ $dataset->version }}.0
                </span>
            </div>

            <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight mb-4 leading-snug">
                {{ $dataset->judul }}
            </h1>

            <p class="text-slate-300 max-w-4xl text-sm md:text-base leading-relaxed mb-6">
                {{ $dataset->deskripsi ?: 'Dataset publik resmi yang diterbitkan dalam kerangka keterbukaan data instansi pemerintah dan Satu Data Indonesia.' }}
            </p>

            <!-- Metadata Pills & Action Buttons -->
            <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-white/10">
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-300">
                    <div>Sumber: <strong class="text-white">{{ $dataset->sumber }}</strong></div>
                    <div>Periode: <strong class="text-white">{{ $dataset->periode }}</strong></div>
                    <div>Pembaruan: <strong class="text-white">{{ $dataset->tanggal_pembaruan?->translatedFormat('d F Y') }}</strong></div>
                    <div>Unduhan: <strong class="text-white">{{ number_format($dataset->total_unduhan) }} kali</strong></div>
                </div>

                <!-- Download Actions -->
                <div class="flex items-center gap-2">
                    <a 
                        href="{{ route('open-data.download', ['slug' => $dataset->slug, 'format' => 'csv']) }}" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition-all"
                    >
                        <span class="material-symbols-outlined text-[16px]">download</span>
                        <span>Unduh CSV</span>
                    </a>
                    <a 
                        href="{{ route('open-data.download', ['slug' => $dataset->slug, 'format' => 'json']) }}" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-md transition-all"
                    >
                        <span class="material-symbols-outlined text-[16px]">data_object</span>
                        <span>Unduh JSON</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 mt-8 space-y-8">
        <!-- Visualisasi Interaktif Chart.js -->
        <section class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm" x-data="chartComponent(@js($chartConfig))">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">query_stats</span>
                        Visualisasi Grafik Interaktif
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Sumbu X: <strong>{{ $chartConfig['x_label'] ?? 'Kolom 1' }}</strong> | Sumbu Y: <strong>{{ $chartConfig['y_label'] ?? 'Nilai' }}</strong>
                    </p>
                </div>

                <!-- Type Selector (Batang, Garis, Pai) -->
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-700/60 rounded-xl text-xs font-semibold">
                    <button 
                        @click="changeType('bar')" 
                        :class="currentType === 'bar' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-300'"
                        class="px-3 py-1.5 rounded-lg transition-all"
                    >
                        Grafik Batang
                    </button>
                    <button 
                        @click="changeType('line')" 
                        :class="currentType === 'line' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-300'"
                        class="px-3 py-1.5 rounded-lg transition-all"
                    >
                        Grafik Garis
                    </button>
                    <button 
                        @click="changeType('pie')" 
                        :class="currentType === 'pie' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-300'"
                        class="px-3 py-1.5 rounded-lg transition-all"
                    >
                        Grafik Pai
                    </button>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="relative w-full h-80 md:h-96">
                <canvas id="datasetChart"></canvas>
            </div>
        </section>

        <!-- Tabel Pratinjau Data -->
        <section class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">table_rows</span>
                        Pratinjau Data Tabel
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Menampilkan pratinjau data awal (Total: {{ number_format($dataset->row_count) }} baris data).
                    </p>
                </div>
            </div>

            @if(!empty($previewData))
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                        <thead class="bg-slate-100 dark:bg-slate-700/50 text-slate-900 dark:text-white font-bold border-b border-slate-200 dark:border-slate-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">#</th>
                                @foreach(array_keys($previewData[0]) as $header)
                                    <th class="px-4 py-3 whitespace-nowrap">{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-mono">
                            @foreach($previewData as $index => $row)
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-4 py-2.5 text-center text-slate-400">{{ $index + 1 }}</td>
                                    @foreach($row as $colVal)
                                        <td class="px-4 py-2.5 whitespace-nowrap">{{ $colVal }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-slate-500 text-sm">
                    Data tabel tidak dapat dimuat. Silakan unduh berkas CSV asli secara langsung.
                </div>
            @endif
        </section>

        <!-- Dokumentasi Singkat API Baca-Saja & Riwayat Versi -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Dokumentasi API -->
            <section class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-blue-600">api</span>
                    Integrasi API Publik (Read-Only)
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    Developer dan analis data dapat mengonsumsi dataset ini langsung via REST API JSON:
                </p>

                <div class="bg-slate-900 text-slate-100 p-3.5 rounded-xl font-mono text-xs overflow-x-auto mb-3">
                    <span class="text-emerald-400">GET</span> {{ url("/api/v1/datasets/{$dataset->slug}") }}
                </div>

                <div class="bg-slate-50 dark:bg-slate-700/30 p-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs space-y-1.5 text-slate-600 dark:text-slate-300">
                    <div>• <strong>Rate Limit:</strong> 60 permintaan per menit</div>
                    <div>• <strong>Metode:</strong> GET (Tanpa autentikasi, publik)</div>
                    <div>• <strong>Format Respons:</strong> JSON (metadata, schema, data)</div>
                </div>
            </section>

            <!-- Riwayat Versi Dataset -->
            <section class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-blue-600">history</span>
                    Riwayat Versi Dataset
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    Pencatatan riwayat pembaruan berkas data:
                </p>

                <div class="space-y-3">
                    <div class="p-3 bg-blue-50/60 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl text-xs flex items-center justify-between">
                        <div>
                            <div class="font-bold text-blue-900 dark:text-blue-300">Versi {{ $dataset->version }}.0 (Versi Aktif)</div>
                            <div class="text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">
                                Diperbarui: {{ $dataset->tanggal_pembaruan?->translatedFormat('d F Y') }} • {{ number_format($dataset->row_count) }} baris
                            </div>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-600 text-white rounded text-[10px] font-bold">TERKINI</span>
                    </div>

                    @foreach($dataset->versions as $ver)
                        <div class="p-3 bg-slate-50 dark:bg-slate-700/30 border border-slate-200 dark:border-slate-700 rounded-xl text-xs flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-800 dark:text-slate-200">Versi {{ $ver->version }}.0</div>
                                <div class="text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">
                                    {{ $ver->catatan_perubahan ?: 'Pembaruan data berkala' }} • {{ number_format($ver->row_count) }} baris
                                </div>
                            </div>
                            <span class="text-slate-400 text-[11px]">{{ $ver->created_at?->translatedFormat('d/m/Y') }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</main>

<!-- Chart.js Script via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function chartComponent(config) {
        return {
            chart: null,
            currentType: config.type || 'bar',
            init() {
                const ctx = document.getElementById('datasetChart');
                if (!ctx) return;

                const chartConfig = JSON.parse(JSON.stringify(config));
                chartConfig.type = this.currentType;

                this.chart = new Chart(ctx, chartConfig);
            },
            changeType(newType) {
                this.currentType = newType;
                if (!this.chart) return;

                this.chart.destroy();
                const newConfig = JSON.parse(JSON.stringify(config));
                newConfig.type = newType;

                if (newType === 'line') {
                    newConfig.data.datasets[0].fill = false;
                } else if (newType === 'pie') {
                    newConfig.options.plugins.legend.display = true;
                }

                const ctx = document.getElementById('datasetChart');
                this.chart = new Chart(ctx, newConfig);
            }
        };
    }
</script>
@endsection
