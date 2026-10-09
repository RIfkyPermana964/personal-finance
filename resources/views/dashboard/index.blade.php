<x-layouts.app title="Dashboard" header="Dashboard Keuangan Pribadi" subheader="Ringkasan arus kas, pembagian gaji, tabungan, dan anggaran bulanan" x-data="{ createAllocModal: false, balanceModal: false }">

    <!-- Header Banner Periode Bulan / Tahun & Total In/Out -->
    <div class="space-y-5">
        
        <!-- Header Controls & Top Banner -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Periode Aktif</span>
                    <h3 class="text-lg font-bold text-slate-900">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</h3>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <select name="month" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                    @for ($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                    Lihat
                </button>
            </form>
        </div>

        <!-- 3 Kartu Banner Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            
            <!-- TOTAL SELURUH UANG MASUK -->
            <div class="p-5 rounded-2xl bg-white border border-emerald-100 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">
                    <span>Total Uang Masuk</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 font-mono mt-2">
                    Rp {{ number_format($metrics['income_month'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-500 mt-1">Total pendapatan & gaji bulan ini</p>
            </div>

            <!-- TOTAL SELURUH UANG KELUAR -->
            <div class="p-5 rounded-2xl bg-white border border-rose-100 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between text-xs font-bold text-rose-700 uppercase tracking-wider mb-1">
                    <span>Total Uang Keluar</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 font-mono mt-2">
                    Rp {{ number_format($metrics['expense_month'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-500 mt-1">Total pengeluaran harian & tagihan</p>
            </div>

            <!-- TOTAL TABUNGAN -->
            <div class="p-5 rounded-2xl bg-white border border-sky-100 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between text-xs font-bold text-sky-700 uppercase tracking-wider mb-1">
                    <span>Total Tabungan</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 font-mono mt-2">
                    Rp {{ number_format($metrics['total_savings'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-500 mt-1">Saldo aset tabungan & dana darurat</p>
            </div>

        </div>

    </div>

    <!-- SECTION: KESIMPULAN & PEMBAGIAN GAJI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KESIMPULAN KAS & SALDO (Kiri) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-4 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">KESIMPULAN SALDO</h3>
                    <a href="{{ route('salary-allocations.index', ['month' => $month, 'year' => $year]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Kelola &rarr;</a>
                </div>

                <div class="space-y-3 mt-4">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xs font-semibold text-slate-600 uppercase">CASH AWAL</span>
                        <span class="text-sm font-mono font-bold text-slate-900">Rp {{ number_format($balanceData['cash_initial'], 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xs font-semibold text-slate-600 uppercase">SALDO AWAL</span>
                        <span class="text-sm font-mono font-bold text-slate-900">Rp {{ number_format($balanceData['bank_initial'], 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50/70 border border-emerald-100">
                        <div>
                            <span class="text-xs font-bold text-emerald-800 uppercase">CASH AKHIR</span>
                            <span class="text-[10px] text-slate-500 block">(Estimasi tunai)</span>
                        </div>
                        <span class="text-base font-mono font-black text-emerald-700">Rp {{ number_format($balanceData['cash_final'], 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-sky-50/70 border border-sky-100">
                        <div>
                            <span class="text-xs font-bold text-sky-800 uppercase">SALDO AKHIR</span>
                            <span class="text-[10px] text-slate-500 block">(Estimasi rekening)</span>
                        </div>
                        <span class="text-base font-mono font-black text-sky-700">Rp {{ number_format($balanceData['bank_final'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100">
                <a href="{{ route('salary-allocations.index', ['month' => $month, 'year' => $year]) }}" class="w-full block py-2.5 px-3 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold transition text-center">
                    Buka Rincian Saldo & Alokasi
                </a>
            </div>
        </div>

        <!-- PEMBAGIAN GAJI DENGAN STATUS UNPAID / PAID (Kanan - 2 Kolom) -->
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 space-y-4 shadow-xs">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">PEMBAGIAN GAJI</h3>
                    <p class="text-xs text-slate-500">Pos pengeluaran terencana & status pembayaran</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-mono font-bold text-emerald-800">
                        Total Gaji: Rp {{ number_format($balanceData['total_salary'], 0, ',', '.') }}
                    </div>
                    <a href="{{ route('salary-allocations.index', ['month' => $month, 'year' => $year]) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">Kelola &rarr;</a>
                </div>
            </div>

            <!-- Tabel Pos Gaji & Status UNPAID / PAID -->
            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Pos Pengeluaran</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($allocationsData['items'] as $item)
                            <tr class="hover:bg-slate-50/70 transition {{ $item->isPaid() ? 'bg-emerald-50/30' : '' }}">
                                <td class="py-3 px-4 font-semibold text-slate-900">
                                    {{ $item->item_name }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form method="POST" action="{{ route('salary-allocations.toggle', $item->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Klik untuk mengubah status PAID/UNPAID" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider transition duration-150 cursor-pointer shadow-2xs {{ $item->isPaid() ? 'bg-emerald-100 text-emerald-800 border border-emerald-300 hover:bg-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-300 hover:bg-rose-200' }}">
                                            @if ($item->isPaid())
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                PAID
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                                UNPAID
                                            @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-slate-400">
                                    Belum ada pos pembagian gaji untuk bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Sub Summary -->
            <div class="flex items-center justify-between text-xs text-slate-600 pt-1 font-medium">
                <span>Terbayar: <strong class="text-emerald-700 font-mono font-bold">Rp {{ number_format($allocationsData['total_paid'], 0, ',', '.') }}</strong></span>
                <span>Belum Terbayar: <strong class="text-rose-700 font-mono font-bold">Rp {{ number_format($allocationsData['total_unpaid'], 0, ',', '.') }}</strong></span>
            </div>

        </div>

    </div>

    <!-- SECTION: MONITORING HUTANG, PIUTANG & SEWA (DEBT & RENT) -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Monitoring Hutang, Piutang & Sewa</h3>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Pantau orang yang meminjam ke kita, kewajiban pinjaman pribadi, dan tagihan sewa berkala</p>
            </div>
            <a href="{{ route('debts.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                <span>Kelola di Menu Pinjaman & Sewa</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- 1. Piutang (Dipinjam Orang) -->
            <a href="{{ route('debts.index', ['type' => 'receivable']) }}" 
               class="p-4 rounded-xl border border-emerald-100 bg-emerald-50/40 hover:bg-emerald-50 hover:border-emerald-200 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 bg-white px-2 py-0.5 rounded border border-emerald-200">
                        Piutang (Hak Kita)
                    </span>
                    <span class="text-xs text-emerald-600 font-semibold group-hover:translate-x-0.5 transition">&rarr;</span>
                </div>
                <div class="mt-3">
                    <p class="text-xs text-slate-500">Orang Pinjam ke Kita</p>
                    <p class="text-xl font-black font-mono text-emerald-700 mt-1">
                        Rp {{ number_format($debtsSummary['receivable_unpaid'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="mt-3 pt-2 border-t border-emerald-100/60 flex items-center justify-between text-[11px] text-slate-500">
                    <span>{{ $debtsSummary['receivable_count'] }} orang / data aktif</span>
                    <span class="text-emerald-700 font-semibold">Uang masuk dinanti</span>
                </div>
            </a>

            <!-- 2. Hutang (Pinjaman Kita) -->
            <a href="{{ route('debts.index', ['type' => 'debt']) }}" 
               class="p-4 rounded-xl border border-rose-100 bg-rose-50/40 hover:bg-rose-50 hover:border-rose-200 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700 bg-white px-2 py-0.5 rounded border border-rose-200">
                        Hutang (Kewajiban)
                    </span>
                    <span class="text-xs text-rose-600 group-hover:translate-x-0.5 transition">&rarr;</span>
                </div>
                <div class="mt-3">
                    <p class="text-xs text-slate-500">Pinjaman Saya Sendiri</p>
                    <p class="text-xl font-black font-mono text-rose-700 mt-1">
                        Rp {{ number_format($debtsSummary['debt_unpaid'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="mt-3 pt-2 border-t border-rose-100/60 flex items-center justify-between text-[11px] text-slate-500">
                    <span>{{ $debtsSummary['debt_count'] }} pinjaman aktif</span>
                    <span class="text-rose-700 font-semibold">Harus dilunasi</span>
                </div>
            </a>

            <!-- 3. Sewa (Rent) -->
            <a href="{{ route('debts.index', ['type' => 'rent']) }}" 
               class="p-4 rounded-xl border border-indigo-100 bg-indigo-50/40 hover:bg-indigo-50 hover:border-indigo-200 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 bg-white px-2 py-0.5 rounded border border-indigo-200">
                        Tagihan Sewa (Rent)
                    </span>
                    <span class="text-xs text-indigo-600 group-hover:translate-x-0.5 transition">&rarr;</span>
                </div>
                <div class="mt-3">
                    <p class="text-xs text-slate-500">Kos, Kontrakan, dll</p>
                    <p class="text-xl font-black font-mono text-indigo-700 mt-1">
                        Rp {{ number_format($debtsSummary['rent_unpaid'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="mt-3 pt-2 border-t border-indigo-100/60 flex items-center justify-between text-[11px] text-slate-500">
                    <span>{{ $debtsSummary['rent_count'] }} tagihan aktif</span>
                    <span class="text-indigo-700 font-semibold">Tagihan rutin</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Bar Chart: 6 Months Trend -->
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Tren Keuangan 6 Bulan Terakhir</h3>
                    <p class="text-xs text-slate-500">Perbandingan uang masuk, keluar, dan tabungan</p>
                </div>
            </div>
            <div class="h-72 w-full relative">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Expense by Category -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Pengeluaran per Kategori</h3>
                    <p class="text-xs text-slate-500">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
                </div>
            </div>
            <div class="h-64 w-full flex items-center justify-center relative">
                @if (empty($expenseCategories['data']))
                    <div class="text-center text-slate-400 text-xs">
                        Belum ada data pengeluaran bulan ini.
                    </div>
                @else
                    <canvas id="expenseCategoryChart"></canvas>
                @endif
            </div>
        </div>

    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 tracking-tight">Transaksi Terbaru</h3>
                <p class="text-xs text-slate-500">Daftar mutasi masuk dan keluar terkini</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Lihat Semua Riwayat &rarr;</a>
        </div>

        @if ($recentTransactions->isEmpty())
            <div class="py-8 text-center text-slate-400 text-xs">Belum ada riwayat transaksi.</div>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Metode Bayar</th>
                            <th class="py-3 px-4 text-center">Tipe</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 font-mono text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-4 font-medium text-slate-900">{{ $tx->description ?: '-' }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $tx->category?->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $tx->paymentMethod?->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $tx->type->badgeClass() }}">
                                        {{ $tx->type->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold {{ $tx->type->isPositive() ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->type->isPositive() ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Chart.js Scripts Initialization with Light Aesthetic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Chart Defaults for clean Light Theme
            Chart.defaults.color = '#64748B';
            Chart.defaults.borderColor = '#F1F5F9';
            Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";

            // 1. Monthly Trend Bar Chart
            const trendCtx = document.getElementById('monthlyTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($monthlyTrends['labels']) !!},
                        datasets: [
                            {
                                label: 'Pemasukan (In)',
                                data: {!! json_encode($monthlyTrends['income']) !!},
                                backgroundColor: '#10B981',
                                borderRadius: 6,
                                barPercentage: 0.7,
                            },
                            {
                                label: 'Pengeluaran (Out)',
                                data: {!! json_encode($monthlyTrends['expense']) !!},
                                backgroundColor: '#F43F5E',
                                borderRadius: 6,
                                barPercentage: 0.7,
                            },
                            {
                                label: 'Tabungan',
                                data: {!! json_encode($monthlyTrends['saving'] ?? []) !!},
                                backgroundColor: '#0EA5E9',
                                borderRadius: 6,
                                barPercentage: 0.7,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                    font: { size: 11, weight: '600' }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0F172A',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + window.formatRupiah(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#F1F5F9' },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000) + ' Jt';
                                    }
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Expense Category Doughnut Chart
            const categoryCtx = document.getElementById('expenseCategoryChart');
            if (categoryCtx) {
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($expenseCategories['labels']) !!},
                        datasets: [{
                            data: {!! json_encode($expenseCategories['data']) !!},
                            backgroundColor: {!! json_encode($expenseCategories['colors']) !!},
                            borderWidth: 2,
                            borderColor: '#FFFFFF',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    usePointStyle: true,
                                    font: { size: 10, weight: '600' }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0F172A',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.label + ': ' + window.formatRupiah(context.raw);
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
</x-layouts.app>