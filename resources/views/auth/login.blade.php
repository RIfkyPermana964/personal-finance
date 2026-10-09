<x-layouts.guest title="Masuk">
    <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-xl space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-emerald-600/20 text-white font-black text-2xl mx-auto">
                PF
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Personal Financial System</h1>
            <p class="text-xs text-slate-500">Masuk untuk mengelola keuangan dan arus kas Anda</p>
        </div>

        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tombol Masuk dengan Google Resmi -->
        <a href="{{ route('auth.google') }}" 
           class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm border border-slate-200/90 hover:border-slate-300 rounded-xl shadow-2xs hover:shadow-xs transition duration-150 cursor-pointer">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Lanjutkan dengan Akun Google</span>
        </a>

        <!-- Divider -->
        <div class="relative flex items-center justify-center">
            <div class="border-t border-slate-200 w-full"></div>
            <span class="bg-white px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 absolute">atau masuk dengan email</span>
        </div>

        <!-- Form Login Manual -->
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', 'admin@noc.id') }}" required autofocus
                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                       placeholder="nama@domain.com">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi</label>
                <input type="password" id="password" name="password" required value="password"
                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 hover:shadow-lg transition duration-150 cursor-pointer">
                Masuk ke Sistem
            </button>
        </form>

        <!-- Link Registrasi Akun Baru -->
        <div class="text-center pt-1 border-t border-slate-100">
            <p class="text-xs text-slate-500">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                    Daftar Sekarang
                </a>
            </p>
        </div>

        <!-- Credentials Helper Note for Demo -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600 space-y-1">
            <p class="font-bold text-slate-700">Akun Demo Default:</p>
            <p>Email: <code class="text-emerald-700 font-mono font-semibold">admin@noc.id</code> | Sandi: <code class="text-emerald-700 font-mono font-semibold">password</code></p>
        </div>
    </div>
</x-layouts.guest>
