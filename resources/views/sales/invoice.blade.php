@extends('layouts.app')

@include('sales.partials.doc-helpers')

@php
    $formattedWhatsAppText = view('sales.partials.whatsapp-text', compact('sale', 'items', 'grandTotal', 'invoiceNumber', 'isBatch'))->render();
    $draftReasons = getDocDraftReasons($sale, $items);
    $isDraft = count($draftReasons) > 0;
@endphp

@section('title', 'Faktur Penjualan #' . $invoiceNumber)

@section('breadcrumbs')
<a href="{{ route('dashboard') }}">Beranda</a>
<i data-lucide="chevron-right"></i>
<a href="{{ route('sales.index') }}">Data Penjualan</a>
<i data-lucide="chevron-right"></i>
<span>Faktur #{{ $invoiceNumber }}</span>
@endsection

@section('styles')
<style>
    /* Screen Preview Container */
    .invoice-container {
        max-width: 210mm;
        margin: 0 auto 3rem auto;
    }

    .invoice-sheet {
        background: #ffffff;
        color: #111827;
        font-family: 'Manrope', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 9pt;
        line-height: 1.45;
        border: 1px solid #d1d5db;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        padding: 15mm;
        position: relative;
        min-height: 297mm;
        border-radius: 0;
    }

    /* Conditional DRAFT Watermark */
    .draft-watermark {
        position: absolute;
        top: 48%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-28deg);
        font-size: 7.5rem;
        font-weight: 900;
        color: rgba(30, 58, 95, 0.05);
        letter-spacing: 12px;
        pointer-events: none;
        user-select: none;
        z-index: 0;
        white-space: nowrap;
    }

    .draft-notice {
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-left: 3px solid #64748b;
        padding: 8px 12px;
        font-size: 8.5pt;
        color: #475569;
        margin-bottom: 16px;
    }

    /* Kop Styling */
    .doc-kop {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
    }

    .doc-kop-left {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .doc-kop-logo {
        width: 52px;
        height: 52px;
        object-fit: contain;
        flex-shrink: 0;
    }

    .doc-kop-name {
        font-size: 13pt;
        font-weight: 800;
        color: #1E3A5F;
        margin: 0 0 2px 0;
        line-height: 1.2;
    }

    .doc-kop-tagline {
        font-size: 8.5pt;
        color: #4b5563;
        font-weight: 500;
        margin-bottom: 3px;
    }

    .doc-kop-meta {
        font-size: 8pt;
        color: #6b7280;
        line-height: 1.35;
    }

    .doc-kop-right {
        text-align: right;
    }

    .doc-kop-title {
        font-size: 18pt;
        font-weight: 800;
        color: #1E3A5F;
        line-height: 1;
        letter-spacing: -0.3px;
    }

    .doc-kop-line {
        height: 1.5px;
        background-color: #1E3A5F;
        margin-top: 12px;
        margin-bottom: 18px;
    }

    /* Meta Information Grid (No Boxes) */
    .doc-meta-grid {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 18px;
    }

    .meta-col-left {
        flex: 1;
    }

    .meta-col-right {
        flex: 1;
    }

    .meta-title {
        font-size: 8pt;
        font-weight: 700;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .meta-store-name {
        font-size: 11pt;
        font-weight: 700;
        color: #111827;
        margin-bottom: 2px;
    }

    .meta-store-info {
        font-size: 8.5pt;
        color: #4b5563;
        line-height: 1.4;
    }

    .meta-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5pt;
    }

    .meta-table td {
        padding: 2px 0;
        vertical-align: top;
    }

    .meta-table td.lbl {
        width: 130px;
        color: #6b7280;
    }

    .meta-table td.sep {
        width: 12px;
        color: #6b7280;
        text-align: center;
    }

    .meta-table td.val {
        color: #111827;
        font-weight: 600;
    }

    /* Items Table */
    .invoice-table-wrap {
        margin: 16px 0;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5pt;
    }

    .invoice-table thead th {
        background-color: #1E3A5F;
        color: #ffffff;
        font-weight: 700;
        font-size: 8pt;
        padding: 8px 10px;
        border: none;
        text-align: left;
    }

    .invoice-table thead th.text-center {
        text-align: center;
    }

    .invoice-table thead th.text-end {
        text-align: right;
    }

    .invoice-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
    }

    .invoice-table tbody td {
        padding: 9px 10px;
        vertical-align: top;
        color: #111827;
    }

    .item-name {
        font-weight: 600;
        color: #111827;
    }

    .item-cat {
        font-size: 7.5pt;
        color: #6b7280;
        margin-top: 1px;
    }

    .font-tabular {
        font-variant-numeric: tabular-nums;
    }

    .font-mono {
        font-family: 'JetBrains Mono', Consolas, monospace;
        font-size: 8pt;
    }

    /* Summary & Payment Section */
    .invoice-summary-section {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        margin-top: 14px;
        page-break-inside: avoid;
    }

    .summary-left {
        flex: 1.1;
    }

    .summary-right {
        flex: 0.9;
    }

    .section-label {
        font-size: 8pt;
        font-weight: 700;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .terbilang-text {
        font-size: 8.5pt;
        font-style: italic;
        color: #374151;
        margin-bottom: 14px;
    }

    .bank-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8pt;
    }

    .bank-table td {
        padding: 2px 0;
    }

    .bank-table td.lbl {
        width: 105px;
        color: #6b7280;
    }

    .bank-table td.sep {
        width: 12px;
        color: #6b7280;
        text-align: center;
    }

    .bank-table td.val {
        color: #111827;
        font-weight: 600;
    }

    .calc-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5pt;
    }

    .calc-table td {
        padding: 4px 0;
    }

    .calc-table td.calc-lbl {
        color: #4b5563;
    }

    .calc-table td.calc-val {
        text-align: right;
        font-weight: 600;
        color: #111827;
        font-variant-numeric: tabular-nums;
    }

    .calc-total-row td {
        padding-top: 8px;
        padding-bottom: 8px;
        font-size: 10pt;
        font-weight: 800;
        color: #1E3A5F;
        border-top: 1px solid #1E3A5F;
        border-bottom: 3px double #1E3A5F;
    }

    /* Terms */
    .doc-terms {
        margin-top: 24px;
        padding-top: 0;
        font-size: 7.5pt;
        color: #6b7280;
        page-break-inside: avoid;
    }

    .terms-title {
        font-weight: 700;
        margin-bottom: 4px;
        color: #4b5563;
    }

    .terms-list {
        margin: 0;
        padding-left: 16px;
        line-height: 1.45;
    }

    /* Signatures & Footer */
    .doc-closing {
        margin-top: 26px;
        page-break-inside: avoid;
    }

    .doc-thanks {
        font-size: 8.5pt;
        color: #374151;
        margin-bottom: 16px;
    }

    .doc-signatures {
        display: flex;
        justify-content: space-between;
        gap: 32px;
    }

    .signature-col {
        width: 200px;
    }

    .signature-col-right {
        text-align: left;
    }

    .signature-role {
        font-size: 8.5pt;
        color: #374151;
        font-weight: 500;
    }

    .signature-space {
        height: 52px;
    }

    .signature-line {
        border-top: 1px solid #111827;
        margin-bottom: 4px;
    }

    .signature-name {
        font-size: 8.5pt;
        font-weight: 700;
        color: #111827;
    }

    .signature-sub {
        font-size: 8pt;
        color: #6b7280;
    }

    .signature-date {
        font-size: 7.5pt;
        color: #6b7280;
        margin-top: 4px;
    }

    .doc-system-footer {
        margin-top: 22px;
        padding-top: 8px;
        border-top: 1px solid #e5e7eb;
        font-size: 7pt;
        color: #9ca3af;
        text-align: right;
    }

    /* Print Styles */
    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .sidebar, .topbar, .btn-no-print, .breadcrumbs, .scanner-status-pill, nav, .modal {
            display: none !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .invoice-container {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .invoice-sheet {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: auto !important;
        }

        .invoice-table thead {
            display: table-header-group;
        }

        .invoice-table tr {
            page-break-inside: avoid;
        }

        .invoice-summary-section, .doc-closing {
            page-break-inside: avoid;
        }

        .invoice-table thead th {
            background-color: #1E3A5F !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .doc-kop-line {
            background-color: #1E3A5F !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .calc-total-row td {
            color: #1E3A5F !important;
            border-top-color: #1E3A5F !important;
            border-bottom-color: #1E3A5F !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endsection

@section('content')
<div class="invoice-container">
    <!-- Screen Action Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 btn-no-print">
        <a href="{{ route('sales.index') }}" class="btn btn-outline-modern d-flex align-items-center gap-2">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            <span>Kembali ke data penjualan</span>
        </a>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($storeSameDaySalesCount > 1)
                @if($isBatch)
                    <a href="{{ route('sales.invoice', $sale->id) }}" class="btn btn-outline-modern btn-sm d-flex align-items-center gap-1.5" title="Tampilkan hanya transaksi ini">
                        <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
                        <span>Transaksi tunggal</span>
                    </a>
                @else
                    <a href="{{ route('sales.invoice', [$sale->id, 'mode' => 'batch']) }}" class="btn btn-outline-modern btn-sm d-flex align-items-center gap-1.5" title="Gabungkan semua transaksi toko hari ini">
                        <i data-lucide="layers" style="width: 14px; height: 14px;"></i>
                        <span>Gabung transaksi hari ini ({{ $storeSameDaySalesCount }})</span>
                    </a>
                @endif
            @endif

            <a href="{{ route('sales.thermal', $sale->id) . ($isBatch ? '?mode=batch' : '') }}" target="_blank" class="btn btn-outline-modern btn-sm d-flex align-items-center gap-1.5" title="Cetak struk thermal">
                <i data-lucide="receipt" style="width: 14px; height: 14px;"></i>
                <span>Struk thermal</span>
            </a>

            <button type="button" class="btn btn-outline-modern btn-sm d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#whatsappModal">
                <i data-lucide="message-circle" style="width: 14px; height: 14px;"></i>
                <span>Nota WhatsApp</span>
            </button>

            <button onclick="window.print()" class="btn btn-accent btn-sm d-flex align-items-center gap-2">
                <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                <span>Cetak A4 / PDF</span>
            </button>
        </div>
    </div>

    <!-- Official A4 Sheet Document -->
    <div class="invoice-sheet">
        {{-- Conditional Watermark & Draft Notice --}}
        @include('sales.partials.draft-notice')

        {{-- Kop Header --}}
        @include('sales.partials.kop')

        {{-- Meta Information Grid (No Boxes) --}}
        <div class="doc-meta-grid">
            <div class="meta-col-left">
                <div class="meta-title">Ditagihkan kepada:</div>
                <div class="meta-store-name">{{ $sale->store->name }}</div>
                <div class="meta-store-info {{ empty($sale->store->alamat) ? 'text-muted fst-italic' : '' }}">
                    {{ $sale->store->alamat ?: 'Alamat belum diisi' }}
                </div>
                @if(!empty($sale->store->penanggung_jawab))
                    <div class="meta-store-info">Penanggung jawab: {{ $sale->store->penanggung_jawab }}</div>
                @endif
            </div>

            <div class="meta-col-right">
                <table class="meta-table">
                    <tr>
                        <td class="lbl">Nomor faktur</td>
                        <td class="sep">:</td>
                        <td class="val font-tabular">{{ $invoiceNumber }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Tanggal</td>
                        <td class="sep">:</td>
                        <td class="val">{{ formatDocDate($sale->tanggal) }}</td>
                    </tr>
                    @if(!empty($sale->jatuh_tempo ?? null))
                    <tr>
                        <td class="lbl">Jatuh tempo</td>
                        <td class="sep">:</td>
                        <td class="val">{{ formatDocDate($sale->jatuh_tempo) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="lbl">Jenis kerja sama</td>
                        <td class="sep">:</td>
                        <td class="val">Konsinyasi</td>
                    </tr>
                    <tr>
                        <td class="lbl">Tanggal cetak</td>
                        <td class="sep">:</td>
                        <td class="val">{{ formatDocDateTime(now()) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="invoice-table-wrap">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 38px;">No</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 120px;">Kode batch</th>
                        <th class="text-center" style="width: 75px;">Jumlah</th>
                        <th class="text-end" style="width: 125px;">Harga satuan</th>
                        <th class="text-end" style="width: 135px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td class="text-center text-muted" style="font-size: 8pt;">{{ $index + 1 }}</td>
                        <td>
                            <div class="item-name">{{ $item->coffeeType->name ?? 'Kopi' }}</div>
                            <div class="item-cat">{{ ucfirst($item->coffeeType->category ?? 'Robusta') }}</div>
                        </td>
                        <td class="text-center font-mono">{{ $item->stockBatch->kode_produksi ?? '-' }}</td>
                        <td class="text-center font-tabular">{{ number_format($item->jumlah, 0, ',', '.') }} pcs</td>
                        <td class="text-end font-tabular">{{ formatDocRupiah($item->harga) }}</td>
                        <td class="text-end font-tabular">{{ formatDocRupiah($item->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary and Payment Details --}}
        <div class="invoice-summary-section">
            <div class="summary-left">
                <div class="section-label">Terbilang:</div>
                <div class="terbilang-text">
                    @if($isDraft || (float)$grandTotal <= 0)
                        -
                    @else
                        {{ $terbilang }}
                    @endif
                </div>

                <div class="section-label">Cara pembayaran:</div>
                @if(!empty(config('business.bank.name')) && !empty(config('business.bank.account_number')))
                <table class="bank-table">
                    <tr>
                        <td class="lbl">Bank</td>
                        <td class="sep">:</td>
                        <td class="val">{{ config('business.bank.name') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Nomor rekening</td>
                        <td class="sep">:</td>
                        <td class="val font-tabular">{{ config('business.bank.account_number') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Atas nama</td>
                        <td class="sep">:</td>
                        <td class="val">{{ config('business.bank.account_name') }}</td>
                    </tr>
                </table>
                @else
                <div class="text-muted fst-italic" style="font-size: 8.5pt;">Belum diisi</div>
                @endif
            </div>

            <div class="summary-right">
                <table class="calc-table">
                    <tr>
                        <td class="calc-lbl">Subtotal</td>
                        <td class="calc-val">{{ formatDocRupiah($grandTotal) }}</td>
                    </tr>
                    <tr>
                        <td class="calc-lbl">Diskon</td>
                        <td class="calc-val">-</td>
                    </tr>
                    @if(isset($ppn) && (float)$ppn > 0)
                    <tr>
                        <td class="calc-lbl">PPN</td>
                        <td class="calc-val">{{ formatDocRupiah($ppn) }}</td>
                    </tr>
                    @endif
                    <tr class="calc-total-row">
                        <td class="calc-lbl">Total Tagihan</td>
                        <td class="calc-val">{{ formatDocRupiah($grandTotal) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Terms and Conditions --}}
        <div class="doc-terms">
            <div class="terms-title">Syarat dan ketentuan:</div>
            <ol class="terms-list">
                @foreach(config('business.terms', []) as $term)
                    <li>{{ $term }}</li>
                @endforeach
            </ol>
        </div>

        {{-- Signatures and Closing --}}
        @include('sales.partials.signature')
    </div>
</div>

<!-- Modal Kirim Nota WhatsApp -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-color, #cbd5e1); box-shadow: var(--shadow-md);">
            <div class="modal-header" style="background: #1E3A5F; color: #ffffff; border-top-left-radius: var(--radius-sm, 8px); border-top-right-radius: var(--radius-sm, 8px);">
                <h5 class="modal-title d-flex align-items-center gap-2" id="whatsappModalLabel" style="font-size: 1rem; color: #ffffff;">
                    <i data-lucide="message-circle" style="width: 18px; height: 18px;"></i>
                    <span>Kirim nota via WhatsApp</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Nomor WhatsApp tujuan (opsional):</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted">+62</span>
                        <input type="text" id="waRecipientPhone" class="form-control" placeholder="Contoh: 81234567890 (kosongkan jika ingin memilih kontak langsung di WA)">
                    </div>
                    <div class="form-text text-muted" style="font-size: 0.78rem;">Masukkan nomor tujuan tanpa angka 0 di depan, atau langsung klik tombol kirim untuk memilih kontak di aplikasi WhatsApp.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">
                        <span>Pratinjau pesan nota WhatsApp:</span>
                    </label>
                    <textarea id="waMessageText" class="form-control font-monospace" rows="12" style="font-size: 0.82rem; background: #f8fafc; border: 1px solid #cbd5e1; white-space: pre-wrap;" readonly>{{ $formattedWhatsAppText }}</textarea>
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-bottom-left-radius: var(--radius-sm, 8px); border-bottom-right-radius: var(--radius-sm, 8px);">
                <button type="button" class="btn btn-outline-modern" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-outline-modern d-flex align-items-center gap-1.5" onclick="copyWhatsAppMessage()">
                    <i data-lucide="copy" style="width: 14px; height: 14px;"></i>
                    <span>Salin teks nota</span>
                </button>
                <button type="button" class="btn btn-accent d-flex align-items-center gap-1.5" onclick="sendWhatsAppNow()">
                    <i data-lucide="send" style="width: 14px; height: 14px;"></i>
                    <span>Buka WhatsApp & kirim</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Auto print trigger if ?auto=1 is passed
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto') === '1') {
            setTimeout(function() {
                window.print();
            }, 600);
        }
    });

    function copyWhatsAppMessage() {
        const text = document.getElementById('waMessageText').value;
        navigator.clipboard.writeText(text).then(() => {
            alert('Teks nota WhatsApp berhasil disalin ke clipboard!');
        }).catch(err => {
            console.error('Gagal menyalin:', err);
        });
    }

    function sendWhatsAppNow() {
        let phone = document.getElementById('waRecipientPhone').value.trim();
        const text = document.getElementById('waMessageText').value;
        if (phone) {
            if (phone.startsWith('0')) {
                phone = '62' + phone.substring(1);
            } else if (!phone.startsWith('62')) {
                phone = '62' + phone;
            }
            phone = phone.replace(/[^0-9]/g, '');
            window.open('https://api.whatsapp.com/send?phone=' + phone + '&text=' + encodeURIComponent(text), '_blank');
        } else {
            window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
        }
    }
</script>
@endsection
