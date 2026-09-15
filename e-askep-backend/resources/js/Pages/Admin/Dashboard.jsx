import { Head, Link } from '@inertiajs/react';
import AuthLayout from '@/Layouts/AuthLayout';
import {
    Users, GraduationCap, BookOpen, FileText,
    ClipboardList, HeartPulse, ChevronRight, Activity
} from 'lucide-react';
import { statusLabel, triageLabel, formatDateTime } from '@/utils';

function StatCard({ label, value, icon: Icon, bg, text }) {
    return (
        <div className="bg-white rounded-2xl p-5 border border-slate-200 flex items-center gap-4 shadow-xs">
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

export default function AdminDashboard({
    totalMahasiswa,
    totalDosen,
    totalCourses,
    totalCareSessions,
    totalSdki,
    totalSpo,
    recentSessions,
    courses,
}) {
    return (
        <AuthLayout title="Dashboard Administrator">
            <Head title="Admin Dashboard" />

            {/* Header */}
            <div className="mb-6">
                <h1 className="text-xl font-black text-slate-900">Pusat Kendali Akademik e-Askep</h1>
                <p className="text-sm text-slate-500 mt-0.5">Monitoring aktivitas praktik klinik, kurikulum, dan master data keperawatan</p>
            </div>

            {/* Metrics */}
            <div className="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
                <StatCard label="Mahasiswa Aktif" value={totalMahasiswa} icon={GraduationCap} bg="bg-blue-100" text="text-blue-600" />
                <StatCard label="Dosen CI" value={totalDosen} icon={Users} bg="bg-violet-100" text="text-violet-600" />
                <StatCard label="Total Kasus" value={totalCareSessions} icon={FileText} bg="bg-emerald-100" text="text-emerald-600" />
                <StatCard label="Stase Kurikulum" value={totalCourses} icon={BookOpen} bg="bg-teal-100" text="text-teal-600" />
                <StatCard label="Kamus 3S PPNI" value={totalSdki} icon={ClipboardList} bg="bg-amber-100" text="text-amber-600" />
                <StatCard label="Katalog SPO" value={totalSpo} icon={HeartPulse} bg="bg-rose-100" text="text-rose-600" />
            </div>

            {/* Two Column Grid */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {/* Courses Distribution */}
                <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    <div className="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                        <h3 className="font-bold text-slate-900 text-sm">Distribusi Stase Kurikulum</h3>
                        <Link href="/admin/courses" className="text-xs text-[#008D88] font-bold hover:underline">
                            Kelola
                        </Link>
                    </div>

                    <div className="space-y-3">
                        {courses?.map((c) => (
                            <div key={c.id} className="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <div className="font-bold text-xs text-slate-900">{c.code}</div>
                                    <div className="text-[11px] text-slate-500">{c.name}</div>
                                </div>
                                <span className="px-2.5 py-1 rounded-full text-xs font-bold bg-[#008D88]/10 text-[#008D88]">
                                    {c.care_sessions_count || 0} Kasus
                                </span>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Recent Clinical Sessions */}
                <div className="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    <div className="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                        <h3 className="font-bold text-slate-900 text-sm">Aktivitas Kasus Klinis Terbaru</h3>
                        <span className="text-xs text-slate-400">8 Terakhir</span>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th className="pb-2.5">Pasien & No RM</th>
                                    <th className="pb-2.5">Mahasiswa</th>
                                    <th className="pb-2.5">Stase</th>
                                    <th className="pb-2.5">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {recentSessions?.map((s) => (
                                    <tr key={s.id} className="hover:bg-slate-50/80 transition-colors">
                                        <td className="py-3">
                                            <div className="font-bold text-slate-900">{s.patient_name}</div>
                                            <div className="text-[11px] text-slate-400 font-mono">{s.medical_record_no || '-'}</div>
                                        </td>
                                        <td className="py-3">
                                            <div className="text-slate-800 font-semibold">{s.student?.name}</div>
                                            <div className="text-[11px] text-slate-400">{s.student?.nim_nip}</div>
                                        </td>
                                        <td className="py-3 font-semibold text-[#008D88]">
                                            {s.course?.code}
                                        </td>
                                        <td className="py-3">
                                            <span className={`px-2.5 py-0.5 rounded-full text-[11px] font-bold border ${statusLabel[s.status]?.badge || 'bg-slate-100 text-slate-700'}`}>
                                                {statusLabel[s.status]?.label || s.status}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthLayout>
    );
}
