<x-layouts.app title="Dashboard" header="Financial Dashboard" subheader="Ringkasan arus kas, tabungan, dan anggaran keuangan pribadi">

    <!-- Filter Periode Bulan / Tahun -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-2xl bg-[#111827]/70 border border-slate-800/80 backdrop-blur-md">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-300">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Periode Data: <span class="text-white">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</span></span>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            <select name="month" class="px-3 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" class="px-3 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                @for ($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                Terapkan
            </button>
        </form>
    </div>

    <!-- 6 Executive Financial Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        
        <!-- Total Pemasukan -->
        <x-stat-card title="Total Pemasukan Bulan Ini" 
                     value="Rp {{ number_format($metrics['income_month'], 0, ',', '.') }}" 
                     subtitle="Pemasukan operasional & sampingan" 
                     color="emerald">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Total Pengeluaran -->
        <x-stat-card title="Total Pengeluaran Bulan Ini" 
                     value="Rp {{ number_format($metrics['expense_month'], 0, ',', '.') }}" 
                     subtitle="Pengeluaran konsumtif & tagihan" 
                     color="rose">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Surplus Bulan Ini -->
        <x-stat-card title="Surplus / Arus Kas Bersih" 
                     value="Rp {{ number_format($metrics['net_cashflow_month'], 0, ',', '.') }}" 
                     subtitle="{{ $metrics['net_cashflow_month'] >= 0 ? 'Surplus bulan ini (Income - Expense)' : 'Defisit bulan ini' }}" 
                     color="{{ $metrics['net_cashflow_month'] >= 0 ? 'cyan' : 'rose' }}">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Kas Operasional Tersedia -->
        <x-stat-card title="Kas Operasional Tersedia (Free Cash)" 
                     value="Rp {{ number_format($metrics['free_cash_balance'], 0, ',', '.') }}" 
                     subtitle="Saldo kas likuid di dompet & rekening" 
                     color="indigo">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Total Tabungan Terkumpul -->
        <x-stat-card title="Total Tabungan Terkumpul" 
                     value="Rp {{ number_format($metrics['total_savings'], 0, ',', '.') }}" 
                     subtitle="Alokasi tabungan: {{ $metrics['saving_rate_month'] }}% dari gaji bulan ini" 
                     color="amber">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Target & Progress Tabungan Global -->
        <x-stat-card title="Target Tabungan Global" 
                     value="{{ $metrics['saving_progress_pct'] }}%" 
                     subtitle="Terkumpul dari Rp {{ number_format($metrics['total_target_savings'], 0, ',', '.') }}" 
                     color="cyan">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </x-slot:icon>
            <div class="w-full bg-slate-800 rounded-full h-2 mt-3 overflow-hidden border border-slate-700">
                <div class="bg-gradient-to-r from-cyan-500 to-emerald-400 h-2 rounded-full transition-all duration-500" style="width: {{ $metrics['saving_progress_pct'] }}%"></div>
            </div>
        </x-stat-card>

    </div>

    <!-- Charts Section (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Bar Chart: 6 Months Trend -->
        <div class="lg:col-span-2 bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-5 sm:p-6 backdrop-blur-md shadow-xl">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800/80">
                <div>
                    <h3 class="text-sm font-bold text-white tracking-wide">Tren Keuangan 6 Bulan Terakhir</h3>
                    <p class="text-xs text-slate-300">Perbandingan pemasukan, pengeluaran, dan tabungan</p>
                </div>
            </div>
            <div class="h-72 w-full relative">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Expense by Category -->
        <div class="bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-5 sm:p-6 backdrop-blur-md shadow-xl">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800/80">
                <div>
                    <h3 class="text-sm font-bold text-white tracking-wide">Pengeluaran per Kategori</h3>
                    <p class="text-xs text-slate-300">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
                </div>
            </div>
            <div class="h-64 w-full flex items-center justify-center relative">
                @if (empty($expenseCategories['data']))
                    <div class="text-center text-slate-300 text-xs">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        Belum ada data pengeluaran bulan ini.
                    </div>
                @else
                    <canvas id="expenseCategoryChart"></canvas>
                @endif
            </div>
        </div>

    </div>

    <!-- Widgets: Budget Progress & Saving Goals & Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Budget Health Widget -->
        <div class="bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-5 sm:p-6 backdrop-blur-md shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <h3 class="text-sm font-bold text-white">Status Pagu Anggaran (Budget)</h3>
                <a href="{{ route('budgets.index', ['month' => $month, 'year' => $year]) }}" class="text-xs text-emerald-400 hover:underline">Kelola &rarr;</a>
            </div>

            @if (empty($budgetSummary['items']))
                <div class="py-8 text-center text-slate-300 text-xs">
                    Belum ada anggaran yang diset untuk bulan ini.
                    <div class="mt-3">
                        <a href="{{ route('budgets.index') }}" class="inline-flex px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold transition">Buat Budget Baru</a>
                    </div>
                </div>
            @else
                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                    @foreach ($budgetSummary['items'] as $b)
                        <div class="space-y-1.5 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/60">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-white">{{ $b['category_name'] }}</span>
                                <span class="font-mono text-slate-400">Rp {{ number_format($b['spent'], 0, ',', '.') }} / {{ number_format($b['amount'], 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-300 {{ $b['percentage'] > 100 ? 'bg-rose-500' : ($b['percentage'] > 75 ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ min(100, $b['percentage']) }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="{{ $b['is_over_budget'] ? 'text-rose-400 font-bold' : 'text-slate-300' }}">
                                    {{ $b['is_over_budget'] ? '⚠️ Over Budget (' . $b['percentage'] . '%)' : 'Terpakai: ' . $b['percentage'] . '%' }}
                                </span>
                                <span class="text-slate-300 font-medium">Sisa: Rp {{ number_format($b['remaining'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Saving Goals Widget -->
        <div class="bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-5 sm:p-6 backdrop-blur-md shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <h3 class="text-sm font-bold text-white">Target Tabungan Aktif</h3>
                <a href="{{ route('saving-goals.index') }}" class="text-xs text-cyan-400 hover:underline">Semua Target &rarr;</a>
            </div>

            @if ($savingGoals->isEmpty())
                <div class="py-8 text-center text-slate-300 text-xs">
                    Belum ada target tabungan yang dibuat.
                    <div class="mt-3">
                        <a href="{{ route('saving-goals.index') }}" class="inline-flex px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold transition">Buat Target Tabungan</a>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($savingGoals as $goal)
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-white">{{ $goal->name }}</span>
                                <span class="text-xs font-bold text-cyan-400">{{ $goal->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full bg-gradient-to-r from-cyan-500 to-emerald-400" style="width: {{ $goal->progress_percentage }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-300 font-mono">
                                <span>Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                                <span>Target: Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Transactions Table -->
        <div class="bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-5 sm:p-6 backdrop-blur-md shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <h3 class="text-sm font-bold text-white">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-400 hover:underline">Lihat Semua &rarr;</a>
            </div>

            @if ($recentTransactions->isEmpty())
                <div class="py-8 text-center text-slate-300 text-xs">Belum ada riwayat transaksi.</div>
            @else
                <div class="space-y-2.5">
                    @foreach ($recentTransactions as $tx)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-900/40 border border-slate-800/60 hover:bg-slate-800/50 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ $tx->type->value === 'income' ? 'bg-emerald-500/10 text-emerald-400' : ($tx->type->value === 'expense' ? 'bg-rose-500/10 text-rose-400' : 'bg-amber-500/10 text-amber-400') }}">
                                    @if ($tx->type->value === 'income')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                    @elseif ($tx->type->value === 'expense')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-white truncate">{{ $tx->description ?: ($tx->category?->name ?: $tx->type->label()) }}</p>
                                    <p class="text-[10px] text-slate-300">{{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M Y') }} • {{ $tx->paymentMethod?->name ?? 'Kas' }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs font-mono font-bold {{ $tx->type->value === 'income' ? 'text-emerald-400' : ($tx->type->value === 'expense' ? 'text-rose-400' : 'text-amber-400') }}">
                                    {{ $tx->type->value === 'income' ? '+' : ($tx->type->value === 'expense' ? '-' : '•') }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Chart.js Scripts Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Monthly Trend Bar Chart
            const ctxTrend = document.getElementById('monthlyTrendChart');
            if (ctxTrend) {
                new Chart(ctxTrend, {
                    type: 'bar',
                    data: {
                        labels: @json($monthlyTrends['labels']),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: @json($monthlyTrends['income']),
                                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                borderRadius: 6,
                            },
                            {
                                label: 'Pengeluaran',
                                data: @json($monthlyTrends['expense']),
                                backgroundColor: 'rgba(244, 63, 94, 0.85)',
                                borderRadius: 6,
                            },
                            {
                                label: 'Tabungan',
                                data: @json($monthlyTrends['saving']),
                                backgroundColor: 'rgba(245, 158, 11, 0.85)',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#94a3b8', font: { size: 11, family: 'Plus Jakarta Sans' } }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#94a3b8', font: { size: 10 } }
                            },
                            y: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: {
                                    color: '#94a3b8',
                                    font: { size: 10 },
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Expense Category Doughnut Chart
            const ctxCat = document.getElementById('expenseCategoryChart');
            if (ctxCat) {
                new Chart(ctxCat, {
                    type: 'doughnut',
                    data: {
                        labels: @json($expenseCategories['labels']),
                        datasets: [{
                            data: @json($expenseCategories['data']),
                            backgroundColor: @json($expenseCategories['colors']),
                            borderWidth: 2,
                            borderColor: '#111827',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#94a3b8', boxWidth: 12, font: { size: 10, family: 'Plus Jakarta Sans' } }
                            }
                        },
                        cutout: '68%'
                    }
                });
            }
        });
    </script>
</x-layouts.app>