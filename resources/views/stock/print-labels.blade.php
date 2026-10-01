<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        :root {
            --bg-page: #f8fafc;
            --border-card: #e2e8f0;
            --text-dark: #0f172a;
        }

        body {
            background-color: var(--bg-page);
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .no-print-bar {
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .label-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
            padding: 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* 4 Kolom Mode */
        .label-grid.grid-cols-4 {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
        }

        /* 1 Kolom / Thermal Roll Mode */
        .label-grid.grid-cols-thermal {
            grid-template-columns: minmax(240px, 320px);
            justify-content: center;
            gap: 14px;
        }

        /* Professional Enterprise Barcode Sticker */
        .barcode-sticker-card {
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            break-inside: avoid;
            page-break-inside: avoid;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .barcode-sticker-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .barcode-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .barcode-svg {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Print Specific Optimizations */
        @media print {
            @page {
                size: auto;
                margin: 6mm 5mm;
            }

            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .label-grid {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 5mm 4mm !important;
                padding: 0 !important;
                margin: 0 auto !important;
                width: 100% !important;
                justify-content: flex-start !important;
            }

            /* Default 3-kolom pada kertas A4 */
            .barcode-sticker-card {
                width: calc(33.333% - 4mm) !important;
                border: 1px dashed #cbd5e1 !important;
                box-shadow: none !important;
                padding: 4mm 3mm !important;
                border-radius: 4px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                background: #ffffff !important;
            }

            /* 4 Kolom mode pada print */
            .label-grid.grid-cols-4 .barcode-sticker-card {
                width: calc(25% - 4mm) !important;
                padding: 3mm 2mm !important;
            }

            /* Thermal roll mode pada print */
            .label-grid.grid-cols-thermal {
                gap: 2mm !important;
            }
            .label-grid.grid-cols-thermal .barcode-sticker-card {
                width: 100% !important;
                max-width: 60mm !important;
                margin: 0 auto 2mm auto !important;
                padding: 3mm 2mm !important;
            }

            .barcode-sticker-card.border-none {
                border: none !important;
            }

            .barcode-svg {
                max-width: 100% !important;
                height: 48px !important;
            }

            .barcode-code-text {
                font-size: 12px !important;
                letter-spacing: 0.20em !important;
                color: #000000 !important;
                margin-top: 4px !important;
            }

            .lot-info-text {
                font-size: 8px !important;
                letter-spacing: 0.1em !important;
                font-family: monospace !important;
                font-weight: 700 !important;
                color: #000000 !important;
                margin-bottom: 2px !important;
            }

            .lot-info-text.d-none {
                display: none !important;
            }
        }
    </style>
</head>
<body>

{{-- Toolbar Kontrol Atas (Tidak Ikut Tercetak) --}}
<div class="no-print-bar no-print py-2.5 px-4 mb-4" style="background: #ffffff; border-bottom: 1px solid #e2e8f0;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3" style="max-width: 1200px; margin: 0 auto; min-height: 44px;">
        <div class="d-flex align-items-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary" style="flex-shrink:0;">
                <path d="M3 5v14"></path><path d="M8 5v14"></path><path d="M12 5v14"></path><path d="M17 5v14"></path><path d="M21 5v14"></path>
            </svg>
            <h6 class="m-0 fw-bold text-dark" style="font-size: 0.95rem; letter-spacing: -0.01em;">Cetak Barcode</h6>
            <span class="badge bg-light text-secondary border px-2 py-0.5 font-monospace" style="font-size: 0.72rem; font-weight: 600;">
                {{ count($batches) }} Label
            </span>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Pilihan Layout Grid --}}
            <div class="btn-group btn-group-sm" role="group" aria-label="Layout Presets">
                <button type="button" class="btn btn-outline-secondary active fw-medium px-2.5" id="btnLayout3" onclick="setLayout('grid-cols-3', this)">
                    3 Kolom (A4)
                </button>
                <button type="button" class="btn btn-outline-secondary fw-medium px-2.5" id="btnLayout4" onclick="setLayout('grid-cols-4', this)">
                    4 Kolom
                </button>
                <button type="button" class="btn btn-outline-secondary fw-medium px-2.5" id="btnLayoutThermal" onclick="setLayout('grid-cols-thermal', this)">
                    Thermal Roll
                </button>
            </div>

            {{-- Toggle Garis Panduan Potong --}}
            <button type="button" class="btn btn-sm btn-outline-secondary fw-medium px-2.5 d-flex align-items-center gap-1.5" id="btnToggleBorder" onclick="toggleCutBorder()">
                <span class="d-inline-block rounded-circle" id="borderIndicator" style="width: 7px; height: 7px; background: #10b981;"></span>
                <span>Garis Potong</span>
            </button>

            {{-- Toggle Tampilkan Kode Produksi (Lot) --}}
            <button type="button" class="btn btn-sm btn-outline-secondary fw-medium px-2.5 d-flex align-items-center gap-1.5" id="btnToggleLot" onclick="toggleLotDisplay()" title="Tampilkan / Sembunyikan Kode Produksi di atas barcode">
                <span class="d-inline-block rounded-circle" id="lotIndicator" style="width: 7px; height: 7px; background: #94a3b8;"></span>
                <span>Kode Produksi</span>
            </button>

            {{-- Tombol Aksi Cetak & Tutup --}}
            <button onclick="window.print()" class="btn btn-sm btn-dark fw-semibold px-3 d-flex align-items-center gap-1.5 shadow-xs">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Cetak</span>
            </button>
            <button onclick="window.close()" class="btn btn-sm btn-outline-secondary px-3">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- Grid Barcode Stiker Bersih & Minimalis --}}
