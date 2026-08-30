@props(['name', 'title' => 'Formulir', 'maxWidth' => '2xl'])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '3xl' => 'max-w-3xl',
    default => 'max-w-2xl',
};
@endphp

<div x-show="{{ $name }}" x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md overflow-y-auto"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div @click.away="{{ $name }} = false" 
         class="w-full {{ $maxWidthClass }} bg-[#111827] border border-slate-800 rounded-2xl shadow-2xl overflow-hidden my-8"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/50">
            <h3 class="text-base font-bold text-white">{{ $title }}</h3>
            <button type="button" @click="{{ $name }} = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            {{ $slot }}
        </div>
    </div>
</div>