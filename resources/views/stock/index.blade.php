@extends('layouts.app')
@section('title', 'Stock')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Data Stock</span>
    </div>
@endsection

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h3 class="page-title m-0">Kelola Stock & Barcode Produk</h3>
            </div>
            <p class="page-subtitle mt-1 mb-0">Pantau ketersediaan stock, cetak label barcode kemasan, dan atur distribusi ke setiap toko.</p>
        </div>
        <div class="page-actions d-flex gap-2 flex-wrap">
            <a href="{{ route('exports.index') }}" class="btn btn-outline-modern d-flex align-items-center gap-1.5" title="Ekspor laporan stok ke Excel/PDF">
                <i data-lucide="file-spreadsheet"></i> Ekspor data
            </a>
            <a href="{{ route('stock.print-labels') }}{{ request('store_id') ? '?store_id='.request('store_id') : '' }}" target="_blank" class="btn btn-outline-modern" title="Cetak label barcode kemasan">
                <i data-lucide="printer"></i> Cetak semua barcode
            </a>
            <a href="{{ route('stock.create') }}" class="btn btn-accent">
                <i data-lucide="plus"></i> Tambah stok
            </a>
        </div>
    </div>


    {{-- Unified Control Toolbar (Compact & Professional) --}}
    <div class="card-modern mb-4 p-3" style="background: var(--bg-card); border: 1px solid var(--border);">
        <form method="GET" class="row g-2 align-items-center">
            {{-- Filter Toko --}}
            <div class="col-lg-3 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i data-lucide="store" style="width:14px;height:14px;"></i>
                    </span>
                    <select name="store_id" class="form-select form-select-sm border-start-0 ps-0" onchange="this.form.submit()">
                        <option value="">Semua toko mitra</option>
                        @foreach ($stores as $s)
                            <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="col-lg-9 col-md-8">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" id="stockBarcodeInput" class="form-control form-control-sm" placeholder="Cari barcode, kode produksi, nama kopi..." value="{{ request('search') }}" autocomplete="off">
                    @if(request('search'))
                        <a href="{{ route('stock.index', request('store_id') ? ['store_id' => request('store_id')] : []) }}" class="btn btn-sm btn-outline-modern" title="Reset pencarian">
                            <i data-lucide="x" style="width:12px;height:12px;"></i>
                        </a>
                    @endif
                    <button class="btn btn-sm btn-outline-modern px-3" type="submit">
                        <i data-lucide="search" style="width:12px;height:12px;"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Stock Batches Table Card --}}
    <div class="card-modern">
        <div class="card-body-modern p-0">
            <div class="table-responsive">
                <table class="table-modern w-100 m-0 align-middle">
                    <thead>
                        <tr>
                            <th style="min-width: 140px; padding-left: 18px;">Barcode & batch</th>
                            <th style="min-width: 130px;">Toko mitra</th>
                            <th style="min-width: 130px;">Jenis kopi</th>
                            <th style="min-width: 95px;">Tgl. masuk</th>
                            <th style="min-width: 95px;">Tgl. kedaluwarsa</th>
                            <th class="text-end" style="width: 55px;">Awal</th>
                            <th class="text-end" style="width: 55px;">Laku</th>
                            <th class="text-end" style="width: 55px;">Sisa</th>
                            <th class="text-end" style="min-width: 105px;">Total nilai</th>
                            <th class="text-center" style="width: 80px;">Status</th>
                            <th class="text-end" style="min-width: 155px; padding-right: 18px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockBatches as $batch)
                            <tr style="{{ $batch->is_expired ? 'background: rgba(192,57,43,0.04);' : ($batch->is_expiring_soon ? 'background: rgba(200,138,78,0.04);' : '') }}">
                                <td style="padding-left: 18px;">
                                    <div class="d-flex flex-column gap-1" style="width: 135px; max-width: 135px; overflow: hidden;">
                                        <div style="width: 135px; height: 26px; overflow: hidden; display: flex; align-items: center;">
                                            <svg class="barcode-table-svg" data-code="{{ $batch->barcode }}" style="display: block; width: 100%; height: 26px;"></svg>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between" style="width: 135px;">
                                            <span class="font-monospace fw-bold text-dark text-nowrap" style="font-size:0.75rem; letter-spacing: 0.04em;" title="Nomor Barcode">
                                                {{ $batch->barcode }}
                                            </span>
                                            <button type="button" class="btn btn-link p-0 text-muted btn-copy-code" data-code="{{ $batch->barcode }}" title="Salin nomor barcode">
                                                <i data-lucide="copy" style="width:11px;height:11px;"></i>
                                            </button>
                                        </div>
                                        <div class="d-flex align-items-center gap-1" title="Kode Produksi">
                                            <span class="badge-modern badge-neutral font-monospace text-truncate" style="font-size:0.68rem; max-width: 135px;">
                                                <i data-lucide="tag" style="width:9px;height:9px;" class="me-0.5"></i>{{ $batch->kode_produksi }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column justify-content-center" style="min-width: 110px;">
                                        <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                            <i data-lucide="store" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0;"></i>
                                            <span class="text-nowrap" style="font-size:0.88rem;">{{ $batch->store->name ?? 'Belum diisi' }}</span>
                                        </div>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">PJ: {{ $batch->store->penanggung_jawab ?: 'Belum diisi' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $batch->coffeeType->name }}</div>
                                    @php $stkCat = strtolower($batch->coffeeType->category ?? ''); @endphp
                                    @if($stkCat === 'robusta')
                                        <div class="mt-1"><span class="badge-robusta" style="font-size: 0.7rem;">Robusta</span></div>
                                    @elseif($stkCat === 'arabika')
                                        <div class="mt-1"><span class="badge-arabika" style="font-size: 0.7rem;">Arabika</span></div>
                                    @else
                                        <div class="mt-1"><span class="badge-neutral" style="font-size: 0.7rem;">{{ ucfirst($batch->coffeeType->category ?? '-') }}</span></div>
                                    @endif
                                </td>
                                <td class="tabular-nums" style="font-size:0.82rem;">{{ $batch->tgl_stock->format('d/m/Y') }}</td>
                                <td>
                                    <span class="tabular-nums" style="font-size:0.82rem;">{{ $batch->tgl_exp->format('d/m/Y') }}</span>
                                    @if ($batch->is_expired)
                                        <div class="mt-1"><span class="badge-modern badge-danger" style="font-size:0.65rem;">Kedaluwarsa</span></div>
                                    @elseif ($batch->is_expiring_soon)
                                        <div class="mt-1"><span class="badge-modern badge-warning" style="font-size:0.65rem;">Segera kedaluwarsa</span></div>
                                    @endif
                                </td>
                                <td class="tabular-nums text-end">{{ $batch->jumlah_stock }}</td>
                                <td class="tabular-nums text-end">{{ $batch->laku }}</td>
                                <td class="tabular-nums text-end fw-bold {{ $batch->sisa > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $batch->sisa }}
                                </td>
                                <td class="tabular-nums text-end fw-semibold">
                                    Rp {{ number_format($batch->total, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if ($batch->status === 'normal')
                                        <span class="badge-modern badge-success" style="font-size:0.72rem;">Normal</span>
                                    @elseif ($batch->status === 'tarik')
                                        <span class="badge-modern badge-danger" style="font-size:0.72rem;">Ditarik</span>
                                    @else
                                        <span class="badge-modern badge-neutral" style="font-size:0.72rem;">Diganti</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1.5">
                                        <button type="button" class="btn btn-table-action btn-open-label-modal" 
                                            title="Cetak Label Stiker Kemasan"
                                            data-id="{{ $batch->id }}"
                                            data-code="{{ $batch->barcode }}"
                                            data-kode-produksi="{{ $batch->kode_produksi }}"
                                            data-store="{{ $batch->store->name }}"
                                            data-coffee="{{ $batch->coffeeType->name }}"
                                            data-category="{{ ucfirst($batch->coffeeType->category) }}"
                                            data-stock="{{ $batch->tgl_stock->format('d/m/Y') }}"
                                            data-exp="{{ $batch->tgl_exp->format('d/m/Y') }}">
                                            <i data-lucide="tag"></i>
                                        </button>
                                        <button type="button" class="btn btn-table-action btn-open-quick-edit" 
                                            data-bs-toggle="modal"
                                            data-bs-target="#quickEditStockModal"
                                            title="Edit Kode Produksi, Tanggal & Stok"
                                            data-id="{{ $batch->id }}"
                                            data-barcode="{{ $batch->barcode }}"
                                            data-kode-produksi="{{ $batch->kode_produksi }}"
                                            data-store="{{ $batch->store->name }}"
                                            data-coffee="{{ $batch->coffeeType->name }}"
                                            data-stock-date="{{ $batch->tgl_stock->format('Y-m-d') }}"
                                            data-exp-date="{{ $batch->tgl_exp->format('Y-m-d') }}"
                                            data-total="{{ $batch->jumlah_stock }}"
                                            data-laku="{{ $batch->laku }}"
                                            data-status="{{ $batch->status }}"
                                            data-ket="{{ $batch->keterangan ?? '' }}"
                                            data-url="{{ route('stock.update', $batch) }}"
                                            data-edit-url="{{ route('stock.edit', $batch) }}">
                                            <i data-lucide="edit" style="pointer-events:none;"></i>
                                        </button>
                                        <form action="{{ route('stock.tambah', $batch) }}" method="POST" class="d-inline-flex gap-1" onsubmit="return confirmTambah(event, this)">
                                            @csrf
                                            <input type="number" name="jumlah_tambahan" min="1" class="form-control form-control-sm tabular-nums" style="width:52px; height: 32px; font-size:0.8125rem;" placeholder="+qty">
                                            <button type="submit" class="btn btn-table-action" title="Tambah Stock Cepat"><i data-lucide="plus"></i></button>
                                        </form>
                                        <form action="{{ route('stock.destroy', $batch) }}" method="POST" onsubmit="return confirm('Yakin hapus batch stock ini?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-table-action text-danger" title="Hapus Batch"><i data-lucide="trash-2"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state text-center py-5">
                                        <div class="stat-icon stat-icon-stock mx-auto mb-3" style="width: 54px; height: 54px;">
                                            <i data-lucide="package-search" style="width: 26px; height: 26px;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">Belum ada data stok yang cocok</h6>
                                        <p class="text-muted small mb-3">Gunakan scanner external atau ubah filter pencarian untuk menemukan stok toko mitra.</p>
                                        <a href="{{ route('stock.create') }}" class="btn btn-sm btn-accent">
                                            <i data-lucide="plus" style="width:14px;height:14px;"></i> Tambah Batch Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stockBatches->hasPages())
                <div class="p-3 border-top">
                    {{ $stockBatches->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Preview & Cetak Stiker Barcode Kemasan Produk --}}
    <div class="modal fade" id="barcodeLabelModal" tabindex="-1" aria-labelledby="barcodeLabelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" id="barcodeLabelModalLabel">
                        <i data-lucide="tag" class="me-1 text-primary"></i> Preview Barcode
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background: var(--bg-primary);">
                    {{-- Detail Batch Info --}}
                    <div class="p-3 mb-3 bg-white rounded-3 border text-start shadow-xs">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 id="modalLabelCoffee" class="fw-bold mb-1 text-dark">Nama Kopi</h6>
                                <div class="small text-muted d-flex gap-2 align-items-center">
                                    <span id="modalLabelCategory" class="badge bg-secondary-subtle text-secondary border">Kategori</span>
                                    <span>•</span>
                                    <span>Toko Mitra: <strong id="modalLabelStore" class="text-dark">Toko</strong></span>
                                </div>
                            </div>
                            <div class="text-end small font-monospace">
                                <div>Prod: <span id="modalLabelStock" class="text-muted">01/01/2026</span></div>
                                <div>Exp: <span id="modalLabelExp" class="text-danger fw-semibold">01/04/2026</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- Stiker Barcode --}}
                    <div class="d-flex justify-content-center">
                        <div id="printableLabelCard" class="bg-white p-3 rounded-2 text-center" style="width: 290px; border: 1.5px dashed #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
                                <div style="width: 100%; display: flex; justify-content: center; overflow: hidden;">
                                    <svg id="modalBarcodeSvg" style="max-width: 100%; height: auto; display: block; margin: 0 auto;"></svg>
                                </div>
                                <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                                    <span id="modalLabelCode" class="text-muted font-monospace" style="font-size: 0.8rem;">KODE</span>
                                    <button type="button" class="btn btn-sm btn-link p-0 text-muted" id="btnCopyModalCode" title="Salin Kode">
                                        <i data-lucide="copy" style="width:13px;height:13px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 d-flex justify-content-between">
                    <a id="modalDirectPrintLink" href="#" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i data-lucide="external-link"></i> Cetak Lembar Massal
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-modern" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-accent" onclick="printModalSticker()">
                            <i data-lucide="printer"></i> Cetak Barcode
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Quick Edit Batch: Tanggal Masuk, Tanggal Expired, Stok, Status --}}
    <div class="modal fade" id="quickEditStockModal" tabindex="-1" aria-labelledby="quickEditStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <form id="quickEditStockForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom py-3" style="background: var(--bg-card, #ffffff);">
                        <div class="d-flex align-items-center gap-2">
                            <div class="stat-icon stat-icon-edit" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(200,138,78,0.12); color: var(--accent, #C88A4E); display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="edit-3" style="width: 17px; height: 17px;"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold mb-0 text-primary" id="quickEditStockModalLabel">Edit Tanggal & Stok Batch</h6>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <span id="qeCoffeeName" class="fw-semibold text-dark"></span> &bull; 
                                    <span id="qeStoreName"></span> &bull; 
                                    <span id="qeBatchCode" class="font-monospace fw-bold text-accent"></span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" style="background: var(--bg-primary, #F8F9FA);">
                        <div class="row g-3">
                            {{-- Field 1: Kode Produksi (Bisa diedit dan ditambahkan sendiri secara bebas) --}}
                            <div class="col-sm-6">
                                <label class="form-label-modern fw-bold mb-1 d-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                    <i data-lucide="tag" style="width: 13px; height: 13px; color: var(--accent, #C88A4E);"></i>
                                    Kode Produksi <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="kode_produksi" id="qeInputKodeProduksi" class="form-control form-control-modern font-monospace fw-bold" placeholder="Contoh: HH-2609-001" required>
                                <small class="text-muted" style="font-size: 0.7rem;">Nomor batch / kode produksi (dapat diedit).</small>
                            </div>

                            {{-- Field 2: Barcode --}}
                            <div class="col-sm-6">
                                <label class="form-label-modern fw-bold mb-1 d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                                    <span class="d-flex align-items-center gap-1">
                                        <i data-lucide="scan-barcode" style="width: 13px; height: 13px; color: #1E3A5F;"></i>
                                        Barcode <span class="text-danger">*</span>
                                    </span>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none text-primary" id="qeBtnGenBarcode" style="font-size: 0.7rem;">
                                        <i data-lucide="refresh-cw" style="width: 11px; height: 11px;"></i> Acak Barcode
                                    </button>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="barcode" id="qeInputBarcode" class="form-control form-control-modern font-monospace fw-bold" maxlength="13" required>
                                    <button class="btn btn-outline-secondary" type="button" id="qeBtnResetBarcode" title="Kembalikan ke barcode awal">
                                        <i data-lucide="rotate-ccw" style="width: 12px; height: 12px;"></i>
                                    </button>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Nomor barcode kemasan retail.</small>
                            </div>

                            {{-- Live Barcode Preview --}}
                            <div class="col-12">
                                <div class="p-2 bg-white rounded-3 border text-center">
                                    <div class="text-muted text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.08em; font-weight: 700;">Pratinjau Garis Barcode</div>
                                    <div style="height: 38px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        <svg id="qeLiveBarcodeSvg" style="max-width: 200px; height: 38px; display: block; margin: 0 auto;"></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label-modern fw-bold mb-1 d-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                    <i data-lucide="calendar" style="width: 13px; height: 13px; color: var(--accent, #C88A4E);"></i>
                                    Tanggal Masuk <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tgl_stock" id="qeInputTglStock" class="form-control form-control-modern" required>
                                <small class="text-muted" style="font-size: 0.7rem;">Tanggal batch masuk toko</small>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label-modern fw-bold mb-1 d-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                    <i data-lucide="clock" style="width: 13px; height: 13px; color: #C0392B;"></i>
                                    Tanggal Expired <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tgl_exp" id="qeInputTglExp" class="form-control form-control-modern" required>
                                <small class="text-muted" style="font-size: 0.7rem;">Batas waktu konsumsi</small>
                            </div>

                            <div class="col-sm-4">
                                <label class="form-label-modern fw-bold mb-1" style="font-size: 0.78rem;">
                                    Stok Awal <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="jumlah_stock" id="qeInputJumlahStock" min="1" class="form-control form-control-modern" required oninput="calcQuickSisa()">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label-modern fw-bold mb-1" style="font-size: 0.78rem;">
                                    Laku (Terjual) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="laku" id="qeInputLaku" min="0" class="form-control form-control-modern" required oninput="calcQuickSisa()">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label-modern fw-bold mb-1" style="font-size: 0.78rem;">
                                    Sisa Fisik
                                </label>
                                <input type="text" id="qeDisplaySisa" class="form-control form-control-modern bg-light font-monospace fw-bold text-primary" readonly>
                            </div>

                            <div class="col-sm-12">
                                <label class="form-label-modern fw-bold mb-1" style="font-size: 0.78rem;">
                                    Status Batch <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="qeInputStatus" class="form-select form-control-modern" required>
                                    <option value="normal">Normal (Aktif di Toko)</option>
                                    <option value="tarik">Tarik (Ditarik dari Toko)</option>
                                    <option value="ganti">Ganti (Retur / Diganti Batch Baru)</option>
                                </select>
                            </div>

                            <div class="col-sm-12">
                                <label class="form-label-modern fw-bold mb-1" style="font-size: 0.78rem;">
                                    Catatan / Keterangan (Opsional)
                                </label>
                                <input type="text" name="keterangan" id="qeInputKet" class="form-control form-control-modern" placeholder="Koreksi batch / tanggal / jumlah">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2.5 px-4 d-flex justify-content-between" style="background: var(--bg-card, #ffffff);">
                        <a id="qeFullEditLink" href="#" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                            <i data-lucide="external-link" style="width: 13px; height: 13px;"></i> Halaman Edit Lengkap
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-modern btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-accent btn-sm d-flex align-items-center gap-1">
                                <i data-lucide="check" style="width: 14px; height: 14px;"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();

        // Render semua barcode di tabel stock dengan auto-fit viewBox agar tidak overflow
        document.querySelectorAll('.barcode-table-svg').forEach(function(svg) {
            const code = (svg.dataset.code || '').trim();
            if (code) {
                const isEan13 = /^\d{13}$/.test(code);
                const format = isEan13 ? "EAN13" : "CODE128";
                try {
                    JsBarcode(svg, code, {
                        format: format,
                        lineColor: "#1E3A5F",
                        width: isEan13 ? 1.3 : 1.2,
                        height: 26,
                        displayValue: false,
                        background: "transparent",
                        margin: 0
                    });

                    // Set viewBox and clean width/height attributes so SVG scales inside its 125px container
                    const w = svg.getAttribute('width');
                    const h = svg.getAttribute('height');
                    if (w && h) {
                        svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
                        svg.removeAttribute('width');
                        svg.removeAttribute('height');
                    }
                    svg.style.width = '100%';
                    svg.style.height = '26px';
                } catch(e) {
                    try {
                        JsBarcode(svg, code, { format: "CODE128", lineColor: "#1E3A5F", width: 1.2, height: 26, displayValue: false, margin: 0 });
                    } catch(err2) {
                        console.error("Barcode table error for " + code, err2);
                    }
                }
            }
        });

        // Modal handler
        const labelModalEl = document.getElementById('barcodeLabelModal');
        const bsModal = new bootstrap.Modal(labelModalEl);

        document.querySelectorAll('.btn-open-label-modal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const code = (this.dataset.code || '').trim();
                const store = this.dataset.store;
                const coffee = this.dataset.coffee;
                const cat = this.dataset.category;
                const stock = this.dataset.stock;
                const exp = this.dataset.exp;

                document.getElementById('modalLabelCoffee').innerText = coffee;
                document.getElementById('modalLabelCategory').innerText = cat;
                document.getElementById('modalLabelStore').innerText = store;
                document.getElementById('modalLabelCode').innerText = code;
                document.getElementById('modalLabelStock').innerText = stock;
                document.getElementById('modalLabelExp').innerText = exp;
                document.getElementById('modalDirectPrintLink').href = "{{ route('stock.print-labels') }}?batch_id=" + id;

                // Render barcode SVG di modal (Standar Retail Internasional EAN-13 & Code-128)
                const isEan13 = /^\d{13}$/.test(code);
                const format = isEan13 ? "EAN13" : "CODE128";
                try {
                    JsBarcode('#modalBarcodeSvg', code, {
                        format: format,
                        lineColor: "#000000",
                        background: "#ffffff",
                        width: isEan13 ? 1.9 : 2.0,
                        height: 52,
                        displayValue: true,
                        fontSize: 14,
                        font: "'JetBrains Mono', 'Consolas', monospace",
                        fontOptions: "bold",
                        textMargin: 5,
                        margin: 12 // Quiet Zone wajib agar bisa discan
                    });
                } catch(e) {
                    try {
                        JsBarcode('#modalBarcodeSvg', code, {
                            format: "CODE128",
                            lineColor: "#000000",
                            background: "#ffffff",
                            width: 2.0,
                            height: 52,
                            displayValue: true,
                            margin: 12
                        });
                    } catch(err2) {
                        console.error("Modal barcode render error", err2);
                    }
                }

                bsModal.show();
            });
        });

        // Copy barcode to clipboard with micro-interaction
        document.addEventListener('click', function(e) {
            const copyBtn = e.target.closest('.btn-copy-code') || (e.target.id === 'btnCopyModalCode' ? e.target : null);
            if (copyBtn) {
                const code = copyBtn.dataset.code || document.getElementById('modalLabelCode').innerText;
                if (code && navigator.clipboard) {
                    navigator.clipboard.writeText(code).then(() => {
                        const originalHtml = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<span class="text-success fw-bold" style="font-size:0.75rem;">Tersalin! ✓</span>';
                        setTimeout(() => {
                            copyBtn.innerHTML = originalHtml;
                            lucide.createIcons();
                        }, 1500);
                    });
                }
            }
        });

        // Quick Edit Modal handler (Tanggal Masuk, Tanggal Expired, Stok, Status)
        const quickEditModalEl = document.getElementById('quickEditStockModal');
        if (quickEditModalEl) {
            function populateQuickEdit(btn) {
                if (!btn) return;
                const ds = btn.dataset;
                const form = document.getElementById('quickEditStockForm');
                if (form && ds.url) form.action = ds.url;

                const setTxt = (id, val) => { const el = document.getElementById(id); if (el) el.innerText = val || ''; };
                const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = (val !== undefined ? val : ''); };

                setTxt('qeCoffeeName', ds.coffee);
                setTxt('qeStoreName', ds.store);
                setTxt('qeBatchCode', (ds.kodeProduksi || '') + ' (' + (ds.barcode || '') + ')');
                
                qeOriginalKode = ds.kodeProduksi || '';
                qeOriginalBarcode = ds.barcode || '';

                setVal('qeInputKodeProduksi', qeOriginalKode);
                setVal('qeInputBarcode', qeOriginalBarcode);
                updateQeBarcodePreview();

                setVal('qeInputTglStock', ds.stockDate);
                setVal('qeInputTglExp', ds.expDate);

                const expInput = document.getElementById('qeInputTglExp');
                if (expInput && ds.stockDate) expInput.min = ds.stockDate;

                setVal('qeInputJumlahStock', ds.total);
                setVal('qeInputLaku', ds.laku);
                setVal('qeInputStatus', ds.status || 'normal');
                setVal('qeInputKet', ds.ket || '');

                const fullLink = document.getElementById('qeFullEditLink');
                if (fullLink && ds.editUrl) fullLink.href = ds.editUrl;

                calcQuickSisa();
                lucide.createIcons();
            }

            let qeOriginalKode = '';
            let qeOriginalBarcode = '';

            const qeKodeInput = document.getElementById('qeInputKodeProduksi');
            const qeBarcodeInput = document.getElementById('qeInputBarcode');
            const qeLiveSvg = document.getElementById('qeLiveBarcodeSvg');
            const qeBtnGenKode = document.getElementById('qeBtnGenKodeProd');
            const qeBtnGenBarcode = document.getElementById('qeBtnGenBarcode');
            const qeBtnResetBarcode = document.getElementById('qeBtnResetBarcode');

            function updateQeBarcodePreview() {
                if (!qeBarcodeInput || !qeLiveSvg) return;
                const code = qeBarcodeInput.value.trim();
                if (!code) {
                    qeLiveSvg.innerHTML = '';
                    return;
                }
                const isEan13 = /^\d{13}$/.test(code);
                try {
                    JsBarcode(qeLiveSvg, code, {
                        format: isEan13 ? "EAN13" : "CODE128",
                        lineColor: "#1C1410",
                        background: "#ffffff",
                        width: isEan13 ? 1.8 : 1.6,
                        height: 36,
                        displayValue: true,
                        fontSize: 12,
                        font: "'JetBrains Mono', monospace",
                        margin: 6
                    });
                } catch(e) {
                    try {
                        JsBarcode(qeLiveSvg, code, { format: "CODE128", width: 1.6, height: 36, displayValue: true, margin: 6 });
                    } catch(e2) {
                        qeLiveSvg.innerHTML = '';
                    }
                }
            }

            if (qeBarcodeInput) {
                qeBarcodeInput.addEventListener('input', updateQeBarcodePreview);
            }

            if (qeBtnResetBarcode) {
                qeBtnResetBarcode.addEventListener('click', function() {
                    if (qeBarcodeInput && qeOriginalBarcode) {
                        qeBarcodeInput.value = qeOriginalBarcode;
                        updateQeBarcodePreview();
                    }
                });
            }

            if (qeBtnGenKode) {
                qeBtnGenKode.addEventListener('click', function() {
                    fetch('{{ route("stock.generate-code") }}?type=kode_produksi')
                        .then(r => r.json())
                        .then(data => {
                            if (data.code && qeKodeInput) {
                                qeKodeInput.value = data.code;
                            }
                        });
                });
            }

            if (qeBtnGenBarcode) {
                qeBtnGenBarcode.addEventListener('click', function() {
                    fetch('{{ route("stock.generate-code") }}?type=barcode')
                        .then(r => r.json())
                        .then(data => {
                            if (data.code && qeBarcodeInput) {
                                qeBarcodeInput.value = data.code;
                                updateQeBarcodePreview();
                            }
                        });
                });
            }

            // Bootstrap event hook when modal is about to show
            quickEditModalEl.addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget || document.activeElement?.closest('.btn-open-quick-edit');
                populateQuickEdit(btn);
            });

            // Delegated click listener fallback
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-open-quick-edit');
                if (btn) {
                    populateQuickEdit(btn);
                    const bsModal = bootstrap.Modal.getOrCreateInstance(quickEditModalEl);
                    bsModal.show();
                }
            });

            // Otomatis sesuaikan batas minimum tgl_exp jika tgl_stock diubah
            const qeStockInput = document.getElementById('qeInputTglStock');
            const qeExpInput = document.getElementById('qeInputTglExp');
            if (qeStockInput && qeExpInput) {
                qeStockInput.addEventListener('change', function() {
                    qeExpInput.min = this.value;
                    if (qeExpInput.value && qeExpInput.value <= this.value) {
                        const d = new Date(this.value);
                        d.setMonth(d.getMonth() + 3);
                        qeExpInput.value = d.toISOString().split('T')[0];
                    }
                });
            }
        }
    });

    function focusScanInput() {
        const input = document.getElementById('stockBarcodeInput');
        if (input) {
            input.focus();
            input.select();
        }
    }

    function printModalSticker() {
        const barcodeSvg = document.getElementById('modalBarcodeSvg').outerHTML;
        const codeText = document.getElementById('modalLabelCode').innerText.trim();
        const printWindow = window.open('', '_blank', 'width=450,height=350');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <title>Barcode ${codeText}</title>
                    <link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
                    <style>
                        @page {
                            size: auto;
                            margin: 0;
                        }
                        * {
                            box-sizing: border-box;
                            margin: 0;
                            padding: 0;
                        }
                        body {
                            background: #ffffff;
                            margin: 0;
                            padding: 0;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                            font-family: 'JetBrains Mono', Consolas, 'Courier New', monospace;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        .sticker-print {
                            width: 65mm;
                            height: 38mm;
                            padding: 3mm;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            text-align: center;
                            background: #ffffff;
                            border: 1px dashed #cbd5e1;
                            box-sizing: border-box;
                        }
                        .sticker-print svg {
                            max-width: 100%;
                            height: auto;
                            display: block;
                            margin: 0 auto;
                        }
                        @media print {
                            body {
                                min-height: auto;
                            }
                            .sticker-print {
                                border: none !important;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="sticker-print">
                        ${barcodeSvg}
                    </div>
                    <script>
                        window.onload = function() {
                            window.focus();
                            window.print();
                            window.onafterprint = function() { window.close(); };
                        };
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }

    function confirmTambah(e, form) {
        const qty = form.querySelector('input[name=jumlah_tambahan]').value;
        if (!qty || qty < 1) {
            e.preventDefault();
            alert('Isi jumlah tambahan stock dulu.');
            return false;
        }
        return true;
    }

    function calcQuickSisa() {
        const total = parseInt(document.getElementById('qeInputJumlahStock').value) || 0;
        const laku = parseInt(document.getElementById('qeInputLaku').value) || 0;
        const sisa = Math.max(0, total - laku);
        const el = document.getElementById('qeDisplaySisa');
        if (el) el.value = sisa + ' pcs';
    }
</script>
@endsection
