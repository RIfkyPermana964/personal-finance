<x-layouts.app title="Pengeluaran" header="Pencatatan Pengeluaran" subheader="Pantau dan catat seluruh pengeluaran konsumtif, tagihan bulanan, operasional, dan gaya hidup" x-data="{ createModal: false, editModal: false, editData: {} }">

    <!-- Header Action Bar & Stat -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="sm:col-span-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md">
            <div>
                <p class="text-xs text-slate-300 font-semibold uppercase">Total Pengeluaran Bulan {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
                <h3 class="text-2xl font-black text-rose-400 font-mono mt-1">Rp {{ number_format($totalMonthExpense, 0, ',', '.') }}</h3>
            </div>
            <button @click="createModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-rose-600/30 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Catat Pengeluaran
            </button>
        </div>

        <!-- Filter Periode -->
        <div class="p-5 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md flex flex-col justify-center">
            <p class="text-xs text-slate-300 font-semibold mb-2">Pilih Bulan & Tahun</p>
            <form method="GET" action="{{ route('expenses.index') }}" class="flex items-center gap-2">
                <select name="month" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('M') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
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

    <!-- Main Table Card -->
    <x-card title="Daftar Riwayat Pengeluaran" subtitle="Menampilkan rincian pengeluaran pada periode yang dipilih">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/60 text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Kategori & Subkategori</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                        <th class="py-3.5 px-4">Metode Bayar</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($transactions as $item)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4 font-mono text-slate-300">{{ \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-white">{{ $item->category?->name ?? 'Pengeluaran Umum' }}</span>
                                    @if ($item->category?->parent)
                                        <span class="text-[10px] text-slate-300">{{ $item->category->parent->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-white font-medium">{{ $item->description ?: '-' }}</td>
                            <td class="py-3 px-4 text-slate-300">{{ $item->paymentMethod?->name ?? 'Tunai' }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-rose-400 text-sm">
                                - Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="editData = {
                                        id: '{{ $item->id }}',
                                        amount: '{{ $item->amount }}',
                                        transaction_date: '{{ $item->transaction_date->format('Y-m-d') }}',
                                        category_id: '{{ $item->category_id }}',
                                        payment_method_id: '{{ $item->payment_method_id }}',
                                        description: '{{ addslashes($item->description) }}'
                                    }; editModal = true;" 
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button @click="deleteAction = '{{ route('expenses.destroy', $item->id) }}'; deleteMessage = 'Hapus pengeluaran Rp {{ number_format($item->amount, 0, ',', '.') }} ({{ $item->description }})?'; deleteModal = true;"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-300">
                                Belum ada data pengeluaran pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </x-card>

    <!-- Modal Catat Pengeluaran Baru -->
    <x-modal name="createModal" title="Catat Pengeluaran Baru">
        <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nominal Pengeluaran (Rp) *</label>
                <input type="number" step="0.01" name="amount" required placeholder="Contoh: 150000"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-rose-500">
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori / Subkategori *</label>
                    <select name="category_id" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
                        <option value="">-- Pilih Kategori Pengeluaran --</option>
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
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Metode Pembayaran *</label>
                    <select name="payment_method_id" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
                        <option value="">-- Pilih Metode Bayar --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tanggal Transaksi *</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                       class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan / Deskripsi</label>
                <input type="text" name="description" placeholder="Contoh: Beli Bensin Pertamax + Cemilan Shift Malam"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-rose-600/30">Simpan Pengeluaran</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit Pengeluaran -->
    <x-modal name="editModal" title="Edit Data Pengeluaran">
        <form :action="'{{ url('expenses') }}/' + editData.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nominal Pengeluaran (Rp) *</label>
                <input type="number" step="0.01" name="amount" x-model="editData.amount" required
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-rose-500">
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori / Subkategori *</label>
                    <select name="category_id" x-model="editData.category_id" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
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
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Metode Pembayaran *</label>
                    <select name="payment_method_id" x-model="editData.payment_method_id" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tanggal Transaksi *</label>
                <input type="date" name="transaction_date" x-model="editData.transaction_date" required
                       class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan / Deskripsi</label>
                <input type="text" name="description" x-model="editData.description"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-rose-600/30">Perbarui Pengeluaran</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>