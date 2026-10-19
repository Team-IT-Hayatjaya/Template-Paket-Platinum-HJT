<x-filament-panels::page>
    @php
        $latest = $this->getLatestCheck();
        $lockedUsers = $this->getLockedUsers();
        $events = $this->getRecentEvents();
        $statusColor = match ($latest?->status) {
            'modified' => 'border-danger-500 bg-danger-50 text-danger-800 dark:bg-danger-950 dark:text-danger-200',
            'clean', 'baseline' => 'border-success-500 bg-success-50 text-success-800 dark:bg-success-950 dark:text-success-200',
            default => 'border-gray-300 bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-200',
        };
    @endphp

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl p-5 border-l-4 {{ $statusColor }} shadow-xs">
            <div class="text-xs font-bold uppercase tracking-wider">Integritas Berkas</div>
            <div class="text-lg font-black mt-2">{{ $latest?->statusLabel() ?? 'Belum Ada Baseline' }}</div>
            <div class="text-xs mt-1">
                {{ $latest ? 'Terakhir: '.$latest->checked_at->format('d/m/Y H:i').' · '.$latest->files_checked.' berkas' : 'Klik "Perbarui Baseline" untuk memulai.' }}
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Akun Terkunci</div>
            <div class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ $lockedUsers->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Ambang: {{ config('security.lockout.max_attempts') }}x gagal · {{ config('security.lockout.decay_minutes') }} menit</div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Blokir IP Aktif</div>
            <div class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ $this->getActiveBlockCount() }}</div>
            <div class="text-xs text-gray-500 mt-1">
                <a href="{{ \App\Filament\Resources\IpBlocklists\IpBlocklistResource::getUrl('index') }}" class="text-primary-600 hover:underline">Kelola daftar blokir &rarr;</a>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Login Gagal (24 Jam)</div>
            <div class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ $this->getFailedLoginCount24h() }}</div>
            <div class="text-xs text-gray-500 mt-1">Backup otomatis: {{ \App\Support\Package::limit('backup_frequency') === 'weekly' ? 'Mingguan (Senin 02.00)' : ucfirst((string) \App\Support\Package::limit('backup_frequency')) }}</div>
        </div>
    </div>

    {{-- Detail perubahan berkas terakhir --}}
    @if($latest && $latest->status === 'modified')
        <section aria-labelledby="judul-perubahan" class="rounded-xl p-5 border border-danger-300 bg-white dark:bg-gray-900 shadow-xs space-y-3">
            <h3 id="judul-perubahan" class="text-base font-bold text-danger-700 dark:text-danger-300">Perubahan Berkas Terdeteksi ({{ $latest->totalChanges() }})</h3>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-xs">
                @foreach(['Diubah' => $latest->modified_files, 'Hilang' => $latest->missing_files, 'Baru' => $latest->new_files] as $label => $files)
                    <div>
                        <div class="font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ $label }} ({{ count($files ?? []) }})</div>
                        <ul class="font-mono space-y-0.5 text-gray-600 dark:text-gray-400 max-h-48 overflow-y-auto">
                            @forelse(array_slice($files ?? [], 0, 100) as $f)
                                <li>{{ $f }}</li>
                            @empty
                                <li class="italic">-</li>
                            @endforelse
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Akun terkunci --}}
    <section aria-labelledby="judul-akun-terkunci" class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs space-y-3">
        <h3 id="judul-akun-terkunci" class="text-base font-bold text-gray-900 dark:text-white">Akun yang Sedang Dikunci</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="py-2.5 px-3">Nama</th>
                        <th scope="col" class="py-2.5 px-3">Email</th>
                        <th scope="col" class="py-2.5 px-3">Terkunci Hingga</th>
                        <th scope="col" class="py-2.5 px-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($lockedUsers as $u)
                        <tr wire:key="locked-{{ $u->id }}">
                            <td class="py-2.5 px-3 font-semibold">{{ $u->name }}</td>
                            <td class="py-2.5 px-3">{{ $u->email }}</td>
                            <td class="py-2.5 px-3">{{ $u->locked_until->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 px-3 text-right">
                                <x-filament::button size="xs" color="warning" icon="heroicon-m-lock-open"
                                    wire:click="unlockUser({{ $u->id }})"
                                    wire:confirm="Buka kunci akun {{ $u->email }}?">
                                    Buka Kunci
                                </x-filament::button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-xs text-gray-500">Tidak ada akun yang terkunci.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Peristiwa keamanan terbaru --}}
    <section aria-labelledby="judul-peristiwa" class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-xs space-y-3">
        <h3 id="judul-peristiwa" class="text-base font-bold text-gray-900 dark:text-white">Peristiwa Keamanan Terbaru</h3>
        <ul class="divide-y divide-gray-200 dark:divide-gray-800 text-sm">
            @forelse($events as $ev)
                <li class="py-2 flex flex-wrap items-start justify-between gap-2">
                    <span class="text-gray-800 dark:text-gray-200">{{ $ev->description }}
                        @if($ev->properties['ip'] ?? null)
                            <code class="ml-1 text-xs text-gray-500">{{ $ev->properties['ip'] }}</code>
                        @endif
                    </span>
                    <time class="text-xs text-gray-500" datetime="{{ $ev->created_at->toIso8601String() }}">{{ $ev->created_at->diffForHumans() }}</time>
                </li>
            @empty
                <li class="py-4 text-center text-xs text-gray-500">Belum ada peristiwa keamanan.</li>
            @endforelse
        </ul>
    </section>
</x-filament-panels::page>
