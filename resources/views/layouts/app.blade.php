<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Kopi Hiku Himu</title>

    <!-- Google Fonts: Manrope (Primary Sans) & JetBrains Mono (Tabular Data) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
    $topNotifications = collect();
    try {
        if(class_exists(\App\Models\Notification::class)) {
            \App\Http\Controllers\NotificationController::syncOperationalAlerts();
            $unreadNotifications = \App\Models\Notification::where('is_read', false)->count();
            $topNotifications = \App\Models\Notification::latest()->limit(6)->get();
        }
        if(class_exists(\App\Models\StockBatch::class)) {
            $expiringStock = \App\Models\StockBatch::where('status', '!=', 'tarik')->whereBetween('tgl_exp', [now(), now()->addDays(7)])->count();
        }
    } catch (\Exception $e) {
        // Table might not exist yet
    }
@endphp

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-badge-container">
                <img src="{{ asset('images/logo-kopi-hiku-himu.png') }}" alt="Kopi Hiku Himu" class="logo-icon-img">
            </div>
            <div class="sidebar-brand-text">
                <span class="logo-text">Kopi Hiku Himu</span>
                <span class="logo-subtext">Artisan Roastery</span>
            </div>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" title="Kecilkan / Lebarkan Sidebar">
            <i data-lucide="menu"></i>
        </button>
    </div>
    <div class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('stores.index') }}" class="nav-item {{ request()->routeIs('stores.*') ? 'active' : '' }}">
                <i data-lucide="store"></i>
                <span>Toko</span>
            </a>
            <a href="{{ route('coffee-types.index') }}" class="nav-item {{ request()->routeIs('coffee-types.index') || request()->routeIs('coffee-types.create') || request()->routeIs('coffee-types.edit') ? 'active' : '' }}">
                <i data-lucide="coffee"></i>
                <span>Jenis Kopi</span>
            </a>
            <a href="{{ route('coffee-types.modal') }}" class="nav-item {{ request()->routeIs('coffee-types.modal*') ? 'active' : '' }}">
                <i data-lucide="coins"></i>
                <span>Modal / HPP Kopi</span>
            </a>
            <a href="{{ route('stock.index') }}" class="nav-item {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                <i data-lucide="package"></i>
                <span>Stok</span>
                @if($expiringStock > 0)
                <span class="nav-badge">{{ $expiringStock }}</span>
                @endif
            </a>
            <a href="{{ route('stock-opname.index') }}" class="nav-item {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}">
                <i data-lucide="clipboard-check"></i>
                <span>Stok Opname</span>
            </a>
            <a href="{{ route('scanner.index') }}" class="nav-item {{ request()->routeIs('scanner.*') ? 'active' : '' }}">
                <i data-lucide="scan-barcode"></i>
                <span>Terminal Scanner</span>
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Laporan</div>
            <a href="{{ route('sales.index') }}" class="nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3"></i>
                <span>Penjualan</span>
            </a>
            <a href="{{ route('reports.sales') }}" class="nav-item {{ request()->routeIs('reports.sales*') ? 'active' : '' }}">
                <i data-lucide="trending-up"></i>
                <span>Analisis Penjualan</span>
            </a>
            <a href="{{ route('finance.index') }}" class="nav-item {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                <i data-lucide="wallet"></i>
                <span>Keuangan</span>
            </a>
            <a href="{{ route('exports.index') }}" class="nav-item {{ request()->routeIs('exports.*') || request()->routeIs('export.*') ? 'active' : '' }}">
                <i data-lucide="file-spreadsheet"></i>
                <span>Ekspor Laporan</span>
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Alat</div>
            <a href="{{ route('calendar.index') }}" class="nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                <span>Kalender</span>
            </a>
            <a href="{{ route('activity-log.index') }}" class="nav-item {{ request()->routeIs('activity-log.*') ? 'active' : '' }}">
                <i data-lucide="history"></i>
                <span>Log Aktivitas</span>
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
        <div class="topbar-right d-flex align-items-center gap-2">
            <div class="topbar-date d-none d-lg-flex">
                <i data-lucide="calendar"></i>
                <span id="currentDate"></span>
            </div>
            
            <button class="topbar-btn d-flex align-items-center gap-2 px-2" id="searchBtn" title="Spotlight Command Palette (Ctrl+K)" style="width: auto;">
                <i data-lucide="search" style="width:16px;height:16px;"></i>
                <span class="d-none d-md-inline text-muted small me-1">Cari...</span>
                <kbd class="d-none d-md-inline" style="font-size:0.65rem; padding: 2px 6px; border-radius: 4px; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-muted); font-family: 'JetBrains Mono', monospace;">Ctrl K</kbd>
            </button>
            
            <button class="topbar-btn" id="themeToggleBtn" title="Ganti Mode Gelap / Terang">
                <i data-lucide="moon" id="themeIcon"></i>
            </button>
            


            <div class="dropdown">
                <button class="topbar-btn position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Pusat Peringatan & Notifikasi" id="topbarNotifBtn">
                    <i data-lucide="bell"></i>
                    @if($unreadNotifications > 0)
                        <span class="notification-badge" id="topbarNotifBadge">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0 border" style="width: 320px;">
                    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-semibold text-dark small m-0">Peringatan Operasional</span>
                        @if($unreadNotifications > 0)
                            <button type="button" class="btn btn-link p-0 text-muted small text-decoration-none" id="btnMarkAllReadTopbar" style="font-size:0.72rem;">
                                Tandai Dibaca
                            </button>
                        @endif
                    </div>
                    <div class="notification-list" style="max-height: 280px; overflow-y: auto;">
                        @forelse($topNotifications as $notif)
                            <a href="{{ route('notifications.go', $notif->id) }}" class="p-2.5 px-3 border-bottom d-block text-decoration-none {{ $notif->is_read ? 'opacity-75' : '' }}">
                                <div class="d-flex align-items-center justify-content-between mb-0.5">
                                    <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                        @if(!$notif->is_read)
                                            <span style="width:6px; height:6px; border-radius:50%; background-color:var(--accent); display:inline-block; flex-shrink:0;"></span>
                                        @endif
                                        <strong class="text-dark small text-truncate" style="max-width: 200px;">{{ $notif->title }}</strong>
                                    </div>
                                    <small class="text-muted tabular-nums" style="font-size: 0.65rem;">{{ $notif->created_at->diffForHumans(null, true) }}</small>
                                </div>
                                <p class="text-muted mb-0 small text-truncate" style="font-size: 0.76rem;">{{ $notif->message }}</p>
                            </a>
                        @empty
                            <div class="text-center py-4 px-3 text-muted">
                                <small>Semua operasional roastery aman dan terkendali.</small>
                            </div>
                        @endforelse
                    </div>
                    <div class="p-2 border-top text-center">
                        <a href="{{ route('notifications.index') }}" class="small fw-semibold text-secondary text-decoration-none" style="font-size:0.75rem;">
                            Buka Semua Notifikasi &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <div class="dropdown ms-2">
                <button class="profile-btn" data-bs-toggle="dropdown" aria-expanded="false">
                    @php
                        $authUser = auth()->user();
                        $avatarUrl = $authUser && $authUser->avatar 
                            ? (str_starts_with($authUser->avatar, 'http') ? $authUser->avatar : asset($authUser->avatar)) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($authUser->name ?? 'Admin') . '&background=1E3A5F&color=fff';
                    @endphp
                    <img src="{{ $avatarUrl }}" alt="Profile" class="profile-avatar">
                    <div class="profile-info d-none d-md-flex">
                        <span class="profile-name">{{ $authUser->name ?? 'Admin' }}</span>
                        <span class="profile-role">{{ $authUser->role ?? 'Administrator' }}</span>
                    </div>
                    <i data-lucide="chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.index') }}"><i data-lucide="user" class="icon-sm"></i> Profil</a></li>

                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') ?? '/logout' }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                <i data-lucide="log-out" class="icon-sm"></i> Keluar
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

