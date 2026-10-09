<x-layouts.app title="Hutang & Piutang" 
               header="Hutang, Piutang & Sewa" 
               subheader="Monitoring orang yang meminjam ke kita (piutang), pinjaman pribadi (hutang), dan tagihan sewa berkala">

<div x-data="{
    createModal: false,
    editModal: false,
    payModal: false,
    historyModal: false,
    
    // Data untuk create
    createType: '{{ $selectedType !== 'all' ? $selectedType : 'receivable' }}',
    
    // Data untuk edit
    editData: {
        id: null,
        type: 'debt',
        name: '',
        total_amount: '',
        start_date: '',
        due_date: '',
        rent_type: '',
        notes: ''
    },

    // Data untuk catat pembayaran
    payData: {
        debt_id: null,
        debt_name: '',
        debt_type: '',
        total_amount: 0,
        paid_amount: 0,
        remaining_amount: 0,
        amount: '',
        payment_date: '{{ now()->toDateString() }}',
        payment_method_id: '',
        record_as_transaction: true,
        notes: ''
    },

    // Data untuk riwayat pembayaran
    historyData: {
        debt_id: null,
        debt_name: '',
        debt_type: '',
        total_amount: 0,
        remaining_amount: 0,
        payments: []
    },

    setFullPayment() {
        this.payData.amount = formatRupiahInput(this.payData.remaining_amount);
    }
}" class="space-y-6">

    <!-- KPI Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Piutang (Orang pinjam ke kita) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                        Piutang (Hak Kita)
                    </span>
                    <h3 class="text-xs font-semibold text-slate-500 mt-2">Dipinjam Orang Lain</h3>
                    <p class="text-xl font-bold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($totalReceivableUnpaid, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500 border-t border-slate-100 pt-2.5">
                <span>Total Aktif: <strong>{{ $counts['receivable'] }}</strong> data</span>
                <span class="text-emerald-600 font-semibold">Uang masuk dinanti</span>
            </div>
        </div>

        <!-- Card 2: Hutang Saya (Pinjaman kita) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">
                        Hutang (Kewajiban)
                    </span>
                    <h3 class="text-xs font-semibold text-slate-500 mt-2">Pinjaman Saya</h3>
                    <p class="text-xl font-bold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($totalDebtUnpaid, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500 border-t border-slate-100 pt-2.5">
                <span>Total Aktif: <strong>{{ $counts['debt'] }}</strong> data</span>
                <span class="text-rose-600 font-semibold">Harus dibayar</span>
            </div>
        </div>

        <!-- Card 3: Sewa & Tagihan Rutin (Rent) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                        Sewa / Rutin (Rent)
                    </span>
                    <h3 class="text-xs font-semibold text-slate-500 mt-2">Tagihan Kos/Sewa</h3>
                    <p class="text-xl font-bold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($totalRentUnpaid, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500 border-t border-slate-100 pt-2.5">
                <span>Total Aktif: <strong>{{ $counts['rent'] }}</strong> data</span>
                <span class="text-indigo-600 font-semibold">Tagihan rutin</span>
            </div>
        </div>

        <!-- Card 4: Total Sudah Dicicil / Terbayar -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-sky-600 bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100">
                        Akumulasi Bayar
                    </span>
                    <h3 class="text-xs font-semibold text-slate-500 mt-2">Total Telah Dicicil</h3>
                    <p class="text-xl font-bold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($totalPaidOverall, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500 border-t border-slate-100 pt-2.5">
                <span>{{ $counts['paid'] }} Lunas</span>
                <span>{{ $counts['nyicil'] }} Sedang Nyicil</span>
            </div>
        </div>
    </div>

    <!-- Header Action Bar & Filter Tabs -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-2xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Type Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 p-1 bg-slate-100 rounded-xl text-xs font-semibold">
                <a href="{{ route('debts.index', array_merge(request()->query(), ['type' => 'all'])) }}"
                   class="px-3.5 py-2 rounded-lg transition {{ $selectedType === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua ({{ $counts['all'] }})
                </a>
                <a href="{{ route('debts.index', array_merge(request()->query(), ['type' => 'receivable'])) }}"
                   class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $selectedType === 'receivable' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-emerald-700' }}">
                    <span>Piutang (Dipinjam Orang)</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $selectedType === 'receivable' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['receivable'] }}</span>
                </a>
                <a href="{{ route('debts.index', array_merge(request()->query(), ['type' => 'debt'])) }}"
                   class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $selectedType === 'debt' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-rose-700' }}">
                    <span>Hutang Saya</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $selectedType === 'debt' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['debt'] }}</span>
                </a>
                <a href="{{ route('debts.index', array_merge(request()->query(), ['type' => 'rent'])) }}"
                   class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $selectedType === 'rent' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-indigo-700' }}">
                    <span>Sewa & Tagihan (Rent)</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $selectedType === 'rent' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['rent'] }}</span>
                </a>
            </div>

            <!-- Add Button -->
            <button @click="createModal = true" 
                    class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm hover:shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Tambah Data Pinjaman / Sewa</span>
            </button>
        </div>

        <!-- Filter Row: Status & Search -->
        <form method="GET" action="{{ route('debts.index') }}" class="flex flex-col sm:flex-row items-center gap-3 pt-2 border-t border-slate-100">
            <input type="hidden" name="type" value="{{ $selectedType }}">
            
            <div class="w-full sm:w-48">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-500">
                    <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="unpaid" {{ $selectedStatus === 'unpaid' ? 'selected' : '' }}>Belum Bayar (UNPAID)</option>
                    <option value="nyicil" {{ $selectedStatus === 'nyicil' ? 'selected' : '' }}>Sedang Dicicil (NYICIL)</option>
                    <option value="paid" {{ $selectedStatus === 'paid' ? 'selected' : '' }}>Lunas (PAID)</option>
                </select>
            </div>

            <div class="w-full flex-1 relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama peminjam, pihak, atau keterangan..."
                       class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            @if($search || $selectedStatus !== 'all' || $selectedType !== 'all')
                <a href="{{ route('debts.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Jenis & Pihak / Item</th>
                        <th class="py-3.5 px-4 text-right">Total Pinjaman / Sewa</th>
                        <th class="py-3.5 px-4 text-center">Progress Cicilan</th>
                        <th class="py-3.5 px-4 text-right">Sisa Belum Lunas</th>
                        <th class="py-3.5 px-4">Jatuh Tempo</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($debts as $item)
                        @php
                            $isOverdue = $item->due_date && $item->status->value !== 'paid' && $item->due_date->isPast();
                            $isReceivable = $item->type->value === 'receivable';
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Jenis & Nama -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-start gap-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $item->type->badgeClass() }}">
                                        {{ $item->type->shortLabel() }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $item->name }}</p>
                                        @if($item->rent_type)
                                            <p class="text-[11px] text-indigo-600 font-semibold">Tipe: {{ $item->rent_type }}</p>
                                        @endif
                                        @if($item->notes)
                                            <p class="text-[11px] text-slate-500 line-clamp-1">{{ $item->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 text-sm">
                                Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                            </td>

                            <!-- Progress Cicilan -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="max-w-[140px] mx-auto space-y-1">
                                    <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono">
                                        <span>Rp {{ number_format($item->paid_amount, 0, ',', '.') }}</span>
                                        <span>{{ $item->progressPercentage() }}%</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300 {{ $item->status->value === 'paid' ? 'bg-emerald-500' : ($isReceivable ? 'bg-emerald-400' : 'bg-rose-500') }}"
                                             style="width: {{ $item->progressPercentage() }}%"></div>
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $item->payments->count() }}x cicilan tercatat
                                    </div>
                                </div>
                            </td>

                            <!-- Sisa -->
                            <td class="py-3.5 px-4 text-right">
                                <span class="font-mono font-bold text-sm {{ $item->remaining_amount <= 0 ? 'text-slate-400' : ($isReceivable ? 'text-emerald-700' : 'text-rose-600') }}">
                                    Rp {{ number_format($item->remaining_amount, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Jatuh Tempo -->
                            <td class="py-3.5 px-4">
                                @if ($item->due_date)
                                    <div class="space-y-0.5">
                                        <p class="text-slate-700 font-semibold">{{ $item->due_date->translatedFormat('d M Y') }}</p>
                                        @if ($isOverdue)
                                            <span class="inline-flex items-center gap-1 text-[10px] text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">
                                                Lewat Tempo!
                                            </span>
                                        @elseif($item->status->value !== 'paid' && $item->due_date->diffInDays(now()) <= 3)
                                            <span class="inline-flex items-center text-[10px] text-amber-600 font-semibold">
                                                Segera jatuh tempo
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Tidak ditentukan</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $item->status->badgeClass() }}">
                                    {{ $item->status->code() }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Tombol Bayar / Cicil -->
                                    @if ($item->status->value !== 'paid')
                                        <button @click="payData = {
                                                    debt_id: '{{ $item->id }}',
                                                    debt_name: '{{ addslashes($item->name) }}',
                                                    debt_type: '{{ $item->type->value }}',
                                                    total_amount: {{ $item->total_amount }},
                                                    paid_amount: {{ $item->paid_amount }},
                                                    remaining_amount: {{ $item->remaining_amount }},
                                                    amount: '',
                                                    payment_date: '{{ now()->toDateString() }}',
                                                    payment_method_id: '{{ $paymentMethods->first()?->id ?? '' }}',
                                                    record_as_transaction: true,
                                                    notes: ''
                                                }; payModal = true"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] transition flex items-center gap-1 cursor-pointer"
                                                title="Catat Cicilan / Pelunasan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            <span>Bayar</span>
                                        </button>
                                    @endif

                                    <!-- Tombol Riwayat Pembayaran -->
                                    <button @click="historyData = {
                                                debt_id: '{{ $item->id }}',
                                                debt_name: '{{ addslashes($item->name) }}',
                                                debt_type: '{{ $item->type->label() }}',
                                                total_amount: {{ $item->total_amount }},
                                                remaining_amount: {{ $item->remaining_amount }},
                                                payments: {{ json_encode($item->payments->map(fn($p) => [
                                                    'id' => $p->id,
                                                    'amount' => $p->amount,
                                                    'payment_date' => $p->payment_date->format('Y-m-d'),
                                                    'payment_method' => $p->paymentMethod?->name ?? 'Tunai',
                                                    'notes' => $p->notes ?? '-'
                                                ])) }}
                                            }; historyModal = true"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                                            title="Lihat Riwayat Cicilan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>

                                    <!-- Tombol Edit -->
                                    <button @click="editData = {
                                                id: '{{ $item->id }}',
                                                type: '{{ $item->type->value }}',
                                                name: '{{ addslashes($item->name) }}',
                                                total_amount: formatRupiahInput('{{ $item->total_amount }}'),
                                                start_date: '{{ $item->start_date?->format('Y-m-d') ?? '' }}',
                                                due_date: '{{ $item->due_date?->format('Y-m-d') ?? '' }}',
                                                rent_type: '{{ addslashes($item->rent_type ?? '') }}',
                                                notes: '{{ addslashes($item->notes ?? '') }}'
                                            }; editModal = true"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer"
                                            title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <button @click="$dispatch('open-delete', { 
                                                action: '{{ route('debts.destroy', $item->id) }}', 
                                                message: 'Hapus data {{ $item->type->shortLabel() }} {{ $item->name }} (Rp {{ number_format($item->total_amount, 0, ',', '.') }})?' 
                                            })"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="font-semibold text-slate-600">Belum ada data pinjaman atau sewa</p>
                                    <p class="text-[11px] text-slate-400">Klik tombol di atas untuk mencatat orang yang berhutang ke Anda atau pinjaman pribadi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($debts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $debts->links() }}
            </div>
        @endif
    </div>

    <!-- Modal 1: Tambah Pinjaman / Sewa (Create) -->
    <x-modal name="createModal" title="Tambah Hutang, Piutang atau Sewa">
        <form method="POST" action="{{ route('debts.store') }}" class="space-y-4">
            @csrf

            <!-- Jenis Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Jenis Catatan *</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition"
                           :class="createType === 'receivable' ? 'bg-emerald-50 border-emerald-500 text-emerald-900 font-bold' : 'text-slate-600'">
                        <input type="radio" name="type" value="receivable" x-model="createType" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-xs font-bold block">Piutang</span>
                            <span class="text-[10px] text-slate-400 font-normal">Orang pinjam ke kita</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition"
                           :class="createType === 'debt' ? 'bg-rose-50 border-rose-500 text-rose-900 font-bold' : 'text-slate-600'">
                        <input type="radio" name="type" value="debt" x-model="createType" class="text-rose-600 focus:ring-rose-500">
                        <div>
                            <span class="text-xs font-bold block">Hutang Saya</span>
                            <span class="text-[10px] text-slate-400 font-normal">Kita pinjam ke orang/bank</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition"
                           :class="createType === 'rent' ? 'bg-indigo-50 border-indigo-500 text-indigo-900 font-bold' : 'text-slate-600'">
                        <input type="radio" name="type" value="rent" x-model="createType" class="text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <span class="text-xs font-bold block">Sewa (Rent)</span>
                            <span class="text-[10px] text-slate-400 font-normal">Tagihan kos/kontrakan</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Nama Pihak / Item -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    <span x-text="createType === 'receivable' ? 'Nama Orang Yang Meminjam *' : (createType === 'debt' ? 'Nama Pemberi Pinjaman / Bank *' : 'Nama Item Sewaan (Kosan/Kontrakan) *')"></span>
                </label>
                <input type="text" name="name" required placeholder="Contoh: Budi Prasetyo / Bank Mandiri / Kost Bulanan"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <!-- Total Nominal dengan titik otomatis -->
            <x-money-input name="total_amount" label="Total Nominal (Rp)" placeholder="Contoh: 1.000.000" required />

            <!-- Input Khusus jika Rent -->
            <div x-show="createType === 'rent'" class="transition">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Sewa (Opsional)</label>
                <input type="text" name="rent_type" placeholder="Contoh: Kost Bulanan, Kontrakan Rumah, Sewa Server"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Tanggal Pinjam & Jatuh Tempo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai / Pinjam</label>
                    <input type="date" name="start_date" value="{{ now()->toDateString() }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Jatuh Tempo (Deadline)</label>
                    <input type="date" name="due_date"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan kesepakatan, nomor kontak, dll..."
                          class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-emerald-500"></textarea>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" @click="createModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm cursor-pointer">Simpan Data</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal 2: Catat Pembayaran / Cicilan -->
    <x-modal name="payModal" title="Catat Cicilan / Pelunasan">
        <form :action="'{{ url('/debts') }}/' + payData.debt_id + '/payment'" method="POST" class="space-y-4">
            @csrf

            <!-- Info Box Ringkasan Sisa -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Nama:</span>
                    <strong class="text-slate-900" x-text="payData.debt_name"></strong>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Sisa Belum Lunas:</span>
                    <strong class="font-mono text-sm text-rose-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(payData.remaining_amount)"></strong>
                </div>
                <button type="button" @click="setFullPayment()" 
                        class="w-full py-1.5 px-3 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-[11px] font-bold transition flex items-center justify-center gap-1 cursor-pointer">
                    <span>⚡ Klik untuk Lunasi Penuh Seluruh Sisa</span>
                </button>
            </div>

            <!-- Nominal Cicilan dengan auto format titik -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Bayar / Cicilan (Rp) *</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                    <input type="text"
                           inputmode="numeric"
                           name="amount"
                           x-model="payData.amount"
                           x-money
                           required
                           placeholder="Contoh: 250.000"
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Titik otomatis ditambahkan saat mengetik</p>
            </div>

            <!-- Tanggal & Metode Bayar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Pembayaran *</label>
                    <input type="date" name="payment_date" x-model="payData.payment_date" required
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode / Dompet</label>
                    <select name="payment_method_id" x-model="payData.payment_method_id"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-emerald-500">
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Checkbox Sinkronkan ke Transaksi Buku Kas -->
            <div class="p-3 bg-emerald-50/70 border border-emerald-100 rounded-xl">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="record_as_transaction" value="1" x-model="payData.record_as_transaction" class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <div class="text-[11px] text-emerald-900">
                        <span class="font-bold block">Sinkronkan ke Buku Transaksi Kas</span>
                        <span class="text-emerald-700 text-[10px]">
                            Otomatis tercatat sebagai <span x-text="payData.debt_type === 'receivable' ? 'Pemasukan' : 'Pengeluaran'"></span> pada dompet yang dipilih.
                        </span>
                    </div>
                </label>
            </div>

            <!-- Catatan Cicilan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Cicilan (Opsional)</label>
                <input type="text" name="notes" x-model="payData.notes" placeholder="Contoh: Cicilan ke-2, transfer BCA, pelunasan"
                       class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-emerald-500">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" @click="payModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm cursor-pointer">Simpan Pembayaran</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal 3: Riwayat Cicilan (History Modal) -->
    <x-modal name="historyModal" title="Riwayat Pembayaran & Cicilan">
        <div class="space-y-4">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold" x-text="historyData.debt_type"></span>
                    <strong class="text-sm text-slate-900 font-bold" x-text="historyData.debt_name"></strong>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 block">Sisa Hutang/Piutang</span>
                    <span class="font-mono font-bold text-sm text-rose-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(historyData.remaining_amount)"></span>
                </div>
            </div>

            <!-- List Pembayaran -->
            <div class="space-y-2 max-h-72 overflow-y-auto">
                <template x-if="historyData.payments && historyData.payments.length > 0">
                    <div class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
                        <template x-for="(p, index) in historyData.payments" :key="p.id">
                            <div class="p-3 bg-white flex items-center justify-between text-xs hover:bg-slate-50 transition">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold font-mono text-slate-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(p.amount)"></span>
                                        <span class="text-[10px] px-1.5 py-0.2 bg-slate-100 rounded text-slate-600" x-text="p.payment_method"></span>
                                    </div>
                                    <p class="text-[11px] text-slate-500" x-text="p.payment_date + (p.notes !== '-' ? ' • ' + p.notes : '')"></p>
                                </div>
                                <form :action="'{{ url('/debts') }}/' + historyData.debt_id + '/payments/' + p.id" method="POST" 
                                      onsubmit="return confirm('Hapus catatan pembayaran ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 rounded text-slate-300 hover:text-rose-600 transition" title="Hapus Riwayat Ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!historyData.payments || historyData.payments.length === 0">
                    <div class="py-8 text-center text-slate-400 text-xs">
                        Belum ada pembayaran atau cicilan yang dicatat.
                    </div>
                </template>
            </div>

            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="button" @click="historyModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Modal 4: Edit Data Pinjaman / Sewa -->
    <x-modal name="editModal" title="Edit Data Pinjaman / Sewa">
        <form :action="'{{ url('/debts') }}/' + editData.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Jenis Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis *</label>
                <select name="type" x-model="editData.type" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-emerald-500">
                    <option value="receivable">Piutang (Orang pinjam ke kita)</option>
                    <option value="debt">Hutang (Pinjaman pribadi kita)</option>
                    <option value="rent">Sewa & Tagihan Rutin (Rent)</option>
                </select>
            </div>

            <!-- Nama Pihak / Item -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Pihak / Item *</label>
                <input type="text" name="name" x-model="editData.name" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-500">
            </div>

            <!-- Total Nominal -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Total Nominal (Rp) *</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                    <input type="text"
                           inputmode="numeric"
                           name="total_amount"
                           x-model="editData.total_amount"
                           x-money
                           required
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
            </div>

            <div x-show="editData.type === 'rent'">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Sewa (Opsional)</label>
                <input type="text" name="rent_type" x-model="editData.rent_type" placeholder="Contoh: Kost, Kontrakan"
                       class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" x-model="editData.start_date"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Jatuh Tempo</label>
                    <input type="date" name="due_date" x-model="editData.due_date"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan (Opsional)</label>
                <textarea name="notes" x-model="editData.notes" rows="2"
                          class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" @click="editModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>

</div>
</x-layouts.app>
