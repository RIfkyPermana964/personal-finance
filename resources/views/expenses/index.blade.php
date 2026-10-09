<x-layouts.app title="Pengeluaran" header="Catat Pengeluaran" subheader="Rekam semua pengeluaran harian Anda secara detail">

<div x-data="{ createModal: false, editModal: false, editData: { id: null, amount: '', category_id: '', payment_method_id: '', transaction_date: '', description: '' } }" class="space-y-6">

    <!-- Header Action Bar & Stat -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="sm:col-span-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white border border-rose-100 shadow-xs">
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Pengeluaran Bulan {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
                <h3 class="text-2xl font-black text-rose-600 font-mono mt-1">Rp {{ number_format($totalMonthExpense, 0, ',', '.') }}</h3>
            </div>
            <button @click="createModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catat Pengeluaran
            </button>
        </div>

        <!-- Filter Periode -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-col justify-center">
            <p class="text-xs text-slate-500 font-semibold mb-2">Pilih Bulan & Tahun</p>
            <form method="GET" action="{{ route('expenses.index') }}" class="flex items-center gap-2">
                <select name="month" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-rose-500 focus:bg-white transition cursor-pointer">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('M') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-rose-500 focus:bg-white transition cursor-pointer">
                    @for ($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">
                    Go
                </button>
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <x-card title="Daftar Riwayat Pengeluaran" subtitle="Menampilkan rincian pengeluaran pada periode yang dipilih">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Kategori & Subkategori</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                        <th class="py-3.5 px-4">Metode Bayar</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900">{{ $item->category?->name ?? 'Pengeluaran Umum' }}</span>
                                    @if ($item->category?->parent)
                                        <span class="text-[10px] text-slate-400 font-medium">↳ {{ $item->category->parent->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-slate-900 font-medium">{{ $item->description ?: '-' }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->paymentMethod?->name ?? 'Tunai' }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-rose-600 text-sm">
                                - Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="editData = {
                                        id: '{{ $item->id }}',
                                        amount: formatRupiahInput('{{ $item->amount }}'),
                                        transaction_date: '{{ $item->transaction_date->format('Y-m-d') }}',
                                        category_id: '{{ $item->category_id }}',
                                        payment_method_id: '{{ $item->payment_method_id }}',
                                        description: '{{ addslashes($item->description) }}'
                                    }; editModal = true;" 
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button @click="$dispatch('open-delete', { action: '{{ route('expenses.destroy', $item->id) }}', message: 'Hapus pengeluaran Rp {{ number_format($item->amount, 0, ',', '.') }} ({{ $item->description }})?' })"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada data pengeluaran pada periode ini.
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

    <!-- Modal: Catat Pengeluaran Baru -->
    <x-modal name="createModal" title="Catat Pengeluaran Baru" maxWidth="md">
        <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="type" value="expense">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan Pengeluaran (Opsional)</label>
                <input type="text" name="description" placeholder="Contoh: Makan Siang, Bensin, Tagihan Wifi"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal (Rp) *</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                    <input type="text" inputmode="numeric" name="amount" x-money required placeholder="Contoh: 50.000"
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Titik pemisah ribuan otomatis ditambahkan saat mengetik</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori *</label>
                    <select name="category_id" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-rose-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $parent)
                            <optgroup label="{{ $parent->name }}">
                                <option value="{{ $parent->id }}">{{ $parent->name }} (Utama)</option>
                                @foreach ($parent->children as $child)
                                    <option value="{{ $child->id }}">&nbsp;&nbsp;↳ {{ $child->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Bayar *</label>
                    <select name="payment_method_id" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-rose-500">
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
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-rose-500">
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="createModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Simpan Pengeluaran</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal: Edit Pengeluaran -->
    <x-modal name="editModal" title="Edit Data Pengeluaran" maxWidth="md">
        <form :action="'/expenses/' + editData.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="expense">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan Pengeluaran</label>
                <input type="text" name="description" x-model="editData.description" placeholder="Contoh: Makan Siang, Bensin, Tagihan Wifi"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal (Rp) *</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                    <input type="text" inputmode="numeric" name="amount" x-model="editData.amount" x-money required
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Titik pemisah ribuan otomatis ditambahkan saat mengetik</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori *</label>
                    <select name="category_id" x-model="editData.category_id" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-rose-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $parent)
                            <optgroup label="{{ $parent->name }}">
                                <option value="{{ $parent->id }}">{{ $parent->name }} (Utama)</option>
                                @foreach ($parent->children as $child)
                                    <option value="{{ $child->id }}">&nbsp;&nbsp;↳ {{ $child->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Bayar *</label>
                    <select name="payment_method_id" x-model="editData.payment_method_id" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-rose-500">
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
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-rose-500">
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Perbarui Pengeluaran</button>
            </div>
        </form>
    </x-modal>

</div>
</x-layouts.app>