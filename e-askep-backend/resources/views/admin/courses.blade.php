@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Katalog Stase & Kurikulum RPS</h1>
            <p class="text-sm text-slate-500 mt-0.5">Daftar mata kuliah praktik klinik keperawatan terakreditasi Poltekkes Kemenkes Riau.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($courses as $c)
            <div class="card-clinical p-6 border-t-4 border-t-[#008D88] flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#E6F5F4] text-[#008D88] font-mono">
                            {{ $c->code }}
                        </span>
                        <span class="text-xs font-bold text-slate-400">Semester {{ $c->semester }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $c->name }}</h3>
                    <p class="text-xs text-slate-600 line-clamp-3 mb-4">{{ $c->description }}</p>
                </div>

                <div class="pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-2 bg-slate-50 rounded-lg">
                        <span class="font-bold text-slate-900 block font-mono text-sm">{{ $c->credits }}</span>
                        <span class="text-[10px] text-slate-400 uppercase">SKS</span>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-lg">
                        <span class="font-bold text-slate-900 block font-mono text-sm">{{ $c->care_sessions_count }}</span>
                        <span class="text-[10px] text-slate-400 uppercase">Kasus</span>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-lg">
                        <span class="font-bold text-slate-900 block font-mono text-sm">{{ $c->spo_procedures_count }}</span>
                        <span class="text-[10px] text-slate-400 uppercase">SPO</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
