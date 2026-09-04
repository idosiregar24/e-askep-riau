@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dashboard Administrator Akademik</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pemantauan distribusi stase, kelompok praktik, integrasi kurikulum RPS, dan kamus 3S PPNI.</p>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                Akses Administrator
            </span>
        </div>
    </div>

    <!-- 6 Metrics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="card-clinical p-4 border-l-4 border-l-blue-500">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mahasiswa</p>
            <h3 class="text-xl font-black text-slate-900 mt-1">{{ $totalMahasiswa }}</h3>
        </div>
        <div class="card-clinical p-4 border-l-4 border-l-[#008D88]">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dosen / CI</p>
            <h3 class="text-xl font-black text-slate-900 mt-1">{{ $totalDosen }}</h3>
        </div>
        <div class="card-clinical p-4 border-l-4 border-l-purple-500">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Stase Kurikulum</p>
            <h3 class="text-xl font-black text-slate-900 mt-1">{{ $totalCourses }}</h3>
        </div>
        <div class="card-clinical p-4 border-l-4 border-l-emerald-500">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kasus</p>
            <h3 class="text-xl font-black text-slate-900 mt-1">{{ $totalCareSessions }}</h3>
        </div>
        <div class="card-clinical p-4 border-l-4 border-l-amber-500">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Master SDKI</p>
            <h3 class="text-xl font-black text-slate-900 mt-1">{{ $totalSdki }}</h3>
        </div>
        <div class="card-clinical p-4 border-l-4 border-l-rose-500">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Katalog SPO</p>
            <h3 class="text-xl font-black text-slate-900 mt-1">{{ $totalSpo }}</h3>
        </div>
    </div>

    <!-- Courses Breakdown & Recent Sessions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Course Stase Breakdown -->
        <div class="card-clinical p-6">
            <h3 class="font-bold text-slate-900 text-sm mb-4">Distribusi Stase Kurikulum RPS</h3>
            <div class="space-y-3">
                @foreach($courses as $c)
                    <div class="p-3.5 rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-[#008D88]">{{ $c->code }}</span>
                            <h4 class="text-sm font-semibold text-slate-800">{{ $c->name }}</h4>
                            <p class="text-[11px] text-slate-400">{{ $c->credits }} SKS &bull; Semester {{ $c->semester }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-slate-900 font-mono">{{ $c->care_sessions_count }}</span>
                            <span class="block text-[10px] text-slate-400 uppercase">Kasus</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.courses') }}" class="text-xs font-semibold text-[#008D88] hover:underline flex items-center gap-1">
                    Kelola Semua Stase & Kurikulum &rarr;
                </a>
            </div>
        </div>

        <!-- Right: Recent Care Sessions Table -->
        <div class="lg:col-span-2 card-clinical overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200/80 bg-slate-50/50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-sm">Aktivitas Asuhan Keperawatan Terkini</h3>
                <span class="text-xs text-slate-400">8 Kasus Terbaru</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b">
                        <tr>
                            <th class="px-4 py-3">Pasien</th>
                            <th class="px-4 py-3">Stase</th>
                            <th class="px-4 py-3">Mahasiswa</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSessions as $s)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $s->patient_name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">RM: {{ $s->medical_record_no }}</div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ $s->course?->code }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $s->student?->name }}</td>
                                <td class="px-4 py-3">
                                    @if($s->status === 'approved_graded')
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px]">Terkunci Sah</span>
                                    @elseif($s->status === 'submitted')
                                        <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold text-[10px]">Antrean Telaah</span>
                                    @elseif($s->status === 'need_revision')
                                        <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 font-bold text-[10px]">Revisi</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px]">Draf</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('print.case', $s->uuid) }}" target="_blank" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-[11px]">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada kasus tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
