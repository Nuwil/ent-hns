<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ENTer — ENT/HNS Clinic Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --navy:   #0a1628;
            --teal:   #0d9488;
            --teal2:  #14b8a6;
            --cream:  #f8f5f0;
            --gold:   #d4a853;
            --white:  #ffffff;
            --text:   #1e293b;
            --muted:  #64748b;
            --border: #e2e8f0;
            --error:  #dc2626;
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
        }

        /* ── Layout ──────────────────────────────────────── */
        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ── LEFT PANEL ──────────────────────────────────── */
        .left-panel {
            flex: 1;
            background: var(--navy);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }

        /* Subtle background texture */
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 80%, rgba(13,148,136,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 20%, rgba(212,168,83,0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Decorative grid lines */
        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .left-content {
            position: relative;
            z-index: 1;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }

        /* Logo mark */
        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }
        .brand-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, var(--teal), var(--teal2));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: white;
            box-shadow: 0 8px 24px rgba(13,148,136,0.4);
        }
        .brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 28px;
            color: white;
            letter-spacing: -0.5px;
        }
        .brand-name span { color: var(--teal2); }

        /* ENT Doctor SVG Illustration */
        .doctor-illustration {
            margin: 0 auto 36px;
            width: 280px;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-10px); }
        }

        .panel-heading {
            font-family: 'DM Serif Display', serif;
            font-size: 28px;
            color: white;
            line-height: 1.3;
            margin-bottom: 16px;
        }
        .panel-heading em {
            font-style: italic;
            color: var(--teal2);
        }

        .panel-desc {
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            margin-bottom: 36px;
        }

        /* Feature pills */
        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }
        .pill {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12px;
            color: rgba(255,255,255,0.75);
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .pill:hover {
            background: rgba(13,148,136,0.2);
            border-color: var(--teal);
            color: white;
        }
        .pill i { color: var(--teal2); font-size: 13px; }

        /* Bottom tag */
        .panel-footer-tag {
            position: absolute;
            bottom: 24px;
            left: 0; right: 0;
            text-align: center;
            font-size: 11px;
            color: rgba(255,255,255,0.25);
            letter-spacing: 0.5px;
            z-index: 1;
        }

        /* ── RIGHT PANEL ─────────────────────────────────── */
        .right-panel {
            width: 460px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 48px;
            background: var(--white);
            position: relative;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: linear-gradient(180deg, var(--teal) 0%, var(--teal2) 50%, var(--gold) 100%);
        }

        .login-box {
            width: 100%;
            max-width: 360px;
        }

        .login-greeting {
            margin-bottom: 32px;
        }
        .login-greeting h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 30px;
            color: var(--navy);
            margin-bottom: 6px;
        }
        .login-greeting p {
            font-size: 14px;
            color: var(--muted);
        }

        /* Form fields */
        .field-group {
            margin-bottom: 20px;
        }
        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            letter-spacing: 0.2px;
        }
        .field-input-wrap {
            position: relative;
        }
        .field-input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 15px;
            pointer-events: none;
        }
        .field-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            background: #fafbfc;
            transition: all 0.2s;
            outline: none;
        }
        .field-input:focus {
            border-color: var(--teal);
            background: white;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
        }
        .field-input.is-error {
            border-color: var(--error);
            background: #fff5f5;
        }

        /* Password toggle */
        .pw-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--muted);
            font-size: 15px;
            border: none;
            background: none;
            padding: 0;
        }
        .pw-toggle:hover { color: var(--teal); }

        /* Error message */
        .field-error {
            font-size: 12px;
            color: var(--error);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Remember me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }
        .remember-row input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--teal);
            cursor: pointer;
        }
        .remember-row label {
            font-size: 13px;
            color: var(--muted);
            cursor: pointer;
        }

        /* Submit button */
        .login-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--teal) 0%, var(--teal2) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(13,148,136,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.2px;
        }
        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(13,148,136,0.45);
        }
        .login-btn:active { transform: translateY(0); }
        .login-btn:disabled {
            opacity: 0.7; cursor: not-allowed; transform: none;
        }

        /* Divider */
        .login-divider {
            text-align: center;
            margin: 28px 0;
            position: relative;
        }
        .login-divider::before {
            content: '';
            position: absolute;
            top: 50%; left: 0; right: 0;
            height: 1px;
            background: var(--border);
        }
        .login-divider span {
            position: relative;
            background: white;
            padding: 0 12px;
            font-size: 12px;
            color: var(--muted);
        }

        /* System info card */
        .system-info {
            background: linear-gradient(135deg, #f0fdfa, #f8fafc);
            border: 1px solid #ccfbf1;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .system-info-icon {
            width: 36px; height: 36px;
            background: var(--teal);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 16px; flex-shrink: 0;
        }
        .system-info-text {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.6;
        }
        .system-info-text strong {
            display: block;
            color: var(--text);
            font-size: 13px;
            margin-bottom: 2px;
        }

        /* Right panel footer */
        .right-footer {
            position: absolute;
            bottom: 20px;
            font-size: 11px;
            color: var(--border);
            text-align: center;
        }

        /* ── Responsive ──────────────────────────────────── */
        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; border-left: none; }
            .right-panel::before { display: none; }
        }

        /* Loading spinner on submit */
        .spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="login-wrapper">

    {{-- ── LEFT PANEL ──────────────────────────────────── --}}
    <div class="left-panel">
        <div class="left-content">

            {{-- Brand --}}
            <div class="brand-logo">
                <div class="brand-icon">
                    <i class="bi bi-ear-fill"></i>
                </div>
                <span class="brand-name">ENT<span>er</span></span>
            </div>

            {{-- ENT Doctor SVG Illustration --}}
            <div class="doctor-illustration">
                <svg viewBox="0 0 280 300" xmlns="http://www.w3.org/2000/svg" fill="none">
                    <!-- Background circle glow -->
                    <ellipse cx="140" cy="260" rx="90" ry="18" fill="rgba(13,148,136,0.15)"/>

                    <!-- Doctor body - coat -->
                    <rect x="75" y="155" width="130" height="130" rx="20" fill="#ffffff" opacity="0.95"/>
                    <rect x="75" y="155" width="130" height="130" rx="20" fill="url(#coatGrad)"/>

                    <!-- Coat lapels -->
                    <path d="M140 160 L115 185 L105 155 Z" fill="#e2e8f0"/>
                    <path d="M140 160 L165 185 L175 155 Z" fill="#e2e8f0"/>

                    <!-- Stethoscope -->
                    <path d="M118 190 Q105 210 108 228 Q111 244 125 244 Q139 244 139 230 Q139 218 128 216" stroke="#0d9488" stroke-width="3" stroke-linecap="round" fill="none"/>
                    <circle cx="125" cy="244" r="7" fill="#0d9488" opacity="0.9"/>
                    <path d="M128 216 Q138 213 148 220" stroke="#0d9488" stroke-width="3" stroke-linecap="round" fill="none"/>
                    <circle cx="120" cy="188" r="4" fill="#14b8a6"/>
                    <circle cx="116" cy="194" r="3" fill="#14b8a6"/>

                    <!-- Doctor neck -->
                    <rect x="128" y="130" width="24" height="32" rx="8" fill="#fbbf8c"/>

                    <!-- Doctor head -->
                    <ellipse cx="140" cy="110" rx="42" ry="46" fill="#fbbf8c"/>

                    <!-- Hair -->
                    <path d="M98 100 Q100 70 140 65 Q180 60 182 100 Q175 75 140 72 Q105 72 98 100Z" fill="#1e293b"/>
                    <path d="M98 100 Q96 88 100 80" stroke="#1e293b" stroke-width="3" fill="none"/>

                    <!-- Face features -->
                    <!-- Eyes -->
                    <ellipse cx="124" cy="108" rx="6" ry="7" fill="white"/>
                    <ellipse cx="156" cy="108" rx="6" ry="7" fill="white"/>
                    <circle cx="125" cy="109" r="4" fill="#1e293b"/>
                    <circle cx="157" cy="109" r="4" fill="#1e293b"/>
                    <circle cx="126" cy="107" r="1.5" fill="white"/>
                    <circle cx="158" cy="107" r="1.5" fill="white"/>
                    <!-- Eyebrows -->
                    <path d="M117 100 Q124 96 131 100" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                    <path d="M149 100 Q156 96 163 100" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                    <!-- Nose -->
                    <path d="M138 112 Q136 120 139 123 Q141 124 143 123 Q146 120 142 112" fill="none" stroke="#e8956d" stroke-width="1.5" stroke-linecap="round"/>
                    <!-- Smile -->
                    <path d="M128 130 Q140 139 152 130" stroke="#c97a50" stroke-width="2" stroke-linecap="round" fill="none"/>

                    <!-- Ear pieces (otoscope) -->
                    <g transform="translate(172, 95)">
                        <!-- Otoscope device -->
                        <rect x="0" y="0" width="14" height="36" rx="4" fill="#64748b"/>
                        <rect x="2" y="2" width="10" height="14" rx="3" fill="#94a3b8"/>
                        <circle cx="7" cy="9" r="4" fill="#1e293b"/>
                        <circle cx="7" cy="9" r="2" fill="#fbbf24" opacity="0.8"/>
                        <!-- Light ray -->
                        <path d="M7 14 L3 28 L11 28 Z" fill="#0d9488" opacity="0.6"/>
                        <ellipse cx="7" cy="29" rx="4" ry="2" fill="#14b8a6" opacity="0.4"/>
                        <!-- Handle grip lines -->
                        <rect x="3" y="20" width="8" height="1.5" rx="1" fill="#475569"/>
                        <rect x="3" y="24" width="8" height="1.5" rx="1" fill="#475569"/>
                        <rect x="3" y="28" width="8" height="1.5" rx="1" fill="#475569"/>
                    </g>

                    <!-- Right arm holding otoscope -->
                    <path d="M205 120 Q195 130 186 140 Q180 148 175 108" stroke="#fbbf8c" stroke-width="18" stroke-linecap="round" fill="none"/>
                    <!-- Hand -->
                    <ellipse cx="175" cy="105" rx="10" ry="12" fill="#fbbf8c"/>

                    <!-- Left arm -->
                    <path d="M75 175 Q65 195 72 215" stroke="#ffffff" stroke-width="22" stroke-linecap="round" fill="none" opacity="0.9"/>
                    <!-- Clipboard in left hand -->
                    <rect x="48" y="210" width="38" height="50" rx="5" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="1.5"/>
                    <rect x="60" y="205" width="14" height="8" rx="3" fill="#94a3b8"/>
                    <line x1="54" y1="224" x2="80" y2="224" stroke="#94a3b8" stroke-width="1.5"/>
                    <line x1="54" y1="232" x2="80" y2="232" stroke="#94a3b8" stroke-width="1.5"/>
                    <line x1="54" y1="240" x2="72" y2="240" stroke="#94a3b8" stroke-width="1.5"/>
                    <line x1="54" y1="248" x2="76" y2="248" stroke="#94a3b8" stroke-width="1.5"/>

                    <!-- Name tag -->
                    <rect x="140" y="175" width="52" height="28" rx="5" fill="#0d9488" opacity="0.9"/>
                    <rect x="143" y="178" width="46" height="22" rx="3" fill="white" opacity="0.15"/>
                    <text x="166" y="190" text-anchor="middle" fill="white" font-size="7" font-family="DM Sans, sans-serif" font-weight="600">Dr. ENT</text>
                    <text x="166" y="199" text-anchor="middle" fill="rgba(255,255,255,0.8)" font-size="5.5" font-family="DM Sans, sans-serif">Specialist</text>

                    <!-- Floating medical icons -->
                    <g opacity="0.6">
                        <circle cx="42" cy="135" r="16" fill="rgba(13,148,136,0.15)" stroke="rgba(13,148,136,0.3)" stroke-width="1"/>
                        <text x="42" y="140" text-anchor="middle" font-size="14">👂</text>
                    </g>
                    <g opacity="0.5">
                        <circle cx="238" cy="180" r="14" fill="rgba(212,168,83,0.15)" stroke="rgba(212,168,83,0.3)" stroke-width="1"/>
                        <text x="238" y="185" text-anchor="middle" font-size="12">👃</text>
                    </g>
                    <g opacity="0.4">
                        <circle cx="55" cy="80" r="12" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
                        <text x="55" y="85" text-anchor="middle" font-size="10">🫁</text>
                    </g>

                    <defs>
                        <linearGradient id="coatGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#f8fafc"/>
                            <stop offset="100%" stop-color="#e2e8f0"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            {{-- Heading --}}
            <h2 class="panel-heading">
                Smarter Care for<br><em>ENT & HNS</em> Specialists
            </h2>
            <p class="panel-desc">
                A complete clinic management system built for Ear, Nose, Throat, Head & Neck Surgery practices — streamlining patient records, visits, prescriptions, and analytics in one secure platform.
            </p>

            {{-- Feature pills --}}
            <div class="feature-pills">
                <div class="pill"><i class="bi bi-people-fill"></i> Patient Management</div>
                <div class="pill"><i class="bi bi-calendar2-check"></i> Appointments</div>
                <div class="pill"><i class="bi bi-clipboard2-pulse"></i> SOAP Visit Records</div>
                <div class="pill"><i class="bi bi-capsule"></i> Prescriptions</div>
                <div class="pill"><i class="bi bi-graph-up-arrow"></i> Analytics & Forecast</div>
                <div class="pill"><i class="bi bi-printer"></i> Print Rx</div>
            </div>
        </div>

        <div class="panel-footer-tag">
            ENTer — ENT/HNS Clinic Management System · Baguio City
        </div>
    </div>

    {{-- ── RIGHT PANEL ─────────────────────────────────── --}}
    <div class="right-panel">
        <div class="login-box">

            <div class="login-greeting">
                <h1>Welcome back</h1>
                <p>Sign in to your clinic account to continue.</p>
            </div>

            {{-- Error alert --}}
            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#dc2626;display:flex;align-items:center;gap:8px">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('toast_success'))
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#16a34a;display:flex;align-items:center;gap:8px">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('toast_success') }}
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                @csrf

                {{-- Email --}}
                <div class="field-group">
                    <label class="field-label" for="email">Email Address</label>
                    <div class="field-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email"
                               class="field-input {{ $errors->has('email') ? 'is-error' : '' }}"
                               value="{{ old('email') }}"
                               placeholder="doctor@entclinic.com"
                               autocomplete="email"
                               required>
                    </div>
                    @error('email')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <label class="field-label" for="password">Password</label>
                    <div class="field-input-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password"
                               class="field-input {{ $errors->has('password') ? 'is-error' : '' }}"
                               placeholder="••••••••"
                               autocomplete="current-password"
                               required>
                        <button type="button" class="pw-toggle" onclick="togglePassword()" tabindex="-1">
                            <i class="bi bi-eye" id="pwEyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="remember-row">
                    <input type="checkbox" id="remember_me" name="remember_me"
                           {{ old('remember_me') ? 'checked' : '' }}>
                    <label for="remember_me">Keep me signed in</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="login-btn" id="loginBtn">
                    <span class="spinner" id="loginSpinner"></span>
                    <span id="loginBtnText"><i class="bi bi-box-arrow-in-right me-1"></i>Sign In</span>
                </button>
            </form>

            <div class="login-divider"><span>Clinic Access</span></div>

            {{-- System info --}}
            <div class="system-info">
                <div class="system-info-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="system-info-text">
                    <strong>Secure Role-Based Access</strong>
                    Access is granted based on your assigned role — Admin, Doctor, or Secretary. Contact your clinic administrator if you need an account.
                </div>
            </div>

        </div>

        <div class="right-footer">
            © {{ date('Y') }} ENTer · ENT/HNS Clinic Management System
        </div>
    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('pwEyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

document.getElementById('loginForm').addEventListener('submit', function() {
    const btn     = document.getElementById('loginBtn');
    const spinner = document.getElementById('loginSpinner');
    const text    = document.getElementById('loginBtnText');
    btn.disabled       = true;
    spinner.style.display = 'block';
    text.textContent   = 'Signing in...';
});
</script>

</body>
</html>