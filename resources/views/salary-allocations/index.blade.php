<x-layouts.app title="Pembagian Gaji & Saldo" header="Pembagian Gaji & Kesimpulan Saldo" subheader="Rencanakan alokasi gaji bulanan, tandai pos pengeluaran UNPAID/PAID, dan pantau saldo kas & bank">

<div x-data="{ createModal: false, balanceModal: false }" class="space-y-6">

    <!-- Header Periode -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Periode Perencanaan</span>
                <h3 class="text-xl font-bold text-slate-900">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</h3>
            </div>
        </div>
        <form method="GET" action="{{ route('salary-allocations.index') }}" class="flex items-center gap-2">
            <select name="month" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}</option>
                @endfor
            </select>
            <select name="year" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                @for ($y = \Carbon\Carbon::now()->year - 2; $y <= \Carbon\Carbon::now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">Pilih</button>
        </form>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kiri: Kesimpulan Saldo -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">KESIMPULAN SALDO</h3>
                        <p class="text-xs text-slate-500">Posisi kas tunai & rekening bank</p>
                    </div>
                    <button @click="balanceModal = true" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer" title="Edit Saldo Awal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                </div>
                <div class="space-y-3">
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
                <button @click="balanceModal = true" class="w-full py-2.5 px-3 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition text-center cursor-pointer">
                    Atur Saldo Awal & Total Gaji
                </button>
            </div>

            <!-- Status Widget -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-3 shadow-xs">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">STATUS PEMBAYARAN GAJI</h4>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-600">Total Dialokasikan:</span>
                        <span class="font-mono font-bold text-slate-900">Rp {{ number_format($allocationsData['total_allocated'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-semibold flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terbayar (PAID):</span>
                        <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($allocationsData['total_paid'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-rose-700 font-semibold flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Belum Bayar (UNPAID):</span>
                        <span class="font-mono font-bold text-rose-700">Rp {{ number_format($allocationsData['total_unpaid'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanan: Tabel Pembagian Gaji -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 space-y-5 shadow-xs">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Rencana Anggaran</span>
                        <div class="flex items-baseline gap-3 mt-0.5">
                            <h3 class="text-xl font-black text-slate-900 font-mono">Rp {{ number_format($balanceData['total_salary'], 0, ',', '.') }}</h3>
                            <span class="text-xs text-slate-500 font-medium">Total Gaji</span>
                        </div>
                    </div>

                    <button @click="createModal = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition cursor-pointer flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Pos Pengeluaran
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3.5 px-4">Pos Pengeluaran</th>
                                <th class="py-3.5 px-4 text-right">Nominal</th>
                                <th class="py-3.5 px-4 text-center">Status Pembayaran</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($allocationsData['items'] as $item)
                                <tr class="hover:bg-slate-50/70 transition {{ $item->isPaid() ? 'bg-emerald-50/30' : '' }}">
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        {{ $item->item_name }}
                                        @if ($item->notes)
                                            <p class="text-[10px] text-slate-400 font-normal mt-0.5">{{ $item->notes }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 text-sm">
                                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
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
                                    <td class="py-3.5 px-4 text-center">
                                        <button @click="$dispatch('open-delete', { action: '{{ route('salary-allocations.destroy', $item->id) }}', message: 'Hapus pos pengeluaran {{ addslashes($item->item_name) }}?' })"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        Belum ada pos pembagian gaji untuk bulan ini. Klik "+ Tambah Pos Pengeluaran" untuk mulai mengalokasikan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary Bar -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs">
                    <div>
                        <span class="text-slate-500">Sisa Belum Dialokasikan:</span>
                        <span class="font-mono font-bold ml-1 {{ ($balanceData['total_salary'] - $allocationsData['total_allocated']) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            Rp {{ number_format($balanceData['total_salary'] - $allocationsData['total_allocated'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-slate-600">
                        <span>Paid: <strong class="text-emerald-700 font-mono">Rp {{ number_format($allocationsData['total_paid'], 0, ',', '.') }}</strong></span>
                        <span>Unpaid: <strong class="text-rose-700 font-mono">Rp {{ number_format($allocationsData['total_unpaid'], 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal: Tambah Pos Pengeluaran -->
    <x-modal name="createModal" title="Tambah Pos Pengeluaran Gaji" maxWidth="md">
        <form method="POST" action="{{ route('salary-allocations.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Pos Pengeluaran *</label>
                <input type="text" name="item_name" required placeholder="Contoh: Belanja Bulanan, Kuota Internet, Bayar Kos"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Alokasi (Rp) *</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                    <input type="text" inputmode="numeric" name="amount" x-money required placeholder="Contoh: 500.000"
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Titik otomatis ditambahkan saat mengetik</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Pembayaran</label>
                <select name="status" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="unpaid">UNPAID (Belum Bayar)</option>
                    <option value="paid">PAID (Sudah Terbayar)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Sewa kos bulan ini"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="createModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Simpan Pos</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal: Atur Saldo Awal & Total Gaji -->
    <x-modal name="balanceModal" title="Atur Saldo Awal & Total Gaji" maxWidth="md">
        <form method="POST" action="{{ route('salary-allocations.balance') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Total Gaji Bulan Ini (Rp) *</label>
                <input type="text" inputmode="numeric" name="total_salary" x-money value="{{ number_format($balanceData['total_salary'], 0, ',', '.') }}" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                <p class="text-[11px] text-slate-500 mt-1">Total penghasilan atau gaji bersih yang siap dibagikan</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Cash Awal (Tunai)</label>
                    <input type="text" inputmode="numeric" name="cash_initial" x-money value="{{ number_format($balanceData['cash_initial'], 0, ',', '.') }}"
                           class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Saldo Awal (Bank)</label>
                    <input type="text" inputmode="numeric" name="bank_initial" x-money value="{{ number_format($balanceData['bank_initial'], 0, ',', '.') }}"
                           class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="balanceModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Perbarui Saldo</button>
            </div>
        </form>
    </x-modal>

</div>
</x-layouts.app>