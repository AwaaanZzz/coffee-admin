<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Kopi Hiku Himu</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Literata:ital,wght@0,400;0,600;0,700;1,400&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
    
    @yield('styles')
</head>
<body>

@php
    $unreadNotifications = 0;
    $expiringStock = 0;
    try {
        if(class_exists(\App\Models\Notification::class)) {
            $unreadNotifications = \App\Models\Notification::where('is_read', false)->count();
        }
        if(class_exists(\App\Models\StockBatch::class)) {
            $expiringStock = \App\Models\StockBatch::whereBetween('tgl_exp', [now(), now()->addDays(7)])->count();
        }
    } catch (\Exception $e) {
        // Table might not exist yet
    }
@endphp

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo-kopi-hiku-himu.png') }}" alt="Kopi Hiku Himu" class="logo-icon-img" style="width:40px;height:40px;max-width:40px;max-height:40px;object-fit:cover;border-radius:10px;">
            <span class="logo-text">Kopi Hiku Himu</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i data-lucide="menu"></i>
        </button>
    </div>
    <div class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('stores.index') }}" class="nav-item {{ request()->routeIs('stores.*') ? 'active' : '' }}">
                <i data-lucide="store"></i>
                <span>Toko</span>
            </a>
            <a href="{{ route('coffee-types.index') }}" class="nav-item {{ request()->routeIs('coffee-types.*') ? 'active' : '' }}">
                <i data-lucide="coffee"></i>
                <span>Jenis Kopi</span>
            </a>
            <a href="{{ route('stock.index') }}" class="nav-item {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                <i data-lucide="package"></i>
                <span>Stock</span>
                @if($expiringStock > 0)
                <span class="nav-badge">{{ $expiringStock }}</span>
                @endif
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Laporan</div>
            <a href="{{ route('sales.index') }}" class="nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3"></i>
                <span>Penjualan</span>
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Tools</div>
            <a href="{{ route('calendar.index') }}" class="nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                <span>Calendar</span>
            </a>

        </div>
    </div>
</aside>

