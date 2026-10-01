@extends('layouts.app')

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
    .invoice-wrapper {
        max-width: 860px;
        margin: 0 auto 3rem auto;
    }
    .invoice-paper {
        background: #ffffff;
        color: var(--text-main, #1e293b);
        border-radius: var(--radius-sm, 8px);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color, #e2e8f0);
        padding: 3rem;
        position: relative;
    }
    .invoice-watermark {
        position: absolute;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-25deg);
        font-size: 5rem;
        font-weight: 800;
        color: rgba(15, 23, 42, 0.02);
        pointer-events: none;
        user-select: none;
        letter-spacing: 6px;
        white-space: nowrap;
    }
    .invoice-brand-title {
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: var(--navy, #1E3A5F);
        line-height: 1.2;
    }
    .invoice-title {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: var(--navy, #1E3A5F);
    }
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }
    .invoice-table th {
        background: var(--bg-input, #f8fafc);
        border-bottom: 2px solid var(--border-color, #cbd5e1);
        border-top: 1px solid var(--border-color, #e2e8f0);
        font-weight: 600;
        font-size: 0.78rem;
        letter-spacing: 0.3px;
        color: var(--text-muted, #475569);
        padding: 10px 14px;
    }
    .invoice-table td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--border-color, #f1f5f9);
        font-size: 0.9rem;
        color: var(--text-main, #1e293b);
    }
    .invoice-table tbody tr:hover {
        background-color: var(--bg-hover, #f8fafc);
    }
    .invoice-summary-box {
        background: var(--bg-card, #f8fafc);
        border-radius: var(--radius-sm, 8px);
        border: 1px solid var(--border-color, #e2e8f0);
        padding: 1.25rem 1.5rem;
    }
    .invoice-total-highlight {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--accent, #C88A4E);
        font-variant-numeric: tabular-nums;
    }
    .invoice-terbilang-box {
        background: var(--bg-card, #f8fafc);
        border-left: 3px solid var(--navy, #1E3A5F);
        padding: 0.75rem 1rem;
        border-radius: 4px;
        font-size: 0.85rem;
        color: var(--text-main, #334155);
    }
    .signature-area {
        margin-top: 3.5rem;
    }
    .signature-line {
        border-top: 1px solid var(--border-color, #94a3b8);
        width: 190px;
        padding-top: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        text-align: center;
        color: var(--text-main, #0f172a);
    }
    .verified-seal {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: var(--radius-sm, 8px);
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(46, 125, 50, 0.08);
        color: var(--success, #2e7d32);
        border: 1px solid rgba(46, 125, 50, 0.25);
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm 12mm 12mm 12mm;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 10pt;
        }
        .sidebar, .topbar, .btn-no-print, .breadcrumbs, .scanner-status-pill, nav {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .invoice-wrapper {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .invoice-paper {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 0 !important;
        }
        .invoice-watermark {
            display: none !important;
        }
        .invoice-table th {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .invoice-summary-box, .invoice-terbilang-box {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endsection

@section('content')
<div class="invoice-wrapper">
    <!-- Top Action Bar (Screen Only) -->
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

            <a href="{{ route('sales.thermal', $sale->id) . ($isBatch ? '?mode=batch' : '') }}" target="_blank" class="btn btn-outline-modern btn-sm d-flex align-items-center gap-1.5" title="Cetak struk ukuran 58mm / 80mm ala kasir POS">
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

    <!-- Official Printable Invoice Document -->
    <div class="invoice-paper">
        <div class="invoice-watermark">KOPI HIKU HIMU</div>

        <!-- Top Header & Brand -->
        <div class="d-flex justify-content-between align-items-start pb-4 border-bottom">
            <div class="d-flex align-items-start gap-3">
                <div style="width: 48px; height: 48px; background: #0f172a; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i data-lucide="coffee" style="color: #f59e0b; width: 26px; height: 26px;"></i>
                </div>
                <div>
                    <h1 class="invoice-brand-title mb-1">KOPI HIKU HIMU</h1>
                    <div class="text-muted small" style="line-height: 1.4;">
                        Artisan Roastery & Mitra Konsinyasi Kopi Berkualitas<br>
                        Jl. Roastery No. 8, Indonesia &bull; Telp / WA: 0812-3456-7890<br>
                        Email: halo@kopihikuhimu.id
                    </div>
                </div>
            </div>

            <div class="text-end">
                <div class="invoice-title text-uppercase">FAKTUR PENJUALAN</div>
                <div class="text-muted small">COMMERCIAL SALES INVOICE</div>
                <div class="mt-2">
                    <span class="verified-seal">
                        <i data-lucide="check-circle" style="width: 13px; height: 13px;"></i> LUNAS / TERCATAT
                    </span>
                </div>
            </div>
        </div>

        <!-- Invoice Meta Details & Billing Grid -->
        <div class="row g-4 py-4 border-bottom">
            <!-- Customer / Mitra Info -->
            <div class="col-6">
                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Ditagihkan Kepada (Bill To):</div>
                <h5 class="fw-bold text-dark mb-1">{{ $sale->store->name }}</h5>
                <div class="text-secondary small" style="line-height: 1.5;">
                    <i data-lucide="map-pin" style="width: 13px; height: 13px; vertical-align: -2px;"></i> {{ $sale->store->alamat ?? 'Alamat Mitra Belum Diisi' }}<br>
                    @if($sale->store->penanggung_jawab)
                    <i data-lucide="user" style="width: 13px; height: 13px; vertical-align: -2px;"></i> Kontak / PJ: <strong>{{ $sale->store->penanggung_jawab }}</strong><br>
                    @endif
                    Tipe Kerjasama: <strong>Konsinyasi Kopi</strong>
                </div>
            </div>

            <!-- Invoice Specifics -->
            <div class="col-6">
                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Rincian Dokumen:</div>
                <table class="w-100 text-secondary small" style="line-height: 1.7;">
                    <tr>
                        <td style="width: 140px;">No. Faktur</td>
                        <td style="width: 15px;">:</td>
                        <td class="fw-bold text-dark font-monospace">{{ $invoiceNumber }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Transaksi</td>
                        <td>:</td>
                        <td class="fw-bold text-dark">{{ $sale->tanggal->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Cetak</td>
                        <td>:</td>
                        <td>{{ now()->format('d/m/Y, H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td>Metode Transaksi</td>
                        <td>:</td>
                        <td><span class="badge bg-light text-dark border">Penjualan Konsinyasi</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Product Table -->
        <div class="table-responsive">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 45px;">No</th>
                        <th>Deskripsi produk / varian kopi</th>
                        <th class="text-center" style="width: 130px;">Kode batch</th>
                        <th class="text-center" style="width: 80px;">Qty</th>
                        <th class="text-end" style="width: 130px;">Harga satuan</th>
                        <th class="text-end" style="width: 140px;">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td class="text-center text-muted small">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->coffeeType->name ?? '-' }}</div>
                            <div class="text-muted small text-uppercase" style="font-size: 0.72rem;">
                                Kategori: {{ $item->coffeeType->category ?? 'Robusta' }}
                            </div>
                        </td>
                        <td class="text-center font-monospace small">
                            <span class="badge bg-light text-dark border font-monospace">
                                {{ $item->stockBatch->kode_produksi ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center font-monospace fw-bold" style="font-variant-numeric: tabular-nums;">{{ $item->jumlah }} pcs</td>
                        <td class="text-end font-monospace" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-end font-monospace fw-bold text-dark" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Terbilang & Calculation Summary Block -->
        <div class="row g-4 mt-1 align-items-start">
            <div class="col-7">
                <div class="invoice-terbilang-box mb-3">
                    <div class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Terbilang:</div>
                    <div class="fst-italic fw-semibold text-dark">{{ $terbilang }}</div>
                </div>

                <div class="text-secondary small" style="line-height: 1.5;">
                    <div class="fw-bold text-dark mb-1">Catatan & informasi pembayaran:</div>
                    <div>&bull; Pembayaran via transfer Bank: <strong>BCA 123-456-7890</strong> a.n. Kopi Hiku Himu</div>
                    <div>&bull; Harap konfirmasi bukti transfer via WhatsApp ke nomor kasir resmi roastery.</div>
                    <div>&bull; Barang titip konsinyasi terjamin kesegaran kualitasnya hingga tanggal kedaluwarsa.</div>
                </div>
            </div>

            <div class="col-5">
                <div class="invoice-summary-box">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Total kuantitas:</span>
                        <span class="font-monospace fw-bold text-dark" style="font-variant-numeric: tabular-nums;">{{ number_format($totalQty) }} pcs</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Subtotal penjualan:</span>
                        <span class="font-monospace fw-bold text-dark" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Diskon / potongan:</span>
                        <span class="font-monospace text-muted">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small pb-2 border-bottom">
                        <span class="text-muted">Pajak pertambahan nilai (PPN):</span>
                        <span class="font-monospace text-muted">0% (Bebas)</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark" style="font-size: 0.95rem;">TOTAL TAGIHAN:</span>
                        <span class="invoice-total-highlight">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Official Signatures -->
        <div class="d-flex justify-content-between align-items-end signature-area">
            <div>
                <div class="text-muted small mb-1">Diterima & diverifikasi oleh:</div>
                <div class="signature-line" style="margin-top: 60px;">
                    ( {{ $sale->store->penanggung_jawab ?: $sale->store->name }} )
                </div>
                <div class="text-muted small text-center mt-1">Pihak toko mitra</div>
            </div>

            <div class="text-center">
                <div class="text-muted small" style="font-size: 0.75rem;">
                    Dokumen ini sah dan dicetak dari Sistem ERP Kopi Hiku Himu.
                </div>
            </div>

            <div class="text-end">
                <div class="text-muted small mb-1">Hormat kami,</div>
                <div class="signature-line ms-auto" style="margin-top: 60px;">
                    ( Admin Kopi Hiku Himu )
                </div>
                <div class="text-muted small text-center mt-1 ms-auto" style="width: 190px;">Roastery & Supplier</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kirim Nota WhatsApp -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-color, #cbd5e1); box-shadow: var(--shadow-md);">
            <div class="modal-header" style="background: var(--navy, #1E3A5F); color: #ffffff; border-top-left-radius: var(--radius-sm, 8px); border-top-right-radius: var(--radius-sm, 8px);">
                <h5 class="modal-title d-flex align-items-center gap-2" id="whatsappModalLabel" style="font-size: 1rem; color: #ffffff;">
                    <i data-lucide="message-circle" style="width: 18px; height: 18px;"></i>
                    <span>Kirim nota elektronik via WhatsApp</span>
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
                    <label class="form-label fw-bold text-dark small d-flex justify-content-between align-items-center">
                        <span>Pratinjau pesan nota WhatsApp:</span>
                        <span class="badge bg-light text-muted border font-monospace">Formatted WA Markdown</span>
                    </label>
                    <textarea id="waMessageText" class="form-control font-monospace" rows="12" style="font-size: 0.82rem; background: var(--bg-card, #f8fafc); border: 1px solid var(--border-color, #cbd5e1); white-space: pre-wrap;" readonly>{{ $whatsAppText }}</textarea>
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
            alert('Teks Nota WhatsApp berhasil disalin ke clipboard!');
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
