@extends('layouts.guest')

@section('content')
<div class="max-w-md w-full">
    <!-- Floating Clean Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xl p-8">
        
        <!-- Header & Logo -->
        <div class="text-center mb-6">
            <img src="{{ asset('asset/images/kemenkes-logo.png') }}" alt="Kemenkes Poltekkes Riau" class="h-12 w-auto mx-auto mb-4 object-contain">
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun e-Askep</h2>
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

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap & Gelar</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    required 
                    placeholder="Contoh: Ahmad Fadhil Pratama"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] focus:border-transparent transition-all shadow-xs"
                >
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="nim_nip" class="block text-xs font-semibold text-slate-700 mb-1">NIM / NIP</label>
                    <input 
                        type="text" 
                        id="nim_nip" 
                        name="nim_nip" 
                        value="{{ old('nim_nip') }}"
                        required 
                        placeholder="P032414401001"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] focus:border-transparent transition-all shadow-xs font-mono"
                    >
                </div>

                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">Peran Pengguna</label>
                    <select 
                        name="role" 
                        id="role"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] focus:border-transparent transition-all shadow-xs bg-white"
                    >
                        <option value="mahasiswa" {{ old('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('role') === 'dosen' ? 'selected' : '' }}>Dosen / CI</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required 
                    placeholder="nama@poltekkes-riau.ac.id"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] focus:border-transparent transition-all shadow-xs"
                >
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Minimal 6 karakter"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] focus:border-transparent transition-all shadow-xs"
                    >
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Sandi</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                        placeholder="Ulangi sandi"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] focus:border-transparent transition-all shadow-xs"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white font-semibold text-sm shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer mt-3"
            >
                Daftar Akun Baru
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-bold text-[#008D88] hover:underline ml-1">Masuk Sekarang</a>
        </div>
    </div>
</div>
@endsection
