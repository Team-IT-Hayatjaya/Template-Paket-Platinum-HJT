@php
    $quotaService = app(\App\Services\ContentQuotaService::class);
    $status = $quotaService->getQuotaStatus();
@endphp

@if($status['is_near_limit'] || $status['is_exceeded'])
<div class="mb-4 p-4 rounded-xl border flex items-center justify-between gap-4 {{ $status['is_exceeded'] ? 'bg-rose-50 border-rose-300 text-rose-900 dark:bg-rose-950/40 dark:border-rose-800 dark:text-rose-200' : 'bg-amber-50 border-amber-300 text-amber-900 dark:bg-amber-950/40 dark:border-amber-800 dark:text-amber-200' }}">
    <div class="flex items-center gap-3">
        <span class="text-xl select-none">
            {{ $status['is_exceeded'] ? '⚠️' : 'ℹ️' }}
        </span>
        <div>
            <div class="text-xs font-bold uppercase tracking-wider">
                {{ $status['is_exceeded'] ? 'Pemberitahuan Kuota Pembaruan Konten Terlampaui' : 'Pemberitahuan Kuota Pembaruan Konten' }}
            </div>
            <p class="text-xs mt-0.5 opacity-90">
                Pembaruan konten bulan ini telah mencapai <strong>{{ $status['used'] }} dari {{ $status['limit_label'] }}</strong> ({{ $status['percentage'] }}%).
                @if(!$status['is_exceeded'])
                    Tersisa <strong>{{ $status['remaining'] }}</strong> kuota pembaruan untuk periode {{ $status['period_label'] }}.
                @else
                    Batas kuota bulanan paket {{ ucfirst($status['package']) }} telah tercapai.
                @endif
            </p>
        </div>
    </div>
    <div class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-lg border {{ $status['is_exceeded'] ? 'bg-rose-100 border-rose-300 dark:bg-rose-900/60' : 'bg-amber-100 border-amber-300 dark:bg-amber-900/60' }}">
        {{ $status['percentage'] }}% Terpakai
    </div>
</div>
@endif
