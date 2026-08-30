<x-layouts.app title="Laporan Keuangan" header="Laporan & Ringkasan Keuangan" subheader="Analisis komprehensif pemasukan, pengeluaran, alokasi tabungan, dan rasio tabungan">

    <!-- Filter Periode Laporan -->
    <x-card class="mb-6" x-data="{ currentType: '{{ $periodType }}' }">
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
            <div class="flex flex-wrap items-center gap-3 border-b border-slate-800/80 pb-3">
                <span class="text-xs font-bold text-slate-300">Tipe Periode:</span>
                <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
                    <input type="radio" name="period_type" value="monthly" x-model="currentType" class="text-indigo-600 focus:ring-indigo-500">
                    <span>Bulanan</span>
                </label>
                <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
                    <input type="radio" name="period_type" value="yearly" x-model="currentType" class="text-indigo-600 focus:ring-indigo-500">
                    <span>Tahunan</span>
                </label>
                <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
                    <input type="radio" name="period_type" value="custom" x-model="currentType" class="text-indigo-600 focus:ring-indigo-500">
                    <span>Custom Rentang Tanggal</span>
                </label>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <!-- Monthly Filter -->
                <div x-show="currentType === 'monthly'" class="flex items-center gap-2">
                    <select name="month" class="px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                        @for ($y = Carbon\Carbon::now()->year - 3; $y <= Carbon\Carbon::now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Yearly Filter -->
                <div x-show="currentType === 'yearly'" class="flex items-center gap-2">
                    <select name="year" class="px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                        @for ($y = Carbon\Carbon::now()->year - 3; $y <= Carbon\Carbon::now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Custom Range Filter -->
                <div x-show="currentType === 'custom'" class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                    <span class="text-xs text-slate-300">s/d</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30 transition cursor-pointer">
                    Tampilkan Laporan
                </button>
            </div>
        </form>
    </x-card>

    <!-- Report Header Title -->
    <div class="flex items-center justify-between">
        <div>
            <span class="text-xs text-slate-300 uppercase font-semibold">Hasil Laporan Periode</span>
            <h3 class="text-lg font-bold text-white">{{ $report['period_label'] }}</h3>
        </div>
        <div class="text-right text-xs text-slate-300">
            Total Transaksi: <strong class="text-white">{{ $report['transaction_count'] }}</strong> catatan
        </div>
    </div>

    <!-- 4 Metrik Utama Periode -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <x-stat-card title="Total Pemasukan" 
                     value="Rp {{ number_format($report['total_income'], 0, ',', '.') }}" 
                     subtitle="Arus kas masuk" 
                     color="emerald">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Total Pengeluaran" 
                     value="Rp {{ number_format($report['total_expense'], 0, ',', '.') }}" 
                     subtitle="Pengeluaran konsumsi riil" 
                     color="rose">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Surplus / Arus Kas Bersih" 
                     value="Rp {{ number_format($report['net_balance'], 0, ',', '.') }}" 
                     subtitle="{{ $report['net_balance'] >= 0 ? 'Surplus Finansial' : 'Defisit Finansial' }}" 
                     color="{{ $report['net_balance'] >= 0 ? 'cyan' : 'rose' }}">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Saving Rate (Tingkat Tabungan)" 
                     value="{{ $report['saving_rate'] }}%" 
                     subtitle="Alokasi: Rp {{ number_format($report['total_saving_deposit'], 0, ',', '.') }}" 
                     color="amber">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

    </div>

    <!-- Category Breakdown Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Pemasukan Berdasarkan Kategori -->
        <x-card title="Rincian Pemasukan per Kategori">
            @if (empty($report['income_by_category']))
                <p class="py-8 text-center text-slate-300 text-xs">Tidak ada data pemasukan pada periode ini.</p>
            @else
                <div class="space-y-3">
                    @foreach ($report['income_by_category'] as $cat => $amt)
                        @php $pct = $report['total_income'] > 0 ? round(($amt / $report['total_income']) * 100, 1) : 0; @endphp
                        <div class="space-y-1 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/60">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-white">{{ $cat }}</span>
                                <span class="font-mono text-emerald-400 font-bold">Rp {{ number_format($amt, 0, ',', '.') }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full bg-emerald-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <!-- Pengeluaran Berdasarkan Kategori -->
        <x-card title="Rincian Pengeluaran per Kategori">
            @if (empty($report['expense_by_category']))
                <p class="py-8 text-center text-slate-300 text-xs">Tidak ada data pengeluaran pada periode ini.</p>
            @else
                <div class="space-y-3">
                    @foreach ($report['expense_by_category'] as $cat => $amt)
                        @php $pct = $report['total_expense'] > 0 ? round(($amt / $report['total_expense']) * 100, 1) : 0; @endphp
                        <div class="space-y-1 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/60">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-white">{{ $cat }}</span>
                                <span class="font-mono text-rose-400 font-bold">Rp {{ number_format($amt, 0, ',', '.') }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full bg-rose-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

    </div>

</x-layouts.app>