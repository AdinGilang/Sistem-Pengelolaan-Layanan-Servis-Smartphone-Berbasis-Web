@php
    $userRole = auth()->user()?->role ?? 'admin';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Seluruh halaman panel berisi data pelanggan, jadi tidak boleh
         terindeks mesin pencari. Middleware SecurityHeaders juga mengirim
         header X-Robots-Tag yang sama sebagai lapisan kedua. --}}
    <title>{{ isset($header) ? $header . ' — ' : '' }}{{ config('seo.site_name', 'Phone Repair') }}</title>
    <meta name="robots" content="noindex, nofollow, noarchive">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: var(--font-sans); }
        [x-cloak] { display: none !important; }

        .sidebar {
            background: var(--navy);
            min-height: 100vh;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--r-md);
            color: rgba(255, 255, 255, .6);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color .18s ease, color .18s ease;
            margin-bottom: 2px;
        }

        .sidebar-nav-item:hover { background: var(--navy-soft); color: #fff; }

        .sidebar-nav-item.active {
            background: var(--blue);
            color: #fff;
        }

        .sidebar-nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }

        .sidebar-nav-item .nav-badge {
            margin-left: auto;
            background: rgba(255, 255, 255, .15);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .sidebar-nav-item.active .nav-badge { background: rgba(255, 255, 255, .25); }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            color: #8a93b2;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 0 8px;
            margin: 16px 0 6px;
            display: block;
        }

        .role-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .role-badge.admin { background: rgba(59, 91, 219, .25); color: #a9bcff; }
        .role-badge.owner { background: rgba(245, 159, 0, .25); color: #ffd98a; }

        /* Warna di atas kontras dirancang untuk latar navy sidebar. Badge
           yang sama juga dipakai di topbar yang berlatar putih — warna
           terang di atas latar terang itu jadi pucat dan sulit dibaca
           (persis seperti terlihat pada tangkapan layar: "ADMIN" tampak
           kusam dibanding avatar biru di sampingnya). Aturan ini menimpa
           warnanya khusus saat berada di dalam topbar, tanpa perlu
           mengubah markup di halaman mana pun yang memakainya. */
        .topbar .role-badge.admin { background: var(--blue-wash); color: var(--blue-dark); }
        .topbar .role-badge.owner { background: var(--amber-wash); color: var(--amber); }

        .avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--blue);
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            flex-shrink: 0;
        }

        /* Tombol logout: efek sorot ditulis sebagai CSS, bukan atribut
           onmouseover yang menulis ulang style lewat JavaScript inline. */
        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--r-md);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            color: #ff8787;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background-color .18s ease;
        }

        .logout-btn:hover { background: rgba(250, 82, 82, .14); }
        .logout-btn svg { width: 17px; height: 17px; }

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--line);
        }

        .topbar-back {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--muted);
            text-decoration: none;
            margin-bottom: 2px;
            transition: color .15s ease;
        }

        .topbar-back:hover { color: var(--blue); }
        .topbar-back svg { width: 14px; height: 14px; flex-shrink: 0; }
    </style>

    @stack('styles')
</head>

<body class="antialiased" style="background: var(--bg); color: var(--text);">

<a class="skip-link" href="#konten">Lompat ke konten utama</a>

