<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkas Asuhan Keperawatan — {{ $session->patient_name }} (RM: {{ $session->medical_record_no }})</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 10pt !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-serif antialiased p-4 sm:p-8">

    <!-- Print Action Floating Bar -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between p-4 bg-white rounded-2xl shadow-md border border-slate-200">
        <div class="text-sm font-sans">
            <span class="font-bold text-slate-800">Pratinjau Dokumen Cetak Standar 1:1</span>
            <span class="text-slate-400 block text-xs">Sesuai format resmi instrumen Jurusan Keperawatan Poltekkes Kemenkes Riau.</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-sans font-bold shadow-md cursor-pointer flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar Dokumen (PDF)
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-sans font-semibold cursor-pointer">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official A4 Paper Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 shadow-lg border border-slate-200 text-black">
        
        <!-- Official Kop Surat Poltekkes Kemenkes Riau -->
        <div class="border-b-4 border-double border-black pb-4 mb-6 text-center">
            <div class="flex items-center justify-center gap-5 mb-2">
                <img src="{{ asset('asset/images/kemenkes-logo.png') }}" alt="Logo Kementerian Kesehatan RI" class="h-16 w-auto object-contain">
                <div class="text-left border-l-2 border-slate-300 pl-4">
                    <h3 class="text-xs uppercase font-bold tracking-widest text-slate-700 font-sans leading-tight">Kementerian Kesehatan Republik Indonesia</h3>
                    <h2 class="text-sm uppercase font-black tracking-tight text-slate-900 font-sans leading-tight">Direktorat Jenderal Tenaga Kesehatan</h2>
                    <h1 class="text-lg uppercase font-black tracking-normal text-emerald-900 font-sans leading-tight">Politeknik Kesehatan Kemenkes Riau</h1>
                    <p class="text-xs font-bold text-slate-800 font-sans leading-tight mt-0.5">JURUSAN KEPERAWATAN — PUSAT PENDIDIKAN KLINIS TERPADU</p>
                    <p class="text-[10px] text-slate-600 font-sans">Jl. Melur No. 103, Sukajadi, Pekanbaru, Riau 28122 &bull; Laman: www.poltekkesriau.ac.id</p>
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center my-6">
            <h2 class="text-base font-black uppercase tracking-wider underline">LEMBAR PENGKAJIAN & ASUHAN KEPERAWATAN KLINIS</h2>
            <p class="text-xs font-sans text-slate-600 mt-1">Stase: {{ $session->course?->code }} - {{ $session->course?->name }}</p>
        </div>

        <!-- Identitas Pasien & Kasus Table -->
        <div class="mb-6 border border-black text-xs font-sans">
            <div class="bg-slate-100 p-2 font-bold uppercase border-b border-black">
                A. IDENTITAS PASIEN & KASUS KLINIK
            </div>
            <div class="grid grid-cols-2 p-3 gap-y-2 gap-x-4">
                <div><strong>Nama Pasien:</strong> {{ $session->patient_name }}</div>
                <div><strong>No. Rekam Medis:</strong> {{ $session->medical_record_no }}</div>
                <div><strong>Usia / Jenis Kelamin:</strong> {{ $session->age }} Tahun / {{ $session->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</div>
                <div><strong>Klasifikasi Triase:</strong> {{ strtoupper($session->triage_category) }}</div>
                <div><strong>Mahasiswa Pengkaji:</strong> {{ $session->student?->name }} ({{ $session->student?->nim_nip }})</div>
                <div><strong>Dosen Pembimbing:</strong> {{ $session->mentor?->name }} ({{ $session->mentor?->nim_nip }})</div>
            </div>
        </div>

        <!-- Bagian I: Pengkajian Klinis -->
        <div class="mb-6 border border-black text-xs font-sans">
            <div class="bg-slate-100 p-2 font-bold uppercase border-b border-black">
                B. HASIL PENGKAJIAN KLINIS KEPERAWATAN
            </div>
            <div class="p-3 space-y-3">
                <div>
                    <strong>Keluhan Utama:</strong>
                    <p class="mt-0.5 text-slate-800">{{ $session->assessment?->assessment_payload['keluhan_utama'] ?? '-' }}</p>
                </div>
                <div>
                    <strong>Riwayat Penyakit Sekarang & Terdahulu:</strong>
                    <p class="mt-0.5 text-slate-800">{{ $session->assessment?->assessment_payload['riwayat_penyakit'] ?? '-' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200">
                    <div><strong>Airway:</strong> {{ $session->assessment?->assessment_payload['airway'] ?? '-' }}</div>
                    <div><strong>Breathing:</strong> {{ $session->assessment?->assessment_payload['breathing'] ?? '-' }}</div>
                    <div><strong>Circulation:</strong> {{ $session->assessment?->assessment_payload['circulation'] ?? '-' }}</div>
                    <div><strong>Disability:</strong> {{ $session->assessment?->assessment_payload['disability'] ?? '-' }}</div>
                    <div class="col-span-2"><strong>Exposure / Pemeriksaan Fisik:</strong> {{ $session->assessment?->assessment_payload['exposure'] ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Bagian II: Rencana Asuhan 3S PPNI -->
        <div class="mb-6 border border-black text-xs font-sans">
            <div class="bg-slate-100 p-2 font-bold uppercase border-b border-black">
                C. RENCANA ASUHAN KEPERAWATAN TERINTEGRASI 3S (SDKI - SLKI - SIKI)
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-black">
                        <th class="p-2 border-r border-black w-8 text-center">No</th>
                        <th class="p-2 border-r border-black w-1/3">Diagnosis Keperawatan (SDKI) & Data</th>
                        <th class="p-2 border-r border-black w-1/3">Luaran Keperawatan (SLKI)</th>
                        <th class="p-2 w-1/3">Intervensi Keperawatan (SIKI)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($session->carePlans as $idx => $plan)
                        <tr class="border-b border-slate-300">
                            <td class="p-2 border-r border-black text-center font-bold">{{ $plan->priority_order }}</td>
                            <td class="p-2 border-r border-black">
                                <div class="font-bold text-slate-900">{{ $plan->sdki?->code }} - {{ $plan->sdki?->title }}</div>
                                <div class="mt-1 text-[11px] text-slate-600">
                                    <strong>DS:</strong> {{ $plan->subjective_data ?? '-' }}<br>
                                    <strong>DO:</strong> {{ $plan->objective_data ?? '-' }}
                                </div>
                            </td>
                            <td class="p-2 border-r border-black">
                                <div class="font-bold text-slate-900">{{ $plan->slki?->code }}</div>
                                <div class="text-[11px] text-slate-700">{{ $plan->slki?->title }}</div>
                            </td>
                            <td class="p-2">
                                <div class="font-bold text-slate-900">{{ $plan->siki?->code }}</div>
                                <div class="text-[11px] text-slate-700">{{ $plan->siki?->title }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-slate-400 italic">Belum ada formulir 3S yang disusun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bagian III: Pemantauan TTV & SPO Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- TTV -->
            <div class="border border-black text-xs font-sans">
                <div class="bg-slate-100 p-2 font-bold uppercase border-b border-black">
                    D. PEMANTAUAN TTV BERKALA
                </div>
                <table class="w-full text-center border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-black text-[10px]">
                            <th class="p-1 border-r border-black">Waktu</th>
                            <th class="p-1 border-r border-black">TD</th>
                            <th class="p-1 border-r border-black">HR</th>
                            <th class="p-1 border-r border-black">RR</th>
                            <th class="p-1 border-r border-black">Suhu</th>
                            <th class="p-1">SpO2</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($session->vitalSigns as $v)
                            <tr class="border-b border-slate-200">
                                <td class="p-1 border-r border-black">{{ $v->recorded_at?->format('H:i') }}</td>
                                <td class="p-1 border-r border-black font-bold">{{ $v->blood_pressure }}</td>
                                <td class="p-1 border-r border-black">{{ $v->heart_rate }}</td>
                                <td class="p-1 border-r border-black">{{ $v->respiratory_rate }}</td>
                                <td class="p-1 border-r border-black">{{ $v->temperature }}</td>
                                <td class="p-1">{{ $v->oxygen_saturation ?? '-' }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-2 text-center text-slate-400">Tidak ada data TTV.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- SPO Checklist -->
            <div class="border border-black text-xs font-sans">
                <div class="bg-slate-100 p-2 font-bold uppercase border-b border-black">
                    E. VERIFIKASI TINDAKAN SPO
                </div>
                <div class="p-2 space-y-1 max-h-48 overflow-y-auto">
                    @forelse($session->procedureLogs as $log)
                        <div class="flex items-center justify-between text-[11px] py-0.5 border-b border-slate-100">
                            <span>{{ $log->procedure?->name }}</span>
                            <span class="font-bold {{ $log->is_verified ? 'text-emerald-700' : ($log->is_performed ? 'text-blue-600' : 'text-slate-400') }}">
                                {{ $log->is_verified ? '[Sah E-Paraf]' : ($log->is_performed ? '[Dikerjakan]' : '[Belum]') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-slate-400 text-center py-2">Tidak ada data SPO.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Bagian IV: Lembar Pengesahan, Tanda Tangan & Skor Rubrik -->
        <div class="border border-black text-xs font-sans p-4 mt-6">
            <div class="flex items-start justify-between">
                <div>
                    <h4 class="font-bold uppercase mb-1">EVALUASI RUBRIK SUB-CPMK KLINIS:</h4>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-0.5 text-[11px] text-slate-700">
                        <div>1. Pengkajian (25%): <strong>{{ $session->review?->rubric_scores['pengkajian'] ?? '-' }}</strong></div>
                        <div>2. Penalaran 3S (25%): <strong>{{ $session->review?->rubric_scores['diagnosa'] ?? '-' }}</strong></div>
                        <div>3. Keterampilan SPO (30%): <strong>{{ $session->review?->rubric_scores['prosedur'] ?? '-' }}</strong></div>
                        <div>4. Evaluasi SBAR (20%): <strong>{{ $session->review?->rubric_scores['evaluasi'] ?? '-' }}</strong></div>
                    </div>
                    <div class="mt-2 text-sm font-bold text-slate-900">
                        NILAI AKHIR: {{ $session->review?->final_score ? number_format($session->review->final_score, 1) : 'BELUM DINILAI' }}
                    </div>
                </div>

                <div class="text-center w-56">
                    <p class="text-[11px] text-slate-600">Pekanbaru, {{ now()->format('d F Y') }}</p>
                    <p class="text-[11px] font-bold text-slate-800 mt-0.5">Dosen Pembimbing / CI</p>
                    
                    <div class="h-16 flex items-center justify-center my-1">
                        @if($session->status === 'approved_graded')
                            <div class="border border-emerald-600 px-3 py-1 rounded text-emerald-700 font-bold text-[10px] uppercase font-mono tracking-widest">
                                E-PARAF RESMI SAH<br>
                                <span class="text-[8px] text-slate-400">{{ $session->review?->reviewed_at?->format('d/m/Y H:i') }}</span>
                            </div>
                        @else
                            <span class="text-slate-300 italic text-xs">(Belum Ditandatangani)</span>
                        @endif
                    </div>

                    <p class="font-bold underline text-xs text-slate-900">{{ $session->mentor?->name }}</p>
                    <p class="text-[10px] text-slate-600 font-mono">NIP: {{ $session->mentor?->nim_nip }}</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
