@props([
    'title'       => null,
    'description' => null,
    'keywords'    => null,
    'noindex'     => false,
    'schema'      => null,
    'wide'        => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-seo
        :title="$title"
        :description="$description"
        :keywords="$keywords"
        :noindex="$noindex"
        :schema="$schema"
    />

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-phone-repair.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    @stack('styles')
</head>

<body class="page {{ $wide ? 'page--wide' : '' }}">
    <a class="skip-link" href="#konten">Lompat ke konten utama</a>

    <header class="site-header">
        <a href="{{ route('home') }}" class="brand" aria-label="Beranda Phone Repair">
            <img src="{{ asset('images/logo-phone-repair.png') }}"
                 alt="Logo Phone Repair"
                 width="40" height="40" class="brand__logo">
            <span class="brand__name">Phone Repair</span>
        </a>

        <nav class="site-nav" aria-label="Navigasi utama">
            <a href="{{ route('servis.cek') }}"
               class="btn btn--ghost {{ request()->routeIs('servis.cek') ? 'is-active' : '' }}">
                Cek Status Servis
            </a>

            {{-- Tombol "Masuk" maupun "Dashboard" sengaja dihilangkan dari
                 halaman publik ini, tanpa terkecuali untuk staf yang sedang
                 login. Halaman ini murni untuk pelanggan; staf mengakses
                 panel lewat tautan /login yang diberikan terpisah. --}}
        </nav>
    </header>

    <main id="konten" class="site-main">
        {{ $slot }}
    </main>

    <footer class="site-footer">
        <p class="site-footer__copy">
            &copy; {{ date('Y') }} Phone Repair. Sistem pengelolaan layanan servis smartphone.
        </p>
        <nav class="site-footer__links" aria-label="Tautan legal">
            <a href="{{ route('servis.cek') }}">Cek Status</a>
            <a href="{{ route('legal.privasi') }}">Kebijakan Privasi</a>
            <a href="{{ route('legal.syarat') }}">Syarat &amp; Ketentuan</a>
        </nav>
    </footer>

    @stack('scripts')
</body>
</html>
