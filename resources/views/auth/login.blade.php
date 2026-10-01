<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kopi Hiku Himu</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #1E3A5F;
            --primary-dark: #152B47;
            --accent: #C88A4E;
            --accent-hover: #B77A3E;
            --text-main: #2C1E14;
            --text-muted: #786C60;
            --border-color: #E8DFD5;
            --bg-page: #FBF7F0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-page);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.25rem;
            color: var(--text-main);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            animation: cardAppear 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand-section {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .logo-img-box {
            position: relative;
            display: inline-block;
            margin-bottom: 0.85rem;
        }

        .logo-img {
            width: 74px;
            height: 74px;
            border-radius: 18px;
            box-shadow: 0 10px 24px -4px rgba(200, 138, 78, 0.28);
            border: 2px solid #ffffff;
            object-fit: cover;
            display: block;
        }

        .brand-name {
            font-family: 'Manrope', sans-serif;
            color: var(--primary);
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin-bottom: 0.2rem;
        }

        .tagline {
            font-family: 'Manrope', sans-serif;
            color: var(--accent);
            font-style: italic;
            font-size: 0.88rem;
            letter-spacing: 0.2px;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 45px -12px rgba(44, 30, 20, 0.08), 0 2px 6px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
        }

        .card-body-custom {
            padding: 2.25rem 2rem;
        }

        .auth-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.25rem;
            text-align: center;
        }

        .auth-subtitle {
            font-size: 0.82rem;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .form-label-custom {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.45rem;
            display: block;
        }

        .custom-input-group {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .custom-input-group .form-control {
            width: 100%;
            height: 48px;
            padding: 0.65rem 42px 0.65rem 1rem;
            font-size: 0.92rem;
            font-family: inherit;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            background: #ffffff;
            color: var(--text-main);
            transition: all 0.2s ease;
        }

        .custom-input-group .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(200, 138, 78, 0.14);
            background: #ffffff;
        }

        /* Disable Microsoft Edge / IE native password reveal & clear icons */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        .custom-input-group .input-icon-box {
            position: absolute;
            right: 14px;
            color: var(--text-muted);
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-input-group .btn-password-toggle {
            position: absolute;
            right: 8px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 6px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
            z-index: 5;
            user-select: none;
        }

        .custom-input-group .btn-password-toggle:hover {
            color: var(--primary);
        }

        .form-check-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.5rem;
        }

        .form-check-custom .form-check-input {
            width: 17px;
            height: 17px;
            margin-top: 0;
            border: 1.5px solid #C8BFB5;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check-custom .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-custom .form-check-label {
            font-size: 0.84rem;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            height: 48px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.2px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(30, 58, 95, 0.25);
            cursor: pointer;
        }

        .btn-login:hover {
            background: var(--primary-dark);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(30, 58, 95, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-feedback {
            color: #dc2626;
            font-size: 0.78rem;
            font-weight: 500;
            margin-top: -0.9rem;
            margin-bottom: 1rem;
            display: block;
        }

        .footer-credit {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.76rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Logo & Header Roastery -->
        <div class="brand-section">
            <div class="logo-img-box">
                <img src="{{ asset('images/logo-kopi-hiku-himu.png') }}" alt="Kopi Hiku Himu" class="logo-img">
            </div>
            <h1 class="brand-name">Kopi Hiku Himu</h1>
            <div class="tagline">"Hidupku Hidupmu No Julid No Drama"</div>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            <div class="card-body-custom">
                <h2 class="auth-title">Admin Login</h2>
                <p class="auth-subtitle">Sistem Manajemen & Distribusi Kopi</p>

                @if (session('error'))
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.85rem; border-radius: 10px;">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.85rem; border-radius: 10px;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Email / Admin ID -->
                    <div>
                        <label class="form-label-custom">Admin ID or Email</label>
                        <div class="custom-input-group">
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username">
                            <span class="input-icon-box">
                                <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                            </span>
                        </div>
                        @error('email')
                            <span class="error-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="form-label-custom">Password</label>
                        <div class="custom-input-group">
                            <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
                            <button type="button" class="btn-password-toggle" onclick="togglePassword()" title="Tampilkan / Sembunyikan Password" tabindex="-1">
                                <span id="iconEyeOpen" style="display: inline-flex; align-items: center;"><i data-lucide="eye" style="width: 18px; height: 18px;"></i></span>
                                <span id="iconEyeClosed" style="display: none; align-items: center;"><i data-lucide="eye-off" style="width: 18px; height: 18px;"></i></span>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check-custom">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login">
                        <span>Login to Portal</span>
                        <i data-lucide="arrow-right" style="width: 17px; height: 17px;"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="footer-credit">
            &copy; {{ date('Y') }} Kopi Hiku Himu Artisan Roastery
        </div>
    </div>

    <!-- Bootstrap JS & Lucide -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('iconEyeOpen');
            const eyeClosed = document.getElementById('iconEyeClosed');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                if (eyeOpen) eyeOpen.style.display = 'none';
                if (eyeClosed) eyeClosed.style.display = 'inline-flex';
            } else {
                passwordInput.type = 'password';
                if (eyeOpen) eyeOpen.style.display = 'inline-flex';
                if (eyeClosed) eyeClosed.style.display = 'none';
            }
        }
    </script>
</body>
</html>
