@props(['title', 'value', 'subtitle' => null, 'icon' => null, 'color' => 'emerald'])

@php
$colorStyles = match($color) {
    'emerald' => ['border' => 'border-emerald-100', 'bg' => 'bg-emerald-50/50', 'iconBg' => 'bg-emerald-100 text-emerald-700', 'valueColor' => 'text-emerald-700'],
    'rose' => ['border' => 'border-rose-100', 'bg' => 'bg-rose-50/50', 'iconBg' => 'bg-rose-100 text-rose-700', 'valueColor' => 'text-rose-700'],
    'indigo' => ['border' => 'border-indigo-100', 'bg' => 'bg-indigo-50/50', 'iconBg' => 'bg-indigo-100 text-indigo-700', 'valueColor' => 'text-indigo-700'],
    'amber' => ['border' => 'border-amber-100', 'bg' => 'bg-amber-50/50', 'iconBg' => 'bg-amber-100 text-amber-700', 'valueColor' => 'text-amber-700'],
    'cyan' => ['border' => 'border-sky-100', 'bg' => 'bg-sky-50/50', 'iconBg' => 'bg-sky-100 text-sky-700', 'valueColor' => 'text-sky-700'],
    default => ['border' => 'border-slate-200/80', 'bg' => 'bg-white', 'iconBg' => 'bg-slate-100 text-slate-700', 'valueColor' => 'text-slate-900'],
};
@endphp

<div class="relative overflow-hidden bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
    <div class="flex items-start justify-between gap-3">
        <div class="space-y-1 min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 truncate">{{ $title }}</p>
            <h4 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight font-mono">{{ $value }}</h4>
            @if ($subtitle)
                <p class="text-xs text-slate-500 font-medium">{{ $subtitle }}</p>
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