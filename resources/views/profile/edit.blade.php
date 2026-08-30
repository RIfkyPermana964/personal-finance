<x-layouts.app title="Pengaturan Profil" header="Pengaturan Akun & Keamanan" subheader="Kelola informasi identitas akun, preferensi mata uang, dan ubah kata sandi">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Update Profile Card -->
        <x-card title="Informasi Profil" subtitle="Perbarui nama, alamat email, dan kontak Anda">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Mata Uang</label>
                        <input type="text" name="currency" value="{{ old('currency', $user->currency) }}" required readonly
                               class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-800 text-slate-400 rounded-xl text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nomor HP / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08123456789"
                               class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                    </div>
                </div>

                <div class="flex justify-end pt-3">
                    <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/30 transition">
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </x-card>

        <!-- Update Password Card -->
        <x-card title="Ubah Kata Sandi" subtitle="Pastikan kata sandi menggunakan kombinasi yang aman">
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi Saat Ini *</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi Baru *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Konfirmasi Kata Sandi Baru *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div class="flex justify-end pt-3">
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </x-card>

    </div>

</x-layouts.app>