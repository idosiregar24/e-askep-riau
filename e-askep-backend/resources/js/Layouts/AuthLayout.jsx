import { Link, router, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import {
    LayoutDashboard, FileText, Users, BookOpen,
    LogOut, Menu, X, ChevronDown, Bell,
    Stethoscope, ClipboardList, Settings, HeartPulse, UserCheck,
    CheckCircle2, AlertTriangle, XCircle
} from 'lucide-react';

function NavLink({ href, icon: Icon, label, active }) {
    return (
        <Link
            href={href}
            className={`flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors ${
                active
                    ? 'bg-[#E6F5F4] text-[#008D88] font-semibold border border-[#008D88]/20 shadow-xs'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
            }`}
        >
            <Icon size={18} className={active ? 'text-[#008D88]' : 'text-slate-400'} />
            <span>{label}</span>
        </Link>
    );
}

export default function AuthLayout({ children, title }) {
    const { auth, flash } = usePage().props;
    const user = auth.user;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [showFlash, setShowFlash] = useState(true);

    useEffect(() => {
        setShowFlash(true);
        const timer = setTimeout(() => setShowFlash(false), 5000);
        return () => clearTimeout(timer);
    }, [flash]);

    const handleLogout = () => {
        router.post('/logout');
    };

    const currentPath = window.location.pathname;

    const roleLabel = {
        mahasiswa: 'Mahasiswa',
        dosen: 'Dosen Pembimbing',
        admin: 'Administrator',
    }[user?.role] || 'Pengguna';

    const roleColor = {
        mahasiswa: 'bg-blue-100 text-blue-700',
        dosen: 'bg-violet-100 text-violet-700',
        admin: 'bg-amber-100 text-amber-700',
    }[user?.role] || 'bg-slate-100 text-slate-600';

    return (
        <div className="min-h-screen bg-slate-50 flex">
            {/* Sidebar Overlay (mobile) */}
            {sidebarOpen && (
                <div
                    className="fixed inset-0 bg-black/40 z-40 lg:hidden"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            {/* Sidebar Modular Poltekkes Kemenkes Riau */}
            <aside className={`
                fixed top-0 left-0 h-screen w-72 bg-[#FAFAFA] border-r border-slate-200/90 shadow-xl z-50
                flex flex-col transition-transform duration-300 ease-in-out shrink-0
                ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'}
                lg:translate-x-0 lg:sticky lg:top-0 lg:shadow-none lg:z-30
            `}>
                {/* Brand Header */}
                <div className="p-5 border-b border-slate-200/80 flex items-center justify-between">
                    <Link href="/" className="flex items-center gap-2.5">
                        <img src="/asset/images/kemenkes-logo.png" alt="Kemenkes Poltekkes Riau" className="h-8 w-auto object-contain" />
                        <div className="border-l border-slate-200 pl-2.5">
                            <span className="text-sm font-bold tracking-tight text-slate-900 block leading-tight">e-Askep Riau</span>
                            <span className="text-[10px] font-medium text-slate-500 block">Jurusan Keperawatan</span>
                        </div>
                    </Link>
                    <button
                        onClick={() => setSidebarOpen(false)}
                        className="lg:hidden text-slate-400 hover:text-slate-600 p-1"
                    >
                        <X size={18} />
                    </button>
                </div>

                {/* Navigation Links */}
                <div className="flex-1 px-4 py-5 space-y-6 overflow-y-auto">
                    {user?.role === 'admin' && (
                        <div>
                            <div className="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                                Administrasi Akademik
                            </div>
                            <nav className="space-y-1">
                                <NavLink
                                    href="/admin/dashboard"
                                    icon={LayoutDashboard}
                                    label="Dashboard Admin"
                                    active={currentPath === '/admin/dashboard'}
                                />
                                <NavLink
                                    href="/admin/users"
                                    icon={UserCheck}
                                    label="Kelola Pengguna"
                                    active={currentPath === '/admin/users' || currentPath.startsWith('/admin/users/')}
                                />
                                <NavLink
                                    href="/admin/courses"
                                    icon={BookOpen}
                                    label="Stase & Kurikulum"
                                    active={currentPath === '/admin/courses'}
                                />
                                <NavLink
                                    href="/admin/groups"
                                    icon={Users}
                                    label="Kelompok Praktik"
                                    active={currentPath === '/admin/groups'}
                                />
                                <NavLink
                                    href="/admin/master-3s"
                                    icon={ClipboardList}
                                    label="Master 3S PPNI"
                                    active={currentPath === '/admin/master-3s'}
                                />
                                <NavLink
                                    href="/admin/spo"
                                    icon={HeartPulse}
                                    label="Katalog SPO"
                                    active={currentPath === '/admin/spo'}
                                />
                            </nav>
                        </div>
                    )}

                    {user?.role === 'dosen' && (
                        <div>
                            <div className="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                                Meja Telaah Klinis
                            </div>
                            <nav className="space-y-1">
                                <NavLink
                                    href="/dosen/dashboard"
                                    icon={LayoutDashboard}
                                    label="Dashboard Telaah"
                                    active={currentPath === '/dosen/dashboard'}
                                />
                            </nav>
                        </div>
                    )}

                    {user?.role === 'mahasiswa' && (
                        <div>
                            <div className="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                                Asuhan Keperawatan
                            </div>
                            <nav className="space-y-1">
                                <NavLink
                                    href="/mahasiswa/dashboard"
                                    icon={LayoutDashboard}
                                    label="Kasus Pasien Saya"
                                    active={currentPath === '/mahasiswa/dashboard'}
                                />
                                <NavLink
                                    href="/mahasiswa/kasus/baru"
                                    icon={FileText}
                                    label="Input Kasus Baru"
                                    active={currentPath === '/mahasiswa/kasus/baru'}
                                />
                            </nav>
                        </div>
                    )}
                </div>

                {/* User Profile Footer Card */}
                <div className="p-4 border-t border-slate-200/80 bg-white">
                    <div className="flex items-center gap-3">
                        <div className="w-9 h-9 rounded-full bg-[#E6F5F4] text-[#008D88] flex items-center justify-center font-bold text-sm shrink-0 border border-[#008D88]/30">
                            {user?.name?.charAt(0)?.toUpperCase() || 'U'}
                        </div>
                        <div className="min-w-0 flex-1">
                            <div className="text-sm font-semibold text-slate-800 truncate">{user?.name}</div>
                            <div className="text-xs text-slate-500 truncate flex items-center gap-1.5">
                                <span className={`inline-block w-1.5 h-1.5 rounded-full ${user?.role === 'admin' ? 'bg-amber-500' : user?.role === 'dosen' ? 'bg-violet-500' : 'bg-blue-500'}`} />
                                <span>{roleLabel}</span>
                            </div>
                        </div>
                        <button
                            onClick={handleLogout}
                            title="Keluar dari Sistem"
                            className="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors shrink-0 cursor-pointer"
                        >
                            <LogOut size={16} />
                        </button>
                    </div>
                </div>
            </aside>

            {/* Main Content */}
            <div className="flex-1 flex flex-col min-w-0">
                {/* Top Bar */}
                <header className="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200/80 h-14 flex items-center justify-between px-4 lg:px-6">
                    <div className="flex items-center gap-3">
                        <button
                            onClick={() => setSidebarOpen(!sidebarOpen)}
                            className="lg:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors text-slate-500"
                        >
                            {sidebarOpen ? <X size={20} /> : <Menu size={20} />}
                        </button>
                        {title && (
                            <h1 className="text-sm font-semibold text-slate-800">{title}</h1>
                        )}
                    </div>
                    <div className="flex items-center gap-2">
                        <div className="hidden sm:flex items-center gap-2 text-sm text-slate-600">
                            <span className="font-medium">{user?.name}</span>
                            <span className={`px-2 py-0.5 rounded text-[11px] font-bold ${roleColor}`}>{roleLabel}</span>
                        </div>
                    </div>
                </header>

                {/* Flash Messages */}
                {showFlash && (flash?.success || flash?.warning || flash?.error) && (
                    <div className="px-4 lg:px-6 pt-4">
                        {flash?.success && (
                            <div className="flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800">
                                <CheckCircle2 size={16} className="text-emerald-500 mt-0.5 shrink-0" />
                                <span>{flash.success}</span>
                                <button onClick={() => setShowFlash(false)} className="ml-auto text-emerald-400 hover:text-emerald-600">
                                    <X size={14} />
                                </button>
                            </div>
                        )}
                        {flash?.warning && (
                            <div className="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                                <AlertTriangle size={16} className="text-amber-500 mt-0.5 shrink-0" />
                                <span>{flash.warning}</span>
                                <button onClick={() => setShowFlash(false)} className="ml-auto text-amber-400 hover:text-amber-600">
                                    <X size={14} />
                                </button>
                            </div>
                        )}
                        {flash?.error && (
                            <div className="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800">
                                <XCircle size={16} className="text-red-500 mt-0.5 shrink-0" />
                                <span>{flash.error}</span>
                                <button onClick={() => setShowFlash(false)} className="ml-auto text-red-400 hover:text-red-600">
                                    <X size={14} />
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {/* Page Content */}
                <main className="flex-1 p-4 lg:p-6">
                    {children}
                </main>
            </div>
        </div>
    );
}
