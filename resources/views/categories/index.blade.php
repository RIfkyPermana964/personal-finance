<x-layouts.app title="Kategori Transaksi" header="Master Kategori Transaksi" subheader="Kelola kategori pemasukan dan pengeluaran beserta subkategori hirarkis" x-data="{ tab: 'expense', createModal: false, editModal: false, editData: {} }">

    <!-- Top Action Bar & Tabs -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md">
        <div class="flex items-center gap-2 p-1 bg-slate-900 rounded-xl border border-slate-800">
            <button @click="tab = 'expense'" :class="tab === 'expense' ? 'bg-rose-600 text-white shadow-md' : 'text-slate-400 hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-bold transition">
                Kategori Pengeluaran
            </button>
            <button @click="tab = 'income'" :class="tab === 'income' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-400 hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-bold transition">
                Kategori Pemasukan
            </button>
        </div>

        <button @click="createModal = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30 transition cursor-pointer">
            + Tambah Kategori Baru
        </button>
    </div>

    <!-- Tab Content: Expense Categories (Hierarchical) -->
    <div x-show="tab === 'expense'" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach ($expenseParents as $parent)
                <div class="bg-[#111827]/90 border border-slate-800/80 rounded-2xl p-5 space-y-3 backdrop-blur-md shadow-lg">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">{{ $parent->name }}</h4>
                                <span class="text-[10px] text-slate-300">{{ $parent->user_id ? 'Kategori Kustom' : 'Sistem Bawaan' }}</span>
                            </div>
                        </div>
                        @if ($parent->user_id)
                            <button @click="deleteAction = '{{ route('categories.destroy', $parent->id) }}'; deleteMessage = 'Hapus atau nonaktifkan kategori {{ $parent->name }}?'; deleteModal = true;" class="text-slate-500 hover:text-rose-400 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        @endif
                    </div>

                    <!-- Subcategories List -->
                    <div class="space-y-1 pl-2">
                        <p class="text-[11px] font-semibold text-slate-300 mb-1">Subkategori:</p>
                        @forelse ($parent->children as $child)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/50 text-xs text-slate-300">
                                <span>↳ {{ $child->name }}</span>
                                @if ($child->user_id)
                                    <button @click="deleteAction = '{{ route('categories.destroy', $child->id) }}'; deleteMessage = 'Hapus subkategori {{ $child->name }}?'; deleteModal = true;" class="text-slate-500 hover:text-rose-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-300 italic">Belum ada subkategori.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tab Content: Income Categories -->
    <div x-show="tab === 'income'" class="space-y-4">
        <x-card title="Kategori Sumber Pemasukan">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($incomeCategories as $item)
                    <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">{{ $item->name }}</h4>
                                <span class="text-[10px] text-slate-300">{{ $item->user_id ? 'Kustom' : 'Sistem Bawaan' }}</span>
                            </div>
                        </div>
                        @if ($item->user_id)
                            <button @click="deleteAction = '{{ route('categories.destroy', $item->id) }}'; deleteMessage = 'Hapus kategori {{ $item->name }}?'; deleteModal = true;" class="text-slate-500 hover:text-rose-400 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>

    <!-- Modal Tambah Kategori -->
    <x-modal name="createModal" title="Tambah Kategori Baru">
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tipe Kategori *</label>
                <select name="type" required class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="expense">Pengeluaran</option>
                    <option value="income">Pemasukan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Kategori *</label>
                <input type="text" name="name" required placeholder="Contoh: Belanja Server / Sertifikasi Jaringan"
                       class="w-full px-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori Induk (Opsional — untuk Subkategori)</label>
                <select name="parent_id" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="">-- Tidak Ada (Kategori Utama) --</option>
                    @foreach ($expenseParents as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30">Simpan Kategori</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>