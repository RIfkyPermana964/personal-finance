<x-layouts.app title="Kategori" header="Master Kategori Transaksi" subheader="Kelola kategori dan subkategori untuk pengeluaran dan pemasukan Anda">

<div x-data="{
    tab: 'expense',
    createModal: false,
    editModal: false,
    editId: null,
    editName: '',
    editType: 'expense',
    editParentId: '',
    toast: { show: false, message: '', type: 'success' },
    showToast(message, type = 'success') {
        this.toast.message = message;
        this.toast.type = type;
        this.toast.show = true;
        clearTimeout(this._toastTimer);
        this._toastTimer = setTimeout(() => { this.toast.show = false; }, 3000);
    },
    openEdit(id, name, type, parentId) {
        this.editId = id;
        this.editName = name;
        this.editType = type;
        this.editParentId = parentId ? String(parentId) : '';
        this.editModal = true;
    }
}" 
@category-toast.window="showToast($event.detail.message, $event.detail.type)"
class="space-y-6">

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

    <!-- Drag & Drop Instruction Banner -->
    <div class="flex items-start sm:items-center gap-3 p-4 rounded-2xl bg-indigo-50/80 border border-indigo-100 text-indigo-950 text-xs shadow-2xs">
        <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center flex-shrink-0 font-bold shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 6h.01M8 12h.01M8 18h.01M16 6h.01M16 12h.01M16 18h.01"/>
            </svg>
        </div>
        <div class="flex-1 leading-relaxed">
            <span class="font-bold">Sesuaikan Urutan (Drag & Drop):</span> Tekan dan geser ikon gagang <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-indigo-200 text-indigo-700 font-bold">⋮⋮</span> pada kategori utama atau subkategori (misal: geser <em>Makanan / Kebutuhan Pokok</em> ke urutan paling atas). Urutan baru otomatis tersimpan dan menjadi prioritas teratas saat mencatat transaksi.
        </div>
    </div>

    <!-- Tab: Pengeluaran -->
    <div x-show="tab === 'expense'" class="space-y-4">
        <div id="parentCategoriesList" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach ($expenseParents as $parent)
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-3 shadow-xs group hover:border-rose-200 transition parent-category-card" data-id="{{ $parent->id }}">
                    <!-- Parent Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <!-- Drag Handle for Parent -->
                            <div class="drag-handle-parent cursor-grab active:cursor-grabbing p-1.5 -ml-1 text-slate-300 hover:text-slate-600 active:text-indigo-600 transition flex items-center justify-center rounded-lg hover:bg-slate-100 touch-none" title="Geser untuk ubah urutan kategori utama">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 6h.01M8 12h.01M8 18h.01M16 6h.01M16 12h.01M16 18h.01"/>
                                </svg>
                            </div>
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $parent->name }}</h4>
                                <span class="text-[10px] text-slate-400 font-medium subcategory-counter">{{ $parent->children->count() }} subkategori</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button @click="openEdit({{ $parent->id }}, '{{ addslashes($parent->name) }}', 'expense', null)"
                                    class="p-1.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 bg-slate-100/80 transition cursor-pointer" title="Edit Kategori">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button @click="$dispatch('open-delete', { action: '{{ route('categories.destroy', $parent->id) }}', message: 'Hapus kategori {{ addslashes($parent->name) }} beserta seluruh subkategorinya?' })"
                                    class="p-1.5 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 bg-slate-100/80 transition cursor-pointer" title="Hapus Kategori">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Subkategori Sortable Container -->
                    <div class="space-y-1.5 pl-1 subcategories-list min-h-[36px]" data-parent-id="{{ $parent->id }}">
                        @forelse ($parent->children as $child)
                            <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 border border-slate-100 text-xs hover:border-slate-300 hover:bg-slate-100/60 transition subcategory-item" data-id="{{ $child->id }}" data-parent-id="{{ $parent->id }}">
                                <div class="flex items-center gap-2">
                                    <!-- Drag Handle for Subcategory -->
                                    <div class="drag-handle-sub cursor-grab active:cursor-grabbing text-slate-300 hover:text-slate-600 active:text-indigo-600 p-1 -ml-1 rounded transition flex items-center justify-center touch-none" title="Geser urutan subkategori">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 6h.01M8 12h.01M8 18h.01M16 6h.01M16 12h.01M16 18h.01"/>
                                        </svg>
                                    </div>
                                    <span class="text-slate-700 font-medium">↳ {{ $child->name }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click="openEdit({{ $child->id }}, '{{ addslashes($child->name) }}', 'expense', {{ $child->parent_id }})"
                                            class="p-1 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-white border border-transparent hover:border-slate-200 cursor-pointer transition shadow-2xs" title="Edit Subkategori">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button @click="$dispatch('open-delete', { action: '{{ route('categories.destroy', $child->id) }}', message: 'Hapus subkategori {{ addslashes($child->name) }}?' })"
                                            class="p-1 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-white border border-transparent hover:border-slate-200 cursor-pointer transition shadow-2xs" title="Hapus Subkategori">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic px-2 py-1 empty-placeholder">Belum ada subkategori.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tab: Pemasukan -->
    <div x-show="tab === 'income'" class="space-y-4">
        <div id="incomeCategoriesList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($incomeCategories as $item)
                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 flex items-center justify-between hover:border-emerald-200 transition shadow-xs income-category-item" data-id="{{ $item->id }}">
                    <div class="flex items-center gap-3">
                        <!-- Drag Handle for Income Category -->
                        <div class="drag-handle-income cursor-grab active:cursor-grabbing text-slate-300 hover:text-slate-600 active:text-emerald-600 p-1 -ml-1 rounded transition flex items-center justify-center touch-none" title="Geser urutan kategori pemasukan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 6h.01M8 12h.01M8 18h.01M16 6h.01M16 12h.01M16 18h.01"/>
                            </svg>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $item->name }}</h4>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button @click="openEdit({{ $item->id }}, '{{ addslashes($item->name) }}', 'income', null)"
                                class="p-1.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 bg-slate-100/80 transition cursor-pointer" title="Edit Kategori">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button @click="$dispatch('open-delete', { action: '{{ route('categories.destroy', $item->id) }}', message: 'Hapus kategori pemasukan {{ addslashes($item->name) }}?' })"
                                class="p-1.5 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 bg-slate-100/80 transition cursor-pointer" title="Hapus Kategori">
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

    <!-- Floating Reorder Feedback Toast -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 transform translate-y-4 scale-95"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900/95 text-white shadow-2xl text-xs font-semibold border border-slate-700/60 backdrop-blur-md"
         style="display: none;">
        <template x-if="toast.type === 'loading'">
            <svg class="animate-spin w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </template>
        <template x-if="toast.type === 'success'">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400/50"></span>
        </template>
        <template x-if="toast.type === 'error'">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-400 shadow-sm shadow-rose-400/50"></span>
        </template>
        <span x-text="toast.message"></span>
    </div>

