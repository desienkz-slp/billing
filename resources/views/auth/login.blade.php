<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LadaPala-Bill - Sistem Billing ISP Professional">
    <title>Login — LadaPala-Bill</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary: #0a0e1a;
            --bg-card: rgba(15, 23, 42, 0.8);
            --bg-input: rgba(30, 41, 59, 0.6);
            --border-color: rgba(99, 102, 241, 0.2);
            --border-focus: rgba(99, 102, 241, 0.6);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent: #6366f1;
            --accent-light: #818cf8;
            --accent-glow: rgba(99, 102, 241, 0.4);
            --danger: #ef4444;
            --success: #22c55e;
            --gradient-1: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            --gradient-2: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background */
        .bg-animation {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-animation .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s ease-in-out infinite;
        }

        .bg-animation .orb:nth-child(1) {
            width: 500px; height: 500px;
            background: #6366f1;
            top: -10%; left: -5%;
            animation-delay: 0s;
        }

        .bg-animation .orb:nth-child(2) {
            width: 400px; height: 400px;
            background: #8b5cf6;
            bottom: -15%; right: -10%;
            animation-delay: -5s;
            animation-duration: 25s;
        }

        .bg-animation .orb:nth-child(3) {
            width: 300px; height: 300px;
            background: #0ea5e9;
            top: 50%; left: 60%;
            animation-delay: -10s;
            animation-duration: 30s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(50px, -30px) scale(1.05); }
            50% { transform: translate(-20px, 40px) scale(0.95); }
            75% { transform: translate(30px, 20px) scale(1.02); }
        }

        /* Grid pattern overlay */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 1;
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Login container */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        /* Logo */
        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 64px; height: 64px;
            background: var(--gradient-1);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 32px var(--accent-glow);
            animation: pulse-glow 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 8px 32px var(--accent-glow); }
            50% { box-shadow: 0 8px 48px rgba(99, 102, 241, 0.6); }
        }

        .logo-icon svg {
            width: 32px; height: 32px;
            fill: white;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .logo-subtitle {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
            font-weight: 400;
        }

        /* Card */
        .login-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: border-color 0.3s ease;
        }

        .login-card:hover {
            border-color: rgba(99, 102, 241, 0.35);
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .card-desc {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 28px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            width: 18px; height: 18px;
            transition: color 0.2s;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 15px;
            font-family: inherit;
            outline: none;
            transition: all 0.25s ease;
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .form-input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: rgba(30, 41, 59, 0.9);
        }

        .form-input:focus ~ .input-icon,
        .form-input:focus + .input-icon {
            color: var(--accent-light);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: var(--text-primary);
        }

        /* Checkbox */
        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .form-check input[type="checkbox"] {
            appearance: none;
            width: 18px; height: 18px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .form-check input[type="checkbox"]:checked {
            background: var(--accent);
            border-color: var(--accent);
        }

        .form-check input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 5px; top: 2px;
            width: 5px; height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .form-check label {
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--gradient-1);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px var(--accent-glow);
        }

        .btn-login:hover::before {
            opacity: 1;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login .spinner {
            display: none;
            width: 20px; height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin: 0 auto;
        }

        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .spinner { display: inline-block; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Error */
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.4s ease-in-out;
        }

        .alert-error svg {
            flex-shrink: 0;
            width: 18px; height: 18px;
            color: var(--danger);
        }

        .alert-error span {
            font-size: 13px;
            color: #fca5a5;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .login-footer a {
            color: var(--accent-light);
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card { padding: 28px 24px; }
            .logo-text { font-size: 24px; }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>
    <div class="bg-grid"></div>

    <!-- Login Form -->
    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div class="logo-text">LadaPala-Bill</div>
            <div class="logo-subtitle">Sistem Billing ISP Professional</div>
        </div>

        <div class="login-card">
            <h1 class="card-title">Selamat Datang</h1>
            <p class="card-desc">Masuk ke dashboard untuk mengelola billing ISP Anda</p>

            @if ($errors->any())
                <div class="alert-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input type="text" class="form-input" id="username" name="username"
                               value="{{ old('username') }}" placeholder="Masukkan username"
                               autofocus autocomplete="username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input type="password" class="form-input" id="password" name="password"
                               placeholder="Masukkan password" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="btn-text">Masuk ke Dashboard</span>
                    <div class="spinner"></div>
                </button>
            </form>
        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} LadaPala-Bill v2.0 &mdash; ISP Billing System
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('loginBtn').classList.add('loading');
        });
    </script>
</body>
</html>
