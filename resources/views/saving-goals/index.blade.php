<x-layouts.app title="Target Tabungan" header="Target & Alokasi Tabungan" subheader="Rencanakan target dana darurat, sertifikasi NOC, upgrade tools, dan impian finansial" x-data="{ createModal: false, editModal: false, mutateModal: false, editData: {}, mutateData: { type: 'deposit', goal_name: '', goal_id: '' } }">

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

        <div class="p-5 rounded-2xl bg-[#111827]/80 border border-slate-800/80 backdrop-blur-md flex flex-col justify-between">
            <div>
                <p class="text-xs text-slate-300 font-semibold uppercase">Buat Perencanaan Baru</p>
                <p class="text-xs text-slate-300 mt-1">Disiplin menabung untuk masa depan dan karir.</p>
            </div>
            <button @click="createModal = true" class="w-full py-2.5 px-4 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-cyan-600/30 transition cursor-pointer mt-3">
                + Tambah Target Tabungan
            </button>
        </div>
    </div>

    <!-- Goals Grid -->
    <div class="space-y-4">
        <h3 class="text-sm font-bold text-white tracking-wide">Daftar Target Tabungan</h3>

        @if ($goals->isEmpty())
            <div class="bg-[#111827]/80 border border-slate-800/80 rounded-2xl p-12 text-center text-slate-300 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h4 class="text-sm font-bold text-white">Belum Ada Target Tabungan</h4>
                <p class="text-xs text-slate-300 max-w-sm mx-auto">Mulai susun target tabungan seperti Dana Darurat 6 bulan, Laptop ThinkPad NOC, atau Sertifikasi Mikrotik / Cisco.</p>
                <button @click="createModal = true" class="inline-flex px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-cyan-600/30">
                    + Buat Target Pertama
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($goals as $goal)
                    <div class="bg-[#111827]/90 border {{ $goal->status->value === 'completed' ? 'border-emerald-500/40 glow-emerald' : 'border-slate-800/80' }} rounded-2xl p-5 space-y-4 backdrop-blur-md shadow-xl flex flex-col justify-between">
                        
                        <div class="space-y-3">
                            <!-- Header & Status -->
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h4 class="text-sm font-bold text-white">{{ $goal->name }}</h4>
                                    @if ($goal->target_date)
                                        <p class="text-[11px] text-slate-300 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Deadline: {{ $goal->target_date->translatedFormat('d M Y') }}
                                        </p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $goal->status->badgeClass() }}">
                                    {{ $goal->status->label() }}
                                </span>
                            </div>

                            @if ($goal->notes)
                                <p class="text-xs text-slate-300 line-clamp-2 bg-slate-900/50 p-2 rounded-lg border border-slate-800/50">
                                    {{ $goal->notes }}
                                </p>
                            @endif

                            <!-- Amounts & Progress -->
                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-lg font-black text-cyan-400 font-mono">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                                    <span class="text-xs font-mono text-slate-300">/ Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden border border-slate-700/60">
                                    <div class="h-2.5 rounded-full bg-gradient-to-r from-cyan-500 to-emerald-400 transition-all duration-500" style="width: {{ $goal->progress_percentage }}%"></div>
                                </div>

                                <div class="flex justify-between text-[11px] text-slate-300">
                                    <span>Tercapai: <strong class="text-white">{{ $goal->progress_percentage }}%</strong></span>
                                    <span>Sisa: <strong class="text-slate-300 font-mono">Rp {{ number_format($goal->remaining_amount, 0, ',', '.') }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Buttons -->
                        <div class="pt-3 border-t border-slate-800/80 space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <button @click="mutateData = { goal_id: '{{ $goal->id }}', goal_name: '{{ addslashes($goal->name) }}', type: 'deposit' }; mutateModal = true;" 
                                        class="py-2 px-3 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Setor Dana
                                </button>
                                <button @click="mutateData = { goal_id: '{{ $goal->id }}', goal_name: '{{ addslashes($goal->name) }}', type: 'withdraw' }; mutateModal = true;" 
                                        class="py-2 px-3 bg-amber-600/20 hover:bg-amber-600/30 text-amber-400 border border-amber-500/30 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    Tarik Dana
                                </button>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-1">
                                <button @click="editData = {
                                    id: '{{ $goal->id }}',
                                    name: '{{ addslashes($goal->name) }}',
                                    target_amount: '{{ $goal->target_amount }}',
                                    target_date: '{{ $goal->target_date ? $goal->target_date->format('Y-m-d') : '' }}',
                                    notes: '{{ addslashes($goal->notes) }}',
                                    status: '{{ $goal->status->value }}'
                                }; editModal = true;" class="text-slate-400 hover:text-white transition">
                                    Edit Detail
                                </button>
                                <button @click="deleteAction = '{{ route('saving-goals.destroy', $goal->id) }}'; deleteMessage = 'Hapus target tabungan {{ $goal->name }}?'; deleteModal = true;" class="text-slate-500 hover:text-rose-400 transition">
                                    Hapus Target
                                </button>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal Tambah Target Tabungan -->
    <x-modal name="createModal" title="Buat Target Tabungan Baru">
        <form method="POST" action="{{ route('saving-goals.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Target Finansial *</label>
                <input type="text" name="name" required placeholder="Contoh: Dana Darurat 6 Bulan / Sertifikasi MTCNA"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs focus:outline-none focus:border-cyan-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Target Nominal (Rp) *</label>
                    <input type="number" step="0.01" name="target_amount" required placeholder="Contoh: 10000000"
                           class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tenggat Waktu / Deadline</label>
                    <input type="date" name="target_date"
                           class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan / Rencana Strategi</label>
                <textarea name="notes" rows="2" placeholder="Contoh: Alokasi 20% gaji setiap tanggal 25 ke rekening khusus..."
                          class="w-full px-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-cyan-600/30">Simpan Target</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Setor / Tarik Tabungan (Mutasi) -->
    <x-modal name="mutateModal" title="Mutasi Tabungan">
        <form :action="'{{ url('saving-goals') }}/' + mutateData.goal_id + '/mutate'" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="type" x-model="mutateData.type">

            <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800">
                <span class="text-xs text-slate-300">Target Tabungan:</span>
                <h4 class="text-sm font-bold text-white mt-0.5" x-text="mutateData.goal_name"></h4>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5" x-text="mutateData.type === 'deposit' ? 'Nominal Setoran (Rp) *' : 'Nominal Penarikan (Rp) *'"></label>
                <input type="number" step="0.01" name="amount" required placeholder="Contoh: 500000"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-base focus:outline-none focus:border-cyan-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Metode Rekening / Dompet</label>
                    <select name="payment_method_id" class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500">
                        <option value="">-- Pilih Metode --</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tanggal Mutasi *</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan Memo</label>
                <input type="text" name="notes" placeholder="Contoh: Alokasi gaji bulan ini / Pembayaran ujian"
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500">
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="mutateModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" :class="mutateData.type === 'deposit' ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-amber-600 hover:bg-amber-500'" class="px-4 py-2 text-white rounded-xl text-xs font-bold shadow-lg">
                    <span x-text="mutateData.type === 'deposit' ? 'Konfirmasi Setoran' : 'Konfirmasi Penarikan'"></span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit Detail Target -->
    <x-modal name="editModal" title="Edit Target Tabungan">
        <form :action="'{{ url('saving-goals') }}/' + editData.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Target *</label>
                <input type="text" name="name" x-model="editData.name" required
                       class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs focus:outline-none focus:border-cyan-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Target Nominal (Rp) *</label>
                    <input type="number" step="0.01" name="target_amount" x-model="editData.target_amount" required
                           class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Deadline</label>
                    <input type="date" name="target_date" x-model="editData.target_date"
                           class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Status Target</label>
                <select name="status" x-model="editData.status" class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500">
                    <option value="active">Sedang Berjalan</option>
                    <option value="completed">Tercapai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan</label>
                <textarea name="notes" x-model="editData.notes" rows="2"
                          class="w-full px-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-cyan-600/30">Perbarui Target</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>