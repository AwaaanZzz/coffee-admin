@extends('layouts.app')
@section('title', 'Edit Data Stock & Tanggal Expired')

@section('breadcrumbs')
<a href="{{ route('dashboard') }}">Beranda</a>
<i data-lucide="chevron-right"></i>
<a href="{{ route('stock.index') }}">Data Stock</a>
<i data-lucide="chevron-right"></i>
<span>Edit Batch: {{ $batch->kode_produksi }}</span>
@endsection

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <span class="badge" style="background: rgba(200,138,78,0.15); color: var(--accent, #C88A4E); font-weight: 700; font-size: 0.75rem;">
                Edit Batch Produksi
            </span>
            <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.8rem;">
                {{ $batch->kode_produksi }}
            </span>
        </div>
        <h2 class="page-title m-0">Edit Data Stock & Tanggal Kadaluarsa</h2>
        <p class="page-subtitle mt-1 mb-0 text-muted">
            Perbarui tanggal masuk, tanggal expired, stok, atau status distribusi untuk batch ini.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock.index') }}" class="btn btn-outline-modern">
            <i data-lucide="arrow-left"></i> Kembali ke Data Stock
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-10">
        <div class="card-modern shadow-sm">
            <div class="card-header-modern d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="edit" class="text-accent" style="width: 18px; height: 18px;"></i>
                    <h5 class="card-title-modern m-0">Edit Batch Stok #{{ $batch->kode_produksi }}</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border">{{ $batch->store->name }}</span>
                    <span class="badge bg-secondary-subtle text-secondary border">{{ $batch->coffeeType->name }}</span>
                </div>
            </div>
            
            <div class="card-body-modern p-4">
                <form action="{{ route('stock.update', $batch) }}" method="POST" class="form-modern" id="editStockForm">
                    @csrf
                    @method('PUT')

                    <!-- Baris 1: Kode Produksi & Barcode Retail -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 form-group-modern">
                            <label class="form-label-modern fw-semibold mb-1">Kode Produksi <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="kode_produksi" 
                                   id="inputKodeProduksi" 
                                   class="form-control form-control-modern font-monospace fw-bold @error('kode_produksi') is-invalid @enderror" 
                                   value="{{ old('kode_produksi', $batch->kode_produksi) }}" 
                                   placeholder="HH-2609-001" 
                                   required 
                                   style="letter-spacing: 0.05em;">
                            @error('kode_produksi') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 form-group-modern">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label-modern fw-semibold mb-0">Barcode Retail <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="btnGenBarcode" title="Acak Barcode" style="font-size: 0.72rem;">
                                    <i data-lucide="refresh-cw" style="width: 11px; height: 11px;"></i> Acak
                                </button>
                            </div>
                            <input type="text" 
                                   name="barcode" 
                                   id="inputBarcode" 
                                   class="form-control form-control-modern font-monospace fw-bold @error('barcode') is-invalid @enderror" 
                                   value="{{ old('barcode', $batch->barcode) }}" 
                                   maxlength="13" 
                                   required 
                                   style="letter-spacing: 0.08em;">
                            @error('barcode') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- Baris 2: Stok Awal, Laku, Sisa Fisik -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4 form-group-modern">
                            <label class="form-label-modern fw-semibold mb-1">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="jumlah_stock" 
                                   id="inputJumlahStock"
                                   min="1" 
                                   class="form-control form-control-modern font-monospace fw-bold @error('jumlah_stock') is-invalid @enderror" 
                                   value="{{ old('jumlah_stock', $batch->jumlah_stock) }}" 
                                   oninput="calcSisa()"
                                   required>
                            @error('jumlah_stock') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4 form-group-modern">
                            <label class="form-label-modern fw-semibold mb-1">Laku (Terjual) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="laku" 
                                   id="inputLaku"
                                   min="0" 
                                   class="form-control form-control-modern font-monospace fw-bold @error('laku') is-invalid @enderror" 
                                   value="{{ old('laku', $batch->laku) }}" 
                                   oninput="calcSisa()"
                                   required>
                            @error('laku') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4 form-group-modern">
                            <label class="form-label-modern fw-semibold mb-1">Sisa Stok Fisik</label>
                            <input type="text" 
                                   id="displaySisa" 
                                   class="form-control form-control-modern bg-light font-monospace fw-bold text-accent" 
                                   value="{{ $batch->sisa }}" 
                                   readonly>
                        </div>
                    </div>

                    <!-- Baris 3: Tanggal Masuk & Expired -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 form-group-modern">
                            <label class="form-label-modern fw-semibold mb-1">Tanggal Masuk Stok <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="tgl_stock" 
                                   id="inputTglStock"
                                   class="form-control form-control-modern font-monospace @error('tgl_stock') is-invalid @enderror" 
                                   value="{{ old('tgl_stock', $batch->tgl_stock->format('Y-m-d')) }}" 
                                   required>
                            @error('tgl_stock') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 form-group-modern">
                            <label class="form-label-modern fw-semibold mb-1">Tanggal Kadaluarsa (Exp) <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="tgl_exp" 
                                   id="inputTglExp"
                                   class="form-control form-control-modern font-monospace @error('tgl_exp') is-invalid @enderror" 
                                   value="{{ old('tgl_exp', $batch->tgl_exp->format('Y-m-d')) }}" 
                                   required>
                            @error('tgl_exp') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- Baris 4: Status Distribusi & Catatan -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 form-group-modern">
                            <label class="form-label-modern fw-semibold mb-1">Status Distribusi <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-control-modern @error('status') is-invalid @enderror" required>
                                <option value="normal" {{ old('status', $batch->status) === 'normal' ? 'selected' : '' }}>Normal (Aktif di Toko)</option>
                                <option value="tarik" {{ old('status', $batch->status) === 'tarik' ? 'selected' : '' }}>Tarik (Ditarik dari Toko)</option>
                                <option value="ganti" {{ old('status', $batch->status) === 'ganti' ? 'selected' : '' }}>Ganti (Retur / Ganti Batch)</option>
                            </select>
                            @error('status') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 form-group-modern">
                            <label class="form-label-modern fw-semibold mb-1">Catatan (Opsional)</label>
                            <input type="text" 
                                   name="keterangan" 
                                   class="form-control form-control-modern" 
                                   placeholder="Catatan perubahan stok..." 
                                   value="{{ old('keterangan', $batch->keterangan ?? '') }}">
                        </div>
                    </div>

                    <!-- Pratinjau Barcode SVG Box -->
                    <div class="p-3 mb-4 rounded-3 border bg-white text-center shadow-xs" style="max-width: 440px; margin: 0 auto;">
                        <div class="d-flex align-items-center justify-content-center mb-2 px-1">
                            <span class="text-uppercase fw-bold text-muted" style="font-size: 0.68rem; letter-spacing: 0.08em;">Pratinjau Stiker Barcode</span>
                        </div>
                        <div style="min-height: 50px; display: flex; align-items: center; justify-content: center;">
                            <svg id="editLiveBarcodeSvg" style="max-width: 100%; height: 50px; display: block; margin: 0 auto;"></svg>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary px-3">Batal</a>
                        <button type="submit" class="btn btn-accent px-4 d-inline-flex align-items-center gap-1.5">
                            <i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    function calcSisa() {
        const total = parseInt(document.getElementById('inputJumlahStock').value) || 0;
        const laku = parseInt(document.getElementById('inputLaku').value) || 0;
        const sisa = Math.max(0, total - laku);
        document.getElementById('displaySisa').value = sisa;
    }

    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();

        // Ensure tgl_exp is after tgl_stock dynamically
        const tglStock = document.getElementById('inputTglStock');
        const tglExp = document.getElementById('inputTglExp');

        if (tglStock && tglExp) {
            tglStock.addEventListener('change', function() {
                if (this.value) {
                    tglExp.min = this.value;
                }
            });
        }

        // Live Barcode Preview & Code Generators
        const kodeInput = document.getElementById('inputKodeProduksi');
        const barcodeInput = document.getElementById('inputBarcode');
        const liveSvg = document.getElementById('editLiveBarcodeSvg');
        const btnGenKode = document.getElementById('btnGenKode');
        const btnGenBarcode = document.getElementById('btnGenBarcode');

        function updateBarcode() {
            if (!barcodeInput || !liveSvg) return;
            const val = barcodeInput.value.trim();
            if (!val) {
                liveSvg.innerHTML = '';
                return;
            }

            const isEan13 = /^\d{13}$/.test(val);
            try {
                JsBarcode(liveSvg, val, {
                    format: isEan13 ? "EAN13" : "CODE128",
                    lineColor: "#1C1410",
                    background: "#ffffff",
                    width: 1.8,
                    height: 48,
                    displayValue: true,
                    fontSize: 13,
                    font: "'JetBrains Mono', monospace",
                    margin: 8
                });
            } catch(e) {
                try {
                    JsBarcode(liveSvg, val, { format: "CODE128", width: 1.6, height: 48, displayValue: true, margin: 8 });
                } catch(e2) {
                    liveSvg.innerHTML = '';
                }
            }
        }

        if (barcodeInput) {
            barcodeInput.addEventListener('input', updateBarcode);
        }

        if (btnGenBarcode) {
            btnGenBarcode.addEventListener('click', function() {
                fetch('{{ route("stock.generate-code") }}?type=barcode')
                    .then(r => r.json())
                    .then(data => {
                        if (data.code && barcodeInput) {
                            barcodeInput.value = data.code;
                            updateBarcode();
                        }
                    });
            });
        }

        if (btnGenKode) {
            btnGenKode.addEventListener('click', function() {
                fetch('{{ route("stock.generate-code") }}?type=kode_produksi')
                    .then(r => r.json())
                    .then(data => {
                        if (data.code && kodeInput) {
                            kodeInput.value = data.code;
                        }
                    });
            });
        }

        // Initial render
        updateBarcode();
    });
</script>
@endsection
