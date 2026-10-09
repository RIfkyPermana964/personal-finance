<x-layouts.guest title="Konfigurasi Google Sign-In">
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6 max-w-xl mx-auto">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm mx-auto">
                <svg class="w-8 h-8" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Koneksi Akun Google</h1>
            <p class="text-xs text-slate-500">Kunci API Google belum terpasang di file <code class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-700 font-mono text-[11px]">.env</code></p>
        </div>

        <!-- Info Box -->
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-xs text-amber-900 space-y-1.5">
            <p class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kenapa muncul pesan ini?
            </p>
            <p class="text-[11px] leading-relaxed text-amber-800">
                Untuk terhubung langsung ke server resmi Google (Gmail), Google mewajibkan Anda mendaftarkan <strong>Client ID</strong> & <strong>Client Secret</strong> di Google Cloud Console.
            </p>
        </div>

        <!-- Section 1: Simulasi Login Cepat untuk Multi-Akun (Local Dev) -->
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Opsi 1: Coba Multi-Akun Langsung (Simulasi)</h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Instan</span>
            </div>
            <p class="text-[11px] text-slate-500">
                Pilih akun di bawah untuk mencoba sistem multi-akun sekarang juga tanpa perlu menunggu setup Google Cloud:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <!-- Akun 1 -->
                <form method="POST" action="{{ route('auth.google.dev') }}">
                    @csrf
                    <input type="hidden" name="email" value="budi.finance@gmail.com">
                    <input type="hidden" name="name" value="Budi Santoso">
                    <button type="submit" class="w-full p-3 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/40 text-left transition group cursor-pointer">
                        <p class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">Akun 1: Budi Santoso</p>
                        <p class="text-[11px] text-slate-500 font-mono">budi.finance@gmail.com</p>
                    </button>
                </form>

                <!-- Akun 2 -->
                <form method="POST" action="{{ route('auth.google.dev') }}">
                    @csrf
                    <input type="hidden" name="email" value="siti.investor@gmail.com">
                    <input type="hidden" name="name" value="Siti Rahma">
                    <button type="submit" class="w-full p-3 rounded-xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/40 text-left transition group cursor-pointer">
                        <p class="text-xs font-bold text-slate-900 group-hover:text-indigo-700">Akun 2: Siti Rahma</p>
                        <p class="text-[11px] text-slate-500 font-mono">siti.investor@gmail.com</p>
                    </button>
                </form>
            </div>

            <!-- Custom Email Form -->
            <form method="POST" action="{{ route('auth.google.dev') }}" class="pt-2 flex gap-2">
                @csrf
                <input type="email" name="email" required placeholder="Ketik email Gmail lainnya..." 
                       class="flex-1 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-emerald-500 focus:bg-white">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition cursor-pointer">
                    Masuk
                </button>
            </form>
        </div>

        <div class="border-t border-slate-100"></div>

        <!-- Section 2: Panduan Pasang Kredensial Resmi -->
        <div class="space-y-3" x-data="{ openGuide: false }">
            <button @click="openGuide = !openGuide" type="button" class="w-full flex items-center justify-between text-left text-xs font-bold text-slate-700 hover:text-indigo-600 transition cursor-pointer">
                <span>Opsi 2: Panduan Pasang Akun Google Resmi di .env (Hanya 2 Menit)</span>
                <span x-text="openGuide ? '▲ Tutup' : '▼ Lihat Cara'"></span>
            </button>

            <div x-show="openGuide" x-cloak class="space-y-3 text-xs text-slate-600 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <ol class="list-decimal list-inside space-y-2 text-[11px] leading-relaxed">
                    <li>Buka <a href="https://console.cloud.google.com/" target="_blank" class="text-indigo-600 underline font-bold">Google Cloud Console</a> dan buat project baru.</li>
                    <li>Masuk menu <strong>APIs & Services</strong> &gt; <strong>OAuth consent screen</strong>, pilih <strong>External</strong>, lalu lengkapi nama aplikasi dan email Anda.</li>
                    <li>Masuk menu <strong>Credentials</strong> &gt; klik <strong>Create Credentials</strong> &gt; <strong>OAuth client ID</strong> (pilih Web application).</li>
                    <li>Pada <strong>Authorized redirect URIs</strong>, tambahkan URL berikut:
                        <div class="mt-1 p-2 bg-slate-900 text-emerald-400 font-mono rounded-lg text-[10px] break-all select-all">
                            {{ url('/auth/google/callback') }}
                        </div>
                    </li>
                    <li>Klik <strong>Create</strong>, lalu salin Client ID & Client Secret ke file <code class="font-bold">.env</code>:
                        <pre class="mt-1 p-2 bg-slate-900 text-slate-100 font-mono rounded-lg text-[10px]">GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here</pre>
                    </li>
                </ol>
            </div>
        </div>

        <!-- Back to Login -->
        <div class="pt-2 text-center">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                &larr; Kembali ke Halaman Login Utama
            </a>
        </div>

    </div>
</x-layouts.guest>
