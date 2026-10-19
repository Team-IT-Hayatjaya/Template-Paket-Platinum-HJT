@extends('layouts.app')

@section('title', $statistik->judul . ' - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))
@section('meta_title', 'Data Statistik: ' . $statistik->judul)
@section('meta_description', 'Publikasi data statistik ' . $statistik->kategori . ' periode ' . $statistik->periode . ' resmi.')

@section('content')
<div class="space-y-space-xl max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Data & Statistik', 'url' => route('statistik.index')],
        ['label' => $statistik->judul],
    ]" />

    <!-- Header Section -->
    <section class="bg-surface-container-lowest p-space-lg lg:p-space-xl rounded-2xl shadow-2xs border border-surface-container space-y-space-md">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-secondary-fixed text-primary uppercase tracking-wider">
                {{ $statistik->kategori }}
            </span>
            <span class="text-xs font-semibold text-on-surface-variant bg-surface-container px-3 py-1 rounded-full">
                Periode: {{ $statistik->periode }}
            </span>
            <span class="text-xs font-semibold text-primary bg-primary-fixed/20 px-3 py-1 rounded-full">
                Tahun {{ $statistik->tahun }}
            </span>
            @if($statistik->sumber)
                <span class="text-xs text-on-surface-variant flex items-center gap-1 bg-surface-container px-3 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[14px] text-secondary">source</span>
                    <span>Sumber: {{ $statistik->sumber }}</span>
                </span>
            @endif
        </div>

        <h1 class="font-headline-xl text-2xl sm:text-3xl font-bold tracking-tight text-primary leading-tight">
            {{ $statistik->judul }}
        </h1>
        <p class="font-body-md text-sm text-on-surface-variant max-w-3xl leading-relaxed">
            Data agregat resmi {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }} disajikan secara terbuka, transparan, dan dapat diunduh dalam format terbuka sesuai standar Satu Data Indonesia.
        </p>
    </section>

    <!-- Konten Data Statistik & Grafik Interaktif -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <div class="lg:col-span-8 space-y-space-lg">

            @if(!empty($statistik->data_grafik))
                <!-- 1. KARTU GRAFIK INTERAKTIF (DARI EXCEL / SPREADSHEET) -->
                <div class="bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-2xs space-y-space-md" id="section-grafik">
                    <!-- Top Bar Grafik: Judul & Tombol Aksi -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-surface-container pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-primary text-on-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">query_stats</span>
                            </span>
                            <div>
                                <h2 class="font-title-lg text-base font-bold text-primary">Visualisasi Grafik Interaktif</h2>
                                <p class="text-[11px] text-on-surface-variant">Dihasilkan langsung dari berkas data spreadsheet resmi</p>
                            </div>
                        </div>

                        <!-- Selector Model Grafik & Export PNG -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <div class="inline-flex rounded-lg bg-surface-container p-0.5 text-xs font-bold" role="group">
                                <button type="button" onclick="gantiTipeGrafik('bar')" id="btn-chart-bar" class="px-2.5 py-1 rounded-md transition text-primary hover:bg-surface-container-high">
                                    Batang
                                </button>
                                <button type="button" onclick="gantiTipeGrafik('line')" id="btn-chart-line" class="px-2.5 py-1 rounded-md transition text-on-surface-variant hover:bg-surface-container-high">
                                    Garis
                                </button>
                                <button type="button" onclick="gantiTipeGrafik('pie')" id="btn-chart-pie" class="px-2.5 py-1 rounded-md transition text-on-surface-variant hover:bg-surface-container-high">
                                    Pie
                                </button>
                                <button type="button" onclick="gantiTipeGrafik('doughnut')" id="btn-chart-doughnut" class="px-2.5 py-1 rounded-md transition text-on-surface-variant hover:bg-surface-container-high">
                                    Donat
                                </button>
                            </div>
                            <button type="button" onclick="unduhGrafikPNG()" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-surface-container hover:bg-surface-container-high text-primary text-xs font-bold transition border border-surface-container" title="Unduh Gambar Grafik">
                                <span class="material-symbols-outlined text-[15px]">image</span>
                                <span class="hidden sm:inline">Simpan PNG</span>
                            </button>
                        </div>
                    </div>

                    <!-- Ringkasan Metrik Angka Kunci -->
                    @if(!empty($statistik->data_grafik['summary']))
                        @php $sum = $statistik->data_grafik['summary']; @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                            <div class="p-2.5 rounded-xl bg-surface-container-low border border-surface-container">
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant tracking-wider">Total Akumulasi</span>
                                <div class="text-lg font-bold text-primary mt-0.5">{{ number_format($sum['total'] ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-surface-container-low border border-surface-container">
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant tracking-wider">Rata-Rata</span>
                                <div class="text-lg font-bold text-secondary mt-0.5">{{ number_format($sum['rata_rata'] ?? 0, 1, ',', '.') }}</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-surface-container-low border border-surface-container">
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant tracking-wider">Nilai Tertinggi</span>
                                <div class="text-lg font-bold text-emerald-600 mt-0.5">{{ number_format($sum['tertinggi'] ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-surface-container-low border border-surface-container">
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant tracking-wider">Cakupan Data</span>
                                <div class="text-lg font-bold text-primary mt-0.5">{{ $sum['jumlah_baris'] ?? 0 }} Baris</div>
                            </div>
                        </div>
                    @endif

                    <!-- Area Kanvas Chart.js -->
                    <div class="relative w-full h-72 sm:h-80 md:h-96 pt-2">
                        <canvas id="canvasStatistik"></canvas>
                    </div>
                </div>
            @endif

            <!-- 2. TABEL DATA RINCIAN (DARI SPREADSHEET ATAU RICH TEXT) -->
            <div class="bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-2xs space-y-space-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-surface-container pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-secondary text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">table_chart</span>
                        </span>
                        <div>
                            <h2 class="font-title-lg text-base font-bold text-primary">Tabel Rincian Data Statistik</h2>
                            <p class="text-[11px] text-on-surface-variant">Struktur matriks data tabular terverifikasi</p>
                        </div>
                    </div>

                    <!-- Tombol Unduh Berkas Excel & PDF -->
                    <div class="flex items-center gap-2 flex-wrap">
                        @if(!empty($statistik->berkas_spreadsheet))
                            <a href="{{ asset('storage/' . $statistik->berkas_spreadsheet) }}" target="_blank" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-2xs">
                                <span class="material-symbols-outlined text-[16px]">file_download</span>
                                <span>Unduh Excel (.xlsx)</span>
                            </a>
                        @endif

                        @if(!empty($statistik->berkas_pdf))
                            <a href="{{ asset('storage/' . $statistik->berkas_pdf) }}" target="_blank" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-white text-xs font-bold transition shadow-2xs">
                                <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                                <span>Unduh PDF</span>
                            </a>
                        @endif
                    </div>
                </div>

                @if(!empty($statistik->data_tabel['headers']) && !empty($statistik->data_tabel['rows']))
                    <!-- Filter Pencarian Cepat di Tabel -->
                    <div class="flex items-center justify-between gap-2 pt-1">
                        <div class="relative w-full sm:w-64">
                            <span class="material-symbols-outlined absolute left-2.5 top-2 text-[16px] text-on-surface-variant">search</span>
                            <input type="text" id="tabelSearchInput" onkeyup="filterTabelData()" placeholder="Cari dalam tabel..." class="w-full bg-surface-container-low pl-8 pr-3 py-1.5 rounded-lg text-xs border border-surface-container text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>
                        <span class="text-[11px] text-on-surface-variant hidden sm:inline" id="rowCounter">
                            Total: {{ count($statistik->data_tabel['rows']) }} entri
                        </span>
                    </div>

                    <!-- Tabel Dinamis Dari Excel -->
                    <div class="overflow-x-auto rounded-xl border border-surface-container">
                        <table class="w-full text-xs text-left border-collapse" id="dynamicDataTable">
                            <thead>
                                <tr class="bg-primary text-on-primary">
                                    <th class="p-3 text-center w-10 font-bold border-r border-primary-container/30">#</th>
                                    @foreach($statistik->data_tabel['headers'] as $h)
                                        <th class="p-3 font-bold border-r border-primary-container/30 whitespace-nowrap">{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container">
                                @foreach($statistik->data_tabel['rows'] as $idx => $row)
                                    <tr class="hover:bg-surface-container-low transition-colors {{ $loop->even ? 'bg-surface-container-lowest' : 'bg-surface-container-low/40' }}">
                                        <td class="p-2.5 text-center font-mono text-on-surface-variant text-[11px]">{{ $idx + 1 }}</td>
                                        @foreach($row as $colIdx => $cell)
                                            <td class="p-2.5 {{ $colIdx === 0 ? 'font-bold text-primary' : 'text-on-surface font-mono' }} whitespace-nowrap">
                                                {{ is_numeric($cell) ? number_format($cell, 0, ',', '.') : $cell }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif(!empty($statistik->isi))
                    <!-- Tabel Rich Text Bawaan -->
                    <div class="prose max-w-none text-sm text-on-surface leading-relaxed overflow-x-auto">
                        {!! $statistik->isi !!}
                    </div>
                @else
                    <div class="p-6 text-center text-xs text-on-surface-variant bg-surface-container-low rounded-xl">
                        Rincian data sedang dalam proses sinkronisasi.
                    </div>
                @endif
            </div>

            <!-- Gambar Infografis jika ada -->
            @if(!empty($statistik->gambar_infografis))
                <div class="bg-surface-container-lowest p-space-md rounded-2xl border border-surface-container shadow-2xs overflow-hidden">
                    <h3 class="font-title-md text-sm font-bold text-primary mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-secondary">image</span>
                        <span>Visualisasi Infografis Tambahan</span>
                    </h3>
                    <img src="{{ asset('storage/' . $statistik->gambar_infografis) }}" alt="{{ $statistik->judul }}" class="w-full h-auto rounded-xl border border-surface-container shadow-xs" loading="lazy">
                </div>
            @endif
        </div>

        <!-- Sidebar Kanan -->
        <div class="lg:col-span-4 space-y-space-md">
            <!-- Permintaan Data Tambahan PPID -->
            <div class="bg-surface-container-lowest p-space-md rounded-2xl border border-surface-container shadow-2xs space-y-space-sm">
                <h3 class="font-title-md text-sm font-bold text-primary border-b border-surface-container pb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-secondary">help</span>
                    <span>Butuh Data Lebih Spesifik?</span>
                </h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Masyarakat atau peneliti yang memerlukan rincian data sektoral dapat mengajukan Permohonan Informasi Publik resmi kepada PPID.
                </p>
                <div class="pt-2">
                    <a href="{{ route('ppid.permohonan') }}" class="w-full text-center block py-2.5 rounded-xl bg-primary text-on-primary font-bold text-xs hover:bg-primary-container transition shadow-2xs">
                        Ajukan Permohonan Data PPID &rarr;
                    </a>
                </div>
            </div>

            <!-- Metadata Teknis Dokumen -->
            <div class="bg-surface-container-lowest p-space-md rounded-2xl border border-surface-container shadow-2xs space-y-space-xs text-xs">
                <h3 class="font-title-md text-xs font-bold text-primary uppercase tracking-wider border-b border-surface-container pb-2 mb-2">
                    Metadata Keterbukaan Data
                </h3>
                <div class="flex justify-between py-1 border-b border-surface-container/40">
                    <span class="text-on-surface-variant">Standar:</span>
                    <span class="font-bold text-primary">Satu Data Indonesia (SDI)</span>
                </div>
                <div class="flex justify-between py-1 border-b border-surface-container/40">
                    <span class="text-on-surface-variant">Format Terbuka:</span>
                    <span class="font-bold text-primary">XLSX, CSV, PDF, Web</span>
                </div>
                <div class="flex justify-between py-1 border-b border-surface-container/40">
                    <span class="text-on-surface-variant">Lisensi Data:</span>
                    <span class="font-bold text-primary">Domain Publik (Bebas Pakai)</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-on-surface-variant">Pembaruan:</span>
                    <span class="font-bold text-primary">{{ $statistik->updated_at?->format('d M Y') ?? date('d M Y') }}</span>
                </div>
            </div>

            <!-- Data Statistik Lainnya -->
            @if(isset($statistikLain) && $statistikLain->isNotEmpty())
                <div class="bg-surface-container-lowest p-space-md rounded-2xl border border-surface-container shadow-2xs space-y-space-sm">
                    <h3 class="font-title-md text-sm font-bold text-primary border-b border-surface-container pb-2">
                        Data Statistik Terkait
                    </h3>
                    <div class="divide-y divide-surface-container">
                        @foreach($statistikLain as $item)
                            <a href="{{ route('statistik.detail', $item->slug) }}" class="block py-2.5 hover:bg-surface-container-low transition rounded-lg px-2 group">
                                <div class="text-[11px] font-bold text-secondary uppercase">{{ $item->kategori }}</div>
                                <div class="text-xs font-bold text-primary group-hover:text-secondary transition-colors mt-0.5 line-clamp-2">{{ $item->judul }}</div>
                                <div class="text-[11px] text-on-surface-variant mt-1">Periode: {{ $item->periode }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
@if(!empty($statistik->data_grafik))
    const rawChartData = @json($statistik->data_grafik);
    let currentChartType = '{{ $statistik->tipe_grafik ?? "bar" }}';
    let chartInstance = null;

    function renderStatistikChart(type) {
        const canvas = document.getElementById('canvasStatistik');
        if (!canvas) return;

        if (chartInstance) {
            chartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');
        const isCircular = (type === 'pie' || type === 'doughnut');

        // Buat salinan dataset agar tidak merusak data asli
        const datasets = JSON.parse(JSON.stringify(rawChartData.datasets || []));

        // Jika circular, gunakan palet warna berbeda untuk tiap irisan segmen
        if (isCircular && datasets.length > 0) {
            const colors = [
                'rgba(30, 58, 138, 0.85)',
                'rgba(217, 119, 6, 0.85)',
                'rgba(5, 150, 105, 0.85)',
                'rgba(2, 132, 199, 0.85)',
                'rgba(124, 58, 237, 0.85)',
                'rgba(225, 29, 72, 0.85)',
                'rgba(202, 138, 4, 0.85)',
                'rgba(14, 165, 233, 0.85)',
                'rgba(16, 185, 129, 0.85)',
                'rgba(245, 158, 11, 0.85)',
            ];
            datasets[0].backgroundColor = rawChartData.labels.map((_, i) => colors[i % colors.length]);
            datasets[0].borderColor = '#ffffff';
            datasets[0].borderWidth = 2;
        }

        chartInstance = new Chart(ctx, {
            type: type,
            data: {
                labels: rawChartData.labels || [],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: isCircular ? 'right' : 'top',
                        labels: {
                            font: { family: 'Source Sans 3', size: 12 },
                            boxWidth: 14,
                            padding: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null && context.parsed.y !== undefined) {
                                    label += new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                } else if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('id-ID').format(context.parsed);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: isCircular ? {} : {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Source Sans 3', size: 11 },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { family: 'Source Sans 3', size: 11 },
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });

        // Update active button state
        ['bar', 'line', 'pie', 'doughnut'].forEach(t => {
            const btn = document.getElementById('btn-chart-' + t);
            if (btn) {
                if (t === type) {
                    btn.classList.add('bg-primary', 'text-white', 'shadow-2xs');
                    btn.classList.remove('text-primary', 'text-on-surface-variant');
                } else {
                    btn.classList.remove('bg-primary', 'text-white', 'shadow-2xs');
                    btn.classList.add('text-on-surface-variant');
                }
            }
        });
    }

    function gantiTipeGrafik(newType) {
        currentChartType = newType;
        renderStatistikChart(newType);
    }

    function unduhGrafikPNG() {
        if (!chartInstance) return;
        const link = document.createElement('a');
        link.download = 'grafik-{{ $statistik->slug }}.png';
        link.href = chartInstance.toBase64Image();
        link.click();
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderStatistikChart(currentChartType);
    });
@endif

// Pencarian cepat data dalam tabel
function filterTabelData() {
    const input = document.getElementById('tabelSearchInput');
    const filter = input ? input.value.toLowerCase() : '';
    const table = document.getElementById('dynamicDataTable');
    if (!table) return;

    const tr = table.getElementsByTagName('tr');
    let visibleCount = 0;

    for (let i = 1; i < tr.length; i++) {
        const text = tr[i].innerText.toLowerCase();
        if (text.indexOf(filter) > -1) {
            tr[i].style.display = '';
            visibleCount++;
        } else {
            tr[i].style.display = 'none';
        }
    }

    const counter = document.getElementById('rowCounter');
    if (counter) {
        counter.innerText = 'Ditemukan: ' + visibleCount + ' entri';
    }
}
</script>
@endpush
