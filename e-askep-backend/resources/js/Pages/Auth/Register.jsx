import { Head, Link, useForm } from '@inertiajs/react';
import { Eye, EyeOff, Stethoscope, ArrowRight, ArrowLeft, AlertCircle, Lock, User, Mail, IdCard, Check } from 'lucide-react';
import { useState } from 'react';

export default function Register() {
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        nim_nip: '',
        email: '',
        role: 'mahasiswa',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <>
            <Head title="Daftar Akun Baru" />

            <div className="min-h-screen flex">
                {/* Left Brand Panel */}
                <div className="hidden lg:flex lg:w-[40%] xl:w-[45%] relative bg-[#00736F] border-r border-[#005F5C] flex-col items-center justify-center p-12 overflow-hidden">
                    <div className="relative z-10 text-center max-w-xs">
                        <div className="bg-white rounded-2xl p-4 inline-flex items-center justify-center shadow-xs mb-6">
                            <img src="/asset/images/kemenkes-logo.png" alt="Kemenkes Poltekkes Riau" className="h-10 w-auto object-contain" />
                        </div>
                        <h1 className="text-2xl font-extrabold text-white mb-3">Bergabung dengan e-Askep Riau</h1>
                        <p className="text-white/80 text-xs leading-relaxed">
                            Daftarkan akun Anda untuk mulai mendokumentasikan asuhan keperawatan secara digital dan terstandar.
                        </p>

                        <div className="mt-8 space-y-3 text-left">
                            {[
                                'Akses standar SDKI · SLKI · SIKI lengkap',
                                'Pencatatan SPO tindakan per kasus',
                                'Telaah dan penilaian oleh Dosen CI',
                                'Cetak dokumen asuhan keperawatan resmi',
                            ].map((item) => (
                                <div key={item} className="flex items-center gap-2.5 text-white/90 text-xs">
                                    <div className="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                                        <Check size={10} className="text-white" />
                                    </div>
                                    {item}
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="absolute bottom-6 text-white/50 text-xs">
                        Poltekkes Kemenkes Riau · Jurusan Keperawatan
                    </div>
                </div>

                {/* Right Form Panel */}
                <div className="flex-1 flex items-center justify-center p-6 sm:p-10 bg-white overflow-y-auto">
                    <div className="w-full max-w-sm py-4">
                        {/* Mobile Logo */}
                        <div className="flex lg:hidden items-center gap-3 mb-6">
                            <img src="/asset/images/kemenkes-logo.png" alt="Kemenkes Poltekkes Riau" className="h-9 w-auto object-contain" />
                            <div className="border-l border-slate-200 pl-3">
                                <div className="font-bold text-slate-900 text-sm">e-Askep Riau</div>
                                <div className="text-[10px] text-slate-500">Poltekkes Kemenkes Riau</div>
                            </div>
                        </div>

                        <div className="mb-6">
                            <h2 className="text-2xl font-black text-slate-900 mb-1">Buat akun baru</h2>
                            <p className="text-slate-500 text-sm">Isi data berikut untuk mendaftar ke sistem e-Askep.</p>
                        </div>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            {/* Role Selection */}
                            <div>
                                <label className="block text-sm font-semibold text-slate-700 mb-1.5">Daftar sebagai</label>
                                <div className="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                                    {[
                                        { value: 'mahasiswa', label: 'Mahasiswa' },
                                        { value: 'dosen', label: 'Dosen CI' },
                                    ].map((role) => (
                                        <button
                                            key={role.value}
                                            type="button"
                                            onClick={() => setData('role', role.value)}
                                            className={`py-2 rounded-lg text-sm font-semibold transition-all duration-200 ${
                                                data.role === role.value
                                                    ? 'bg-white text-[#008D88] shadow-md shadow-slate-200'
                                                    : 'text-slate-500 hover:text-slate-700'
                                            }`}
                                        >
                                            {role.label}
                                        </button>
                                    ))}
                                </div>
                                {errors.role && <p className="mt-1 text-xs text-red-600">{errors.role}</p>}
                            </div>

                            {/* Full Name */}
                            <div>
                                <label className="block text-sm font-semibold text-slate-700 mb-1.5" htmlFor="name">
                                    Nama Lengkap
                                </label>
                                <div className="relative">
                                    <User size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                                    <input
                                        id="name"
                                        type="text"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="Nama sesuai identitas resmi"
                                        autoFocus
                                        className={`w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm transition-all outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 ${errors.name ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                                    />
                                </div>
                                {errors.name && <p className="mt-1 text-xs text-red-600 flex gap-1"><AlertCircle size={12} className="mt-0.5 shrink-0" />{errors.name}</p>}
                            </div>

                            {/* NIM/NIP */}
                            <div>
                                <label className="block text-sm font-semibold text-slate-700 mb-1.5" htmlFor="nim_nip">
                                    {data.role === 'mahasiswa' ? 'NIM (Nomor Induk Mahasiswa)' : 'NIP (Nomor Induk Pegawai)'}
                                </label>
                                <div className="relative">
                                    <IdCard size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                                    <input
                                        id="nim_nip"
                                        type="text"
                                        value={data.nim_nip}
                                        onChange={(e) => setData('nim_nip', e.target.value)}
                                        placeholder={data.role === 'mahasiswa' ? 'Contoh: 2241310001' : 'Contoh: 197501012000031001'}
                                        className={`w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm transition-all outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 ${errors.nim_nip ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                                    />
                                </div>
                                {errors.nim_nip && <p className="mt-1 text-xs text-red-600 flex gap-1"><AlertCircle size={12} className="mt-0.5 shrink-0" />{errors.nim_nip}</p>}
                            </div>

                            {/* Email */}
                            <div>
                                <label className="block text-sm font-semibold text-slate-700 mb-1.5" htmlFor="email">
                                    Alamat Email
                                </label>
                                <div className="relative">
                                    <Mail size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                                    <input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="email@poltekkes.ac.id"
                                        autoComplete="email"
                                        className={`w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm transition-all outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 ${errors.email ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                                    />
                                </div>
                                {errors.email && <p className="mt-1 text-xs text-red-600 flex gap-1"><AlertCircle size={12} className="mt-0.5 shrink-0" />{errors.email}</p>}
                            </div>

                            {/* Password */}
                            <div>
                                <label className="block text-sm font-semibold text-slate-700 mb-1.5" htmlFor="password">
                                    Kata Sandi
                                </label>
                                <div className="relative">
                                    <Lock size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                                    <input
                                        id="password"
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Minimal 6 karakter"
                                        autoComplete="new-password"
                                        className={`w-full pl-10 pr-10 py-2.5 rounded-xl border text-sm transition-all outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 ${errors.password ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                                    />
                                    <button type="button" onClick={() => setShowPassword(!showPassword)} className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                        {showPassword ? <EyeOff size={16} /> : <Eye size={16} />}
                                    </button>
                                </div>
                                {errors.password && <p className="mt-1 text-xs text-red-600 flex gap-1"><AlertCircle size={12} className="mt-0.5 shrink-0" />{errors.password}</p>}
                            </div>

                            {/* Password Confirmation */}
                            <div>
                                <label className="block text-sm font-semibold text-slate-700 mb-1.5" htmlFor="password_confirmation">
                                    Konfirmasi Kata Sandi
                                </label>
                                <div className="relative">
                                    <Lock size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                                    <input
                                        id="password_confirmation"
                                        type={showConfirm ? 'text' : 'password'}
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        placeholder="Ulangi kata sandi Anda"
                                        autoComplete="new-password"
                                        className={`w-full pl-10 pr-10 py-2.5 rounded-xl border text-sm transition-all outline-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#008D88]/20 ${errors.password_confirmation ? 'border-red-300' : 'border-slate-200 focus:border-[#008D88]'}`}
                                    />
                                    <button type="button" onClick={() => setShowConfirm(!showConfirm)} className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                        {showConfirm ? <EyeOff size={16} /> : <Eye size={16} />}
                                    </button>
                                </div>
                                {errors.password_confirmation && <p className="mt-1 text-xs text-red-600 flex gap-1"><AlertCircle size={12} className="mt-0.5 shrink-0" />{errors.password_confirmation}</p>}
                            </div>

                            {/* Submit */}
                            <button
                                type="submit"
                                id="btn-register"
                                disabled={processing}
                                className="w-full flex items-center justify-center gap-2 bg-[#008D88] hover:bg-[#00736F] disabled:opacity-60 disabled:cursor-not-allowed text-white py-3 rounded-xl text-sm font-bold transition-colors shadow-xs mt-2"
                            >
                                {processing ? (
                                    <>
                                        <svg className="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        Mendaftarkan akun...
                                    </>
                                ) : (
                                    <>
                                        Buat Akun Sekarang
                                        <ArrowRight size={16} />
                                    </>
                                )}
                            </button>
                        </form>

                        <div className="mt-5 text-center text-sm text-slate-500">
                            Sudah punya akun?{' '}
                            <Link href="/login" className="text-[#008D88] font-semibold hover:underline">
                                Masuk di sini
                            </Link>
                        </div>
                        <div className="mt-2 text-center">
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
