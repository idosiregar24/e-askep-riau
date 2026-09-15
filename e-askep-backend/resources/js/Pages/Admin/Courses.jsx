import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import { BookOpen, Plus, Edit2, Trash2, X } from 'lucide-react';

export default function AdminCourses({ courses }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [editingCourse, setEditingCourse] = useState(null);

    // Create Form
    const { data: addData, setData: setAddData, post: postAdd, processing: addProcessing, reset: resetAdd, errors: addErrors } = useForm({
        code: '',
        name: '',
        program_study: 'D-III Keperawatan',
        academic_year: '2025/2026 Ganjil',
    });

    // Edit Form
    const { data: editData, setData: setEditData, put: putEdit, processing: editProcessing, reset: resetEdit, errors: editErrors } = useForm({
        code: '',
        name: '',
        program_study: '',
        academic_year: '',
    });

    const handleCreateCourse = (e) => {
        e.preventDefault();
        postAdd('/admin/courses', {
            preserveScroll: true,
            onSuccess: () => {
                setShowAddModal(false);
                resetAdd();
            },
        });
    };

    const handleStartEdit = (course) => {
        setEditingCourse(course);
        setEditData({
            code: course.code,
            name: course.name,
            program_study: course.program_study,
            academic_year: course.academic_year,
        });
    };

    const handleUpdateCourse = (e) => {
        e.preventDefault();
        putEdit(`/admin/courses/${editingCourse.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                setEditingCourse(null);
                resetEdit();
            },
        });
    };

    const handleDeleteCourse = (course) => {
        if (confirm(`Hapus stase kurikulum "${course.code} - ${course.name}"? Seluruh data terkait akan terhapus.`)) {
            router.delete(`/admin/courses/${course.id}`, { preserveScroll: true });
        }
    };

    return (
        <AuthLayout title="Stase Kurikulum RPS">
            <Head title="Mata Kuliah / Stase RPS" />

            <div className="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-xl font-black text-slate-900">Mata Kuliah / Stase Praktik Klinis</h1>
                    <p className="text-sm text-slate-500 mt-0.5">Kurikulum baku berbasis RPS Prodi D-III & Sarjana Terapan Keperawatan</p>
                </div>
                <button
                    onClick={() => setShowAddModal(true)}
                    className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                >
                    <Plus size={15} />
                    Tambah Stase / Mata Kuliah
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {courses?.map((course) => (
                    <div key={course.id} className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between hover:border-[#008D88]/50 transition-colors">
                        <div>
                            <div className="flex items-center justify-between mb-3">
                                <span className="px-2.5 py-1 rounded-lg text-xs font-black bg-[#008D88]/10 text-[#008D88]">
                                    {course.code}
                                </span>
                                <div className="flex items-center gap-1">
                                    <button
                                        onClick={() => handleStartEdit(course)}
                                        className="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                                        title="Edit Stase"
                                    >
                                        <Edit2 size={13} />
                                    </button>
                                    <button
                                        onClick={() => handleDeleteCourse(course)}
                                        className="p-1 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                                        title="Hapus Stase"
                                    >
                                        <Trash2 size={13} />
                                    </button>
                                </div>
                            </div>

                            <h3 className="font-black text-slate-900 text-base mb-1">{course.name}</h3>
                            <p className="text-xs text-slate-500 mb-1">{course.program_study}</p>
                            <p className="text-[11px] font-mono text-slate-400 mb-4">{course.academic_year}</p>

                            <div className="grid grid-cols-3 gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
                                <div>
                                    <div className="text-base font-black text-slate-900">{course.care_sessions_count || 0}</div>
                                    <div className="text-[10px] text-slate-400 font-medium">Kasus</div>
                                </div>
                                <div>
                                    <div className="text-base font-black text-slate-900">{course.student_groups_count || 0}</div>
                                    <div className="text-[10px] text-slate-400 font-medium">Bimbingan</div>
                                </div>
                                <div>
                                    <div className="text-base font-black text-slate-900">{course.spo_procedures_count || 0}</div>
                                    <div className="text-[10px] text-slate-400 font-medium">SPO</div>
                                </div>
                            </div>
                        </div>

                        <div className="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span>Status: Aktif</span>
                            <span className="font-semibold text-[#008D88]">RPS Terverifikasi</span>
                        </div>
                    </div>
                ))}
            </div>

            {/* CREATE MODAL */}
            {showAddModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                        <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 className="font-bold text-slate-900 text-base">Tambah Stase Kurikulum Baru</h3>
                            <button onClick={() => setShowAddModal(false)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleCreateCourse} className="space-y-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Kode Mata Kuliah RPS</label>
                                <input
                                    type="text"
                                    required
                                    value={addData.code}
                                    onChange={(e) => setAddData('code', e.target.value)}
                                    placeholder="Contoh: WAT5.31.24"
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {addErrors.code && <span className="text-[11px] text-red-600">{addErrors.code}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Nama Mata Kuliah / Stase</label>
                                <input
                                    type="text"
                                    required
                                    value={addData.name}
                                    onChange={(e) => setAddData('name', e.target.value)}
                                    placeholder="Contoh: Keperawatan Gawat Darurat (KGD)"
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Program Studi</label>
                                    <select
                                        value={addData.program_study}
                                        onChange={(e) => setAddData('program_study', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    >
                                        <option value="D-III Keperawatan">D-III Keperawatan</option>
                                        <option value="Sarjana Terapan Keperawatan">Sarjana Terapan Keperawatan</option>
                                        <option value="Profesi Ners">Profesi Ners</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Tahun Akademik</label>
                                    <input
                                        type="text"
                                        required
                                        value={addData.academic_year}
                                        onChange={(e) => setAddData('academic_year', e.target.value)}
                                        placeholder="2025/2026 Ganjil"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                </div>
                            </div>

                            <div className="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setShowAddModal(false)}
                                    className="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={addProcessing}
                                    className="px-5 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm"
                                >
                                    {addProcessing ? 'Menyimpan...' : 'Simpan Stase'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* EDIT MODAL */}
            {editingCourse && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                        <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 className="font-bold text-slate-900 text-base">Edit Stase: {editingCourse.code}</h3>
                            <button onClick={() => setEditingCourse(null)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleUpdateCourse} className="space-y-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Kode Mata Kuliah RPS</label>
                                <input
                                    type="text"
                                    required
                                    value={editData.code}
                                    onChange={(e) => setEditData('code', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {editErrors.code && <span className="text-[11px] text-red-600">{editErrors.code}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Nama Mata Kuliah / Stase</label>
                                <input
                                    type="text"
                                    required
                                    value={editData.name}
                                    onChange={(e) => setEditData('name', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Program Studi</label>
                                    <select
                                        value={editData.program_study}
                                        onChange={(e) => setEditData('program_study', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    >
                                        <option value="D-III Keperawatan">D-III Keperawatan</option>
                                        <option value="Sarjana Terapan Keperawatan">Sarjana Terapan Keperawatan</option>
                                        <option value="Profesi Ners">Profesi Ners</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Tahun Akademik</label>
                                    <input
                                        type="text"
                                        required
                                        value={editData.academic_year}
                                        onChange={(e) => setEditData('academic_year', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                </div>
                            </div>

                            <div className="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setEditingCourse(null)}
                                    className="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={editProcessing}
                                    className="px-5 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm"
                                >
                                    {editProcessing ? 'Memperbarui...' : 'Simpan Perubahan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthLayout>
    );
}
