<x-layouts.app title="Riwayat Transaksi" header="Riwayat Transaksi Lengkap" subheader="Daftar seluruh mutasi arus kas, pengeluaran konsumsi, dan alokasi tabungan">

    <!-- Search & Filter Card -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Pencarian</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari memo / deskripsi..."
                           class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Tipe Transaksi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tipe Mutasi</label>
                    <select name="type" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Semua Tipe --</option>
                        <option value="income" {{ ($filters['type'] ?? '') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ ($filters['type'] ?? '') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="saving_deposit" {{ ($filters['type'] ?? '') === 'saving_deposit' ? 'selected' : '' }}>Setoran Tabungan</option>
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
                        Reset Filter
                    </a>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition cursor-pointer">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Transactions Master Table -->
    <x-card title="Semua Transaksi Finansial" subtitle="Total transaksi ditemukan: {{ $transactions->total() }}">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/60 text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Tipe</th>
                        <th class="py-3.5 px-4">Kategori / Target</th>
                        <th class="py-3.5 px-4">Deskripsi / Memo</th>
                        <th class="py-3.5 px-4">Metode Bayar</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4 font-mono text-slate-300">
                                {{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if ($tx->type->value === 'income')
                                    <x-badge color="emerald">Pemasukan</x-badge>
                                @elseif ($tx->type->value === 'expense')
                                    <x-badge color="rose">Pengeluaran</x-badge>
                                @elseif ($tx->type->value === 'saving_deposit')
                                    <x-badge color="amber">Alokasi Tabungan</x-badge>
                                @else
                                    <x-badge color="cyan">Penarikan Tabungan</x-badge>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
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
                            <td class="py-3.5 px-4 text-white font-medium max-w-xs truncate">
                                {{ $tx->description ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-300">
                                {{ $tx->paymentMethod?->name ?? 'Kas / Tunai' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-sm">
                                <span class="{{ $tx->type->value === 'income' ? 'text-emerald-400' : ($tx->type->value === 'expense' ? 'text-rose-400' : ($tx->type->value === 'saving_deposit' ? 'text-amber-400' : 'text-cyan-400')) }}">
                                    {{ $tx->type->value === 'income' ? '+' : ($tx->type->value === 'expense' ? '-' : '•') }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button @click="deleteAction = '{{ route('transactions.destroy', $tx->id) }}'; deleteMessage = 'Hapus riwayat transaksi Rp {{ number_format($tx->amount, 0, ',', '.') }} ({{ $tx->description }})?'; deleteModal = true;"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-300">
                                Tidak ada transaksi yang sesuai dengan filter pencarian.
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