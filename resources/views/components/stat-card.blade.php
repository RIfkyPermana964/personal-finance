@props(['title', 'value', 'subtitle' => null, 'icon' => null, 'color' => 'emerald'])

@php
$colorStyles = match($color) {
    'emerald' => ['border' => 'border-emerald-100 dark:border-emerald-900/50', 'bg' => 'bg-emerald-50/50 dark:bg-emerald-950/20', 'iconBg' => 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400', 'valueColor' => 'text-emerald-700 dark:text-emerald-400'],
    'rose' => ['border' => 'border-rose-100 dark:border-rose-900/50', 'bg' => 'bg-rose-50/50 dark:bg-rose-950/20', 'iconBg' => 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400', 'valueColor' => 'text-rose-700 dark:text-rose-400'],
    'indigo' => ['border' => 'border-indigo-100 dark:border-indigo-900/50', 'bg' => 'bg-indigo-50/50 dark:bg-indigo-950/20', 'iconBg' => 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400', 'valueColor' => 'text-indigo-700 dark:text-indigo-400'],
    'amber' => ['border' => 'border-amber-100 dark:border-amber-900/50', 'bg' => 'bg-amber-50/50 dark:bg-amber-950/20', 'iconBg' => 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400', 'valueColor' => 'text-amber-700 dark:text-amber-400'],
    'cyan' => ['border' => 'border-sky-100 dark:border-sky-900/50', 'bg' => 'bg-sky-50/50 dark:bg-sky-950/20', 'iconBg' => 'bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400', 'valueColor' => 'text-sky-700 dark:text-sky-400'],
    default => ['border' => 'border-slate-200/80 dark:border-slate-800', 'bg' => 'bg-white dark:bg-[#151E2E]', 'iconBg' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300', 'valueColor' => 'text-slate-900 dark:text-white'],
};
@endphp

<div class="relative overflow-hidden bg-white dark:bg-[#151E2E] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
    <div class="flex items-start justify-between gap-3">
        <div class="space-y-1 min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">{{ $title }}</p>
            <h4 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight font-mono">{{ $value }}</h4>
            @if ($subtitle)
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $subtitle }}</p>
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