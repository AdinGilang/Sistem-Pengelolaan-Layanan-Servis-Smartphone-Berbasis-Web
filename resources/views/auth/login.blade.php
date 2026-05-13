<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Brand Header --}}
    <div style="text-align:center; margin-bottom:2rem;">
        <div style="display:inline-flex; align-items:center; justify-content:center;
                    width:52px; height:52px; border-radius:14px; margin-bottom:1rem;
                    background: linear-gradient(135deg, #6366f1, #8b5cf6);">
            <svg width="26" height="26" fill="none" stroke="#ffffff" stroke-width="2"
                 viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="2" width="14" height="20" rx="2"/>
                <line x1="12" y1="18" x2="12" y2="18.01"/>
            </svg>
        </div>
        <h1 style="font-size:1.2rem; font-weight:600; color:#f1f5f9; margin:0 0 4px;">
            Servis Smartphone Phone Repair
        </h1>
        <p style="font-size:0.8rem; color:#94a3b8; margin:0;">
            Portal akses khusus staf Phone Repair
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div style="margin-bottom:1.1rem;">
            <label for="email"
                   style="display:block; font-size:0.82rem; font-weight:600;
                          color:#cbd5e1; margin-bottom:6px;">
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
                       background:#0f172a; border:1.5px solid #334155;
                       border-radius:8px; font-size:0.9rem;
                       color:#f1f5f9; outline:none;
                       transition: border-color 0.15s;"
                onfocus="this.style.borderColor='#6366f1'"
                onblur="this.style.borderColor='#334155'"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div style="margin-bottom:1.1rem;">
            <label for="password"
                   style="display:block; font-size:0.82rem; font-weight:600;
                          color:#cbd5e1; margin-bottom:6px;">
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
                    style="width:100%; box-sizing:border-box; padding:10px 42px 10px 14px;
                           background:#0f172a; border:1.5px solid #334155;
                           border-radius:8px; font-size:0.9rem;
                           color:#f1f5f9; outline:none;
                           transition: border-color 0.15s;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='#334155'"
                />
                <button
                    type="button"
                    onclick="togglePassword()"
                    tabindex="-1"
                    style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                           background:none; border:none; cursor:pointer; padding:0;
                           color:#64748b; display:flex; align-items:center;"
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
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:1.4rem;">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                style="width:15px; height:15px; accent-color:#6366f1; cursor:pointer;"
            />
            <label for="remember_me"
                   style="font-size:0.82rem; color:#94a3b8; cursor:pointer; user-select:none;">
                Ingat saya
            </label>
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            style="width:100%; padding:11px; background:#6366f1; color:#ffffff;
                   border:none; border-radius:8px; font-size:0.9rem; font-weight:600;
                   cursor:pointer; letter-spacing:0.03em;
                   transition: background 0.15s, transform 0.1s;"
            onmouseover="this.style.background='#4f46e5'"
            onmouseout="this.style.background='#6366f1'"
            onmousedown="this.style.transform='scale(0.98)'"
            onmouseup="this.style.transform='scale(1)'"
        >
            MASUK
        </button>

        {{-- Forgot Password --}}
        @if (Route::has('password.request'))
            <div style="text-align:center; margin-top:1rem;">
                <a href="{{ route('password.request') }}"
                   style="font-size:0.78rem; color:#818cf8;
                          text-decoration:underline; text-underline-offset:2px;">
                    Lupa password?
                </a>
            </div>
        @endif
    </form>

    {{-- Role Badge --}}
    <div style="display:flex; justify-content:center; gap:8px;
                margin-top:1.5rem; padding-top:1.25rem;
                border-top:1px solid #1e293b;">
        <span style="font-size:0.72rem; font-weight:600; padding:4px 14px;
                     border-radius:20px; background:#1e3a5f; color:#93c5fd;">
            Owner
        </span>
        <span style="font-size:0.72rem; font-weight:600; padding:4px 14px;
                     border-radius:20px; background:#2e1b6e; color:#c4b5fd;">
            Admin
        </span>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const show  = document.getElementById('eye-show');
            const hide  = document.getElementById('eye-hide');
            if (input.type === 'password') {
                input.type = 'text';
                show.style.display = 'none';
                hide.style.display = 'block';
            } else {
                input.type = 'password';
                show.style.display = 'block';
                hide.style.display = 'none';
            }
        }
    </script>
</x-guest-layout>

