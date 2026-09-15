import { Head, Link } from '@inertiajs/react';
import { Stethoscope, ArrowRight, CheckCircle2, Users, BookOpen, ClipboardList, Shield } from 'lucide-react';

const features = [
    {
        icon: ClipboardList,
        title: 'Pengkajian Klinis Digital',
        desc: 'Input data pasien, vital sign, dan pengkajian KDM/KGD/KMB secara terstruktur dan real-time.',
        iconBg: 'bg-teal-50 text-teal-700 border border-teal-200',
    },
    {
        icon: BookOpen,
        title: 'Rencana Asuhan 3S PPNI',
        desc: 'Penyusunan diagnosis SDKI, luaran SLKI, dan intervensi SIKI sesuai standar nasional PPNI.',
        iconBg: 'bg-blue-50 text-blue-700 border border-blue-200',
    },
    {
        icon: Users,
        title: 'Telaah & Verifikasi Dosen',
        desc: 'Dosen CI memverifikasi SPO, memberikan catatan revisi, dan menetapkan penilaian akhir berbobot.',
        iconBg: 'bg-purple-50 text-purple-700 border border-purple-200',
    },
    {
        icon: Shield,
        title: 'Dokumentasi Teraudit',
        desc: 'Setiap perubahan tercatat dengan timestamp dan e-paraf digital dosen pembimbing klinik.',
        iconBg: 'bg-amber-50 text-amber-700 border border-amber-200',
    },
];

const stats = [
    { number: '3S', label: 'Standar PPNI (SDKI·SLKI·SIKI)' },
    { number: '100%', label: 'Berbasis Web & Mobile' },
    { number: 'Real-time', label: 'Telaah & Penilaian Dosen' },
];

