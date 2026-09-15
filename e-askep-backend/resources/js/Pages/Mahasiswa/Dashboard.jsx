import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import {
    FileText, Plus, Clock, CheckCircle, AlertTriangle, RotateCcw,
    Search, Filter, ChevronRight, Stethoscope, Calendar, User
} from 'lucide-react';
import { statusLabel, triageLabel, formatDate } from '@/utils';

function StatCard({ label, value, icon: Icon, color }) {
    return (
        <div className={`bg-white rounded-2xl p-5 border border-slate-200 flex items-center gap-4`}>
            <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${color}`}>
                <Icon size={22} className="text-white" />
            </div>
            <div>
                <div className="text-2xl font-black text-slate-900">{value}</div>
                <div className="text-xs text-slate-500 font-medium mt-0.5">{label}</div>
            </div>
        </div>
    );
}

export default function MahasiswaDashboard({ sessions, totalDraft, totalSubmitted, totalNeedRevision, totalApproved, courses }) {
    const [search, setSearch] = useState('');
    const [statusFilter, setStatusFilter] = useState('');
    const [courseFilter, setCourseFilter] = useState('');

    const handleFilter = () => {
        router.get('/mahasiswa/dashboard', {
            search: search || undefined,
            status: statusFilter || undefined,
            course_id: courseFilter || undefined,
        }, { preserveState: true, replace: true });
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Enter') handleFilter();
    };

    return (
        <AuthLayout title="Dashboard Mahasiswa">
            <Head title="Dashboard Mahasiswa" />

            {/* Page Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 className="text-xl font-black text-slate-900">Portofolio Kasus Klinis</h1>
                    <p className="text-sm text-slate-500 mt-0.5">Kelola dokumentasi asuhan keperawatan Anda</p>
                </div>
                <Link
                    href="/mahasiswa/kasus/baru"
                    id="btn-kasus-baru"
                    className="inline-flex items-center gap-2 bg-[#008D88] hover:bg-[#00736F] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-xs whitespace-nowrap"
                >
                    <Plus size={16} />
                    Kasus Pasien Baru
                </Link>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <StatCard label="Sedang Dikerjakan" value={totalDraft} icon={Clock} color="bg-slate-400" />
                <StatCard label="Menunggu Telaah" value={totalSubmitted} icon={FileText} color="bg-blue-500" />
                <StatCard label="Perlu Revisi" value={totalNeedRevision} icon={AlertTriangle} color="bg-orange-500" />
                <StatCard label="Disetujui & Dinilai" value={totalApproved} icon={CheckCircle} color="bg-emerald-500" />
            </div>

            {/* Filter & Search */}
            <div className="bg-white rounded-2xl border border-slate-200 p-4 mb-5">
                <div className="flex flex-col sm:flex-row gap-3">
                    <div className="relative flex-1">
                        <Search size={15} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={handleKeyDown}
                            placeholder="Cari nama pasien atau No. RM..."
                            className="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] focus:ring-2 focus:ring-[#008D88]/15 bg-slate-50 focus:bg-white transition-all"
                        />
                    </div>
                    <select
                        value={statusFilter}
                        onChange={(e) => setStatusFilter(e.target.value)}
                        className="px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] bg-slate-50 focus:bg-white text-slate-700 transition-all"
                    >
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Menunggu Telaah</option>
                        <option value="need_revision">Perlu Revisi</option>
                        <option value="approved_graded">Disetujui</option>
                    </select>
                    <select
                        value={courseFilter}
                        onChange={(e) => setCourseFilter(e.target.value)}
                        className="px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] bg-slate-50 focus:bg-white text-slate-700 transition-all"
                    >
                        <option value="">Semua Mata Kuliah</option>
                        {courses?.map((c) => (
                            <option key={c.id} value={c.id}>{c.code} – {c.name}</option>
                        ))}
                    </select>
                    <button
                        onClick={handleFilter}
                        className="flex items-center gap-2 px-4 py-2 bg-[#008D88] text-white rounded-xl text-sm font-semibold hover:bg-[#00736F] transition-colors whitespace-nowrap"
                    >
                        <Filter size={14} />
                        Filter
                    </button>
                </div>
            </div>

            {/* Sessions Table */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                {sessions?.data?.length === 0 ? (
                    <div className="py-20 text-center">
                        <div className="w-16 h-16 rounded-2xl bg-[#E6F5F4] flex items-center justify-center mx-auto mb-4">
                            <Stethoscope size={28} className="text-[#008D88]" />
                        </div>
                        <h3 className="text-base font-bold text-slate-900 mb-1">Belum Ada Kasus</h3>
                        <p className="text-sm text-slate-500 mb-5">Mulai dokumentasikan kasus pasien pertama Anda.</p>
                        <Link
                            href="/mahasiswa/kasus/baru"
                            className="inline-flex items-center gap-2 bg-[#008D88] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#00736F] transition-colors"
                        >
                            <Plus size={16} />
                            Buat Kasus Pertama
                        </Link>
                    </div>
                ) : (
                    <>
                        {/* Desktop Table */}
                        <div className="hidden md:block overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/50">
                                        <th className="text-left text-xs font-semibold text-slate-500 px-5 py-3.5 uppercase tracking-wide">Pasien</th>
                                        <th className="text-left text-xs font-semibold text-slate-500 px-4 py-3.5 uppercase tracking-wide">Mata Kuliah</th>
                                        <th className="text-left text-xs font-semibold text-slate-500 px-4 py-3.5 uppercase tracking-wide">Dosen CI</th>
                                        <th className="text-left text-xs font-semibold text-slate-500 px-4 py-3.5 uppercase tracking-wide">Status</th>
                                        <th className="text-left text-xs font-semibold text-slate-500 px-4 py-3.5 uppercase tracking-wide">Diperbarui</th>
                                        <th className="px-4 py-3.5" />
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {sessions.data.map((session) => {
                                        const status = statusLabel(session.status);
                                        const triage = triageLabel(session.triage_category);
                                        return (
                                            <tr key={session.uuid} className="hover:bg-slate-50/70 transition-colors group">
                                                <td className="px-5 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className={`w-2 h-2 rounded-full ${triage.dot} shrink-0`} />
                                                        <div>
                                                            <div className="font-semibold text-slate-900 text-sm">{session.patient_name}</div>
                                                            <div className="text-xs text-slate-400 font-mono">{session.medical_record_no}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-4">
                                                    <div className="text-sm text-slate-700 font-medium">{session.course?.code}</div>
                                                    <div className="text-xs text-slate-400">{session.course?.name?.substring(0, 30)}...</div>
                                                </td>
                                                <td className="px-4 py-4">
                                                    <div className="text-sm text-slate-700">{session.mentor?.name}</div>
                                                </td>
                                                <td className="px-4 py-4">
                                                    <span className={`inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold ${status.bg} ${status.text}`}>
                                                        {status.label}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-4">
                                                    <div className="text-xs text-slate-500">{formatDate(session.updated_at)}</div>
                                                </td>
                                                <td className="px-4 py-4 text-right">
                                                    <Link
                                                        href={`/mahasiswa/kasus/${session.uuid}`}
                                                        className="inline-flex items-center gap-1 text-xs font-semibold text-[#008D88] hover:text-[#00736F] group-hover:gap-2 transition-all"
                                                    >
                                                        Detail
                                                        <ChevronRight size={14} />
                                                    </Link>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>

                        {/* Mobile Cards */}
                        <div className="md:hidden divide-y divide-slate-100">
                            {sessions.data.map((session) => {
                                const status = statusLabel(session.status);
                                const triage = triageLabel(session.triage_category);
                                return (
                                    <Link
                                        key={session.uuid}
                                        href={`/mahasiswa/kasus/${session.uuid}`}
                                        className="flex items-center gap-4 p-4 hover:bg-slate-50 transition-colors"
                                    >
                                        <div className={`w-10 h-10 rounded-xl ${triage.bg} flex items-center justify-center shrink-0`}>
                                            <User size={18} className={triage.text} />
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <div className="font-semibold text-slate-900 text-sm truncate">{session.patient_name}</div>
                                            <div className="text-xs text-slate-400">{session.course?.code} · {session.mentor?.name}</div>
                                        </div>
                                        <div className="shrink-0 text-right">
                                            <span className={`inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold ${status.bg} ${status.text}`}>
                                                {status.label}
                                            </span>
                                            <ChevronRight size={16} className="text-slate-400 mt-1 ml-auto" />
                                        </div>
                                    </Link>
                                );
                            })}
                        </div>
                    </>
                )}

                {/* Pagination */}
                {sessions?.last_page > 1 && (
                    <div className="border-t border-slate-100 px-5 py-3 flex items-center justify-between">
                        <div className="text-xs text-slate-500">
                            Menampilkan {sessions.from}–{sessions.to} dari {sessions.total} kasus
                        </div>
                        <div className="flex gap-2">
                            {sessions.links?.filter((l, i) => i !== 0 && i !== sessions.links.length - 1).map((link, i) => (
                                <Link
                                    key={i}
                                    href={link.url || '#'}
                                    preserveScroll
                                    className={`w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-colors ${
                                        link.active
                                            ? 'bg-[#008D88] text-white'
                                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                    } ${!link.url ? 'opacity-40 pointer-events-none' : ''}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </AuthLayout>
    );
}
