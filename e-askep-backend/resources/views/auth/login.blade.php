@extends('layouts.guest')

@section('content')
<div class="max-w-md w-full" x-data="{ 
    email: '{{ old('email', 'dosen@poltekkes-riau.ac.id') }}',
    password: 'dosen123',
    setAccount(role) {
        if(role === 'dosen') {
            this.email = 'dosen@poltekkes-riau.ac.id';
            this.password = 'dosen123';
        } else if(role === 'mahasiswa') {
            this.email = 'mahasiswa@poltekkes-riau.ac.id';
            this.password = 'mhs123';
        } else if(role === 'admin') {
            this.email = 'admin@poltekkes-riau.ac.id';
            this.password = 'admin123';
        }
    }
}">
    <!-- Floating Clean Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xl p-8">
        
        <!-- Header & Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#008D88] text-white font-black text-2xl mb-3 shadow-lg shadow-[#008D88]/25">
                +
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">e-Askep Web Portal</h2>
            <p class="text-xs font-semibold text-emerald-700 tracking-wide uppercase mt-1">Politeknik Kesehatan Kemenkes Riau</p>
            <p class="text-xs text-slate-500 mt-1">Sistem Asuhan Keperawatan & Meja Telaah Klinis</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Quick Demo Switcher Tabs -->
        <div class="mb-6 p-1 bg-slate-100 rounded-xl flex gap-1 text-xs font-medium text-slate-600">
            <button type="button" @click="setAccount('dosen')" class="flex-1 py-1.5 rounded-lg transition-all"
                :class="email.includes('dosen') ? 'bg-white text-[#008D88] font-bold shadow-xs' : 'hover:text-slate-900'">
                Dosen / CI
            </button>
            <button type="button" @click="setAccount('mahasiswa')" class="flex-1 py-1.5 rounded-lg transition-all"
                :class="email.includes('mahasiswa') ? 'bg-white text-[#008D88] font-bold shadow-xs' : 'hover:text-slate-900'">
                Mahasiswa
            </button>
            <button type="button" @click="setAccount('admin')" class="flex-1 py-1.5 rounded-lg transition-all"
                :class="email.includes('admin') ? 'bg-white text-[#008D88] font-bold shadow-xs' : 'hover:text-slate-900'">
                Admin
            </button>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email / NIM / NIP</label>
                <input 
                    type="text" 
                    id="email" 
                    name="email" 
                    x-model="email"
                    required 
                    autofocus
                    placeholder="nama@poltekkes-riau.ac.id atau NIM/NIP"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] focus:border-transparent transition-all shadow-xs"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    x-model="password"
                    required
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] focus:border-transparent transition-all shadow-xs"
                >
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-slate-600 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#008D88] focus:ring-[#008D88]">
                    <span>Ingat saya</span>
                </label>
                <span class="text-slate-400">Default: dosen123 / mhs123 / admin123</span>
            </div>

            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white font-semibold text-sm shadow-md shadow-[#008D88]/25 transition-all flex items-center justify-center gap-2 cursor-pointer mt-2"
            >
                Masuk ke Sistem
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-400">
            Jurusan Keperawatan — Poltekkes Kemenkes Riau &copy; {{ date('Y') }}
        </div>
    </div>
</div>
@endsection
