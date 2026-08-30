@props(['variant' => 'primary', 'type' => 'button'])

@php
$variantClass = match($variant) {
    'primary' => 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 border border-emerald-500/30',
    'secondary' => 'bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700',
    'danger' => 'bg-rose-600 hover:bg-rose-500 text-white shadow-lg shadow-rose-600/20 border border-rose-500/30',
    'indigo' => 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/20 border border-indigo-500/30',
    default => 'bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700',
};
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed {$variantClass}"]) }}>
    {{ $slot }}
</button>