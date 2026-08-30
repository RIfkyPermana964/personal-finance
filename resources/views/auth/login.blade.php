<x-layouts.guest title="Masuk">
    <div class="bg-[#111827]/90 border border-slate-800/90 rounded-3xl p-8 backdrop-blur-xl shadow-2xl space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-indigo-600 flex items-center justify-center shadow-xl shadow-emerald-500/25 text-white font-black text-2xl mx-auto">
                PF
            </div>
            <h1 class="text-xl font-bold text-white tracking-tight">Personal Financial System</h1>
            <p class="text-xs text-slate-300">Masuk untuk mengelola keuangan dan target tabungan Anda</p>
        </div>

        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email</label>
                <div class="relative">
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@noc.id') }}" required autofocus
                           class="w-full px-4 py-3 bg-slate-900/90 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                           placeholder="nama@domain.com">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required value="password"
                           class="w-full px-4 py-3 bg-slate-900/90 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-800 text-emerald-600 focus:ring-emerald-500">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition duration-150 cursor-pointer">
                Masuk ke Sistem
            </button>
        </form>

        <!-- Credentials Helper Note for Demo -->
        <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800/80 text-[11px] text-slate-300 space-y-1">
            <p class="font-semibold text-slate-300">Default Demo Credentials:</p>
            <p>Email: <code class="text-emerald-400 font-mono">admin@noc.id</code> | Sandi: <code class="text-emerald-400 font-mono">password</code></p>
        </div>
    </div>
</x-layouts.guest>