<div x-data="{ open: false }" class="min-h-screen flex">

    {{-- ═══════════════════════ SIDEBAR ═══════════════════════ --}}
    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="sidebar fixed inset-y-0 left-0 w-60 flex flex-col transform transition-transform duration-300 z-40 md:relative md:translate-x-0">

        <div class="flex items-center gap-3 px-5 py-6" style="border-bottom:1px solid rgba(255,255,255,.07);">
            <div class="flex items-center justify-center rounded-xl flex-shrink-0"
                 style="width:36px;height:36px;background:var(--blue);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <span class="text-white font-bold text-base tracking-tight">PHONE REPAIR</span>
            <button @click="open = false" type="button"
                    class="md:hidden ml-auto text-white opacity-60 hover:opacity-100"
                    aria-label="Tutup menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-3 pt-5 overflow-y-auto" aria-label="Navigasi panel">
            <span class="nav-section-label">Menu</span>

            <a href="{{ route('dashboard') }}"
               class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('servis.index') }}"
               class="sidebar-nav-item {{ request()->routeIs('servis.*') ? 'active' : '' }}"
               @if (request()->routeIs('servis.*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/>
                </svg>
                Data Servis
                {{-- Nilai ini dibagikan view composer dan di-cache satu menit,
                     tidak lagi query langsung dari dalam template. --}}
                <span class="nav-badge">{{ $jumlahServis ?? 0 }}</span>
            </a>

            @if ($userRole === 'owner')
                <span class="nav-section-label">Manajemen</span>

                <a href="{{ route('laporan.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/>
                    </svg>
                    Laporan
                </a>

                <a href="{{ route('statistik.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('statistik.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/>
                    </svg>
                    Statistik
                </a>
            @endif

            @if ($userRole === 'admin')
                <span class="nav-section-label">Pengaturan</span>

                <a href="{{ route('setting.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('setting.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    Pengaturan Nota
                </a>
            @endif
        </nav>

        <div class="px-3 pb-6 pt-4" style="border-top:1px solid rgba(255,255,255,.07);">
            <div class="flex items-center gap-3 px-3 py-2 rounded-xl mb-1">
                <div class="avatar" style="width:34px;height:34px;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-white font-semibold truncate" style="font-size:13px;">
                        {{ auth()->user()->name ?? 'Pengguna' }}
                    </div>
                    <div style="margin-top:2px;">
                        <span class="role-badge {{ $userRole }}">{{ ucfirst($userRole) }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div x-show="open" @click="open = false" x-cloak
         class="fixed inset-0 z-30 md:hidden"
         style="background:rgba(0,0,0,.45);"
         x-transition.opacity.duration.200ms></div>

    {{-- ═══════════════════════ AREA UTAMA ═══════════════════════ --}}
    <div class="flex-1 flex flex-col min-w-0">

        <header class="topbar sticky top-0 z-20 flex items-center justify-between px-4 sm:px-8 py-4">
            <div class="flex items-center gap-4">
                <button @click="open = true" type="button"
                        class="md:hidden flex items-center justify-center rounded-lg"
                        style="width:36px;height:36px;color:var(--muted);"
                        aria-label="Buka menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                        <path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h18"/>
                    </svg>
                </button>

                <div>
                    {{-- Tautan kembali ditaruh di topbar yang sticky, bukan
                         hanya di bagian bawah halaman. Sebelumnya halaman
                         detail dan formulir sepanjang 300-600 baris hanya
                         punya satu tombol "Kembali" di paling bawah — admin
                         harus menggulir seluruh halaman dulu untuk kembali
                         ke daftar. Sekarang tautannya selalu terlihat,
                         di halaman mana pun posisi gulir sedang berada. --}}
                    @isset($backTo)
                        <a href="{{ $backTo }}" class="topbar-back">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>
                            </svg>
                            {{ $backLabel ?? 'Kembali' }}
                        </a>
                    @endisset

                    @isset($header)
                        <h1 style="font-size:18px;font-weight:700;color:var(--navy);line-height:1.2;margin:0;">
                            {{ $header }}
                        </h1>
                    @endisset
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="role-badge {{ $userRole }} hidden sm:inline-block">{{ ucfirst($userRole) }}</span>
                <div class="avatar" style="width:32px;height:32px;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <span class="hidden sm:block font-medium" style="font-size:14px;color:var(--navy);">
                    {{ auth()->user()->name ?? '' }}
                </span>
            </div>
        </header>

        <main id="konten" class="flex-1 py-7 px-4 sm:px-8">
            <div class="max-w-7xl mx-auto">

                @foreach (['success' => 'green', 'error' => 'red'] as $jenis => $warna)
                    @if (session($jenis))
                        <div x-data="{ show: true }"
                             x-init="setTimeout(() => show = false, 4000)"
                             x-show="show" x-cloak
                             x-transition.opacity.duration.200ms
                             class="mb-6" role="status">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl"
                                 style="background:var(--{{ $warna }}-wash);border:1px solid var(--{{ $warna }});color:var(--{{ $warna }});">
                                <span class="font-medium" style="font-size:14px;">{{ session($jenis) }}</span>
                                <button @click="show = false" type="button" class="opacity-60 hover:opacity-100"
                                        aria-label="Tutup pesan">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{ $slot }}

            </div>
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
