@props([
    'documents' => [],
    'caption' => 'Daftar Dokumen Informasi Publik Resmi',
])

<div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <!-- Indikator Geser Layar Sentuh Mobile -->
    <div class="sm:hidden flex items-center gap-1.5 px-3.5 py-2 bg-blue-50/70 border-b border-blue-100 text-[11px] text-blue-900 font-medium select-none">
        <span aria-hidden="true">↔️</span>
        <span>Geser tabel ke samping untuk melihat rincian dokumen & tombol unduh</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm text-slate-700 divide-y divide-slate-200">
            <caption class="sr-only">{{ $caption }}</caption>
            <thead class="bg-slate-50 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-4 py-3.5 text-center w-12">No.</th>
                    <th scope="col" class="px-4 py-3.5">Judul Dokumen & Informasi</th>
                    <th scope="col" class="px-4 py-3.5 whitespace-nowrap">Kategori</th>
                    <th scope="col" class="px-4 py-3.5 text-center whitespace-nowrap">Tahun / Tanggal</th>
                    <th scope="col" class="px-4 py-3.5 text-center whitespace-nowrap">Format / Ukuran</th>
                    <th scope="col" class="px-4 py-3.5 text-right whitespace-nowrap">Aksi Unduh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($documents as $index => $doc)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-4 text-center font-medium text-slate-500">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-semibold text-slate-900 leading-snug">
                                {{ $doc['judul'] ?? '-' }}
                            </div>
                            @if(!empty($doc['keterangan']))
                                <div class="text-xs text-slate-500 mt-1">
                                    {{ $doc['keterangan'] }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                {{ $doc['kategori'] ?? 'Informasi Publik' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap text-slate-600">
                            {{ $doc['tahun'] ?? date('Y') }}
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase bg-slate-100 text-slate-700">
                                {{ $doc['format'] ?? 'PDF' }} ({{ $doc['ukuran'] ?? '1.2 MB' }})
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap">
                            <a href="{{ $doc['url'] ?? '#' }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-blue-600 shadow-sm"
                               download
                               aria-label="Unduh dokumen {{ $doc['judul'] ?? 'Informasi Publik' }} format {{ $doc['format'] ?? 'PDF' }}">
                                <span aria-hidden="true">📥</span> Unduh
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500 italic">
                            Belum ada dokumen informasi publik yang tersedia untuk kategori ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
