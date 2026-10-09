<x-layouts.app title="Pemasukan" header="Catat Pemasukan" subheader="Rekam semua sumber pemasukan Anda secara akurat">

<div x-data="{
    createModal: false,
    editModal: false,
    editData: { id: null, amount: '', category_id: '', payment_method_id: '', transaction_date: '', description: '' },
    openEdit(id, amount, category_id, payment_method_id, transaction_date, description) {
        this.editData = { id, amount: formatRupiahInput(amount), category_id: String(category_id), payment_method_id: String(payment_method_id), transaction_date, description };
        this.editModal = true;
    }
}" class="space-y-6">

    <!-- Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('income.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="month" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-500 focus:bg-white transition cursor-pointer">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-500 focus:bg-white transition cursor-pointer">
                @for ($y = \Carbon\Carbon::now()->year - 2; $y <= \Carbon\Carbon::now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">Filter</button>
        </form>
        <button @click="createModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Pemasukan
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="p-5 rounded-2xl bg-white border border-emerald-100 shadow-xs space-y-1">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Pemasukan</p>
            <p class="text-2xl font-black font-mono text-emerald-700">Rp {{ number_format($totalMonthIncome, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-1">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Jumlah Transaksi</p>
            <p class="text-2xl font-black text-slate-900">{{ $transactions->total() }}</p>
            <p class="text-xs text-slate-400">entri tercatat</p>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-sky-100 shadow-xs space-y-1">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Rata-rata Transaksi</p>
            <p class="text-2xl font-black font-mono text-sky-700">
                Rp {{ $transactions->total() > 0 ? number_format($totalMonthIncome / $transactions->total(), 0, ',', '.') : '0' }}
            </p>
            <p class="text-xs text-slate-400">per entri</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Riwayat Pemasukan</h3>
            <span class="text-xs font-semibold text-slate-500">{{ $transactions->total() }} transaksi</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Metode</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d M Y') }}</td>
                            <td class="py-3.5 px-4 text-slate-900 font-medium">{{ $item->description ?: '-' }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">{{ $item->category->name ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $item->paymentMethod->name ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-600 text-sm">+ Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="openEdit({{ $item->id }}, {{ $item->amount }}, {{ $item->category_id }}, {{ $item->payment_method_id }}, '{{ $item->transaction_date->format('Y-m-d') }}', '{{ addslashes($item->description ?? '') }}')"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button @click="$dispatch('open-delete', { action: '{{ route('income.destroy', $item->id) }}', message: 'Hapus pemasukan Rp {{ number_format($item->amount, 0, ',', '.') }}?' })"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-400">Belum ada data pemasukan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Catat Pemasukan Baru -->
    <x-modal name="createModal" title="Catat Pemasukan Baru" maxWidth="md">
        <form method="POST" action="{{ route('income.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="type" value="income">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal (Rp) *</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                    <input type="text" inputmode="numeric" name="amount" x-money required placeholder="Contoh: 5.000.000"
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Titik pemisah ribuan otomatis ditambahkan saat mengetik</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori *</label>
                    <select name="category_id" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Terima *</label>
                    <select name="payment_method_id" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pilih Metode --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @php
                $defaultDate = ($month == \Carbon\Carbon::now()->month && $year == \Carbon\Carbon::now()->year)
                    ? \Carbon\Carbon::now()->format('Y-m-d')
                    : \Carbon\Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
            @endphp
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal *</label>
                <input type="date" name="transaction_date" value="{{ $defaultDate }}" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan / Sumber (Opsional)</label>
                <input type="text" name="description" placeholder="Contoh: Gaji Bulanan, Bonus, Proyek Freelance"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500">
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="createModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Simpan Pemasukan</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal: Edit Pemasukan -->
    <x-modal name="editModal" title="Edit Pemasukan" maxWidth="md">
        <form :action="'/income/' + editData.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="income">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal (Rp) *</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                    <input type="text" inputmode="numeric" name="amount" x-model="editData.amount" x-money required
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Titik pemisah ribuan otomatis ditambahkan saat mengetik</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori *</label>
                    <select name="category_id" x-model="editData.category_id" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Terima *</label>
                    <select name="payment_method_id" x-model="editData.payment_method_id" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pilih Metode --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal *</label>
                <input type="date" name="transaction_date" x-model="editData.transaction_date" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan / Sumber</label>
                <input type="text" name="description" x-model="editData.description"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500">
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Perbarui Pemasukan</button>
            </div>
        </form>
    </x-modal>

</div>
</x-layouts.app>