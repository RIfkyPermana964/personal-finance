<x-layouts.app title="Budget Bulanan" header="Pagu Anggaran (Budgeting)" subheader="Tetapkan batas pengeluaran bulanan per kategori untuk mengontrol pengeluaran" x-data="{ createModal: false }">

    <!-- Top Summary & Period Filter -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <!-- Summary Stats Card -->
        <div class="sm:col-span-2 p-5 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md flex flex-col justify-between space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-300 font-semibold uppercase">Total Anggaran {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
                    <div class="flex items-baseline gap-3 mt-1">
                        <h3 class="text-2xl font-black text-amber-400 font-mono">Rp {{ number_format($budgets['total_budget'], 0, ',', '.') }}</h3>
                        <span class="text-xs font-mono text-slate-300">Terpakai: Rp {{ number_format($budgets['total_spent'], 0, ',', '.') }} ({{ $budgets['overall_percentage'] }}%)</span>
                    </div>
                </div>
                <button @click="createModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-amber-600/30 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Tetapkan Anggaran
                </button>
            </div>

            <!-- Overall Progress Bar -->
            <div class="space-y-1.5">
                <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-500 {{ $budgets['overall_percentage'] > 100 ? 'bg-rose-500' : ($budgets['overall_percentage'] > 75 ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ min(100, $budgets['overall_percentage']) }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] text-slate-300 font-medium">
                    <span>Sisa Pagu Global: <strong class="text-emerald-400 font-mono">Rp {{ number_format($budgets['total_remaining'], 0, ',', '.') }}</strong></span>
                    <span>{{ $budgets['overall_percentage'] }}% digunakan</span>
                </div>
            </div>
        </div>

        <!-- Filter Periode Bulan -->
        <div class="p-5 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md flex flex-col justify-center">
            <p class="text-xs text-slate-300 font-semibold mb-2">Pilih Periode Anggaran</p>
            <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center gap-2">
                <select name="month" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('M') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                    @for ($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold">
                    Go
                </button>
            </form>
        </div>

    </div>

    <!-- Category Budget Cards Grid -->
    <div class="space-y-4">
        <h3 class="text-sm font-bold text-white tracking-wide">Rincian Anggaran Kategori</h3>

        @if (empty($budgets['items']))
            <div class="bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-12 text-center text-slate-300 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="text-sm font-bold text-white">Belum Ada Pagu Anggaran</h4>
                <p class="text-xs text-slate-300 max-w-sm mx-auto">Buat batas pengeluaran untuk kategori seperti Makanan, Tagihan Listrik, Bensin, atau Kebutuhan NOC untuk mengontrol pengeluaran Anda.</p>
                <button @click="createModal = true" class="inline-flex px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-amber-600/30">
                    + Buat Anggaran Pertama
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ($budgets['items'] as $item)
                    <div class="bg-[#111827]/90 border {{ $item['is_over_budget'] ? 'border-rose-500/40 glow-rose' : 'border-slate-800/80' }} rounded-2xl p-5 space-y-4 backdrop-blur-md shadow-lg transition hover:shadow-xl">
                        
                        <!-- Header Card -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-white font-bold" style="color: {{ $item['category_color'] }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-white">{{ $item['category_name'] }}</h4>
                                    <p class="text-[11px] text-slate-300 font-mono">Pagu: Rp {{ number_format($item['amount'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <button @click="deleteAction = '{{ route('budgets.destroy', $item['id']) }}'; deleteMessage = 'Hapus pagu anggaran untuk {{ $item['category_name'] }}?'; deleteModal = true;" 
                                    class="text-slate-500 hover:text-rose-400 p-1.5 rounded-lg hover:bg-rose-500/10 transition" title="Hapus Anggaran">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                        <!-- Progress Bar & Warnings -->
                        <div class="space-y-1.5">
                            <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden border border-slate-700/60">
                                <div class="h-2.5 rounded-full transition-all duration-500 {{ $item['is_over_budget'] ? 'bg-rose-500' : ($item['percentage'] > 75 ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ min(100, $item['percentage']) }}%"></div>
                            </div>
                            
                            <div class="flex items-center justify-between text-xs font-mono">
                                <span class="text-slate-300">Terpakai: Rp {{ number_format($item['spent'], 0, ',', '.') }}</span>
                                <span class="font-bold {{ $item['is_over_budget'] ? 'text-rose-400' : 'text-slate-300' }}">{{ $item['percentage'] }}%</span>
                            </div>
                        </div>

                        <!-- Footer Status -->
                        <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs">
                            @if ($item['is_over_budget'])
                                <span class="inline-flex items-center gap-1 text-rose-400 font-bold text-[11px]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Over Budget Rp {{ number_format($item['spent'] - $item['amount'], 0, ',', '.') }}!
                                </span>
                            @else
                                <span class="text-slate-300 text-[11px]">Sisa Anggaran: <strong class="text-emerald-400 font-mono">Rp {{ number_format($item['remaining'], 0, ',', '.') }}</strong></span>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal Set Anggaran Baru -->
    <x-modal name="createModal" title="Tetapkan Pagu Anggaran Bulanan">
        <form method="POST" action="{{ route('budgets.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori Pengeluaran *</label>
                <select name="category_id" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                    <option value="">-- Pilih Kategori Utama --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-300 mt-1">Perhitungan mencakup seluruh subkategori di bawah kategori ini secara otomatis.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Batas Pagu Nominal (Rp) *</label>
                <input type="number" step="0.01" name="amount" required placeholder="Contoh: 1500000"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-amber-500">
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-amber-600/30">Simpan Anggaran</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>