@props(['title', 'value', 'subtitle' => null, 'icon' => null, 'color' => 'emerald', 'glow' => false])

@php
$colorStyles = match($color) {
    'emerald' => ['border' => 'border-emerald-500/20', 'iconBg' => 'bg-emerald-500/10 text-emerald-400', 'glow' => 'hover:shadow-emerald-500/10'],
    'rose' => ['border' => 'border-rose-500/20', 'iconBg' => 'bg-rose-500/10 text-rose-400', 'glow' => 'hover:shadow-rose-500/10'],
    'indigo' => ['border' => 'border-indigo-500/20', 'iconBg' => 'bg-indigo-500/10 text-indigo-400', 'glow' => 'hover:shadow-indigo-500/10'],
    'amber' => ['border' => 'border-amber-500/20', 'iconBg' => 'bg-amber-500/10 text-amber-400', 'glow' => 'hover:shadow-amber-500/10'],
    'cyan' => ['border' => 'border-cyan-500/20', 'iconBg' => 'bg-cyan-500/10 text-cyan-400', 'glow' => 'hover:shadow-cyan-500/10'],
    default => ['border' => 'border-slate-800/80', 'iconBg' => 'bg-slate-800 text-slate-400', 'glow' => ''],
};
@endphp

<div class="relative overflow-hidden bg-[#111827]/90 border {{ $colorStyles['border'] }} rounded-2xl p-5 backdrop-blur-md shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl {{ $colorStyles['glow'] }}">
    <div class="flex items-start justify-between">
        <div class="space-y-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-300">{{ $title }}</p>
            <h4 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $value }}</h4>
            @if ($subtitle)
                <p class="text-xs text-slate-300 font-medium">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="w-10 h-10 rounded-xl {{ $colorStyles['iconBg'] }} flex items-center justify-center flex-shrink-0">
                {{ $icon }}
            </div>
        @endif
    </div>
    {{ $slot ?? '' }}
</div>