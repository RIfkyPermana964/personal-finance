<x-layouts.app title="Metode Pembayaran" header="Master Metode Pembayaran" subheader="Kelola rekening bank, e-wallet, kartu debit/kredit, QRIS, dan dompet kas">

<div x-data="{
    createModal: false,
    editModal: false,
    editId: null,
    editName: '',
    editType: 'bank',
    editAccountNumber: '',
    openEdit(id, name, type, account) {
        this.editId = id;
        this.editName = name;
        this.editType = type;
        this.editAccountNumber = account;
        this.editModal = true;
    }
}" class="space-y-6">

    <!-- Action Bar -->
    <div class="flex items-center justify-between p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
        <p class="text-xs text-slate-500">Total Metode Terdaftar: <strong class="text-slate-900 font-bold">{{ $methods->count() }}</strong></p>
        <button @click="createModal = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Rekening / Metode
        </button>
    </div>

    <!-- Payment Methods Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
        @foreach ($methods as $item)
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-3 shadow-xs flex flex-col justify-between group hover:border-indigo-200 transition hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">{{ $item->name }}</h4>
                            <span class="text-[11px] text-indigo-600 font-semibold">{{ $item->type->label() }}</span>
                        </div>
                    </div>

                    <!-- Tombol Edit & Hapus -->
                    <div class="flex items-center gap-1">
                        <button @click="openEdit({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->type->value }}', '{{ $item->account_number ?? '' }}')"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button @click="$dispatch('open-delete', { action: '{{ route('payment-methods.destroy', $item->id) }}', message: 'Hapus metode {{ addslashes($item->name) }}?' })"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-medium">Nomor Rekening / HP:</span>
                    <span class="font-mono text-slate-900 font-bold">{{ $item->account_number ?: '-' }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Tambah -->
    <x-modal name="createModal" title="Tambah Rekening / Metode Bayar" maxWidth="md">
        <form method="POST" action="{{ route('payment-methods.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Akun / Bank *</label>
                <input type="text" name="name" required placeholder="Contoh: BCA Tabungan / GoPay / Dompet Tunai"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe *</label>
                <select name="type" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="bank">Transfer Bank</option>
                    <option value="ewallet">E-Wallet</option>
                    <option value="cash">Tunai (Cash)</option>
                    <option value="card">Kartu Debit / Kredit</option>
                    <option value="qris">QRIS</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Rekening / No. HP (Opsional)</label>
                <input type="text" name="account_number" placeholder="Contoh: 0812xxxxxx"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 font-mono focus:outline-none focus:border-indigo-500">
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="createModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Simpan Metode</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit -->
    <x-modal name="editModal" title="Edit Metode Pembayaran" maxWidth="md">
        <form method="POST" :action="'/payment-methods/' + editId" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Akun / Bank *</label>
                <input type="text" name="name" x-model="editName" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe *</label>
                <select name="type" x-model="editType" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="bank">Transfer Bank</option>
                    <option value="ewallet">E-Wallet</option>
                    <option value="cash">Tunai (Cash)</option>
                    <option value="card">Kartu Debit / Kredit</option>
                    <option value="qris">QRIS</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Rekening / No. HP (Opsional)</label>
                <input type="text" name="account_number" x-model="editAccountNumber"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 font-mono focus:outline-none focus:border-indigo-500">
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>

</div>
</x-layouts.app>