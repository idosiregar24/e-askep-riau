import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import { Users, Plus, Edit2, Trash2, BookOpen, GraduationCap, UserCheck, X } from 'lucide-react';

export default function AdminGroups({ groups, courses, dosens, students }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [editingGroup, setEditingGroup] = useState(null);

    // Create Form
    const { data: addData, setData: setAddData, post: postAdd, processing: addProcessing, reset: resetAdd, errors: addErrors } = useForm({
        group_name: 'Kelompok A - RSUD Arifin Achmad',
        course_id: courses[0]?.id || '',
        mentor_dosen_id: dosens[0]?.id || '',
        student_id: students[0]?.id || '',
    });

    // Edit Form
    const { data: editData, setData: setEditData, put: putEdit, processing: editProcessing, reset: resetEdit, errors: editErrors } = useForm({
        group_name: '',
        course_id: '',
        mentor_dosen_id: '',
        student_id: '',
    });

    const handleCreateGroup = (e) => {
        e.preventDefault();
        postAdd('/admin/groups', {
            preserveScroll: true,
            onSuccess: () => {
                setShowAddModal(false);
                resetAdd();
            },
        });
    };

    const handleStartEdit = (g) => {
        setEditingGroup(g);
        setEditData({
            group_name: g.group_name,
            course_id: g.course_id,
            mentor_dosen_id: g.mentor_dosen_id,
            student_id: g.student_id,
        });
    };

    const handleUpdateGroup = (e) => {
        e.preventDefault();
        putEdit(`/admin/groups/${editingGroup.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                setEditingGroup(null);
                resetEdit();
            },
        });
    };

    const handleDeleteGroup = (g) => {
        if (confirm(`Hapus bimbingan kelompok "${g.group_name}" untuk ${g.student?.name}?`)) {
            router.delete(`/admin/groups/${g.id}`, { preserveScroll: true });
        }
    };

    return (
        <AuthLayout title="Kelompok Praktik & Bimbingan">
            <Head title="Kelompok Praktik" />

            <div className="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-xl font-black text-slate-900">Kelompok Praktik & Penugasan Dosen CI</h1>
                    <p className="text-sm text-slate-500 mt-0.5">Pemetaan mahasiswa, stase aktif, dan instruktur klinis pembimbing</p>
                </div>
                <button
                    onClick={() => setShowAddModal(true)}
                    className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                >
                    <Plus size={15} />
                    Tetapkan Bimbingan Baru
                </button>
            </div>

            {/* Groups Table */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                        <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th className="py-3 px-4">Nama Kelompok</th>
                                <th className="py-3 px-4">Stase Kurikulum</th>
                                <th className="py-3 px-4">Mahasiswa</th>
                                <th className="py-3 px-4">Dosen Pembimbing (CI)</th>
                                <th className="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {groups.data?.length === 0 ? (
                                <tr>
                                    <td colSpan={5} className="py-8 text-center text-slate-400">
                                        Belum ada kelompok bimbingan yang terdaftar.
                                    </td>
                                </tr>
                            ) : (
                                groups.data?.map((g) => (
                                    <tr key={g.id} className="hover:bg-slate-50/80 transition-colors">
                                        <td className="py-3 px-4 font-bold text-slate-900">{g.group_name}</td>
                                        <td className="py-3 px-4">
                                            <span className="font-semibold text-[#008D88]">{g.course?.code}</span>
                                            <div className="text-[11px] text-slate-400">{g.course?.name}</div>
                                        </td>
                                        <td className="py-3 px-4">
                                            <div className="font-semibold text-slate-800">{g.student?.name}</div>
                                            <div className="text-[11px] text-slate-400 font-mono">{g.student?.nim_nip}</div>
                                        </td>
                                        <td className="py-3 px-4">
                                            <div className="font-semibold text-slate-800">{g.mentor?.name}</div>
                                            <div className="text-[11px] text-slate-400 font-mono">{g.mentor?.nim_nip}</div>
                                        </td>
                                        <td className="py-3 px-4 text-right">
                                            <div className="flex items-center justify-end gap-1.5">
                                                <button
                                                    onClick={() => handleStartEdit(g)}
                                                    className="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors cursor-pointer"
                                                    title="Edit Kelompok"
                                                >
                                                    <Edit2 size={13} />
                                                </button>
                                                <button
                                                    onClick={() => handleDeleteGroup(g)}
                                                    className="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors cursor-pointer"
                                                    title="Hapus Kelompok"
                                                >
                                                    <Trash2 size={13} />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {groups.links?.length > 3 && (
                    <div className="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Menampilkan {groups.from} - {groups.to} dari {groups.total} kelompok</span>
                        <div className="flex gap-1">
                            {groups.links.map((link, idx) => (
                                <button
                                    key={idx}
                                    disabled={!link.url}
                                    onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    className={`px-3 py-1.5 rounded-lg border ${
                                        link.active
                                            ? 'bg-[#008D88] text-white border-[#008D88] font-bold'
                                            : 'border-slate-200 text-slate-600 hover:bg-slate-50'
                                    } ${!link.url ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'}`}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>

            {/* CREATE MODAL */}
            {showAddModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                        <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 className="font-bold text-slate-900 text-base">Tetapkan Bimbingan Baru</h3>
                            <button onClick={() => setShowAddModal(false)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleCreateGroup} className="space-y-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Nama Kelompok / Lokasi Bangsal</label>
                                <input
                                    type="text"
                                    required
                                    value={addData.group_name}
                                    onChange={(e) => setAddData('group_name', e.target.value)}
                                    placeholder="Contoh: Kelompok 1 - Ruang ICU"
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {addErrors.group_name && <span className="text-[11px] text-red-600">{addErrors.group_name}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Stase Kurikulum</label>
                                <select
                                    value={addData.course_id}
                                    onChange={(e) => setAddData('course_id', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    required
                                >
                                    {courses?.map((c) => (
                                        <option key={c.id} value={c.id}>{c.code} — {c.name}</option>
                                    ))}
                                </select>
                                {addErrors.course_id && <span className="text-[11px] text-red-600">{addErrors.course_id}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Dosen Pembimbing (CI)</label>
                                <select
                                    value={addData.mentor_dosen_id}
                                    onChange={(e) => setAddData('mentor_dosen_id', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    required
                                >
                                    {dosens?.map((d) => (
                                        <option key={d.id} value={d.id}>{d.name} ({d.nim_nip})</option>
                                    ))}
                                </select>
                                {addErrors.mentor_dosen_id && <span className="text-[11px] text-red-600">{addErrors.mentor_dosen_id}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Mahasiswa Praktikan</label>
                                <select
                                    value={addData.student_id}
                                    onChange={(e) => setAddData('student_id', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    required
                                >
                                    {students?.map((s) => (
                                        <option key={s.id} value={s.id}>{s.name} ({s.nim_nip})</option>
                                    ))}
                                </select>
                                {addErrors.student_id && <span className="text-[11px] text-red-600">{addErrors.student_id}</span>}
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
                                    {addProcessing ? 'Menyimpan...' : 'Simpan Bimbingan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* EDIT MODAL */}
            {editingGroup && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                        <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 className="font-bold text-slate-900 text-base">Edit Bimbingan Kelompok</h3>
                            <button onClick={() => setEditingGroup(null)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleUpdateGroup} className="space-y-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Nama Kelompok / Lokasi Bangsal</label>
                                <input
                                    type="text"
                                    required
                                    value={editData.group_name}
                                    onChange={(e) => setEditData('group_name', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {editErrors.group_name && <span className="text-[11px] text-red-600">{editErrors.group_name}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Stase Kurikulum</label>
                                <select
                                    value={editData.course_id}
                                    onChange={(e) => setEditData('course_id', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    required
                                >
                                    {courses?.map((c) => (
                                        <option key={c.id} value={c.id}>{c.code} — {c.name}</option>
                                    ))}
                                </select>
                                {editErrors.course_id && <span className="text-[11px] text-red-600">{editErrors.course_id}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Dosen Pembimbing (CI)</label>
                                <select
                                    value={editData.mentor_dosen_id}
                                    onChange={(e) => setEditData('mentor_dosen_id', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    required
                                >
                                    {dosens?.map((d) => (
                                        <option key={d.id} value={d.id}>{d.name} ({d.nim_nip})</option>
                                    ))}
                                </select>
                                {editErrors.mentor_dosen_id && <span className="text-[11px] text-red-600">{editErrors.mentor_dosen_id}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Mahasiswa Praktikan</label>
                                <select
                                    value={editData.student_id}
                                    onChange={(e) => setEditData('student_id', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    required
                                >
                                    {students?.map((s) => (
                                        <option key={s.id} value={s.id}>{s.name} ({s.nim_nip})</option>
                                    ))}
                                </select>
                                {editErrors.student_id && <span className="text-[11px] text-red-600">{editErrors.student_id}</span>}
                            </div>

                            <div className="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setEditingGroup(null)}
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
