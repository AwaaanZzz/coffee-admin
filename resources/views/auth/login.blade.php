<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kopi Hiku Himu</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Literata:ital,opsz,wght@0,7..72,200..900;1,7..72,200..900&family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #1E3A5F;
            --secondary: #C88A4E;
            --tertiary: #A67261;
            --neutral: #F5E5D3;
            --bg-gradient: linear-gradient(135deg, #F5E5D3 0%, #FAF6F0 100%);
        }
        body {
            font-family: 'Manrope', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1, h2, h3, h4, h5, h6, .brand-name {
            font-family: 'Literata', serif;
        }
        .login-wrapper {
            width: 100%;
            max-width: 440px;
        }
        .brand-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-img {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            object-fit: cover;
        }
        .brand-name {
            color: var(--primary);
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .tagline {
            color: var(--tertiary);
            font-style: italic;
            font-size: 0.9rem;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .tabs {
            display: flex;
            border-bottom: 1px solid #eee;
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 1rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        .tab.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
            background: #fdfdfd;
        }
        .tab:not(.active) {
            color: #888;
            border-bottom: 3px solid transparent;
        }
        .tab:not(.active):hover {
            color: #555;
            background: #fafafa;
        }
        .card-body {
            padding: 2.5rem 2rem;
        }
        .form-label {
            font-weight: 600;
            color: #444;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }
        .input-group-text {
            background: transparent;
            border-left: none;
            color: #888;
            cursor: pointer;
        }
        .form-control {
            padding: 0.7rem 1rem;
            border-right: none;
            border-radius: 8px 0 0 8px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary);
        }
        .form-control:focus + .input-group-text {
            border-color: var(--primary);
            color: var(--primary);
        }
        .input-group {
            margin-bottom: 1.2rem;
        }
        .input-group > .form-control {
            border-right: none;
        }
        .input-group > .input-group-text {
            border-radius: 0 8px 8px 0;
            background-color: #fff;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border: none;
            border-radius: 30px;
            padding: 14px;
            font-weight: 600;
            font-size: 1.05rem;
            transition: background-color 0.2s;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary:hover {
            background-color: #152b47;
        }
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .reset-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .reset-link:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: -0.8rem;
            margin-bottom: 1rem;
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
                <span class="tab active" style="cursor:default;">Admin Login</span>
            </div>

            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.9rem;">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.9rem;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div>
                        <label class="form-label">Admin ID or Email</label>
                        <div class="input-group">
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
                            <span class="input-group-text">
                                <i data-lucide="user" width="18" height="18"></i>
                            </span>
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                            <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;" title="Show/Hide Password">
                                <i data-lucide="eye" id="eye-icon" width="18" height="18"></i>
                            </span>
                        </div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember" style="font-size: 0.85rem; color: #666;">
                                Remember me
                            </label>
                        </div>
                        <a href="#" class="reset-link">Reset Access</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Login to Portal 
                        <i data-lucide="arrow-right" width="18" height="18"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
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
