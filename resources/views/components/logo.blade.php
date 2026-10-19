@props(['class' => ''])

@if(!empty($pengaturan?->logo_url))
    <img src="{{ $pengaturan->logo_url }}" 
         alt="Lambang Resmi {{ $pengaturan->nama_instansi }}" 
         loading="lazy" 
         class="h-10 sm:h-12 w-auto object-contain shrink-0 {{ $class }}">
@else
    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center font-extrabold text-sm sm:text-base text-white shadow-sm shrink-0 select-none {{ $class }}" 
         style="background: linear-gradient(135deg, var(--gov-secondary, #b91c1c), var(--gov-primary, #1e3a8a));" 
         aria-label="Lambang Resmi Republik Indonesia">
        <span>RI</span>
    </div>
@endif

