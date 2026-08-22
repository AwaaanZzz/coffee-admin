<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kopi Hiku Himu</title>
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
            max-width: 460px;
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
            padding: 2rem 2.5rem;
        }
        .form-heading {
            color: var(--primary);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }
        .form-subtitle {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
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
        }
        .input-group-text.clickable {
            cursor: pointer;
        }
        .form-control {
            padding: 0.6rem 1rem;
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
            margin-bottom: 1rem;
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
            padding: 12px;
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
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
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
                <a href="{{ route('login') }}" class="tab">Admin Login</a>
                <a href="{{ route('register') }}" class="tab active">Register New Admin</a>
            </div>

            <div class="card-body">
                <h2 class="form-heading">System Access Request</h2>
                <div class="form-subtitle">Please provide credentials for the new administrative role.</div>
                
                @if (session('error'))
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.9rem;">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div>
                        <label class="form-label">Full Name</label>
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                            <span class="input-group-text">
                                <i data-lucide="user" width="18" height="18"></i>
                            </span>
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="admin@example.com">
                            <span class="input-group-text">
                                <i data-lucide="mail" width="18" height="18"></i>
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
                            <span class="input-group-text clickable" onclick="togglePassword('password', 'eye-icon-1')" title="Show/Hide Password">
                                <i data-lucide="eye" id="eye-icon-1" width="18" height="18"></i>
                            </span>
                        </div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="••••••••">
                            <span class="input-group-text clickable" onclick="togglePassword('password_confirmation', 'eye-icon-2')" title="Show/Hide Password">
                                <i data-lucide="eye" id="eye-icon-2" width="18" height="18"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Create Account
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
