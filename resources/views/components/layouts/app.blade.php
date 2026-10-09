<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 dark:bg-[#0A0F1D]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Personal Finance</title>
    
    <!-- Instant Dark Mode & Sidebar FOUC Prevention Script -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            if (localStorage.getItem('sidebar_collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 dark:bg-[#0A0F1D] text-slate-800 dark:text-slate-100 antialiased font-sans flex transition-colors duration-200" 
      x-data="{ 
          sidebarOpen: false, 
          sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
          deleteModal: false, 
          deleteAction: '', 
          deleteMessage: '',
          darkMode: document.documentElement.classList.contains('dark'),
          toggleTheme() {
              this.darkMode = !this.darkMode;
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
                  localStorage.setItem('theme', 'dark');
              } else {
                  document.documentElement.classList.remove('dark');
                  localStorage.setItem('theme', 'light');
              }
              window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: this.darkMode } }));
          },
          toggleSidebarCollapse() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
              if (this.sidebarCollapsed) {
                  document.documentElement.classList.add('sidebar-collapsed');
              } else {
                  document.documentElement.classList.remove('sidebar-collapsed');
              }
          }
      }" 
      @open-delete.window="deleteAction = $event.detail.action; deleteMessage = $event.detail.message; deleteModal = true">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-cloak 
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden transition-opacity">
    </div>

    <!-- Sidebar Navigation -->
    <aside :class="[
               sidebarOpen ? 'translate-x-0' : '-translate-x-full',
               sidebarCollapsed ? 'lg:w-20' : 'lg:w-72'
           ]" 
           class="app-sidebar fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-[#0D1424] border-r border-slate-200/80 dark:border-[#1A2438] flex flex-col transition-all duration-300 ease-in-out lg:static lg:translate-x-0 shadow-xs flex-shrink-0">
        
        <!-- Brand Logo & Minimize Toggle -->
        <div class="h-20 flex items-center border-b border-slate-100 dark:border-[#1A2438] transition-all duration-300"
             :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-6'">
            <div class="flex items-center gap-3 overflow-hidden cursor-pointer"
                 @click="if (sidebarCollapsed) toggleSidebarCollapse()"
                 :title="sidebarCollapsed ? 'Klik untuk memperluas sidebar' : ''">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-indigo-600 flex items-center justify-center shadow-md shadow-emerald-600/20 text-white font-black text-lg flex-shrink-0 transition-transform hover:scale-105">
                    PF
                </div>
                <div x-show="!sidebarCollapsed" x-cloak class="min-w-0 transition-opacity duration-200">
                    <h1 class="text-base font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-1.5 truncate">
                        Personal Finance
                    </h1>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium truncate">Financial Management</p>
                </div>
            </div>

            <!-- Minimize Button on Desktop inside Sidebar Header -->
            <button @click="toggleSidebarCollapse()" 
                    type="button"
                    x-show="!sidebarCollapsed" 
                    x-cloak
                    class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#162138] transition cursor-pointer flex-shrink-0"
                    title="Kecilkan Sidebar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto py-5 transition-all duration-300"
             :class="sidebarCollapsed ? 'px-2 space-y-4' : 'px-4 space-y-6'">
            
            <!-- Group: Utama -->
            <div>
                <p x-show="!sidebarCollapsed" x-cloak class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-opacity">Ringkasan</p>
                <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-slate-100 dark:border-[#1A2438]"></div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('dashboard') }}" 
                       :title="sidebarCollapsed ? 'Dashboard' : ''"
                       class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 dark:border dark:border-emerald-500/20 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#162138]' }}"
                       :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-cloak class="truncate">Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- Group: Perencanaan Gaji & Anggaran -->
            <div>
                <p x-show="!sidebarCollapsed" x-cloak class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-opacity">Perencanaan Gaji</p>
                <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-slate-100 dark:border-[#1A2438]"></div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('salary-allocations.index') }}" 
                       :title="sidebarCollapsed ? 'Pembagian Gaji & Saldo' : ''"
                       class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('salary-allocations.*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 dark:border dark:border-indigo-500/20 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#162138]' }}"
                       :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('salary-allocations.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-cloak class="truncate">Pembagian Gaji & Saldo</span>
                    </a>
                    <a href="{{ route('budgets.index') }}" 
                       :title="sidebarCollapsed ? 'Pagu Anggaran (Budget)' : ''"
                       class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('budgets.*') ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 dark:border dark:border-amber-500/20 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#162138]' }}"
                       :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('budgets.*') ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-cloak class="truncate">Pagu Anggaran (Budget)</span>
                    </a>
                    <a href="{{ route('saving-goals.index') }}" 
                       :title="sidebarCollapsed ? 'Target Tabungan' : ''"
                       class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('saving-goals.*') ? 'bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 dark:border dark:border-sky-500/20 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#162138]' }}"
                       :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('saving-goals.*') ? 'text-sky-600 dark:text-sky-400' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-cloak class="truncate">Target Tabungan</span>
                    </a>
                </div>
            </div>

            <!-- Group: Pinjaman & Sewa (Debt & Rent) -->
            <div>
                <p x-show="!sidebarCollapsed" x-cloak class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-opacity">Pinjaman & Sewa</p>
                <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-slate-100 dark:border-[#1A2438]"></div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('debts.index') }}" 
                       :title="sidebarCollapsed ? 'Hutang & Piutang' : ''"
                       class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('debts.*') && (!request()->has('type') || request('type') === 'all') ? 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 dark:border dark:border-rose-500/20 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#162138]' }}"
                       :class="sidebarCollapsed ? 'justify-center p-2.5' : 'justify-between px-3 py-2.5'">
                        <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('debts.*') ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Hutang & Piutang</span>
                        </div>
                    </a>

                    <!-- Sub-navigasi khusus jenis pinjaman/sewa (hanya tampil saat expanded) -->
                    <div x-show="!sidebarCollapsed" x-cloak class="pl-7 space-y-0.5 pt-0.5">
                        <a href="{{ route('debts.index', ['type' => 'receivable']) }}" 
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('debts.*') && request('type') === 'receivable' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-slate-50 dark:hover:bg-[#162138]' }}">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                            <span>Piutang (Dipinjam Orang)</span>
                        </a>
                        <a href="{{ route('debts.index', ['type' => 'debt']) }}" 
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('debts.*') && request('type') === 'debt' ? 'bg-rose-50 dark:bg-rose-500/10 text-rose-800 dark:text-rose-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-rose-700 dark:hover:text-rose-400 hover:bg-slate-50 dark:hover:bg-[#162138]' }}">
                            <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                            <span>Hutang Saya (Pinjaman)</span>
                        </a>
                        <a href="{{ route('debts.index', ['type' => 'rent']) }}" 
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('debts.*') && request('type') === 'rent' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-800 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-700 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-[#162138]' }}">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                            <span>Tagihan Sewa (Rent)</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Group: Keuangan Harian -->
            <div>
                <p x-show="!sidebarCollapsed" x-cloak class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-opacity">Transaksi Harian</p>
                <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-slate-100 dark:border-[#1A2438]"></div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('transactions.index') }}" 
                       :title="sidebarCollapsed ? 'Transaksi Terpadu' : ''"
                       class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('transactions.*') && !request()->has('type') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 dark:border dark:border-indigo-500/20 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#162138]' }}"
                       :class="sidebarCollapsed ? 'justify-center p-2.5' : 'justify-between px-3 py-2.5'">
                        <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('transactions.*') && !request()->has('type') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Transaksi Terpadu</span>
                        </div>
                    </a>

                    <!-- Sub-navigasi jenis transaksi (hanya tampil saat expanded) -->
                    <div x-show="!sidebarCollapsed" x-cloak class="pl-7 space-y-0.5 pt-0.5">
                        <a href="{{ route('transactions.index') }}" 
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('transactions.*') && !request()->has('type') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-800 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-700 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-[#162138]' }}">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                            <span>Semua Transaksi</span>
                        </a>
                        <a href="{{ route('transactions.index', ['type' => 'income']) }}" 
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('transactions.*') && request('type') === 'income' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-slate-50 dark:hover:bg-[#162138]' }}">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                            <span>Pemasukan (In)</span>
                        </a>
                        <a href="{{ route('transactions.index', ['type' => 'expense']) }}" 
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request()->routeIs('transactions.*') && request('type') === 'expense' ? 'bg-rose-50 dark:bg-rose-500/10 text-rose-800 dark:text-rose-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-rose-700 dark:hover:text-rose-400 hover:bg-slate-50 dark:hover:bg-[#162138]' }}">
                            <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                            <span>Pengeluaran (Out)</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Group: Analisis -->
            <div>
                <p x-show="!sidebarCollapsed" x-cloak class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-opacity">Laporan</p>
                <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-slate-100 dark:border-[#1A2438]"></div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('reports.index') }}" 
                       :title="sidebarCollapsed ? 'Laporan Keuangan' : ''"
                       class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('reports.*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 dark:border dark:border-indigo-500/20 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#162138]' }}"
                       :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('reports.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-cloak class="truncate">Laporan Keuangan</span>
                    </a>
                </div>
            </div>

            <!-- Group: Pengaturan & Master Data -->
            <div>
                <p x-show="!sidebarCollapsed" x-cloak class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 transition-opacity">Master Data</p>
                <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-slate-100 dark:border-[#1A2438]"></div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('categories.index') }}" 
                       :title="sidebarCollapsed ? 'Kategori Transaksi' : ''"
                       class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('categories.*') ? 'bg-slate-100 dark:bg-[#162138] text-slate-900 dark:text-slate-100 dark:border dark:border-[#1E293B] shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#162138]' }}"
                       :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('categories.*') ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-cloak class="truncate">Kategori Transaksi</span>
                    </a>
                    <a href="{{ route('payment-methods.index') }}" 
                       :title="sidebarCollapsed ? 'Metode Pembayaran' : ''"
                       class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('payment-methods.*') ? 'bg-slate-100 dark:bg-[#162138] text-slate-900 dark:text-slate-100 dark:border dark:border-[#1E293B] shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#162138]' }}"
                       :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('payment-methods.*') ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400 dark:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-cloak class="truncate">Metode Pembayaran</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Sidebar Collapse Toggle Action Rail (Desktop Footer) -->
        <div class="hidden lg:block px-3 py-2 border-t border-slate-100 dark:border-[#1A2438]">
            <button @click="toggleSidebarCollapse()" 
                    type="button"
                    class="w-full flex items-center rounded-xl text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-[#162138] transition p-2 cursor-pointer"
                    :class="sidebarCollapsed ? 'justify-center' : 'gap-3 px-3'"
                    :title="sidebarCollapsed ? 'Perluas Sidebar' : 'Kecilkan Sidebar'">
                <svg class="w-4 h-4 transition-transform duration-300 flex-shrink-0" :class="sidebarCollapsed ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak class="truncate">Kecilkan Sidebar</span>
            </button>
        </div>

        <!-- User Profile Card in Sidebar -->
        <div class="p-3 border-t border-slate-100 dark:border-[#1A2438] bg-slate-50/70 dark:bg-[#0A0F1D]/80 transition-all duration-300">
            <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
                <a href="{{ route('profile.edit') }}" 
                   :title="sidebarCollapsed ? '{{ Auth::user()->name ?? 'User' }} — Pengaturan Akun' : ''"
                   class="relative flex-shrink-0 group">
                    @if (Auth::user()?->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-[#1E293B] group-hover:ring-2 group-hover:ring-emerald-500 transition">
                    @else
                        <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/25 flex items-center justify-center font-bold text-emerald-800 dark:text-emerald-400 text-xs group-hover:ring-2 group-hover:ring-emerald-500 transition">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </a>
                <div x-show="!sidebarCollapsed" x-cloak class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                </div>
                <a x-show="!sidebarCollapsed" x-cloak href="{{ route('profile.edit') }}" title="Pengaturan Akun" class="text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-200/60 dark:hover:bg-[#162138] transition flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Top Navbar -->
        <header class="app-topbar h-20 bg-white/90 dark:bg-[#0D1424]/90 border-b border-slate-200/80 dark:border-[#1A2438] backdrop-blur-md px-4 sm:px-6 flex items-center justify-between z-30 shadow-2xs">
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Mobile Sidebar Toggle -->
                <button @click="sidebarOpen = true" class="p-2 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#162138] lg:hidden cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Desktop Sidebar Minimize Toggle Button -->
                <button @click="toggleSidebarCollapse()" 
                        type="button" 
                        class="hidden lg:flex p-2 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#162138] border border-slate-200/80 dark:border-[#1E293B] transition cursor-pointer"
                        :title="sidebarCollapsed ? 'Perluas Sidebar' : 'Kecilkan Sidebar'">
                    <svg class="w-5 h-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"/>
                    </svg>
                </button>

                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight">{{ $header ?? 'Dashboard' }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">{{ $subheader ?? 'Pencatatan dan pengelolaan keuangan pribadi' }}</p>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Theme Toggle Button (Light / Dark) -->
                <button @click="toggleTheme()" 
                        type="button" 
                        class="p-2.5 rounded-xl text-slate-600 dark:text-slate-300 bg-white dark:bg-[#121A2D] hover:bg-slate-100 dark:hover:bg-[#162138] border border-slate-200/80 dark:border-[#1E293B] transition cursor-pointer flex items-center gap-2 text-xs font-semibold shadow-2xs group"
                        :title="darkMode ? 'Beralih ke Mode Terang (Light)' : 'Beralih ke Mode Gelap (Dark)'">
                    <!-- Sun Icon (Active in Dark Mode) -->
                    <svg x-show="darkMode" class="w-4 h-4 text-amber-400 transition-transform group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <!-- Moon Icon (Active in Light Mode) -->
                    <svg x-show="!darkMode" class="w-4 h-4 text-slate-600 transition-transform group-hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <span class="hidden md:inline font-medium text-slate-700 dark:text-slate-300" x-text="darkMode ? 'Mode Gelap' : 'Mode Terang'"></span>
                </button>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" title="Keluar" class="p-2.5 rounded-xl text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 bg-white dark:bg-[#121A2D] hover:bg-rose-50 dark:hover:bg-rose-500/10 border border-slate-200 dark:border-[#1E293B] hover:border-rose-200 dark:hover:border-rose-500/25 transition flex items-center gap-1.5 text-xs font-semibold cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Scrollable Body -->
        <main id="main-content" class="app-main flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6 bg-slate-50 dark:bg-[#0A0F1D] transition-colors duration-200">
            
            <!-- Flash Success Message Toast -->
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/25 text-emerald-800 dark:text-emerald-300 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 p-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            <!-- Flash Error Message Toast -->
            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" 
                     class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/25 text-rose-800 dark:text-rose-300 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold">Terdapat kesalahan pada formulir:</p>
                                <ul class="mt-1 text-xs list-disc list-inside space-y-0.5 text-rose-700 dark:text-rose-400">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button @click="show = false" class="text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-200 p-1 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            {{ $slot }}
        </main>
    </div>

    <!-- Global Delete Confirmation Modal -->
    <div x-show="deleteModal" x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-black/80 backdrop-blur-sm">
        <div @click.away="deleteModal = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-sm bg-white dark:bg-[#121A2D] border border-slate-200/90 dark:border-[#1E293B] rounded-3xl p-6 shadow-2xl space-y-5 text-center">
            
            <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/25 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            
            <div class="space-y-2">
                <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Hapus Data Ini?</h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium" x-text="deleteMessage"></p>
                <p class="text-[11px] text-rose-500 dark:text-rose-400 font-semibold bg-rose-50/60 dark:bg-rose-950/40 py-1 px-2.5 rounded-lg inline-block">⚠️ Tindakan ini permanen dan tidak dapat dibatalkan</p>
            </div>

            <div class="grid grid-cols-2 gap-2.5 pt-2">
                <button type="button" @click="deleteModal = false" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-slate-100 dark:bg-[#0B1020] hover:bg-slate-200 dark:hover:bg-[#162138] text-slate-700 dark:text-slate-200 border border-transparent dark:border-[#1A2438] transition cursor-pointer">
                    Batal
                </button>
                <form :action="deleteAction" method="POST" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-sm hover:shadow transition cursor-pointer">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>