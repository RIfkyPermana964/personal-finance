<x-layouts.app title="Riwayat Transaksi" header="Tabel Transaksi Finansial" subheader="Format pembukuan transaksi lengkap dengan pencatatan Total Masuk (In) dan Total Keluar (Out)">

    <!-- Filter Card -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Pencarian Item / Keterangan</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari..."
                           class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Tipe Transaksi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Jenis Transaksi</label>
                    <select name="type" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Semua Jenis --</option>
                        <option value="income" {{ ($filters['type'] ?? '') === 'income' ? 'selected' : '' }}>Pemasukan (In)</option>
                        <option value="expense" {{ ($filters['type'] ?? '') === 'expense' ? 'selected' : '' }}>Pengeluaran (Out)</option>
                        <option value="saving_deposit" {{ ($filters['type'] ?? '') === 'saving_deposit' ? 'selected' : '' }}>Alokasi Tabungan</option>
                        <option value="saving_withdraw" {{ ($filters['type'] ?? '') === 'saving_withdraw' ? 'selected' : '' }}>Penarikan Tabungan</option>
                    </select>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori</label>
                    <select name="category_id" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
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
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Metode Bayar</label>
                    <select name="payment_method_id" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Semua Metode --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" {{ ($filters['payment_method_id'] ?? '') == $pm->id ? 'selected' : '' }}>
                                {{ $pm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Row 2: Date Ranges & Sort Actions -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-3 border-t border-slate-800/80">
                <div class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none">
                    <span class="text-xs text-slate-300">s/d</span>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none">
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('transactions.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 transition text-center">
                        Reset
                    </a>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition cursor-pointer">
                        Filter Data
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Spreadsheet-Style Master Transactions Table -->
    <x-card title="Buku Kas Transaksi" subtitle="Tabel pencatatan transaksi masuk dan keluar (Total data: {{ $transactions->total() }})">
        <div class="overflow-x-auto rounded-xl border border-slate-800/80">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-[#0B1120] text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-3">Tanggal</th>
                        <th class="py-3.5 px-3">Item / Nama</th>
                        <th class="py-3.5 px-3 text-right">Harga</th>
                        <th class="py-3.5 px-3">Keterangan</th>
                        <th class="py-3.5 px-3 text-center">Jenis</th>
                        <th class="py-3.5 px-3">Kategori</th>
                        <th class="py-3.5 px-3">Metode</th>
                        <th class="py-3.5 px-3 text-right text-rose-400">Total Out</th>
                        <th class="py-3.5 px-3 text-right text-emerald-400">Total In</th>
                        <th class="py-3.5 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-slate-800/30 transition">
                            <!-- Tanggal -->
                            <td class="py-3 px-3 font-mono text-slate-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d/m/Y') }}
                            </td>
                            
                            <!-- Item -->
                            <td class="py-3 px-3 font-bold text-white whitespace-nowrap">
                                {{ $tx->description ?: ($tx->category?->name ?: $tx->type->label()) }}
                            </td>

                            <!-- Harga / Nominal Asli -->
                            <td class="py-3 px-3 text-right font-mono font-semibold text-slate-300 whitespace-nowrap">
                                Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>

                            <!-- Keterangan -->
                            <td class="py-3 px-3 text-slate-300 max-w-xs truncate">
                                {{ $tx->description ?: '-' }}
                            </td>

                            <!-- Jenis -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if ($tx->type->value === 'income')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">INCOME</span>
                                @elseif ($tx->type->value === 'expense')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">EXPENSE</span>
                                @elseif ($tx->type->value === 'saving_deposit')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">SAVING</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">WITHDRAW</span>
                                @endif
                            </td>

                            <!-- Kategori -->
                            <td class="py-3 px-3 whitespace-nowrap">
                                @if ($tx->category)
                                    <span class="font-medium text-white">{{ $tx->category->name }}</span>
                                    @if ($tx->category->parent)
                                        <span class="text-[10px] text-slate-300 block">({{ $tx->category->parent->name }})</span>
                                    @endif
                                @elseif ($tx->savingGoal)
                                    <span class="font-medium text-cyan-300">{{ $tx->savingGoal->name }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>

                            <!-- Metode -->
                            <td class="py-3 px-3 text-slate-300 whitespace-nowrap">
                                {{ $tx->paymentMethod?->name ?? 'Tunai' }}
                            </td>

                            <!-- Total Out -->
                            <td class="py-3 px-3 text-right font-mono font-bold whitespace-nowrap">
                                @if (in_array($tx->type->value, ['expense', 'saving_deposit']))
                                    <span class="text-rose-400">Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>

                            <!-- Total In -->
                            <td class="py-3 px-3 text-right font-mono font-bold whitespace-nowrap">
                                @if (in_array($tx->type->value, ['income', 'saving_withdraw']))
                                    <span class="text-emerald-400">Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <button @click="deleteAction = '{{ route('transactions.destroy', $tx->id) }}'; deleteMessage = 'Hapus transaksi {{ $tx->description }}?'; deleteModal = true;"
                                        class="p-1 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-10 text-center text-slate-300">
                                Tidak ada catatan transaksi.
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

</x-layouts.app>