<!-- Main Content Wrapper -->
<div class="main-content">
    
    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="btn-hamburger" id="mobileMenuBtn">
                <i data-lucide="menu"></i>
            </button>
            <div class="breadcrumbs">
                @yield('breadcrumbs')
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date d-none d-md-flex">
                <i data-lucide="calendar"></i>
                <span id="currentDate"></span>
            </div>
            
            <button class="topbar-btn" id="searchBtn" title="Search (Ctrl+K)">
                <i data-lucide="search"></i>
            </button>
            
            <button class="topbar-btn" id="themeToggleBtn" title="Toggle Dark Mode">
                <i data-lucide="moon" id="themeIcon"></i>
            </button>
            


            <div class="dropdown">
                <button class="topbar-btn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i data-lucide="bell"></i>
                    @if($unreadNotifications > 0)
                        <span class="notification-badge">{{ $unreadNotifications }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <div class="dropdown-header">Notifikasi</div>
                    <div class="notification-list">
                        <!-- Notifications would be loaded dynamically here -->
                        <div class="notification-item unread text-center p-3 text-muted">
                            Belum ada notifikasi
                        </div>
                    </div>
                </div>
            </div>

            <div class="dropdown ms-2">
                <button class="profile-btn" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=1E3A5F&color=fff" alt="Profile" class="profile-avatar">
                    <div class="profile-info d-none d-md-flex">
                        <span class="profile-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="profile-role">{{ auth()->user()->role ?? 'Administrator' }}</span>
                    </div>
                    <i data-lucide="chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.index') }}"><i data-lucide="user" class="icon-sm"></i> Profile</a></li>

                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') ?? '/logout' }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                <i data-lucide="log-out" class="icon-sm"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<!-- Search Modal -->
<div class="search-modal" id="searchModal">
    <div class="search-box">
        <div class="search-input-wrap">
            <i data-lucide="search"></i>
            <input type="text" class="search-input" id="searchInput" placeholder="Cari data, menu, laporan...">
            <span class="search-kbd">ESC</span>
        </div>
        <div class="search-results" id="searchResults">
            <div class="text-center p-4 text-muted">
                <small>Ketik untuk mulai mencari...</small>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // Set Indonesian Date
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').innerText = new Date().toLocaleDateString('id-ID', dateOptions);

    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    
    // Desktop: check saved state (default = expanded)
    // Reset sidebar state after Vintage Roast redesign
    const isMobile = window.innerWidth <= 991;
    if (!localStorage.getItem('vintageRoastV1')) {
        localStorage.removeItem('sidebarCollapsed');
        localStorage.setItem('vintageRoastV1', 'true');
    }
    if(!isMobile && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }

    if(sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }

    if(mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            if (window.innerWidth <= 991) {
                sidebar.classList.toggle('mobile-open');
            } else {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        });
    }

    // Dark Mode Toggle
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const htmlElement = document.documentElement;
    const themeIcon = document.getElementById('themeIcon');
    
    // Check saved theme
    const currentTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon(currentTheme);

    themeToggleBtn.addEventListener('click', () => {
        const newTheme = htmlElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        htmlElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        if(theme === 'dark') {
            themeIcon.setAttribute('data-lucide', 'sun');
        } else {
            themeIcon.setAttribute('data-lucide', 'moon');
        }
        lucide.createIcons();
    }

    // Search Modal & Shortcuts
    const searchModal = document.getElementById('searchModal');
    const searchBtn = document.getElementById('searchBtn');
    const searchInput = document.getElementById('searchInput');

    function openSearch() {
        searchModal.classList.add('active');
        searchInput.focus();
    }

    function closeSearch() {
        searchModal.classList.remove('active');
    }

    searchBtn.addEventListener('click', openSearch);
    
    searchModal.addEventListener('click', (e) => {
        if(e.target === searchModal) closeSearch();
    });

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openSearch();
        }
        if (e.key === 'Escape' && searchModal.classList.contains('active')) {
            closeSearch();
        }
    });

    // Search AJAX
    searchInput.addEventListener('input', debounce(function() {
        const query = this.value;
        if(query.length > 1) {
            document.getElementById('searchResults').innerHTML = '<div class="p-3 text-center text-muted">Mencari...</div>';
            fetch('/search?q=' + encodeURIComponent(query))
                .then(r => r.json())
                .then(data => {
                    let html = '';
                    if(data.stores && data.stores.length) {
                        html += '<div class="px-3 pt-2 pb-1" style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em">Toko</div>';
                        data.stores.forEach(item => {
                            html += '<a href="'+item.url+'" class="search-result-item"><i data-lucide="store"></i><div><div>'+item.title+'</div><small class="text-muted">'+item.subtitle+'</small></div></a>';
                        });
                    }
                    if(data.coffeeTypes && data.coffeeTypes.length) {
                        html += '<div class="px-3 pt-2 pb-1" style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em">Jenis Kopi</div>';
                        data.coffeeTypes.forEach(item => {
                            html += '<a href="'+item.url+'" class="search-result-item"><i data-lucide="coffee"></i><div><div>'+item.title+'</div><small class="text-muted">'+item.subtitle+'</small></div></a>';
                        });
                    }
                    if(data.stocks && data.stocks.length) {
                        html += '<div class="px-3 pt-2 pb-1" style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em">Stock</div>';
                        data.stocks.forEach(item => {
                            html += '<a href="'+item.url+'" class="search-result-item"><i data-lucide="package"></i><div><div>'+item.title+'</div><small class="text-muted">'+item.subtitle+'</small></div></a>';
                        });
                    }
                    if(!html) html = '<div class="p-3 text-center text-muted">Tidak ada hasil ditemukan</div>';
                    document.getElementById('searchResults').innerHTML = html;
                    lucide.createIcons();
                })
                .catch(() => {
                    document.getElementById('searchResults').innerHTML = '<div class="p-3 text-center text-muted">Error saat mencari</div>';
                });
        } else {
            document.getElementById('searchResults').innerHTML = '<div class="text-center p-4 text-muted"><small>Ketik untuk mulai mencari...</small></div>';
        }
    }, 300));

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-modern').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // PWA Service Worker Registration
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').then(registration => {
                console.log('SW registered: ', registration);
            }).catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
        });
    }
</script>

@yield('scripts')

</body>
</html>
