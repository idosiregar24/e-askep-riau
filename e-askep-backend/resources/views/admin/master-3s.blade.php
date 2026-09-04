@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kamus Standar 3S PPNI</h1>
            <p class="text-sm text-slate-500 mt-0.5">Standar Diagnosis (SDKI), Luaran (SLKI), dan Intervensi Keperawatan Indonesia (SIKI).</p>
        </div>
        <div class="flex gap-2">
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                SLKI: {{ $slkiCount }} Indikator
            </span>
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                SIKI: {{ $sikiCount }} Tindakan
            </span>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card-clinical p-4">
        <form action="{{ route('admin.master3s') }}" method="GET" class="flex gap-3">
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}"
                placeholder="Cari kode atau judul diagnosis SDKI (contoh: D.0001 atau jalan napas)..."
                class="flex-1 px-3.5 py-2 border rounded-xl text-xs focus:ring-2 focus:ring-[#008D88] focus:outline-none"
            >
            <button type="submit" class="px-4 py-2 bg-[#008D88] text-white rounded-xl text-xs font-bold shadow-xs">
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.master3s') }}" class="px-3 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- SDKI Table -->
    <div class="card-clinical overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200/80 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Katalog Standar Diagnosis Keperawatan Indonesia (SDKI)</h3>
            <span class="text-xs text-slate-400 font-mono">{{ $sdkiList->total() }} Diagnosa</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Judul Diagnosis</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Tanda & Gejala Mayor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sdkiList as $s)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3.5 font-mono font-bold text-[#008D88] whitespace-nowrap">{{ $s->code }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900 text-sm">{{ $s->title }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[10px]">
                                    {{ $s->category ?? 'Fisiologis' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                @if(!empty($s->major_signs))
                                    <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                                        @foreach(array_slice($s->major_signs, 0, 2) as $sign)
                                            <li>{{ $sign }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">Diagnosis tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sdkiList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $sdkiList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
