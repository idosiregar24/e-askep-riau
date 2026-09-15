import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import {
    ArrowLeft, FileText, CheckCircle, AlertTriangle, Clock,
    Users, BookOpen, ClipboardList, ChevronRight, Search, Filter
} from 'lucide-react';
import { statusLabel, triageLabel, formatDate, formatDateTime } from '@/utils';

function StatCard({ label, value, icon: Icon, bg, text }) {
    return (
        <div className="bg-white rounded-2xl p-5 border border-slate-200 flex items-center gap-4">
            <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${bg}`}>
                <Icon size={22} className={text} />
            </div>
            <div>
                <div className="text-2xl font-black text-slate-900">{value}</div>
                <div className="text-xs text-slate-500 font-medium mt-0.5">{label}</div>
            </div>
        </div>
    );
}

export default function DosenDashboard({
    sessions,
    totalSubmitted,
    totalNeedRevision,
    totalApproved,
    totalStudents,
    courses,
}) {
    const [search, setSearch] = useState('');
    const [statusFilter, setStatusFilter] = useState('');
    const [courseFilter, setCourseFilter] = useState('');

    const handleFilter = () => {
        router.get('/dosen/dashboard', {
            search: search || undefined,
            status: statusFilter || undefined,
            course_id: courseFilter || undefined,
        }, { preserveState: true, replace: true });
    };

    return (
        <AuthLayout title="Dashboard Dosen Pembimbing">
            <Head title="Dashboard Dosen CI" />

            {/* Header */}
            <div className="mb-6">
                <h1 className="text-xl font-black text-slate-900">Meja Telaah Dosen Pembimbing Klinik</h1>
                <p className="text-sm text-slate-500 mt-0.5">Telaah, verifikasi, dan nilai berkas asuhan keperawatan mahasiswa</p>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <StatCard label="Antrian Telaah" value={totalSubmitted} icon={FileText} bg="bg-blue-100" text="text-blue-600" />
                <StatCard label="Perlu Revisi" value={totalNeedRevision} icon={AlertTriangle} bg="bg-orange-100" text="text-orange-600" />
                <StatCard label="Telah Disetujui" value={totalApproved} icon={CheckCircle} bg="bg-emerald-100" text="text-emerald-600" />
                <StatCard label="Total Mahasiswa" value={totalStudents} icon={Users} bg="bg-violet-100" text="text-violet-600" />
            </div>

            {/* Filter */}
            <div className="bg-white rounded-2xl border border-slate-200 p-4 mb-5">
                <div className="flex flex-col sm:flex-row gap-3">
                    <div className="relative flex-1">
                        <Search size={15} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && handleFilter()}
                            placeholder="Cari nama pasien, RM, atau mahasiswa..."
                            className="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] focus:ring-2 focus:ring-[#008D88]/15 bg-slate-50 transition-all"
                        />
                    </div>
                    <select
                        value={statusFilter}
                        onChange={(e) => setStatusFilter(e.target.value)}
                        className="px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] bg-slate-50 text-slate-700"
                    >
                        <option value="">Semua Status</option>
                        <option value="submitted">Menunggu Telaah</option>
                        <option value="need_revision">Perlu Revisi</option>
                        <option value="approved_graded">Disetujui</option>
                    </select>
                    <select
                        value={courseFilter}
                        onChange={(e) => setCourseFilter(e.target.value)}
                        className="px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-[#008D88] bg-slate-50 text-slate-700"
                    >
                        <option value="">Semua Mata Kuliah</option>
                        {courses?.map((c) => (
                            <option key={c.id} value={c.id}>{c.code}</option>
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

            {/* Table */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                {sessions?.data?.length === 0 ? (
                    <div className="py-20 text-center">
                        <div className="w-16 h-16 rounded-2xl bg-[#E6F5F4] flex items-center justify-center mx-auto mb-4">
                            <ClipboardList size={28} className="text-[#008D88]" />
                        </div>
                        <h3 className="text-base font-bold text-slate-900 mb-1">Tidak Ada Berkas</h3>
                        <p className="text-sm text-slate-500">Belum ada berkas kasus mahasiswa yang sesuai dengan filter.</p>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-slate-100 bg-slate-50/50">
                                    <th className="text-left text-xs font-semibold text-slate-500 px-5 py-3.5 uppercase tracking-wide">Pasien</th>
                                    <th className="text-left text-xs font-semibold text-slate-500 px-4 py-3.5 uppercase tracking-wide">Mahasiswa</th>
                                    <th className="text-left text-xs font-semibold text-slate-500 px-4 py-3.5 uppercase tracking-wide">Mata Kuliah</th>
                                    <th className="text-left text-xs font-semibold text-slate-500 px-4 py-3.5 uppercase tracking-wide">Status</th>
                                    <th className="text-left text-xs font-semibold text-slate-500 px-4 py-3.5 uppercase tracking-wide">Diperbarui</th>
                                    <th className="px-4 py-3.5" />
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {sessions.data.map((session) => {
                                    const status = statusLabel(session.status);
                                    const triage = triageLabel(session.triage_category);
                                    const isActionable = session.status === 'submitted';
                                    return (
                                        <tr key={session.uuid} className={`hover:bg-slate-50/70 transition-colors group ${isActionable ? 'bg-blue-50/30' : ''}`}>
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
                                                <div className="text-sm text-slate-700">{session.student?.name}</div>
                                                <div className="text-xs text-slate-400 font-mono">{session.student?.nim_nip}</div>
                                            </td>
                                            <td className="px-4 py-4">
                                                <div className="text-sm font-medium text-slate-700">{session.course?.code}</div>
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
                                                    href={`/dosen/review/${session.uuid}`}
                                                    className={`inline-flex items-center gap-1 text-xs font-semibold transition-all group-hover:gap-2 ${
                                                        isActionable ? 'text-blue-600 hover:text-blue-700' : 'text-[#008D88] hover:text-[#00736F]'
                                                    }`}
                                                >
                                                    {isActionable ? 'Telaah' : 'Lihat'}
                                                    <ChevronRight size={14} />
                                                </Link>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                )}

                {/* Pagination */}
                {sessions?.last_page > 1 && (
                    <div className="border-t border-slate-100 px-5 py-3 flex items-center justify-between">
                        <div className="text-xs text-slate-500">
                            Menampilkan {sessions.from}–{sessions.to} dari {sessions.total} berkas
                        </div>
                        <div className="flex gap-2">
                            {sessions.links?.filter((l, i) => i !== 0 && i !== sessions.links.length - 1).map((link, i) => (
                                <Link
                                    key={i}
                                    href={link.url || '#'}
                                    preserveScroll
                                    className={`w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-colors ${
                                        link.active ? 'bg-[#008D88] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
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
