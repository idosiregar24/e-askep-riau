<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>e-Askep Poltekkes Kemenkes Riau</title>
    <meta name="description" content="Sistem e-Askep Poltekkes Kemenkes Riau - Platform digital asuhan keperawatan berbasis SDKI, SLKI, SIKI dan SPO PPNI untuk mahasiswa dan dosen pembimbing klinik.">

    <!-- Official Kemenkes Icon & Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('asset/images/kemenkes-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('asset/images/icon.svg') }}">

    <!-- Preconnect for fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="antialiased">
    @inertia
</body>
</html>
