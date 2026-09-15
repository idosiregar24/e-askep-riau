@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kasus Asuhan Keperawatan Saya</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pendataan pasien, instrumen pengkajian klinis, integrasi 3S PPNI, dan logbook SPO.</p>
        </div>
        <div>
            <a href="{{ route('mahasiswa.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white font-bold text-sm shadow-xs flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Input Kasus Baru
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-clinical p-5 border-l-4 border-l-slate-400">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Draf Kasus</p>
            <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $totalDraft }}</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Belum diajukan</p>
        </div>
        <div class="card-clinical p-5 border-l-4 border-l-amber-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Antrean Telaah</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalSubmitted }}</h3>
            <p class="text-[11px] text-amber-600 mt-0.5">Diperiksa Dosen / CI</p>
        </div>
        <div class="card-clinical p-5 border-l-4 border-l-red-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Perlu Revisi</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalNeedRevision }}</h3>
            <p class="text-[11px] text-red-600 mt-0.5">Harap segera diperbaiki</p>
        </div>
        <div class="card-clinical p-5 border-l-4 border-l-emerald-500">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Disetujui & Dinilai</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalApproved }}</h3>
            <p class="text-[11px] text-emerald-600 mt-0.5">Tuntas & terkunci sah</p>
        </div>
    </div>

    <!-- Table of Student Cases -->
    <div class="card-clinical overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200/80 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Kasus Pasien Bimbingan</h3>
            <span class="text-xs text-slate-500 font-mono">{{ $sessions->total() }} Kasus</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-3.5">Pasien & Rekam Medis</th>
                        <th class="px-4 py-3.5">Stase / Triase</th>
                        <th class="px-4 py-3.5">Dosen Pembimbing</th>
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
                                <div class="font-medium text-slate-800">{{ $s->mentor?->name }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $s->mentor?->nim_nip }}</div>
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
                                        Terkunci Sah
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
                                    <a href="{{ route('mahasiswa.show', $s->uuid) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-semibold transition-all shadow-xs">
                                        Buka Kasus
                                    </a>
                                    <a href="{{ route('print.case', $s->uuid) }}" target="_blank"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-all" title="Cetak Dokumen">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <p class="text-sm font-medium">Anda belum membuat kasus asuhan keperawatan.</p>
                                <a href="{{ route('mahasiswa.create') }}" class="inline-block mt-3 px-4 py-2 rounded-xl bg-[#008D88] text-white text-xs font-semibold">
                                    + Input Kasus Baru Sekarang
                                </a>
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
