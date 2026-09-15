import { Head, Link, useForm } from '@inertiajs/react';
import { Eye, EyeOff, Stethoscope, ArrowRight, ArrowLeft, AlertCircle, Lock, User, ClipboardList, BarChart3, CheckCircle2, Printer } from 'lucide-react';
import { useState } from 'react';

export default function Login() {
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <>
            <Head title="Masuk ke Sistem" />

            <div className="min-h-screen flex">
                {/* Left Panel – Brand */}
                <div className="hidden lg:flex lg:w-[48%] xl:w-[55%] relative bg-[#00736F] border-r border-[#005F5C] flex-col items-center justify-center p-12 overflow-hidden">
                    <div className="relative z-10 text-center max-w-md">
                        {/* Logo */}
                        <div className="bg-white rounded-2xl p-4 inline-flex items-center justify-center shadow-xs mb-6">
                            <img src="/asset/images/kemenkes-logo.png" alt="Kemenkes Poltekkes Riau" className="h-12 w-auto object-contain" />
                        </div>

                        <h1 className="text-3xl font-extrabold text-white mb-3 leading-tight">
                            e-Askep Riau
                        </h1>
                        <p className="text-white/80 text-sm leading-relaxed mb-8">
                            Platform dokumentasi asuhan keperawatan digital berbasis standar <strong className="text-white">SDKI · SLKI · SIKI</strong> dan SPO PPNI untuk Poltekkes Kemenkes Riau.
                        </p>

                        <div className="grid grid-cols-2 gap-3 text-left">
                            {[
                                { icon: ClipboardList, text: 'Pengkajian Klinis Terstruktur' },
                                { icon: BarChart3, text: 'Penilaian Rubrik Berbobot' },
                                { icon: CheckCircle2, text: 'Verifikasi SPO Real-time' },
                                { icon: Printer, text: 'Cetak Dokumen Resmi' },
                            ].map((item) => (
                                <div key={item.text} className="flex items-center gap-2.5 bg-white/10 rounded-xl px-3 py-2.5 border border-white/10">
                                    <item.icon size={16} className="text-white shrink-0" />
                                    <span className="text-white/90 text-xs font-medium leading-tight">{item.text}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="absolute bottom-6 left-0 right-0 text-center text-white/50 text-xs">
                        Poltekkes Kemenkes Riau · Jurusan Keperawatan
                    </div>
                </div>

                {/* Right Panel – Login Form */}
                <div className="flex-1 flex items-center justify-center p-6 sm:p-10 bg-white">
                    <div className="w-full max-w-sm">
                        {/* Mobile Logo */}
                        <div className="flex lg:hidden items-center gap-3 mb-8">
                            <img src="/asset/images/kemenkes-logo.png" alt="Kemenkes Poltekkes Riau" className="h-9 w-auto object-contain" />
                            <div className="border-l border-slate-200 pl-3">
                                <div className="font-bold text-slate-900 text-sm">e-Askep Riau</div>
                                <div className="text-[10px] text-slate-500">Poltekkes Kemenkes Riau</div>
                            </div>
                        </div>

                        <div className="mb-8">
                            <h2 className="text-2xl font-black text-slate-900 mb-1">Selamat datang kembali</h2>
                            <p className="text-slate-500 text-sm">Masukkan kredensial Anda untuk mengakses sistem e-Askep.</p>
                        </div>

                        {/* Global Error */}
                        {errors.email && !data.email && (
                            <div className="mb-5 flex items-start gap-3 p-3.5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                                <AlertCircle size={16} className="mt-0.5 shrink-0" />
                                <span>{errors.email}</span>
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-5">
                            {/* Email / NIM/NIP Field */}
                            <div>
                                <label className="block text-sm font-semibold text-slate-700 mb-1.5" htmlFor="email">
                                    Email atau NIM/NIP
                                </label>
                                <div className="relative">
                                    <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                        <User size={16} />
                                    </div>
                                    <input
                                        id="email"
                                        type="text"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="email@poltekkes.ac.id atau NIM/NIP"
                                        autoFocus
                                        autoComplete="username"
                                        className={`w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm transition-all duration-200 outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 ${
                                            errors.email
                                                ? 'border-red-300 focus:border-red-400'
                                                : 'border-slate-200 focus:border-[#008D88]'
                                        }`}
                                    />
                                </div>
                                {errors.email && (
                                    <p className="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                        <AlertCircle size={12} /> {errors.email}
                                    </p>
                                )}
                            </div>

                            {/* Password Field */}
                            <div>
                                <div className="flex items-center justify-between mb-1.5">
                                    <label className="block text-sm font-semibold text-slate-700" htmlFor="password">
                                        Kata Sandi
                                    </label>
                                </div>
                                <div className="relative">
                                    <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                        <Lock size={16} />
                                    </div>
                                    <input
                                        id="password"
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Masukkan kata sandi Anda"
                                        autoComplete="current-password"
                                        className={`w-full pl-10 pr-10 py-2.5 rounded-xl border text-sm transition-all duration-200 outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 ${
                                            errors.password
                                                ? 'border-red-300 focus:border-red-400'
                                                : 'border-slate-200 focus:border-[#008D88]'
                                        }`}
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                                    >
                                        {showPassword ? <EyeOff size={16} /> : <Eye size={16} />}
                                    </button>
                                </div>
                                {errors.password && (
                                    <p className="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                        <AlertCircle size={12} /> {errors.password}
                                    </p>
                                )}
                            </div>

                            {/* Remember Me */}
                            <div className="flex items-center gap-2">
                                <input
                                    id="remember"
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="w-4 h-4 rounded border-slate-300 text-[#008D88] accent-[#008D88] cursor-pointer"
                                />
                                <label htmlFor="remember" className="text-sm text-slate-600 cursor-pointer">
                                    Ingat saya selama 30 hari
                                </label>
                            </div>

                            {/* Submit */}
                            <button
                                type="submit"
                                id="btn-login"
                                disabled={processing}
                                className="w-full flex items-center justify-center gap-2 bg-[#008D88] hover:bg-[#00736F] disabled:opacity-60 disabled:cursor-not-allowed text-white py-3 rounded-xl text-sm font-bold transition-colors shadow-xs"
                            >
                                {processing ? (
                                    <>
                                        <svg className="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        Memverifikasi...
                                    </>
                                ) : (
                                    <>
                                        Masuk ke Sistem
                                        <ArrowRight size={16} />
                                    </>
                                )}
                            </button>
                        </form>

                        {/* Register Link */}
                        <div className="mt-6 text-center text-sm text-slate-500">
                            Belum punya akun?{' '}
                            <Link href="/register" className="text-[#008D88] font-semibold hover:underline">
                                Daftar sekarang
                            </Link>
                        </div>

                        {/* Back to Home */}
                        <div className="mt-3 text-center">
                            <Link href="/" className="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 transition-colors">
                                <ArrowLeft size={12} />
                                Kembali ke Halaman Utama
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
