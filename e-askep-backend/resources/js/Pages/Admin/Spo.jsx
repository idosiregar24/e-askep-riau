import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import {
    HeartPulse, Plus, Search, Edit2, Trash2, BookOpen, X
} from 'lucide-react';

export default function AdminSpo({ procedures, courses, filters }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [courseFilter, setCourseFilter] = useState(filters?.course_id || '');
    const [showAddModal, setShowAddModal] = useState(false);
    const [editingSpo, setEditingSpo] = useState(null);

    // Create Form
    const { data: addData, setData: setAddData, post: postAdd, processing: addProcessing, reset: resetAdd, errors: addErrors } = useForm({
        course_id: courses[0]?.id || '',
        sub_cpmk_reference: 'Sub-CPMK 2',
        domain_category: 'Airway & Breathing Management',
        procedure_name: '',
    });

    // Edit Form
    const { data: editData, setData: setEditData, put: putEdit, processing: editProcessing, reset: resetEdit, errors: editErrors } = useForm({
        course_id: '',
        sub_cpmk_reference: '',
        domain_category: '',
        procedure_name: '',
    });

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/admin/spo', {
            search: search || undefined,
            course_id: courseFilter || undefined,
        }, { preserveState: true, replace: true });
    };

    const handleCreateSpo = (e) => {
        e.preventDefault();
        postAdd('/admin/spo', {
            preserveScroll: true,
            onSuccess: () => {
                setShowAddModal(false);
                resetAdd();
            },
        });
    };

    const handleStartEdit = (spo) => {
        setEditingSpo(spo);
        setEditData({
            course_id: spo.course_id,
            sub_cpmk_reference: spo.sub_cpmk_reference,
            domain_category: spo.domain_category,
            procedure_name: spo.procedure_name,
        });
    };

    const handleUpdateSpo = (e) => {
        e.preventDefault();
        putEdit(`/admin/spo/${editingSpo.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                setEditingSpo(null);
                resetEdit();
            },
        });
    };

    const handleDeleteSpo = (spo) => {
        if (confirm(`Hapus prosedur "${spo.procedure_name}"?`)) {
            router.delete(`/admin/spo/${spo.id}`, { preserveScroll: true });
        }
    };

    return (
        <AuthLayout title="Katalog Prosedur SPO">
            <Head title="Master Prosedur Tindakan SPO" />

            {/* Header */}
            <div className="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-xl font-black text-slate-900">Katalog Prosedur Standar Operasional (SPO Praktikum)</h1>
                    <p className="text-sm text-slate-500 mt-0.5">Katalog keterampilan klinis mahasiswa terpetakan ke capaian Sub-CPMK RPS</p>
                </div>
                <button
                    onClick={() => setShowAddModal(true)}
                    className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                >
                    <Plus size={15} />
                    Tambah Prosedur SPO
                </button>
            </div>

            {/* Filter & Search */}
            <div className="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-xs">
                <form onSubmit={handleFilter} className="flex flex-col sm:flex-row gap-3">
                    <div className="relative flex-1">
                        <Search size={15} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari nama prosedur atau kategori domain SPO..."
                            className="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] bg-slate-50 focus:bg-white transition-all"
                        />
                    </div>
                    <select
                        value={courseFilter}
                        onChange={(e) => {
                            setCourseFilter(e.target.value);
                            router.get('/admin/spo', {
                                search: search || undefined,
                                course_id: e.target.value || undefined,
                            }, { preserveState: true, replace: true });
                        }}
                        className="px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] bg-slate-50 text-slate-700"
                    >
                        <option value="">Semua Stase Kurikulum</option>
                        {courses?.map((c) => (
                            <option key={c.id} value={c.id}>{c.code} — {c.name}</option>
                        ))}
                    </select>
                    <button
                        type="submit"
                        className="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition-colors whitespace-nowrap cursor-pointer"
                    >
                        Cari
                    </button>
                </form>
            </div>

            {/* SPO Table */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                        <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th className="py-3 px-4">Nama Prosedur SPO</th>
                                <th className="py-3 px-4">Stase Kurikulum</th>
                                <th className="py-3 px-4">Kategori Domain</th>
                                <th className="py-3 px-4">Acuan Sub-CPMK</th>
                                <th className="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {procedures.data?.length === 0 ? (
                                <tr>
                                    <td colSpan={5} className="py-8 text-center text-slate-400">
                                        Tidak ada katalog prosedur SPO yang ditemukan.
                                    </td>
                                </tr>
                            ) : (
                                procedures.data?.map((p) => (
                                    <tr key={p.id} className="hover:bg-slate-50/80 transition-colors">
                                        <td className="py-3 px-4">
                                            <div className="font-bold text-slate-900 text-sm">{p.procedure_name}</div>
                                        </td>
                                        <td className="py-3 px-4">
                                            <span className="font-semibold text-[#008D88]">{p.course?.code}</span>
                                            <div className="text-[11px] text-slate-400">{p.course?.name}</div>
                                        </td>
                                        <td className="py-3 px-4 text-slate-600">
                                            {p.domain_category}
                                        </td>
                                        <td className="py-3 px-4">
                                            <span className="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                                {p.sub_cpmk_reference}
                                            </span>
                                        </td>
                                        <td className="py-3 px-4 text-right">
                                            <div className="flex items-center justify-end gap-1.5">
                                                <button
                                                    onClick={() => handleStartEdit(p)}
                                                    className="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors cursor-pointer"
                                                    title="Edit Prosedur"
                                                >
                                                    <Edit2 size={13} />
                                                </button>
                                                <button
                                                    onClick={() => handleDeleteSpo(p)}
                                                    className="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors cursor-pointer"
                                                    title="Hapus Prosedur"
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
                {procedures.links?.length > 3 && (
                    <div className="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Menampilkan {procedures.from} - {procedures.to} dari {procedures.total} item</span>
                        <div className="flex gap-1">
                            {procedures.links.map((link, idx) => (
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
                            <h3 className="font-bold text-slate-900 text-base">Tambah Prosedur Tindakan SPO Baru</h3>
                            <button onClick={() => setShowAddModal(false)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleCreateSpo} className="space-y-3">
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
                                <label className="block text-xs font-bold text-slate-700 mb-1">Nama Prosedur Tindakan SPO</label>
                                <input
                                    type="text"
                                    required
                                    value={addData.procedure_name}
                                    onChange={(e) => setAddData('procedure_name', e.target.value)}
                                    placeholder="Contoh: Pemasangan Oropharyngeal Airway (OPA)"
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {addErrors.procedure_name && <span className="text-[11px] text-red-600">{addErrors.procedure_name}</span>}
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Kategori Domain</label>
                                    <input
                                        type="text"
                                        required
                                        value={addData.domain_category}
                                        onChange={(e) => setAddData('domain_category', e.target.value)}
                                        placeholder="Contoh: Airway & Breathing"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {addErrors.domain_category && <span className="text-[11px] text-red-600">{addErrors.domain_category}</span>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Acuan Sub-CPMK RPS</label>
                                    <input
                                        type="text"
                                        required
                                        value={addData.sub_cpmk_reference}
                                        onChange={(e) => setAddData('sub_cpmk_reference', e.target.value)}
                                        placeholder="Contoh: Sub-CPMK 2"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {addErrors.sub_cpmk_reference && <span className="text-[11px] text-red-600">{addErrors.sub_cpmk_reference}</span>}
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
                                    {addProcessing ? 'Menyimpan...' : 'Simpan Prosedur'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* EDIT MODAL */}
            {editingSpo && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                        <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 className="font-bold text-slate-900 text-base">Edit Prosedur SPO</h3>
                            <button onClick={() => setEditingSpo(null)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleUpdateSpo} className="space-y-3">
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
                                <label className="block text-xs font-bold text-slate-700 mb-1">Nama Prosedur Tindakan SPO</label>
                                <input
                                    type="text"
                                    required
                                    value={editData.procedure_name}
                                    onChange={(e) => setEditData('procedure_name', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {editErrors.procedure_name && <span className="text-[11px] text-red-600">{editErrors.procedure_name}</span>}
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Kategori Domain</label>
                                    <input
                                        type="text"
                                        required
                                        value={editData.domain_category}
                                        onChange={(e) => setEditData('domain_category', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {editErrors.domain_category && <span className="text-[11px] text-red-600">{editErrors.domain_category}</span>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Acuan Sub-CPMK RPS</label>
                                    <input
                                        type="text"
                                        required
                                        value={editData.sub_cpmk_reference}
                                        onChange={(e) => setEditData('sub_cpmk_reference', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {editErrors.sub_cpmk_reference && <span className="text-[11px] text-red-600">{editErrors.sub_cpmk_reference}</span>}
                                </div>
                            </div>

                            <div className="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setEditingSpo(null)}
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
