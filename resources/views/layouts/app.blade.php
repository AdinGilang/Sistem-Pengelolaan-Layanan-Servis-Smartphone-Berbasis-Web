<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ServisApp') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: rgba(255,255,255,0.55);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.18s ease;
            margin-bottom: 2px;
        }
        .sidebar-nav-item:hover { background: #252c4a; color: #fff; }
        .sidebar-nav-item.active {
            background: #3b5bdb;
            color: #fff;
            box-shadow: 0 4px 12px rgba(59,91,219,0.4);
        }
        .sidebar-nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }
        .sidebar-nav-item .nav-badge {
            margin-left: auto;
            background: rgba(255,255,255,0.15);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 20px;
        }
        .sidebar-nav-item.active .nav-badge { background: rgba(255,255,255,0.25); }

        .role-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .role-badge.admin { background: rgba(59,91,219,0.25); color: #7c9dff; }
        .role-badge.owner { background: rgba(245,159,0,0.25);  color: #ffd43b; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            color: #8a93b2;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 8px;
            margin: 16px 0 6px;
            display: block;
        }
    </style>
</head>

<body class="antialiased" style="background:#f0f2f7; color:#1a1f36;">

@php $userRole = auth()->user()->role ?? 'admin'; @endphp

<div x-data="{ open: false }" class="min-h-screen flex">

    <!-- ═══════════════════════ SIDEBAR ═══════════════════════ -->
    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 w-60 flex flex-col transform transition-transform duration-300 z-40
               md:relative md:translate-x-0"
        style="background:#1a1f36; min-height:100vh;">

        <!-- Brand -->
        <div class="flex items-center gap-3 px-5 py-6" style="border-bottom:1px solid rgba(255,255,255,0.06);">
            <div class="flex items-center justify-center rounded-xl flex-shrink-0"
                 style="width:36px;height:36px;background:#3b5bdb;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <span class="text-white font-bold text-base tracking-tight">PHONE REPAIR</span>
            <button @click="open = false" class="md:hidden ml-auto text-white opacity-60 hover:opacity-100 text-lg leading-none">✕</button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 pt-5 overflow-y-auto">

            <span class="nav-section-label">Menu</span>

            {{-- Dashboard — semua role --}}
            <a href="{{ route('dashboard') }}"
               class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                Dashboard
            </a>

            {{-- Data Servis — semua role (owner read-only) --}}
            <a href="{{ route('servis.index') }}"
               class="sidebar-nav-item {{ request()->routeIs('servis.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Data Servis
                <span class="nav-badge">{{ \App\Models\Servis::count() }}</span>
            </a>

            {{-- Laporan & Statistik — Owner only --}}
            @if($userRole === 'owner')
                <span class="nav-section-label">Manajemen</span>

                <a href="{{ route('laporan.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    Laporan
                </a>

                <a href="{{ route('statistik.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('statistik.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                    Statistik
                </a>
            @endif

        </nav>

        <!-- Footer: User + Logout -->
        <div class="px-3 pb-6 pt-4" style="border-top:1px solid rgba(255,255,255,0.06);">
            <div class="flex items-center gap-3 px-3 py-2 rounded-xl mb-1">
                <div class="flex items-center justify-center rounded-full flex-shrink-0 text-white font-bold"
                     style="width:34px;height:34px;background:linear-gradient(135deg,#4c6ef5,#9c36b5);font-size:12px;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-white font-semibold truncate" style="font-size:13px;">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div style="margin-top:2px;">
                        <span class="role-badge {{ $userRole }}">{{ ucfirst($userRole) }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all duration-200"
                        style="font-size:14px;color:#fa5252;background:transparent;border:none;cursor:pointer;"
                        onmouseover="this.style.background='rgba(250,82,82,0.12)'"
                        onmouseout="this.style.background='transparent'">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div x-show="open" @click="open = false" x-cloak
         class="fixed inset-0 z-30 md:hidden"
         style="background:rgba(0,0,0,0.4);"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- ═══════════════════════ MAIN AREA ═══════════════════════ -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- TOPBAR -->
        <header class="sticky top-0 z-20 flex items-center justify-between px-8 py-4"
                style="background:#fff; border-bottom:1px solid #e8eaf0;">

            <div class="flex items-center gap-4">
                <button @click="open = true"
                        class="md:hidden flex items-center justify-center rounded-lg transition"
                        style="width:36px;height:36px;color:#8a93b2;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>

                @isset($header)
                    <div style="font-size:18px;font-weight:700;color:#1a1f36;line-height:1.2;">
                        {{ $header }}
                    </div>
                @endisset
            </div>

            <div class="flex items-center gap-3">
                {{-- Role badge di topbar --}}
                <span class="role-badge {{ $userRole }} hidden sm:inline-block">{{ ucfirst($userRole) }}</span>
                <div class="flex items-center justify-center rounded-full text-white font-bold flex-shrink-0"
                     style="width:32px;height:32px;background:linear-gradient(135deg,#4c6ef5,#9c36b5);font-size:12px;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <span class="hidden sm:block font-medium" style="font-size:14px;color:#1a1f36;">
                    {{ auth()->user()->name ?? '' }}
                </span>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 py-7 px-4 sm:px-8">
            <div class="max-w-7xl mx-auto">

                @if(session('success'))
                    <div
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 3000)"
                        x-show="show"
                        x-cloak
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="mb-6">
                        <div class="flex items-center justify-between px-4 py-3 rounded-xl shadow-sm"
                             style="background:#d3f9d8;border:1px solid #b2f2bb;color:#2f9e44;">
                            <div class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="font-medium" style="font-size:14px;">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="opacity-60 hover:opacity-100">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{ $slot }}

            </div>
        </main>

    </div>
</div>

</body>
</html>