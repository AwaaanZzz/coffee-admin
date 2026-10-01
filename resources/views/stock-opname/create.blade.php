@extends('layouts.app')

@section('title', 'Audit Stok Opname - ' . $store->name)

@section('breadcrumbs')
<a href="{{ route('dashboard') }}">Beranda</a>
<i data-lucide="chevron-right"></i>
<a href="{{ route('stock-opname.index') }}">Stok Opname Mitra</a>
<i data-lucide="chevron-right"></i>
<span>Audit: {{ $store->name }}</span>
@endsection

@section('styles')
<style>
    /* Professional Studio Header */
    .audit-studio-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, #E8DFD5);
        border-radius: var(--radius-lg, 18px);
        padding: 1.75rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.5rem;
    }
    .scanner-live-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(74, 124, 89, 0.12);
        color: var(--success, #4A7C59);
        border: 1px solid rgba(74, 124, 89, 0.25);
        padding: 5px 12px;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .scanner-live-pill .laser-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseLaser 1.6s infinite;
    }
    @keyframes pulseLaser {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .scanner-console-box {
        background: #ffffff;
        border: 1px solid var(--border, #E8DFD5);
        border-radius: var(--radius, 14px);
        padding: 1.1rem 1.25rem;
    }
    .scanner-input-group {
        position: relative;
        flex: 1;
        min-width: 280px;
    }
    .scanner-input-group input {
        padding-left: 44px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.05rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 12px;
        background: var(--bg-card, #ffffff);
        border: 1.5px solid var(--border, #cbd5e1);
        color: var(--text-primary, #0f172a);
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }
    .scanner-input-group input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        background: #ffffff;
    }
    .scanner-input-group .scanner-icon-badge {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
    }
    .mode-tab-pill {
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 7px 14px;
        border: 1px solid var(--border, #cbd5e1);
        background: var(--bg-card, #ffffff);
        color: var(--text-secondary, #64748b);
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .mode-tab-pill:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }
    .mode-tab-pill.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
    }
    /* Stepper Interactive Controls */
    .stepper-btn-modern {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid var(--border, #cbd5e1);
        background: var(--bg-card, #ffffff);
        color: var(--text-primary, #0f172a);
        cursor: pointer;
        font-weight: bold;
        transition: all 0.15s ease;
    }
    .stepper-btn-modern:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a;
        transform: scale(1.05);
    }
    .stepper-input-modern {
        width: 65px;
        text-align: center;
        font-weight: 700;
        font-size: 1.05rem;
        border-radius: 8px;
        background: #ffffff;
        border: 1.5px solid var(--border, #cbd5e1);
        color: var(--text-primary, #0f172a);
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
        padding: 4px;
    }
    .stepper-input-modern:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    /* Animated highlight flash for scanned row */
    .row-scan-pulse {
        animation: highlightFlash 1.4s ease-out;
    }
    @keyframes highlightFlash {
        0% { background-color: rgba(74, 124, 89, 0.25) !important; transform: scale(1.01); }
        100% { background-color: transparent !important; transform: scale(1); }
    }
    /* Floating Dynamic Bottom Dock */
    .bottom-dock-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 290px);
        right: 0;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(14px);
        border-top: 1px solid var(--border, #E8DFD5);
        padding: 1rem 2rem;
        z-index: 1040;
        box-shadow: 0 -8px 25px rgba(26, 18, 9, 0.08);
        transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    body.sidebar-collapsed .bottom-dock-bar {
        left: var(--sidebar-collapsed, 78px);
    }
    @media (max-width: 991.98px) {
        .bottom-dock-bar {
            left: 0 !important;
            padding: 0.75rem 1rem;
        }
    }
    [data-theme="dark"] .bottom-dock-bar {
        background: rgba(28, 20, 16, 0.96);
        border-top-color: var(--border);
        box-shadow: 0 -8px 25px rgba(0, 0, 0, 0.5);
    }
    /* Dynamic Live Activity Card */
    .scan-live-hud {
        display: none;
        padding: 8px 16px;
        border-radius: 10px;
        background: rgba(74, 124, 89, 0.12);
        border: 1px solid rgba(74, 124, 89, 0.3);
        color: var(--success, #4A7C59);
        font-weight: 600;
        font-size: 0.85rem;
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .scan-live-hud-error {
        display: none;
        padding: 8px 16px;
        border-radius: 10px;
        background: rgba(192, 57, 43, 0.1);
        border: 1px solid rgba(192, 57, 43, 0.3);
        color: var(--danger, #C0392B);
        font-weight: 600;
        font-size: 0.85rem;
        animation: slideDown 0.3s ease-out;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-2 px-md-4">
    <!-- Header Workspace Card -->
    <div class="audit-studio-card">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1.5">
                    <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1.5 px-2.5 py-1" style="font-size: 0.75rem; font-weight: 600;">
                        <span class="laser-dot"></span> Scanner Siap
                    </span>
                </div>
                <h2 class="page-title m-0">
                    {{ $store->name }}
                </h2>
                @if($store->alamat || $store->penanggung_jawab)
                <p class="text-muted small mb-0 mt-1 d-flex align-items-center gap-2 flex-wrap">
                    @if($store->alamat)
                    <span><i data-lucide="map-pin" style="width: 13px; height: 13px; display:inline-block; vertical-align:-1px;"></i> {{ $store->alamat }}</span>
                    @endif
                    @if($store->alamat && $store->penanggung_jawab) &bull; @endif
                    @if($store->penanggung_jawab)
                    <span><i data-lucide="user" style="width: 13px; height: 13px; display:inline-block; vertical-align:-1px;"></i> PJ: <strong>{{ $store->penanggung_jawab }}</strong></span>
                    @endif
                </p>
                @endif
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-outline-modern d-flex align-items-center gap-2" id="openCameraBtn">
                    <i data-lucide="camera" style="width: 16px; height: 16px; color: var(--accent);"></i>
                    <span>Scan Kamera HP</span>
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-modern dropdown-toggle d-flex align-items-center gap-1.5" data-bs-toggle="dropdown">
                        <i data-lucide="sliders" style="width: 15px; height: 15px;"></i> Shortcut
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="setMode('zero')"><i data-lucide="scan" class="icon-sm me-2 text-primary"></i> Mulai Scan Rak (Semua Fisik = 0)</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="setMode('system')"><i data-lucide="check-check" class="icon-sm me-2 text-success"></i> Samakan Fisik = Stok Sistem (0 Terjual)</a></li>
                    </ul>
                </div>
                <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-modern">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Scanner Console Box -->
        <div class="scanner-console-box">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2.5">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="small fw-semibold text-secondary" style="font-size: 0.8rem;">Metode Hitung:</span>
                    <button type="button" id="btnModeZero" class="mode-tab-pill active" onclick="setMode('zero')">
                        <i data-lucide="scan" style="width: 14px; height: 14px;"></i>
                        <span>Scan Rak Fisik</span>
                    </button>
                    <button type="button" id="btnModeSystem" class="mode-tab-pill" onclick="setMode('system')">
                        <i data-lucide="check-check" style="width: 14px; height: 14px;"></i>
                        <span>Samakan Stok</span>
                    </button>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="scanner-input-group w-100">
                    <i data-lucide="barcode" class="scanner-icon-badge" style="width: 20px; height: 20px;"></i>
                    <input type="text" id="barcodeGunInput" class="form-control" placeholder="Scan barcode kemasan atau ketik barcode produk..." autofocus autocomplete="off">
                </div>
            </div>

            <!-- Dynamic Scan HUD Activity Feedback -->
            <div id="scanFeedBack" class="scan-live-hud mt-2.5">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                    <span id="scanFeedbackText"></span>
                </div>
            </div>
            <div id="scanFeedBackError" class="scan-live-hud-error mt-2.5">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
                    <span id="scanFeedbackErrorText"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Table Form -->
    <form action="{{ route('stock-opname.submit', $store->id) }}" method="POST" id="auditForm">
        @csrf
        <div class="card-modern mb-5" style="margin-bottom: 7.5rem !important;">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h5 class="card-title-modern m-0">Daftar batch stok konsinyasi toko</h5>
                <span class="badge-modern badge-neutral">{{ count($auditItems) }} batch aktif</span>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table-modern w-100 m-0 align-middle" id="auditTable">
                        <thead>
                            <tr>
                                <th style="width: 45px; padding-left: 20px;" class="text-center">#</th>
                                <th style="min-width: 140px;">Barcode & batch</th>
                                <th>Jenis kopi</th>
                                <th class="text-center">Tgl. kedaluwarsa</th>
                                <th class="text-end">Stok sistem</th>
                                <th class="text-center" style="min-width: 175px;">Fisik ditemukan di rak</th>
                                <th class="text-end">Terjual otomatis</th>
                                <th class="text-end">Harga satuan</th>
                                <th class="text-end" style="padding-right: 20px;">Subtotal laku</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditItems as $index => $item)
                            <tr id="row-{{ $item['batch_id'] }}" data-barcode="{{ strtolower($item['barcode'] ?? '') }}" data-kode="{{ strtolower($item['kode_produksi']) }}" data-batch-id="{{ $item['batch_id'] }}">
                                <td class="text-center text-muted small" style="padding-left: 20px;">{{ $index + 1 }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][batch_id]" value="{{ $item['batch_id'] }}">
                                    @if(!empty($item['barcode']))
                                        <div class="font-monospace fw-bold" style="color: #0f172a; font-size: 0.88rem;">{{ $item['barcode'] }}</div>
                                    @endif
                                    <div class="font-monospace text-muted small" style="font-size: 0.74rem;">{{ $item['kode_produksi'] }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $item['coffee_name'] }}
                                    </div>
                                    @php $opCat = strtolower($item['category'] ?? ''); @endphp
                                    @if($opCat === 'robusta')
                                        <span class="badge-robusta" style="font-size: 0.68rem;">Robusta</span>
                                    @elseif($opCat === 'arabika')
                                        <span class="badge-arabika" style="font-size: 0.68rem;">Arabika</span>
                                    @else
                                        <span class="badge-neutral" style="font-size: 0.68rem;">{{ ucfirst($item['category']) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="tabular-nums small {{ $item['is_expired'] ? 'text-danger fw-bold' : ($item['is_expiring_soon'] ? 'text-warning fw-bold' : 'text-muted') }}">
                                        {{ $item['tgl_exp'] }}
                                    </span>
                                    @if($item['is_expired'])
                                        <div><span class="badge-modern badge-danger" style="font-size: 0.65rem;">Kedaluwarsa</span></div>
                                    @elseif($item['is_expiring_soon'])
                                        <div><span class="badge-modern badge-warning" style="font-size: 0.65rem;">Segera kedaluwarsa</span></div>
                                    @endif
                                </td>
                                <td class="text-end tabular-nums">
                                    <span class="badge-modern badge-neutral px-2.5 py-1.5 font-monospace" id="stok-sistem-{{ $item['batch_id'] }}">
                                        {{ $item['stok_sistem'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <button type="button" class="stepper-btn-modern" onclick="stepItem({{ $item['batch_id'] }}, -1)">
                                            <i data-lucide="minus" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <input type="number" 
                                               name="items[{{ $index }}][fisik_terhitung]" 
                                               id="fisik-input-{{ $item['batch_id'] }}" 
                                               class="stepper-input-modern form-control form-control-sm text-center tabular-nums" 
                                               value="0" 
                                               min="0" 
                                               data-sistem="{{ $item['stok_sistem'] }}"
                                               data-price="{{ $item['harga_satuan'] }}"
                                               oninput="recalculateRow({{ $item['batch_id'] }})">
                                        <button type="button" class="stepper-btn-modern" onclick="stepItem({{ $item['batch_id'] }}, 1)">
                                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-end tabular-nums">
                                    <span class="badge-modern badge-neutral px-2.5 py-1" id="selisih-badge-{{ $item['batch_id'] }}">
                                        0 pcs
                                    </span>
                                </td>
                                <td class="text-end tabular-nums font-monospace text-muted">
                                    Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}
                                </td>
                                <td class="text-end tabular-nums font-monospace fw-semibold" style="padding-right: 20px;" id="subtotal-{{ $item['batch_id'] }}">
                                    Rp 0
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i data-lucide="package-x" style="width: 44px; height: 44px; opacity: 0.35;" class="mb-2"></i>
                                    <h6>Tidak Ada Stok Aktif di Toko Ini</h6>
                                    <p class="small mb-0">Toko mitra ini belum memiliki batch stok kopi konsinyasi yang tersisa.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sticky Floating Bottom Summary Dock -->
        <div class="bottom-dock-bar">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon">
                            <i data-lucide="boxes" style="width:18px;height:18px;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Total fisik di rak</div>
                            <div class="fs-5 fw-bold text-dark"><span id="sumTotalFisik" class="tabular-nums">0</span> <small class="text-muted fw-normal fs-6">pcs</small></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon">
                            <i data-lucide="shopping-cart" style="width:18px;height:18px;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Terjual otomatis</div>
                            <div class="fs-5 fw-bold text-dark"><span id="sumTotalTerjual" class="tabular-nums">0</span> <small class="text-muted fw-normal fs-6">pcs</small></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div>
                        <div class="stat-label">Total setoran toko</div>
                        <div class="fs-4 fw-bold font-monospace text-dark tabular-nums" id="sumTotalNilai">Rp 0</div>
                    </div>
                </div>
                <div class="col-12 col-md-3 text-md-end">
                    @if(count($auditItems) > 0)
                    <button type="button" class="btn btn-accent btn-lg w-100 fw-semibold d-flex align-items-center justify-content-center gap-2" onclick="confirmFinishAudit()">
                        <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                        <span>Selesaikan audit</span>
                    </button>
                    @else
                    <button type="button" class="btn btn-outline-modern btn-lg w-100 fw-semibold disabled" disabled>
                        <span>Tidak ada item</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Confirm Submit -->
        <div class="modal fade" id="confirmAuditModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="color: var(--text-primary, #1E3A5F);">
                            <i data-lucide="clipboard-check" class="text-success"></i> Konfirmasi Selesaikan Audit
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">Sistem akan melakukan kalkulasi final, memotong sisa stok batch, dan mencatat transaksi penjualan:</p>
                        
                        <div class="p-3 rounded-3 mb-3" style="background: var(--bg-input, #FBF7F0); border: 1px solid var(--border, #E8DFD5);">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Toko Mitra:</span>
                                <strong style="color: var(--text-primary, #1E3A5F);">{{ $store->name }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Total Fisik Tersisa di Rak:</span>
                                <strong class="text-info" id="modalFisik">0 pcs</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Kopi Terjual (Laku):</span>
                                <strong class="text-warning" id="modalTerjual">0 pcs</strong>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <span class="fw-bold" style="color: var(--text-primary, #1E3A5F);">Total Uang Setoran:</span>
                                <strong class="fs-5 font-monospace" style="color: var(--success, #4A7C59);" id="modalTagihan">Rp 0</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Catatan Hasil Audit (Opsional):</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Kondisi rak rapi, kemasan utuh, pembayaran setoran tunai."></textarea>
                        </div>

                        <div class="alert alert-info small mb-0 py-2">
                            <i data-lucide="info" class="icon-sm me-1"></i> Data penjualan dan mutasi stok akan langsung tercatat secara otomatis setelah konfirmasi.
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-modern" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-accent fw-bold px-4">
                            Ya, Simpan & Terbitkan Berita Acara
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Mobile Camera Scanner -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="color: var(--text-primary, #1E3A5F);">
                    <i data-lucide="camera" class="text-accent"></i> Scanner Kamera HP
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeCameraModalBtn"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <div id="cameraPreview" style="width: 100%; min-height: 280px; background: #000; border-radius: 12px; overflow: hidden;"></div>
                <div class="mt-3 text-muted small">
                    <i data-lucide="help-circle" class="icon-sm me-1"></i> Arahkan kamera smartphone ke barcode kemasan kopi. Pastikan pencahayaan cukup.
                </div>
                <div id="cameraLastScan" class="mt-2 text-success fw-bold font-monospace" style="min-height: 24px;"></div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-modern w-100" data-bs-dismiss="modal" id="stopCameraBtn">Tutup Kamera</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Include HTML5-QRCode for Camera Scanning --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    // Audit Items Data Reference
    const auditData = @json($auditItems);
    let html5QrCode = null;
    let currentMode = 'zero'; // 'zero' = scan from 0, 'system' = start from system stock

    // Expose global handler for app.blade.php barcode engine
    window.handleScannedBarcodeGlobal = function(barcode) {
        handleScannedBarcode(barcode);
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Initial setup based on default mode ('zero')
        setMode('zero');

        // Barcode Gun Input Listener
        const barcodeInput = document.getElementById('barcodeGunInput');
        if (barcodeInput) {
            barcodeInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                    const code = this.value.trim();
                    if (code) {
                        handleScannedBarcode(code);
                        this.value = '';
                    }
                }
            });

            // Focus on barcode input when clicking non-interactive elements
            document.addEventListener('click', function(e) {
                const interactiveTags = ['INPUT', 'TEXTAREA', 'BUTTON', 'A', 'SELECT', 'SVG', 'PATH', 'POLYLINE', 'LINE'];
                if (!interactiveTags.includes(e.target.tagName) && !e.target.closest('button, a, input, textarea')) {
                    barcodeInput.focus();
                }
            });
        }

        // Camera Scanner Modal logic
        const openCameraBtn = document.getElementById('openCameraBtn');
        const cameraModalEl = document.getElementById('cameraModal');
        const cameraModal = new bootstrap.Modal(cameraModalEl);

        openCameraBtn.addEventListener('click', function() {
            cameraModal.show();
            startCamera();
        });

        cameraModalEl.addEventListener('hidden.bs.modal', function() {
            stopCamera();
        });
    });

    // Handle scanned barcode (From gun or camera)
    function handleScannedBarcode(barcode) {
        if (!barcode) return;
        const cleanCode = barcode.trim().replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
        
        // Find matching item in auditData (Toleran terhadap barcode EAN-13 atau kode_produksi)
        const matched = auditData.find(item => {
            const itemBarcode = (item.barcode || '').trim().replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
            const itemCode = (item.kode_produksi || '').trim().replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
            return itemBarcode === cleanCode || itemCode === cleanCode;
        });

        if (matched) {
            // Play pleasant audio beep
            if (typeof playRetailBeep === 'function') {
                playRetailBeep(true);
            }

            // Increment fisik count
            const input = document.getElementById(`fisik-input-${matched.batch_id}`);
            if (input) {
                let currentVal = parseInt(input.value) || 0;
                input.value = currentVal + 1;
                recalculateRow(matched.batch_id);

                // Highlight Row with smooth pulse
                const row = document.getElementById(`row-${matched.batch_id}`);
                if (row) {
                    row.classList.remove('row-scan-pulse');
                    void row.offsetWidth; // trigger reflow
                    row.classList.add('row-scan-pulse');
                    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                // Show dynamic activity feedback banner
                const sistem = parseInt(input.dataset.sistem) || 0;
                const terjual = Math.max(0, sistem - parseInt(input.value));
                showScanFeedback(`Scan Berhasil: <strong>${matched.barcode || matched.kode_produksi}</strong> (${matched.coffee_name}) &rarr; Fisik di rak: <strong>${input.value} pcs</strong> | Terjual Otomatis: <strong>${terjual} pcs</strong>`);
            }
        } else {
            // Non-blocking toast alert
            showScanError(`Barcode <strong>"${barcode}"</strong> tidak cocok dengan batch aktif toko ini!`);
        }
    }

    function showScanFeedback(htmlMsg) {
        const feedError = document.getElementById('scanFeedBackError');
        feedError.style.display = 'none';

        const feed = document.getElementById('scanFeedBack');
        const feedText = document.getElementById('scanFeedbackText');
        feedText.innerHTML = htmlMsg;
        feed.style.display = 'block';
        clearTimeout(window.scanTimeout);
        window.scanTimeout = setTimeout(() => {
            feed.style.display = 'none';
        }, 5000);
    }

    function showScanError(htmlMsg) {
        const feedOk = document.getElementById('scanFeedBack');
        feedOk.style.display = 'none';

        const feedError = document.getElementById('scanFeedBackError');
        const feedErrorText = document.getElementById('scanFeedbackErrorText');
        feedErrorText.innerHTML = htmlMsg;
        feedError.style.display = 'block';
        clearTimeout(window.scanErrorTimeout);
        window.scanErrorTimeout = setTimeout(() => {
            feedError.style.display = 'none';
        }, 5000);
    }

    // Mode Switcher: 'zero' (start from 0) vs 'system' (start from system stock)
    function setMode(mode) {
        currentMode = mode;
        const btnZero = document.getElementById('btnModeZero');
        const btnSystem = document.getElementById('btnModeSystem');
        const desc = document.getElementById('modeDescription');

        if (mode === 'zero') {
            btnZero.classList.add('active');
            btnSystem.classList.remove('active');
            if (desc) desc.innerHTML = 'Fisik rak dimulai dari 0. Setiap scan barcode menambah fisik yang tersisa di rak toko.';

            auditData.forEach(item => {
                const input = document.getElementById(`fisik-input-${item.batch_id}`);
                if (input) {
                    input.value = 0;
                    recalculateRow(item.batch_id);
                }
            });
        } else {
            btnSystem.classList.add('active');
            btnZero.classList.remove('active');
            if (desc) desc.innerHTML = 'Fisik disamakan dengan stok sistem. Anda cukup kurangi atau ubah fisik yang berkurang.';

            auditData.forEach(item => {
                const input = document.getElementById(`fisik-input-${item.batch_id}`);
                if (input) {
                    input.value = input.dataset.sistem;
                    recalculateRow(item.batch_id);
                }
            });
        }
    }

    // Step item plus / minus
    function stepItem(batchId, delta) {
        const input = document.getElementById(`fisik-input-${batchId}`);
        if (input) {
            let current = parseInt(input.value) || 0;
            let newVal = Math.max(0, current + delta);
            input.value = newVal;
            recalculateRow(batchId);
        }
    }

    // Recalculate single row
    function recalculateRow(batchId) {
        const input = document.getElementById(`fisik-input-${batchId}`);
        if (!input) return;

        const sistem = parseInt(input.dataset.sistem) || 0;
        const price = parseFloat(input.dataset.price) || 0;
        const fisik = Math.max(0, parseInt(input.value) || 0);

        // Terjual otomatis = Stok Sistem - Fisik
        const terjual = Math.max(0, sistem - fisik);
        const subtotal = terjual * price;

        const badge = document.getElementById(`selisih-badge-${batchId}`);
        const subtotalEl = document.getElementById(`subtotal-${batchId}`);

        if (terjual > 0) {
            badge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fs-6 font-monospace';
            badge.textContent = `+${terjual} terjual`;
            subtotalEl.className = 'text-end font-monospace fw-bold';
            subtotalEl.style.color = 'var(--success, #4A7C59)';
        } else if (fisik > sistem) {
            const surplus = fisik - sistem;
            badge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 fs-6 font-monospace';
            badge.textContent = `+${surplus} surplus`;
            subtotalEl.className = 'text-end font-monospace text-muted';
            subtotalEl.style.color = '';
        } else {
            badge.className = 'badge bg-light text-muted border px-2.5 py-1.5 fs-6 font-monospace';
            badge.textContent = `0 pcs`;
            subtotalEl.className = 'text-end font-monospace text-muted';
            subtotalEl.style.color = '';
        }

        subtotalEl.textContent = 'Rp ' + formatRupiah(subtotal);

        recalculateAll();
    }

    // Recalculate whole table totals
    function recalculateAll() {
        let totalFisik = 0;
        let totalTerjual = 0;
        let totalNilai = 0;

        auditData.forEach(item => {
            const input = document.getElementById(`fisik-input-${item.batch_id}`);
            if (input) {
                const sistem = parseInt(input.dataset.sistem) || 0;
                const price = parseFloat(input.dataset.price) || 0;
                const fisik = Math.max(0, parseInt(input.value) || 0);
                const terjual = Math.max(0, sistem - fisik);

                totalFisik += fisik;
                totalTerjual += terjual;
                totalNilai += (terjual * price);
            }
        });

        document.getElementById('sumTotalFisik').textContent = totalFisik;
        document.getElementById('sumTotalTerjual').textContent = totalTerjual;
        document.getElementById('sumTotalNilai').textContent = 'Rp ' + formatRupiah(totalNilai);
    }

    // Confirm finish audit modal
    function confirmFinishAudit() {
        document.getElementById('modalFisik').textContent = document.getElementById('sumTotalFisik').textContent + ' pcs';
        document.getElementById('modalTerjual').textContent = document.getElementById('sumTotalTerjual').textContent + ' pcs';
        document.getElementById('modalTagihan').textContent = document.getElementById('sumTotalNilai').textContent;

        const modal = new bootstrap.Modal(document.getElementById('confirmAuditModal'));
        modal.show();
    }

    // Helper Rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Camera scanner functions
    let lastScannedText = '';
    let lastScanTime = 0;

    function startCamera() {
        if (typeof Html5Qrcode === 'undefined') {
            alert('Library scanner kamera sedang dimuat, silakan coba 2 detik lagi.');
            return;
        }

        const previewDiv = document.getElementById('cameraPreview');
        previewDiv.innerHTML = '';

        try {
            const formats = [
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.CODE_39
            ];
            html5QrCode = new Html5Qrcode("cameraPreview", { formatsToSupport: formats, verbose: false });
            const config = { 
                fps: 15, 
                qrbox: { width: 280, height: 140 },
                aspectRatio: 1.777778,
                experimentalFeatures: { useBarCodeDetectorIfSupported: true }
            };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText, decodedResult) => {
                    const now = Date.now();
                    // Debounce repeated scans within 2 seconds
                    if (decodedText === lastScannedText && (now - lastScanTime) < 2000) {
                        return;
                    }
                    lastScannedText = decodedText;
                    lastScanTime = now;

                    document.getElementById('cameraLastScan').textContent = `Terdeteksi: ${decodedText}`;
                    handleScannedBarcode(decodedText);
                },
                (errorMessage) => {
                    // Ignore transient parsing errors while searching
                }
            ).catch((err) => {
                previewDiv.innerHTML = `<div class="p-4 text-warning small">
                    <i data-lucide="alert-triangle" class="icon-sm me-1"></i>
                    Tidak dapat mengakses kamera: <strong>${err}</strong>.<br><br>
                    Jika menggunakan HP via IP jaringan lokal (HTTP), browser memblokir kamera untuk alasan keamanan. Pastikan menggunakan HTTPS atau gunakan <strong>Scanner Gun</strong>.
                </div>`;
                lucide.createIcons();
            });
        } catch (e) {
            previewDiv.innerHTML = `<div class="p-4 text-danger small">Inisialisasi kamera gagal: ${e.message}</div>`;
        }
    }

    function stopCamera() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
            }).catch(err => {
                console.error("Failed to stop camera:", err);
            });
        }
        document.getElementById('cameraLastScan').textContent = '';
    }
</script>
@endsection
