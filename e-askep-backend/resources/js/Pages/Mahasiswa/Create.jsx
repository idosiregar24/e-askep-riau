import { Head, Link, useForm, usePage } from '@inertiajs/react';
import AuthLayout from '@/Layouts/AuthLayout';
import { ArrowLeft, User, BookOpen, Stethoscope, AlertCircle } from 'lucide-react';

export default function CreateKasus({ courses, dosens, defaultDosenId, defaultCourseId }) {
    const { data, setData, post, processing, errors } = useForm({
        course_id: defaultCourseId ?? '',
        mentor_dosen_id: defaultDosenId ?? '',
        patient_name: '',
        medical_record_no: '',
        age: '',
        gender: '',
        triage_category: 'hijau',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/mahasiswa/kasus');
    };

    const triageOptions = [
        { value: 'merah', label: 'Merah', desc: 'Kritis / Immediate', bg: 'bg-red-50 border-red-300 text-red-700' },
        { value: 'kuning', label: 'Kuning', desc: 'Urgent / Delayed', bg: 'bg-yellow-50 border-yellow-300 text-yellow-700' },
        { value: 'hijau', label: 'Hijau', desc: 'Minor / Minimal', bg: 'bg-green-50 border-green-300 text-green-700' },
        { value: 'hitam', label: 'Hitam', desc: 'Expectant / DOA', bg: 'bg-gray-100 border-gray-400 text-gray-700' },
    ];

    return (
        <AuthLayout title="Buat Kasus Pasien Baru">
            <Head title="Kasus Pasien Baru" />

            <div className="max-w-2xl mx-auto">
                {/* Back */}
                <Link href="/mahasiswa/dashboard" className="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800 transition-colors mb-5">
                    <ArrowLeft size={16} />
                    Kembali ke Dashboard
                </Link>

                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    {/* Header */}
                    <div className="p-6 border-b border-slate-100 bg-slate-50">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-[#008D88] flex items-center justify-center text-white shadow-xs">
                                <Stethoscope size={20} />
                            </div>
                            <div>
                                <h2 className="font-black text-slate-900 text-base">Data Pasien Baru</h2>
                                <p className="text-xs text-slate-500">Isi data identitas pasien dan informasi kasus klinis</p>
                            </div>
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="p-6 space-y-5">
                        {/* Course */}
                        <div>
                            <label className="block text-sm font-semibold text-slate-700 mb-1.5">Mata Kuliah Praktik</label>
                            <select
                                value={data.course_id}
                                onChange={(e) => setData('course_id', e.target.value)}
                                className={`w-full px-3 py-2.5 rounded-xl border text-sm outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 transition-all ${errors.course_id ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                            >
                                <option value="">-- Pilih Mata Kuliah --</option>
                                {courses?.map((c) => (
                                    <option key={c.id} value={c.id}>{c.code} — {c.name}</option>
                                ))}
                            </select>
                            {errors.course_id && <p className="mt-1 text-xs text-red-600 flex gap-1"><AlertCircle size={12} className="mt-0.5 shrink-0" />{errors.course_id}</p>}
                        </div>

                        {/* Mentor Dosen */}
                        <div>
                            <label className="block text-sm font-semibold text-slate-700 mb-1.5">Dosen Pembimbing Klinik (CI)</label>
                            <select
                                value={data.mentor_dosen_id}
                                onChange={(e) => setData('mentor_dosen_id', e.target.value)}
                                className={`w-full px-3 py-2.5 rounded-xl border text-sm outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 transition-all ${errors.mentor_dosen_id ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                            >
                                <option value="">-- Pilih Dosen CI --</option>
                                {dosens?.map((d) => (
                                    <option key={d.id} value={d.id}>{d.name} ({d.nim_nip})</option>
                                ))}
                            </select>
                            {errors.mentor_dosen_id && <p className="mt-1 text-xs text-red-600 flex gap-1"><AlertCircle size={12} className="mt-0.5 shrink-0" />{errors.mentor_dosen_id}</p>}
                        </div>

                        <div className="border-t border-slate-100 pt-5">
                            <h3 className="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                                <User size={16} className="text-[#008D88]" />
                                Identitas Pasien
                            </h3>

                            {/* Patient Name */}
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700 mb-1.5">Nama Pasien</label>
                                    <input
                                        type="text"
                                        value={data.patient_name}
                                        onChange={(e) => setData('patient_name', e.target.value)}
                                        placeholder="Nama lengkap pasien"
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 transition-all ${errors.patient_name ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                                    />
                                    {errors.patient_name && <p className="mt-1 text-xs text-red-600">{errors.patient_name}</p>}
                                </div>

                                {/* Medical Record No */}
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Rekam Medis</label>
                                    <input
                                        type="text"
                                        value={data.medical_record_no}
                                        onChange={(e) => setData('medical_record_no', e.target.value)}
                                        placeholder="Contoh: RM-2024-001234"
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 transition-all font-mono ${errors.medical_record_no ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                                    />
                                    {errors.medical_record_no && <p className="mt-1 text-xs text-red-600">{errors.medical_record_no}</p>}
                                </div>

                                {/* Age & Gender */}
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-semibold text-slate-700 mb-1.5">Usia (Tahun)</label>
                                        <input
                                            type="number"
                                            min="0"
                                            max="150"
                                            value={data.age}
                                            onChange={(e) => setData('age', e.target.value)}
                                            placeholder="Contoh: 45"
                                            className={`w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 transition-all ${errors.age ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                                        />
                                        {errors.age && <p className="mt-1 text-xs text-red-600">{errors.age}</p>}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin</label>
                                        <select
                                            value={data.gender}
                                            onChange={(e) => setData('gender', e.target.value)}
                                            className={`w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 transition-all ${errors.gender ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                                        >
                                            <option value="">-- Pilih --</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                        {errors.gender && <p className="mt-1 text-xs text-red-600">{errors.gender}</p>}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Triage */}
                        <div>
                            <label className="block text-sm font-semibold text-slate-700 mb-2">Kategori Triase</label>
                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                {triageOptions.map((t) => (
                                    <button
                                        key={t.value}
                                        type="button"
                                        onClick={() => setData('triage_category', t.value)}
                                        className={`p-3 rounded-xl border-2 text-center transition-all duration-200 ${
                                            data.triage_category === t.value
                                                ? t.bg + ' shadow-md scale-[1.02]'
                                                : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300'
                                        }`}
                                    >
                                        <div className="font-bold text-sm">{t.label}</div>
                                        <div className="text-[10px] mt-0.5 opacity-75">{t.desc}</div>
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Submit */}
                        <div className="flex gap-3 pt-2">
                            <Link
                                href="/mahasiswa/dashboard"
                                className="flex-1 flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex-1 flex items-center justify-center gap-2 bg-[#008D88] hover:bg-[#00736F] disabled:opacity-60 text-white py-2.5 rounded-xl text-sm font-bold transition-colors shadow-xs"
                            >
                                {processing ? (
                                    <>
                                        <svg className="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        Menyimpan...
                                    </>
                                ) : 'Buat Kasus & Mulai Pengkajian'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthLayout>
    );
}
