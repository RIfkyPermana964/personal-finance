<x-layouts.app title="Target Tabungan" header="Target Tabungan" subheader="Buat dan pantau progress target keuangan dan tabungan Anda">

<div x-data="{ createModal: false, editModal: false, mutateModal: false, editData: { id: null, name: '', target_amount: '', target_date: '', status: 'active', notes: '' }, mutateData: { goal_id: null, goal_name: '', type: 'deposit' } }" class="space-y-6">

    <!-- Top Summary Banner -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <x-stat-card title="Total Tabungan Terkumpul" 
                     value="Rp {{ number_format($totalSaved, 0, ',', '.') }}" 
                     subtitle="Akumulasi seluruh target aktif" 
                     color="cyan">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Total Target Nominal" 
                     value="Rp {{ number_format($totalTarget, 0, ',', '.') }}" 
                     subtitle="Total kebutuhan dana" 
                     color="indigo">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Perencanaan Baru</p>
                <p class="text-xs text-slate-500 mt-1">Disiplin menabung untuk masa depan dan impian Anda.</p>
            </div>
            <button @click="createModal = true" class="w-full py-2.5 px-4 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow-md transition cursor-pointer mt-3 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Target Tabungan
            </button>
        </div>
    </div>

    <!-- Goals Grid -->
    <div class="space-y-4">
        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Daftar Target Tabungan</h3>

        @if ($goals->isEmpty())
            <div class="bg-white border border-slate-200/80 rounded-2xl p-12 text-center text-slate-500 space-y-3 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900">Belum Ada Target Tabungan</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Mulai susun target tabungan seperti Dana Darurat 6 bulan, Laptop Baru, atau Liburan Keluarga.</p>
                <button @click="createModal = true" class="inline-flex px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">
                    Tambah Target Pertama
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($goals as $goal)
                    <div class="bg-white border {{ $goal->status->value === 'completed' ? 'border-emerald-300' : 'border-slate-200/80' }} rounded-2xl p-5 space-y-4 shadow-xs flex flex-col justify-between transition hover:shadow-md">
                        
                        <div class="space-y-3">
                            <!-- Header & Status -->
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $goal->name }}</h4>
                                    @if ($goal->target_date)
                                        <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Deadline: {{ $goal->target_date->translatedFormat('d M Y') }}
                                        </p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $goal->status->badgeClass() }}">
                                    {{ $goal->status->label() }}
                                </span>
                            </div>

                            @if ($goal->notes)
                                <p class="text-xs text-slate-600 line-clamp-2 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                    {{ $goal->notes }}
                                </p>
                            @endif

                            <!-- Amounts & Progress -->
                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-lg font-black text-sky-700 font-mono">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                                    <span class="text-xs font-mono text-slate-500">/ Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-2.5 rounded-full bg-sky-500 transition-all duration-500" style="width: {{ $goal->progress_percentage }}%"></div>
                                </div>

                                <div class="flex justify-between text-[11px] text-slate-500">
                                    <span>Tercapai: <strong class="text-slate-900 font-semibold">{{ $goal->progress_percentage }}%</strong></span>
                                    <span>Sisa: <strong class="text-slate-700 font-mono">Rp {{ number_format($goal->remaining_amount, 0, ',', '.') }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Buttons -->
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <button @click="mutateData = { goal_id: '{{ $goal->id }}', goal_name: '{{ addslashes($goal->name) }}', type: 'deposit' }; mutateModal = true;" 
                                        class="py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Setor Dana
                                </button>
                                <button @click="mutateData = { goal_id: '{{ $goal->id }}', goal_name: '{{ addslashes($goal->name) }}', type: 'withdraw' }; mutateModal = true;" 
                                        class="py-2 px-3 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    Tarik Dana
                                </button>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-1">
                                <button @click="editData = {
                                    id: '{{ $goal->id }}',
                                    name: '{{ addslashes($goal->name) }}',
                                    target_amount: formatRupiahInput('{{ $goal->target_amount }}'),
                                    target_date: '{{ $goal->target_date ? $goal->target_date->format('Y-m-d') : '' }}',
                                    notes: '{{ addslashes($goal->notes) }}',
                                    status: '{{ $goal->status->value }}'
                                }; editModal = true;" class="text-slate-500 hover:text-indigo-600 font-medium transition cursor-pointer">
                                    Edit Detail
                                </button>
                                <button @click="$dispatch('open-delete', { action: '{{ route('saving-goals.destroy', $goal->id) }}', message: 'Hapus target tabungan {{ addslashes($goal->name) }}?' })" class="text-slate-400 hover:text-rose-600 font-medium transition cursor-pointer">
                                    Hapus Target
                                </button>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal: Buat Target Tabungan Baru -->
    <x-modal name="createModal" title="Buat Target Tabungan Baru" maxWidth="md">
        <form method="POST" action="{{ route('saving-goals.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Target *</label>
                <input type="text" name="name" required placeholder="Contoh: Dana Darurat / Beli Motor / Liburan"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Target Nominal (Rp) *</label>
                    <div class="relative rounded-xl shadow-2xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                        <input type="text" inputmode="numeric" name="target_amount" x-money required placeholder="Contoh: 10.000.000"
                               class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-sm font-semibold focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">Titik otomatis ditambahkan saat mengetik</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Deadline (Opsional)</label>
                    <input type="date" name="target_date"
                           class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-sky-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan / Strategi</label>
                <textarea name="notes" rows="2" placeholder="Contoh: Sisihkan 500rb per bulan dari gaji"
                          class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500"></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="createModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Simpan Target</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal: Mutasi Tabungan (Setor/Tarik) -->
    <x-modal name="mutateModal" title="Mutasi Tabungan" maxWidth="md">
        <form :action="'/saving-goals/' + mutateData.goal_id + '/mutate'" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="type" x-model="mutateData.type" :value="mutateData.type">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-xs text-slate-500 font-medium">Target Tabungan:</span>
                <h4 class="text-sm font-bold text-slate-900 mt-0.5" x-text="mutateData.goal_name"></h4>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5" x-text="mutateData.type === 'deposit' ? 'Nominal Setoran (Rp) *' : 'Nominal Penarikan (Rp) *'"></label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                    <input type="text" inputmode="numeric" name="amount" x-money required placeholder="Contoh: 500.000"
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-base font-semibold focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Titik otomatis ditambahkan saat mengetik</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Rekening / Dompet</label>
                    <select name="payment_method_id" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-sky-500">
                        <option value="">-- Pilih --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal *</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-sky-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan</label>
                <input type="text" name="notes" placeholder="Contoh: Alokasi gaji bulanan"
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500">
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="mutateModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" :class="mutateData.type === 'deposit' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700'" class="px-5 py-2.5 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">
                    <span x-text="mutateData.type === 'deposit' ? 'Setor Sekarang' : 'Tarik Sekarang'"></span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal: Edit Target -->
    <x-modal name="editModal" title="Edit Target Tabungan" maxWidth="md">
        <form :action="'/saving-goals/' + editData.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Target *</label>
                <input type="text" name="name" x-model="editData.name" required
                       class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-sky-500">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Target Nominal (Rp) *</label>
                    <div class="relative rounded-xl shadow-2xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-sm">Rp</div>
                        <input type="text" inputmode="numeric" name="target_amount" x-model="editData.target_amount" x-money required
                               class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-sm font-semibold focus:outline-none focus:border-sky-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Deadline</label>
                    <input type="date" name="target_date" x-model="editData.target_date"
                           class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-sky-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Status</label>
                <select name="status" x-model="editData.status" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-sky-500">
                    <option value="active">Sedang Berjalan</option>
                    <option value="completed">Tercapai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan</label>
                <textarea name="notes" x-model="editData.notes" rows="2"
                          class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-sky-500"></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>

</div>
</x-layouts.app>