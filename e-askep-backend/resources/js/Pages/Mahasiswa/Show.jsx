import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import {
    ArrowLeft, Printer, Send, AlertTriangle, CheckCircle2,
    Activity, ClipboardList, Stethoscope, HeartPulse, FileSpreadsheet,
    Clock, Plus, Trash2, Check, ShieldAlert, FileText, UserCheck, ChevronRight
} from 'lucide-react';
import { statusLabel, triageLabel, formatDate, formatDateTime } from '@/utils';

export default function MahasiswaShow({ session, masterSdki = [], masterSlki = [], masterSiki = [] }) {
    const { auth } = usePage().props;
    const [activeTab, setActiveTab] = useState('assessment');
    const [showAddCarePlan, setShowAddCarePlan] = useState(false);
    const [showAddVital, setShowAddVital] = useState(false);
    const [showAddHandover, setShowAddHandover] = useState(false);
    const [handoverFormat, setHandoverFormat] = useState('SBAR');

    const isLocked = session.status === 'approved_graded' || session.status === 'submitted';
    const courseCode = (session.course?.code || '').toUpperCase();
    const courseName = (session.course?.name || '').toUpperCase();

    // Determine Stage Type
    const stageType = session.assessment?.stage_type || (
        courseCode.includes('WAT5.31') || courseName.includes('KGD') || courseName.includes('DARURAT') ? 'kgd' :
        courseCode.includes('WAT5.24') || courseName.includes('KMB') || courseName.includes('BEDAH') ? 'kmb' : 'kdm'
    );

    const initialPayload = session.assessment?.assessment_payload || {};

    // Assessment Form State
    const { data: assessData, setData: setAssessData, post: postAssess, processing: assessProcessing } = useForm({
        stage_type: stageType,
        payload: {
            keluhan_utama: initialPayload.keluhan_utama || '',
            riwayat_penyakit: initialPayload.riwayat_penyakit || '',
            // KGD fields
            cara_datang: initialPayload.cara_datang || 'Ambulans',
            waktu_kejadian: initialPayload.waktu_kejadian || '',
            airway_status: initialPayload.airway_status || 'Paten',
            airway_obstruksi: initialPayload.airway_obstruksi || 'Tidak Ada',
            airway_suara: initialPayload.airway_suara || 'Bersih',
            cervical_collar: initialPayload.cervical_collar || 'Tidak',
            breathing_rr: initialPayload.breathing_rr || '20',
            breathing_pola: initialPayload.breathing_pola || 'Reguler / Normal',
            breathing_retraksi: initialPayload.breathing_retraksi || 'Tidak Ada',
            breathing_suara: initialPayload.breathing_suara || 'Vesikuler',
            breathing_spo2: initialPayload.breathing_spo2 || '98',
            breathing_o2: initialPayload.breathing_o2 || 'Nasal Canul 3 Lpm',
            circulation_nadi: initialPayload.circulation_nadi || '84',
            circulation_kualitas: initialPayload.circulation_kualitas || 'Kuat & Reguler',
            circulation_td: initialPayload.circulation_td || '120/80',
            circulation_akral: initialPayload.circulation_akral || 'Hangat, Kering, Merah',
            circulation_crt: initialPayload.circulation_crt || '< 2 detik',
            circulation_sianosis: initialPayload.circulation_sianosis || 'Tidak Ada',
            circulation_perdarahan: initialPayload.circulation_perdarahan || 'Tidak Ada',
            circulation_iv: initialPayload.circulation_iv || 'IV Line 18G Vena Fossa Kubiti Ka RL 20 tpm',
            disability_gcs_e: initialPayload.disability_gcs_e || '4',
            disability_gcs_m: initialPayload.disability_gcs_m || '6',
            disability_gcs_v: initialPayload.disability_gcs_v || '5',
            disability_kesadaran: initialPayload.disability_kesadaran || 'Compos Mentis',
            disability_pupil: initialPayload.disability_pupil || 'Isokor, 3mm / 3mm, RC (+/+)',
            exposure_suhu: initialPayload.exposure_suhu || '36.7',
            exposure_jejas: initialPayload.exposure_jejas || 'Tidak ditemukan jejas mayor / fraktur terbuka',
            exposure_hipotermia: initialPayload.exposure_hipotermia || 'Pemberian selimut hangat',
            ample_a: initialPayload.ample_a || 'Tidak ada riwayat alergi obat/makanan',
            ample_m: initialPayload.ample_m || 'Tidak sedang mengonsumsi obat rutin',
            ample_p: initialPayload.ample_p || 'Hipertensi terkontrol 2 tahun',
            ample_l: initialPayload.ample_l || 'Makan nasi 3 jam sebelum kejadian',
            ample_e: initialPayload.ample_e || 'Onset keluhan mendadak saat beraktivitas',
            head_to_toe: initialPayload.head_to_toe || 'Kepala: Mesosefal, konjungtiva ananemis. Leher: JVP 5-2 cmH2O. Thorax: Simetris, cor S1-S2 murni. Abdomen: Supel, BU normal. Ekstremitas: Akral hangat, edema (-/-).',

            // KDM 9 Henderson Domains
            kdm_oksigenasi: initialPayload.kdm_oksigenasi || 'Pernapasan spontan adekuat, tidak sesak, tidak ada batuk atau sekret.',
            kdm_nutrisi_bb: initialPayload.kdm_nutrisi_bb || '60',
            kdm_nutrisi_tb: initialPayload.kdm_nutrisi_tb || '165',
            kdm_nutrisi_diet: initialPayload.kdm_nutrisi_diet || 'Diet makanan lunak / MB TKTP 2100 kkal',
            kdm_nutrisi_keluhan: initialPayload.kdm_nutrisi_keluhan || 'Nafsu makan baik, tidak ada mual atau muntah, bising usus 12x/m.',
            kdm_cairan_intake: initialPayload.kdm_cairan_intake || '2200 cc/24j (minum + infus)',
            kdm_cairan_output: initialPayload.kdm_cairan_output || '1800 cc/24j (urin + IWL)',
            kdm_cairan_balance: initialPayload.kdm_cairan_balance || '+400 cc/24j, turgor kulit elastis, mukosa lembap',
            kdm_eliminasi_bak: initialPayload.kdm_eliminasi_bak || 'Urin jernih kuning 1500 cc/24j, spontan tanpa kateter',
            kdm_eliminasi_bab: initialPayload.kdm_eliminasi_bab || '1x sehari, konsistensi lunak, warna kecoklatan, konstipasi (-)',
            kdm_aktivitas_barthel: initialPayload.kdm_aktivitas_barthel || 'Skor Barthel 85 (Ketergantungan Ringan), ambulasi mandiri',
            kdm_tidur: initialPayload.kdm_tidur || 'Tidur 6-7 jam/malam, rasa segar saat bangun',
            kdm_hygiene: initialPayload.kdm_hygiene || 'Mandi 2x/hari, gigi bersih, Skala Braden 19 (Risiko Rendah Dekubitus)',
            kdm_termoregulasi: initialPayload.kdm_termoregulasi || 'Suhu aksila 36.8°C, tidak menggigil, akral hangat',
            kdm_nyeri_skala: initialPayload.kdm_nyeri_skala || '3',
            kdm_nyeri_pqrst: initialPayload.kdm_nyeri_pqrst || 'P: saat bergerak aktif, Q: tumpul / linu, R: regio abdomen bawah, S: skala 3 (ringan), T: hilang timbul',
            kdm_psikososial: initialPayload.kdm_psikososial || 'Kecemasan ringan terhadap prosedur, kooperatif, rutin beribadah di tempat tidur, dukungan keluarga baik.',
            kdm_enam_benar_obat: initialPayload.kdm_enam_benar_obat || 'Telah diterapkan 6 Benar: Benar Pasien, Obat, Dosis, Rute, Waktu, dan Dokumentasi.',

            // KMB 9 Systems & Perioperatif
            kmb_b1_breathing: initialPayload.kmb_b1_breathing || 'Inspeksi dada simetris, palpasi fremitus seimbang, perkusi sonor, auskultasi vesikuler tanpa ronkhi/wheezing.',
            kmb_b2_blood: initialPayload.kmb_b2_blood || 'TD 125/80 mmHg, HR 82 bpm, BJ I-II reguler murni, murmur (-), gallop (-), akral hangat, CRT < 2 detik.',
            kmb_b3_brain: initialPayload.kmb_b3_brain || 'GCS 15 (E4M6V5), reflek fisiologis patella (+/+), kaku kuduk (-), fungsi sensorik & motorik baik.',
            kmb_b4_bladder: initialPayload.kmb_b4_bladder || 'Urin spontan 1400 cc/24j, kuning terang, nyeri berkemih (-), distensi vesika urinaria (-).',
            kmb_b5_bowel: initialPayload.kmb_b5_bowel || 'Abdomen simetris, bising usus 10x/m normoaktif, nyeri tekan (-), hepar/lien tidak teraba membesar.',
            kmb_b6_bone: initialPayload.kmb_b6_bone || 'Kekuatan otot 5/5 pada keempat ekstremitas, ROM bebas aktif, fraktur (-), lesi kulit (-).',
            kmb_endokrin: initialPayload.kmb_endokrin || 'Tidak ada pembesaran kelenjar tiroid, tremor (-), GDS sewaktu 118 mg/dL.',
            kmb_penginderaan: initialPayload.kmb_penginderaan || 'Penglihatan baik sklera anikterik, fungsi pendengaran & penciuman normal.',
            kmb_imunologi: initialPayload.kmb_imunologi || 'Tanda infeksi sistemik (-), petekie (-), pembesaran KGB colli/aksila (-).',
            kmb_diagnostik_lab: initialPayload.kmb_diagnostik_lab || 'Hb 13.8 g/dL, Leukosit 7.200 /uL, Trombosit 245.000 /uL, Ureum 24 mg/dL, Kreatinin 0.9 mg/dL.',
            kmb_diagnostik_penunjang: initialPayload.kmb_diagnostik_penunjang || 'Rontgen Thorax PA: Cor dan pulmo dalam batas normal. EKG: Sinus Rhythm 80 bpm.',
            kmb_perioperatif: initialPayload.kmb_perioperatif || 'Bila ada jadwal tindakan operatif: Informed consent sah, puasa pre-op tercatat, verifikasi site marking.',
            riwayat_keluarga_genogram: initialPayload.riwayat_keluarga_genogram || 'Tidak ada riwayat diabetes melitus atau asma dalam silsilah keluarga 3 generasi.',
        }
    });

    const updatePayloadField = (key, value) => {
        setAssessData('payload', {
            ...assessData.payload,
            [key]: value,
        });
    };

    const handleSaveAssessment = (e) => {
        e.preventDefault();
        postAssess(`/mahasiswa/kasus/${session.uuid}/assessment`, {
            preserveScroll: true,
        });
    };

    // Care Plan Form
    const { data: planData, setData: setPlanData, post: postPlan, processing: planProcessing, reset: resetPlan } = useForm({
        master_sdki_id: masterSdki[0]?.id || '',
        master_slki_id: masterSlki[0]?.id || '',
        master_siki_id: masterSiki[0]?.id || '',
        subjective_data: '',
        objective_data: '',
        etiology: '',
        custom_outcome_targets: '',
        custom_interventions: '',
        priority_order: (session.care_plans?.length || 0) + 1,
    });

    const handleSaveCarePlan = (e) => {
        e.preventDefault();
        postPlan(`/mahasiswa/kasus/${session.uuid}/care-plan`, {
            preserveScroll: true,
            onSuccess: () => {
                setShowAddCarePlan(false);
                resetPlan();
            },
        });
    };

    const handleDeletePlan = (planId) => {
        if (confirm('Hapus rencana asuhan ini?')) {
            router.delete(`/mahasiswa/kasus/${session.uuid}/care-plan/${planId}`, { preserveScroll: true });
        }
    };

    // Vital Sign Form
    const { data: vitalData, setData: setVitalData, post: postVital, processing: vitalProcessing, reset: resetVital } = useForm({
        recorded_at: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
        blood_pressure: '120/80',
        heart_rate: '80',
        respiratory_rate: '20',
        temperature: '36.7',
        spo2: '98',
        gcs_score: '15',
        evaluation_notes: 'Keadaan umum tenang, hemodinamik stabil.',
    });

    const handleSaveVital = (e) => {
        e.preventDefault();
        postVital(`/mahasiswa/kasus/${session.uuid}/vital-sign`, {
            preserveScroll: true,
            onSuccess: () => {
                setShowAddVital(false);
                resetVital();
            },
        });
    };

    // Handover Form
    const { data: handoverData, setData: setHandoverData, post: postHandover, processing: handoverProcessing, reset: resetHandover } = useForm({
        format_type: 'SBAR',
        situation: '',
        background: '',
        assessment: '',
        recommendation: '',
        subjektif: '',
        objektif: '',
        analisis: '',
        planning: '',
    });

    const handleSaveHandover = (e) => {
        e.preventDefault();
        postHandover(`/mahasiswa/kasus/${session.uuid}/handover`, {
            preserveScroll: true,
            onSuccess: () => {
                setShowAddHandover(false);
                resetHandover();
            },
        });
    };

    const handleToggleSPO = (logId) => {
        if (isLocked) return;
        router.post(`/mahasiswa/kasus/${session.uuid}/procedure/${logId}/toggle`, {}, { preserveScroll: true });
    };

    const handleSubmitForReview = () => {
        if (confirm('Apakah Anda yakin ingin mengajukan berkas kasus ini ke Dosen Pembimbing untuk ditelaah? Berkas akan terkunci sementara selama proses telaah.')) {
            router.post(`/mahasiswa/kasus/${session.uuid}/submit`);
        }
    };

    const performedSPO = session.procedure_logs?.filter(p => p.is_performed) || [];

    return (
        <AuthLayout title={`Kasus: ${session.patient_name}`}>
            <Head title={`Kasus: ${session.patient_name}`} />

            {/* Breadcrumb & Action Bar */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div className="flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/mahasiswa/dashboard" className="hover:text-[#008D88] flex items-center gap-1 font-semibold transition-colors">
                        <ArrowLeft size={14} />
                        Daftar Kasus
                    </Link>
                    <span>/</span>
                    <span className="text-slate-800 font-bold truncate max-w-xs">{session.patient_name}</span>
                </div>

                <div className="flex items-center gap-2.5">
                    <a
                        href={`/print/case/${session.uuid}`}
                        target="_blank"
                        rel="noreferrer"
                        className="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all"
                    >
                        <Printer size={15} className="text-slate-500" />
                        Cetak Lembar Resmi A4
                    </a>

                    {!isLocked && (
                        <button
                            onClick={handleSubmitForReview}
                            className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                        >
                            <Send size={14} />
                            Ajukan Telaah ke Dosen
                        </button>
                    )}
                </div>
            </div>

            {/* Revision Alert (if need_revision) */}
            {session.status === 'need_revision' && session.review?.revision_notes && (
                <div className="p-5 rounded-2xl bg-amber-50 border-2 border-amber-300 shadow-sm flex items-start gap-4 mb-6">
                    <div className="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black shrink-0 mt-0.5 shadow-md">
                        <AlertTriangle size={20} />
                    </div>
                    <div>
                        <h4 className="font-bold text-amber-900 text-sm">
                            Catatan Revisi dari Dosen Pembimbing ({session.mentor?.name}):
                        </h4>
                        <div className="text-xs text-amber-900 mt-2 font-medium bg-white/80 p-3.5 rounded-xl border border-amber-200">
                            "{session.review.revision_notes}"
                        </div>
                        <p className="text-xs text-amber-800 mt-2">
                            Silakan perbaiki data pada tab-tab di bawah ini, kemudian klik tombol <strong>Ajukan Telaah ke Dosen</strong> kembali.
                        </p>
                    </div>
                </div>
            )}

            {/* Patient Header Card */}
            <div className="bg-white rounded-2xl border border-slate-200 p-6 mb-6 shadow-xs">
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div className="space-y-3">
                        <div className="flex flex-wrap items-center gap-2.5">
                            <h1 className="text-2xl font-black text-slate-900 tracking-tight">{session.patient_name}</h1>
                            <span className={`px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide border ${triageLabel[session.triage_category]?.badge || 'bg-slate-100 text-slate-700'}`}>
                                Triase: {triageLabel[session.triage_category]?.label || 'Hijau'}
                            </span>
                            <span className={`px-2.5 py-1 rounded-full text-xs font-bold border ${statusLabel[session.status]?.badge || 'bg-slate-100 text-slate-700'}`}>
                                {statusLabel[session.status]?.label || session.status}
                            </span>
                        </div>

                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                            <div>
                                <span className="text-slate-400 block font-medium">No. Rekam Medis</span>
                                <span className="font-bold text-slate-800 font-mono text-sm">{session.medical_record_no || '-'}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block font-medium">Usia & Gender</span>
                                <span className="font-bold text-slate-800">{session.age} Tahun / {session.gender === 'L' ? 'Laki-Laki' : 'Perempuan'}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block font-medium">Stase Kurikulum</span>
                                <span className="font-bold text-[#008D88]">{session.course?.code} — {session.course?.name}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block font-medium">Dosen Pembimbing</span>
                                <span className="font-bold text-slate-800">{session.mentor?.name || '-'}</span>
                            </div>
                        </div>
                    </div>

                    {session.review?.final_score && (
                        <div className="bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-4 text-center shrink-0 min-w-40 shadow-xs">
                            <div className="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Nilai Akhir Sah</div>
                            <div className="text-3xl font-black text-emerald-900 font-mono mt-1">
                                {Number(session.review.final_score).toFixed(1)}
                            </div>
                            <div className="text-[11px] text-emerald-600 font-medium mt-0.5">Rubrik Sub-CPMK</div>
                        </div>
                    )}
                </div>
            </div>

            {/* Navigation Tabs */}
            <div className="border-b border-slate-200 mb-6 flex items-center gap-2 overflow-x-auto text-sm font-semibold">
                <button
                    onClick={() => setActiveTab('assessment')}
                    className={`px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-xl ${
                        activeTab === 'assessment' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                    }`}
                >
                    <Stethoscope size={16} />
                    <span>1. Pengkajian ({stageType.toUpperCase()})</span>
                </button>
                <button
                    onClick={() => setActiveTab('care_plans')}
                    className={`px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-xl ${
                        activeTab === 'care_plans' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                    }`}
                >
                    <ClipboardList size={16} />
                    <span>2. Rencana Asuhan 3S ({session.care_plans?.length || 0})</span>
                </button>
                <button
                    onClick={() => setActiveTab('vitals')}
                    className={`px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-xl ${
                        activeTab === 'vitals' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                    }`}
                >
                    <Activity size={16} />
                    <span>3. Pemantauan TTV ({session.vital_signs?.length || 0})</span>
                </button>
                <button
                    onClick={() => setActiveTab('procedures')}
                    className={`px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-xl ${
                        activeTab === 'procedures' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                    }`}
                >
                    <HeartPulse size={16} />
                    <span>4. Checklist Tindakan SPO ({performedSPO.length}/{session.procedure_logs?.length || 0})</span>
                </button>
                <button
                    onClick={() => setActiveTab('evaluations')}
                    className={`px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-xl ${
                        activeTab === 'evaluations' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                    }`}
                >
                    <FileSpreadsheet size={16} />
                    <span>5. Evaluasi & SBAR ({session.evaluations_and_handovers?.length || 0})</span>
                </button>
            </div>

            {/* TAB 1: PENGKAJIAN KLINIS SESUAI STASE */}
            {activeTab === 'assessment' && (
                <div className="space-y-6">
                    <form onSubmit={handleSaveAssessment}>
                        <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-6">
                            <div className="flex items-center justify-between border-b border-slate-100 pb-4">
                                <div>
                                    <h3 className="font-bold text-slate-900 text-base">
                                        Formulir Pengkajian Klinis: {stageType === 'kgd' ? 'Keperawatan Gawat Darurat (KGD)' : stageType === 'kmb' ? 'Keperawatan Medikal Bedah (KMB)' : 'Kebutuhan Dasar Manusia (KDM)'}
                                    </h3>
                                    <p className="text-xs text-slate-500 mt-0.5">
                                        Sesuai kurikulum baku RPS Poltekkes Kemenkes Riau & Standar PPNI
                                    </p>
                                </div>
                                {!isLocked && (
                                    <button
                                        type="submit"
                                        disabled={assessProcessing}
                                        className="px-5 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm transition-all cursor-pointer disabled:opacity-50"
                                    >
                                        {assessProcessing ? 'Menyimpan...' : 'Simpan Pengkajian'}
                                    </button>
                                )}
                            </div>

                            {/* Anamnesis Umum */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="sm:col-span-2">
                                    <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Keluhan Utama <span className="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        rows={2}
                                        disabled={isLocked}
                                        value={assessData.payload.keluhan_utama}
                                        onChange={(e) => updatePayloadField('keluhan_utama', e.target.value)}
                                        placeholder="Contoh: Pasien mengeluh sesak napas berat sejak 2 jam lalu disertai nyeri dada kiri..."
                                        className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#008D88] focus:ring-2 focus:ring-[#008D88]/15 outline-none disabled:bg-slate-50"
                                    />
                                </div>

                                <div className="sm:col-span-2">
                                    <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Riwayat Penyakit Sekarang (RPS) & Mekanisme Kejadian
                                    </label>
                                    <textarea
                                        rows={3}
                                        disabled={isLocked}
                                        value={assessData.payload.riwayat_penyakit}
                                        onChange={(e) => updatePayloadField('riwayat_penyakit', e.target.value)}
                                        placeholder="Uraikan kronologi keluhan, onset, faktor pemberat, penanganan awal sebelum masuk faskes..."
                                        className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#008D88] focus:ring-2 focus:ring-[#008D88]/15 outline-none disabled:bg-slate-50"
                                    />
                                </div>
                            </div>

                            {/* KGD SPECIFIC: SURVEI PRIMER ABCDE & AMPLE */}
                            {stageType === 'kgd' && (
                                <div className="space-y-6 pt-4 border-t border-slate-100">
                                    <div className="bg-red-50/50 border border-red-200/80 rounded-2xl p-4">
                                        <h4 className="font-bold text-red-900 text-sm flex items-center gap-2">
                                            <ShieldAlert size={18} className="text-red-600" />
                                            Survei Primer Kegawatdaruratan (ABCDE Approach)
                                        </h4>
                                        <p className="text-xs text-red-700 mt-0.5">Identifikasi dan tata laksana segera kondisi yang mengancam jiwa</p>
                                    </div>

                                    {/* Airway */}
                                    <div className="p-4 rounded-xl border border-slate-200 space-y-3">
                                        <div className="font-bold text-xs uppercase tracking-wider text-[#008D88] flex items-center gap-1.5">
                                            <span className="w-2 h-2 rounded-full bg-[#008D88]"></span>
                                            A - Airway & Cervical Spine Control
                                        </div>
                                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Status Kepatenan</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.airway_status}
                                                    onChange={(e) => updatePayloadField('airway_status', e.target.value)}
                                                    placeholder="Paten / Obstruksi parsial"
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Suara Napas Tambahan</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.airway_suara}
                                                    onChange={(e) => updatePayloadField('airway_suara', e.target.value)}
                                                    placeholder="Bersih / Snoring / Stridor / Gargling"
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Fiksasi Cervical Collar</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.cervical_collar}
                                                    onChange={(e) => updatePayloadField('cervical_collar', e.target.value)}
                                                    placeholder="Ya (terpasang) / Tidak"
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Breathing */}
                                    <div className="p-4 rounded-xl border border-slate-200 space-y-3">
                                        <div className="font-bold text-xs uppercase tracking-wider text-[#008D88] flex items-center gap-1.5">
                                            <span className="w-2 h-2 rounded-full bg-[#008D88]"></span>
                                            B - Breathing & Ventilation
                                        </div>
                                        <div className="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">RR (x/menit)</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.breathing_rr}
                                                    onChange={(e) => updatePayloadField('breathing_rr', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">SpO2 (%)</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.breathing_spo2}
                                                    onChange={(e) => updatePayloadField('breathing_spo2', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Pola & Retraksi Dada</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.breathing_pola}
                                                    onChange={(e) => updatePayloadField('breathing_pola', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Terapi Oksigen</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.breathing_o2}
                                                    onChange={(e) => updatePayloadField('breathing_o2', e.target.value)}
                                                    placeholder="Nasal Canul / NRM / Masker"
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Circulation */}
                                    <div className="p-4 rounded-xl border border-slate-200 space-y-3">
                                        <div className="font-bold text-xs uppercase tracking-wider text-[#008D88] flex items-center gap-1.5">
                                            <span className="w-2 h-2 rounded-full bg-[#008D88]"></span>
                                            C - Circulation & Bleeding Control
                                        </div>
                                        <div className="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Tekanan Darah (mmHg)</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.circulation_td}
                                                    onChange={(e) => updatePayloadField('circulation_td', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Nadi & Kualitas</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.circulation_nadi}
                                                    onChange={(e) => updatePayloadField('circulation_nadi', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Akral & CRT</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.circulation_akral}
                                                    onChange={(e) => updatePayloadField('circulation_akral', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Akses Intravena (IV Line)</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.circulation_iv}
                                                    onChange={(e) => updatePayloadField('circulation_iv', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Disability */}
                                    <div className="p-4 rounded-xl border border-slate-200 space-y-3">
                                        <div className="font-bold text-xs uppercase tracking-wider text-[#008D88] flex items-center gap-1.5">
                                            <span className="w-2 h-2 rounded-full bg-[#008D88]"></span>
                                            D - Disability & Neurological Status
                                        </div>
                                        <div className="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">GCS (E / M / V)</label>
                                                <div className="flex gap-2">
                                                    <input
                                                        type="text"
                                                        placeholder="E"
                                                        disabled={isLocked}
                                                        value={assessData.payload.disability_gcs_e}
                                                        onChange={(e) => updatePayloadField('disability_gcs_e', e.target.value)}
                                                        className="w-1/3 px-2 py-2 rounded-lg border border-slate-200 text-center text-xs font-semibold"
                                                    />
                                                    <input
                                                        type="text"
                                                        placeholder="M"
                                                        disabled={isLocked}
                                                        value={assessData.payload.disability_gcs_m}
                                                        onChange={(e) => updatePayloadField('disability_gcs_m', e.target.value)}
                                                        className="w-1/3 px-2 py-2 rounded-lg border border-slate-200 text-center text-xs font-semibold"
                                                    />
                                                    <input
                                                        type="text"
                                                        placeholder="V"
                                                        disabled={isLocked}
                                                        value={assessData.payload.disability_gcs_v}
                                                        onChange={(e) => updatePayloadField('disability_gcs_v', e.target.value)}
                                                        className="w-1/3 px-2 py-2 rounded-lg border border-slate-200 text-center text-xs font-semibold"
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Tingkat Kesadaran</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.disability_kesadaran}
                                                    onChange={(e) => updatePayloadField('disability_kesadaran', e.target.value)}
                                                    placeholder="Compos Mentis / Somnolen"
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                            <div className="sm:col-span-2">
                                                <label className="block text-xs text-slate-600 mb-1">Pupil & Refleks Cahaya</label>
                                                <input
                                                    type="text"
                                                    disabled={isLocked}
                                                    value={assessData.payload.disability_pupil}
                                                    onChange={(e) => updatePayloadField('disability_pupil', e.target.value)}
                                                    placeholder="Isokor 3mm/3mm, RC (+/+)"
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Exposure & AMPLE */}
                                    <div className="p-4 rounded-xl border border-slate-200 space-y-4">
                                        <div className="font-bold text-xs uppercase tracking-wider text-[#008D88] flex items-center gap-1.5">
                                            <span className="w-2 h-2 rounded-full bg-[#008D88]"></span>
                                            E - Exposure & Survei Sekunder (AMPLE History)
                                        </div>
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Suhu Tubuh & Jejas Trauma</label>
                                                <textarea
                                                    rows={2}
                                                    disabled={isLocked}
                                                    value={assessData.payload.exposure_jejas}
                                                    onChange={(e) => updatePayloadField('exposure_jejas', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs text-slate-600 mb-1">Riwayat AMPLE (Alergi, Medikasi, Penyakit, Last meal, Event)</label>
                                                <textarea
                                                    rows={2}
                                                    disabled={isLocked}
                                                    value={assessData.payload.ample_a}
                                                    onChange={(e) => updatePayloadField('ample_a', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                            <div className="sm:col-span-2">
                                                <label className="block text-xs text-slate-600 mb-1">Pemeriksaan Fisik Head-to-Toe</label>
                                                <textarea
                                                    rows={2}
                                                    disabled={isLocked}
                                                    value={assessData.payload.head_to_toe}
                                                    onChange={(e) => updatePayloadField('head_to_toe', e.target.value)}
                                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* KDM SPECIFIC: 9 DOMAIN VIRGINIA HENDERSON & 6 BENAR OBAT */}
                            {stageType === 'kdm' && (
                                <div className="space-y-6 pt-4 border-t border-slate-100">
                                    <div className="bg-emerald-50/50 border border-emerald-200/80 rounded-2xl p-4">
                                        <h4 className="font-bold text-emerald-900 text-sm flex items-center gap-2">
                                            <Stethoscope size={18} className="text-emerald-600" />
                                            Pengkajian 9 Domain Kebutuhan Dasar Manusia (Virginia Henderson)
                                        </h4>
                                        <p className="text-xs text-emerald-700 mt-0.5">Pendekatan holistik bio-psiko-sosio-spiritual untuk asuhan keperawatan dasar</p>
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        {/* 1. Oksigenasi */}
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">1. Kebutuhan Oksigenasi</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_oksigenasi}
                                                onChange={(e) => updatePayloadField('kdm_oksigenasi', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        {/* 2. Nutrisi */}
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">2. Kebutuhan Nutrisi (BB, TB, Diet, IMT)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_nutrisi_keluhan}
                                                onChange={(e) => updatePayloadField('kdm_nutrisi_keluhan', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        {/* 3. Cairan & Elektrolit */}
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">3. Kebutuhan Cairan & Elektrolit (Balance Cairan)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_cairan_balance}
                                                onChange={(e) => updatePayloadField('kdm_cairan_balance', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        {/* 4. Eliminasi */}
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">4. Kebutuhan Eliminasi (Urin & Fekal)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_eliminasi_bak}
                                                onChange={(e) => updatePayloadField('kdm_eliminasi_bak', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        {/* 5. Aktivitas & Istirahat */}
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">5. Aktivitas & Istirahat (Indeks Barthel / Katz)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_aktivitas_barthel}
                                                onChange={(e) => updatePayloadField('kdm_aktivitas_barthel', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        {/* 6. Personal Hygiene */}
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">6. Personal Hygiene & Skala Braden Dekubitus</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_hygiene}
                                                onChange={(e) => updatePayloadField('kdm_hygiene', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        {/* 7. Termoregulasi */}
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">7. Kebutuhan Termoregulasi</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_termoregulasi}
                                                onChange={(e) => updatePayloadField('kdm_termoregulasi', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        {/* 8. Rasa Aman & Nyaman (PQRST Nyeri) */}
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">8. Kenyamanan & Pengkajian Nyeri PQRST (Skala 0-10)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_nyeri_pqrst}
                                                onChange={(e) => updatePayloadField('kdm_nyeri_pqrst', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        {/* 9. Psikososial Spiritual */}
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5 sm:col-span-2">
                                            <label className="block text-xs font-bold text-slate-800">9. Kebutuhan Psikososial, Spiritual & Koping Pasien</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_psikososial}
                                                onChange={(e) => updatePayloadField('kdm_psikososial', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        {/* Prinsip 6 Benar Obat */}
                                        <div className="p-3.5 rounded-xl border border-amber-200 bg-amber-50/40 space-y-1.5 sm:col-span-2">
                                            <label className="block text-xs font-bold text-amber-900">Penerapan Prinsip 6 Benar Pemberian Obat (Sub-CPMK 2)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kdm_enam_benar_obat}
                                                onChange={(e) => updatePayloadField('kdm_enam_benar_obat', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-amber-200 text-xs bg-white"
                                            />
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* KMB SPECIFIC: 9 SISTEM ORGAN B1-B6 & PERIOPERATIF */}
                            {stageType === 'kmb' && (
                                <div className="space-y-6 pt-4 border-t border-slate-100">
                                    <div className="bg-blue-50/50 border border-blue-200/80 rounded-2xl p-4">
                                        <h4 className="font-bold text-blue-900 text-sm flex items-center gap-2">
                                            <Activity size={18} className="text-blue-600" />
                                            Pengkajian 9 Sistem Tubuh (B1-B6) & Data Penunjang Medikal Bedah
                                        </h4>
                                        <p className="text-xs text-blue-700 mt-0.5">Pemeriksaan fisik spesifik sistem organ dan data penunjang laboratoris/radiologis</p>
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">B1 - Breathing (Pernapasan & Paru)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kmb_b1_breathing}
                                                onChange={(e) => updatePayloadField('kmb_b1_breathing', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">B2 - Blood (Kardiovaskuler & Sirkulasi)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kmb_b2_blood}
                                                onChange={(e) => updatePayloadField('kmb_b2_blood', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">B3 - Brain (Persarafan & Sensorik)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kmb_b3_brain}
                                                onChange={(e) => updatePayloadField('kmb_b3_brain', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">B4 - Bladder (Perkemihan & Ginjal)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kmb_b4_bladder}
                                                onChange={(e) => updatePayloadField('kmb_b4_bladder', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">B5 - Bowel (Pencernaan & Nutrisi)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kmb_b5_bowel}
                                                onChange={(e) => updatePayloadField('kmb_b5_bowel', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">B6 - Bone (Muskuloskeletal & Integumen)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kmb_b6_bone}
                                                onChange={(e) => updatePayloadField('kmb_b6_bone', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">Hasil Laboratorium (DL, Ureum, Kreatinin, GDS)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kmb_diagnostik_lab}
                                                onChange={(e) => updatePayloadField('kmb_diagnostik_lab', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                            <label className="block text-xs font-bold text-slate-800">Pemeriksaan Penunjang (EKG, Ro Thorax, USG)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kmb_diagnostik_penunjang}
                                                onChange={(e) => updatePayloadField('kmb_diagnostik_penunjang', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>

                                        <div className="p-3.5 rounded-xl border border-slate-200 space-y-1.5 sm:col-span-2">
                                            <label className="block text-xs font-bold text-slate-800">Asuhan Perioperatif (Bila Ada Tindakan Pembedahan)</label>
                                            <textarea
                                                rows={2}
                                                disabled={isLocked}
                                                value={assessData.payload.kmb_perioperatif}
                                                onChange={(e) => updatePayloadField('kmb_perioperatif', e.target.value)}
                                                className="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs"
                                            />
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Submit button on bottom */}
                            {!isLocked && (
                                <div className="pt-4 border-t border-slate-100 flex justify-end">
                                    <button
                                        type="submit"
                                        disabled={assessProcessing}
                                        className="px-6 py-2.5 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs transition-colors cursor-pointer disabled:opacity-50"
                                    >
                                        {assessProcessing ? 'Menyimpan Pengkajian...' : 'Simpan Seluruh Data Pengkajian'}
                                    </button>
                                </div>
                            )}
                        </div>
                    </form>
                </div>
            )}

            {/* TAB 2: RENCANA ASUHAN 3S (SDKI, SLKI, SIKI) */}
            {activeTab === 'care_plans' && (
                <div className="space-y-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h3 className="font-bold text-slate-900 text-base">Rencana Asuhan Keperawatan 3S (SDKI - SLKI - SIKI)</h3>
                            <p className="text-xs text-slate-500 mt-0.5">Analisis data klinis, formulasi diagnosa, penentuan luaran, dan intervensi PPNI</p>
                        </div>
                        {!isLocked && (
                            <button
                                onClick={() => setShowAddCarePlan(!showAddCarePlan)}
                                className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm flex items-center gap-1.5 transition-all cursor-pointer"
                            >
                                <Plus size={15} />
                                Tambah Rencana 3S
                            </button>
                        )}
                    </div>

                    {/* Add Care Plan Modal / Form */}
                    {showAddCarePlan && !isLocked && (
                        <form onSubmit={handleSaveCarePlan} className="bg-slate-50 border-2 border-[#008D88]/30 rounded-2xl p-6 shadow-sm space-y-4">
                            <h4 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                                <ClipboardList size={17} className="text-[#008D88]" />
                                Formulir Analisa & Perencanaan 3S Baru
                            </h4>

                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Diagnosis SDKI <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={planData.master_sdki_id}
                                        onChange={(e) => setPlanData('master_sdki_id', e.target.value)}
                                        required
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white font-medium outline-none focus:border-[#008D88]"
                                    >
                                        {masterSdki.map((s) => (
                                            <option key={s.id} value={s.id}>{s.code} - {s.title}</option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Luaran SLKI <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={planData.master_slki_id}
                                        onChange={(e) => setPlanData('master_slki_id', e.target.value)}
                                        required
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white font-medium outline-none focus:border-[#008D88]"
                                    >
                                        {masterSlki.map((s) => (
                                            <option key={s.id} value={s.id}>{s.code} - {s.title}</option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Intervensi SIKI <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={planData.master_siki_id}
                                        onChange={(e) => setPlanData('master_siki_id', e.target.value)}
                                        required
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white font-medium outline-none focus:border-[#008D88]"
                                    >
                                        {masterSiki.map((s) => (
                                            <option key={s.id} value={s.id}>{s.code} - {s.title}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Data Subjektif (DS)</label>
                                    <textarea
                                        rows={2}
                                        value={planData.subjective_data}
                                        onChange={(e) => setPlanData('subjective_data', e.target.value)}
                                        placeholder="Keluhan pasien (contoh: Pasien mengeluh sesak dan lemas...)"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Data Objektif (DO)</label>
                                    <textarea
                                        rows={2}
                                        value={planData.objective_data}
                                        onChange={(e) => setPlanData('objective_data', e.target.value)}
                                        placeholder="Temuan klinis terukur (contoh: RR 28x/m, SpO2 92%, ronkhi basah...)"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    />
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div className="sm:col-span-2">
                                    <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Etiologi / Penyebab Patofisiologis</label>
                                    <input
                                        type="text"
                                        value={planData.etiology}
                                        onChange={(e) => setPlanData('etiology', e.target.value)}
                                        placeholder="Contoh: Hambatan upaya napas sekunder terhadap spasme bronkus"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Skala Prioritas</label>
                                    <select
                                        value={planData.priority_order}
                                        onChange={(e) => setPlanData('priority_order', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white font-semibold outline-none focus:border-[#008D88]"
                                    >
                                        <option value={1}>Prioritas 1 (Utama)</option>
                                        <option value={2}>Prioritas 2</option>
                                        <option value={3}>Prioritas 3</option>
                                    </select>
                                </div>
                            </div>

                            <div className="flex justify-end gap-2 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setShowAddCarePlan(false)}
                                    className="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100 transition-colors"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={planProcessing}
                                    className="px-5 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm transition-all"
                                >
                                    {planProcessing ? 'Menyimpan...' : 'Tambahkan Rencana 3S'}
                                </button>
                            </div>
                        </form>
                    )}

                    {/* Care Plans List */}
                    <div className="space-y-4">
                        {session.care_plans?.length === 0 ? (
                            <div className="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-sm">
                                <ClipboardList size={36} className="mx-auto mb-2 text-slate-300" />
                                Belum ada rencana asuhan 3S yang ditambahkan. Silakan klik tombol <strong>Tambah Rencana 3S</strong> di atas.
                            </div>
                        ) : (
                            session.care_plans?.map((plan, idx) => (
                                <div key={plan.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs relative hover:border-[#008D88]/50 transition-colors">
                                    <div className="flex items-start justify-between gap-4 mb-3">
                                        <div className="flex items-center gap-2">
                                            <span className="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#008D88]/10 text-[#008D88]">
                                                Prioritas #{plan.priority_order || idx + 1}
                                            </span>
                                            <h4 className="font-bold text-slate-900 text-sm">
                                                {plan.sdki?.code} — {plan.sdki?.title}
                                            </h4>
                                        </div>
                                        {!isLocked && (
                                            <button
                                                onClick={() => handleDeletePlan(plan.id)}
                                                className="text-slate-400 hover:text-red-600 transition-colors p-1"
                                                title="Hapus Rencana"
                                            >
                                                <Trash2 size={16} />
                                            </button>
                                        )}
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs mb-3">
                                        <div className="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                            <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Data Subjektif (DS)</span>
                                            <p className="text-slate-800">{plan.subjective_data || '-'}</p>
                                        </div>
                                        <div className="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                            <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Data Objektif (DO)</span>
                                            <p className="text-slate-800">{plan.objective_data || '-'}</p>
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-2 border-t border-slate-100">
                                        <div>
                                            <span className="font-bold text-emerald-700 uppercase tracking-wider block mb-0.5">
                                                Luaran (SLKI): {plan.slki?.code}
                                            </span>
                                            <p className="text-slate-800 font-medium">{plan.slki?.title}</p>
                                        </div>
                                        <div>
                                            <span className="font-bold text-blue-700 uppercase tracking-wider block mb-0.5">
                                                Intervensi (SIKI): {plan.siki?.code}
                                            </span>
                                            <p className="text-slate-800 font-medium">{plan.siki?.title}</p>
                                        </div>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>
                </div>
            )}

            {/* TAB 3: PEMANTAUAN TTV BERKALA (TIME-SERIES) */}
            {activeTab === 'vitals' && (
                <div className="space-y-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h3 className="font-bold text-slate-900 text-base">Lembar Pemantauan Tanda-Tanda Vital Berkala</h3>
                            <p className="text-xs text-slate-500 mt-0.5">Pencatatan hemodinamik dan observasi respon klinis berkelanjutan</p>
                        </div>
                        {!isLocked && (
                            <button
                                onClick={() => setShowAddVital(!showAddVital)}
                                className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm flex items-center gap-1.5 transition-all cursor-pointer"
                            >
                                <Plus size={15} />
                                Input Observasi TTV
                            </button>
                        )}
                    </div>

                    {/* Add Vital Sign Form */}
                    {showAddVital && !isLocked && (
                        <form onSubmit={handleSaveVital} className="bg-slate-50 border-2 border-[#008D88]/30 rounded-2xl p-6 shadow-sm space-y-4">
                            <h4 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                                <Activity size={17} className="text-[#008D88]" />
                                Input Parameter TTV Pasien Baru
                            </h4>

                            <div className="grid grid-cols-2 sm:grid-cols-6 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Jam Catat</label>
                                    <input
                                        type="text"
                                        value={vitalData.recorded_at}
                                        onChange={(e) => setVitalData('recorded_at', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white text-center font-bold"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">TD (mmHg)</label>
                                    <input
                                        type="text"
                                        value={vitalData.blood_pressure}
                                        onChange={(e) => setVitalData('blood_pressure', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white text-center font-bold"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Nadi (x/m)</label>
                                    <input
                                        type="text"
                                        value={vitalData.heart_rate}
                                        onChange={(e) => setVitalData('heart_rate', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white text-center font-bold"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">RR (x/m)</label>
                                    <input
                                        type="text"
                                        value={vitalData.respiratory_rate}
                                        onChange={(e) => setVitalData('respiratory_rate', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white text-center font-bold"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Suhu (°C)</label>
                                    <input
                                        type="text"
                                        value={vitalData.temperature}
                                        onChange={(e) => setVitalData('temperature', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white text-center font-bold"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">SpO2 (%)</label>
                                    <input
                                        type="text"
                                        value={vitalData.spo2}
                                        onChange={(e) => setVitalData('spo2', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white text-center font-bold"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Catatan Evaluasi Klinis / Respon Tindakan</label>
                                <input
                                    type="text"
                                    value={vitalData.evaluation_notes}
                                    onChange={(e) => setVitalData('evaluation_notes', e.target.value)}
                                    placeholder="Contoh: Pasien tampak lebih tenang setelah nebulisasi, wheezing berkurang."
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                                />
                            </div>

                            <div className="flex justify-end gap-2">
                                <button
                                    type="button"
                                    onClick={() => setShowAddVital(false)}
                                    className="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={vitalProcessing}
                                    className="px-5 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm"
                                >
                                    {vitalProcessing ? 'Menyimpan...' : 'Simpan Data TTV'}
                                </button>
                            </div>
                        </form>
                    )}

                    {/* Vitals Table */}
                    <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs">
                                <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <tr>
                                        <th className="py-3 px-4">Waktu / Jam</th>
                                        <th className="py-3 px-4">TD (mmHg)</th>
                                        <th className="py-3 px-4">Nadi (x/m)</th>
                                        <th className="py-3 px-4">RR (x/m)</th>
                                        <th className="py-3 px-4">Suhu (°C)</th>
                                        <th className="py-3 px-4">SpO2</th>
                                        <th className="py-3 px-4">GCS</th>
                                        <th className="py-3 px-4">Catatan Perkembangan</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {session.vital_signs?.length === 0 ? (
                                        <tr>
                                            <td colSpan={8} className="py-8 text-center text-slate-400 text-xs">
                                                Belum ada pemantauan tanda vital berkala yang diinput.
                                            </td>
                                        </tr>
                                    ) : (
                                        session.vital_signs?.map((v) => (
                                            <tr key={v.id} className="hover:bg-slate-50/80 transition-colors">
                                                <td className="py-3 px-4 font-bold font-mono text-slate-900">{v.recorded_at}</td>
                                                <td className="py-3 px-4 font-semibold text-slate-800">{v.blood_pressure}</td>
                                                <td className="py-3 px-4 font-semibold text-slate-800">{v.heart_rate}</td>
                                                <td className="py-3 px-4 font-semibold text-slate-800">{v.respiratory_rate}</td>
                                                <td className="py-3 px-4 font-semibold text-slate-800">{v.temperature}°C</td>
                                                <td className="py-3 px-4 font-semibold text-[#008D88]">{v.spo2 || v.oxygen_saturation || 98}%</td>
                                                <td className="py-3 px-4 font-semibold text-slate-700">{v.gcs_score || 15}</td>
                                                <td className="py-3 px-4 text-slate-600">{v.evaluation_notes || '-'}</td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            )}

            {/* TAB 4: LOGBOOK CHECKLIST TINDAKAN SPO */}
            {activeTab === 'procedures' && (
                <div className="space-y-6">
                    <div>
                        <h3 className="font-bold text-slate-900 text-base">Checklist Prosedur Standar Operasional (SPO Praktikum)</h3>
                        <p className="text-xs text-slate-500 mt-0.5">
                            Centang tindakan mandiri yang telah dilakukan mahasiswa. Status validasi e-paraf akan diberikan oleh Dosen Pembimbing Klinik.
                        </p>
                    </div>

                    <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs">
                                <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <tr>
                                        <th className="py-3 px-4 w-12 text-center">Status</th>
                                        <th className="py-3 px-4">Prosedur Tindakan Klinis</th>
                                        <th className="py-3 px-4">Target Sub-CPMK</th>
                                        <th className="py-3 px-4">Waktu Pelaksanaan</th>
                                        <th className="py-3 px-4 text-right">E-Paraf Dosen</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {session.procedure_logs?.length === 0 ? (
                                        <tr>
                                            <td colSpan={5} className="py-8 text-center text-slate-400">
                                                Tidak ada katalog prosedur SPO untuk stase mata kuliah ini.
                                            </td>
                                        </tr>
                                    ) : (
                                        session.procedure_logs?.map((p) => (
                                            <tr key={p.id} className="hover:bg-slate-50/80 transition-colors">
                                                <td className="py-3 px-4 text-center">
                                                    <button
                                                        type="button"
                                                        disabled={isLocked}
                                                        onClick={() => handleToggleSPO(p.id)}
                                                        className={`w-6 h-6 rounded-lg flex items-center justify-center transition-all ${
                                                            p.is_performed
                                                                ? 'bg-[#008D88] text-white shadow-xs'
                                                                : 'border-2 border-slate-300 hover:border-[#008D88] text-transparent'
                                                        } ${isLocked ? 'cursor-not-allowed opacity-80' : 'cursor-pointer'}`}
                                                    >
                                                        <Check size={14} className={p.is_performed ? 'opacity-100' : 'opacity-0'} />
                                                    </button>
                                                </td>
                                                <td className="py-3 px-4">
                                                    <div className={`font-bold ${p.is_performed ? 'text-slate-900' : 'text-slate-500'}`}>
                                                        {p.procedure?.procedure_name}
                                                    </div>
                                                    <div className="text-[11px] text-slate-400 mt-0.5">{p.procedure?.domain_category}</div>
                                                </td>
                                                <td className="py-3 px-4">
                                                    <span className="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                                        {p.procedure?.sub_cpmk_reference}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 text-slate-500 font-mono">
                                                    {p.performed_at ? formatDateTime(p.performed_at) : '-'}
                                                </td>
                                                <td className="py-3 px-4 text-right">
                                                    {p.is_verified ? (
                                                        <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                            <CheckCircle2 size={13} className="text-emerald-600" />
                                                            Terparaf Sah
                                                        </span>
                                                    ) : p.is_performed ? (
                                                        <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                                            <Clock size={13} className="text-amber-600" />
                                                            Menunggu Paraf
                                                        </span>
                                                    ) : (
                                                        <span className="text-slate-400 text-[11px]">Belum Dilakukan</span>
                                                    )}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            )}

            {/* TAB 5: EVALUASI SBAR & SOAP */}
            {activeTab === 'evaluations' && (
                <div className="space-y-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h3 className="font-bold text-slate-900 text-base">Evaluasi Klinis & Handover (SBAR & SOAP)</h3>
                            <p className="text-xs text-slate-500 mt-0.5">Catatan perkembangan pasien dan instrumen timbang terima serah rawat</p>
                        </div>
                        {!isLocked && (
                            <button
                                onClick={() => setShowAddHandover(!showAddHandover)}
                                className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm flex items-center gap-1.5 transition-all cursor-pointer"
                            >
                                <Plus size={15} />
                                Tambah Catatan Evaluasi
                            </button>
                        )}
                    </div>

                    {/* Add Handover Form */}
                    {showAddHandover && !isLocked && (
                        <form onSubmit={handleSaveHandover} className="bg-slate-50 border-2 border-[#008D88]/30 rounded-2xl p-6 shadow-sm space-y-4">
                            <div className="flex items-center gap-4 border-b border-slate-200 pb-3">
                                <span className="text-xs font-bold text-slate-700">Pilih Format:</span>
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setHandoverFormat('SBAR');
                                            setHandoverData('format_type', 'SBAR');
                                        }}
                                        className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${
                                            handoverFormat === 'SBAR' ? 'bg-[#008D88] text-white' : 'bg-white text-slate-700 border border-slate-200'
                                        }`}
                                    >
                                        Format SBAR (Timbang Terima)
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setHandoverFormat('SOAP');
                                            setHandoverData('format_type', 'SOAP');
                                        }}
                                        className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${
                                            handoverFormat === 'SOAP' ? 'bg-[#008D88] text-white' : 'bg-white text-slate-700 border border-slate-200'
                                        }`}
                                    >
                                        Format SOAP (Catatan Perkembangan)
                                    </button>
                                </div>
                            </div>

                            {handoverFormat === 'SBAR' ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">S - Situation (Situasi Terkini Pasien)</label>
                                        <textarea
                                            rows={2}
                                            value={handoverData.situation}
                                            onChange={(e) => setHandoverData('situation', e.target.value)}
                                            placeholder="Nama pasien, keluhan saat ini, status triase, diagnosa medis..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">B - Background (Latar Belakang Klinis)</label>
                                        <textarea
                                            rows={2}
                                            value={handoverData.background}
                                            onChange={(e) => setHandoverData('background', e.target.value)}
                                            placeholder="Riwayat penyakit, terapi yang sudah diberikan, hasil lab/diagnostik..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">A - Assessment (Penilaian Kondisi Klinis)</label>
                                        <textarea
                                            rows={2}
                                            value={handoverData.assessment}
                                            onChange={(e) => setHandoverData('assessment', e.target.value)}
                                            placeholder="Analisis respon hemodinamik, kepatenan jalan napas, resiko penurunan..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">R - Recommendation (Rekomendasi Tindak Lanjut)</label>
                                        <textarea
                                            rows={2}
                                            value={handoverData.recommendation}
                                            onChange={(e) => setHandoverData('recommendation', e.target.value)}
                                            placeholder="Instruksi monitoring lanjut, jadwal pemberian obat, persiapan rujukan/pindah..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                                        />
                                    </div>
                                </div>
                            ) : (
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">S - Subjektif</label>
                                        <textarea
                                            rows={2}
                                            value={handoverData.subjektif}
                                            onChange={(e) => setHandoverData('subjektif', e.target.value)}
                                            placeholder="Keluhan yang dirasakan pasien saat evaluasi..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">O - Objektif</label>
                                        <textarea
                                            rows={2}
                                            value={handoverData.objektif}
                                            onChange={(e) => setHandoverData('objektif', e.target.value)}
                                            placeholder="TTV terakhir dan hasil pemeriksaan fisik objektif..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">A - Analisis Masalah</label>
                                        <textarea
                                            rows={2}
                                            value={handoverData.analisis}
                                            onChange={(e) => setHandoverData('analisis', e.target.value)}
                                            placeholder="Masalah keperawatan teratasi sebagian / belum teratasi..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">P - Planning (Rencana Intervensi)</label>
                                        <textarea
                                            rows={2}
                                            value={handoverData.planning}
                                            onChange={(e) => setHandoverData('planning', e.target.value)}
                                            placeholder="Lanjutkan intervensi 1, 2, 3 atau modifikasi intervensi..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                                        />
                                    </div>
                                </div>
                            )}

                            <div className="flex justify-end gap-2">
                                <button
                                    type="button"
                                    onClick={() => setShowAddHandover(false)}
                                    className="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={handoverProcessing}
                                    className="px-5 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm"
                                >
                                    {handoverProcessing ? 'Menyimpan...' : 'Simpan Evaluasi'}
                                </button>
                            </div>
                        </form>
                    )}

                    {/* Evaluations List */}
                    <div className="space-y-4">
                        {session.evaluations_and_handovers?.length === 0 ? (
                            <div className="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-sm">
                                <FileSpreadsheet size={36} className="mx-auto mb-2 text-slate-300" />
                                Belum ada catatan evaluasi atau handover yang dibuat.
                            </div>
                        ) : (
                            session.evaluations_and_handovers?.map((ev) => (
                                <div key={ev.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                                    <div className="flex items-center justify-between gap-4 mb-3 border-b border-slate-100 pb-2">
                                        <span className={`px-2.5 py-0.5 rounded-full text-xs font-bold ${
                                            ev.format_type === 'SBAR' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'
                                        }`}>
                                            Format {ev.format_type}
                                        </span>
                                        <span className="text-xs text-slate-400 font-mono">
                                            {formatDateTime(ev.created_at)}
                                        </span>
                                    </div>

                                    {ev.format_type === 'SBAR' ? (
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                            <div className="p-3 rounded-xl bg-slate-50">
                                                <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Situation (S)</span>
                                                <p className="text-slate-800">{ev.payload?.situation || '-'}</p>
                                            </div>
                                            <div className="p-3 rounded-xl bg-slate-50">
                                                <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Background (B)</span>
                                                <p className="text-slate-800">{ev.payload?.background || '-'}</p>
                                            </div>
                                            <div className="p-3 rounded-xl bg-slate-50">
                                                <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Assessment (A)</span>
                                                <p className="text-slate-800">{ev.payload?.assessment || '-'}</p>
                                            </div>
                                            <div className="p-3 rounded-xl bg-slate-50">
                                                <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Recommendation (R)</span>
                                                <p className="text-slate-800">{ev.payload?.recommendation || '-'}</p>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                            <div className="p-3 rounded-xl bg-slate-50">
                                                <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Subjektif (S)</span>
                                                <p className="text-slate-800">{ev.payload?.subjektif || '-'}</p>
                                            </div>
                                            <div className="p-3 rounded-xl bg-slate-50">
                                                <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Objektif (O)</span>
                                                <p className="text-slate-800">{ev.payload?.objektif || '-'}</p>
                                            </div>
                                            <div className="p-3 rounded-xl bg-slate-50">
                                                <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Analisis (A)</span>
                                                <p className="text-slate-800">{ev.payload?.analisis || '-'}</p>
                                            </div>
                                            <div className="p-3 rounded-xl bg-slate-50">
                                                <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Planning (P)</span>
                                                <p className="text-slate-800">{ev.payload?.planning || '-'}</p>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ))
                        )}
                    </div>
                </div>
            )}
        </AuthLayout>
    );
}
