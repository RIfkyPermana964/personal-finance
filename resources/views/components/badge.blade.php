@props(['color' => 'slate'])

@php
$classes = match($color) {
    'emerald' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
    'rose' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
    'indigo' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
    'amber' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
    'cyan' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
    'purple' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
    default => 'bg-slate-800 text-slate-400 border-slate-700',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border {$classes}"]) }}>
    {{ $slot }}
</span>