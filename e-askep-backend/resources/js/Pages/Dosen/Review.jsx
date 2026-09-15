import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import {
    ArrowLeft, Printer, AlertTriangle, CheckCircle2,
    Activity, ClipboardList, Stethoscope, HeartPulse, FileSpreadsheet,
    Clock, ShieldCheck, Check, Send, Award, FileText, User, ChevronRight
} from 'lucide-react';
import { statusLabel, triageLabel, formatDate, formatDateTime } from '@/utils';

export default function DosenReview({ session }) {
    const { auth } = usePage().props;
    const [activeTab, setActiveTab] = useState('assessment');
    const [actionType, setActionType] = useState('none'); // 'none', 'revision', 'grade'
    const [selectedSpoIds, setSelectedSpoIds] = useState(
        session.procedure_logs?.filter(p => p.is_performed && !p.is_verified).map(p => p.id) || []
    );

    const isLocked = session.status === 'approved_graded';
    const courseCode = (session.course?.code || '').toUpperCase();
    const courseName = (session.course?.name || '').toUpperCase();
    const stageType = session.assessment?.stage_type || (
        courseCode.includes('WAT5.31') || courseName.includes('KGD') || courseName.includes('DARURAT') ? 'kgd' :
        courseCode.includes('WAT5.24') || courseName.includes('KMB') || courseName.includes('BEDAH') ? 'kmb' : 'kdm'
    );

    const payload = session.assessment?.assessment_payload || {};

    // Revision Form
    const { data: revData, setData: setRevData, post: postRev, processing: revProcessing } = useForm({
        revision_notes: '',
    });

    const handleRequestRevision = (e) => {
        e.preventDefault();
        if (!revData.revision_notes.trim()) {
            alert('Silakan tuliskan catatan revisi untuk mahasiswa.');
            return;
        }
        postRev(`/dosen/review/${session.uuid}/request-revision`);
    };

    // Rubric Grading Form
    const { data: gradeData, setData: setGradeData, post: postGrade, processing: gradeProcessing } = useForm({
        score_pengkajian: 85,
        score_diagnosa: 85,
        score_prosedur: 90,
        score_evaluasi: 85,
        general_notes: 'Asuhan keperawatan telah ditelaah secara komprehensif, temuan klinis sinkron dengan diagnosa 3S, dan prosedur SPO terverifikasi sah.',
    });

    const calcFinalScore = () => {
        const p = Number(gradeData.score_pengkajian) || 0;
        const d = Number(gradeData.score_diagnosa) || 0;
        const s = Number(gradeData.score_prosedur) || 0;
        const e = Number(gradeData.score_evaluasi) || 0;
        return (p * 0.25) + (d * 0.25) + (s * 0.30) + (e * 0.20);
    };

    const finalScore = calcFinalScore();

    const getGradeLetter = (score) => {
        if (score >= 85) return { letter: 'A', text: 'text-emerald-700', bg: 'bg-emerald-100' };
        if (score >= 80) return { letter: 'A-', text: 'text-emerald-600', bg: 'bg-emerald-50' };
        if (score >= 75) return { letter: 'B+', text: 'text-blue-700', bg: 'bg-blue-100' };
        if (score >= 70) return { letter: 'B', text: 'text-blue-600', bg: 'bg-blue-50' };
        if (score >= 65) return { letter: 'B-', text: 'text-yellow-700', bg: 'bg-yellow-100' };
        if (score >= 60) return { letter: 'C+', text: 'text-orange-700', bg: 'bg-orange-100' };
        return { letter: 'C', text: 'text-red-700', bg: 'bg-red-100' };
    };

    const handleApproveGrade = (e) => {
        e.preventDefault();
        if (confirm(`Konfirmasi persetujuan dan pembubuhan E-Paraf Sah Dosen CI dengan Skor Akhir ${finalScore.toFixed(1)} (${getGradeLetter(finalScore).letter})? Berkas akan terkunci permanen.`)) {
            postGrade(`/dosen/review/${session.uuid}/approve-grade`);
        }
    };

    // Batch SPO verification
    const toggleSelectSpo = (id) => {
        setSelectedSpoIds(prev =>
            prev.includes(id) ? prev.filter(item => item !== id) : [...prev, id]
        );
    };

    const selectAllUnverified = () => {
        const unverified = session.procedure_logs?.filter(p => p.is_performed && !p.is_verified).map(p => p.id) || [];
        setSelectedSpoIds(unverified);
    };

    const handleBatchVerifySPO = () => {
        if (selectedSpoIds.length === 0) {
            alert('Pilih setidaknya satu tindakan yang telah dilakukan mahasiswa untuk diverifikasi.');
            return;
        }
        router.post(`/dosen/review/${session.uuid}/verify-spo`, {
            procedure_ids: selectedSpoIds,
        }, {
            preserveScroll: true,
            onSuccess: () => setSelectedSpoIds([]),
        });
    };

    const performedProcedures = session.procedure_logs?.filter(p => p.is_performed) || [];
    const verifiedProcedures = session.procedure_logs?.filter(p => p.is_verified) || [];

    return (
        <AuthLayout title={`Telaah: ${session.patient_name}`}>
            <Head title={`Telaah Berkas: ${session.patient_name}`} />

            {/* Top Breadcrumb & Action Bar */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div className="flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/dosen/dashboard" className="hover:text-[#008D88] flex items-center gap-1 font-semibold transition-colors">
                        <ArrowLeft size={14} />
                        Antrean Telaah
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
                        Cetak Dokumen Resmi A4
                    </a>

                    {!isLocked && (
                        <div className="flex items-center gap-2">
                            <button
                                onClick={() => setActionType(actionType === 'revision' ? 'none' : 'revision')}
                                className="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                            >
                                <AlertTriangle size={14} />
                                Minta Revisi
                            </button>
                            <button
                                onClick={() => setActionType(actionType === 'grade' ? 'none' : 'grade')}
                                className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer"
                            >
                                <Award size={14} />
                                Setujui & Nilai Rubrik
                            </button>
                        </div>
                    )}
                </div>
            </div>

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
                                <span className="text-slate-400 block font-medium">Mahasiswa Pengkaji</span>
                                <span className="font-bold text-slate-900">{session.student?.name} ({session.student?.nim_nip})</span>
                            </div>
                        </div>
                    </div>

                    {session.review?.final_score ? (
                        <div className="bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-4 text-center shrink-0 min-w-44 shadow-xs">
                            <div className="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Skor Akhir Sah</div>
                            <div className="text-3xl font-black text-emerald-900 font-mono mt-1">
                                {Number(session.review.final_score).toFixed(1)}
                            </div>
                            <div className="text-xs font-bold text-emerald-700 mt-0.5">
                                Predikat: {getGradeLetter(session.review.final_score).letter}
                            </div>
                            <div className="text-[10px] text-emerald-600 mt-1">E-Paraf CI Tersertifikasi</div>
                        </div>
                    ) : (
                        <div className="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-center shrink-0 min-w-44 shadow-xs">
                            <div className="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Status Evaluasi</div>
                            <div className="text-base font-bold text-amber-900 mt-1">Menunggu Penilaian</div>
                            <div className="text-[10px] text-amber-600 mt-0.5">Dosen Pembimbing Klinik</div>
                        </div>
                    )}
                </div>
            </div>

            {/* ACTION PANEL: REVISION */}
            {actionType === 'revision' && !isLocked && (
                <div className="bg-amber-50 border-2 border-amber-300 rounded-2xl p-6 mb-6 shadow-sm">
                    <div className="flex items-center gap-2 text-amber-900 font-bold text-sm mb-2">
                        <AlertTriangle size={18} className="text-amber-600" />
                        Formulir Pengembalian Berkas Untuk Revisi
                    </div>
                    <p className="text-xs text-amber-800 mb-4">
                        Tuliskan catatan perbaikan spesifik terkait pengkajian, rumusan diagnosa 3S, atau evaluasi yang wajib diperbaiki oleh mahasiswa.
                    </p>
                    <form onSubmit={handleRequestRevision} className="space-y-3">
                        <textarea
                            rows={3}
                            value={revData.revision_notes}
                            onChange={(e) => setRevData('revision_notes', e.target.value)}
                            placeholder="Contoh: Pada pengkajian ABCDE, saturasi oksigen 90% belum sinkron dengan intervensi oksigenasi pada SIKI. Mohon perbaiki rumusan kriteria luaran SLKI dan tambahkan tindakan mandiri suctioning..."
                            className="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 bg-white text-xs outline-none focus:ring-2 focus:ring-amber-500/20"
                            required
                        />
                        <div className="flex justify-end gap-2">
                            <button
                                type="button"
                                onClick={() => setActionType('none')}
                                className="px-4 py-2 rounded-xl border border-amber-300 text-amber-900 text-xs font-semibold hover:bg-amber-100/60"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                disabled={revProcessing}
                                className="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm transition-all"
                            >
                                {revProcessing ? 'Mengirim...' : 'Kirim Catatan Revisi ke Mahasiswa'}
                            </button>
                        </div>
                    </form>
                </div>
            )}

            {/* ACTION PANEL: GRADE WITH SUB-CPMK RUBRIC */}
            {actionType === 'grade' && !isLocked && (
                <div className="bg-emerald-50/70 border-2 border-emerald-300 rounded-2xl p-6 mb-6 shadow-sm">
                    <div className="flex items-center justify-between border-b border-emerald-200/80 pb-3 mb-4">
                        <div className="flex items-center gap-2 text-emerald-950 font-bold text-sm">
                            <ShieldCheck size={20} className="text-[#008D88]" />
                            Rubrik Penilaian Terintegrasi Sub-CPMK & Pembubuhan E-Paraf Sah
                        </div>
                        <div className="flex items-center gap-3">
                            <span className="text-xs text-slate-600">Skor Akhir:</span>
                            <span className="text-xl font-black font-mono text-emerald-900">{finalScore.toFixed(1)}</span>
                            <span className={`px-2.5 py-0.5 rounded-full text-xs font-black ${getGradeLetter(finalScore).bg} ${getGradeLetter(finalScore).text}`}>
                                {getGradeLetter(finalScore).letter}
                            </span>
                        </div>
                    </div>

                    <form onSubmit={handleApproveGrade} className="space-y-4">
                        <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div className="bg-white p-3.5 rounded-xl border border-emerald-200">
                                <label className="block text-xs font-bold text-slate-700 mb-1">
                                    1. Pengkajian Klinis (Bobot 25%)
                                </label>
                                <input
                                    type="number"
                                    min={0}
                                    max={100}
                                    value={gradeData.score_pengkajian}
                                    onChange={(e) => setGradeData('score_pengkajian', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-bold text-center"
                                />
                                <span className="text-[10px] text-slate-400 mt-1 block text-center">Kelengkapan data anamnesis & fisik</span>
                            </div>

                            <div className="bg-white p-3.5 rounded-xl border border-emerald-200">
                                <label className="block text-xs font-bold text-slate-700 mb-1">
                                    2. Analisa Diagnosa 3S (Bobot 25%)
                                </label>
                                <input
                                    type="number"
                                    min={0}
                                    max={100}
                                    value={gradeData.score_diagnosa}
                                    onChange={(e) => setGradeData('score_diagnosa', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-bold text-center"
                                />
                                <span className="text-[10px] text-slate-400 mt-1 block text-center">Sinkronisasi SDKI, SLKI, SIKI</span>
                            </div>

                            <div className="bg-white p-3.5 rounded-xl border border-emerald-200">
                                <label className="block text-xs font-bold text-slate-700 mb-1">
                                    3. Keterampilan SPO (Bobot 30%)
                                </label>
                                <input
                                    type="number"
                                    min={0}
                                    max={100}
                                    value={gradeData.score_prosedur}
                                    onChange={(e) => setGradeData('score_prosedur', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-bold text-center"
                                />
                                <span className="text-[10px] text-slate-400 mt-1 block text-center">Prosedur tindakan & kepatuhan SPO</span>
                            </div>

                            <div className="bg-white p-3.5 rounded-xl border border-emerald-200">
                                <label className="block text-xs font-bold text-slate-700 mb-1">
                                    4. Evaluasi & SBAR (Bobot 20%)
                                </label>
                                <input
                                    type="number"
                                    min={0}
                                    max={100}
                                    value={gradeData.score_evaluasi}
                                    onChange={(e) => setGradeData('score_evaluasi', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-bold text-center"
                                />
                                <span className="text-[10px] text-slate-400 mt-1 block text-center">Catatan SOAP & ketepatan handover</span>
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Umpan Balik & Catatan Penguatan Dosen Pembimbing</label>
                            <textarea
                                rows={2}
                                value={gradeData.general_notes}
                                onChange={(e) => setGradeData('general_notes', e.target.value)}
                                className="w-full px-3.5 py-2 rounded-xl border border-emerald-300 bg-white text-xs outline-none"
                            />
                        </div>

                        <div className="flex items-center justify-between pt-2 border-t border-emerald-200/80">
                            <div className="flex items-center gap-2 text-xs text-slate-600">
                                <CheckCircle2 size={16} className="text-[#008D88]" />
                                <span>Menyetujui asuhan ini mengesahkan portofolio klinis dan mengunci data secara permanen.</span>
                            </div>
                            <div className="flex gap-2">
                                <button
                                    type="button"
                                    onClick={() => setActionType('none')}
                                    className="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-white"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={gradeProcessing}
                                    className="px-6 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs transition-colors"
                                >
                                    {gradeProcessing ? 'Memproses...' : 'Bubuhkan E-Paraf & Sahkan Penilaian'}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            )}

            {/* Navigation Tabs */}
            <div className="border-b border-slate-200 mb-6 flex items-center gap-2 overflow-x-auto text-sm font-semibold">
                <button
                    onClick={() => setActiveTab('assessment')}
                    className={`px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-xl ${
                        activeTab === 'assessment' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                    }`}
                >
                    <Stethoscope size={16} />
                    <span>1. Pengkajian Klinis ({stageType.toUpperCase()})</span>
                </button>
                <button
                    onClick={() => setActiveTab('care_plans')}
                    className={`px-4 py-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer rounded-t-xl ${
                        activeTab === 'care_plans' ? 'border-[#008D88] text-[#008D88] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                    }`}
                >
                    <ClipboardList size={16} />
                    <span>2. Rencana 3S ({session.care_plans?.length || 0})</span>
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
                    <span>4. Tindakan SPO & E-Paraf ({verifiedProcedures.length}/{performedProcedures.length})</span>
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

            {/* TAB 1: PENGKAJIAN KLINIS */}
            {activeTab === 'assessment' && (
                <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-6">
                    <div className="border-b border-slate-100 pb-3">
                        <h3 className="font-bold text-slate-900 text-base">
                            Hasil Temuan Pengkajian Mahasiswa ({stageType.toUpperCase()})
                        </h3>
                        <p className="text-xs text-slate-500 mt-0.5">Telaah data subjektif, objektif, dan instrumen kurikulum</p>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div className="p-4 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                            <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Keluhan Utama</span>
                            <p className="text-slate-900 text-sm font-semibold">{payload.keluhan_utama || '-'}</p>
                        </div>
                        <div className="p-4 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                            <span className="font-bold text-slate-500 uppercase tracking-wider block mb-1">Riwayat Penyakit Sekarang & Kronologi Onset</span>
                            <p className="text-slate-800 leading-relaxed">{payload.riwayat_penyakit || '-'}</p>
                        </div>
                    </div>

                    {/* Stage Specific Views */}
                    {stageType === 'kgd' && (
                        <div className="space-y-4 pt-2">
                            <div className="font-bold text-xs uppercase tracking-wider text-red-600 flex items-center gap-1.5">
                                <span className="w-2 h-2 rounded-full bg-red-600"></span>
                                Data Survei Primer (ABCDE)
                            </div>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                                <div className="p-3.5 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block mb-1">Airway & Cervical</span>
                                    <p className="font-semibold text-slate-900">{payload.airway_status || 'Paten'}</p>
                                    <p className="text-slate-600 mt-0.5">Suara: {payload.airway_suara || 'Bersih'}</p>
                                    <p className="text-slate-600">Cervical Collar: {payload.cervical_collar || 'Tidak'}</p>
                                </div>
                                <div className="p-3.5 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block mb-1">Breathing & O2</span>
                                    <p className="font-semibold text-slate-900">RR: {payload.breathing_rr || 20} x/m | SpO2: {payload.breathing_spo2 || 98}%</p>
                                    <p className="text-slate-600 mt-0.5">Pola: {payload.breathing_pola || 'Reguler'}</p>
                                    <p className="text-slate-600">Terapi O2: {payload.breathing_o2 || '-'}</p>
                                </div>
                                <div className="p-3.5 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block mb-1">Circulation & Perdarahan</span>
                                    <p className="font-semibold text-slate-900">TD: {payload.circulation_td || '120/80'} | Nadi: {payload.circulation_nadi || 80}</p>
                                    <p className="text-slate-600 mt-0.5">Akral: {payload.circulation_akral || '-'}</p>
                                    <p className="text-slate-600">IV Line: {payload.circulation_iv || '-'}</p>
                                </div>
                                <div className="p-3.5 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block mb-1">Disability & Kesadaran</span>
                                    <p className="font-semibold text-slate-900">GCS: {payload.disability_gcs_e || 4}E {payload.disability_gcs_m || 6}M {payload.disability_gcs_v || 5}V</p>
                                    <p className="text-slate-600 mt-0.5">Kesadaran: {payload.disability_kesadaran || 'Compos Mentis'}</p>
                                    <p className="text-slate-600">Pupil: {payload.disability_pupil || '-'}</p>
                                </div>
                                <div className="p-3.5 rounded-xl border border-slate-200 sm:col-span-2">
                                    <span className="font-bold text-slate-500 block mb-1">Exposure & AMPLE History</span>
                                    <p className="text-slate-800"><span className="font-semibold">Jejas:</span> {payload.exposure_jejas || '-'}</p>
                                    <p className="text-slate-800 mt-0.5"><span className="font-semibold">AMPLE:</span> {payload.ample_a || '-'}</p>
                                </div>
                            </div>
                        </div>
                    )}

                    {stageType === 'kdm' && (
                        <div className="space-y-4 pt-2">
                            <div className="font-bold text-xs uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                                <span className="w-2 h-2 rounded-full bg-emerald-600"></span>
                                Data 9 Domain Kebutuhan Dasar Virginia Henderson
                            </div>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">1. Oksigenasi</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kdm_oksigenasi || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">2. Nutrisi & Diet</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kdm_nutrisi_keluhan || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">3. Cairan & Elektrolit</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kdm_cairan_balance || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">4. Eliminasi Urin & Fekal</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kdm_eliminasi_bak || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">5. Aktivitas & Barthel Index</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kdm_aktivitas_barthel || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">6. Personal Hygiene & Integritas Kulit</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kdm_hygiene || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">7. Termoregulasi</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kdm_termoregulasi || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">8. Kenyamanan & Nyeri PQRST</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kdm_nyeri_pqrst || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200 sm:col-span-2">
                                    <span className="font-bold text-slate-500 block">9. Psikososial & Penerapan 6 Benar Obat</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kdm_psikososial || '-'}</p>
                                    <p className="text-emerald-700 font-semibold mt-1">{payload.kdm_enam_benar_obat || '-'}</p>
                                </div>
                            </div>
                        </div>
                    )}

                    {stageType === 'kmb' && (
                        <div className="space-y-4 pt-2">
                            <div className="font-bold text-xs uppercase tracking-wider text-blue-700 flex items-center gap-1.5">
                                <span className="w-2 h-2 rounded-full bg-blue-600"></span>
                                Data 9 Sistem Tubuh (B1-B6) & Hasil Diagnostik
                            </div>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">B1 - Breathing</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kmb_b1_breathing || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">B2 - Blood</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kmb_b2_blood || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">B3 - Brain</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kmb_b3_brain || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">B4 - Bladder</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kmb_b4_bladder || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">B5 - Bowel</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kmb_b5_bowel || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200">
                                    <span className="font-bold text-slate-500 block">B6 - Bone</span>
                                    <p className="text-slate-800 mt-0.5">{payload.kmb_b6_bone || '-'}</p>
                                </div>
                                <div className="p-3 rounded-xl border border-slate-200 sm:col-span-2">
                                    <span className="font-bold text-slate-500 block">Data Lab & Penunjang (EKG, Ro Thorax)</span>
                                    <p className="text-slate-800 mt-0.5"><span className="font-semibold">Lab:</span> {payload.kmb_diagnostik_lab || '-'}</p>
                                    <p className="text-slate-800 mt-0.5"><span className="font-semibold">Penunjang:</span> {payload.kmb_diagnostik_penunjang || '-'}</p>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            )}

            {/* TAB 2: RENCANA ASUHAN 3S */}
            {activeTab === 'care_plans' && (
                <div className="space-y-4">
                    {session.care_plans?.length === 0 ? (
                        <div className="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-sm">
                            Mahasiswa belum menginput rencana asuhan keperawatan 3S.
                        </div>
                    ) : (
                        session.care_plans?.map((plan, idx) => (
                            <div key={plan.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                                <div className="flex items-center gap-2 mb-3">
                                    <span className="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#008D88]/10 text-[#008D88]">
                                        Prioritas #{plan.priority_order || idx + 1}
                                    </span>
                                    <h4 className="font-bold text-slate-900 text-sm">
                                        {plan.sdki?.code} — {plan.sdki?.title}
                                    </h4>
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
            )}

            {/* TAB 3: PEMANTAUAN TTV */}
            {activeTab === 'vitals' && (
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
                                    <th className="py-3 px-4">Catatan Evaluasi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {session.vital_signs?.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="py-8 text-center text-slate-400 text-xs">
                                            Tidak ada data pemantauan tanda vital berkala yang tercatat.
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
            )}

            {/* TAB 4: CHECKLIST TINDAKAN SPO & BATCH E-PARAF */}
            {activeTab === 'procedures' && (
                <div className="space-y-4">
                    <div className="bg-white rounded-2xl border border-slate-200 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                        <div>
                            <div className="font-bold text-slate-900 text-sm">Validasi & Pembubuhan E-Paraf Tindakan Mahasiswa</div>
                            <div className="text-xs text-slate-500">
                                {performedProcedures.length} tindakan dilakukan oleh mahasiswa, {verifiedProcedures.length} telah diverifikasi e-paraf.
                            </div>
                        </div>

                        {!isLocked && (
                            <div className="flex items-center gap-2">
                                <button
                                    onClick={selectAllUnverified}
                                    className="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer"
                                >
                                    Pilih Semua yang Dilakukan
                                </button>
                                <button
                                    onClick={handleBatchVerifySPO}
                                    disabled={selectedSpoIds.length === 0}
                                    className="px-4 py-1.5 rounded-lg bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs disabled:opacity-50 transition-all flex items-center gap-1.5 cursor-pointer"
                                >
                                    <ShieldCheck size={14} />
                                    Batch E-Paraf ({selectedSpoIds.length})
                                </button>
                            </div>
                        )}
                    </div>

                    <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs">
                                <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <tr>
                                        {!isLocked && <th className="py-3 px-4 w-10 text-center">Pilih</th>}
                                        <th className="py-3 px-4">Prosedur SPO</th>
                                        <th className="py-3 px-4">Sub-CPMK</th>
                                        <th className="py-3 px-4">Status Mahasiswa</th>
                                        <th className="py-3 px-4 text-right">Validasi E-Paraf</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {session.procedure_logs?.map((p) => (
                                        <tr key={p.id} className="hover:bg-slate-50/80 transition-colors">
                                            {!isLocked && (
                                                <td className="py-3 px-4 text-center">
                                                    {p.is_performed && !p.is_verified ? (
                                                        <input
                                                            type="checkbox"
                                                            checked={selectedSpoIds.includes(p.id)}
                                                            onChange={() => toggleSelectSpo(p.id)}
                                                            className="w-4 h-4 rounded text-[#008D88] focus:ring-[#008D88] cursor-pointer"
                                                        />
                                                    ) : (
                                                        <span className="text-slate-300">-</span>
                                                    )}
                                                </td>
                                            )}
                                            <td className="py-3 px-4">
                                                <div className="font-bold text-slate-900">{p.procedure?.procedure_name}</div>
                                                <div className="text-[11px] text-slate-400">{p.procedure?.domain_category}</div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                                    {p.procedure?.sub_cpmk_reference}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                {p.is_performed ? (
                                                    <span className="font-semibold text-emerald-700 flex items-center gap-1">
                                                        <Check size={13} />
                                                        Dilakukan ({p.performed_at ? formatDateTime(p.performed_at) : '-'})
                                                    </span>
                                                ) : (
                                                    <span className="text-slate-400">Belum Dilakukan</span>
                                                )}
                                            </td>
                                            <td className="py-3 px-4 text-right">
                                                {p.is_verified ? (
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                        <CheckCircle2 size={13} className="text-emerald-600" />
                                                        Terparaf Sah
                                                    </span>
                                                ) : p.is_performed ? (
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                                        <Clock size={13} className="text-amber-600" />
                                                        Menunggu Paraf
                                                    </span>
                                                ) : (
                                                    <span className="text-slate-300">-</span>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            )}

            {/* TAB 5: EVALUASI & SBAR */}
            {activeTab === 'evaluations' && (
                <div className="space-y-4">
                    {session.evaluations_and_handovers?.length === 0 ? (
                        <div className="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-sm">
                            Tidak ada catatan evaluasi atau handover yang dibuat mahasiswa.
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
            )}
        </AuthLayout>
    );
}
