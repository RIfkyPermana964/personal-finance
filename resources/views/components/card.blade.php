@props(['title' => null, 'subtitle' => null, 'action' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-[#121A2D] border border-slate-200/80 dark:border-[#1E293B] rounded-2xl p-5 sm:p-6 shadow-xs']) }}>
    @if ($title || $action)
        <div class="flex items-center justify-between gap-4 pb-4 mb-5 border-b border-slate-100 dark:border-[#1A2438]">
            <div>
                @if ($title)
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if ($action)
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>