export default function Welcome({ auth }) {
    return (
        <>
            <Head title="Beranda — e-Askep Poltekkes Riau" />

            <div className="min-h-screen bg-[#F1F5F9] font-sans">
                {/* Navigation */}
                <header className="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-xs">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                        <a href="/" className="flex items-center gap-3 group">
                            <img src="/asset/images/kemenkes-logo.png" alt="Kemenkes Poltekkes Riau" className="h-10 w-auto object-contain" />
                            <div className="border-l border-slate-200 pl-3">
                                <div className="flex items-center gap-2">
                                    <span className="text-base font-extrabold text-slate-900 tracking-tight">e-Askep</span>
                                    <span className="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#E6F5F4] text-[#008D88] border border-[#008D88]/30">
                                        Resmi
                                    </span>
                                </div>
                                <p className="text-[11px] text-slate-500 font-medium hidden sm:block">Jurusan Keperawatan</p>
                            </div>
                        </a>

                        <nav className="flex items-center gap-2">
                            {auth?.user ? (
                                <Link
                                    href={
                                        auth.user.role === 'admin' ? '/admin/dashboard'
                                        : auth.user.role === 'dosen' ? '/dosen/dashboard'
                                        : '/mahasiswa/dashboard'
                                    }
                                    className="flex items-center gap-2 bg-[#008D88] hover:bg-[#00736F] text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors shadow-xs"
                                >
                                    Masuk ke Dashboard
                                    <ArrowRight size={15} />
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href="/login"
                                        className="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-colors"
                                    >
                                        Masuk
                                    </Link>
                                    <Link
                                        href="/register"
                                        className="flex items-center gap-2 bg-[#008D88] hover:bg-[#00736F] text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors shadow-xs"
                                    >
                                        Daftar Akun
                                        <ArrowRight size={15} />
                                    </Link>
                                </>
                            )}
                        </nav>
                    </div>
                </header>

                {/* Hero Section */}
                <section className="border-b border-slate-200 bg-white">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16 md:pt-16 md:pb-20">
                        <div className="max-w-3xl">
                            <h1 className="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight tracking-tight mb-5">
                                Platform <span className="text-[#008D88]">e-Askep</span>
                                <br />
                                Poltekkes Kemenkes Riau
                            </h1>

                            <p className="text-base sm:text-lg text-slate-600 leading-relaxed mb-8 max-w-2xl">
                                Sistem dokumentasi asuhan keperawatan digital berbasis standar <strong>SDKI · SLKI · SIKI</strong> dan <strong>SPO PPNI</strong> untuk mahasiswa keperawatan dan dosen pembimbing klinik Poltekkes Kemenkes Riau.
                            </p>

                            <div className="flex flex-col sm:flex-row gap-3">
                                <Link
                                    href="/register"
                                    className="inline-flex items-center justify-center gap-2 bg-[#008D88] hover:bg-[#00736F] text-white px-6 py-3 rounded-xl text-sm font-bold transition-colors shadow-xs"
                                >
                                    Mulai Daftar Sekarang
                                    <ArrowRight size={16} />
                                </Link>
                                <Link
                                    href="/login"
                                    className="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-6 py-3 rounded-xl text-sm font-semibold transition-colors shadow-xs"
                                >
                                    Sudah Punya Akun? Masuk
                                </Link>
                            </div>

                            {/* Trust Badges */}
                            <div className="mt-8 pt-6 border-t border-slate-100 flex flex-wrap gap-5">
                                {[
                                    'Standar PPNI 2023',
                                    'SDKI · SLKI · SIKI',
                                    'e-Paraf Dosen Sah',
                                    'Cetak PDF Resmi 1:1',
                                ].map((badge) => (
                                    <div key={badge} className="flex items-center gap-1.5 text-xs text-slate-600">
                                        <CheckCircle2 size={15} className="text-[#008D88]" />
                                        <span className="font-semibold">{badge}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </section>

                {/* Stats Bar */}
                <section className="border-b border-slate-200 bg-slate-50/75">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <div className="grid grid-cols-3 gap-4 divide-x divide-slate-200">
                            {stats.map((stat) => (
                                <div key={stat.label} className="text-center px-4">
                                    <div className="text-2xl sm:text-3xl font-extrabold text-slate-900">{stat.number}</div>
                                    <div className="text-xs sm:text-sm text-slate-500 font-medium mt-1">{stat.label}</div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Features Grid */}
                <section className="py-14 md:py-20">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-12">
                            <h2 className="text-2xl sm:text-3xl font-bold text-slate-900 mb-3">Fitur Sistem e-Askep</h2>
                            <p className="text-slate-600 text-sm sm:text-base max-w-2xl mx-auto">
                                Solusi lengkap untuk mendukung proses pembelajaran klinik keperawatan yang terstandarisasi dan ramah praktikum.
                            </p>
                        </div>

                        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            {features.map((feature) => (
                                <div
                                    key={feature.title}
                                    className="bg-white rounded-2xl p-6 border border-slate-200 hover:border-slate-300 shadow-xs transition-all"
                                >
                                    <div className={`w-11 h-11 rounded-xl ${feature.iconBg} flex items-center justify-center mb-4`}>
                                        <feature.icon size={20} />
                                    </div>
                                    <h3 className="font-bold text-slate-900 mb-2 text-sm">{feature.title}</h3>
                                    <p className="text-xs text-slate-600 leading-relaxed">{feature.desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* CTA Section */}
                <section className="pb-16 md:pb-20">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="bg-[#00736F] rounded-2xl p-8 sm:p-12 text-center text-white border border-[#005F5C] shadow-sm">
                            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white text-slate-800 text-xs font-semibold mb-5 shadow-xs">
                                <img src="/asset/images/kemenkes-logo.png" alt="Kemenkes Poltekkes Riau" className="h-5 w-auto" />
                            </div>
                            <h2 className="text-2xl sm:text-3xl font-extrabold text-white mb-3 leading-tight">
                                Siap Memulai Pembelajaran Klinik Digital?
                            </h2>
                            <p className="text-white/85 text-sm sm:text-base mb-8 max-w-xl mx-auto leading-relaxed">
                                Daftarkan diri Anda sekarang dan mulai mendokumentasikan asuhan keperawatan secara terstandar tanpa kendala format manual.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-3 justify-center">
                                <Link
                                    href="/register"
                                    className="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-[#00736F] px-6 py-3 rounded-xl font-bold text-sm transition-colors shadow-xs"
                                >
                                    Daftar Akun Mahasiswa
                                    <ArrowRight size={16} />
                                </Link>
                                <Link
                                    href="/login"
                                    className="inline-flex items-center justify-center gap-2 bg-[#005F5C] hover:bg-[#004D4A] text-white border border-white/20 px-6 py-3 rounded-xl font-semibold text-sm transition-colors"
                                >
                                    Masuk sebagai Dosen
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Footer */}
                <footer className="border-t border-slate-200 bg-white py-8">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <img src="/asset/images/kemenkes-logo.png" alt="Kemenkes Poltekkes Riau" className="h-9 w-auto object-contain" />
                            <div className="border-l border-slate-200 pl-3">
                                <div className="text-sm font-bold text-slate-900">e-Askep Poltekkes Kemenkes Riau</div>
                                <div className="text-xs text-slate-500">Jurusan Keperawatan &bull; Sistem Informasi Klinis</div>
                            </div>
                        </div>
                        <div className="text-xs text-slate-500 text-center">
                            © {new Date().getFullYear()} Poltekkes Kemenkes Riau. Hak cipta dilindungi undang-undang.
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
