<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Brand Header --}}
    <div style="text-align:center; margin-bottom:2rem;">
        {{-- Inline SVG logo icon --}}
        <div style="display:inline-block; margin-bottom:1rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="52" height="52" style="display:block;">
                <rect x="10" y="10" width="180" height="180" rx="36" fill="#0f172a"/>
                <rect x="62" y="34" width="60" height="98" rx="10" fill="none" stroke="#60a5fa" stroke-width="4"/>
                <rect x="69" y="46" width="46" height="66" rx="5" fill="#1e3a5f"/>
                <path d="M78 60 Q92 52 106 60" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round"/>
                <path d="M83 71 Q92 64 101 71" fill="none" stroke="#818cf8" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="92" cy="83" r="3" fill="#6366f1"/>
                <rect x="83" y="122" width="18" height="5" rx="2.5" fill="#60a5fa" opacity="0.6"/>
                <rect x="79" y="40" width="26" height="4" rx="2" fill="#60a5fa" opacity="0.3"/>
                <circle cx="137" cy="145" r="26" fill="#1e3a5f"/>
                <circle cx="137" cy="145" r="26" fill="none" stroke="#6366f1" stroke-width="2.5"/>
                <rect x="134.5" y="116" width="5" height="9" rx="2" fill="#818cf8"/>
                <rect x="134.5" y="166" width="5" height="9" rx="2" fill="#818cf8"/>
                <rect x="108" y="142.5" width="9" height="5" rx="2" fill="#818cf8"/>
                <rect x="158" y="142.5" width="9" height="5" rx="2" fill="#818cf8"/>
                <rect x="117" y="119" width="5" height="9" rx="2" fill="#818cf8" transform="rotate(45 119.5 123.5)"/>
                <rect x="152" y="119" width="5" height="9" rx="2" fill="#818cf8" transform="rotate(-45 154.5 123.5)"/>
                <rect x="117" y="159" width="5" height="9" rx="2" fill="#818cf8" transform="rotate(-45 119.5 163.5)"/>
                <rect x="152" y="159" width="5" height="9" rx="2" fill="#818cf8" transform="rotate(45 154.5 163.5)"/>
                <circle cx="137" cy="145" r="12" fill="#0f172a"/>
                <circle cx="137" cy="145" r="6" fill="#6366f1"/>
                <circle cx="137" cy="145" r="3" fill="#818cf8"/>
            </svg>
        </div>
        <h1 style="font-size:1.2rem; font-weight:700; color:#f1f5f9; margin:0 0 4px;
                   letter-spacing:0.04em;">
            <span style="color:#f1f5f9;">PHONE</span>
            <span style="color:#6366f1;"> REPAIR</span>
        </h1>
        <p style="font-size:0.75rem; color:#64748b; margin:0; letter-spacing:0.08em; text-transform:uppercase;">
            Portal Akses Staf
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf

        {{-- Email --}}
        <div style="margin-bottom:1.1rem;">
            <label for="email"
                   style="display:block; font-size:0.78rem; font-weight:600;
                          color:#94a3b8; margin-bottom:6px; letter-spacing:0.05em; text-transform:uppercase;">
                Email
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="nama@servis.com"
                required
                autofocus
                autocomplete="username"
                style="width:100%; box-sizing:border-box; padding:10px 14px;
                       background:#0f172a; border:1.5px solid #1e293b;
                       border-radius:8px; font-size:0.875rem;
                       color:#f1f5f9; outline:none;
                       transition: border-color 0.2s, box-shadow 0.2s;"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div style="margin-bottom:1.1rem;">
            <label for="password"
                   style="display:block; font-size:0.78rem; font-weight:600;
                          color:#94a3b8; margin-bottom:6px; letter-spacing:0.05em; text-transform:uppercase;">
                Password
            </label>
            <div style="position:relative;">
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                    style="width:100%; box-sizing:border-box; padding:10px 44px 10px 14px;
                           background:#0f172a; border:1.5px solid #1e293b;
                           border-radius:8px; font-size:0.875rem;
                           color:#f1f5f9; outline:none;
                           transition: border-color 0.2s, box-shadow 0.2s;"
                />
                <button
                    type="button"
                    data-aksi="toggle-password"
                    tabindex="-1"
                    style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                           background:none; border:none; cursor:pointer; padding:0;
                           color:#475569; display:flex; align-items:center;
                           transition: color 0.15s;"
                    aria-label="Tampilkan password"
                >
                    <svg id="eye-show" width="17" height="17" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg id="eye-hide" width="17" height="17" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:1.5rem;">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                style="width:15px; height:15px; accent-color:#6366f1; cursor:pointer;"
            />
            <label for="remember_me"
                   style="font-size:0.82rem; color:#64748b; cursor:pointer; user-select:none;">
                Ingat saya
            </label>
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            style="width:100%; padding:11px; background:#6366f1; color:#ffffff;
                   border:none; border-radius:8px; font-size:0.875rem; font-weight:600;
                   cursor:pointer; letter-spacing:0.06em; text-transform:uppercase;
                   transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
                   box-shadow: 0 0 0 0 rgba(99,102,241,0);"
            
        >
            Masuk
        </button>

        {{-- Forgot Password --}}
        @if (Route::has('password.request'))
            <div style="text-align:center; margin-top:1rem;">
                <a href="{{ route('password.request') }}"
                   style="font-size:0.78rem; color:#6366f1;
                          text-decoration:none; opacity:0.8;
                          transition: opacity 0.15s;"
                   >
                    Lupa password?
                </a>
            </div>
        @endif
    </form>

    {{-- Role Badge --}}
    <div style="display:flex; justify-content:center; gap:8px;
                margin-top:1.5rem; padding-top:1.25rem;
                border-top:1px solid #1e293b;">
        <span style="font-size:0.7rem; font-weight:600; padding:4px 16px;
                     border-radius:20px; background:#0c2340; color:#60a5fa;
                     letter-spacing:0.06em; text-transform:uppercase;">
            Owner
        </span>
        <span style="font-size:0.7rem; font-weight:600; padding:4px 16px;
                     border-radius:20px; background:#1e1b4b; color:#818cf8;
                     letter-spacing:0.06em; text-transform:uppercase;">
            Admin
        </span>
    </div>

    <style>
        /* Efek fokus dan sorot dulu ditulis sebagai atribut onfocus, onblur,
           onmouseover, dan onmouseout yang menimpa style elemen lewat
           JavaScript. Semuanya kini menjadi aturan CSS biasa. */
        .login-form input[type="email"]:focus,
        .login-form input[type="password"]:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
        }

        .login-form button[data-aksi="toggle-password"]:hover { color: #818cf8; }

        .login-form button[type="submit"]:hover {
            background: #4f46e5 !important;
            box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
        }

        .login-form a:hover { opacity: 1 !important; }

        .login-form button[type="submit"]:active { transform: scale(.98); }
    </style>

    <script>
        (function () {
            const tombol = document.querySelector('[data-aksi="toggle-password"]');
            const input  = document.getElementById('password');

            if (!tombol || !input) {
                return;
            }

            tombol.addEventListener('click', function () {
                const tersembunyi = input.type === 'password';
                const show = document.getElementById('eye-show');
                const hide = document.getElementById('eye-hide');

                input.type = tersembunyi ? 'text' : 'password';

                if (show) { show.style.display = tersembunyi ? 'none' : 'block'; }
                if (hide) { hide.style.display = tersembunyi ? 'block' : 'none'; }

                tombol.setAttribute(
                    'aria-label',
                    tersembunyi ? 'Sembunyikan password' : 'Tampilkan password',
                );
            });
        })();
    </script>
</x-guest-layout>