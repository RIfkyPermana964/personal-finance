@props([
    'name' => 'amount',
    'id' => null,
    'value' => '',
    'placeholder' => 'Contoh: 100.000',
    'required' => false,
    'model' => null,
    'label' => 'Nominal (Rp)',
])

@php
    $inputId = $id ?? $name;
    $initialValue = old($name, $value);
    if (is_numeric($initialValue) && $initialValue > 0) {
        $initialValue = number_format((float)$initialValue, 0, ',', '.');
    }
@endphp

<div>
    @if ($label)
        <label for="{{ $inputId }}" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
            {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
        </label>
    @endif
    <div class="relative rounded-xl shadow-2xs">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 dark:text-slate-500 font-bold text-sm">
            Rp
        </div>
        <input type="text"
               inputmode="numeric"
               name="{{ $name }}"
               id="{{ $inputId }}"
               value="{{ $initialValue }}"
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}
               x-money
               @if($model) x-model="{{ $model }}" @endif
               {{ $attributes->merge(['class' => 'w-full pl-11 pr-4 py-2.5 bg-white dark:bg-[#0B1020] border border-slate-200 dark:border-[#1A2438] rounded-xl text-slate-900 dark:text-white font-mono text-base font-semibold focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition']) }}>
    </div>
    <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">Titik otomatis ditambahkan per kelipatan ribuan</p>
</div>
