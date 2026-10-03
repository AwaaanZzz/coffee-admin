@include('sales.partials.doc-helpers')
@php
    $standardInvoiceNumber = 'INV/' . $sale->tanggal->format('Ymd') . '/' . str_pad($sale->id, 5, '0', STR_PAD_LEFT);
    $waText = view('sales.partials.whatsapp-text', [
        'sale' => $sale,
        'items' => $items,
        'grandTotal' => $grandTotal,
        'invoiceNumber' => $standardInvoiceNumber,
        'isBatch' => $isBatch,
    ])->render();
    $waUrl = 'https://api.whatsapp.com/send?text=' . urlencode($waText);
    $publicBase = config('business.public_url');
    if (empty($publicBase)) {
        $website = config('business.website', 'kopihikuhimu.id');
        $publicBase = 'https://' . preg_replace('#^https?://#', '', $website);
    }
    $publicBase = rtrim($publicBase, '/');
    $publicInvoiceUrl = $publicBase . '/sales/' . $sale->id . '/invoice' . ($isBatch ? '?mode=batch' : '');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $standardInvoiceNumber }} - {{ config('business.name') }}</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f1f5f9;
            color: #000000;
            font-family: 'JetBrains Mono', Courier, monospace;
            font-size: 11px;
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            border-radius: 6px;
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
            background: #1E3A5F;
            color: #ffffff;
            border-color: #1E3A5F;
        }

        .btn-action-primary:hover {
            background: #162a45;
            color: #ffffff;
        }

        .btn-action-success {
            background: #ffffff;
            color: #1e293b;
            border-color: #cbd5e1;
        }

        .btn-action-success:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        /* Thermal Ticket Container */
        .thermal-ticket {
            background: #ffffff;
            width: 300px; /* 58mm standard */
            max-width: 100%;
            padding: 16px 14px 20px 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            border-radius: 4px;
            color: #000000;
            position: relative;
            transition: width 0.2s ease;
        }

        .thermal-ticket.paper-80mm {
            width: 400px; /* 80mm standard */
        }

        /* Lineart Logo (Ink-saving, borderless clean icon mark) */
        .receipt-logo-lineart {
            width: 32px;
            height: 32px;
            margin: 0 auto 4px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000000;
            background: transparent;
        }

        .receipt-logo-icon {
            width: 24px;
            height: 24px;
            stroke-width: 1.8;
        }

        /* Store Header */
        .receipt-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .receipt-store-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 2px;
        }

        .receipt-store-address {
            font-size: 10px;
            line-height: 1.3;
            color: #222222;
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
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
            height: 3px;
            margin: 8px 0;
            width: 100%;
        }

        /* Meta Table */
        .receipt-meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 4px;
        }

        .receipt-meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .receipt-meta-table td.lbl {
            width: 85px;
            white-space: nowrap;
        }

        .receipt-meta-table td.sep {
            width: 10px;
            text-align: center;
        }

        .receipt-meta-table td.val {
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
            font-size: 11px;
            padding: 2px 0;
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
            font-weight: 600;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .item-subtext {
            font-size: 9.5px;
            color: #333333;
            padding-left: 18px;
            margin-bottom: 2px;
        }

        /* Calculation Section */
        .calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
        }

        .calc-row.total-row {
            font-size: 13px;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Footer */
        .receipt-footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            line-height: 1.4;
        }

        .receipt-thanks {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .receipt-policy {
            font-size: 9.5px;
            color: #333333;
            margin-bottom: 8px;
        }

        .receipt-qr-wrap {
            margin: 8px auto 4px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .receipt-qr-img {
            width: 72px;
            height: 72px;
            margin-bottom: 2px;
        }

        .qr-url-text {
            font-size: 8.5px;
            word-break: break-all;
            margin-top: 2px;
            color: #444444;
        }

        .receipt-print-time {
            font-size: 8.5px;
            color: #666666;
            margin-top: 4px;
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
        <div style="display: flex; gap: 6px; align-items: center;">
            @if(isset($storeSameDaySalesCount) && $storeSameDaySalesCount > 1)
                @if($isBatch)
                    <a href="{{ route('sales.thermal', $sale->id) }}" class="btn-action" title="Tampilkan hanya transaksi produk ini">
                        <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Struk tunggal
                    </a>
                @else
                    <a href="{{ route('sales.thermal', [$sale->id, 'mode' => 'batch']) }}" class="btn-action" style="background: #1E3A5F; color: #ffffff; border-color: #1E3A5F;" title="Gabungkan semua {{ $storeSameDaySalesCount }} transaksi toko hari ini dalam 1 struk">
                        <i data-lucide="layers" style="width: 14px; height: 14px;"></i> Gabung hari ini ({{ $storeSameDaySalesCount }})
                    </a>
                @endif
            @endif
            <button type="button" class="btn-action" onclick="togglePaperSize()" id="paperSizeBtn">
                <i data-lucide="maximize-2" style="width: 14px; height: 14px;"></i> Format: 58mm
            </button>
            <a href="{{ $waUrl }}" target="_blank" class="btn-action btn-action-success">
                <i data-lucide="message-circle" style="width: 14px; height: 14px;"></i> Kirim WhatsApp
            </a>
            <button type="button" class="btn-action btn-action-primary" onclick="window.print()">
                <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak struk
            </button>
        </div>
    </div>

    <!-- Physical Thermal Receipt Structure (58mm/80mm) -->
    <div class="thermal-ticket" id="thermalTicket">
        <!-- Lineart Logo (Ink-saving) -->
        <div class="receipt-logo-lineart">
            <i data-lucide="coffee" class="receipt-logo-icon"></i>
        </div>

        <!-- Outlet Header -->
        <div class="receipt-header">
            <div class="receipt-store-title">{{ config('business.name') }}</div>
            <div class="receipt-store-address">
                {{ config('business.address') }}<br>
                Telepon: {{ config('business.phone') }}
            </div>
        </div>

        <hr class="receipt-divider">

        <!-- Transaction Meta -->
        <table class="receipt-meta-table">
            <tr>
                <td class="lbl">No. Ref</td>
                <td class="sep">:</td>
                <td class="val">{{ $standardInvoiceNumber }}</td>
            </tr>
            <tr>
                <td class="lbl">Tanggal</td>
                <td class="sep">:</td>
                <td class="val">{{ formatDocDate($sale->tanggal) }} {{ formatDocTime($sale->created_at ?? $sale->tanggal) }}</td>
            </tr>
            <tr>
                <td class="lbl">Petugas</td>
                <td class="sep">:</td>
                <td class="val">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</td>
            </tr>
            <tr>
                <td class="lbl">Toko Mitra</td>
                <td class="sep">:</td>
                <td class="val">{{ $sale->store->name ?? 'Toko Mitra' }}</td>
            </tr>
            @if(!empty($sale->store->penanggung_jawab))
            <tr>
                <td class="lbl">Penanggung Jawab</td>
                <td class="sep">:</td>
                <td class="val">{{ $sale->store->penanggung_jawab }}</td>
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
                <div class="item-total">{{ formatDocRupiah($item->total) }}</div>
            </div>
            <div class="item-subtext">
                @ {{ formatDocRupiah($item->harga) }}
                @if(!empty($item->stockBatch?->kode_produksi))
                    Batch {{ $item->stockBatch->kode_produksi }}
                @endif
            </div>
            @endforeach
        </div>

        <hr class="receipt-divider">

        <!-- Totals & Payment -->
        <div class="calc-row">
            <div>Subtotal</div>
            <div>{{ formatDocRupiah($grandTotal) }}</div>
        </div>
        <div class="calc-row">
            <div>Diskon</div>
            <div>-</div>
        </div>
        <div class="calc-row total-row">
            <div>Total Tagihan</div>
            <div>{{ formatDocRupiah($grandTotal) }}</div>
        </div>
        <div class="calc-row" style="margin-top: 3px; font-size: 10px;">
            <div>Pembayaran</div>
            <div>Konsinyasi</div>
        </div>

        <hr class="receipt-divider-double">

        <!-- Thermal Footer -->
        <div class="receipt-footer">
            <div class="receipt-thanks">Terima kasih.</div>
            <div class="receipt-policy">Komplain maks. 1x24 jam sejak barang diterima.</div>

            <div class="receipt-qr-wrap">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($publicInvoiceUrl) }}" alt="QR Faktur" class="receipt-qr-img" onerror="this.style.display='none'">
                <div class="qr-url-text">E-Nota: {{ config('business.website') }}</div>
            </div>
            <div class="receipt-print-time">Dicetak: {{ formatDocDate(now()) }} {{ formatDocTime(now()) }}</div>
        </div>
    </div>

    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

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
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
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
