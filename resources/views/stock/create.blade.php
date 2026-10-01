@extends('layouts.app')
@section('title', 'Tambah Stock')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('stock.index') }}">Data Stock</a>
        <i data-lucide="chevron-right"></i>
        <span>Tambah Stock</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Tambah Stock Baru</h3>
            <p class="page-subtitle">Alokasikan stock kopi ke toko mitra.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <div class="card-modern shadow-sm">
                <div class="card-header-modern d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="package-plus" class="text-accent" style="width: 20px; height: 20px;"></i>
                        <h5 class="card-title-modern m-0">Tambah Stock Batch Baru</h5>
                    </div>
                    <span class="badge bg-light text-muted border font-monospace">Formulir Stok</span>
                </div>
                
                <div class="card-body-modern p-4">
                    <form action="{{ route('stock.store') }}" method="POST" class="form-modern">
                        @csrf

                        <!-- Baris 1: Toko Mitra & Jenis Kopi -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6 form-group-modern">
                                <label class="form-label-modern fw-semibold">Toko Mitra <span class="text-danger">*</span></label>
                                <select name="store_id" class="form-control-modern w-100" required>
                                    <option value="">-- Pilih Toko Mitra --</option>
                                    @foreach ($stores as $s)
                                        <option value="{{ $s->id }}" {{ old('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                @error('store_id') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6 form-group-modern">
                                <label class="form-label-modern fw-semibold">Jenis Kopi <span class="text-danger">*</span></label>
                                <select name="coffee_type_id" class="form-control-modern w-100" required>
                                    <option value="">-- Pilih Varian Kopi --</option>
                                    @foreach ($coffeeTypes as $c)
                                        <option value="{{ $c->id }}" {{ old('coffee_type_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ ucfirst($c->category) }})</option>
                                    @endforeach
                                </select>
                                @error('coffee_type_id') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Baris 2: Kode Produksi & Barcode Retail -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6 form-group-modern">
                                <label class="form-label-modern fw-semibold mb-1">Kode Produksi <span class="text-danger">*</span></label>
                                <input type="text" name="kode_produksi" id="kodeProduksiInput" class="form-control-modern form-control font-monospace fw-bold" value="{{ old('kode_produksi', $defaultKodeProduksi ?? '') }}" placeholder="HH-2609-001" required style="letter-spacing: 0.05em;">
                                @error('kode_produksi') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6 form-group-modern">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label-modern fw-semibold mb-0">Barcode Retail <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="btnGenBarcode" title="Acak Barcode" style="font-size: 0.72rem;">
                                        <i data-lucide="refresh-cw" style="width: 11px; height: 11px;"></i> Acak
                                    </button>
                                </div>
                                <input type="text" name="barcode" id="barcodeInput" class="form-control-modern form-control font-monospace fw-bold" value="{{ old('barcode', $defaultBarcode ?? '') }}" maxlength="13" required style="letter-spacing: 0.08em;">
                                @error('barcode') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Baris 3: Jumlah Stok, Tanggal Masuk, Tanggal Kadaluarsa -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4 form-group-modern">
                                <label class="form-label-modern fw-semibold">Jumlah Stok (Pcs) <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah_stock" min="1" class="form-control-modern w-100 font-monospace fw-bold" value="{{ old('jumlah_stock') }}" placeholder="0" required>
                                @error('jumlah_stock') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 form-group-modern">
                                <label class="form-label-modern fw-semibold">Tanggal Masuk Stok <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_stock" class="form-control-modern w-100 font-monospace" value="{{ old('tgl_stock', date('Y-m-d')) }}" required>
                                @error('tgl_stock') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 form-group-modern">
                                <label class="form-label-modern fw-semibold">Tanggal Kadaluarsa (Exp) <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_exp" class="form-control-modern w-100 font-monospace" value="{{ old('tgl_exp', date('Y-m-d', strtotime('+3 months'))) }}" required>
                                @error('tgl_exp') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Pratinjau Barcode Kemasan Retail -->
                        <div class="p-3 mb-4 rounded-3 border bg-white text-center shadow-xs" id="barcodePreviewBox" style="max-width: 440px; margin: 0 auto;">
                            <div class="d-flex align-items-center justify-content-center mb-2 px-1">
                                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.68rem; letter-spacing: 0.08em;">Pratinjau Stiker Barcode</span>
                            </div>
                            <div class="d-flex justify-content-center align-items-center py-1">
                                <svg id="liveBarcodeSvg" style="max-width: 100%; height: 50px; display: block; margin: 0 auto;"></svg>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary px-3">Batal</a>
                            <button type="submit" class="btn btn-accent px-4 d-inline-flex align-items-center gap-1.5">
                                <i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();

        const kodeInput = document.getElementById('kodeProduksiInput');
        const barcodeInput = document.getElementById('barcodeInput');
        const svg = document.getElementById('liveBarcodeSvg');
        const text = document.getElementById('liveBarcodeText');
        const btnGenKode = document.getElementById('btnGenKode');
        const btnGenBarcode = document.getElementById('btnGenBarcode');

        function updateBarcode() {
            if (!barcodeInput || !svg) return;
            const val = barcodeInput.value.trim();
            if (val) {
                const isEan13 = /^\d{13}$/.test(val);
                try {
                    JsBarcode(svg, val, {
                        format: isEan13 ? "EAN13" : "CODE128",
                        lineColor: "#1C1410",
                        background: "#ffffff",
                        width: 1.9,
                        height: 48,
                        displayValue: true,
                        fontSize: 14,
                        font: "'JetBrains Mono', 'Consolas', monospace",
                        fontOptions: "bold",
                        textMargin: 4,
                        margin: 10
                    });
                    if (text) text.innerText = val;
                    document.getElementById('barcodePreviewBox').style.display = 'block';
                } catch(e) {
                    try {
                        JsBarcode(svg, val, { format: "CODE128", width: 1.8, height: 48, displayValue: true, margin: 10 });
                        text.innerText = val;
                        document.getElementById('barcodePreviewBox').style.display = 'block';
                    } catch(err2) {
                        text.innerText = val + " (Format barcode belum lengkap)";
                    }
                }
            } else {
                document.getElementById('barcodePreviewBox').style.display = 'none';
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
