<x-layouts.app title="Kategori" header="Master Kategori Transaksi" subheader="Kelola kategori dan subkategori untuk pengeluaran dan pemasukan Anda">

<div x-data="{
    tab: 'expense',
    createModal: false,
    editModal: false,
    editId: null,
    editName: '',
    editType: 'expense',
    editParentId: '',
    openEdit(id, name, type, parentId) {
        this.editId = id;
        this.editName = name;
        this.editType = type;
        this.editParentId = parentId ? String(parentId) : '';
        this.editModal = true;
    }
}" class="space-y-6">

    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
        <!-- Tab Switch -->
        <div class="flex items-center gap-1 p-1 bg-slate-100 rounded-xl border border-slate-200">
            <button @click="tab = 'expense'"
                    :class="tab === 'expense' ? 'bg-white text-rose-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="px-4 py-2 rounded-lg text-xs transition cursor-pointer">
                🏷️ Kategori Pengeluaran
            </button>
            <button @click="tab = 'income'"
                    :class="tab === 'income' ? 'bg-white text-emerald-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="px-4 py-2 rounded-lg text-xs transition cursor-pointer">
                💰 Kategori Pemasukan
            </button>
        </div>

        <button @click="createModal = true"
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kategori Baru
        </button>
    </div>

    <!-- Tab: Pengeluaran -->
    <div x-show="tab === 'expense'" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach ($expenseParents as $parent)
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-3 shadow-xs group hover:border-rose-200 transition">
                    <!-- Parent Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $parent->name }}</h4>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $parent->children->count() }} subkategori</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 opacity-60 group-hover:opacity-100 transition">
                            <button @click="openEdit({{ $parent->id }}, '{{ addslashes($parent->name) }}', 'expense', null)"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button @click="$dispatch('open-delete', { action: '{{ route('categories.destroy', $parent->id) }}', message: 'Hapus kategori {{ addslashes($parent->name) }} beserta seluruh subkategorinya?' })"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Subkategori -->
                    <div class="space-y-1.5 pl-1">
                        @forelse ($parent->children as $child)
                            <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 border border-slate-100 text-xs group/child hover:border-slate-200 transition">
                                <span class="text-slate-700 font-medium">↳ {{ $child->name }}</span>
                                <div class="flex items-center gap-1 opacity-0 group-hover/child:opacity-100 transition">
                                    <button @click="openEdit({{ $child->id }}, '{{ addslashes($child->name) }}', 'expense', {{ $child->parent_id }})"
                                            class="p-1 rounded text-slate-400 hover:text-indigo-600 cursor-pointer transition" title="Edit">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button @click="$dispatch('open-delete', { action: '{{ route('categories.destroy', $child->id) }}', message: 'Hapus subkategori {{ addslashes($child->name) }}?' })"
                                            class="p-1 rounded text-slate-400 hover:text-rose-600 cursor-pointer transition" title="Hapus">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic px-2">Belum ada subkategori.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tab: Pemasukan -->
    <div x-show="tab === 'income'" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($incomeCategories as $item)
                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 flex items-center justify-between group hover:border-emerald-200 transition shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $item->name }}</h4>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-50 group-hover:opacity-100 transition">
                        <button @click="openEdit({{ $item->id }}, '{{ addslashes($item->name) }}', 'income', null)"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button @click="$dispatch('open-delete', { action: '{{ route('categories.destroy', $item->id) }}', message: 'Hapus kategori pemasukan {{ addslashes($item->name) }}?' })"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <x-modal name="createModal" title="Tambah Kategori Baru" maxWidth="md">
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Kategori *</label>
                <select name="type" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="expense">Pengeluaran</option>
                    <option value="income">Pemasukan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kategori *</label>
                <input type="text" name="name" required placeholder="Contoh: Tagihan BPJS / Makan Siang / Bonus"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Induk <span class="text-slate-400 font-normal">(opsional — jika ini subkategori)</span></label>
                <select name="parent_id" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="">-- Tidak Ada (Kategori Utama) --</option>
                    @foreach ($expenseParents as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="createModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Simpan Kategori</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit Kategori -->
    <x-modal name="editModal" title="Edit Kategori" maxWidth="md">
        <form method="POST" :action="'/categories/' + editId" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Kategori *</label>
                <select name="type" x-model="editType" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="expense">Pengeluaran</option>
                    <option value="income">Pemasukan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kategori *</label>
                <input type="text" name="name" x-model="editName" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Induk <span class="text-slate-400 font-normal">(opsional)</span></label>
                <select name="parent_id" x-model="editParentId" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="">-- Tidak Ada (Kategori Utama) --</option>
                    @foreach ($expenseParents as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>

</div>
</x-layouts.app>