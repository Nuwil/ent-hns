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
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --page-bg: #edf2f7;
            --navy: #071627;
            --blue: #2563eb;
            --blue-dark: #0f172a;
            --white: #ffffff;
            --muted: #64748b;
            --border: #e2e8f0;
            --error: #dc2626;
        }
        html, body { min-height: 100%; font-family: 'DM Sans', sans-serif; background: var(--page-bg); color: var(--blue-dark); }
        body { display: grid; place-items: center; padding: 0; }
        .login-wrapper { width: 100%; display: grid; grid-template-columns: 1.5fr 1fr; min-height: 100vh; border-radius: 0; overflow: hidden; background: #fff; box-shadow: 0 35px 90px rgba(15,23,42,0.14); }
        .left-panel { position: relative; background: linear-gradient(180deg, #071627 0%, #0f3057 100%); padding: 60px 70px; display: flex; flex-direction: column; justify-content: center; color: var(--white); }
        .left-panel::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at top left, rgba(37,99,235,0.16), transparent 25%), radial-gradient(circle at bottom right, rgba(14,165,233,0.12), transparent 22%); pointer-events: none; }
        .brand-block { position: relative; z-index: 1; display: flex; align-items: center; gap: 18px; margin-bottom: 36px; }
        .brand-mark { width: 56px; height: 56px; border-radius: 18px; display: grid; place-items: center; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); box-shadow: 0 16px 40px rgba(15,23,42,0.24); }
        .brand-mark svg { width: 28px; height: 28px; color: #ffffff; }
        .brand-title { font-size: 34px; font-family: 'DM Serif Display', serif; letter-spacing: -0.04em; line-height: 1.05; }
        .brand-title span { color: #93c5fd; }
        .brand-subtitle { margin-top: 10px; font-size: 14px; color: rgba(255,255,255,0.72); line-height: 1.8; max-width: 480px; }
        .brand-description { margin-top: 40px; font-size: 15px; color: rgba(255,255,255,0.84); line-height: 1.9; max-width: 520px; }
        .right-panel { padding: 60px 50px; display: flex; align-items: center; justify-content: center; background: var(--white); }
        .login-card { width: 100%; max-width: 380px; }
        .login-header h1 { font-size: 34px; font-family: 'DM Serif Display', serif; margin-bottom: 10px; color: var(--blue-dark); }
        .login-header p { color: var(--muted); line-height: 1.8; margin-bottom: 28px; }
        .field-group { margin-bottom: 20px; }
        .field-label { display: block; margin-bottom: 10px; font-size: 13px; font-weight: 600; color: #334155; }
        .field-input-wrap { position: relative; }
        .field-input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px; }
        .field-input { width: 100%; padding: 14px 14px 14px 44px; border-radius: 14px; border: 1px solid var(--border); background: #f8fafc; color: var(--blue-dark); font-size: 14px; outline: none; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
        .field-input:focus { border-color: #2563eb; box-shadow: 0 0 0 5px rgba(37,99,235,0.12); background: white; }
        .pw-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); border: none; background: none; color: #64748b; cursor: pointer; font-size: 16px; padding: 0; }
        .field-error { margin-top: 8px; display: flex; align-items: center; gap: 8px; color: var(--error); font-size: 12px; }
        .remember-row { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; color: #475569; font-size: 13px; }
        .remember-row input { width: 16px; height: 16px; accent-color: #2563eb; cursor: pointer; }
        .login-btn { width: 100%; border: none; border-radius: 14px; padding: 14px 18px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; font-size: 15px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 10px; transition: transform 0.2s ease, box-shadow 0.2s ease; box-shadow: 0 16px 35px rgba(37,99,235,0.2); }
        .login-btn:hover { transform: translateY(-1px); }
        .login-btn:disabled { opacity: 0.75; cursor: not-allowed; transform: none; box-shadow: none; }
        .login-btn span { display: inline-flex; align-items: center; gap: 10px; }
        .login-divider { text-align: center; margin: 32px 0; position: relative; color: var(--muted); font-size: 12px; }
        .login-divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: var(--border); transform: translateY(-50%); }
        .login-divider span { position: relative; z-index: 1; padding: 0 12px; background: var(--white); }
        .right-footer { margin-top: 30px; color: #94a3b8; font-size: 12px; text-align: center; }
        .spinner { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.45); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 1040px) { .login-wrapper { grid-template-columns: 1fr; } .left-panel { padding: 50px 32px; } .right-panel { padding: 40px 32px; } }
        @media (max-width: 700px) { body { padding: 16px; } .left-panel { display: none; } .login-wrapper { min-height: auto; border-radius: 24px; } .auth-panel { border-radius: 24px; padding: 30px 22px; } }
    </style>
</head>
<body>
<div class="login-wrapper">
    <aside class="left-panel">
        <div class="brand-block">
            <div class="brand-mark">
                <i class="bi bi-ear"></i>
            </div>
            <div>
                <div class="brand-title">ENTER</div>
                <div class="brand-subtitle">ENT Clinic Management System</div>
            </div>
        </div>
        <div class="brand-description">
            <strong>Smarter Care for ENT &amp; HNS Specialists</strong><br>
            A complete clinic management system built for Ear, Nose, Throat, Head &amp; Neck Surgery practices — streamlining patient records, visits, prescriptions, and analytics in one secure platform.
        </div>
    </aside>
    <main class="right-panel">
        <div class="login-card">
            <div class="login-header">
                <h1>Welcome back</h1>
                <p>Sign in to your clinic account to continue.</p>
            </div>
            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:16px;padding:16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#b91c1c;font-size:13px;">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}
                </div>
            @endif
            @if(session('toast_success'))
                <div style="background:#ecfdf5;border:1px solid #bbf7d0;border-radius:16px;padding:16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#166534;font-size:13px;">
                    <i class="bi bi-check-circle-fill"></i> {{ session('toast_success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                @csrf
                <div class="field-group">
                    <label class="field-label" for="email">Email Address</label>
                    <div class="field-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email" class="field-input {{ $errors->has('email') ? 'is-error' : '' }}" value="{{ old('email') }}" placeholder="doctor@entclinic.com" autocomplete="email" required>
                    </div>
                    @error('email')
                        <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                    @enderror
                </div>
                <div class="field-group">
                    <label class="field-label" for="password">Password</label>
                    <div class="field-input-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password" class="field-input {{ $errors->has('password') ? 'is-error' : '' }}" placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" class="pw-toggle" onclick="togglePassword()" tabindex="-1"><i class="bi bi-eye" id="pwEyeIcon"></i></button>
                    </div>
                    @error('password')
                        <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                    @enderror
                </div>
                <div class="remember-row">
                    <input type="checkbox" id="remember_me" name="remember_me" {{ old('remember_me') ? 'checked' : '' }}>
                    <label for="remember_me">Keep me signed in</label>
                </div>
                <button type="submit" class="login-btn" id="loginBtn">
                    <span><i class="bi bi-box-arrow-in-right"></i> Sign In</span>
                </button>
            </form>
            <div class="right-footer">© {{ date('Y') }} ENTer · ENT/HNS Clinic Management System</div>
        </div>
    </main>
</div>
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('pwEyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
