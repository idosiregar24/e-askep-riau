import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import {
    Users, Plus, Search, Filter, Edit2, Trash2,
    CheckCircle2, XCircle, Shield, GraduationCap, UserCheck, X
} from 'lucide-react';

export default function AdminUsers({ users, filters }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [roleFilter, setRoleFilter] = useState(filters?.role || '');
    const [showAddModal, setShowAddModal] = useState(false);
    const [editingUser, setEditingUser] = useState(null);

    // Create User Form
    const { data: addData, setData: setAddData, post: postAdd, processing: addProcessing, reset: resetAdd, errors: addErrors } = useForm({
        name: '',
        nim_nip: '',
        email: '',
        role: 'mahasiswa',
        password: '',
        phone_number: '',
        is_active: true,
    });

    // Edit User Form
    const { data: editData, setData: setEditData, put: putEdit, processing: editProcessing, reset: resetEdit, errors: editErrors } = useForm({
        name: '',
        nim_nip: '',
        email: '',
        role: 'mahasiswa',
        password: '',
        phone_number: '',
        is_active: true,
    });

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/admin/users', {
            search: search || undefined,
            role: roleFilter || undefined,
        }, { preserveState: true, replace: true });
    };

    const handleCreateUser = (e) => {
        e.preventDefault();
        postAdd('/admin/users', {
            preserveScroll: true,
            onSuccess: () => {
                setShowAddModal(false);
                resetAdd();
            },
        });
    };

    const handleStartEdit = (user) => {
        setEditingUser(user);
        setEditData({
            name: user.name,
            nim_nip: user.nim_nip,
            email: user.email,
            role: user.role,
            password: '',
            phone_number: user.phone_number || '',
            is_active: Boolean(user.is_active),
        });
    };

    const handleUpdateUser = (e) => {
        e.preventDefault();
        putEdit(`/admin/users/${editingUser.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                setEditingUser(null);
                resetEdit();
            },
        });
    };

    const handleDeleteUser = (user) => {
        if (confirm(`Apakah Anda yakin ingin menghapus akun pengguna "${user.name}" (${user.role})? Tindakan ini tidak dapat dibatalkan.`)) {
            router.delete(`/admin/users/${user.id}`, { preserveScroll: true });
        }
    };

    const roleBadge = {
        admin: 'bg-amber-100 text-amber-800 border-amber-200',
        dosen: 'bg-violet-100 text-violet-800 border-violet-200',
        mahasiswa: 'bg-blue-100 text-blue-800 border-blue-200',
    };

    return (
        <AuthLayout title="Manajemen Pengguna">
            <Head title="Kelola Pengguna Sistem" />

            {/* Header */}
            <div className="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-xl font-black text-slate-900">Manajemen Akun Pengguna</h1>
                    <p className="text-sm text-slate-500 mt-0.5">Kelola akun Mahasiswa, Dosen Pembimbing Klinik, dan Administrator</p>
                </div>
                <button
                    onClick={() => setShowAddModal(true)}
                    className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                >
                    <Plus size={15} />
                    Tambah Pengguna Baru
                </button>
            </div>

            {/* Filter & Search Bar */}
            <div className="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-xs">
                <form onSubmit={handleFilter} className="flex flex-col sm:flex-row gap-3">
                    <div className="relative flex-1">
                        <Search size={15} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari nama, NIM/NIP, atau email pengguna..."
                            className="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] bg-slate-50 focus:bg-white transition-all"
                        />
                    </div>
                    <select
                        value={roleFilter}
                        onChange={(e) => {
                            setRoleFilter(e.target.value);
                            router.get('/admin/users', {
                                search: search || undefined,
                                role: e.target.value || undefined,
                            }, { preserveState: true, replace: true });
                        }}
                        className="px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] bg-slate-50 text-slate-700"
                    >
                        <option value="">Semua Peran (Role)</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen Pembimbing (CI)</option>
                        <option value="admin">Administrator</option>
                    </select>
                    <button
                        type="submit"
                        className="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition-colors whitespace-nowrap cursor-pointer"
                    >
                        Cari
                    </button>
                </form>
            </div>

            {/* Users Table */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                        <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th className="py-3 px-4">Nama Pengguna</th>
                                <th className="py-3 px-4">NIM / NIP</th>
                                <th className="py-3 px-4">Email</th>
                                <th className="py-3 px-4">Peran (Role)</th>
                                <th className="py-3 px-4">No. HP</th>
                                <th className="py-3 px-4">Status</th>
                                <th className="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {users.data?.length === 0 ? (
                                <tr>
                                    <td colSpan={7} className="py-8 text-center text-slate-400">
                                        Tidak ada data pengguna yang ditemukan.
                                    </td>
                                </tr>
                            ) : (
                                users.data?.map((u) => (
                                    <tr key={u.id} className="hover:bg-slate-50/80 transition-colors">
                                        <td className="py-3 px-4">
                                            <div className="font-bold text-slate-900 text-sm">{u.name}</div>
                                        </td>
                                        <td className="py-3 px-4 font-mono font-semibold text-slate-700">
                                            {u.nim_nip}
                                        </td>
                                        <td className="py-3 px-4 text-slate-600">
                                            {u.email}
                                        </td>
                                        <td className="py-3 px-4">
                                            <span className={`px-2.5 py-1 rounded-full text-[11px] font-bold border capitalize ${roleBadge[u.role] || 'bg-slate-100 text-slate-700'}`}>
                                                {u.role}
                                            </span>
                                        </td>
                                        <td className="py-3 px-4 text-slate-600">
                                            {u.phone_number || '-'}
                                        </td>
                                        <td className="py-3 px-4">
                                            {u.is_active ? (
                                                <span className="inline-flex items-center gap-1 text-emerald-700 font-bold">
                                                    <CheckCircle2 size={13} /> Aktif
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1 text-red-600 font-bold">
                                                    <XCircle size={13} /> Nonaktif
                                                </span>
                                            )}
                                        </td>
                                        <td className="py-3 px-4 text-right">
                                            <div className="flex items-center justify-end gap-1.5">
                                                <button
                                                    onClick={() => handleStartEdit(u)}
                                                    className="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors cursor-pointer"
                                                    title="Edit Pengguna"
                                                >
                                                    <Edit2 size={13} />
                                                </button>
                                                <button
                                                    onClick={() => handleDeleteUser(u)}
                                                    className="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors cursor-pointer"
                                                    title="Hapus Pengguna"
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
                {users.links?.length > 3 && (
                    <div className="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Menampilkan {users.from} - {users.to} dari {users.total} pengguna</span>
                        <div className="flex gap-1">
                            {users.links.map((link, idx) => (
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

            {/* CREATE USER MODAL */}
            {showAddModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                        <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 className="font-bold text-slate-900 text-base">Tambah Akun Pengguna Baru</h3>
                            <button onClick={() => setShowAddModal(false)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleCreateUser} className="space-y-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap & Gelar</label>
                                <input
                                    type="text"
                                    required
                                    value={addData.name}
                                    onChange={(e) => setAddData('name', e.target.value)}
                                    placeholder="Contoh: Ns. Budi Santoso, M.Kep"
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {addErrors.name && <span className="text-[11px] text-red-600">{addErrors.name}</span>}
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">NIM / NIP</label>
                                    <input
                                        type="text"
                                        required
                                        value={addData.nim_nip}
                                        onChange={(e) => setAddData('nim_nip', e.target.value)}
                                        placeholder="Contoh: P032414401099"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {addErrors.nim_nip && <span className="text-[11px] text-red-600">{addErrors.nim_nip}</span>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Peran (Role)</label>
                                    <select
                                        value={addData.role}
                                        onChange={(e) => setAddData('role', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    >
                                        <option value="mahasiswa">Mahasiswa</option>
                                        <option value="dosen">Dosen Pembimbing (CI)</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                                    <input
                                        type="email"
                                        required
                                        value={addData.email}
                                        onChange={(e) => setAddData('email', e.target.value)}
                                        placeholder="user@poltekkes-riau.ac.id"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {addErrors.email && <span className="text-[11px] text-red-600">{addErrors.email}</span>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Password</label>
                                    <input
                                        type="password"
                                        required
                                        value={addData.password}
                                        onChange={(e) => setAddData('password', e.target.value)}
                                        placeholder="Minimal 6 karakter"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {addErrors.password && <span className="text-[11px] text-red-600">{addErrors.password}</span>}
                                </div>
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
                                <input
                                    type="text"
                                    value={addData.phone_number}
                                    onChange={(e) => setAddData('phone_number', e.target.value)}
                                    placeholder="081234567890"
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                            </div>

                            <div className="flex items-center gap-2 pt-2">
                                <input
                                    type="checkbox"
                                    id="add_is_active"
                                    checked={addData.is_active}
                                    onChange={(e) => setAddData('is_active', e.target.checked)}
                                    className="w-4 h-4 rounded text-[#008D88]"
                                />
                                <label htmlFor="add_is_active" className="text-xs font-semibold text-slate-700">Akun Aktif (Dapat Login)</label>
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
                                    {addProcessing ? 'Menyimpan...' : 'Simpan Pengguna'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* EDIT USER MODAL */}
            {editingUser && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                        <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 className="font-bold text-slate-900 text-base">Edit Pengguna: {editingUser.name}</h3>
                            <button onClick={() => setEditingUser(null)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleUpdateUser} className="space-y-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap & Gelar</label>
                                <input
                                    type="text"
                                    required
                                    value={editData.name}
                                    onChange={(e) => setEditData('name', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {editErrors.name && <span className="text-[11px] text-red-600">{editErrors.name}</span>}
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">NIM / NIP</label>
                                    <input
                                        type="text"
                                        required
                                        value={editData.nim_nip}
                                        onChange={(e) => setEditData('nim_nip', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {editErrors.nim_nip && <span className="text-[11px] text-red-600">{editErrors.nim_nip}</span>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Peran (Role)</label>
                                    <select
                                        value={editData.role}
                                        onChange={(e) => setEditData('role', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                    >
                                        <option value="mahasiswa">Mahasiswa</option>
                                        <option value="dosen">Dosen Pembimbing (CI)</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                                    <input
                                        type="email"
                                        required
                                        value={editData.email}
                                        onChange={(e) => setEditData('email', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {editErrors.email && <span className="text-[11px] text-red-600">{editErrors.email}</span>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">Password Baru (Opsional)</label>
                                    <input
                                        type="password"
                                        value={editData.password}
                                        onChange={(e) => setEditData('password', e.target.value)}
                                        placeholder="Kosongkan jika tidak diubah"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                    />
                                    {editErrors.password && <span className="text-[11px] text-red-600">{editErrors.password}</span>}
                                </div>
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
                                <input
                                    type="text"
                                    value={editData.phone_number}
                                    onChange={(e) => setEditData('phone_number', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                            </div>

                            <div className="flex items-center gap-2 pt-2">
                                <input
                                    type="checkbox"
                                    id="edit_is_active"
                                    checked={editData.is_active}
                                    onChange={(e) => setEditData('is_active', e.target.checked)}
                                    className="w-4 h-4 rounded text-[#008D88]"
                                />
                                <label htmlFor="edit_is_active" className="text-xs font-semibold text-slate-700">Akun Aktif (Dapat Login)</label>
                            </div>

                            <div className="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setEditingUser(null)}
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
