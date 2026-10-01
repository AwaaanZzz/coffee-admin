<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Kasir #{{ $invoiceNumber }} - Kopi Hiku Himu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f1f5f9;
            color: #000;
            font-family: 'JetBrains Mono', 'Courier Prime', Courier, monospace;
            font-size: 12px;
            line-height: 1.35;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Screen Action Bar */
        .thermal-actions {
            width: 100%;
            max-width: 420px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: space-between;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
            font-family: sans-serif;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            transition: all 0.15s ease;
        }

        .btn-action:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .btn-action-primary {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }

        .btn-action-primary:hover {
            background: #1e293b;
            color: #ffffff;
        }

        .btn-action-success {
            background: #10b981;
            color: #ffffff;
            border-color: #059669;
        }

        .btn-action-success:hover {
            background: #059669;
            color: #ffffff;
        }

        /* Thermal Paper Container */
        .thermal-ticket {
            background: #ffffff;
            width: 320px; /* 58mm default preview */
            max-width: 100%;
            padding: 16px 14px 20px 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
            border-radius: 4px;
            color: #000000;
            position: relative;
            transition: width 0.2s ease;
        }

        .thermal-ticket.paper-80mm {
            width: 420px;
        }

        /* Monochromatic Receipt Graphic / Logo Box */
        .receipt-logo-box {
            width: 84px;
            height: 84px;
            margin: 0 auto 8px auto;
            background: #000000;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            padding: 6px;
            text-align: center;
        }

        .receipt-logo-icon {
            width: 38px;
            height: 38px;
            stroke-width: 1.8;
        }

        .receipt-logo-badge {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 4px;
            border-top: 1px solid rgba(255, 255, 255, 0.4);
            padding-top: 2px;
            width: 100%;
        }

        /* Store Header */
        .receipt-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .receipt-store-title {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .receipt-store-sub {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .receipt-store-address {
            font-size: 10.5px;
            line-height: 1.3;
            color: #111;
        }

        /* Dividers */
        .receipt-divider {
            border: none;
            border-top: 1px dashed #000000;
            margin: 8px 0;
            width: 100%;
        }

        .receipt-divider-double {
            border: none;
            border-top: 2px dashed #000000;
            margin: 9px 0;
            width: 100%;
        }

        /* Metadata Table */
        .receipt-meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .receipt-meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .receipt-meta-table td.label-col {
            width: 90px;
            white-space: nowrap;
        }

        .receipt-meta-table td.colon-col {
            width: 12px;
            text-align: center;
        }

        .receipt-meta-table td.val-col {
            font-weight: 600;
            word-break: break-word;
        }

        /* Items List */
        .receipt-items {
            width: 100%;
            margin: 4px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 11.5px;
            padding: 2.5px 0;
            gap: 6px;
        }

        .item-desc {
            flex: 1;
            word-break: break-word;
        }

        .item-qty {
            font-weight: 700;
            margin-right: 4px;
        }

        .item-total {
            text-align: right;
            font-weight: 700;
            white-space: nowrap;
        }

        .item-subtext {
            font-size: 9.5px;
            color: #333;
            padding-left: 18px;
            margin-bottom: 2px;
        }

        /* Calculation Section */
        .calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 11.5px;
            padding: 2px 0;
        }

        .calc-row.total-row {
            font-size: 14px;
            font-weight: 800;
            margin-top: 3px;
        }

        /* Footer */
        .receipt-footer {
            text-align: center;
            font-size: 10px;
            margin-top: 12px;
            line-height: 1.35;
        }

        .receipt-footer-bold {
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .receipt-qr-wrap {
            margin: 10px auto 6px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .qr-placeholder {
            width: 80px;
            height: 80px;
            background: #ffffff;
            border: 2px solid #000;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-url-text {
            font-size: 8.5px;
            word-break: break-all;
            margin-top: 3px;
            color: #333;
        }

        /* Print Media Styles */
        @media print {
            @page {
                margin: 0;
                size: auto;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
                align-items: flex-start !important;
            }

            .thermal-actions {
                display: none !important;
            }

            .thermal-ticket {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 8px 6px 14px 6px !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Action Controls -->
    <div class="thermal-actions">
        <a href="{{ route('sales.invoice', $sale->id) . ($isBatch ? '?mode=batch' : '') }}" class="btn-action">
            <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Faktur A4
        </a>
        <div style="display: flex; gap: 6px;">
            <button type="button" class="btn-action" onclick="togglePaperSize()" id="paperSizeBtn">
                <i data-lucide="maximize-2" style="width: 14px; height: 14px;"></i> Format: 58mm
            </button>
            <a href="{{ $whatsAppUrl }}" target="_blank" class="btn-action btn-action-success">
                <i data-lucide="message-circle" style="width: 14px; height: 14px;"></i> Kirim WA
            </a>
            <button type="button" class="btn-action btn-action-primary" onclick="window.print()">
                <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak Struk
            </button>
        </div>
    </div>

    <!-- Physical Thermal Receipt Structure (58mm/80mm Compatible) -->
    <div class="thermal-ticket" id="thermalTicket">
        <!-- Logo Emblem -->
        <div class="receipt-logo-box">
            <i data-lucide="coffee" class="receipt-logo-icon"></i>
            <div class="receipt-logo-badge">HIKU HIMU</div>
        </div>

        <!-- Header Outlet -->
        <div class="receipt-header">
            <div class="receipt-store-title">Kopi Hiku Himu</div>
            <div class="receipt-store-sub">ARTISAN ROASTERY & MITRA</div>
            <div class="receipt-store-address">
                Jl. Roastery No. 8, Sleman, Yogyakarta<br>
                Telp/WA: 0812-3456-7890
            </div>
        </div>

        <hr class="receipt-divider">

        <!-- Transaction Meta -->
        <table class="receipt-meta-table">
            <tr>
                <td class="label-col">Reff No.</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ $invoiceNumber }}</td>
            </tr>
            <tr>
                <td class="label-col">Tanggal</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ $sale->tanggal ? $sale->tanggal->format('d-m-Y') : now()->format('d-m-Y') }} {{ now()->format('H:i:s') }}</td>
            </tr>
            <tr>
                <td class="label-col">Kasir</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ auth()->check() ? strtoupper(auth()->user()->name) : 'ADMIN' }}</td>
            </tr>
            <tr>
                <td class="label-col">Tipe Transaksi</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ $isBatch ? 'KONSINYASI (GABUNGAN)' : 'PENJUALAN KONSINYASI' }}</td>
            </tr>
            <tr>
                <td class="label-col">Toko / Mitra</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ strtoupper($sale->store->name ?? 'UMUM') }}</td>
            </tr>
            @if(!empty($sale->store->penanggung_jawab))
            <tr>
                <td class="label-col">Nama PJ</td>
                <td class="colon-col">:</td>
                <td class="val-col">{{ strtoupper($sale->store->penanggung_jawab) }}</td>
            </tr>
            @endif
        </table>

        <hr class="receipt-divider">

        <!-- Itemized Products -->
        <div class="receipt-items">
            @foreach($items as $item)
            <div class="item-row">
                <div class="item-desc">
                    <span class="item-qty">{{ $item->jumlah }}</span>
                    <span>{{ $item->coffeeType->name ?? 'Kopi' }}</span>
                </div>
                <div class="item-total">{{ number_format($item->total, 0, ',', '.') }}</div>
            </div>
            <div class="item-subtext">
                @ {{ number_format($item->harga, 0, ',', '.') }}
                @if(!empty($item->stockBatch?->kode_produksi))
                    | Batch: {{ $item->stockBatch->kode_produksi }}
                @endif
            </div>
            @endforeach
        </div>

        <hr class="receipt-divider">

        <!-- Totals & Payment -->
        <div class="calc-row">
            <div>Subtotal</div>
            <div style="font-weight: 700;">{{ number_format($grandTotal, 0, ',', '.') }}</div>
        </div>
        <div class="calc-row">
            <div>Diskon</div>
            <div>0</div>
        </div>
        <div class="calc-row total-row">
            <div>TOTAL</div>
            <div>Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
        </div>
        <div class="calc-row" style="margin-top: 4px; font-size: 11px;">
            <div>Pembayaran</div>
            <div style="font-weight: 700;">QRIS / CASH / LUNAS</div>
        </div>

        <hr class="receipt-divider-double">

        <!-- Thermal Footer (Ringkas & Hemat Kertas) -->
        <div class="receipt-footer">
            <div class="receipt-footer-bold">*** TERIMA KASIH ***</div>
            <div>Simpan biji/bubuk kopi di wadah sejuk & rapat</div>
            <div>Komplain kualitas maks 1x24 jam nota dibawa</div>
            
            <div class="receipt-qr-wrap">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode(route('sales.invoice', $sale->id) . ($isBatch ? '?mode=batch' : '')) }}" alt="QR E-Nota" style="width: 72px; height: 72px; margin-bottom: 2px;" onerror="this.style.display='none'">
                <div class="qr-url-text">E-Nota: kopihikuhimu.id</div>
            </div>
            <div style="font-size: 8.5px; color: #555; margin-top: 4px;">Dicetak: {{ now()->format('d/m/Y H:i') }} WIB</div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function togglePaperSize() {
            const ticket = document.getElementById('thermalTicket');
            const btn = document.getElementById('paperSizeBtn');
            if (ticket.classList.contains('paper-80mm')) {
                ticket.classList.remove('paper-80mm');
                btn.innerHTML = '<i data-lucide="maximize-2" style="width: 14px; height: 14px;"></i> Format: 58mm';
            } else {
                ticket.classList.add('paper-80mm');
                btn.innerHTML = '<i data-lucide="minimize-2" style="width: 14px; height: 14px;"></i> Format: 80mm';
            }
            lucide.createIcons();
        }

        // Auto print trigger if ?auto=1
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto') === '1') {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
