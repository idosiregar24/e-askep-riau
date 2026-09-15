<!-- Sidebar Modular Poltekkes Kemenkes Riau -->
<aside 
    class="w-72 bg-[#FAFAFA] border-r border-slate-200/90 flex flex-col shrink-0 min-h-screen transition-all"
    :class="{ 'fixed inset-y-0 left-0 z-50 shadow-2xl block': sidebarOpen, 'hidden md:flex': !sidebarOpen }"
>
    <!-- Brand Header -->
    <div class="p-6 border-b border-slate-200/80 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <img src="{{ asset('asset/images/kemenkes-logo.png') }}" alt="Kemenkes Poltekkes Riau" class="h-8 w-auto object-contain">
            <div class="border-l border-slate-200 pl-2.5">
                <span class="text-sm font-bold tracking-tight text-slate-900 block leading-tight">e-Askep Riau</span>
                <span class="text-[10px] font-medium text-slate-500 block">Jurusan Keperawatan</span>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">
        
        @if(Auth::user()->isDosen() || Auth::user()->isAdmin())
            <div>
                <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Meja Telaah Klinis</div>
                <nav class="space-y-1">
                    <a href="{{ route('dosen.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all
                       {{ request()->routeIs('dosen.dashboard') ? 'bg-[#E6F5F4] text-[#008D88] font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Dashboard Telaah
                    </a>
                </nav>
            </div>
        @endif

        @if(Auth::user()->isMahasiswa() || Auth::user()->isAdmin())
            <div>
                <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Asuhan Keperawatan</div>
                <nav class="space-y-1">
                    <a href="{{ route('mahasiswa.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all
                       {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-[#E6F5F4] text-[#008D88] font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Kasus Pasien Saya
                    </a>
                    <a href="{{ route('mahasiswa.create') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all
                       {{ request()->routeIs('mahasiswa.create') ? 'bg-[#E6F5F4] text-[#008D88] font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Input Kasus Baru
                    </a>
                </nav>
            </div>
        @endif

        @if(Auth::user()->isAdmin())
            <div>
                <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Administrasi Akademik</div>
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all
                       {{ request()->routeIs('admin.dashboard') ? 'bg-[#E6F5F4] text-[#008D88] font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard Admin
                    </a>
                    <a href="{{ route('admin.courses') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all
                       {{ request()->routeIs('admin.courses') ? 'bg-[#E6F5F4] text-[#008D88] font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Stase & Kurikulum
                    </a>
                    <a href="{{ route('admin.groups') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all
                       {{ request()->routeIs('admin.groups') ? 'bg-[#E6F5F4] text-[#008D88] font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Kelompok Praktik
                    </a>
                    <a href="{{ route('admin.master3s') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all
                       {{ request()->routeIs('admin.master3s') ? 'bg-[#E6F5F4] text-[#008D88] font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Kamus 3S PPNI
                    </a>
                </nav>
            </div>
        @endif

    </div>

    <!-- User Profile Footer Card -->
    <div class="p-4 border-t border-slate-200/80 bg-white">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-[#E6F5F4] text-[#008D88] flex items-center justify-center font-bold text-sm shrink-0 border border-[#008D88]/30">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-slate-400 font-mono truncate">{{ Auth::user()->email }}</div>
            </div>
        </div>
    </div>
</aside>
