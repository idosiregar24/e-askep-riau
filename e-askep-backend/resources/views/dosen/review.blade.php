@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'assessment' }">

    <!-- Top Navigation & Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('dosen.dashboard') }}" class="hover:text-[#008D88] flex items-center gap-1 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Antrean
            </a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $session->patient_name }}</span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('print.case', $session->uuid) }}" target="_blank" 
               class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs flex items-center gap-2 transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar Standar 1:1
            </a>
        </div>
    </div>

    <!-- Header Kasus Banner -->
    <div class="card-clinical p-6 bg-white">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $session->patient_name }}</h2>
                    <!-- Triase Badge -->
                    @if($session->triage_category === 'merah')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold badge-triage-merah uppercase tracking-wide">Merah (Emergent)</span>
                    @elseif($session->triage_category === 'kuning')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold badge-triage-kuning uppercase tracking-wide">Kuning (Urgent)</span>
                    @elseif($session->triage_category === 'hijau')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold badge-triage-hijau uppercase tracking-wide">Hijau (Non-Urgent)</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold badge-triage-hitam uppercase tracking-wide">Hitam (Meninggal)</span>
                    @endif

                    <!-- Document Status Pill -->
                    @if($session->status === 'submitted')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu Telaah</span>
                    @elseif($session->status === 'need_revision')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Sedang Revisi</span>
                    @elseif($session->status === 'approved_graded')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Telah Disetujui & Terkunci</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-700">Draf</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">No. Rekam Medis</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $session->medical_record_no }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Usia & Kelamin</span>
                        <span class="font-bold text-slate-800">{{ $session->age }} Tahun / {{ $session->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Stase Kurikulum</span>
                        <span class="font-bold text-[#008D88]">{{ $session->course?->code }} - {{ $session->course?->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Mahasiswa Pengkaji</span>
                        <span class="font-bold text-slate-800">{{ $session->student?->name }} ({{ $session->student?->nim_nip }})</span>
                    </div>
                </div>
            </div>

            @if($session->review?->final_score)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center shrink-0 min-w-36">
                    <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Skor Akhir</div>
                    <div class="text-3xl font-black text-emerald-900 font-mono mt-1">{{ number_format($session->review->final_score, 1) }}</div>
                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">Rubrik Sub-CPMK</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Interactive Workspace Tabs -->
    <div class="border-b border-slate-200/80 flex items-center gap-2 overflow-x-auto text-sm font-semibold">
        <button @click="activeTab = 'assessment'" 
            :class="activeTab === 'assessment' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
            class="px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-lg">
            <span>1. Pengkajian Klinis</span>
        </button>
        <button @click="activeTab = 'care_plans'" 
            :class="activeTab === 'care_plans' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
            class="px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-lg">
            <span>2. Rencana Asuhan 3S ({{ $session->carePlans->count() }})</span>
        </button>
        <button @click="activeTab = 'vitals'" 
            :class="activeTab === 'vitals' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
            class="px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-lg">
            <span>3. Pemantauan TTV ({{ $session->vitalSigns->count() }})</span>
        </button>
        <button @click="activeTab = 'procedures'" 
            :class="activeTab === 'procedures' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
            class="px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-lg">
            <span>4. Tindakan SPO & E-Paraf</span>
        </button>
        <button @click="activeTab = 'evaluations'" 
            :class="activeTab === 'evaluations' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
            class="px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-lg">
            <span>5. Evaluasi SBAR / SOAP</span>
        </button>
    </div>

    <!-- Tab Contents & Action Panel Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Clinical Data Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- TAB 1: Pengkajian Klinis -->
            <div x-show="activeTab === 'assessment'" class="space-y-6">
                <div class="card-clinical p-6">
                    <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#008D88]"></span>
                        Data Anamnesis & Keluhan
                    </h3>
                    <div class="space-y-4 text-sm">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Keluhan Utama</span>
                            <p class="text-slate-800 font-medium">
                                {{ $session->assessment?->assessment_payload['keluhan_utama'] ?? 'Belum ada data keluhan utama.' }}
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Riwayat Penyakit</span>
                            <p class="text-slate-800">
                                {{ $session->assessment?->assessment_payload['riwayat_penyakit'] ?? 'Tidak ada riwayat penyakit dicatat.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Survei Primer / Domain Khusus -->
                <div class="card-clinical p-6">
                    <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#008D88]"></span>
                        Survei Primer ABCDE / Domain Klinis
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-3.5 rounded-xl border border-slate-200">
                            <span class="text-xs font-bold text-slate-400 uppercase">A - Airway</span>
                            <p class="text-slate-800 mt-1">{{ $session->assessment?->assessment_payload['airway'] ?? 'Paten, tidak ada sumbatan' }}</p>
                        </div>
                        <div class="p-3.5 rounded-xl border border-slate-200">
                            <span class="text-xs font-bold text-slate-400 uppercase">B - Breathing</span>
                            <p class="text-slate-800 mt-1">{{ $session->assessment?->assessment_payload['breathing'] ?? 'Spontan, simetris' }}</p>
                        </div>
                        <div class="p-3.5 rounded-xl border border-slate-200">
                            <span class="text-xs font-bold text-slate-400 uppercase">C - Circulation</span>
                            <p class="text-slate-800 mt-1">{{ $session->assessment?->assessment_payload['circulation'] ?? 'Akral hangat, CRT < 2 detik' }}</p>
                        </div>
                        <div class="p-3.5 rounded-xl border border-slate-200">
                            <span class="text-xs font-bold text-slate-400 uppercase">D - Disability</span>
                            <p class="text-slate-800 mt-1">{{ $session->assessment?->assessment_payload['disability'] ?? 'Alert (GCS 15), pupil isokor' }}</p>
                        </div>
                        <div class="p-3.5 rounded-xl border border-slate-200 sm:col-span-2">
                            <span class="text-xs font-bold text-slate-400 uppercase">E - Exposure / Suhu</span>
                            <p class="text-slate-800 mt-1">{{ $session->assessment?->assessment_payload['exposure'] ?? 'Tidak ditemukan jejas / deformitas mayor' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Rencana Asuhan 3S (SDKI, SLKI, SIKI) -->
            <div x-show="activeTab === 'care_plans'" class="space-y-4">
                @forelse($session->carePlans as $plan)
                    <div class="card-clinical p-6 border-l-4 border-l-[#008D88]">
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#E6F5F4] text-[#008D88]">
                                Prioritas #{{ $plan->priority_order }}
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-400">{{ $plan->sdki?->code }}</span>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-base mb-1">
                            {{ $plan->sdki?->title ?? 'Diagnosa Keperawatan' }}
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 my-3 text-xs">
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="font-bold text-slate-400 uppercase block mb-0.5">Data Subjektif (DS)</span>
                                <p class="text-slate-700">{{ $plan->subjective_data ?? '-' }}</p>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="font-bold text-slate-400 uppercase block mb-0.5">Data Objektif (DO)</span>
                                <p class="text-slate-700">{{ $plan->objective_data ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="font-bold text-emerald-700 uppercase block mb-1">SLKI (Luaran Keperawatan)</span>
                                <p class="font-semibold text-slate-800">{{ $plan->slki?->code }} - {{ $plan->slki?->title }}</p>
                            </div>
                            <div>
                                <span class="font-bold text-blue-700 uppercase block mb-1">SIKI (Intervensi Keperawatan)</span>
                                <p class="font-semibold text-slate-800">{{ $plan->siki?->code }} - {{ $plan->siki?->title }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card-clinical p-8 text-center text-slate-400">
                        Belum ada formulir rencana asuhan 3S yang disusun.
                    </div>
                @endforelse
            </div>

            <!-- TAB 3: Pemantauan TTV -->
            <div x-show="activeTab === 'vitals'" class="space-y-4">
                <div class="card-clinical overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h4 class="font-bold text-slate-900 text-sm">Riwayat Pemantauan Tanda-Tanda Vital Time-Series</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Waktu</th>
                                    <th class="px-4 py-3">TD (mmHg)</th>
                                    <th class="px-4 py-3">Nadi (x/m)</th>
                                    <th class="px-4 py-3">RR (x/m)</th>
                                    <th class="px-4 py-3">Suhu (°C)</th>
                                    <th class="px-4 py-3">SpO2 (%)</th>
                                    <th class="px-4 py-3">GCS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                @forelse($session->vitalSigns as $v)
                                    <tr>
                                        <td class="px-4 py-3 text-slate-500 font-sans">{{ $v->recorded_at_label }}</td>
                                        <td class="px-4 py-3 font-bold text-slate-900">{{ $v->blood_pressure }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $v->heart_rate }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $v->respiratory_rate }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $v->temperature }}</td>
                                        <td class="px-4 py-3 text-emerald-700 font-bold">{{ $v->spo2 !== null && $v->spo2 !== '' ? $v->spo2 . '%' : '-' }}</td>
                                        <td class="px-4 py-3 text-slate-700 font-sans">{{ $v->gcs_score ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-slate-400 font-sans">
                                            Belum ada pencatatan tanda-tanda vital berkala.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 4: Tindakan SPO & Batch E-Paraf -->
            <div x-show="activeTab === 'procedures'" class="space-y-4">
                <div class="card-clinical p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Logbook Prosedur SPO Terjadwal</h4>
                            <p class="text-xs text-slate-500">Pilih tindakan yang telah dipraktikkan mahasiswa untuk dibubuhi E-Paraf.</p>
                        </div>
                    </div>

                    <form action="{{ route('dosen.verify-spo', $session->uuid) }}" method="POST">
                        @csrf
                        <div class="space-y-2 mb-4">
                            @forelse($session->procedureLogs as $log)
                                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-all">
                                    <input 
                                        type="checkbox" 
                                        name="procedure_ids[]" 
                                        value="{{ $log->id }}"
                                        {{ $log->is_verified ? 'checked disabled' : ($log->is_performed ? 'checked' : '') }}
                                        class="mt-1 rounded border-slate-300 text-[#008D88] focus:ring-[#008D88]"
                                    >
                                    <div class="flex-1 text-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-800 text-sm">{{ $log->procedure?->name }}</span>
                                            @if($log->is_verified)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                                    Terverifikasi E-Paraf
                                                </span>
                                            @elseif($log->is_performed)
                                                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-[10px]">
                                                    Dilaksanakan Mhs
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px]">
                                                    Belum Dilaksanakan
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-slate-500 mt-0.5">{{ $log->procedure?->sub_cpmk_target }}</p>
                                    </div>
                                </label>
                            @empty
                                <p class="text-xs text-slate-400 py-4 text-center">Tidak ada katalog prosedur SPO pada stase ini.</p>
                            @endforelse
                        </div>

                        @if($session->status !== 'approved_graded')
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white font-semibold text-xs shadow-xs flex items-center gap-2 cursor-pointer transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Verifikasi E-Paraf Tindakan Terpilih
                            </button>
                        @endif
                    </form>
                </div>
            </div>

            <!-- TAB 5: Evaluasi SBAR & SOAP -->
            <div x-show="activeTab === 'evaluations'" class="space-y-4">
                @forelse($session->evaluationsAndHandovers as $eval)
                    <div class="card-clinical p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                Format: {{ $eval->format_type }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $eval->created_at?->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            @foreach($eval->payload as $k => $v)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="font-bold text-slate-400 uppercase block mb-1">{{ strtoupper($k) }}</span>
                                    <p class="text-slate-800">{{ $v ?: '-' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="card-clinical p-8 text-center text-slate-400">
                        Belum ada lembar evaluasi SOAP atau serah terima SBAR.
                    </div>
                @endforelse
            </div>

        </div>

        <!-- Right 1 Col: Dosen Review & Grading Actions Panel -->
        <div class="space-y-6">
            
            @if($session->status === 'approved_graded')
                <!-- Locked / Graded Ribbon Card -->
                <div class="card-clinical p-6 bg-emerald-50/60 border-emerald-200">
                    <div class="flex items-center gap-2 text-emerald-800 font-bold text-sm mb-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Berkas Asuhan Terkunci Sah
                    </div>
                    <p class="text-xs text-emerald-700 leading-relaxed">
                        Dokumen ini telah dievaluasi, disahkan dengan e-paraf digital, dan dinilai menggunakan rubrik Sub-CPMK resmi Poltekkes Kemenkes Riau.
                    </p>
                    <div class="mt-4 pt-3 border-t border-emerald-200/80 text-xs text-emerald-900 space-y-1">
                        <div><strong>Dosen Penilai:</strong> {{ $session->review?->dosen?->name }}</div>
                        <div><strong>Waktu Pengesahan:</strong> {{ $session->review?->reviewed_at?->format('d M Y, H:i') }} WIB</div>
                        @if($session->review?->revision_notes)
                            <div class="mt-2 p-2.5 rounded-lg bg-white/80 border border-emerald-200 text-xs italic">
                                "{{ $session->review->revision_notes }}"
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Review Action: Request Revision -->
                <div class="card-clinical p-6 border-l-4 border-l-amber-500">
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Minta Perbaikan / Revisi</h4>
                    <p class="text-xs text-slate-500 mb-3">Kembalikan berkas ke mahasiswa dengan instruksi koreksi spesifik.</p>
                    
                    <form action="{{ route('dosen.request-revision', $session->uuid) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea 
                                name="revision_notes" 
                                rows="3" 
                                required
                                placeholder="Tuliskan temuan atau instruksi perbaikan (misal: lengkapi data objektif diagnosa bersihan jalan napas)..."
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500"
                            ></textarea>
                        </div>
                        <button type="submit" class="w-full py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs transition-all shadow-xs">
                            Kembalikan untuk Revisi
                        </button>
                    </form>
                </div>

                <!-- Review Action: Rubrik Penilaian Sub-CPMK & Sign-Off -->
                <div class="card-clinical p-6 border-l-4 border-l-[#008D88]">
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Rubrik Penilaian Sub-CPMK</h4>
                    <p class="text-xs text-slate-500 mb-4">Input skor performa mahasiswa (0 - 100) dan sahkan berkas.</p>

                    <form action="{{ route('dosen.approve-grade', $session->uuid) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <label class="text-slate-700">1. Pengkajian Klinis (25%)</label>
                            </div>
                            <input type="number" name="score_pengkajian" min="0" max="100" step="0.5" required placeholder="85.0"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-[#008D88] focus:outline-none font-mono">
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <label class="text-slate-700">2. Penalaran 3S PPNI (25%)</label>
                            </div>
                            <input type="number" name="score_diagnosa" min="0" max="100" step="0.5" required placeholder="88.0"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-[#008D88] focus:outline-none font-mono">
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <label class="text-slate-700">3. Keterampilan SPO (30%)</label>
                            </div>
                            <input type="number" name="score_prosedur" min="0" max="100" step="0.5" required placeholder="90.0"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-[#008D88] focus:outline-none font-mono">
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <label class="text-slate-700">4. Evaluasi & SBAR (20%)</label>
                            </div>
                            <input type="number" name="score_evaluasi" min="0" max="100" step="0.5" required placeholder="85.0"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-[#008D88] focus:outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Evaluasi / Umpan Balik</label>
                            <textarea name="general_notes" rows="2" placeholder="Catatan apresiasi atau saran pengembangan..."
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-[#008D88] focus:outline-none"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer mt-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Sahkan & Bubuhkan E-Paraf
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
