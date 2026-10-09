<x-layouts.app title="Transaksi Finansial" header="Pencatatan Transaksi Terpadu" subheader="Kelola pemasukan dan pengeluaran dalam satu halaman efisien tanpa harus berpindah tab">

<div x-data="{
    createModal: false,
    editModal: false,
    createType: '{{ ($filters['type'] ?? '') === 'income' ? 'income' : 'expense' }}',
    editData: {
        id: null,
        type: 'expense',
        amount: '',
        expense_category_id: '',
        income_category_id: '',
        payment_method_id: '',
        transaction_date: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}',
        description: ''
    },
    openEdit(id, type, amount, category_id, payment_method_id, transaction_date, description) {
        this.editData = {
            id: id,
            type: type || 'expense',
            amount: window.formatRupiahInput ? window.formatRupiahInput(amount) : String(amount),
            expense_category_id: (type === 'expense' && category_id) ? String(category_id) : '',
            income_category_id: (type === 'income' && category_id) ? String(category_id) : '',
            payment_method_id: String(payment_method_id || ''),
            transaction_date: transaction_date,
            description: description || ''
        };
        this.editModal = true;
    }
}" class="space-y-6">

    <!-- Top Action & Quick Info Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-sm font-bold text-slate-900">Buku Kas &amp; Riwayat Transaksi</h3>
            <p class="text-xs text-slate-500">Pencatatan arus kas masuk (pemasukan) dan keluar (pengeluaran) harian Anda</p>
        </div>
        <button @click="createModal = true" 
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>+ Catat Transaksi</span>
        </button>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pemasukan -->
        <div class="p-5 rounded-2xl bg-white border border-emerald-100 shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Pemasukan (In)</p>
                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-black font-mono text-emerald-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400">Kas masuk tercatat</p>
        </div>

        <!-- Total Pengeluaran -->
        <div class="p-5 rounded-2xl bg-white border border-rose-100 shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Pengeluaran (Out)</p>
                <span class="p-1.5 rounded-lg bg-rose-50 text-rose-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-black font-mono text-rose-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400">Biaya & belanja tercatat</p>
        </div>

        <!-- Arus Kas Bersih (Net Cashflow) -->
        <div class="p-5 rounded-2xl bg-white border {{ $netCashflow >= 0 ? 'border-indigo-100' : 'border-rose-200' }} shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Arus Kas Bersih (Net)</p>
                <span class="p-1.5 rounded-lg {{ $netCashflow >= 0 ? 'bg-indigo-50 text-indigo-600' : 'bg-rose-50 text-rose-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-black font-mono {{ $netCashflow >= 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                {{ $netCashflow >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netCashflow), 0, ',', '.') }}
            </p>
            <p class="text-[11px] {{ $netCashflow >= 0 ? 'text-emerald-600 font-medium' : 'text-rose-600 font-medium' }}">
                {{ $netCashflow >= 0 ? 'Surplus (Kas Positif)' : 'Defisit Pengeluaran' }}
            </p>
        </div>

        <!-- Total Entri Transaksi -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Catatan</p>
                <span class="p-1.5 rounded-lg bg-slate-100 text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-black font-mono text-slate-900">{{ $transactions->total() }}</p>
            <p class="text-[11px] text-slate-400">Transaksi sesuai filter</p>
        </div>
    </div>

    <!-- Filter Card -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Search -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pencarian Item / Keterangan</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari transaksi..."
                           class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <!-- Tipe Transaksi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Transaksi</label>
                    <select name="type" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                        <option value="">-- Semua Jenis (In & Out) --</option>
                        <option value="expense" {{ ($filters['type'] ?? '') === 'expense' ? 'selected' : '' }}>Pengeluaran (Out)</option>
                        <option value="income" {{ ($filters['type'] ?? '') === 'income' ? 'selected' : '' }}>Pemasukan (In)</option>
                        <option value="saving_deposit" {{ ($filters['type'] ?? '') === 'saving_deposit' ? 'selected' : '' }}>Alokasi Tabungan</option>
                        <option value="saving_withdraw" {{ ($filters['type'] ?? '') === 'saving_withdraw' ? 'selected' : '' }}>Penarikan Tabungan</option>
                    </select>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori</label>
                    <select name="category_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                                [{{ $cat->type->label() }}] {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Bayar</label>
                    <select name="payment_method_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                        <option value="">-- Semua Metode --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" {{ ($filters['payment_method_id'] ?? '') == $pm->id ? 'selected' : '' }}>
                                {{ $pm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Row 2: Bulan/Tahun, Rentang Tanggal, & Aksi Filter -->
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 pt-3 border-t border-slate-100">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Bulan Filter -->
                    <select name="month" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                        <option value="">-- Semua Bulan --</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ ($filters['month'] ?? '') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>

                    <!-- Tahun Filter -->
                    <select name="year" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                        <option value="">-- Semua Tahun --</option>
                        @for ($y = \Carbon\Carbon::now()->year - 2; $y <= \Carbon\Carbon::now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ ($filters['year'] ?? '') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>

                    <span class="text-xs text-slate-400 font-medium px-1">atau Rentang:</span>

                    <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                    <span class="text-xs text-slate-400 font-medium">s/d</span>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="flex items-center gap-2 justify-end">
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition text-center">
                        Reset Filter
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition cursor-pointer">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Spreadsheet-Style Master Transactions Table -->
    <x-card title="Buku Kas & Transaksi Terpadu" subtitle="Tabel pencatatan transaksi masuk dan keluar (Total: {{ $transactions->total() }} catatan)">
        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-3">Tanggal</th>
                        <th class="py-3.5 px-3">Keterangan / Item</th>
                        <th class="py-3.5 px-3 text-center">Jenis</th>
                        <th class="py-3.5 px-3">Kategori</th>
                        <th class="py-3.5 px-3">Metode</th>
                        <th class="py-3.5 px-3 text-right text-rose-700">Total Out (Keluar)</th>
                        <th class="py-3.5 px-3 text-right text-emerald-700">Total In (Masuk)</th>
                        <th class="py-3.5 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Tanggal -->
                            <td class="py-3 px-3 font-mono text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d/m/Y') }}
                            </td>
                            
                            <!-- Keterangan -->
                            <td class="py-3 px-3 font-semibold text-slate-900">
                                {{ $tx->description ?: '-' }}
                            </td>

                            <!-- Jenis Badge -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $tx->type->badgeClass() }}">
                                    {{ $tx->type->label() }}
                                </span>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3 px-3 text-slate-600">
                                {{ $tx->category?->name ?? '-' }}
                            </td>

                            <!-- Metode Bayar -->
                            <td class="py-3 px-3 text-slate-600">
                                {{ $tx->paymentMethod?->name ?? '-' }}
                            </td>

                            <!-- Total Out -->
                            <td class="py-3 px-3 text-right font-mono font-bold text-rose-600 text-sm whitespace-nowrap">
                                @if (in_array($tx->type->value, ['expense', 'saving_deposit']))
                                    - Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Total In -->
                            <td class="py-3 px-3 text-right font-mono font-bold text-emerald-600 text-sm whitespace-nowrap">
                                @if (in_array($tx->type->value, ['income', 'saving_withdraw']))
                                    + Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="openEdit({{ $tx->id }}, '{{ $tx->type->value }}', {{ $tx->amount }}, {{ $tx->category_id ?? 'null' }}, {{ $tx->payment_method_id ?? 'null' }}, '{{ \Carbon\Carbon::parse($tx->transaction_date)->format('Y-m-d') }}', '{{ addslashes($tx->description ?? '') }}')"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button @click="$dispatch('open-delete', { action: '{{ route('transactions.destroy', $tx->id) }}', message: 'Hapus transaksi {{ $tx->type->label() }} Rp {{ number_format($tx->amount, 0, ',', '.') }} ({{ addslashes($tx->description ?? '') }})?' })"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                Tidak ada transaksi yang sesuai dengan filter pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </x-card>

    <!-- Modal: Catat Transaksi Baru (Unified Dropdown Input) -->
    <x-modal name="createModal" title="Catat Transaksi Finansial" maxWidth="md">
        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
            @csrf

            <!-- 1. Dropdown Jenis Transaksi (Pengeluaran vs Pemasukan) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Transaksi *</label>
                <div class="relative">
                    <select name="type" x-model="createType" required
                            class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-xs font-bold transition cursor-pointer focus:outline-none focus:ring-2"
                            :class="createType === 'income' ? 'text-emerald-700 border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500/20' : 'text-rose-700 border-rose-300 focus:border-rose-500 focus:ring-rose-500/20'">
                        <option value="expense">Pengeluaran (Uang Keluar)</option>
                        <option value="income">Pemasukan (Uang Masuk)</option>
                    </select>
                </div>
                <p class="mt-1 text-[11px]" :class="createType === 'income' ? 'text-emerald-600 font-medium' : 'text-rose-600 font-medium'">
                    <span x-show="createType === 'income'">Catat sumber pendapatan / kas masuk Anda.</span>
                    <span x-show="createType === 'expense'">Catat belanja, biaya operasional, atau tagihan keluar Anda.</span>
                </p>
            </div>

            <!-- 2. Nominal Transaksi (dengan ribuan otomatis via x-money) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal (Rp) *</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 font-bold text-sm"
                         :class="createType === 'income' ? 'text-emerald-500' : 'text-rose-500'">Rp</div>
                    <input type="text" inputmode="numeric" name="amount" x-money required
                           :placeholder="createType === 'income' ? 'Contoh: 5.000.000' : 'Contoh: 50.000'"
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:ring-2"
                           :class="createType === 'income' ? 'focus:border-emerald-500 focus:ring-emerald-500/20' : 'focus:border-rose-500 focus:ring-rose-500/20'">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Titik pemisah ribuan otomatis ditambahkan saat mengetik</p>
            </div>

            <!-- 3. Kategori (Dinamis sesuai Jenis Transaksi) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori *</label>
                    
                    <!-- Kategori Pengeluaran -->
                    <div x-show="createType === 'expense'">
                        <select name="category_id" :disabled="createType !== 'expense'" required
                                class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-rose-500">
                            <option value="">-- Pilih Kategori Pengeluaran --</option>
                            @foreach ($expenseCategories as $parent)
                                <optgroup label="{{ $parent->name }}">
                                    <option value="{{ $parent->id }}">{{ $parent->name }} (Utama)</option>
                                    @foreach ($parent->children as $child)
                                        <option value="{{ $child->id }}">&nbsp;&nbsp;↳ {{ $child->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kategori Pemasukan -->
                    <div x-show="createType === 'income'" x-cloak>
                        <select name="category_id" :disabled="createType !== 'income'" required
                                class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-500">
                            <option value="">-- Pilih Kategori Pemasukan --</option>
                            @foreach ($incomeCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 4. Metode Pembayaran -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Bayar / Kas *</label>
                    <select name="payment_method_id" required 
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Rekening / Dompet --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 5. Tanggal Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Transaksi *</label>
                <input type="date" name="transaction_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <!-- 6. Keterangan / Deskripsi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan / Catatan (Opsional)</label>
                <input type="text" name="description" placeholder="Contoh: Belanja mingguan, Gaji bulanan, Kopi & makan siang"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="createModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5"
                        :class="createType === 'income' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'">
                    <span x-text="createType === 'income' ? 'Simpan Pemasukan' : 'Simpan Pengeluaran'"></span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal: Edit Transaksi (Unified Dropdown Input) -->
    <x-modal name="editModal" title="Edit Transaksi Finansial" maxWidth="md">
        <form :action="'/transactions/' + editData.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- 1. Dropdown Jenis Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Transaksi *</label>
                <select name="type" x-model="editData.type" required
                        class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-xs font-bold transition cursor-pointer focus:outline-none focus:ring-2"
                        :class="editData.type === 'income' ? 'text-emerald-700 border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500/20' : 'text-rose-700 border-rose-300 focus:border-rose-500 focus:ring-rose-500/20'">
                    <option value="expense">Pengeluaran (Uang Keluar)</option>
                    <option value="income">Pemasukan (Uang Masuk)</option>
                </select>
            </div>

            <!-- 2. Nominal Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal (Rp) *</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 font-bold text-sm"
                         :class="editData.type === 'income' ? 'text-emerald-500' : 'text-rose-500'">Rp</div>
                    <input type="text" inputmode="numeric" name="amount" x-model="editData.amount" x-money required
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:ring-2"
                           :class="editData.type === 'income' ? 'focus:border-emerald-500 focus:ring-emerald-500/20' : 'focus:border-rose-500 focus:ring-rose-500/20'">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Titik pemisah ribuan otomatis ditambahkan saat mengetik</p>
            </div>

            <!-- 3. Kategori Dinamis -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori *</label>
                    
                    <!-- Kategori Pengeluaran saat Edit -->
                    <div x-show="editData.type === 'expense'">
                        <select name="category_id" x-model="editData.expense_category_id" :disabled="editData.type !== 'expense'" required
                                class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-rose-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($expenseCategories as $parent)
                                <optgroup label="{{ $parent->name }}">
                                    <option value="{{ $parent->id }}">{{ $parent->name }} (Utama)</option>
                                    @foreach ($parent->children as $child)
                                        <option value="{{ $child->id }}">&nbsp;&nbsp;↳ {{ $child->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kategori Pemasukan saat Edit -->
                    <div x-show="editData.type === 'income'" x-cloak>
                        <select name="category_id" x-model="editData.income_category_id" :disabled="editData.type !== 'income'" required
                                class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($incomeCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 4. Metode Bayar -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Bayar *</label>
                    <select name="payment_method_id" x-model="editData.payment_method_id" required
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Rekening / Dompet --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 5. Tanggal -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Transaksi *</label>
                <input type="date" name="transaction_date" x-model="editData.transaction_date" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <!-- 6. Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan / Deskripsi</label>
                <input type="text" name="description" x-model="editData.description"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5"
                        :class="editData.type === 'income' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'">
                    <span x-text="editData.type === 'income' ? 'Perbarui Pemasukan' : 'Perbarui Pengeluaran'"></span>
                </button>
            </div>
        </form>
    </x-modal>

</div>
</x-layouts.app>