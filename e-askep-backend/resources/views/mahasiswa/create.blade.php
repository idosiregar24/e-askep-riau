@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Breadcrumb & Title -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-[#008D88] flex items-center gap-1 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Kasus Saya
        </a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Registrasi Kasus Pasien Baru</span>
    </div>

    <!-- Form Card -->
    <div class="card-clinical p-8">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Formulir Kasus Asuhan Keperawatan Baru</h2>
            <p class="text-xs text-slate-500 mt-1">Masukkan data demografis pasien dan pilih stase kurikulum yang sedang dijalani.</p>
        </div>

        <form action="{{ route('mahasiswa.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Stase & Dosen Pembimbing Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="course_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Stase / Kurikulum RPS <span class="text-red-500">*</span>
                    </label>
                    <select name="course_id" id="course_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none bg-white">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ old('course_id', $defaultCourseId) == $c->id ? 'selected' : '' }}>
                                {{ $c->code }} - {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="mentor_dosen_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Dosen Pembimbing / CI <span class="text-red-500">*</span>
                    </label>
                    <select name="mentor_dosen_id" id="mentor_dosen_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none bg-white">
                        @foreach($dosens as $d)
                            <option value="{{ $d->id }}" {{ old('mentor_dosen_id', $defaultDosenId) == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} ({{ $d->nim_nip }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Patient Name & No RM -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="patient_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Lengkap Pasien <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name') }}" required placeholder="Contoh: Ny. Siti Rahmawati"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none">
                </div>
                <div>
                    <label for="medical_record_no" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        No. Rekam Medis <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="medical_record_no" id="medical_record_no" value="{{ old('medical_record_no') }}" required placeholder="Contoh: RM-2026-001"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none font-mono">
                </div>
            </div>

            <!-- Age & Gender -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="age" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Usia (Tahun) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="age" id="age" value="{{ old('age', 35) }}" min="0" max="150" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4 pt-1">
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="gender" value="L" {{ old('gender', 'L') === 'L' ? 'checked' : '' }} class="text-[#008D88] focus:ring-[#008D88]">
                            Laki-Laki
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="gender" value="P" {{ old('gender') === 'P' ? 'checked' : '' }} class="text-[#008D88] focus:ring-[#008D88]">
                            Perempuan
                        </label>
                    </div>
                </div>
            </div>

            <!-- Triage Selection -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Klasifikasi Triase Medis (KGD / Gawat Darurat)
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <label class="border rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-50 transition-all text-center">
                        <input type="radio" name="triage_category" value="merah" class="text-red-600 focus:ring-red-500 mb-1">
                        <span class="text-xs font-bold text-red-600">MERAH</span>
                        <span class="text-[10px] text-slate-400">Emergent</span>
                    </label>
                    <label class="border rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-50 transition-all text-center">
                        <input type="radio" name="triage_category" value="kuning" class="text-amber-600 focus:ring-amber-500 mb-1">
                        <span class="text-xs font-bold text-amber-600">KUNING</span>
                        <span class="text-[10px] text-slate-400">Urgent</span>
                    </label>
                    <label class="border rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-50 transition-all text-center">
                        <input type="radio" name="triage_category" value="hijau" checked class="text-emerald-600 focus:ring-emerald-500 mb-1">
                        <span class="text-xs font-bold text-emerald-600">HIJAU</span>
                        <span class="text-[10px] text-slate-400">Non-Urgent</span>
                    </label>
                    <label class="border rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-50 transition-all text-center">
                        <input type="radio" name="triage_category" value="hitam" class="text-slate-800 focus:ring-slate-700 mb-1">
                        <span class="text-xs font-bold text-slate-800">HITAM</span>
                        <span class="text-[10px] text-slate-400">Meninggal</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('mahasiswa.dashboard') }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white font-bold text-sm shadow-xs transition-colors cursor-pointer">
                    Simpan Kasus & Lanjut Pengkajian
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
