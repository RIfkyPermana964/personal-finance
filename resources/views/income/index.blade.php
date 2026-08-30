<x-layouts.app title="Pemasukan" header="Pencatatan Pemasukan" subheader="Kelola seluruh sumber penghasilan gaji, bonus, freelance, dan pendapatan lainnya" x-data="{ createModal: false, editModal: false, editData: {} }">

    <!-- Header Action Bar & Stat -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="sm:col-span-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md">
            <div>
                <p class="text-xs text-slate-300 font-semibold uppercase">Total Pemasukan Bulan {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
                <h3 class="text-2xl font-black text-emerald-400 font-mono mt-1">Rp {{ number_format($totalMonthIncome, 0, ',', '.') }}</h3>
            </div>
            <button @click="createModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/30 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Catat Pemasukan
            </button>
        </div>

        <!-- Filter Periode -->
        <div class="p-5 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md flex flex-col justify-center">
            <p class="text-xs text-slate-300 font-semibold mb-2">Pilih Bulan & Tahun</p>
            <form method="GET" action="{{ route('income.index') }}" class="flex items-center gap-2">
                <select name="month" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('M') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
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
    <x-card title="Daftar Riwayat Pemasukan" subtitle="Menampilkan pemasukan pada periode yang dipilih">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/60 text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Kategori / Sumber</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                        <th class="py-3.5 px-4">Metode Penerimaan</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($transactions as $item)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4 font-mono text-slate-300">{{ \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    {{ $item->category?->name ?? 'Pemasukan Umum' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-white font-medium">{{ $item->description ?: '-' }}</td>
                            <td class="py-3 px-4 text-slate-300">{{ $item->paymentMethod?->name ?? 'Tunai' }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-emerald-400 text-sm">
                                + Rp {{ number_format($item->amount, 0, ',', '.') }}
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
                                    <button @click="deleteAction = '{{ route('income.destroy', $item->id) }}'; deleteMessage = 'Hapus pemasukan Rp {{ number_format($item->amount, 0, ',', '.') }} ({{ $item->description }})?'; deleteModal = true;"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-300">
                                Belum ada data pemasukan pada periode ini.
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

    <!-- Modal Catat Pemasukan Baru -->
    <x-modal name="createModal" title="Catat Pemasukan Baru">
        <form method="POST" action="{{ route('income.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nominal Pemasukan (Rp) *</label>
                <input type="number" step="0.01" name="amount" required placeholder="Contoh: 5000000"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-emerald-500">
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori / Sumber *</label>
                    <select name="category_id" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pilih Kategori Pemasukan --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Metode Penerimaan *</label>
                    <select name="payment_method_id" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pilih Rekening / Dompet --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tanggal Transaksi *</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                       class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan / Deskripsi</label>
                <input type="text" name="description" placeholder="Contoh: Gaji bulan Agustus + Insentif Shift"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/30">Simpan Pemasukan</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit Pemasukan -->
    <x-modal name="editModal" title="Edit Data Pemasukan">
        <form :action="'{{ url('income') }}/' + editData.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nominal Pemasukan (Rp) *</label>
                <input type="number" step="0.01" name="amount" x-model="editData.amount" required
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-emerald-500">
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori / Sumber *</label>
                    <select name="category_id" x-model="editData.category_id" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Metode Penerimaan *</label>
                    <select name="payment_method_id" x-model="editData.payment_method_id" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tanggal Transaksi *</label>
                <input type="date" name="transaction_date" x-model="editData.transaction_date" required
                       class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan / Deskripsi</label>
                <input type="text" name="description" x-model="editData.description"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/30">Perbarui Pemasukan</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>