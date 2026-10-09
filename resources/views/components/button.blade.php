@props(['variant' => 'primary', 'type' => 'button'])

@php
$variantClass = match($variant) {
    'primary' => 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm hover:shadow-md border border-transparent',
    'secondary' => 'bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-xs hover:border-slate-300 dark:hover:border-slate-600',
    'danger' => 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm hover:shadow-md border border-transparent',
    'indigo' => 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm hover:shadow-md border border-transparent',
    'amber' => 'bg-amber-600 hover:bg-amber-700 text-white shadow-sm hover:shadow-md border border-transparent',
    default => 'bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-white text-white dark:text-slate-900 border border-transparent',
};
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed {$variantClass}"]) }}>
    {{ $slot }}
</button>