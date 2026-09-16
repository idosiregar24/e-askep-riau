import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import {
    ArrowLeft, Printer, AlertTriangle, CheckCircle2,
    Activity, ClipboardList, Stethoscope, HeartPulse, FileSpreadsheet,
    Clock, ShieldCheck, Check, Send, Award, FileText, User, ChevronRight
} from 'lucide-react';
import { statusLabel, triageLabel, formatDate, formatDateTime } from '@/utils';

export default function DosenReview({ session, instrument }) {
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

    // ── Instrumen Penilaian Klinik (KDM / KGD / KMB) ────────────────────────
    // Skor 0-4 tiap aspek pada lembar instrumen resmi otomatis mengisi rubrik
    // Sub-CPMK, nilai akhir, dan kategori kelulusan secara langsung.
    const instrumentItems = instrument?.items || [];
    const maxItemScore = instrument?.max_item_score ?? 4;
    const instrumentMaxScore = instrument?.max_score ?? instrumentItems.length * maxItemScore;
    const passingScore = instrument?.passing_score ?? 75;
    const rubricLabels = instrument?.rubric_labels || {};
    const rubricWeights = instrument?.rubric_weights || {};

    const buildInitialScores = () => {
        const saved = instrument?.saved_scores || {};
        return instrumentItems.reduce((acc, item) => {
            const value = saved[item.no] ?? saved[String(item.no)];
            acc[item.no] = value === undefined || value === null ? '' : String(value);
            return acc;
        }, {});
    };

    const buildInitialNotes = () => {
        const saved = instrument?.saved_notes || {};
        return instrumentItems.reduce((acc, item) => {
            acc[item.no] = saved[item.no] ?? saved[String(item.no)] ?? '';
            return acc;
        }, {});
    };

    const [itemScores, setItemScores] = useState(buildInitialScores);
    const [itemNotes, setItemNotes] = useState(buildInitialNotes);
    const [identity, setIdentity] = useState(() => ({ ...(instrument?.identity || {}) }));
    const [generalNotes, setGeneralNotes] = useState('');
    const [gradeProcessing, setGradeProcessing] = useState(false);

    const setItemScore = (no, value) => setItemScores(prev => ({ ...prev, [no]: value }));
    const setItemNote = (no, value) => setItemNotes(prev => ({ ...prev, [no]: value }));

    // Rekapitulasi instrumen dihitung ulang pada tiap perubahan skor.
    const instrumentSummary = useMemo(() => {
        const groups = {};
        let total = 0;
        let filled = 0;

        instrumentItems.forEach((item) => {
            const raw = itemScores[item.no];
            const hasValue = raw !== '' && raw !== undefined && raw !== null;
            const score = hasValue ? Math.max(0, Math.min(maxItemScore, Number(raw) || 0)) : 0;

            if (hasValue) filled += 1;
            total += score;

            groups[item.group] = groups[item.group] || { sum: 0, count: 0 };
            groups[item.group].sum += score;
            groups[item.group].count += 1;
        });

        const rubric = Object.keys(rubricWeights).reduce((acc, group) => {
            const g = groups[group];
            acc[group] = g && g.count > 0
                ? Math.round((g.sum / (g.count * maxItemScore)) * 10000) / 100
                : 0;
            return acc;
        }, {});

        const nilaiAkhir = instrumentMaxScore > 0
            ? Math.round((total / instrumentMaxScore) * 10000) / 100
            : 0;

        const weighted = Object.entries(rubricWeights)
            .reduce((sum, [group, weight]) => sum + (rubric[group] || 0) * weight, 0);

        return {
            total,
            filled,
            nilaiAkhir,
            rubric,
            finalScore: Math.round(weighted * 100) / 100,
            kategori: nilaiAkhir >= passingScore ? 'Kompeten' : 'Belum Kompeten',
            isComplete: filled === instrumentItems.length && instrumentItems.length > 0,
        };
    }, [itemScores, instrumentItems, maxItemScore, instrumentMaxScore, passingScore, rubricWeights]);

    const finalScore = instrumentSummary.finalScore;

    // Query string agar dosen dapat mengunduh pratinjau sebelum berkas disahkan.
    const exportQuery = useMemo(() => {
        const params = new URLSearchParams();
        instrumentItems.forEach((item) => {
            params.append('scores[' + item.no + ']', itemScores[item.no] === '' ? '0' : String(itemScores[item.no]));
            if (itemNotes[item.no]) {
                params.append('notes[' + item.no + ']', itemNotes[item.no]);
            }
        });
        return params.toString();
    }, [itemScores, itemNotes, instrumentItems]);

    const exportBase = auth?.user?.role === 'mahasiswa'
        ? `/mahasiswa/kasus/${session.uuid}/instrumen`
        : `/dosen/review/${session.uuid}/instrumen`;

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

        if (!instrumentSummary.isComplete) {
            alert(`Seluruh ${instrumentItems.length} aspek instrumen penilaian ${instrument?.stage_label || ''} wajib diberi skor 0-4 sebelum berkas disahkan.`);
            return;
        }

        const confirmed = confirm(
            `Konfirmasi pengesahan berkas dengan Instrumen ${instrument?.stage_label}: `
            + `${instrumentSummary.total}/${instrumentMaxScore} (Nilai ${instrumentSummary.nilaiAkhir.toFixed(2)} — ${instrumentSummary.kategori}), `
            + `Skor Akhir Portofolio ${finalScore.toFixed(1)} (${getGradeLetter(finalScore).letter}). Berkas akan terkunci permanen.`
        );
        if (!confirmed) return;

        setGradeProcessing(true);
        router.post(`/dosen/review/${session.uuid}/approve-grade`, {
            instrument_scores: Object.fromEntries(
                instrumentItems.map(item => [item.no, Number(itemScores[item.no]) || 0])
            ),
            instrument_notes: itemNotes,
            identity,
            general_notes: generalNotes,
        }, {
            onFinish: () => setGradeProcessing(false),
        });
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

                    {/* Instrumen yang sudah disahkan tetap dapat diunduh kembali */}
                    {isLocked && (
                        <div className="flex items-center gap-2">
                            <a
                                href={`${exportBase}/pdf`}
                                className="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all"
                            >
                                <FileText size={15} className="text-red-600" />
                                Instrumen PDF
                            </a>
                            <a
                                href={`${exportBase}/word`}
                                className="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all"
                            >
                                <FileSpreadsheet size={15} className="text-blue-700" />
                                Instrumen Word
                            </a>
                        </div>
                    )}

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
                                Isi Instrumen Penilaian {instrument?.stage_label}
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

            {/* ACTION PANEL: INSTRUMEN PENILAIAN KLINIK (mengisi rubrik Sub-CPMK otomatis) */}
            {actionType === 'grade' && !isLocked && (
                <div className="bg-white border-2 border-[#008D88]/40 rounded-2xl mb-6 shadow-sm overflow-hidden">

                    {/* Kop instrumen resmi */}
                    <div className="px-6 py-4 border-b border-slate-100 bg-[#E6F5F4]/60">
                        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div className="flex items-start gap-3">
                                <ShieldCheck size={22} className="text-[#008D88] mt-0.5 shrink-0" />
                                <div>
                                    <h3 className="text-sm font-black text-slate-900 uppercase tracking-tight">{instrument?.title}</h3>
                                    <p className="text-xs text-slate-600 mt-0.5">{instrument?.subtitle}</p>
                                    <p className="text-[11px] text-slate-500 mt-0.5">{instrument?.faculty}</p>
                                </div>
                            </div>

                            <div className="flex items-center gap-2 shrink-0">
                                <a
                                    href={`${exportBase}/pdf?${exportQuery}`}
                                    className="px-3 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all"
                                >
                                    <FileText size={14} className="text-red-600" />
                                    Ekspor PDF
                                </a>
                                <a
                                    href={`${exportBase}/word?${exportQuery}`}
                                    className="px-3 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all"
                                >
                                    <FileSpreadsheet size={14} className="text-blue-700" />
                                    Ekspor Word
                                </a>
                            </div>
                        </div>
                    </div>

                    <form onSubmit={handleApproveGrade}>

                        {/* Identitas instrumen — terisi otomatis dari berkas, tetap dapat disunting */}
                        <div className="px-6 py-4 border-b border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {Object.entries(identity).map(([label, value]) => (
                                <div key={label} className="flex items-center gap-2">
                                    <span className="text-[11px] font-bold text-slate-500 w-40 shrink-0">{label}</span>
                                    <input
                                        type="text"
                                        value={value}
                                        onChange={(e) => setIdentity(prev => ({ ...prev, [label]: e.target.value }))}
                                        className="flex-1 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white text-xs outline-none focus:ring-2 focus:ring-[#008D88]/20"
                                    />
                                </div>
                            ))}
                        </div>

                        {/* Tabel aspek penilaian berskala 0-4 */}
                        <div className="px-6 py-4">
                            <div className="flex items-center justify-between mb-3">
                                <h4 className="text-xs font-black text-slate-800 uppercase tracking-wide">Kriteria Penilaian</h4>
                                <span className={`text-[11px] font-bold px-2.5 py-1 rounded-full ${
                                    instrumentSummary.isComplete
                                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                        : 'bg-[#FEF9C3] text-[#CA8A04] border border-[#FDE68A]'
                                }`}>
                                    {instrumentSummary.filled}/{instrumentItems.length} aspek terisi
                                </span>
                            </div>

                            <div className="overflow-x-auto border border-slate-200 rounded-xl">
                                <table className="w-full text-xs">
                                    <thead className="bg-slate-50 text-slate-600">
                                        <tr>
                                            <th className="px-3 py-2 text-center font-bold w-10">No</th>
                                            <th className="px-3 py-2 text-left font-bold">Aspek yang Dinilai</th>
                                            <th className="px-3 py-2 text-center font-bold w-56">Skor (0&ndash;{maxItemScore})</th>
                                            <th className="px-3 py-2 text-left font-bold w-56">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100">
                                        {instrumentItems.map((item) => (
                                            <tr key={item.no} className="align-top hover:bg-slate-50/60">
                                                <td className="px-3 py-2.5 text-center font-bold text-slate-500">{item.no}</td>
                                                <td className="px-3 py-2.5">
                                                    <span className="text-slate-800">{item.aspect}</span>
                                                    <span className="block mt-0.5 text-[10px] font-semibold text-[#008D88]">
                                                        &rarr; {rubricLabels[item.group] || item.group}
                                                    </span>
                                                </td>
                                                <td className="px-3 py-2.5">
                                                    <div className="flex items-center justify-center gap-1">
                                                        {Array.from({ length: maxItemScore + 1 }, (_, n) => (
                                                            <button
                                                                key={n}
                                                                type="button"
                                                                onClick={() => setItemScore(item.no, String(n))}
                                                                title={instrument?.score_legend?.[n]}
                                                                className={`w-8 h-8 rounded-lg border text-xs font-black transition-all cursor-pointer ${
                                                                    String(itemScores[item.no]) === String(n)
                                                                        ? 'bg-[#008D88] border-[#008D88] text-white shadow-xs'
                                                                        : 'bg-white border-slate-200 text-slate-500 hover:border-[#008D88]/50 hover:text-[#008D88]'
                                                                }`}
                                                            >
                                                                {n}
                                                            </button>
                                                        ))}
                                                    </div>
                                                </td>
                                                <td className="px-3 py-2.5">
                                                    <input
                                                        type="text"
                                                        value={itemNotes[item.no] || ''}
                                                        onChange={(e) => setItemNote(item.no, e.target.value)}
                                                        maxLength={500}
                                                        placeholder="Catatan penilai"
                                                        className="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white text-xs outline-none focus:ring-2 focus:ring-[#008D88]/20"
                                                    />
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Keterangan skala skor */}
                            <div className="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-slate-500">
                                <span className="font-bold text-slate-600">Keterangan Skor:</span>
                                {Object.entries(instrument?.score_legend || {}).map(([value, meaning]) => (
                                    <span key={value}><strong className="text-slate-700">{value}</strong> = {meaning}</span>
                                ))}
                            </div>
                        </div>

                        {/* Rekapitulasi instrumen + rubrik Sub-CPMK yang terisi otomatis */}
                        <div className="px-6 pb-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

                            <div className="lg:col-span-2 border border-slate-200 rounded-xl overflow-hidden">
                                <div className="px-4 py-2 bg-slate-50 text-[11px] font-black text-slate-700 uppercase tracking-wide border-b border-slate-200">
                                    Rubrik Sub-CPMK (terisi otomatis dari instrumen)
                                </div>
                                <div className="grid grid-cols-2 sm:grid-cols-4 divide-x divide-slate-100">
                                    {Object.entries(rubricLabels).map(([group, label]) => (
                                        <div key={group} className="p-3 text-center">
                                            <div className="text-[10px] font-semibold text-slate-500 leading-tight min-h-8">{label}</div>
                                            <div className="text-lg font-black font-mono text-slate-900 mt-1">
                                                {(instrumentSummary.rubric[group] ?? 0).toFixed(1)}
                                            </div>
                                            <div className="text-[10px] text-[#008D88] font-bold">
                                                Bobot {Math.round((rubricWeights[group] || 0) * 100)}%
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                <div className="border-t border-slate-200 divide-y divide-slate-100 text-xs">
                                    <div className="flex items-center justify-between px-4 py-2">
                                        <span className="font-semibold text-slate-600">Total Skor (maksimal {instrumentMaxScore})</span>
                                        <span className="font-black font-mono text-slate-900">{instrumentSummary.total}</span>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-2">
                                        <span className="font-semibold text-slate-600">Nilai Akhir = (Total Skor / {instrumentMaxScore}) &times; 100</span>
                                        <span className="font-black font-mono text-slate-900">{instrumentSummary.nilaiAkhir.toFixed(2)}</span>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-2">
                                        <span className="font-semibold text-slate-600">
                                            Kategori Kelulusan (Kompeten jika Nilai &ge; {passingScore})
                                        </span>
                                        <span className={`px-2.5 py-0.5 rounded-full text-[11px] font-black border ${
                                            instrumentSummary.kategori === 'Kompeten'
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                : 'bg-red-50 text-red-700 border-red-200'
                                        }`}>
                                            {instrumentSummary.kategori}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div className="border-2 border-[#008D88]/30 rounded-xl bg-[#E6F5F4]/40 p-4 flex flex-col justify-center text-center">
                                <div className="text-[10px] font-black text-[#00736F] uppercase tracking-wider">Skor Akhir Portofolio</div>
                                <div className="text-4xl font-black font-mono text-slate-900 mt-1">{finalScore.toFixed(1)}</div>
                                <div className={`mx-auto mt-2 px-3 py-0.5 rounded-full text-xs font-black ${getGradeLetter(finalScore).bg} ${getGradeLetter(finalScore).text}`}>
                                    Predikat {getGradeLetter(finalScore).letter}
                                </div>
                                <div className="text-[10px] text-slate-500 mt-2 leading-snug">
                                    Dihitung berbobot dari keempat kelompok rubrik Sub-CPMK.
                                </div>
                            </div>
                        </div>

                        {/* Umpan balik & pengesahan */}
                        <div className="px-6 pb-5 space-y-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">
                                    Umpan Balik &amp; Catatan Penguatan Dosen Pembimbing
                                </label>
                                <textarea
                                    rows={2}
                                    value={generalNotes}
                                    onChange={(e) => setGeneralNotes(e.target.value)}
                                    maxLength={2000}
                                    placeholder="Catatan penguatan, apresiasi capaian, dan rekomendasi pengembangan kompetensi klinis mahasiswa."
                                    className="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs outline-none focus:ring-2 focus:ring-[#008D88]/20"
                                />
                            </div>

                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100">
                                <div className="flex items-start gap-2 text-xs text-slate-600">
                                    <CheckCircle2 size={16} className="text-[#008D88] mt-0.5 shrink-0" />
                                    <span>Menyetujui asuhan ini mengesahkan portofolio klinis dan mengunci data secara permanen.</span>
                                </div>
                                <div className="flex gap-2 shrink-0">
                                    <button
                                        type="button"
                                        onClick={() => setActionType('none')}
                                        className="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 cursor-pointer"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={gradeProcessing || !instrumentSummary.isComplete}
                                        className="px-6 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold shadow-xs transition-colors cursor-pointer"
                                    >
                                        {gradeProcessing ? 'Memproses...' : 'Bubuhkan E-Paraf & Sahkan Penilaian'}
                                    </button>
                                </div>
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
