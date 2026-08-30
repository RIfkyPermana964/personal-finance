@props(['title' => null, 'subtitle' => null, 'action' => null])

<div {{ $attributes->merge(['class' => 'bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-5 sm:p-6 backdrop-blur-md shadow-xl']) }}>
    @if ($title || $action)
        <div class="flex items-center justify-between gap-4 pb-4 mb-5 border-b border-slate-800/80">
            <div>
                @if ($title)
                    <h3 class="text-sm font-bold text-white tracking-wide">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-slate-300 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if ($action)
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>