<!-- External Barcode Scanner HUD Toast -->
<div class="scanner-hud-toast" id="scannerHudToast">
    <i data-lucide="scan-barcode" style="color:var(--accent); width:20px; height:20px;"></i>
    <div>
        <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--accent); font-weight:700;">Barcode Gun External</div>
        <div id="scannerHudText" style="font-family:'JetBrains Mono',monospace; font-weight:700; font-size:0.92rem;">HKH-AWA-DAM...</div>
    </div>
</div>

<!-- Spotlight Command Palette (Ctrl + K) -->
<div class="command-palette-backdrop" id="searchModal">
    <div class="command-palette-card">
        <div class="command-palette-input-wrap">
            <i data-lucide="search" style="color:var(--accent); width:20px; height:20px;"></i>
            <input type="text" class="command-palette-input" id="searchInput" placeholder="Cari menu, barcode, toko, atau aksi cepat..." autocomplete="off">
            <kbd style="font-size:0.7rem; padding:3px 7px; border-radius:5px; background:var(--bg-input); border:1px solid var(--border); color:var(--text-muted);">ESC</kbd>
        </div>
        <div class="command-palette-results" id="searchResults">
            <div class="px-2 py-1 text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700;">Aksi Cepat (Quick Actions)</div>
            <a href="{{ route('stock.create') }}" class="command-item">
                <i data-lucide="plus-circle"></i>
                <div><strong>Tambah Stock Baru</strong><div class="small text-muted">Input batch kopi baru & generate barcode</div></div>
                <span class="command-item-badge">Stock</span>
            </a>
            <a href="{{ route('stock.print-labels') }}" target="_blank" class="command-item">
                <i data-lucide="printer"></i>
                <div><strong>Cetak Semua Label Barcode</strong><div class="small text-muted">Buka halaman cetak stiker thermal batch</div></div>
                <span class="command-item-badge">Barcode</span>
            </a>
            <a href="{{ route('sales.create') }}" class="command-item">
                <i data-lucide="shopping-cart"></i>
                <div><strong>Catat Penjualan Kopi</strong><div class="small text-muted">Rekam transaksi penjualan toko mitra</div></div>
                <span class="command-item-badge">Penjualan</span>
            </a>
            <a href="{{ route('stock.index') }}" class="command-item">
                <i data-lucide="package"></i>
                <div><strong>Kelola Data Stock</strong><div class="small text-muted">Pantau sisa stock & status kadaluarsa</div></div>
                <span class="command-item-badge">Navigasi</span>
            </a>
            <a href="{{ route('coffee-types.modal') }}" class="command-item">
                <i data-lucide="coins"></i>
                <div><strong>Modal & HPP Jenis Kopi</strong><div class="small text-muted">Input modal pokok roastery per pack kopi</div></div>
                <span class="command-item-badge">Keuangan</span>
            </a>
            <a href="{{ route('stores.index') }}" class="command-item">
                <i data-lucide="store"></i>
                <div><strong>Daftar Toko Mitra</strong><div class="small text-muted">Kelola toko mitra & harga kopi</div></div>
                <span class="command-item-badge">Navigasi</span>
            </a>
        </div>
        <div class="p-2 border-top d-flex justify-content-between align-items-center text-muted" style="font-size:0.72rem; background:var(--bg-input);">
            <div>Gunakan <kbd style="font-size:0.65rem; padding:1px 4px;">↑</kbd> <kbd style="font-size:0.65rem; padding:1px 4px;">↓</kbd> untuk memilih, <kbd style="font-size:0.65rem; padding:1px 4px;">Enter</kbd> untuk buka</div>
            <div><span class="led-dot led-success me-1"></span>Scanner Gun External Aktif</div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // Global Chart.js Font Standard (Manrope)
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    }

    // Set Indonesian Live Date & Real-time Clock (WIB)
    function updateLiveClock() {
        const el = document.getElementById('currentDate');
        if (!el) return;
        const now = new Date();
        const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
        el.innerText = `${dateStr} • ${timeStr}`;
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);

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

    // =======================================================
    // Web Audio Synthesizer (Crisp Retail Scanner Gun Beep)
    // =======================================================
    function playRetailBeep(success = true) {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            if (success) {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1400, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1900, ctx.currentTime + 0.07);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.1);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(350, ctx.currentTime);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.18);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.18);
            }
        } catch (e) {
            // Audio context requires prior user gesture in some browsers
        }
    }

    // =======================================================
    // Global Hardware / External Barcode Scanner Engine
    // =======================================================
    let barcodeBuffer = '';
    let lastKeyTime = Date.now();
    const hudToast = document.getElementById('scannerHudToast');
    const hudText = document.getElementById('scannerHudText');
    let hudTimeout = null;

    function showScannerHud(code) {
        if (!hudToast) return;
        if (hudText) hudText.innerText = code;
        hudToast.classList.add('active');
        clearTimeout(hudTimeout);
        hudTimeout = setTimeout(() => {
            hudToast.classList.remove('active');
        }, 3000);
    }

    function processScannedBarcode(code) {
        if (!code) return;
        const scannedCode = code.trim().replace(/[\r\n\t]/g, '');
        if (scannedCode.length < 4) return;

        // BEEP & Show HUD
        playRetailBeep(true);
        showScannerHud(scannedCode);

        // Priority 1: Custom page-level handler (e.g. Stock Opname Audit)
        if (typeof window.handleScannedBarcodeGlobal === 'function') {
            window.handleScannedBarcodeGlobal(scannedCode);
            return;
        }

        // Priority 2: Stock Opname input
        if (window.location.pathname.includes('/stock-opname')) {
            const opnameInput = document.getElementById('barcodeGunInput');
            if (opnameInput) {
                opnameInput.value = scannedCode;
                if (typeof window.handleScannedBarcode === 'function') {
                    window.handleScannedBarcode(scannedCode);
                }
            }
            return;
        }

        // Priority 3: Stock page search input
        const stockSearchInput = document.getElementById('stockBarcodeInput') || document.querySelector('input[name="search"]');
        if (stockSearchInput) {
            stockSearchInput.value = scannedCode;
            if (stockSearchInput.form) stockSearchInput.form.submit();
            return;
        }

        // Priority 4: Stock create form
        const kodeProdInput = document.getElementById('kodeProduksiInput') || document.querySelector('input[name="kode_produksi"]');
        if (kodeProdInput) {
            kodeProdInput.value = scannedCode;
            kodeProdInput.dispatchEvent(new Event('input'));
            return;
        }

        // Priority 5: Default redirect to stock with search
        window.location.href = '/stock?search=' + encodeURIComponent(scannedCode);
    }

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.altKey || e.metaKey) return;

        const now = Date.now();
        const diff = now - lastKeyTime;
        lastKeyTime = now;

        // Jika jeda pengetikan lebih dari 180ms, anggap ketikan manual (bukan burst scanner gun) -> reset buffer
        if (diff > 180) {
            barcodeBuffer = '';
        }

        if (e.key === 'Enter') {
            if (document.activeElement && document.activeElement.tagName === 'TEXTAREA') {
                barcodeBuffer = '';
                return;
            }

            const activeInput = document.activeElement;
            const isBarcodeField = activeInput && (activeInput.id === 'stockBarcodeInput' || activeInput.id === 'barcodeGunInput');

            let candidate = barcodeBuffer.trim();
            // Jika buffer kosong tapi input barcode memiliki value dari pengetikan langsung
            if (!candidate && isBarcodeField && activeInput.value) {
                candidate = activeInput.value.trim();
            }

            if (candidate.length >= 4) {
                e.preventDefault();
                processScannedBarcode(candidate);
            }
            barcodeBuffer = '';
        } else if (e.key.length === 1) {
            barcodeBuffer += e.key;

            // Auto-deteksi instan jika buffer mencapai 13 digit EAN-13 (899xxxxxxxxxx) tanpa perlu tekan Enter
            if (/^899\d{10}$/.test(barcodeBuffer)) {
                const code = barcodeBuffer;
                barcodeBuffer = '';
                processScannedBarcode(code);
            }
        }
    });

    // =======================================================
    // Spotlight Command Palette (Ctrl + K) & Keyboard Nav
    // =======================================================
    const searchModal = document.getElementById('searchModal');
    const searchBtn = document.getElementById('searchBtn');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let focusedIndex = -1;

    const initialQuickActions = searchResults ? searchResults.innerHTML : '';

    function openSearch() {
        if (!searchModal) return;
        searchModal.classList.add('active');
        focusedIndex = -1;
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        if (searchResults) {
            searchResults.innerHTML = initialQuickActions;
            lucide.createIcons();
        }
    }

    function closeSearch() {
        if (!searchModal) return;
        searchModal.classList.remove('active');
    }

    if (searchBtn) searchBtn.addEventListener('click', openSearch);
    
    if (searchModal) {
        searchModal.addEventListener('click', (e) => {
            if(e.target === searchModal) closeSearch();
        });
    }

    function updateCommandFocus(items) {
        items.forEach((item, i) => {
            if (i === focusedIndex) {
                item.classList.add('focused');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('focused');
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            if (searchModal && searchModal.classList.contains('active')) {
                closeSearch();
            } else {
                openSearch();
            }
        }
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
            e.preventDefault();
            window.location.href = "{{ route('scanner.index') }}";
        }
        if (e.key === 'Escape' && searchModal && searchModal.classList.contains('active')) {
            closeSearch();
        }

        if (searchModal && searchModal.classList.contains('active')) {
            const items = searchResults.querySelectorAll('.command-item, .search-result-item');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                focusedIndex = (focusedIndex + 1) % items.length;
                updateCommandFocus(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                focusedIndex = (focusedIndex - 1 + items.length) % items.length;
                updateCommandFocus(items);
            } else if (e.key === 'Enter') {
                if (focusedIndex >= 0 && items[focusedIndex]) {
                    e.preventDefault();
                    items[focusedIndex].click();
                }
            }
        }
    });

    // Search AJAX
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const query = this.value.trim();
            focusedIndex = -1;
            if(query.length > 1) {
                searchResults.innerHTML = '<div class="p-3 text-center text-muted"><small>Mencari...</small></div>';
                fetch('/search?q=' + encodeURIComponent(query))
                    .then(r => r.json())
                    .then(data => {
                        let html = '';
                        if(data.stores && data.stores.length) {
                            html += '<div class="px-3 pt-2 pb-1" style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;font-weight:700;">Toko Mitra</div>';
                            data.stores.forEach(item => {
                                html += '<a href="'+encodeURI(item.url)+'" class="command-item"><i data-lucide="store"></i><div><div class="fw-bold">'+escapeHtml(item.title)+'</div><small class="text-muted">'+escapeHtml(item.subtitle)+'</small></div><span class="command-item-badge">Toko</span></a>';
                            });
                        }
                        if(data.coffeeTypes && data.coffeeTypes.length) {
                            html += '<div class="px-3 pt-2 pb-1" style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;font-weight:700;">Jenis Kopi</div>';
                            data.coffeeTypes.forEach(item => {
                                html += '<a href="'+encodeURI(item.url)+'" class="command-item"><i data-lucide="coffee"></i><div><div class="fw-bold">'+escapeHtml(item.title)+'</div><small class="text-muted">'+escapeHtml(item.subtitle)+'</small></div><span class="command-item-badge">Produk</span></a>';
                            });
                        }
                        if(data.stocks && data.stocks.length) {
                            html += '<div class="px-3 pt-2 pb-1" style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;font-weight:700;">Stock & Barcode</div>';
                            data.stocks.forEach(item => {
                                html += '<a href="'+encodeURI(item.url)+'" class="command-item"><i data-lucide="scan-barcode"></i><div><div class="fw-bold font-monospace">'+escapeHtml(item.title)+'</div><small class="text-muted">'+escapeHtml(item.subtitle)+'</small></div><span class="command-item-badge">Barcode</span></a>';
                            });
                        }
                        if(!html) html = '<div class="p-4 text-center text-muted"><i data-lucide="help-circle" class="mb-2 text-muted" style="width:24px;height:24px;"></i><div>Tidak ada hasil untuk "'+escapeHtml(query)+'"</div></div>';
                        searchResults.innerHTML = html;
                        lucide.createIcons();
                    })
                    .catch(() => {
                        searchResults.innerHTML = '<div class="p-3 text-center text-muted"><small>Error saat mencari</small></div>';
                    });
            } else {
                searchResults.innerHTML = initialQuickActions;
                lucide.createIcons();
            }
        }, 200));
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Auto-dismiss session flash alerts only (not permanent cards/guides)
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible.alert-modern').forEach(alert => {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) {}
        });
    }, 5000);

    // Mark All Notifications Read in Topbar Dropdown
    const btnMarkAllTopbar = document.getElementById('btnMarkAllReadTopbar');
    if (btnMarkAllTopbar) {
        btnMarkAllTopbar.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('{{ route("notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    const badge = document.getElementById('topbarNotifBadge');
                    if (badge) badge.remove();
                    btnMarkAllTopbar.remove();
                    document.querySelectorAll('.notification-list a').forEach(el => {
                        el.classList.add('opacity-75', 'bg-white');
                        el.classList.remove('bg-light');
                        const dot = el.querySelector('span[style*="border-radius:50%"]');
                        if (dot) dot.remove();
                    });
                }
            }).catch(err => console.error(err));
        });
    }

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
