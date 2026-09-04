<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>e-Askep Poltekkes Kemenkes Riau</title>

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

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#008D88] flex items-center justify-center text-white shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-tight">e-Askep Riau</h1>
                    <p class="text-xs text-slate-500">Poltekkes Kemenkes Riau</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    API Engine v1.0 Aktif
                </div>
                <a href="/api/v1/health" target="_blank" class="px-3.5 py-1.5 text-xs font-semibold text-[#008D88] bg-[#E6F5F4] hover:bg-[#d6eeec] rounded-lg transition-colors">
                    Cek Health Endpoint
                </a>
            </div>
        </div>
    </header>

    <!-- Main Hero & Dashboard Container -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Hero Section (Floating Card) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 lg:p-10 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-[#E6F5F4] rounded-full filter blur-3xl opacity-70 pointer-events-none"></div>

            <div class="max-w-3xl relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#E6F5F4] text-[#008D88] text-xs font-semibold uppercase tracking-wider mb-4">
                    Clinical Clean Minimalism • Shadcn UI
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Sistem Digitalisasi Asuhan Keperawatan & Logbook Praktikum Terintegrasi
                </h2>
                <p class="mt-3 text-base text-slate-600 leading-relaxed">
                    Presisi Pengkajian, Tertib Prosedur, dan Evaluasi Klinis Tanpa Kertas untuk Program Studi D-III dan Sarjana Terapan Keperawatan Poltekkes Kemenkes Riau.
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                        <svg class="w-3.5 h-3.5 text-[#008D88]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Laravel 11 REST API
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                        <svg class="w-3.5 h-3.5 text-[#008D88]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Flutter 3.x Mobile Client
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                        <svg class="w-3.5 h-3.5 text-[#008D88]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Standar 3S PPNI (SDKI, SLKI, SIKI)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                        <svg class="w-3.5 h-3.5 text-[#008D88]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        14 Tabel MySQL 8.0+
                    </span>
                </div>
            </div>
        </div>

        <!-- 3 Core Clinical Stages Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Card 1: KGD -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col transition-all hover:shadow-md">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#008D88] bg-[#E6F5F4] px-2.5 py-1 rounded-full">
                        WAT5.31.24
                    </span>
                    <div class="flex gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#DC2626]" title="Triase Merah"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#D97706]" title="Triase Kuning"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#16A34A]" title="Triase Hijau"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1E293B]" title="Triase Hitam"></span>
                    </div>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Keperawatan Gawat Darurat (KGD)</h3>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                            Triase 4 warna medis, Survei Primer (ABCDE), Survei Sekunder (AMPLE), checklist tindakan Sub-CPMK 2–13, dan serah terima format SBAR.
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-400">
                        Target: D-III Keperawatan Kelas D
                    </div>
                </div>
            </div>

            <!-- Card 2: KDM -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col transition-all hover:shadow-md">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#CA8A04] bg-[#FEF9C3] px-2.5 py-1 rounded-full">
                        WAT6.07.24
                    </span>
                    <span class="text-xs font-medium text-slate-400">9 Domain</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Kebutuhan Dasar Manusia (KDM)</h3>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                            9 domain Henderson, kepatuhan SPO teknis mandiri Sub-CPMK 1–9, prosedur 6 Benar Pemberian Obat, serta catatan perkembangan harian SOAP.
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-400">
                        Target: Sarjana Terapan Keperawatan
                    </div>
                </div>
            </div>

            <!-- Card 3: KMB -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col transition-all hover:shadow-md">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100 px-2.5 py-1 rounded-full">
                        WAT5.24.24
                    </span>
                    <span class="text-xs font-medium text-slate-400">Multi-Sistem</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Keperawatan Medikal Bedah (KMB)</h3>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                            Pengkajian 9 sistem tubuh komprehensif, genogram riwayat keluarga, pemeriksaan diagnostik penunjang EKG/Lab, dan modul asuhan perioperatif.
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-400">
                        Target: D-III Keperawatan
                    </div>
                </div>
            </div>
        </div>

        <!-- System Architecture & Default Accounts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Default Test Accounts (Floating Card) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#008D88]"></div>
                    <h3 class="text-base font-bold text-slate-900">Akun Pengguna Uji Coba (Seeder)</h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">
                    Gunakan kredensial berikut untuk menguji otentikasi REST API dan login sistem:
                </p>

                <div class="space-y-3">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-red-100 text-red-700">Admin</span>
                                <span class="font-semibold text-sm text-slate-900">Admin Prodi</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">admin@poltekkes-riau.ac.id • NIP: 198501012010121001</p>
                        </div>
                        <code class="text-xs font-mono bg-white px-2.5 py-1 rounded border border-slate-200 text-slate-700">admin123</code>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-700">Dosen / CI</span>
                                <span class="font-semibold text-sm text-slate-900">Ns. Hj. Suryani, M.Kep.</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">dosen@poltekkes-riau.ac.id • NIP: 197903152005012002</p>
                        </div>
                        <code class="text-xs font-mono bg-white px-2.5 py-1 rounded border border-slate-200 text-slate-700">dosen123</code>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-100 text-sky-700">Mahasiswa</span>
                                <span class="font-semibold text-sm text-slate-900">Ahmad Fadhil Pratama</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">mahasiswa@poltekkes-riau.ac.id • NIM: P032414401001</p>
                        </div>
                        <code class="text-xs font-mono bg-white px-2.5 py-1 rounded border border-slate-200 text-slate-700">mhs123</code>
                    </div>
                </div>
            </div>

            <!-- REST API Quick Endpoints -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#EAB308]"></div>
                    <h3 class="text-base font-bold text-slate-900">Katalog Endpoint REST API V1</h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">
                    Seluruh endpoint telah diuji via automated test (`ApiV1Test`):
                </p>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] bg-sky-100 text-sky-700">POST</span>
                            <span class="font-mono text-slate-800">/api/v1/auth/login</span>
                        </div>
                        <span class="text-slate-400">Sanctum Token</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] bg-emerald-100 text-emerald-700">GET</span>
                            <span class="font-mono text-slate-800">/api/v1/master/sdki-slki-siki</span>
                        </div>
                        <span class="text-slate-400">Kamus 3S PPNI</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] bg-emerald-100 text-emerald-700">GET</span>
                            <span class="font-mono text-slate-800">/api/v1/master/spo-procedures</span>
                        </div>
                        <span class="text-slate-400">Checklist SPO</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] bg-sky-100 text-sky-700">POST</span>
                            <span class="font-mono text-slate-800">/api/v1/sessions</span>
                        </div>
                        <span class="text-slate-400">Inisiasi Kasus Pasien</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] bg-amber-100 text-amber-700">PUT</span>
                            <span class="font-mono text-slate-800">/api/v1/sessions/{id}/draft</span>
                        </div>
                        <span class="text-slate-400">Auto-Save Draf Klinis</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] bg-purple-100 text-purple-700">POST</span>
                            <span class="font-mono text-slate-800">/api/v1/ci/verify-procedures</span>
                        </div>
                        <span class="text-slate-400">Batch E-Paraf CI</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] bg-purple-100 text-purple-700">POST</span>
                            <span class="font-mono text-slate-800">/api/v1/ci/sessions/{id}/grade</span>
                        </div>
                        <span class="text-slate-400">Rubrik Skor Sub-CPMK</span>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} Poltekkes Kemenkes Riau. Sistem Informasi Asuhan Keperawatan & Logbook Praktikum Klinis Terintegrasi.
        </div>
    </footer>

</body>
</html>
