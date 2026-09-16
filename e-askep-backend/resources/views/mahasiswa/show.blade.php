@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'assessment' }">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-[#008D88] flex items-center gap-1 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Kasus
            </a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $session->patient_name }}</span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('print.case', $session->uuid) }}" target="_blank" 
               class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs flex items-center gap-2 transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar Resmi
            </a>

            @if(in_array($session->status, ['draft', 'need_revision']))
                <form action="{{ route('mahasiswa.submit', $session->uuid) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengajukan berkas asuhan ini ke Dosen Pembimbing untuk ditelaah?')">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs flex items-center gap-2 cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        Ajukan Telaah ke Dosen
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Need Revision Alert (if applicable) -->
    @if($session->status === 'need_revision' && $session->review?->revision_notes)
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-300 shadow-sm flex items-start gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold shrink-0 mt-0.5">
                !
            </div>
            <div>
                <h4 class="font-bold text-amber-900 text-sm">Catatan Revisi dari Dosen Pembimbing ({{ $session->mentor?->name }}):</h4>
                <p class="text-xs text-amber-800 mt-1 font-medium italic bg-white/70 p-3 rounded-xl border border-amber-200">
                    "{{ $session->review->revision_notes }}"
                </p>
                <p class="text-[11px] text-amber-700 mt-2">Silakan perbaiki data pada tab di bawah ini, lalu klik tombol <strong>Ajukan Telaah ke Dosen</strong> kembali.</p>
            </div>
        </div>
    @endif

    <!-- Patient Header Card -->
    <div class="card-clinical p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $session->patient_name }}</h2>
                    @if($session->triage_category === 'merah')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold badge-triage-merah uppercase tracking-wide">Merah (Emergent)</span>
                    @elseif($session->triage_category === 'kuning')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold badge-triage-kuning uppercase tracking-wide">Kuning (Urgent)</span>
                    @elseif($session->triage_category === 'hijau')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold badge-triage-hijau uppercase tracking-wide">Hijau (Non-Urgent)</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold badge-triage-hitam uppercase tracking-wide">Hitam (Meninggal)</span>
                    @endif

                    @if($session->status === 'submitted')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu Telaah</span>
                    @elseif($session->status === 'need_revision')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Sedang Revisi</span>
                    @elseif($session->status === 'approved_graded')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Disetujui & Terkunci</span>
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
                        <span class="text-slate-400 block font-medium">Usia & Gender</span>
                        <span class="font-bold text-slate-800">{{ $session->age }} Th / {{ $session->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Stase Kurikulum</span>
                        <span class="font-bold text-[#008D88]">{{ $session->course?->code }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Dosen Pembimbing</span>
                        <span class="font-bold text-slate-800">{{ $session->mentor?->name }}</span>
                    </div>
                </div>
            </div>

            @if($session->review?->final_score)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center shrink-0 min-w-36">
                    <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Nilai Akhir</div>
                    <div class="text-3xl font-black text-emerald-900 font-mono mt-1">{{ number_format($session->review->final_score, 1) }}</div>
                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">Sub-CPMK Sah</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Workspace Tabs -->
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
            <span>4. Checklist SPO ({{ $session->procedureLogs->where('is_performed', true)->count() }}/{{ $session->procedureLogs->count() }})</span>
        </button>
        <button @click="activeTab = 'evaluations'" 
            :class="activeTab === 'evaluations' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
            class="px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-lg">
            <span>5. Evaluasi SBAR / SOAP</span>
        </button>
    </div>

    <!-- TAB 1: PENGKAJIAN KLINIS -->
    <div x-show="activeTab === 'assessment'" class="space-y-6">
        <div class="card-clinical p-6">
            <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#008D88]"></span>
                Isi Instrumen Pengkajian Klinis
            </h3>

            @if(in_array($session->status, ['draft', 'need_revision']))
                <form action="{{ route('mahasiswa.update-assessment', $session->uuid) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keluhan Utama <span class="text-red-500">*</span></label>
                        <textarea name="keluhan_utama" rows="2" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none">{{ $session->assessment?->assessment_payload['keluhan_utama'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Riwayat Penyakit Sekarang & Terdahulu</label>
                        <textarea name="riwayat_penyakit" rows="3" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none">{{ $session->assessment?->assessment_payload['riwayat_penyakit'] ?? '' }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Airway (Jalan Napas)</label>
                            <input type="text" name="airway" value="{{ $session->assessment?->assessment_payload['airway'] ?? 'Paten, tidak ada stridor/gurgling' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Breathing (Pernapasan)</label>
                            <input type="text" name="breathing" value="{{ $session->assessment?->assessment_payload['breathing'] ?? 'Spontan, simetris, tidak ada retraksi' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Circulation (Sirkulasi)</label>
                            <input type="text" name="circulation" value="{{ $session->assessment?->assessment_payload['circulation'] ?? 'Akral hangat, CRT < 2 detik' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Disability (Tingkat Kesadaran)</label>
                            <input type="text" name="disability" value="{{ $session->assessment?->assessment_payload['disability'] ?? 'Compos Mentis (GCS 15), pupil isokor' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Exposure / Pemeriksaan Fisik Tambahan</label>
                            <textarea name="exposure" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#008D88] focus:outline-none">{{ $session->assessment?->assessment_payload['exposure'] ?? 'Tidak ditemukan jejas mayor, suhu afebris.' }}</textarea>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="px-5 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                            Simpan Perubahan Pengkajian
                        </button>
                    </div>
                </form>
            @else
                <!-- Read-only view for locked session -->
                <div class="space-y-3 text-sm">
                    <div class="p-3.5 bg-slate-50 rounded-xl">
                        <span class="text-xs font-bold text-slate-400 block uppercase">Keluhan Utama</span>
                        <p class="text-slate-800 mt-0.5">{{ $session->assessment?->assessment_payload['keluhan_utama'] ?? '-' }}</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl">
                        <span class="text-xs font-bold text-slate-400 block uppercase">Riwayat Penyakit</span>
                        <p class="text-slate-800 mt-0.5">{{ $session->assessment?->assessment_payload['riwayat_penyakit'] ?? '-' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: RENCANA ASUHAN 3S (SDKI, SLKI, SIKI) -->
    <div x-show="activeTab === 'care_plans'" class="space-y-6">
        
        @if(in_array($session->status, ['draft', 'need_revision']))
            <!-- Form Add 3S Plan -->
            <div class="card-clinical p-6 bg-slate-50/60 border-l-4 border-l-[#008D88]">
                <h4 class="font-bold text-slate-900 text-sm mb-3">Tambah Rencana Asuhan 3S PPNI</h4>
                
                <form action="{{ route('mahasiswa.store-care-plan', $session->uuid) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">SDKI (Diagnosis) <span class="text-red-500">*</span></label>
                            <select name="master_sdki_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#008D88] bg-white">
                                @foreach($masterSdki as $sdki)
                                    <option value="{{ $sdki->id }}">{{ $sdki->code }} - {{ $sdki->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">SLKI (Luaran) <span class="text-red-500">*</span></label>
                            <select name="master_slki_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#008D88] bg-white">
                                @foreach($masterSlki as $slki)
                                    <option value="{{ $slki->id }}">{{ $slki->code }} - {{ $slki->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">SIKI (Intervensi) <span class="text-red-500">*</span></label>
                            <select name="master_siki_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#008D88] bg-white">
                                @foreach($masterSiki as $siki)
                                    <option value="{{ $siki->id }}">{{ $siki->code }} - {{ $siki->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Data Subjektif (DS)</label>
                            <textarea name="subjective_data" rows="2" placeholder="Pasien mengeluh sesak napas saat beraktivitas..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#008D88]"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Data Objektif (DO)</label>
                            <textarea name="objective_data" rows="2" placeholder="RR: 28x/m, terdengar wheezing, SpO2 94%..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#008D88]"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Prioritas Diagnosa:</label>
                            <input type="number" name="priority_order" min="1" max="10" value="{{ $session->carePlans->count() + 1 }}" class="w-16 px-2 py-1 border rounded-lg text-xs font-mono">
                        </div>

                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs cursor-pointer">
                            + Simpan Rencana 3S
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Existing 3S Plans List -->
        <div class="space-y-4">
            @forelse($session->carePlans as $plan)
                <div class="card-clinical p-6 border-l-4 border-l-[#008D88]">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#E6F5F4] text-[#008D88]">
                                Prioritas #{{ $plan->priority_order }}
                            </span>
                            <span class="font-mono text-xs font-bold text-slate-500">{{ $plan->sdki?->code }}</span>
                        </div>

                        @if(in_array($session->status, ['draft', 'need_revision']))
                            <form action="{{ route('mahasiswa.destroy-care-plan', [$session->uuid, $plan->id]) }}" method="POST" onsubmit="return confirm('Hapus rencana asuhan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>

                    <h4 class="text-base font-extrabold text-slate-900 mb-2">{{ $plan->sdki?->title }}</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs mb-3">
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100">
                            <span class="text-slate-400 font-bold block uppercase">Data Subjektif (DS)</span>
                            <p class="text-slate-800 mt-0.5">{{ $plan->subjective_data ?? '-' }}</p>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100">
                            <span class="text-slate-400 font-bold block uppercase">Data Objektif (DO)</span>
                            <p class="text-slate-800 mt-0.5">{{ $plan->objective_data ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-100 text-xs">
                        <div>
                            <span class="font-bold text-emerald-700 uppercase block mb-0.5">SLKI (Luaran)</span>
                            <p class="font-semibold text-slate-800">{{ $plan->slki?->code }} - {{ $plan->slki?->title }}</p>
                        </div>
                        <div>
                            <span class="font-bold text-blue-700 uppercase block mb-0.5">SIKI (Intervensi)</span>
                            <p class="font-semibold text-slate-800">{{ $plan->siki?->code }} - {{ $plan->siki?->title }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card-clinical p-8 text-center text-slate-400">
                    Belum ada rencana asuhan 3S yang disusun.
                </div>
            @endforelse
        </div>

    </div>

    <!-- TAB 3: PEMANTAUAN TTV -->
    <div x-show="activeTab === 'vitals'" class="space-y-6">
        @if(in_array($session->status, ['draft', 'need_revision']))
            <div class="card-clinical p-6">
                <h4 class="font-bold text-slate-900 text-sm mb-3">Tambah Pemantauan TTV Berkala</h4>
                <form action="{{ route('mahasiswa.store-vital-sign', $session->uuid) }}" method="POST" class="grid grid-cols-2 sm:grid-cols-6 gap-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">TD (mmHg)</label>
                        <input type="text" name="blood_pressure" required placeholder="120/80" class="w-full px-2.5 py-1.5 border rounded-lg text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Nadi (x/m)</label>
                        <input type="number" name="heart_rate" required placeholder="80" class="w-full px-2.5 py-1.5 border rounded-lg text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">RR (x/m)</label>
                        <input type="number" name="respiratory_rate" required placeholder="20" class="w-full px-2.5 py-1.5 border rounded-lg text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Suhu (°C)</label>
                        <input type="number" step="0.1" name="temperature" required placeholder="36.5" class="w-full px-2.5 py-1.5 border rounded-lg text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">SpO2 (%)</label>
                        <input type="number" name="oxygen_saturation" placeholder="98" class="w-full px-2.5 py-1.5 border rounded-lg text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">GCS</label>
                        <input type="text" name="gcs_score" placeholder="15 (E4V5M6)" class="w-full px-2.5 py-1.5 border rounded-lg text-xs">
                    </div>
                    <div class="col-span-2 sm:col-span-6 text-right pt-2">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#008D88] text-white text-xs font-bold shadow-xs cursor-pointer">
                            + Simpan TTV
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="card-clinical overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h4 class="font-bold text-slate-800 text-sm">Riwayat Pemantauan Vital Signs</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">TD</th>
                            <th class="px-4 py-3">Nadi</th>
                            <th class="px-4 py-3">RR</th>
                            <th class="px-4 py-3">Suhu</th>
                            <th class="px-4 py-3">SpO2</th>
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
                                <td class="px-4 py-3 text-slate-700">{{ $v->temperature }} °C</td>
                                <td class="px-4 py-3 text-emerald-700 font-bold">{{ $v->spo2 !== null && $v->spo2 !== '' ? $v->spo2 . '%' : '-' }}</td>
                                <td class="px-4 py-3 text-slate-700 font-sans">{{ $v->gcs_score ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400 font-sans">
                                    Belum ada catatan pemantauan TTV.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: CHECKLIST SPO -->
    <div x-show="activeTab === 'procedures'" class="space-y-4">
        <div class="card-clinical p-6">
            <h4 class="font-bold text-slate-900 text-sm mb-1">Checklist Tindakan Standar Prosedur Operasional (SPO)</h4>
            <p class="text-xs text-slate-500 mb-4">Centang tindakan yang telah Anda laksanakan secara mandiri pada pasien ini.</p>

            <div class="space-y-2">
                @forelse($session->procedureLogs as $log)
                    <div class="flex items-start justify-between p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all">
                        <div class="flex items-start gap-3">
                            @if(in_array($session->status, ['draft', 'need_revision']))
                                <form action="{{ route('mahasiswa.toggle-procedure', [$session->uuid, $log->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="mt-0.5 w-5 h-5 rounded border flex items-center justify-center cursor-pointer transition-all
                                        {{ $log->is_performed ? 'bg-[#008D88] border-[#008D88] text-white' : 'border-slate-300 bg-white text-transparent hover:border-[#008D88]' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                    </button>
                                </form>
                            @else
                                <span class="w-5 h-5 rounded border flex items-center justify-center {{ $log->is_performed ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-transparent' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                </span>
                            @endif

                            <div>
                                <h5 class="text-sm font-bold text-slate-900">{{ $log->procedure?->name }}</h5>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $log->procedure?->sub_cpmk_target }}</p>
                            </div>
                        </div>

                        <div>
                            @if($log->is_verified)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                    Disahkan E-Paraf
                                </span>
                            @elseif($log->is_performed)
                                <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold">
                                    Telah Dikerjakan
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-400 text-[10px]">
                                    Belum Dilakukan
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Tidak ada katalog prosedur SPO pada stase ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 5: EVALUASI SBAR / SOAP -->
    <div x-show="activeTab === 'evaluations'" class="space-y-6">
        @if(in_array($session->status, ['draft', 'need_revision']))
            <div class="card-clinical p-6">
                <h4 class="font-bold text-slate-900 text-sm mb-3">Input Catatan Perkembangan (SOAP / SBAR)</h4>
                <form action="{{ route('mahasiswa.store-handover', $session->uuid) }}" method="POST" class="space-y-3" x-data="{ format: 'SBAR' }">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Format Lembar</label>
                        <select name="format_type" x-model="format" class="px-3 py-1.5 rounded-lg border text-xs font-semibold">
                            <option value="SBAR">SBAR (Serah Terima / Handover)</option>
                            <option value="SOAP">SOAP (Catatan Perkembangan Pasien)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1" x-text="format === 'SBAR' ? 'Situation (S)' : 'Subjective (S)'"></label>
                            <textarea name="situation" rows="2" class="w-full px-3 py-2 border rounded-xl"></textarea>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1" x-text="format === 'SBAR' ? 'Background (B)' : 'Objective (O)'"></label>
                            <textarea name="background" rows="2" class="w-full px-3 py-2 border rounded-xl"></textarea>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1" x-text="format === 'SBAR' ? 'Assessment (A)' : 'Analysis (A)'"></label>
                            <textarea name="assessment" rows="2" class="w-full px-3 py-2 border rounded-xl"></textarea>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1" x-text="format === 'SBAR' ? 'Recommendation (R)' : 'Planning (P)'"></label>
                            <textarea name="recommendation" rows="2" class="w-full px-3 py-2 border rounded-xl"></textarea>
                        </div>
                    </div>

                    <div class="text-right pt-2">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#008D88] text-white text-xs font-bold cursor-pointer">
                            + Simpan Catatan Evaluasi
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="space-y-4">
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
                    Belum ada catatan evaluasi SOAP atau SBAR.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
