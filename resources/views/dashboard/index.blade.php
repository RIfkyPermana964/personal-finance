<x-layouts.app title="Dashboard" header="Dashboard Keuangan Pribadi" subheader="Ringkasan arus kas, pembagian gaji, tabungan, dan anggaran bulanan" x-data="{ createAllocModal: false, balanceModal: false }">

    <!-- Header Banner Periode Bulan / Tahun & Total In/Out (Sesuai Spreadsheet) -->
    <div class="space-y-4">
        
        <!-- Header Controls & Top Banner -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <span class="text-xs text-slate-300 font-semibold uppercase">Periode Aktif</span>
                    <h3 class="text-lg font-black text-white">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</h3>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <select name="month" class="px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                    @for ($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition cursor-pointer">
                    Lihat
                </button>
            </form>
        </div>

        <!-- 3 Kartu Banner Utama (Format Spreadsheet) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            
            <!-- TOTAL SELURUH UANG MASUK -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-[#111827] border border-emerald-500/30 shadow-lg glow-emerald">
                <div class="flex items-center justify-between text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1">
                    <span>TOTAL SELURUH UANG MASUK</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-white font-mono mt-2">
                    Rp {{ number_format($metrics['income_month'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-300 mt-1">Total pendapatan & gaji bulan ini</p>
            </div>

            <!-- TOTAL SELURUH UANG KELUAR -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-[#111827] border border-rose-500/30 shadow-lg glow-rose">
                <div class="flex items-center justify-between text-xs font-bold text-rose-400 uppercase tracking-wider mb-1">
                    <span>TOTAL SELURUH UANG KELUAR</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-white font-mono mt-2">
                    Rp {{ number_format($metrics['expense_month'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-300 mt-1">Total pengeluaran harian & tagihan</p>
            </div>

            <!-- TOTAL TABUNGAN -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-[#111827] border border-cyan-500/30 shadow-lg">
                <div class="flex items-center justify-between text-xs font-bold text-cyan-400 uppercase tracking-wider mb-1">
                    <span>TOTAL TABUNGAN TERKUMPUL</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-white font-mono mt-2">
                    Rp {{ number_format($metrics['total_savings'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-300 mt-1">Saldo aset tabungan & dana darurat</p>
            </div>

        </div>

    </div>

    <!-- SECTION KHUSUS: KESIMPULAN & PEMBAGIAN GAJI (Format Spreadsheet Anda) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KESIMPULAN KAS & SALDO (Kiri) -->
        <div class="bg-[#111827]/90 border border-slate-800/80 rounded-2xl p-5 space-y-4 backdrop-blur-md shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white tracking-wide">KESIMPULAN SALDO</h3>
                    <a href="{{ route('salary-allocations.index', ['month' => $month, 'year' => $year]) }}" class="text-xs text-indigo-400 hover:underline">Kelola &rarr;</a>
                </div>

                <div class="space-y-3 mt-4">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/60">
                        <span class="text-xs font-semibold text-slate-300 uppercase">CASH AWAL</span>
                        <span class="text-sm font-mono font-bold text-white">Rp {{ number_format($balanceData['cash_initial'], 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/60">
                        <span class="text-xs font-semibold text-slate-300 uppercase">SALDO AWAL</span>
                        <span class="text-sm font-mono font-bold text-white">Rp {{ number_format($balanceData['bank_initial'], 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase">CASH AKHIR</span>
                            <span class="text-[10px] text-slate-300 block">(Estimasi tunai)</span>
                        </div>
                        <span class="text-base font-mono font-black text-emerald-400">Rp {{ number_format($balanceData['cash_final'], 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-cyan-500/10 border border-cyan-500/20">
                        <div>
                            <span class="text-xs font-bold text-cyan-400 uppercase">SALDO AKHIR</span>
                            <span class="text-[10px] text-slate-300 block">(Estimasi rekening)</span>
                        </div>
                        <span class="text-base font-mono font-black text-cyan-400">Rp {{ number_format($balanceData['bank_final'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800/80">
                <a href="{{ route('salary-allocations.index', ['month' => $month, 'year' => $year]) }}" class="w-full block py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition text-center">
                    Buka Rincian Saldo & Alokasi
                </a>
            </div>
        </div>

        <!-- PEMBAGIAN GAJI DENGAN STATUS UNPAID / PAID (Kanan - 2 Kolom) -->
        <div class="lg:col-span-2 bg-[#111827]/90 border border-slate-800/80 rounded-2xl p-5 space-y-4 backdrop-blur-md shadow-xl">
            
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-white tracking-wide">PEMBAGIAN GAJI</h3>
                        <p class="text-xs text-slate-300">Pos pengeluaran terencana & status pembayaran</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="px-3 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs font-mono font-bold text-emerald-400">
                        Total Gaji: Rp {{ number_format($balanceData['total_salary'], 0, ',', '.') }}
                    </div>
                    <a href="{{ route('salary-allocations.index', ['month' => $month, 'year' => $year]) }}" class="text-xs text-emerald-400 hover:underline">Kelola &rarr;</a>
                </div>
            </div>

            <!-- Tabel Pos Gaji & Status UNPAID / PAID -->
            <div class="overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900 text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Pos Pengeluaran</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($allocationsData['items'] as $item)
                            <tr class="hover:bg-slate-800/30 transition {{ $item->isPaid() ? 'bg-emerald-500/5' : '' }}">
                                <td class="py-3 px-4 font-semibold text-white">
                                    {{ $item->item_name }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-white">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form method="POST" action="{{ route('salary-allocations.toggle', $item->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Klik untuk mengubah status PAID/UNPAID" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider transition duration-150 cursor-pointer shadow-sm {{ $item->isPaid() ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 hover:bg-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' }}">
                                            @if ($item->isPaid())
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                PAID
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                                                UNPAID
                                            @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-slate-300">
                                    Belum ada pos pembagian gaji untuk bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Sub Summary -->
            <div class="flex items-center justify-between text-xs text-slate-300 pt-1 font-medium">
                <span>Terbayar: <strong class="text-emerald-400 font-mono">Rp {{ number_format($allocationsData['total_paid'], 0, ',', '.') }}</strong></span>
                <span>Belum Terbayar: <strong class="text-rose-400 font-mono">Rp {{ number_format($allocationsData['total_unpaid'], 0, ',', '.') }}</strong></span>
            </div>

        </div>

    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Bar Chart: 6 Months Trend -->
        <div class="lg:col-span-2 bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-5 sm:p-6 backdrop-blur-md shadow-xl">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800/80">
                <div>
                    <h3 class="text-sm font-bold text-white tracking-wide">Tren Keuangan 6 Bulan Terakhir</h3>
                    <p class="text-xs text-slate-300">Perbandingan uang masuk, keluar, dan tabungan</p>
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
                        Belum ada data pengeluaran bulan ini.
                    </div>
                @else
                    <canvas id="expenseCategoryChart"></canvas>
                @endif
            </div>
        </div>

    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-5 sm:p-6 backdrop-blur-md shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <div>
                <h3 class="text-sm font-bold text-white">Transaksi Terbaru</h3>
                <p class="text-xs text-slate-300">Daftar mutasi masuk dan keluar terkini</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-400 hover:underline">Lihat Semua Riwayat &rarr;</a>
        </div>

        @if ($recentTransactions->isEmpty())
            <div class="py-8 text-center text-slate-300 text-xs">Belum ada riwayat transaksi.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/60 text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4">Kategori / Deskripsi</th>
                            <th class="py-3 px-4">Metode Bayar</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach ($recentTransactions as $tx)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 px-4 font-mono text-slate-300">{{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $tx->type->value === 'income' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($tx->type->value === 'expense' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20') }}">
                                        {{ $tx->type->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-white font-medium">
                                    {{ $tx->description ?: ($tx->category?->name ?: '-') }}
                                </td>
                                <td class="py-3 px-4 text-slate-300">{{ $tx->paymentMethod?->name ?? 'Kas / Tunai' }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-sm {{ $tx->type->value === 'income' ? 'text-emerald-400' : ($tx->type->value === 'expense' ? 'text-rose-400' : 'text-amber-400') }}">
                                    {{ $tx->type->value === 'income' ? '+' : ($tx->type->value === 'expense' ? '-' : '•') }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
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