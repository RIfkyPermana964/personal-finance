<x-layouts.app title="Pembagian Gaji & Saldo" header="Pembagian Gaji & Kesimpulan Saldo" subheader="Rencanakan alokasi gaji bulanan, tandai pos pengeluaran UNPAID/PAID, dan pantau saldo kas & bank" x-data="{ createModal: false, balanceModal: false }">

    <!-- Header Periode Bulan & Filter -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <span class="text-xs text-slate-300 font-semibold uppercase">Periode Perencanaan</span>
                <h3 class="text-xl font-black text-white">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</h3>
            </div>
        </div>

        <form method="GET" action="{{ route('salary-allocations.index') }}" class="flex items-center gap-2">
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
                Pilih
            </button>
        </form>
    </div>

    <!-- 2 Main Grid Columns: KESIMPULAN SALDO & PEMBAGIAN GAJI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Column 1: KESIMPULAN (Cash & Saldo Tracker) -->
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-[#111827]/90 border border-slate-800/80 rounded-2xl p-5 space-y-4 backdrop-blur-md shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-white tracking-wide">KESIMPULAN SALDO</h3>
                        <p class="text-xs text-slate-300">Posisi kas tunai & rekening bank</p>
                    </div>
                    <button @click="balanceModal = true" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 transition" title="Edit Saldo Awal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                </div>

                <div class="space-y-3">
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

                <div class="pt-2">
                    <button @click="balanceModal = true" class="w-full py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition text-center">
                        Atur Saldo Awal & Total Gaji
                    </button>
                </div>
            </div>

            <!-- Gaji Stats Breakdown Widget -->
            <div class="bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-5 space-y-3 backdrop-blur-md shadow-lg">
                <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Status Pembayaran Gaji</h4>
                
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-300">Total Dialokasikan:</span>
                        <span class="font-mono font-bold text-white">Rp {{ number_format($allocationsData['total_allocated'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-emerald-400 font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Terbayar (PAID):
                        </span>
                        <span class="font-mono font-bold text-emerald-400">Rp {{ number_format($allocationsData['total_paid'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-rose-400 font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span> Belum Bayar (UNPAID):
                        </span>
                        <span class="font-mono font-bold text-rose-400">Rp {{ number_format($allocationsData['total_unpaid'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Column 2: PEMBAGIAN GAJI (Interactive Spreadsheet Table) -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-[#111827]/90 border border-slate-800/80 rounded-2xl p-5 sm:p-6 space-y-5 backdrop-blur-md shadow-xl">
                
                <!-- Table Header & Salary Banner -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                    <div>
                        <span class="text-xs text-slate-300 font-bold uppercase tracking-wider">Rencana Anggaran</span>
                        <h3 class="text-base font-black text-white tracking-wide">PEMBAGIAN GAJI</h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-right">
                            <span class="text-[10px] text-slate-300 uppercase font-bold block">TOTAL GAJI BULAN INI</span>
                            <span class="text-lg font-mono font-black text-emerald-400">Rp {{ number_format($balanceData['total_salary'], 0, ',', '.') }}</span>
                        </div>
                        <button @click="createModal = true" class="px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/30 transition cursor-pointer flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            + Tambah Pos
                        </button>
                    </div>
                </div>

                <!-- Table Pos Pengeluaran Gaji -->
                <div class="overflow-x-auto rounded-xl border border-slate-800/80">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900 text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="py-3.5 px-4">Pos Pengeluaran</th>
                                <th class="py-3.5 px-4 text-right">Nominal Alokasi</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @forelse ($allocationsData['items'] as $item)
                                <tr class="hover:bg-slate-800/30 transition {{ $item->isPaid() ? 'bg-emerald-500/5' : '' }}">
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold text-white text-sm block">{{ $item->item_name }}</span>
                                        @if ($item->category)
                                            <span class="text-[10px] text-slate-300">({{ $item->category->name }})</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-sm text-white">
                                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <form method="POST" action="{{ route('salary-allocations.toggle', $item->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Klik untuk mengubah status" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider transition duration-150 cursor-pointer shadow-sm {{ $item->isPaid() ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 hover:bg-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' }}">
                                                @if ($item->isPaid())
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    PAID
                                                @else
                                                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                                                    UNPAID
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <button @click="deleteAction = '{{ route('salary-allocations.destroy', $item->id) }}'; deleteMessage = 'Hapus pos pengeluaran gaji {{ $item->item_name }}?'; deleteModal = true;" 
                                                class="text-slate-500 hover:text-rose-400 p-1.5 rounded-lg transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-slate-300">
                                        Belum ada pos pembagian gaji untuk bulan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Math Calculator -->
                <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-slate-300">Sisa Gaji Belum Dialokasikan:</span>
                        <strong class="font-mono text-sm ml-1.5 {{ ($balanceData['total_salary'] - $allocationsData['total_allocated']) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            Rp {{ number_format($balanceData['total_salary'] - $allocationsData['total_allocated'], 0, ',', '.') }}
                        </strong>
                    </div>
                    <div class="text-slate-300">
                        {{ $allocationsData['items']->where('status', 'paid')->count() }} dari {{ $allocationsData['items']->count() }} pos sudah dibayarkan
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Modal Tambah Pos Pembagian Gaji -->
    <x-modal name="createModal" title="Tambah Pos Pengeluaran Gaji">
        <form method="POST" action="{{ route('salary-allocations.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Pos Pengeluaran / Tagihan *</label>
                <input type="text" name="item_name" required placeholder="Contoh: Sewa Kosan / Uang Makan / Listrik"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nominal Alokasi (Rp) *</label>
                <input type="number" step="0.01" name="amount" required placeholder="Contoh: 1000000"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori (Opsional)</label>
                <select name="category_id" class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Status Awal *</label>
                <select name="status" class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                    <option value="unpaid">UNPAID (Belum Dibayarkan)</option>
                    <option value="paid">PAID (Sudah Lunas Terbayar)</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/30">Simpan Pos</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Atur Saldo Awal & Total Gaji -->
    <x-modal name="balanceModal" title="Atur Saldo Awal & Total Gaji Bulanan">
        <form method="POST" action="{{ route('salary-allocations.balance') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Total Gaji Bulan Ini (Rp) *</label>
                <input type="number" step="0.01" name="total_salary" value="{{ $balanceData['total_salary'] }}" required placeholder="Contoh: 6000000"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">CASH AWAL (Uang Tunai Awal Bulan) *</label>
                    <input type="number" step="0.01" name="cash_initial" value="{{ $balanceData['cash_initial'] }}" required placeholder="Contoh: 500000"
                           class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">SALDO AWAL (Saldo Bank Awal Bulan) *</label>
                    <input type="number" step="0.01" name="bank_initial" value="{{ $balanceData['bank_initial'] }}" required placeholder="Contoh: 2500000"
                           class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="balanceModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>