</div>

<!-- SortableJS Reordering Script -->
<script>
(function() {
    function notifyToast(message, type = 'success') {
        window.dispatchEvent(new CustomEvent('category-toast', {
            detail: { message, type }
        }));
    }

    async function sendReorder(items) {
        if (!items || !items.length) return;
        notifyToast('Menyimpan urutan baru...', 'loading');

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch("{{ route('categories.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ items })
            });

            const data = await res.json();
            if (data.success) {
                notifyToast('✓ Urutan kategori berhasil diperbarui!', 'success');
            } else {
                notifyToast(data.message || 'Gagal menyimpan urutan.', 'error');
            }
        } catch (err) {
            console.error('Reorder error:', err);
            notifyToast('Gagal terhubung ke server saat menyimpan urutan.', 'error');
        }
    }

    function updateSubcategoryCounters() {
        document.querySelectorAll('.parent-category-card').forEach(card => {
            const count = card.querySelectorAll('.subcategory-item').length;
            const counter = card.querySelector('.subcategory-counter');
            if (counter) counter.textContent = `${count} subkategori`;

            const list = card.querySelector('.subcategories-list');
            if (!list) return;
            const empty = list.querySelector('.empty-placeholder');
            if (count === 0 && !empty) {
                const p = document.createElement('p');
                p.className = 'text-xs text-slate-400 italic px-2 py-1 empty-placeholder';
                p.textContent = 'Belum ada subkategori.';
                list.appendChild(p);
            } else if (count > 0 && empty) {
                empty.remove();
            }
        });
    }

    function setupSortables() {
        if (typeof window.Sortable === 'undefined') {
            setTimeout(setupSortables, 50);
            return;
        }

        // 1. Parent Expense Categories Sortable
        const parentsContainer = document.getElementById('parentCategoriesList');
        if (parentsContainer) {
            new window.Sortable(parentsContainer, {
                handle: '.drag-handle-parent',
                animation: 200,
                ghostClass: 'opacity-40',
                chosenClass: 'ring-2 ring-indigo-500 rounded-2xl shadow-lg',
                onEnd: function() {
                    const cards = parentsContainer.querySelectorAll('.parent-category-card');
                    const items = Array.from(cards).map((card, idx) => ({
                        id: parseInt(card.dataset.id),
                        sort_order: idx + 1
                    }));
                    sendReorder(items);
                }
            });
        }

        // 2. Subcategories Sortables (supports reordering & moving between parents)
        const subLists = document.querySelectorAll('.subcategories-list');
        subLists.forEach(subList => {
            new window.Sortable(subList, {
                group: 'subcategories',
                handle: '.drag-handle-sub',
                animation: 200,
                ghostClass: 'opacity-40',
                chosenClass: 'ring-2 ring-indigo-500 rounded-xl shadow-md',
                onEnd: function(evt) {
                    updateSubcategoryCounters();

                    const destParentId = evt.to.dataset.parentId;
                    const destItems = Array.from(evt.to.querySelectorAll('.subcategory-item')).map((el, idx) => ({
                        id: parseInt(el.dataset.id),
                        sort_order: idx + 1,
                        parent_id: destParentId ? parseInt(destParentId) : null
                    }));

                    let payload = [...destItems];

                    if (evt.from !== evt.to) {
                        const fromParentId = evt.from.dataset.parentId;
                        const fromItems = Array.from(evt.from.querySelectorAll('.subcategory-item')).map((el, idx) => ({
                            id: parseInt(el.dataset.id),
                            sort_order: idx + 1,
                            parent_id: fromParentId ? parseInt(fromParentId) : null
                        }));
                        payload.push(...fromItems);
                    }

                    sendReorder(payload);
                }
            });
        });

        // 3. Income Categories Sortable
        const incomeContainer = document.getElementById('incomeCategoriesList');
        if (incomeContainer) {
            new window.Sortable(incomeContainer, {
                handle: '.drag-handle-income',
                animation: 200,
                ghostClass: 'opacity-40',
                chosenClass: 'ring-2 ring-emerald-500 rounded-2xl shadow-lg',
                onEnd: function() {
                    const items = Array.from(incomeContainer.querySelectorAll('.income-category-item')).map((el, idx) => ({
                        id: parseInt(el.dataset.id),
                        sort_order: idx + 1
                    }));
                    sendReorder(items);
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupSortables);
    } else {
        setupSortables();
    }
})();
</script>
</x-layouts.app>