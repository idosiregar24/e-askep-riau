import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import {
    ClipboardList, Search, Plus, Edit2, Trash2,
    CheckCircle2, Layers, BookOpen, AlertCircle, X
} from 'lucide-react';

export default function AdminMaster3s({
    sdkiList,
    slkiList,
    sikiList,
    sdkiCount,
    slkiCount,
    sikiCount,
    search: initialSearch,
    activeTab: initialTab = 'sdki'
}) {
    const [currentTab, setCurrentTab] = useState(initialTab || 'sdki');
    const [search, setSearch] = useState(initialSearch || '');

    // Modals state
    const [showAddModal, setShowAddModal] = useState(false);
    const [editingItem, setEditingItem] = useState(null);

    // Forms for SDKI
    const {
        data: sdkiData,
        setData: setSdkiData,
        post: postSdki,
        put: putSdki,
        processing: sdkiProcessing,
        reset: resetSdki,
        errors: sdkiErrors
    } = useForm({
        code: '',
        title: '',
        category: 'Fisiologis',
        sub_category: 'Respirasi'
    });

    // Forms for SLKI
    const {
        data: slkiData,
        setData: setSlkiData,
        post: postSlki,
        put: putSlki,
        processing: slkiProcessing,
        reset: resetSlki,
        errors: slkiErrors
    } = useForm({
        code: '',
        title: ''
    });

    // Forms for SIKI
    const {
        data: sikiData,
        setData: setSikiData,
        post: postSiki,
        put: putSiki,
        processing: sikiProcessing,
        reset: resetSiki,
        errors: sikiErrors
    } = useForm({
        code: '',
        title: ''
    });

    const handleTabChange = (tab) => {
        setCurrentTab(tab);
        setSearch('');
        router.get('/admin/master-3s', { tab, search: '' }, { preserveState: true, replace: true });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/master-3s', { tab: currentTab, search }, { preserveState: true, replace: true });
    };

    // Open Add Modal
    const handleOpenAddModal = () => {
        if (currentTab === 'sdki') {
            resetSdki();
            setSdkiData({
                code: '',
                title: '',
                category: 'Fisiologis',
                sub_category: 'Respirasi'
            });
        } else if (currentTab === 'slki') {
            resetSlki();
            setSlkiData({ code: '', title: '' });
        } else {
            resetSiki();
            setSikiData({ code: '', title: '' });
        }
        setShowAddModal(true);
    };

    // Open Edit Modal
    const handleOpenEditModal = (item) => {
        setEditingItem(item);
        if (currentTab === 'sdki') {
            setSdkiData({
                code: item.code,
                title: item.title,
                category: item.category || 'Fisiologis',
                sub_category: item.sub_category || 'Respirasi'
            });
        } else if (currentTab === 'slki') {
            setSlkiData({
                code: item.code,
                title: item.title
            });
        } else {
            setSikiData({
                code: item.code,
                title: item.title
            });
        }
    };

    // Submit Create
    const handleCreate = (e) => {
        e.preventDefault();
        if (currentTab === 'sdki') {
            postSdki('/admin/master-sdki', {
                preserveScroll: true,
                onSuccess: () => {
                    setShowAddModal(false);
                    resetSdki();
                }
            });
        } else if (currentTab === 'slki') {
            postSlki('/admin/master-slki', {
                preserveScroll: true,
                onSuccess: () => {
                    setShowAddModal(false);
                    resetSlki();
                }
            });
        } else {
            postSiki('/admin/master-siki', {
                preserveScroll: true,
                onSuccess: () => {
                    setShowAddModal(false);
                    resetSiki();
                }
            });
        }
    };

    // Submit Update
    const handleUpdate = (e) => {
        e.preventDefault();
        if (currentTab === 'sdki') {
            putSdki(`/admin/master-sdki/${editingItem.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    setEditingItem(null);
                    resetSdki();
                }
            });
        } else if (currentTab === 'slki') {
            putSlki(`/admin/master-slki/${editingItem.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    setEditingItem(null);
                    resetSlki();
                }
            });
        } else {
            putSiki(`/admin/master-siki/${editingItem.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    setEditingItem(null);
                    resetSiki();
                }
            });
        }
    };

    // Delete item
    const handleDelete = (item) => {
        const typeLabel = currentTab === 'sdki' ? 'Diagnosis SDKI' : currentTab === 'slki' ? 'Luaran SLKI' : 'Intervensi SIKI';
        if (confirm(`Apakah Anda yakin ingin menghapus ${typeLabel} "${item.code} - ${item.title}"?`)) {
            const url = currentTab === 'sdki'
                ? `/admin/master-sdki/${item.id}`
                : currentTab === 'slki'
                ? `/admin/master-slki/${item.id}`
                : `/admin/master-siki/${item.id}`;
            router.delete(url, { preserveScroll: true });
        }
    };

    const currentList = currentTab === 'sdki' ? sdkiList : currentTab === 'slki' ? slkiList : sikiList;
    const tabName = currentTab === 'sdki' ? 'SDKI (Diagnosis)' : currentTab === 'slki' ? 'SLKI (Luaran)' : 'SIKI (Intervensi)';

    return (
        <AuthLayout title="Master 3S PPNI">
            <Head title="Kamus 3S PPNI (SDKI, SLKI, SIKI)" />

            {/* Page Header */}
            <div className="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-xl font-black text-slate-900">Kamus Standar Praktik Keperawatan Indonesia (3S PPNI)</h1>
                    <p className="text-sm text-slate-500 mt-0.5">Katalog terpadu Standar Diagnosis (SDKI), Luaran (SLKI), dan Intervensi (SIKI)</p>
                </div>
                <button
                    onClick={handleOpenAddModal}
                    className="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs flex items-center gap-1.5 transition-colors cursor-pointer whitespace-nowrap self-start sm:self-auto"
                >
                    <Plus size={15} />
                    Tambah {tabName}
                </button>
            </div>

            {/* Stats Overview */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <button
                    onClick={() => handleTabChange('sdki')}
                    className={`bg-white p-5 rounded-2xl border text-left transition-all cursor-pointer shadow-xs ${
                        currentTab === 'sdki'
                            ? 'border-[#008D88] ring-2 ring-[#008D88]/20 bg-teal-50/10'
                            : 'border-slate-200 hover:border-slate-300'
                    }`}
                >
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center text-teal-700">
                            <ClipboardList size={22} />
                        </div>
                        <div>
                            <div className="text-2xl font-black text-slate-900">{sdkiCount || sdkiList?.total || 0}</div>
                            <div className="text-xs text-slate-500 font-medium">SDKI (Standar Diagnosis)</div>
                        </div>
                    </div>
                </button>

                <button
                    onClick={() => handleTabChange('slki')}
                    className={`bg-white p-5 rounded-2xl border text-left transition-all cursor-pointer shadow-xs ${
                        currentTab === 'slki'
                            ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/10'
                            : 'border-slate-200 hover:border-slate-300'
                    }`}
                >
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700">
                            <CheckCircle2 size={22} />
                        </div>
                        <div>
                            <div className="text-2xl font-black text-slate-900">{slkiCount || slkiList?.total || 0}</div>
                            <div className="text-xs text-slate-500 font-medium">SLKI (Standar Luaran)</div>
                        </div>
                    </div>
                </button>

                <button
                    onClick={() => handleTabChange('siki')}
                    className={`bg-white p-5 rounded-2xl border text-left transition-all cursor-pointer shadow-xs ${
                        currentTab === 'siki'
                            ? 'border-blue-500 ring-2 ring-blue-500/20 bg-blue-50/10'
                            : 'border-slate-200 hover:border-slate-300'
                    }`}
                >
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">
                            <Layers size={22} />
                        </div>
                        <div>
                            <div className="text-2xl font-black text-slate-900">{sikiCount || sikiList?.total || 0}</div>
                            <div className="text-xs text-slate-500 font-medium">SIKI (Standar Intervensi)</div>
                        </div>
                    </div>
                </button>
            </div>

            {/* Tab Navigation & Search Bar */}
            <div className="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                {/* Tabs */}
                <div className="flex items-center gap-2 border-b md:border-b-0 border-slate-100 pb-3 md:pb-0">
                    <button
                        onClick={() => handleTabChange('sdki')}
                        className={`px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 ${
                            currentTab === 'sdki'
                                ? 'bg-[#008D88] text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        }`}
                    >
                        <ClipboardList size={14} />
                        <span>SDKI (Diagnosis)</span>
                        <span className={`px-1.5 py-0.2 rounded-full text-[10px] ${currentTab === 'sdki' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'}`}>
                            {sdkiCount || sdkiList?.total || 0}
                        </span>
                    </button>

                    <button
                        onClick={() => handleTabChange('slki')}
                        className={`px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 ${
                            currentTab === 'slki'
                                ? 'bg-emerald-600 text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        }`}
                    >
                        <CheckCircle2 size={14} />
                        <span>SLKI (Luaran)</span>
                        <span className={`px-1.5 py-0.2 rounded-full text-[10px] ${currentTab === 'slki' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'}`}>
                            {slkiCount || slkiList?.total || 0}
                        </span>
                    </button>

                    <button
                        onClick={() => handleTabChange('siki')}
                        className={`px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 ${
                            currentTab === 'siki'
                                ? 'bg-blue-600 text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        }`}
                    >
                        <Layers size={14} />
                        <span>SIKI (Intervensi)</span>
                        <span className={`px-1.5 py-0.2 rounded-full text-[10px] ${currentTab === 'siki' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'}`}>
                            {sikiCount || sikiList?.total || 0}
                        </span>
                    </button>
                </div>

                {/* Search Form */}
                <form onSubmit={handleSearch} className="flex gap-2 flex-1 max-w-md">
                    <div className="relative flex-1">
                        <Search size={14} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={`Cari kode atau judul pada ${tabName}...`}
                            className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88] bg-slate-50 focus:bg-white transition-all"
                        />
                    </div>
                    <button
                        type="submit"
                        className="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition-colors cursor-pointer"
                    >
                        Cari
                    </button>
                    {search && (
                        <button
                            type="button"
                            onClick={() => {
                                setSearch('');
                                router.get('/admin/master-3s', { tab: currentTab, search: '' }, { preserveState: true, replace: true });
                            }}
                            className="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors cursor-pointer"
                        >
                            Reset
                        </button>
                    )}
                </form>
            </div>

            {/* Table */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                        <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th className="py-3 px-4 w-28">Kode Standar</th>
                                <th className="py-3 px-4">
                                    {currentTab === 'sdki' ? 'Judul Diagnosis Keperawatan' : currentTab === 'slki' ? 'Judul Luaran Keperawatan' : 'Judul Intervensi Keperawatan'}
                                </th>
                                {currentTab === 'sdki' && (
                                    <>
                                        <th className="py-3 px-4">Kategori</th>
                                        <th className="py-3 px-4">Sub-Kategori</th>
                                    </>
                                )}
                                <th className="py-3 px-4 text-right w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {currentList?.data?.length === 0 ? (
                                <tr>
                                    <td colSpan={currentTab === 'sdki' ? 5 : 3} className="py-8 text-center text-slate-400">
                                        Tidak ada data yang ditemukan.
                                    </td>
                                </tr>
                            ) : (
                                currentList?.data?.map((item) => (
                                    <tr key={item.id} className="hover:bg-slate-50/80 transition-colors">
                                        <td className="py-3 px-4 font-black font-mono text-[#008D88]">{item.code}</td>
                                        <td className="py-3 px-4 font-bold text-slate-900">{item.title}</td>
                                        {currentTab === 'sdki' && (
                                            <>
                                                <td className="py-3 px-4">
                                                    <span className="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                                        {item.category}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 text-slate-500">{item.sub_category}</td>
                                            </>
                                        )}
                                        <td className="py-3 px-4 text-right">
                                            <div className="flex items-center justify-end gap-1.5">
                                                <button
                                                    onClick={() => handleOpenEditModal(item)}
                                                    className="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors cursor-pointer"
                                                    title={`Edit ${item.code}`}
                                                >
                                                    <Edit2 size={13} />
                                                </button>
                                                <button
                                                    onClick={() => handleDelete(item)}
                                                    className="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors cursor-pointer"
                                                    title={`Hapus ${item.code}`}
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
                {currentList?.links?.length > 3 && (
                    <div className="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Menampilkan {currentList.from} - {currentList.to} dari {currentList.total} item</span>
                        <div className="flex gap-1">
                            {currentList.links.map((link, idx) => (
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
                            <h3 className="font-bold text-slate-900 text-base">Tambah {tabName} Baru</h3>
                            <button onClick={() => setShowAddModal(false)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleCreate} className="space-y-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Kode Standar</label>
                                <input
                                    type="text"
                                    required
                                    value={currentTab === 'sdki' ? sdkiData.code : currentTab === 'slki' ? slkiData.code : sikiData.code}
                                    onChange={(e) => {
                                        if (currentTab === 'sdki') setSdkiData('code', e.target.value);
                                        else if (currentTab === 'slki') setSlkiData('code', e.target.value);
                                        else setSikiData('code', e.target.value);
                                    }}
                                    placeholder={currentTab === 'sdki' ? 'Contoh: D.0005' : currentTab === 'slki' ? 'Contoh: L.01002' : 'Contoh: I.01012'}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono outline-none focus:border-[#008D88]"
                                />
                                {currentTab === 'sdki' && sdkiErrors.code && <span className="text-[11px] text-red-600">{sdkiErrors.code}</span>}
                                {currentTab === 'slki' && slkiErrors.code && <span className="text-[11px] text-red-600">{slkiErrors.code}</span>}
                                {currentTab === 'siki' && sikiErrors.code && <span className="text-[11px] text-red-600">{sikiErrors.code}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Judul / Nomenklatur Standar</label>
                                <input
                                    type="text"
                                    required
                                    value={currentTab === 'sdki' ? sdkiData.title : currentTab === 'slki' ? slkiData.title : sikiData.title}
                                    onChange={(e) => {
                                        if (currentTab === 'sdki') setSdkiData('title', e.target.value);
                                        else if (currentTab === 'slki') setSlkiData('title', e.target.value);
                                        else setSikiData('title', e.target.value);
                                    }}
                                    placeholder="Contoh: Pola Napas Tidak Efektif"
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {currentTab === 'sdki' && sdkiErrors.title && <span className="text-[11px] text-red-600">{sdkiErrors.title}</span>}
                                {currentTab === 'slki' && slkiErrors.title && <span className="text-[11px] text-red-600">{slkiErrors.title}</span>}
                                {currentTab === 'siki' && sikiErrors.title && <span className="text-[11px] text-red-600">{sikiErrors.title}</span>}
                            </div>

                            {currentTab === 'sdki' && (
                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                                        <select
                                            value={sdkiData.category}
                                            onChange={(e) => setSdkiData('category', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                        >
                                            <option value="Fisiologis">Fisiologis</option>
                                            <option value="Psikologis">Psikologis</option>
                                            <option value="Perilaku">Perilaku</option>
                                            <option value="Relasional">Relasional</option>
                                            <option value="Lingkungan">Lingkungan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">Sub-Kategori</label>
                                        <input
                                            type="text"
                                            required
                                            value={sdkiData.sub_category}
                                            onChange={(e) => setSdkiData('sub_category', e.target.value)}
                                            placeholder="Contoh: Respirasi, Sirkulasi"
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                        />
                                        {sdkiErrors.sub_category && <span className="text-[11px] text-red-600">{sdkiErrors.sub_category}</span>}
                                    </div>
                                </div>
                            )}

                            <div className="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setShowAddModal(false)}
                                    className="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100 cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={sdkiProcessing || slkiProcessing || sikiProcessing}
                                    className="px-5 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm cursor-pointer"
                                >
                                    {sdkiProcessing || slkiProcessing || sikiProcessing ? 'Menyimpan...' : 'Simpan Standar'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* EDIT MODAL */}
            {editingItem && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                        <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 className="font-bold text-slate-900 text-base">Edit {tabName}: {editingItem.code}</h3>
                            <button onClick={() => setEditingItem(null)} className="text-slate-400 hover:text-slate-700"><X size={18} /></button>
                        </div>

                        <form onSubmit={handleUpdate} className="space-y-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Kode Standar</label>
                                <input
                                    type="text"
                                    required
                                    value={currentTab === 'sdki' ? sdkiData.code : currentTab === 'slki' ? slkiData.code : sikiData.code}
                                    onChange={(e) => {
                                        if (currentTab === 'sdki') setSdkiData('code', e.target.value);
                                        else if (currentTab === 'slki') setSlkiData('code', e.target.value);
                                        else setSikiData('code', e.target.value);
                                    }}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono outline-none focus:border-[#008D88]"
                                />
                                {currentTab === 'sdki' && sdkiErrors.code && <span className="text-[11px] text-red-600">{sdkiErrors.code}</span>}
                                {currentTab === 'slki' && slkiErrors.code && <span className="text-[11px] text-red-600">{slkiErrors.code}</span>}
                                {currentTab === 'siki' && sikiErrors.code && <span className="text-[11px] text-red-600">{sikiErrors.code}</span>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Judul / Nomenklatur Standar</label>
                                <input
                                    type="text"
                                    required
                                    value={currentTab === 'sdki' ? sdkiData.title : currentTab === 'slki' ? slkiData.title : sikiData.title}
                                    onChange={(e) => {
                                        if (currentTab === 'sdki') setSdkiData('title', e.target.value);
                                        else if (currentTab === 'slki') setSlkiData('title', e.target.value);
                                        else setSikiData('title', e.target.value);
                                    }}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                />
                                {currentTab === 'sdki' && sdkiErrors.title && <span className="text-[11px] text-red-600">{sdkiErrors.title}</span>}
                                {currentTab === 'slki' && slkiErrors.title && <span className="text-[11px] text-red-600">{slkiErrors.title}</span>}
                                {currentTab === 'siki' && sikiErrors.title && <span className="text-[11px] text-red-600">{sikiErrors.title}</span>}
                            </div>

                            {currentTab === 'sdki' && (
                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                                        <select
                                            value={sdkiData.category}
                                            onChange={(e) => setSdkiData('category', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none focus:border-[#008D88]"
                                        >
                                            <option value="Fisiologis">Fisiologis</option>
                                            <option value="Psikologis">Psikologis</option>
                                            <option value="Perilaku">Perilaku</option>
                                            <option value="Relasional">Relasional</option>
                                            <option value="Lingkungan">Lingkungan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-slate-700 mb-1">Sub-Kategori</label>
                                        <input
                                            type="text"
                                            required
                                            value={sdkiData.sub_category}
                                            onChange={(e) => setSdkiData('sub_category', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-[#008D88]"
                                        />
                                        {sdkiErrors.sub_category && <span className="text-[11px] text-red-600">{sdkiErrors.sub_category}</span>}
                                    </div>
                                </div>
                            )}

                            <div className="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setEditingItem(null)}
                                    className="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100 cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={sdkiProcessing || slkiProcessing || sikiProcessing}
                                    className="px-5 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-sm cursor-pointer"
                                >
                                    {sdkiProcessing || slkiProcessing || sikiProcessing ? 'Memperbarui...' : 'Simpan Perubahan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthLayout>
    );
}
