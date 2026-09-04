@extends('layouts.app')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header Title & Subtitle -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Meja Telaah Dosen & Clinical Instructor</h1>
            <p class="text-sm text-slate-500 mt-0.5">Antrean verifikasi asuhan keperawatan, e-paraf tindakan SPO, dan penilaian rubrik Sub-CPMK.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#E6F5F4] text-[#008D88] border border-[#008D88]/30">
                <span class="w-2 h-2 rounded-full bg-[#008D88] animate-pulse"></span>
                Sistem Aktif
            </span>
        </div>
    </div>

    <!-- 4 Clinical Stat Cards (Shadcn Floating Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Antrean Telaah -->
        <div class="card-clinical p-5 flex items-center justify-between border-l-4 border-l-[#008D88]">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Antrean Telaah</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalSubmitted }}</h3>
                <p class="text-[11px] text-[#008D88] font-medium mt-0.5">Menunggu respon Anda</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-[#E6F5F4] text-[#008D88] flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Perlu Revisi -->
        <div class="card-clinical p-5 flex items-center justify-between border-l-4 border-l-amber-500">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Perlu Revisi</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalNeedRevision }}</h3>
                <p class="text-[11px] text-amber-600 font-medium mt-0.5">Dikerjakan mahasiswa</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
        </div>

        <!-- Disetujui & Dinilai -->
        <div class="card-clinical p-5 flex items-center justify-between border-l-4 border-l-emerald-500">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tuntas Dinilai</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalApproved }}</h3>
                <p class="text-[11px] text-emerald-600 font-medium mt-0.5">Berkas terkunci sah</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Mahasiswa Bimbingan -->
        <div class="card-clinical p-5 flex items-center justify-between border-l-4 border-l-blue-500">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Mahasiswa Bimbingan</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalStudents }}</h3>
                <p class="text-[11px] text-blue-600 font-medium mt-0.5">Di stase aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="card-clinical p-4">
        <form action="{{ route('dosen.dashboard') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari nama pasien, No RM, atau nama mahasiswa..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88]"
                >
            </div>
            <div class="w-full sm:w-48">
                <select name="course_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] bg-white">
                    <option value="">Semua Stase / RPS</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->code }} - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-44">
                <select name="status" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#008D88] bg-white">
                    <option value="">Semua Status</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Antrean Telaah</option>
                    <option value="need_revision" {{ request('status') == 'need_revision' ? 'selected' : '' }}>Perlu Revisi</option>
                    <option value="approved_graded" {{ request('status') == 'approved_graded' ? 'selected' : '' }}>Disetujui / Nilai</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draf</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#008D88] hover:bg-[#00736F] text-white rounded-xl font-semibold text-sm transition-all shadow-xs">
                Filter
            </button>
            @if(request()->hasAny(['search', 'course_id', 'status']))
                <a href="{{ route('dosen.dashboard') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table of Care Sessions -->
    <div class="card-clinical overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200/80 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Berkas Asuhan Keperawatan Mahasiswa</h3>
            <span class="text-xs text-slate-500 font-mono">{{ $sessions->total() }} Kasus Ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-3.5">Pasien & No. RM</th>
                        <th class="px-4 py-3.5">Stase / Triase</th>
                        <th class="px-4 py-3.5">Mahasiswa</th>
                        <th class="px-4 py-3.5">Status Berkas</th>
                        <th class="px-4 py-3.5">Skor Akhir</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $s->patient_name }}</div>
                                <div class="text-xs text-slate-400 font-mono">RM: {{ $s->medical_record_no }} &bull; {{ $s->age }} Th / {{ $s->gender }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-xs font-semibold text-slate-700">{{ $s->course?->code }}</div>
                                <div class="mt-1">
                                    @if($s->triage_category === 'merah')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold badge-triage-merah uppercase">Emergent</span>
                                    @elseif($s->triage_category === 'kuning')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold badge-triage-kuning uppercase">Urgent</span>
                                    @elseif($s->triage_category === 'hijau')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold badge-triage-hijau uppercase">Non-Urgent</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold badge-triage-hitam uppercase">Meninggal</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-800">{{ $s->student?->name }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $s->student?->nim_nip }}</div>
                            </td>
                            <td class="px-4 py-4">
                                @if($s->status === 'submitted')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Antrean Telaah
                                    </span>
                                @elseif($s->status === 'need_revision')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                        Perlu Revisi
                                    </span>
                                @elseif($s->status === 'approved_graded')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Disetujui & Dinilai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        Draf
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 font-mono font-bold text-slate-800">
                                @if($s->review?->final_score)
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 text-xs border border-emerald-200">
                                        {{ number_format($s->review->final_score, 1) }}
                                    </span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('dosen.review', $s->uuid) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-semibold transition-all shadow-xs">
                                        Buka Telaah
                                    </a>
                                    <a href="{{ route('print.case', $s->uuid) }}" target="_blank"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-all" title="Format Cetak Standar Poltekkes Riau">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <p class="text-sm font-medium">Belum ada berkas kasus asuhan keperawatan yang masuk.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
