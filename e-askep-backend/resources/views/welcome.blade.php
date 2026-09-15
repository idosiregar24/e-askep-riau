<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>e-Askep — Poltekkes Kemenkes Riau</title>

    <!-- Official Kemenkes Icon & Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('asset/images/kemenkes-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('asset/images/icon.svg') }}">

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 CDN for instant preview -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Plus Jakarta Sans', sans-serif;
            --color-kemenkes-toska: #008D88;
            --color-kemenkes-hover: #00736F;
            --color-kemenkes-tint: #E6F5F4;
            --color-poltekkes-gold: #EAB308;
            --color-poltekkes-gold-deep: #CA8A04;
            --color-slate-canvas: #F1F5F9;
        }
    </style>
</head>
<body class="bg-[#F1F5F9] font-sans antialiased text-slate-800 min-h-screen flex flex-col selection:bg-[#008D88] selection:text-white">

    <!-- Top Navigation Header with Login & Register Buttons -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/90 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-4">
            
            <!-- Brand Logo & Department -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('asset/images/kemenkes-logo.png') }}" alt="Kemenkes Poltekkes Riau" class="h-10 w-auto object-contain">
                <div class="border-l border-slate-200 pl-3">
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-bold text-slate-900 tracking-tight leading-tight">e-Askep Riau</span>
                        <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#E6F5F4] text-[#008D88] border border-[#008D88]/30">
                            Resmi
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Poltekkes Kemenkes Riau &bull; Jurusan Keperawatan</p>
                </div>
            </a>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-bold text-slate-600">
                <a href="#tentang" class="hover:text-[#008D88] transition-colors">Tentang Sistem</a>
                <a href="#stase" class="hover:text-[#008D88] transition-colors">Stase Kurikulum</a>
                <a href="#standar-3s" class="hover:text-[#008D88] transition-colors">Standar 3S PPNI</a>
                <a href="#alur" class="hover:text-[#008D88] transition-colors">Alur Telaah</a>
            </nav>

            <!-- Top Header Action Buttons: Login & Register -->
            <div class="flex items-center gap-2.5">
                @auth
                    <!-- Authenticated User Profile & Direct Dashboard Access -->
                    <div class="hidden sm:block text-right">
                        <span class="text-xs font-bold text-slate-900 block leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] uppercase font-bold text-[#008D88] font-mono">{{ Auth::user()->role }}</span>
                    </div>

                    <a href="{{ Auth::user()->isDosen() ? route('dosen.dashboard') : (Auth::user()->isAdmin() ? route('admin.dashboard') : route('mahasiswa.dashboard')) }}" 
                       class="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Buka Dashboard</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                @else
                    <!-- Guest User: Prominent Login & Register Buttons -->
                    <a href="{{ route('login') }}" 
                       class="px-4 py-2 rounded-xl border border-slate-300 hover:border-[#008D88] hover:text-[#008D88] bg-white text-slate-700 text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span>Masuk (Login)</span>
                    </a>

                    <a href="{{ route('register') }}" 
                       class="px-4 py-2 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Daftar (Register)</span>
                    </a>
                @endauth
            </div>

        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-12">

        <!-- Hero Section: Penjelasan Jelas Apa Itu Website e-Askep -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-10 lg:p-12 relative overflow-hidden">
            <div class="max-w-3xl relative z-10">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Sistem Digitalisasi Asuhan Keperawatan & Logbook Praktikum Terpadu
                </h1>

                <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                    <strong>e-Askep Poltekkes Kemenkes Riau</strong> adalah platform resmi berbasis web dan mobile untuk mendigitalkan seluruh siklus asuhan keperawatan (*Nursing Care Process*) dan logbook praktikum klinis mahasiswa di rumah sakit dan wahana praktik.
                </p>

                <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                    Menghubungkan <strong>Mahasiswa Keperawatan</strong> dengan <strong>Dosen Pembimbing & Clinical Instructor (CI)</strong> secara real-time: mulai dari pengkajian klinis, penalaran diagnosis terstandar 3S PPNI (SDKI, SLKI, SIKI), checklist tindakan SPO mandiri, pemantauan TTV time-series, hingga telaah, perbaikan revisi, dan pembubuhan <strong>e-paraf digital sah</strong> tanpa kertas.
                </p>

                <!-- Hero Action Buttons -->
                <div class="mt-8 flex flex-wrap items-center gap-3.5">
                    <a href="{{ route('login') }}" 
                       class="px-6 py-3.5 rounded-xl bg-[#008D88] hover:bg-[#00736F] text-white text-sm font-bold shadow-xs transition-colors flex items-center gap-2 cursor-pointer">
                        <span>Masuk ke Meja Kerja</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>

                    <a href="{{ route('register') }}" 
                       class="px-6 py-3.5 rounded-xl bg-white border border-slate-300 hover:border-[#008D88] hover:text-[#008D88] text-slate-800 text-sm font-bold shadow-xs transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#008D88]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Daftar Akun Baru</span>
                    </a>

                    <a href="/api/v1/health" target="_blank" 
                       class="px-4 py-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition-colors">
                        Cek Status Sistem API
                    </a>
                </div>

                <!-- 4 Feature Badges -->
                <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="flex items-center gap-2 text-slate-700">
                        <svg class="w-4 h-4 text-[#008D88]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>Standar 3S PPNI</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-700">
                        <svg class="w-4 h-4 text-[#008D88]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>Batch E-Paraf CI</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-700">
                        <svg class="w-4 h-4 text-[#008D88]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>Rubrik Sub-CPMK</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-700">
                        <svg class="w-4 h-4 text-[#008D88]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>Cetak Resmi A4 1:1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Siapa Pengguna e-Askep? (Role Overview) -->
        <div id="tentang" class="space-y-4">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Dirancang Khusus untuk Sivitas Akademika Keperawatan</h2>
                <p class="text-sm text-slate-500 mt-1">Tiga hak akses dengan alur kerja yang terstruktur dan aman.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                <!-- Role 1: Mahasiswa -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between hover:border-[#008D88]/50 transition-all">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Mahasiswa Praktikan</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mencatat data pasien, mengisi pengkajian klinis, merumuskan diagnosis 3S PPNI dengan penalaran data subjektif & objektif, mencentang checklist prosedur SPO, dan mengajukan telaah asuhan ke pembimbing.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-xs font-semibold text-blue-600">
                        Demo: mahasiswa@poltekkes-riau.ac.id
                    </div>
                </div>

                <!-- Role 2: Dosen / CI -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between hover:border-[#008D88]/50 transition-all">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#E6F5F4] text-[#008D88] flex items-center justify-center font-bold mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Dosen & Clinical Instructor (CI)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Memeriksa berkas kasus pada Meja Telaah, membubuhkan batch e-paraf pada prosedur tindakan SPO, memberikan catatan perbaikan/revisi, dan mengisi nilai kuantitatif pada rubrik evaluasi Sub-CPMK.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-xs font-semibold text-[#008D88]">
                        Demo: dosen@poltekkes-riau.ac.id
                    </div>
                </div>

                <!-- Role 3: Admin -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between hover:border-[#008D88]/50 transition-all">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Administrator Akademik</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mengelola stase kurikulum RPS (KGD, KDM, KMB), memetakan kelompok praktik mahasiswa ke dosen pembimbing klinik, mengelola katalog master 3S PPNI, serta mengawasi statistik asuhan seluruh prodi.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-xs font-semibold text-purple-600">
                        Demo: admin@poltekkes-riau.ac.id
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: 3 Stase Kurikulum RPS -->
        <div id="stase" class="space-y-4">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">3 Stase Praktik Kurikulum Terintegrasi</h2>
                <p class="text-sm text-slate-500 mt-1">Sesuai Rencana Pembelajaran Semester (RPS) Program Studi Keperawatan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                <!-- Stase KGD -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 font-mono">
                            WAT5.31.24
                        </span>
                        <span class="text-xs font-semibold text-slate-400">Semester 5</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Keperawatan Gawat Darurat (KGD)</h3>
                    <p class="text-xs text-slate-600 mb-4">
                        Fokus pada triase 4 warna (Merah, Kuning, Hijau, Hitam), survei primer ABCDE, pemantauan TTV serial, dan serah terima pasien format SBAR.
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>4 SKS Praktik</span>
                        <span class="font-bold text-[#008D88]">6 SPO Klinis</span>
                    </div>
                </div>

                <!-- Stase KDM -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 font-mono">
                            WAT6.07.24
                        </span>
                        <span class="text-xs font-semibold text-slate-400">Semester 2</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Kebutuhan Dasar Manusia (KDM)</h3>
                    <p class="text-xs text-slate-600 mb-4">
                        Pengkajian 9 domain kebutuhan dasar Virginia Henderson, verifikasi keselamatan 6 Benar Obat, serta penyusunan catatan perkembangan SOAP harian.
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>3 SKS Praktik</span>
                        <span class="font-bold text-[#008D88]">4 SPO Klinis</span>
                    </div>
                </div>

                <!-- Stase KMB -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 font-mono">
                            WAT5.24.24
                        </span>
                        <span class="text-xs font-semibold text-slate-400">Semester 4</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Keperawatan Medikal Bedah (KMB)</h3>
                    <p class="text-xs text-slate-600 mb-4">
                        Pemeriksaan fisik komprehensif 9 sistem organ (*Head-to-Toe*), anamnesis nyeri PQRST, dan pengkajian pemulihan perioperatif pasien bedah.
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>4 SKS Praktik</span>
                        <span class="font-bold text-[#008D88]">2 SPO Klinis</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Alur Siklus Hidup Praktik (Workflow) -->
        <div id="alur" class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-10">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Alur Kerja Praktikum Digital Tanpa Kertas</h2>
                <p class="text-sm text-slate-500 mt-1">Transparan, akuntabel, dan terkunci otomatis setelah disetujui dosen.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-center">
                <!-- Step 1 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-slate-300 text-slate-800 flex items-center justify-center font-bold text-xs mb-2">1</div>
                    <h4 class="font-bold text-xs text-slate-800">Draf Kasus</h4>
                    <p class="text-[11px] text-slate-500 mt-1">Mahasiswa mengisi instrumen pengkajian & rencana 3S.</p>
                </div>
                <!-- Step 2 -->
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-xs mb-2">2</div>
                    <h4 class="font-bold text-xs text-amber-900">Diajukan</h4>
                    <p class="text-[11px] text-amber-700 mt-1">Berkas diajukan ke antrean Dosen Pembimbing (Terkunci sementara).</p>
                </div>
                <!-- Step 3 -->
                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mb-2">3</div>
                    <h4 class="font-bold text-xs text-blue-900">Meja Telaah</h4>
                    <p class="text-[11px] text-blue-700 mt-1">Dosen memeriksa temuan klinis & e-paraf tindakan SPO.</p>
                </div>
                <!-- Step 4 -->
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center font-bold text-xs mb-2">4</div>
                    <h4 class="font-bold text-xs text-rose-900">Revisi (Opsional)</h4>
                    <p class="text-[11px] text-rose-700 mt-1">Jika ada koreksi data, dosen memberi feedback perbaikan.</p>
                </div>
                <!-- Step 5 -->
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs mb-2">5</div>
                    <h4 class="font-bold text-xs text-emerald-900">Sah & Dinilai</h4>
                    <p class="text-[11px] text-emerald-700 mt-1">Skor Sub-CPMK tersimpan, e-paraf sah, berkas siap cetak 1:1.</p>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer Resmi Poltekkes Kemenkes Riau -->
    <footer class="bg-white border-t border-slate-200/80 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#008D88] flex items-center justify-center text-white font-black text-sm">
                    +
                </div>
                <div>
                    <span class="text-sm font-bold text-slate-800">e-Askep Poltekkes Kemenkes Riau</span>
                    <p class="text-xs text-slate-500">Kementerian Kesehatan Republik Indonesia &bull; Direktorat Jenderal Tenaga Kesehatan</p>
                </div>
            </div>

            <div class="flex items-center gap-4 text-xs text-slate-500">
                <a href="{{ route('login') }}" class="hover:text-[#008D88] font-semibold">Masuk (Login)</a>
                <span>&bull;</span>
                <a href="{{ route('register') }}" class="hover:text-[#008D88] font-semibold">Daftar Akun</a>
                <span>&bull;</span>
                <span>&copy; {{ date('Y') }} Jurusan Keperawatan</span>
            </div>
        </div>
    </footer>

</body>
</html>
