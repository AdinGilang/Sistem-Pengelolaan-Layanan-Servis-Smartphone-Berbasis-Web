<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Phone Repair') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                --navy:   #1a2035;
                --navy-light: #243049;
                --blue:   #4361ee;
                --blue-light: #647cf5;
                --orange: #f7a41d;
                --purple: #9c4fe3;
                --green:  #2ed8a3;
                --bg:     #f0f2f8;
                --white:  #ffffff;
                --text:   #1a2035;
                --muted:  #7a8099;
            }

            * { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background: var(--bg);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                overflow-x: hidden;
            }

            /* ── Animated background blobs ── */
            .bg-blobs {
                position: fixed;
                inset: 0;
                pointer-events: none;
                z-index: 0;
                overflow: hidden;
            }
            .blob {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                opacity: .18;
                animation: drift 14s ease-in-out infinite alternate;
            }
            .blob-1 { width: 600px; height: 600px; background: var(--blue);   top: -200px; left: -150px; animation-delay: 0s; }
            .blob-2 { width: 500px; height: 500px; background: var(--purple); bottom: -150px; right: -100px; animation-delay: -5s; }
            .blob-3 { width: 350px; height: 350px; background: var(--green);  top: 40%; left: 40%; animation-delay: -9s; }
            @keyframes drift {
                from { transform: translate(0,0) scale(1); }
                to   { transform: translate(40px, 30px) scale(1.08); }
            }

            /* ── Nav ── */
            .topbar {
                position: relative; z-index: 10;
                display: flex; align-items: center; justify-content: space-between;
                padding: 20px 40px;
            }
            .logo {
                display: flex; align-items: center; gap: 10px;
                text-decoration: none;
            }
            .logo-icon {
                width: 40px; height: 40px; border-radius: 10px;
                background: var(--navy);
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 4px 14px rgba(67,97,238,.4);
            }
            .logo-icon svg { width: 20px; height: 20px; fill: white; }
            .logo-text {
                font-family: 'Space Grotesk', sans-serif;
                font-weight: 700; font-size: 17px; letter-spacing: .5px;
                color: var(--navy);
            }
            .nav-actions { display: flex; align-items: center; gap: 10px; }
            .btn-ghost {
                padding: 8px 20px; border-radius: 8px; border: 1.5px solid rgba(26,32,53,.15);
                background: transparent; color: var(--navy);
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 14px; font-weight: 500; cursor: pointer;
                text-decoration: none; transition: all .2s;
            }
            .btn-ghost:hover { border-color: var(--blue); color: var(--blue); background: rgba(67,97,238,.06); }
            .btn-primary {
                padding: 8px 22px; border-radius: 8px; border: none;
                background: var(--navy); color: white;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 14px; font-weight: 600; cursor: pointer;
                text-decoration: none; transition: all .2s;
                box-shadow: 0 4px 14px rgba(26,32,53,.25);
            }
            .btn-primary:hover { background: var(--blue); box-shadow: 0 6px 18px rgba(67,97,238,.35); transform: translateY(-1px); }

            /* ── Hero ── */
            .hero {
                position: relative; z-index: 5;
                flex: 1;
                display: flex; align-items: center; justify-content: center;
                padding: 40px 40px 60px;
                gap: 60px;
                flex-wrap: wrap;
            }

            .hero-left { max-width: 540px; flex: 1 1 320px; }

            .badge {
                display: inline-flex; align-items: center; gap: 6px;
                background: rgba(26,32,53,.07); border: 1px solid rgba(26,32,53,.12);
                padding: 5px 14px; border-radius: 100px;
                font-size: 12px; font-weight: 600; color: var(--navy); letter-spacing: .4px;
                margin-bottom: 24px;
                animation: fadeUp .6s ease both;
            }
            .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); box-shadow: 0 0 6px var(--green); }

            .hero-title {
                font-family: 'Space Grotesk', sans-serif;
                font-size: clamp(36px, 5vw, 58px);
                font-weight: 700; line-height: 1.1;
                color: var(--navy);
                margin-bottom: 20px;
                animation: fadeUp .6s .1s ease both;
            }
            .hero-title span {
                background: linear-gradient(135deg, var(--blue), var(--purple));
                -webkit-background-clip: text; -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero-desc {
                font-size: 16px; line-height: 1.7; color: var(--muted);
                margin-bottom: 36px;
                animation: fadeUp .6s .2s ease both;
            }

            .hero-cta {
                display: flex; flex-wrap: wrap; gap: 12px;
                animation: fadeUp .6s .3s ease both;
            }
            .cta-main {
                display: inline-flex; align-items: center; gap: 8px;
                padding: 14px 30px; border-radius: 12px; border: none;
                background: var(--navy); color: white;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 15px; font-weight: 600; cursor: pointer;
                text-decoration: none; transition: all .25s;
                box-shadow: 0 6px 20px rgba(26,32,53,.3);
            }
            .cta-main:hover { background: var(--blue); transform: translateY(-2px); box-shadow: 0 10px 28px rgba(67,97,238,.4); }
            .cta-main svg { width: 18px; height: 18px; }
            .cta-secondary {
                display: inline-flex; align-items: center; gap: 8px;
                padding: 14px 28px; border-radius: 12px;
                border: 1.5px solid rgba(26,32,53,.18); background: white;
                color: var(--navy); font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 15px; font-weight: 600; cursor: pointer;
                text-decoration: none; transition: all .25s;
                box-shadow: 0 2px 8px rgba(0,0,0,.06);
            }
            .cta-secondary:hover { border-color: var(--blue); color: var(--blue); transform: translateY(-2px); }

            /* ── Stats strip ── */
            .stats-strip {
                display: flex; gap: 32px; margin-top: 48px;
                animation: fadeUp .6s .4s ease both;
                flex-wrap: wrap;
            }
            .stat { }
            .stat-num {
                font-family: 'Space Grotesk', sans-serif;
                font-size: 28px; font-weight: 700; color: var(--navy); line-height: 1;
            }
            .stat-num span { color: var(--blue); }
            .stat-label { font-size: 12px; color: var(--muted); margin-top: 3px; font-weight: 500; }
            .stat-divider { width: 1px; background: rgba(26,32,53,.12); align-self: stretch; }

            /* ── Dashboard card mockup ── */
            .hero-right {
                flex: 1 1 380px; max-width: 480px;
                animation: floatIn .8s .2s ease both;
            }
            .dashboard-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 24px 60px rgba(26,32,53,.18), 0 4px 16px rgba(0,0,0,.06);
                overflow: hidden;
                border: 1px solid rgba(255,255,255,.8);
            }
            .card-topbar {
                background: var(--navy);
                padding: 16px 20px;
                display: flex; align-items: center; justify-content: space-between;
            }
            .card-topbar-title {
                font-family: 'Space Grotesk', sans-serif;
                font-size: 13px; font-weight: 600; color: rgba(255,255,255,.9);
                letter-spacing: .3px;
            }
            .card-dots { display: flex; gap: 6px; }
            .card-dots span { width: 9px; height: 9px; border-radius: 50%; }
            .dot-r { background: #ff5f57; }
            .dot-y { background: #febc2e; }
            .dot-g { background: #28c840; }

            .card-body { padding: 20px; }
            .mini-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 18px; }
            .mini-stat {
                background: var(--bg);
                border-radius: 12px;
                padding: 12px 10px;
                text-align: center;
                border-top: 3px solid transparent;
                transition: transform .2s;
            }
            .mini-stat:hover { transform: translateY(-2px); }
            .mini-stat.blue  { border-color: var(--blue); }
            .mini-stat.orange{ border-color: var(--orange); }
            .mini-stat.purple{ border-color: var(--purple); }
            .mini-stat.green { border-color: var(--green); }
            .mini-stat-val {
                font-family: 'Space Grotesk', sans-serif;
                font-size: 22px; font-weight: 700; color: var(--navy);
            }
            .mini-stat-lbl { font-size: 9px; color: var(--muted); font-weight: 600; letter-spacing: .4px; text-transform: uppercase; margin-top: 2px; }

            .card-section-title {
                font-size: 11px; font-weight: 700; color: var(--muted);
                text-transform: uppercase; letter-spacing: .5px;
                margin-bottom: 10px;
            }
            .repair-list { display: flex; flex-direction: column; gap: 8px; }
            .repair-row {
                display: flex; align-items: center; gap: 10px;
                padding: 9px 12px; border-radius: 10px; background: var(--bg);
            }
            .repair-icon {
                width: 30px; height: 30px; border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                flex-shrink: 0;
            }
            .repair-icon svg { width: 15px; height: 15px; }
            .repair-icon.blue   { background: rgba(67,97,238,.12);  }
            .repair-icon.orange { background: rgba(247,164,29,.12); }
            .repair-icon.purple { background: rgba(156,79,227,.12); }
            .repair-info { flex: 1; }
            .repair-name { font-size: 12px; font-weight: 600; color: var(--navy); }
            .repair-type { font-size: 10px; color: var(--muted); }
            .repair-badge {
                font-size: 10px; font-weight: 600; padding: 3px 9px; border-radius: 100px;
            }
            .badge-proses  { background: rgba(67,97,238,.1);  color: var(--blue); }
            .badge-tunggu  { background: rgba(247,164,29,.12); color: #c48100; }
            .badge-selesai { background: rgba(46,216,163,.12); color: #18a07a; }

            .progress-section { margin-top: 16px; }
            .progress-row { margin-bottom: 10px; }
            .progress-header { display: flex; justify-content: space-between; font-size: 11px; color: var(--muted); margin-bottom: 5px; font-weight: 500; }
            .progress-track { background: var(--bg); border-radius: 100px; height: 6px; overflow: hidden; }
            .progress-fill { height: 100%; border-radius: 100px; transition: width .8s cubic-bezier(.4,0,.2,1); }
            .fill-blue   { background: linear-gradient(90deg, var(--blue), var(--blue-light)); }
            .fill-orange { background: linear-gradient(90deg, #e69000, var(--orange)); }
            .fill-purple { background: linear-gradient(90deg, #7c35c5, var(--purple)); }
            .fill-green  { background: linear-gradient(90deg, #1ab88a, var(--green)); }

            /* ── Features ── */
            .features {
                position: relative; z-index: 5;
                padding: 0 40px 80px;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
                max-width: 1100px; margin: 0 auto; width: 100%;
            }
            .feature-card {
                background: white; border-radius: 16px;
                padding: 24px; border: 1px solid rgba(26,32,53,.07);
                box-shadow: 0 2px 12px rgba(0,0,0,.05);
                transition: all .25s;
                animation: fadeUp .6s ease both;
            }
            .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,.1); }
            .feature-icon {
                width: 44px; height: 44px; border-radius: 12px;
                display: flex; align-items: center; justify-content: center;
                margin-bottom: 14px;
            }
            .feature-icon svg { width: 22px; height: 22px; }
            .fi-blue   { background: rgba(67,97,238,.1);  }
            .fi-orange { background: rgba(247,164,29,.12); }
            .fi-purple { background: rgba(156,79,227,.1);  }
            .fi-green  { background: rgba(46,216,163,.12); }
            .feature-title { font-size: 15px; font-weight: 700; color: var(--navy); margin-bottom: 6px; }
            .feature-desc  { font-size: 13px; color: var(--muted); line-height: 1.6; }

            /* ── Footer ── */
            .footer {
                position: relative; z-index: 5;
                padding: 20px 40px;
                display: flex; align-items: center; justify-content: center;
                border-top: 1px solid rgba(26,32,53,.08);
                font-size: 12px; color: var(--muted);
            }

            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(20px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            @keyframes floatIn {
                from { opacity: 0; transform: translateX(30px); }
                to   { opacity: 1; transform: translateX(0); }
            }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50%       { transform: translateY(-8px); }
            }
            .hero-right { animation: floatIn .8s .2s ease both; }
            .dashboard-card { animation: float 6s ease-in-out infinite; animation-delay: 1s; }

            @media (max-width: 768px) {
                .topbar { padding: 16px 20px; }
                .hero { padding: 30px 20px 40px; flex-direction: column; gap: 40px; }
                .hero-left { max-width: 100%; }
                .features { padding: 0 20px 60px; }
                .mini-stats { grid-template-columns: repeat(2, 1fr); }
            }
        </style>
    </head>
    <body>
        <!-- Background blobs -->
        <div class="bg-blobs">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>
        </div>

        <!-- Topbar -->
        <header class="topbar">
            <a href="/" class="logo">
                            <img src="{{ asset('images/logo-phone-repair.png') }}"
                    alt="Phone Repair"
                    class="w-10 h-10 object-contain"
                >
                <span class="font-bold text-lg tracking-wide text-slate-900">
                    PHONE REPAIR
                </span>
            </a>

            @if (Route::has('login'))
                <nav class="nav-actions">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Daftar Sekarang</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Hero -->
        <section class="hero">
            <div class="hero-left">
                <div class="badge">
                    <span class="badge-dot"></span>
                    Sistem Manajemen Servis Aktif
                </div>

                <h1 class="hero-title">
                    Kelola Servis HP<br>
                    Lebih <span>Efisien &amp; Terorganisir</span>
                </h1>

                <p class="hero-desc">
                    Platform manajemen servis handphone yang lengkap. Pantau status perbaikan,
                    kelola data pelanggan, dan tingkatkan produktivitas servis Anda — semua dalam satu dasbor.
                </p>

                <div class="hero-cta">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="cta-main">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="3" width="7" height="7" rx="1"/>
                                <rect x="3" y="14" width="7" height="7" rx="1"/>
                                <rect x="14" y="14" width="7" height="7" rx="1"/>
                            </svg>
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="cta-main">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                            </svg>
                            Masuk ke Sistem
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="cta-secondary">
                                Daftar Akun 
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    @endauth
                </div>

                <div class="stats-strip">
                    <div class="stat">
                        <div class="stat-num">16<span>+</span></div>
                        <div class="stat-label">Total Servis</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <div class="stat-num">7</div>
                        <div class="stat-label">Sedang Diproses</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <div class="stat-num">5</div>
                        <div class="stat-label">Selesai Hari Ini</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <div class="stat-num">4</div>
                        <div class="stat-label">Menunggu</div>
                    </div>
                </div>
            </div>

            <!-- Dashboard mockup -->
            <div class="hero-right">
                <div class="dashboard-card">
                    <div class="card-topbar">
                        <div class="card-dots">
                            <span class="dot-r"></span>
                            <span class="dot-y"></span>
                            <span class="dot-g"></span>
                        </div>
                        <span class="card-topbar-title">Dashboard — Phone Repair</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 1 0 4.93 19.07"/>
                        </svg>
                    </div>

                    <div class="card-body">
                        <!-- Mini stat cards -->
                        <div class="mini-stats">
                            <div class="mini-stat blue">
                                <div class="mini-stat-val">16</div>
                                <div class="mini-stat-lbl">Total</div>
                            </div>
                            <div class="mini-stat orange">
                                <div class="mini-stat-val">4</div>
                                <div class="mini-stat-lbl">Menunggu</div>
                            </div>
                            <div class="mini-stat purple">
                                <div class="mini-stat-val">7</div>
                                <div class="mini-stat-lbl">Proses</div>
                            </div>
                            <div class="mini-stat green">
                                <div class="mini-stat-val">5</div>
                                <div class="mini-stat-lbl">Selesai</div>
                            </div>
                        </div>

                        <!-- Repair list -->
                        <div class="card-section-title">Data Servis Terbaru</div>
                        <div class="repair-list">
                            <div class="repair-row">
                                <div class="repair-icon blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#4361ee" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>
                                </div>
                                <div class="repair-info">
                                    <div class="repair-name">Prabowo Sayang Buna Teddy</div>
                                    <div class="repair-type">LCD + backdoor</div>
                                </div>
                                <span class="repair-badge badge-proses">Proses</span>
                            </div>
                            <div class="repair-row">
                                <div class="repair-icon orange">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#f7a41d" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>
                                </div>
                                <div class="repair-info">
                                    <div class="repair-name">bahlil</div>
                                    <div class="repair-type">fingerprint</div>
                                </div>
                                <span class="repair-badge badge-tunggu">Menunggu</span>
                            </div>
                            <div class="repair-row">
                                <div class="repair-icon purple">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#9c4fe3" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>
                                </div>
                                <div class="repair-info">
                                    <div class="repair-name">Jokowi</div>
                                    <div class="repair-type">LCD</div>
                                </div>
                                <span class="repair-badge badge-proses">Proses</span>
                            </div>
                        </div>

                        <!-- Progress bars -->
                        <div class="progress-section">
                            <div class="progress-row">
                                <div class="progress-header"><span>Menunggu</span><span style="color:var(--orange);font-weight:600">25%</span></div>
                                <div class="progress-track"><div class="progress-fill fill-orange" style="width:25%"></div></div>
                            </div>
                            <div class="progress-row">
                                <div class="progress-header"><span>Sedang Diproses</span><span style="color:var(--purple);font-weight:600">44%</span></div>
                                <div class="progress-track"><div class="progress-fill fill-purple" style="width:44%"></div></div>
                            </div>
                            <div class="progress-row">
                                <div class="progress-header"><span>Selesai</span><span style="color:var(--green);font-weight:600">31%</span></div>
                                <div class="progress-track"><div class="progress-fill fill-green" style="width:31%"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature cards -->
        <div class="features" style="max-width:1100px;margin:0 auto;width:100%;">
            <div class="feature-card" style="animation-delay:.1s">
                <div class="feature-icon fi-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#4361ee" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <div class="feature-title">Tracking Real-time</div>
                <div class="feature-desc">Pantau status setiap perangkat dari masuk hingga selesai diperbaiki secara langsung.</div>
            </div>
            <div class="feature-card" style="animation-delay:.2s">
                <div class="feature-icon fi-orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#f7a41d" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="feature-title">Manajemen Pelanggan</div>
                <div class="feature-desc">Simpan data pelanggan dan riwayat servis dengan mudah dan terstruktur.</div>
            </div>
            <div class="feature-card" style="animation-delay:.3s">
                <div class="feature-icon fi-purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#9c4fe3" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                </div>
                <div class="feature-title">Laporan Statistik</div>
                <div class="feature-desc">Visualisasi data servis harian, mingguan, dan bulanan dalam grafik yang mudah dibaca.</div>
            </div>
            <div class="feature-card" style="animation-delay:.4s">
                <div class="feature-icon fi-green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2ed8a3" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="feature-title">Data Aman & Terjaga</div>
                <div class="feature-desc">Semua data servis tersimpan dengan aman dan hanya dapat diakses oleh staf berwenang.</div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Phone Repair') }} — Sistem Manajemen Servis Handphone
        </footer>
    </body>
</html>