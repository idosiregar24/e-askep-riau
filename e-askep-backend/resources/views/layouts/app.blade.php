<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'e-Askep' }} — Poltekkes Kemenkes Riau</title>
    
    <!-- Official Kemenkes Icon & Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('asset/images/kemenkes-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('asset/images/icon.svg') }}">
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Vite & CDN Fallback for Zero-Config Preview) -->
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        },
                        colors: {
                            toska: {
                                DEFAULT: '#008D88',
                                hover: '#00736F',
                                light: '#E6F5F4',
                            },
                            gold: {
                                DEFAULT: '#EAB308',
                                dark: '#CA8A04',
                                light: '#FEF9C3',
                            },
                        }
                    }
                }
            }
        </script>
        <style>
            .card-clinical {
                background-color: #FFFFFF;
                border-radius: 1rem;
                border: 1px solid rgba(226, 232, 240, 0.9);
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            }
            .badge-triage-merah { background-color: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }
            .badge-triage-kuning { background-color: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; }
            .badge-triage-hijau { background-color: #F0FDF4; color: #16A34A; border: 1px solid #BBF7D0; }
            .badge-triage-hitam { background-color: #F1F5F9; color: #1E293B; border: 1px solid #CBD5E1; }
        </style>
    @endif
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-[#F1F5F9]" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex flex-col md:flex-row">
        
        <!-- Sidebar Navigation (Desktop & Mobile Drawer) -->
        @include('layouts.partials.sidebar')

        <!-- Main Workspace Canvas -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Topbar Header -->
            <header class="bg-white border-b border-slate-200/80 sticky top-0 z-20 px-4 sm:px-6 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('asset/images/kemenkes-logo.png') }}" alt="Kemenkes Poltekkes Riau" class="h-8 w-auto hidden sm:block object-contain">
                        <div class="sm:border-l sm:border-slate-200 sm:pl-2.5">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Poltekkes Kemenkes Riau</div>
                            <h1 class="text-base font-bold text-slate-900 leading-tight">{{ $headerTitle ?? 'Sistem e-Askep' }}</h1>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Role Pill Badge -->
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                        @if(Auth::user()->role === 'admin') bg-purple-50 text-purple-700 border border-purple-200
                        @elseif(Auth::user()->role === 'dosen') bg-[#E6F5F4] text-[#008D88] border border-[#008D88]/20
                        @else bg-blue-50 text-blue-700 border border-blue-200 @endif">
                        {{ strtoupper(Auth::user()->role) }}
                    </span>

                    <!-- User Name & Logout Form -->
                    <div class="hidden sm:block text-right">
                        <div class="text-sm font-semibold text-slate-900 leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ Auth::user()->nim_nip }}</div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar dari sistem" class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Content Container with Flash Notifications -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="text-sm font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div class="text-sm font-medium">{{ session('warning') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-xs">
                        <div class="font-semibold text-sm mb-1">Terdapat kesalahan input:</div>
                        <ul class="list-disc list-inside text-xs space-y-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