<div class="label-grid" id="labelGrid">
    @forelse($batches as $batch)
        <div class="barcode-sticker-card">
            <div class="barcode-wrapper flex-column">
                @if($batch->kode_produksi)
                <div class="lot-info-text d-none text-center mb-1">
                    <span class="text-uppercase fw-semibold" style="font-size: 9px; letter-spacing: 0.12em; color: #475569;">{{ $batch->kode_produksi }}</span>
                </div>
                @endif
                <svg class="barcode-svg" id="barcode-{{ $batch->id }}" data-code="{{ $batch->barcode ?: $batch->kode_produksi }}"></svg>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">Tidak ada data batch stock yang ditemukan untuk dicetak.</p>
            <button onclick="window.close()" class="btn btn-sm btn-secondary">Kembali</button>
        </div>
    @endforelse
</div>

<script>
    function renderBarcode(svg, code) {
        const rawCode = String(code || '').trim();
        if (!rawCode) return;

        const isEan13 = /^\d{13}$/.test(rawCode);
        const isEan8 = /^\d{8}$/.test(rawCode);

        let format = "CODE128";
        if (isEan13) format = "EAN13";
        else if (isEan8) format = "EAN8";

        try {
            JsBarcode(svg, rawCode, {
                format: format,
                lineColor: "#000000",
                background: "#ffffff",
                width: format === 'EAN13' ? 1.9 : 2.0,
                height: 52,
                displayValue: true,
                fontSize: 14,
                font: "'JetBrains Mono', 'Consolas', monospace",
                fontOptions: "bold",
                textMargin: 5,
                margin: 12 // Mandatory Quiet Zone (area putih kiri-kanan) agar scanner gun & kamera mudah mendeteksi
            });
        } catch (e) {
            console.warn("Fallback render to CODE128 for:", rawCode, e);
            try {
                JsBarcode(svg, rawCode, {
                    format: "CODE128",
                    lineColor: "#000000",
                    background: "#ffffff",
                    width: 2.0,
                    height: 52,
                    displayValue: true,
                    fontSize: 14,
                    font: "'JetBrains Mono', monospace",
                    fontOptions: "bold",
                    textMargin: 5,
                    margin: 12
                });
            } catch (err2) {
                console.error("Gagal render barcode:", rawCode, err2);
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Render semua barcode dengan standar industri internasional
        document.querySelectorAll('.barcode-svg').forEach(function (svg) {
            renderBarcode(svg, svg.dataset.code);
        });
    });

    function setLayout(layoutClass, btn) {
        const grid = document.getElementById('labelGrid');
        grid.classList.remove('grid-cols-4', 'grid-cols-thermal');
        if (layoutClass !== 'grid-cols-3') {
            grid.classList.add(layoutClass);
        }

        document.querySelectorAll('#btnLayout3, #btnLayout4, #btnLayoutThermal').forEach(b => {
            b.classList.remove('active', 'btn-secondary');
            b.classList.add('btn-outline-secondary');
        });
        btn.classList.add('active');
    }

    let isBorderActive = true;
    function toggleCutBorder() {
        isBorderActive = !isBorderActive;
        const cards = document.querySelectorAll('.barcode-sticker-card');
        const indicator = document.getElementById('borderIndicator');

        cards.forEach(card => {
            if (isBorderActive) {
                card.classList.remove('border-none');
                card.style.border = '1px dashed #cbd5e1';
            } else {
                card.classList.add('border-none');
                card.style.border = 'none';
            }
        });

        if (indicator) {
            indicator.style.background = isBorderActive ? '#10b981' : '#94a3b8';
        }
    }

    let isLotActive = false;
    function toggleLotDisplay() {
        isLotActive = !isLotActive;
        const lots = document.querySelectorAll('.lot-info-text');
        const indicator = document.getElementById('lotIndicator');

        lots.forEach(lot => {
            if (isLotActive) {
                lot.classList.remove('d-none');
            } else {
                lot.classList.add('d-none');
            }
        });

        if (indicator) {
            indicator.style.background = isLotActive ? '#10b981' : '#94a3b8';
        }
    }
</script>

</body>
</html>
