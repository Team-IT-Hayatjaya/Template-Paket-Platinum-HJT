@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'helpText' => null,
    'error' => null,
    'options' => [], // Untuk tipe select
    'rows' => 3,     // Untuk textarea
])

@php
    $inputId = 'input_' . str_replace(['[', ']', '.'], '_', $name);
    $errorId = $inputId . '_error';
    $helpId = $inputId . '_help';
    
    $describedBy = [];
    if ($error) $describedBy[] = $errorId;
    if ($helpText) $describedBy[] = $helpId;
    $ariaDescribedBy = !empty($describedBy) ? implode(' ', $describedBy) : null;

    $baseControlClass = "w-full rounded-lg text-sm transition-colors border " . 
        ($error 
            ? "border-red-500 bg-red-50/20 text-slate-900 focus:border-red-600 focus:ring-2 focus:ring-red-500 focus:outline-none" 
            : "border-slate-300 bg-white text-slate-900 focus:border-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none") .
        " px-3.5 py-2.5 shadow-sm";
@endphp

<div class="space-y-1.5 w-full">
    <label for="{{ $inputId }}" class="block text-sm font-semibold text-slate-800">
        {{ $label }}
        @if($required)
            <span class="text-red-600 ml-0.5" aria-hidden="true">*</span>
            <span class="sr-only">(wajib diisi)</span>
        @else
            <span class="text-xs font-normal text-slate-500 ml-1">(opsional)</span>
        @endif
    </label>

    @if($type === 'textarea')
        <textarea id="{{ $inputId }}"
                  name="{{ $name }}"
                  rows="{{ $rows }}"
                  placeholder="{{ $placeholder }}"
                  {{ $required ? 'required' : '' }}
                  @if($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
                  @if($error) aria-invalid="true" @endif
                  {{ $attributes->merge(['class' => $baseControlClass]) }}>{{ old($name, $value) }}</textarea>

    @elseif($type === 'select')
        <select id="{{ $inputId }}"
                name="{{ $name }}"
                {{ $required ? 'required' : '' }}
                @if($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
                @if($error) aria-invalid="true" @endif
                {{ $attributes->merge(['class' => $baseControlClass]) }}>
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $val => $text)
                <option value="{{ $val }}" {{ old($name, $value) == $val ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endforeach
        </select>

    @else
        <input id="{{ $inputId }}"
               name="{{ $name }}"
               type="{{ $type }}"
               value="{{ old($name, $value) }}"
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}
               @if($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
               @if($error) aria-invalid="true" @endif
               {{ $attributes->merge(['class' => $baseControlClass]) }}>
    @endif

    @if($helpText)
        <p id="{{ $helpId }}" class="text-xs text-slate-500">
            {{ $helpText }}
        </p>
    @endif

    @if($error)
        <p id="{{ $errorId }}" class="text-xs font-semibold text-red-600 flex items-center gap-1 mt-1" role="alert">
            <span aria-hidden="true">⚠️</span> {{ $error }}
        </p>
    @endif
</div>
