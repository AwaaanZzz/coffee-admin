<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kopi Hiku Himu</title>
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
            --secondary: #C88A4E;
            --border-color: #E8DFD5;
            --bg-page: #FAF5EE;
            --text-main: #1A202C;
            --text-muted: #786C60;
        }
        body {
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-page);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            color: var(--text-main);
        }
        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }
        .brand-section {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-img {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-bottom: 0.75rem;
            object-fit: cover;
        }
        .brand-name {
            color: var(--primary);
            font-size: 1.45rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
        }
        .tagline {
            color: var(--text-muted);
            font-size: 0.82rem;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 0.85rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.15s ease;
        }
        .tab.active {
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
            background: #ffffff;
        }
        .tab:not(.active) {
            color: var(--text-muted);
            border-bottom: 2px solid transparent;
            background: var(--bg-page);
        }
        .card-body {
            padding: 1.75rem;
        }
        .form-heading {
            color: var(--primary);
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .form-subtitle {
            color: var(--text-muted);
            font-size: 0.82rem;
            margin-bottom: 1.25rem;
        }
        .form-label {
            font-weight: 600;
            color: var(--text-main);
            font-size: 0.82rem;
            margin-bottom: 0.35rem;
        }
        .input-group-text {
            background: transparent;
            border-left: none;
            color: var(--text-muted);
        }
        .input-group-text.clickable {
            cursor: pointer;
        }
        .form-control {
            padding: 0.5rem 0.85rem;
            border-right: none;
            border-radius: 8px 0 0 8px;
            border: 1px solid var(--border-color);
            font-size: 0.9rem;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary);
        }
        .form-control:focus + .input-group-text {
            border-color: var(--primary);
            color: var(--primary);
        }
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
        .input-group {
            margin-bottom: 1rem;
        }
        .input-group > .form-control {
            border-right: none;
        }
        .input-group > .input-group-text {
            border-radius: 0 8px 8px 0;
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-left: none;
        }
        
        .btn-register-action {
            background-color: var(--secondary);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.15s;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            width: 100%;
        }
        .btn-register-action:hover {
            background-color: #b57a3e;
            color: #ffffff;
        }
        .error-message {
            color: #c62828;
            font-size: 0.8rem;
            margin-top: -0.6rem;
            margin-bottom: 0.8rem;
            display: block;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="brand-section">
            <img src="/images/logo-kopi-hiku-himu.png" alt="Logo" class="logo-img">
            <h1 class="brand-name">Kopi Hiku Himu</h1>
            <div class="tagline">"Hidupku Hidupmu No Julid No Drama"</div>
        </div>

        <div class="auth-card">
            <div class="tabs">
                <a href="{{ route('login') }}" class="tab">Masuk admin</a>
                <a href="{{ route('register') }}" class="tab active">Daftar admin baru</a>
            </div>

            <div class="card-body">
                <h2 class="form-heading">Pendaftaran akun admin</h2>
                <div class="form-subtitle">Lengkapi identitas untuk akun administratif baru.</div>
                
                @if (session('error'))
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.85rem; border-radius: 8px;">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div>
                        <label class="form-label">Nama lengkap</label>
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Nama lengkap">
                            <span class="input-group-text">
                                <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                            </span>
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Alamat email</label>
                        <div class="input-group">
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="admin@kopihikuhimu.id">
                            <span class="input-group-text">
                                <i data-lucide="mail" style="width: 16px; height: 16px;"></i>
                            </span>
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Kata sandi</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 karakter">
                            <span class="input-group-text clickable" onclick="togglePassword('password', 'eye-icon-1')" title="Tampilkan / sembunyikan kata sandi">
                                <i data-lucide="eye" id="eye-icon-1" style="width: 16px; height: 16px;"></i>
                            </span>
                        </div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="form-label">Konfirmasi kata sandi</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi kata sandi">
                            <span class="input-group-text clickable" onclick="togglePassword('password_confirmation', 'eye-icon-2')" title="Tampilkan / sembunyikan kata sandi">
                                <i data-lucide="eye" id="eye-icon-2" style="width: 16px; height: 16px;"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn-register-action">
                        <span>Buat akun admin</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
