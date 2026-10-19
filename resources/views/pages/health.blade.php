<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Kesehatan Sistem (System Health)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col items-center justify-center p-4 sm:p-6">

    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="p-6 {{ $isHealthy ? 'bg-emerald-600' : 'bg-rose-600' }} text-white">
            <div class="flex items-center justify-between">
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $isHealthy ? 'bg-emerald-800 text-emerald-100' : 'bg-rose-800 text-rose-100' }}">
                        Paket {{ strtoupper($payload['package']) }}
                    </span>
                    <h1 class="text-2xl font-bold mt-2">
                        {{ $isHealthy ? '✅ Sistem Beroperasi Normal' : '⚠️ Terdeteksi Gangguan Sistem' }}
                    </h1>
                </div>
                <div class="text-right text-xs opacity-90">
                    <div>Latency: {{ $payload['total_latency_ms'] }} ms</div>
                    <div>{{ date('d M Y, H:i:s') }}</div>
                </div>
            </div>
        </div>

        <!-- Pemeriksaan Komponen -->
        <div class="p-6 space-y-4">
            <!-- 1. Database -->
            @php $db = $payload['checks']['database']; @endphp
            <div class="flex items-start gap-4 p-4 rounded-xl border {{ $db['status'] === 'ok' ? 'border-emerald-200 bg-emerald-50/50' : 'border-rose-200 bg-rose-50/50' }}">
                <div class="w-10 h-10 rounded-lg {{ $db['status'] === 'ok' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center font-bold text-lg shrink-0">
                    🗄️
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-900 text-sm">Koneksi Database</h4>
                        <span class="text-xs font-bold px-2 py-0.5 rounded {{ $db['status'] === 'ok' ? 'bg-emerald-200 text-emerald-800' : 'bg-rose-200 text-rose-800' }}">
                            {{ strtoupper($db['status']) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">{{ $db['message'] }}</p>
                    @if(isset($db['latency_ms']))
                        <div class="text-[11px] text-slate-400 mt-1">Driver: {{ $db['connection'] }} | Respons: {{ $db['latency_ms'] }} ms</div>
                    @endif
                </div>
            </div>

            <!-- 2. Storage -->
            @php $storage = $payload['checks']['storage']; @endphp
            <div class="flex items-start gap-4 p-4 rounded-xl border {{ $storage['status'] === 'ok' ? 'border-emerald-200 bg-emerald-50/50' : 'border-rose-200 bg-rose-50/50' }}">
                <div class="w-10 h-10 rounded-lg {{ $storage['status'] === 'ok' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center font-bold text-lg shrink-0">
                    💾
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-900 text-sm">Penyimpanan Berkas (Storage)</h4>
                        <span class="text-xs font-bold px-2 py-0.5 rounded {{ $storage['status'] === 'ok' ? 'bg-emerald-200 text-emerald-800' : 'bg-rose-200 text-rose-800' }}">
                            {{ strtoupper($storage['status']) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">{{ $storage['message'] }}</p>
                    <div class="text-[11px] text-slate-400 mt-1">Disk Publik & Privat: Izin Tulis Aman</div>
                </div>
            </div>

            <!-- 3. Queue -->
            @php $queue = $payload['checks']['queue']; @endphp
            <div class="flex items-start gap-4 p-4 rounded-xl border {{ $queue['status'] === 'ok' ? 'border-emerald-200 bg-emerald-50/50' : 'border-rose-200 bg-rose-50/50' }}">
                <div class="w-10 h-10 rounded-lg {{ $queue['status'] === 'ok' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center font-bold text-lg shrink-0">
                    ⚡
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-900 text-sm">Layanan Antrean (Queue)</h4>
                        <span class="text-xs font-bold px-2 py-0.5 rounded {{ $queue['status'] === 'ok' ? 'bg-emerald-200 text-emerald-800' : 'bg-rose-200 text-rose-800' }}">
                            {{ strtoupper($queue['status']) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">{{ $queue['message'] }}</p>
                    <div class="text-[11px] text-slate-400 mt-1">Driver Aktif: {{ $queue['driver'] }}</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
            <span>Portal Resmi Instansi &bull; PT Hayat Jaya Teknologi</span>
            <a href="?format=json" class="text-blue-600 hover:underline">Format JSON (API) &rarr;</a>
        </div>
    </div>

</body>
</html>
