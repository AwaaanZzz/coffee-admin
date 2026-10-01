@extends('layouts.app')

@section('title', 'Terminal Scanner Barcode')

@section('breadcrumbs')
<a href="{{ route('dashboard') }}">Beranda</a>
<i data-lucide="chevron-right"></i>
<span>Terminal Scanner Barcode</span>
@endsection

@section('styles')
<style>
    /* Executive Kiosk Scanner Terminal */
    .scanner-hero-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #E8DFD5);
        border-radius: var(--radius-sm, 8px);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.25rem;
        position: relative;
    }

    .scanner-hero-card::before {
        display: none !important;
    }

    .scanner-hardware-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(46, 125, 50, 0.08);
        color: var(--success, #2e7d32);
        border: 1px solid rgba(46, 125, 50, 0.25);
        padding: 4px 10px;
        border-radius: var(--radius-sm, 8px);
        font-size: 0.8rem;
        font-weight: 600;
    }

    .scanner-hardware-badge .pulse-led {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: var(--success, #2e7d32);
    }

    /* Target Scanner Box */
    .scan-station-box {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #E2E8F0);
        border-radius: var(--radius-sm, 8px);
        padding: 1.25rem 1.5rem;
        position: relative;
        box-shadow: var(--shadow-sm);
    }

    .scan-station-box.is-active {
        border-color: var(--navy, #1E3A5F);
        background: var(--bg-card, #ffffff);
        box-shadow: 0 0 0 1px var(--navy, #1E3A5F);
    }

    .scan-input-wrapper {
        position: relative;
    }

    .scan-input-wrapper input {
        font-family: var(--font-sans, 'Manrope', -apple-system, sans-serif);
        font-size: 1.05rem;
        font-weight: 600;
        padding: 0.75rem 1.25rem 0.75rem 3rem;
        border-radius: var(--radius-sm, 8px);
        border: 1px solid var(--border-color, #E2E8F0);
        background: var(--bg-card, #ffffff);
        color: var(--text-main, #2C1E14);
    }

    .scan-input-wrapper input:focus {
        border-color: var(--navy, #1E3A5F);
        box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.1);
        outline: none;
    }

    .scan-input-wrapper .input-icon {
        position: absolute;
        left: 1.1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted, #8A9BB0);
        pointer-events: none;
    }

    .key-shortcut {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 1px 5px;
        font-size: 0.65rem;
        font-family: inherit;
        font-weight: 600;
        color: var(--text-muted, #786C60);
        background: var(--bg-main, #FAF5EE);
        border: 1px solid var(--border-color, #DCD3C7);
        border-radius: 4px;
        line-height: 1.2;
    }

    .btn-reset-input {
        color: var(--text-muted, #786C60);
        transition: color 0.15s ease;
    }

    .btn-reset-input:hover {
        color: var(--accent, #C88A4E) !important;
    }

    .btn-reset-input:hover .key-shortcut {
        border-color: var(--accent, #C88A4E);
        color: var(--accent, #C88A4E);
    }

    /* Laser Line Animation */
    .laser-scanline {
        position: absolute;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #ef4444, transparent);
        opacity: 0;
        pointer-events: none;
    }

    .laser-scanline.scanning {
        opacity: 0.8;
        animation: scanlineMove 1.2s infinite ease-in-out;
    }

    @keyframes scanlineMove {
        0% { top: 10%; }
        50% { top: 90%; }
        100% { top: 10%; }
    }

    /* Product Display Card */
    .product-kiosk-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #E8DFD5);
        border-radius: var(--radius-sm, 8px);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .product-kiosk-header {
        background: var(--navy, #1E3A5F);
        color: #ffffff;
        padding: 1.25rem 1.5rem;
        position: relative;
    }

    .product-kiosk-header .coffee-title {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: #FFFFFF;
        margin-bottom: 0.25rem;
        line-height: 1.2;
    }

    .product-kiosk-header .badge-category {
        background: rgba(255, 255, 255, 0.15);
        color: #FFFFFF;
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    /* Price Display */
    .price-kiosk-display {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #E8DFD5);
        border-radius: var(--radius-sm, 8px);
        padding: 1.25rem;
        text-align: center;
    }

    .price-kiosk-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted, #786C60);
        margin-bottom: 0.35rem;
    }

    .price-kiosk-value {
        font-size: 1.85rem;
        font-weight: 700;
        color: var(--accent, #C88A4E);
        font-variant-numeric: tabular-nums;
        line-height: 1;
    }

    /* Freshness Pill */
    .freshness-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: var(--radius-sm, 8px);
        font-weight: 600;
        font-size: 0.82rem;
    }

    .freshness-pill.fresh {
        background: rgba(46, 125, 50, 0.1);
        color: var(--success, #2e7d32);
        border: 1px solid rgba(46, 125, 50, 0.25);
    }

    .freshness-pill.warning {
        background: rgba(230, 81, 0, 0.1);
        color: var(--warning, #e65100);
        border: 1px solid rgba(230, 81, 0, 0.25);
    }

    .freshness-pill.expired {
        background: rgba(198, 40, 40, 0.1);
        color: var(--danger, #c62828);
        border: 1px solid rgba(198, 40, 40, 0.25);
    }

    /* Barcode Visual Box */
    .barcode-render-box {
        background: #ffffff;
        border: 1px solid var(--border-color, #E2E8F0);
        border-radius: var(--radius-sm, 8px);
        padding: 0.75rem;
        text-align: center;
    }

    .barcode-render-box svg {
        max-width: 100%;
        height: auto;
    }

    /* Empty Placeholder */
    .kiosk-empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--text-muted, #786C60);
    }

    .kiosk-empty-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm, 8px);
        background: var(--bg-hover, #F4EFEA);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        color: var(--text-muted, #786C60);
        border: 1px solid var(--border-color, #E2E8F0);
    }

    /* Recent Scan Table */
    .table-scan-history {
        font-size: 0.88rem;
    }

    .table-scan-history th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted, #786C60);
        font-weight: 700;
        background: var(--bg-input, #FBF7F0);
        padding: 0.85rem 1rem;
        white-space: nowrap;
    }

    .table-scan-history td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-2">

    <!-- Top Station Header Card -->
    <div class="scanner-hero-card">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div>
                <h3 class="fw-bold mb-0" style="color: var(--text-main, #2C1E14);">Terminal Scanner Barcode</h3>
            </div>
            
            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Status Hardware -->
                <div class="scanner-hardware-badge" title="Scanner terdeteksi dan siap memindai">
                    <span class="pulse-led"></span>
                    <span>Scanner Siap</span>
                </div>

                <!-- Toggle Suara Beep -->
                <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" id="btnToggleSound" title="Aktif/Nonaktifkan Suara Beep">
                    <i data-lucide="volume-2" id="iconSound" style="width:16px;height:16px;"></i>
                    <span id="textSound">Audio On</span>
                </button>

                <!-- Filter Toko Mitra (Opsional) -->
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-white"><i data-lucide="store" style="width:14px;height:14px;"></i></span>
                    <select class="form-select form-select-sm" id="selectStoreFilter" style="max-width: 190px;">
                        <option value="">Semua Toko Mitra</option>
                        @foreach($stores as $st)
                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanner Target & Input Station -->
    <div class="scan-station-box mb-4" id="scanStationBox">
        <div class="laser-scanline" id="laserScanline"></div>
        <div class="row align-items-stretch g-3">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="barcodeTerminalInput" class="form-label fw-bold small text-uppercase mb-0" style="letter-spacing: 0.5px; color: var(--text-muted, #786C60);">
                        <i data-lucide="barcode" style="width: 15px; height: 15px;" class="me-1"></i>
                        Scan barcode produk
                    </label>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.72rem;">
                        <span class="pulse-led d-inline-block me-1" style="width:6px;height:6px;vertical-align:1px;"></span> Siap scan
                    </span>
                </div>
                <div class="scan-input-wrapper">
                    <i data-lucide="scan" class="input-icon" style="width: 22px; height: 22px;"></i>
                    <input 
                        type="text" 
                        id="barcodeTerminalInput" 
                        class="form-control" 
                        autocomplete="off" 
                        autofocus
                    >
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-center gap-2 p-3 h-100" style="background: var(--bg-card, #ffffff); border: 1px solid var(--border-color, #E2E8F0); border-radius: var(--radius-sm, 8px);">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">Barcode terakhir:</span>
                        <span class="badge bg-light text-dark font-monospace" id="scannerLastCode">-</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">Waktu scan:</span>
                        <span class="fw-semibold small font-monospace" id="scannerLastTime">-</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">Total batch:</span>
                        <span class="badge bg-light text-muted border">{{ $totalBatches }} Batch</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Area: Product Information Display -->
    <div id="productResultContainer" style="display: none;" class="mb-4">
        <div class="product-kiosk-card">
            <!-- Header Kiosk Card -->
            <div class="product-kiosk-header">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge-category" id="resCategory">ROBUSTA</span>
                            <span class="badge bg-white text-dark small" id="resStoreName"><i data-lucide="store" style="width:12px;height:12px;" class="me-1"></i>Pusat Roastery</span>
                        </div>
                        <h2 class="coffee-title" id="resCoffeeName">Nama Varian Kopi</h2>
                        <div class="d-flex flex-wrap align-items-center gap-3 text-white-50 small font-monospace mt-2">
                            <div>
                                <span class="text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Kode produksi:</span> 
                                <span class="badge bg-warning text-dark fw-bold px-2 py-1" id="resKodeProduksi">-</span>
                            </div>
                            <div class="border-start ps-3 border-secondary">
                                <span class="text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Barcode:</span> 
                                <span class="fw-bold text-white fs-6" id="resBarcodeVal">-</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="freshness-pill fresh" id="resFreshnessPill">
                            <i data-lucide="check-circle" id="resFreshnessIcon" style="width: 20px; height: 20px;"></i>
                            <div>
                                <div class="fw-bold" id="resFreshnessLabel">SEGAR & AMAN</div>
                                <small style="font-size: 0.75rem; opacity: 0.9;" id="resFreshnessDesc">Tersisa 90 hari lagi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body Kiosk Card -->
            <div class="p-4">
                <div class="row g-4 align-items-stretch">
                    <!-- Column 1: Harga Jual Resmi Toko -->
                    <div class="col-lg-4 col-md-6">
                        <div class="price-kiosk-display h-100 d-flex flex-column justify-content-center">
                            <div class="price-kiosk-label">Harga jual resmi toko</div>
                            <div class="price-kiosk-value" id="resHargaJual">Rp 0</div>
                            <div class="mt-2 text-muted small">
                                Sesuai master harga di toko mitra tersebut
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Parameter Tanggal & Kesegaran -->
                    <div class="col-lg-4 col-md-6">
                        <div class="p-3 h-100" style="background: var(--bg-input, #FBF7F0); border: 1px solid var(--border-color, #E8DFD5); border-radius: var(--radius-sm, 8px);">
                            <div class="text-uppercase fw-bold text-muted small mb-3" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <i data-lucide="calendar" style="width: 14px; height: 14px;" class="me-1 text-accent"></i>
                                Informasi siklus masa simpan
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted small">Tanggal kedaluwarsa:</span>
                                <span class="fw-bold font-monospace text-dark fs-6" id="resTglExp">-</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted small">Tanggal masuk stok:</span>
                                <span class="fw-medium font-monospace text-dark small" id="resTglStock">-</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Hitungan hari:</span>
                                <span class="badge" id="resDaysBadge" style="font-size: 0.75rem;">- Hari</span>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Sisa Stok Fisik & Barcode SVG -->
                    <div class="col-lg-4 col-md-12">
                        <div class="p-3 h-100 d-flex flex-column justify-content-between" style="background: var(--bg-card, #ffffff); border: 1px solid var(--border-color, #E8DFD5); border-radius: var(--radius-sm, 8px);">
                            <div>
                                <div class="text-uppercase fw-bold text-muted small mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <i data-lucide="package" style="width: 14px; height: 14px;" class="me-1 text-accent"></i>
                                    Status sisa stok fisik
                                </div>
                                <div class="d-flex align-items-baseline gap-2 mb-1">
                                    <span class="fs-2 fw-bold font-monospace" id="resSisaStok" style="color: var(--text-main, #2C1E14);">0</span>
                                    <span class="text-muted small">Pcs Tersedia</span>
                                    <span class="badge ms-auto" id="resStockStatusBadge">Aman</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-success" id="resStockProgressBar" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>

                            <!-- Barcode Visualizer -->
                            <div class="barcode-render-box">
                                <svg id="resBarcodeSvg"></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Operations Toolbar for Scanned Batch -->
            <div class="p-3 border-top bg-light d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-bottom-left-radius: var(--radius-sm, 8px); border-bottom-right-radius: var(--radius-sm, 8px);">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-semibold">Aksi cepat:</span>
                    <a href="#" id="btnScanGoToStock" class="btn btn-sm btn-outline-modern" target="_blank">
                        Kelola di stok batch
                    </a>
                    <button type="button" id="btnScanPrintLabel" class="btn btn-sm btn-outline-secondary">
                        Cetak stiker barcode
                    </button>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btnScanRecordSale" class="btn btn-sm btn-accent fw-bold px-3">
                        +1 Pack terjual
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State Placeholder (Before Any Scan) -->
    <div id="productEmptyPlaceholder" class="product-kiosk-card kiosk-empty-state mb-4 py-4 text-center">
        <div class="kiosk-empty-icon mb-2">
            <i data-lucide="scan-barcode" style="width: 28px; height: 28px;"></i>
        </div>
        <div class="fw-semibold text-muted small">Menunggu pemindaian barcode...</div>
    </div>

    <!-- Recent Session Scan History Table -->
    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: var(--radius-sm, 8px);">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="history" style="width: 18px; height: 18px; color: var(--accent, #C88A4E);"></i>
                <h6 class="fw-bold mb-0">Riwayat pemindaian sesi ini</h6>
                <span class="badge bg-light text-dark rounded-pill ms-2" id="badgeHistoryCount">0 Item</span>
            </div>
            
            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" id="btnClearHistory">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                <span>Bersihkan riwayat</span>
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-scan-history mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 85px;">Waktu</th>
                        <th style="min-width: 140px;">Kode produksi</th>
                        <th style="min-width: 140px;">Barcode</th>
                        <th style="min-width: 160px;">Varian kopi</th>
                        <th style="min-width: 130px;">Toko mitra</th>
                        <th style="min-width: 110px;">Tgl kedaluwarsa</th>
                        <th style="min-width: 120px;">Status</th>
                        <th class="text-end" style="min-width: 110px;">Harga jual</th>
                    </tr>
                </thead>
                <tbody id="tableScanHistoryBody">
                    <tr id="emptyHistoryRow">
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i data-lucide="scan-barcode" style="width: 28px; height: 28px; opacity: 0.35;" class="mb-2 d-block mx-auto"></i>
                            <div class="fw-semibold text-secondary small">Belum ada riwayat pemindaian</div>
                            <small class="text-muted" style="font-size:0.75rem;">Scan barcode kemasan untuk melihat detail produk.</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<!-- JsBarcode CDN for Crisp Barcode Rendering -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scannerInput = document.getElementById('barcodeTerminalInput');
    const scanStationBox = document.getElementById('scanStationBox');
    const laserScanline = document.getElementById('laserScanline');
    const btnClearInput = document.getElementById('btnClearInput');
    const storeFilter = document.getElementById('selectStoreFilter');
    const btnToggleSound = document.getElementById('btnToggleSound');
    const iconSound = document.getElementById('iconSound');
    const textSound = document.getElementById('textSound');

    // UI Result Elements
    const productResultContainer = document.getElementById('productResultContainer');
    const productEmptyPlaceholder = document.getElementById('productEmptyPlaceholder');
    const resCoffeeName = document.getElementById('resCoffeeName');
    const resCategory = document.getElementById('resCategory');
    const resStoreName = document.getElementById('resStoreName');
    const resKodeProduksi = document.getElementById('resKodeProduksi');
    const resHargaJual = document.getElementById('resHargaJual');
    const resTglExp = document.getElementById('resTglExp');
    const resTglStock = document.getElementById('resTglStock');
    const resDaysBadge = document.getElementById('resDaysBadge');
    const resFreshnessPill = document.getElementById('resFreshnessPill');
    const resFreshnessIcon = document.getElementById('resFreshnessIcon');
    const resFreshnessLabel = document.getElementById('resFreshnessLabel');
    const resFreshnessDesc = document.getElementById('resFreshnessDesc');
    const resSisaStok = document.getElementById('resSisaStok');
    const resStockStatusBadge = document.getElementById('resStockStatusBadge');
    const resStockProgressBar = document.getElementById('resStockProgressBar');
    const resBarcodeSvg = document.getElementById('resBarcodeSvg');

    const scannerLastCode = document.getElementById('scannerLastCode');
    const scannerLastTime = document.getElementById('scannerLastTime');
    const badgeHistoryCount = document.getElementById('badgeHistoryCount');
    const tableScanHistoryBody = document.getElementById('tableScanHistoryBody');
    const emptyHistoryRow = document.getElementById('emptyHistoryRow');
    const btnClearHistory = document.getElementById('btnClearHistory');

    let isSoundEnabled = localStorage.getItem('scanner_sound_enabled') !== 'false';
    let scanHistory = [];

    // Load initial sound setting
    updateSoundUI();

    btnToggleSound.addEventListener('click', function() {
        isSoundEnabled = !isSoundEnabled;
        localStorage.setItem('scanner_sound_enabled', isSoundEnabled);
        updateSoundUI();
    });

    function updateSoundUI() {
        if (isSoundEnabled) {
            textSound.innerText = 'Audio On';
            btnToggleSound.classList.replace('btn-outline-secondary', 'btn-outline-success');
            iconSound.setAttribute('data-lucide', 'volume-2');
        } else {
            textSound.innerText = 'Audio Mute';
            btnToggleSound.classList.replace('btn-outline-success', 'btn-outline-secondary');
            iconSound.setAttribute('data-lucide', 'volume-x');
        }
        lucide.createIcons();
    }

    // =======================================================
    // Audio Synthesizer (Professional Retail Sound FX)
    // =======================================================
    function playBeep(type = 'success') {
        if (!isSoundEnabled) return;
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();

            if (type === 'success') {
                // High tone clean POS chime
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1046.5, ctx.currentTime); // C6
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.12);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.12);
            } else if (type === 'warning') {
                // Double chime for expiring / low stock
                [659.25, 880].forEach((freq, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + (i * 0.1));
                    gain.gain.setValueAtTime(0.2, ctx.currentTime + (i * 0.1));
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + (i * 0.1) + 0.12);
                    osc.start(ctx.currentTime + (i * 0.1));
                    osc.stop(ctx.currentTime + (i * 0.1) + 0.12);
                });
            } else {
                // Low buzz error
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, ctx.currentTime);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.22);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.22);
            }
        } catch (e) {
            // Audio policy fallback
        }
    }

    // =======================================================
    // Auto-Focus Daemon for Hardware Barcode Scanner
    // =======================================================
    function ensureScannerFocus() {
        if (!scannerInput) return;
        const active = document.activeElement;
        // Jangan paksa fokus jika user sedang sengaja memilih dropdown toko atau modal
        if (active && (active.tagName === 'SELECT' || active.tagName === 'TEXTAREA' || active.id === 'searchInput')) {
            return;
        }
        if (active !== scannerInput) {
            scannerInput.focus();
        }
    }

    // Keep focus locked every 800ms
    setInterval(ensureScannerFocus, 800);
    ensureScannerFocus();

    // =======================================================
    // Global Scanner Hook (Priority 1 in layouts/app.blade.php)
    // =======================================================
    window.handleScannedBarcodeGlobal = function(code) {
        if (!code) return;
        scannerInput.value = code;
        executeLookup(code);
    };

    // Form input keyboard listener (direct Enter from barcode scanner or typing)
    scannerInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value.trim();
            if (val) executeLookup(val);
        } else if (e.key === 'Escape') {
            clearInput();
        }
    });

    if (btnClearInput) {
        btnClearInput.addEventListener('click', clearInput);
    }

    function clearInput() {
        scannerInput.value = '';
        scannerInput.focus();
    }

    // =======================================================
    // Lookup Execution via AJAX
    // =======================================================
    let isProcessing = false;

    function executeLookup(barcode) {
        if (isProcessing || !barcode) return;
        isProcessing = true;

        // Visual animation
        scanStationBox.classList.add('is-active');
        laserScanline.classList.add('scanning');

        const storeId = storeFilter.value;

        fetch('{{ route("scanner.lookup") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                code: barcode,
                store_id: storeId || null
            })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            isProcessing = false;
            laserScanline.classList.remove('scanning');
            setTimeout(() => scanStationBox.classList.remove('is-active'), 400);

            if (status === 200 && body.success) {
                renderProductResult(body.data);
                addToHistory(body.data);

                if (body.data.freshness_status === 'expired') {
                    playBeep('warning');
                } else if (body.data.freshness_status === 'warning') {
                    playBeep('warning');
                } else {
                    playBeep('success');
                }
            } else {
                playBeep('error');
                showNotFoundToast(body.message || 'Barcode tidak ditemukan.');
            }

            // Clear input and refocus ready for next scan
            scannerInput.value = '';
            scannerInput.focus();
        })
        .catch(err => {
            isProcessing = false;
            laserScanline.classList.remove('scanning');
            scanStationBox.classList.remove('is-active');
            playBeep('error');
            showNotFoundToast('Terjadi kesalahan koneksi server.');
            scannerInput.value = '';
            scannerInput.focus();
        });
    }

    // =======================================================
    // Render Scanned Product
    // =======================================================
    function renderProductResult(data) {
        productEmptyPlaceholder.style.display = 'none';
        productResultContainer.style.display = 'block';

        // Update Header
        resCoffeeName.innerText = data.coffee_name;
        resCategory.innerText = data.coffee_category;
        resStoreName.innerHTML = '<i data-lucide="store" style="width:12px;height:12px;" class="me-1"></i>' + data.store_name;
        resKodeProduksi.innerText = data.kode_produksi || '-';
        const elBarcode = document.getElementById('resBarcodeVal');
        if (elBarcode) elBarcode.innerText = data.barcode || '-';

        // Update Harga
        resHargaJual.innerText = data.harga_jual_formatted;

        // Update Expiry & Freshness
        resTglExp.innerText = data.tgl_exp;
        resTglStock.innerText = data.tgl_stock;

        resFreshnessPill.className = 'freshness-pill ' + data.freshness_class;
        resFreshnessLabel.innerText = data.freshness_label;
        resFreshnessDesc.innerText = data.freshness_desc;

        if (data.freshness_status === 'fresh') {
            resFreshnessIcon.setAttribute('data-lucide', 'check-circle');
            resDaysBadge.className = 'badge bg-success';
            resDaysBadge.innerText = 'Sisa ' + data.days_remaining + ' Hari';
        } else if (data.freshness_status === 'warning') {
            resFreshnessIcon.setAttribute('data-lucide', 'alert-triangle');
            resDaysBadge.className = 'badge bg-warning text-dark';
            resDaysBadge.innerText = data.days_remaining + ' Hari Lagi';
        } else {
            resFreshnessIcon.setAttribute('data-lucide', 'alert-octagon');
            resDaysBadge.className = 'badge bg-danger';
            resDaysBadge.innerText = 'Expired ' + Math.abs(data.days_remaining) + ' Hari Lalu';
        }

        // Update Sisa Stok
        resSisaStok.innerText = data.sisa;
        const pct = data.jumlah_stock > 0 ? Math.min(100, Math.round((data.sisa / data.jumlah_stock) * 100)) : 0;
        resStockProgressBar.style.width = pct + '%';

        if (data.is_out_of_stock) {
            resStockStatusBadge.className = 'badge bg-danger';
            resStockStatusBadge.innerText = 'Stok Habis (0)';
            resStockProgressBar.className = 'progress-bar bg-danger';
        } else if (data.is_low_stock) {
            resStockStatusBadge.className = 'badge bg-warning text-dark';
            resStockStatusBadge.innerText = 'Stok Kritis';
            resStockProgressBar.className = 'progress-bar bg-warning';
        } else {
            resStockStatusBadge.className = 'badge bg-success';
            resStockStatusBadge.innerText = 'Stok Tersedia';
            resStockProgressBar.className = 'progress-bar bg-success';
        }

        // Render Barcode SVG via JsBarcode (Prioritas EAN-13 Barcode)
        try {
            const barcodeVal = data.barcode || data.kode_produksi;
            if (/^\d{13}$/.test(barcodeVal)) {
                JsBarcode(resBarcodeSvg, barcodeVal, {
                    format: 'EAN13',
                    width: 2,
                    height: 54,
                    displayValue: true,
                    fontSize: 14,
                    font: 'monospace',
                    margin: 8
                });
            } else {
                JsBarcode(resBarcodeSvg, barcodeVal, {
                    format: 'CODE128',
                    width: 2,
                    height: 54,
                    displayValue: true,
                    fontSize: 14,
                    font: 'monospace',
                    margin: 8
                });
            }
        } catch (e) {
            console.warn('Barcode render fallback:', e);
        }

        // Update Console Info
        scannerLastCode.innerText = data.barcode ? `${data.barcode} (${data.kode_produksi})` : data.kode_produksi;
        scannerLastTime.innerText = data.scanned_at;

        // Wire up Quick Operations Toolbar
        const currentBatchId = data.id;
        const btnGoStock = document.getElementById('btnScanGoToStock');
        if (btnGoStock) {
            btnGoStock.href = `/stock?search=${encodeURIComponent(data.kode_produksi)}`;
        }

        const btnPrint = document.getElementById('btnScanPrintLabel');
        if (btnPrint) {
            btnPrint.onclick = function() {
                const svgContent = resBarcodeSvg.outerHTML;
                const printWindow = window.open('', '_blank', 'width=420,height=300');
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Print Label - ${data.kode_produksi}</title>
                        <style>
                            @page { size: auto; margin: 4mm; }
                            body { font-family: sans-serif; text-align: center; margin: 0; padding: 5px; }
                            .title { font-weight: bold; font-size: 13px; margin-bottom: 2px; }
                            .sub { font-size: 10px; color: #555; margin-bottom: 4px; }
                            svg { max-width: 100%; height: auto; display: block; margin: 0 auto; }
                        </style>
                    </head>
                    <body>
                        <div class="title">${data.coffee_name} (${data.coffee_category})</div>
                        <div class="sub">Toko: ${data.store_name} | Exp: ${data.tgl_exp}</div>
                        ${svgContent}
                        <script>
                            window.onload = function() { window.focus(); window.print(); window.close(); };
                        <\/script>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            };
        }

        const btnRecordSale = document.getElementById('btnScanRecordSale');
        if (btnRecordSale) {
            if (data.sisa <= 0) {
                btnRecordSale.disabled = true;
                btnRecordSale.innerText = 'Stok Habis (0)';
                btnRecordSale.className = 'btn btn-sm btn-secondary fw-bold px-3';
            } else {
                btnRecordSale.disabled = false;
                btnRecordSale.innerText = '+1 Pack Terjual';
                btnRecordSale.className = 'btn btn-sm btn-accent fw-bold px-3';
                btnRecordSale.onclick = function() {
                    btnRecordSale.disabled = true;
                    btnRecordSale.innerText = 'Menyimpan...';

                    fetch("{{ route('scanner.record-sale') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ batch_id: currentBatchId })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            data.sisa = res.new_sisa;
                            data.laku = res.new_laku;
                            resSisaStok.innerText = res.new_sisa;
                            showNotFoundToast(res.message);
                            playBeep('success');
                            renderProductResult(data);
                        } else {
                            alert(res.message || 'Gagal mencatat penjualan.');
                            btnRecordSale.disabled = false;
                            btnRecordSale.innerText = '+1 Pack Terjual';
                        }
                    })
                    .catch(() => {
                        alert('Terjadi kesalahan jaringan.');
                        btnRecordSale.disabled = false;
                        btnRecordSale.innerText = '+1 Pack Terjual';
                    });
                };
            }
        }

        lucide.createIcons();
    }

    // =======================================================
    // Session Scan History Manager
    // =======================================================
    function addToHistory(item) {
        // Prepend to array
        scanHistory.unshift(item);
        if (scanHistory.length > 25) scanHistory.pop();
        renderHistoryTable();
    }

    function renderHistoryTable() {
        if (!scanHistory.length) {
            tableScanHistoryBody.innerHTML = '<tr id="emptyHistoryRow"><td colspan="8" class="text-center py-5 text-muted"><i data-lucide="scan-barcode" style="width: 28px; height: 28px; opacity: 0.35;" class="mb-2 d-block mx-auto"></i><div class="fw-semibold text-secondary small">Belum Ada Riwayat Pemindaian</div><small class="text-muted" style="font-size:0.75rem;">Scan barcode kemasan untuk melihat detail produk.</small></td></tr>';
            badgeHistoryCount.innerText = '0 Item';
            lucide.createIcons();
            return;
        }

        badgeHistoryCount.innerText = scanHistory.length + ' Item';

        let html = '';
        scanHistory.forEach(it => {
            let badgeClass = 'bg-success';
            if (it.freshness_status === 'warning') badgeClass = 'bg-warning text-dark';
            if (it.freshness_status === 'expired') badgeClass = 'bg-danger';

            html += `
                <tr>
                    <td><span class="text-muted font-monospace small">${it.scanned_at}</span></td>
                    <td><span class="badge bg-warning text-dark font-monospace">${it.kode_produksi}</span></td>
                    <td><span class="fw-bold font-monospace text-dark">${it.barcode || '-'}</span></td>
                    <td>
                        <div class="fw-bold">${it.coffee_name}</div>
                        <small class="text-muted">${it.coffee_category}</small>
                    </td>
                    <td><span class="small">${it.store_name}</span></td>
                    <td><span class="font-monospace small">${it.tgl_exp}</span></td>
                    <td><span class="badge ${badgeClass}">${it.freshness_label}</span></td>
                    <td class="text-end fw-bold font-monospace text-accent">${it.harga_jual_formatted}</td>
                </tr>
            `;
        });

        tableScanHistoryBody.innerHTML = html;
    }

    btnClearHistory.addEventListener('click', function() {
        scanHistory = [];
        renderHistoryTable();
    });

    // Alert toast
    function showNotFoundToast(msg) {
        const div = document.createElement('div');
        div.className = 'alert alert-danger alert-dismissible fade show position-fixed bottom-0 end-0 m-4 shadow-lg';
        div.style.zIndex = '9999';
        div.style.maxWidth = '380px';
        div.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="alert-circle" style="width:20px;height:20px;"></i>
                <div>
                    <div class="fw-bold">Peringatan Scanner</div>
                    <small>${msg}</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(div);
        lucide.createIcons();
        setTimeout(() => {
            try {
                const bsAlert = new bootstrap.Alert(div);
                bsAlert.close();
            } catch(e) {
                div.remove();
            }
        }, 4000);
    }
});
</script>
@endsection
