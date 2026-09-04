@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Kelompok Bimbingan Praktik</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pemetaan mahasiswa, dosen pembimbing klinik (CI), stase, dan wahana praktik (Rumah Sakit).</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Assign Form -->
        <div class="card-clinical p-6">
            <h3 class="font-bold text-slate-900 text-sm mb-4">Tambah Penugasan Kelompok</h3>
            <form action="{{ route('admin.groups.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kelompok</label>
                    <input type="text" name="group_name" required placeholder="Kelompok 1 - RSUD Arifin Achmad" class="w-full px-3 py-2 border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stase Kurikulum</label>
                    <select name="course_id" required class="w-full px-3 py-2 border rounded-xl text-xs bg-white">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Dosen Pembimbing / CI</label>
                    <select name="mentor_dosen_id" required class="w-full px-3 py-2 border rounded-xl text-xs bg-white">
                        @foreach($dosens as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mahasiswa Bimbingan</label>
                    <select name="student_id" required class="w-full px-3 py-2 border rounded-xl text-xs bg-white">
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nim_nip }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Wahana Praktik (Rumah Sakit)</label>
                    <input type="text" name="clinical_site" placeholder="RSUD Arifin Achmad Pekanbaru" class="w-full px-3 py-2 border rounded-xl text-xs">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white font-bold text-xs shadow-xs cursor-pointer mt-2">
                    Simpan Penugasan
                </button>
            </form>
        </div>

        <!-- Groups Table -->
        <div class="lg:col-span-2 card-clinical overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200/80 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-sm">Daftar Pemetaan Mahasiswa Bimbingan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b">
                        <tr>
                            <th class="px-4 py-3">Kelompok</th>
                            <th class="px-4 py-3">Stase</th>
                            <th class="px-4 py-3">Mahasiswa</th>
                            <th class="px-4 py-3">Dosen CI</th>
                            <th class="px-4 py-3">Wahana RS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($groups as $g)
                            <tr>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $g->group_name }}</td>
                                <td class="px-4 py-3 font-semibold text-[#008D88]">{{ $g->course?->code }}</td>
                                <td class="px-4 py-3 text-slate-800">{{ $g->student?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $g->mentor?->name }}</td>
                                <td class="px-4 py-3 text-slate-500">-</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada pemetaan kelompok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($groups->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $groups->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
