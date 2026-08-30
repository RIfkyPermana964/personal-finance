<x-layouts.app title="Metode Pembayaran" header="Master Metode Pembayaran" subheader="Kelola rekening bank, e-wallet, kartu debit/kredit, QRIS, dan dompet kas" x-data="{ createModal: false }">

    <!-- Action Bar -->
    <div class="flex items-center justify-between p-4 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md">
        <p class="text-xs text-slate-300">Total Metode Terdaftar: <strong class="text-white">{{ $methods->count() }}</strong></p>
        <button @click="createModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30 transition cursor-pointer">
            + Tambah Rekening / Metode
        </button>
    </div>

    <!-- Payment Methods Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
        @foreach ($methods as $item)
            <div class="bg-[#111827]/90 border border-slate-800/80 rounded-2xl p-5 space-y-3 backdrop-blur-md shadow-lg flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">{{ $item->name }}</h4>
                            <span class="text-[11px] text-indigo-400 font-medium">{{ $item->type->label() }}</span>
                        </div>
                    </div>
                    @if ($item->user_id)
                        <button @click="deleteAction = '{{ route('payment-methods.destroy', $item->id) }}'; deleteMessage = 'Hapus atau nonaktifkan {{ $item->name }}?'; deleteModal = true;" class="text-slate-500 hover:text-rose-400 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    @endif
                </div>

                @if ($item->account_number)
                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/60 flex items-center justify-between text-xs">
                        <span class="text-slate-300">Nomor Rekening / HP:</span>
                        <span class="font-mono text-white font-semibold">{{ $item->account_number }}</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Modal Tambah Metode -->
    <x-modal name="createModal" title="Tambah Rekening / Metode Bayar">
        <form method="POST" action="{{ route('payment-methods.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Akun / Bank *</label>
                <input type="text" name="name" required placeholder="Contoh: Bank Mandiri / GoPay Operasional"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tipe Instrumen *</label>
                <select name="type" required class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="bank">Transfer Bank</option>
                    <option value="ewallet">E-Wallet</option>
                    <option value="cash">Tunai (Cash)</option>
                    <option value="card">Kartu Debit / Kredit</option>
                    <option value="qris">QRIS</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nomor Rekening / No. Dompet</label>
                <input type="text" name="account_number" placeholder="Contoh: 123-456-7890"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-indigo-500">
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30">Simpan Metode</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>