@extends('layouts.app')

@section('title', 'Berita Acara Stok Opname #' . $stockOpname->id)

@section('breadcrumbs')
<a href="{{ route('dashboard') }}">Dashboard</a>
<i data-lucide="chevron-right"></i>
<a href="{{ route('stock-opname.index') }}">Stok Opname Mitra</a>
<i data-lucide="chevron-right"></i>
<span>Berita Acara #{{ $stockOpname->id }}</span>
@endsection

@section('styles')
<style>
    .receipt-container {
        max-width: 860px;
        margin: 0 auto 3rem auto;
    }
    .receipt-card {
        background: #fff;
        color: #1e293b;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        padding: 3rem;
        border: 1px solid #e2e8f0;
    }
    .receipt-header-divider {
        border-top: 2px dashed #cbd5e1;
        margin: 1.5rem 0;
    }
    .receipt-table {
        color: #1e293b;
    }
    .receipt-table th {
        background: #f8fafc;
        border-bottom: 2px solid #cbd5e1;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #475569;
    }
    .receipt-table td {
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.9rem;
    }
    .signature-box {
        border-top: 1px dashed #94a3b8;
        width: 180px;
        margin-top: 65px;
        text-align: center;
        padding-top: 6px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
        }
        .sidebar, .topbar, .btn-no-print, .breadcrumbs, .scanner-status-pill {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .receipt-card {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    }
</style>
@endsection

@section('content')
<div class="receipt-container">
    <!-- Top Action Buttons (Hidden on Print) -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 btn-no-print">
        <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-light d-flex align-items-center gap-2">
            <i data-lucide="arrow-left"></i> Kembali ke Riwayat
        </a>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-primary d-flex align-items-center gap-2">
                <i data-lucide="printer"></i> Cetak Berita Acara
            </button>
        </div>
    </div>

    <!-- Official Printable Berita Acara -->
    <div class="receipt-card">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 54px; height: 54px; background: #0f172a; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="coffee" style="color: #f59e0b; width: 30px; height: 30px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">KOPI HIKU HIMU</h4>
                    <span class="text-muted small text-uppercase" style="letter-spacing: 1px;">Artisan Coffee Roastery & Konsinyasi</span>
                </div>
            </div>
            <div class="text-end">
                <span class="badge bg-success px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.75rem;">Audit Terverifikasi</span>
                <div class="font-monospace fw-bold mt-1 text-dark fs-5">#SO-{{ str_pad($stockOpname->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="text-muted small">{{ $stockOpname->created_at->format('d F Y, H:i') }} WIB</div>
            </div>
        </div>

        <div class="receipt-header-divider"></div>

        <!-- Meta Information -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Mitra Konsinyasi:</div>
                <h5 class="fw-bold text-dark mb-1">{{ $stockOpname->store->name }}</h5>
                <div class="text-muted small">
                    <i data-lucide="map-pin" style="width: 13px; height: 13px;"></i> {{ $stockOpname->store->alamat ?? 'Alamat Mitra' }}<br>
                    @if($stockOpname->store->penanggung_jawab)
                    <i data-lucide="user" style="width: 13px; height: 13px;"></i> PJ: {{ $stockOpname->store->penanggung_jawab }}
                    @endif
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Auditor Lapangan:</div>
                <h5 class="fw-bold text-dark mb-1">{{ $stockOpname->user->name ?? 'Admin Roastery' }}</h5>
                <div class="text-muted small">
                    Tanggal Audit: <strong>{{ $stockOpname->tanggal_audit->format('d/m/Y') }}</strong><br>
                    Metode: <strong>Scan Barcode Lapangan</strong>
                </div>
            </div>
        </div>

        <!-- Table of Audited Items -->
        <div class="table-responsive mb-4">
            <table class="table receipt-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">No</th>
                        <th>Kode Batch</th>
                        <th>Varian Kopi</th>
                        <th class="text-center">Stok Awal</th>
                        <th class="text-center">Sisa Rak</th>
                        <th class="text-center">Laku Terjual</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end">Total Laku</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOpname->items as $idx => $it)
                    <tr>
                        <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                        <td>
                            <span class="font-monospace fw-semibold text-dark">{{ $it->stockBatch->kode_produksi ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $it->coffeeType->name ?? '-' }}</div>
                            <span class="text-muted small text-uppercase" style="font-size: 0.75rem;">{{ $it->coffeeType->category ?? 'robusta' }}</span>
                        </td>
                        <td class="text-center font-monospace">{{ $it->stok_sistem }}</td>
                        <td class="text-center font-monospace fw-bold text-primary">{{ $it->fisik_terhitung }}</td>
                        <td class="text-center font-monospace fw-bold {{ $it->selisih_laku > 0 ? 'text-success' : 'text-muted' }}">
                            {{ $it->selisih_laku > 0 ? '+' . $it->selisih_laku : '0' }}
                        </td>
                        <td class="text-end font-monospace text-muted">
                            Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="text-end font-monospace fw-bold text-dark">
                            Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc; font-weight: 700;">
                        <td colspan="3" class="text-uppercase text-dark py-3">Total Rekonsiliasi</td>
                        <td class="text-center font-monospace py-3">{{ $stockOpname->total_stok_sistem }}</td>
                        <td class="text-center font-monospace py-3 text-primary">{{ $stockOpname->total_fisik_terhitung }} pcs</td>
                        <td class="text-center font-monospace py-3 text-success">{{ $stockOpname->total_selisih_laku }} pcs</td>
                        <td class="text-end py-3 text-muted">Total Setor:</td>
                        <td class="text-end font-monospace fs-5 text-success py-3">
                            Rp {{ number_format($stockOpname->total_nilai_penjualan, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($stockOpname->catatan)
        <div class="p-3 rounded-3 mb-4" style="background: #f1f5f9; border-left: 4px solid #3b82f6;">
            <div class="text-muted small text-uppercase fw-bold mb-1">Catatan Audit:</div>
            <div class="text-dark small">{{ $stockOpname->catatan }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="d-flex justify-content-between align-items-end mt-5 pt-3">
            <div>
                <div class="text-muted small">Pihak Toko Mitra:</div>
                <div class="signature-box">
                    ( {{ $stockOpname->store->name }} )
                </div>
            </div>
            <div>
                <div class="text-muted small text-end">Auditor Kopi Hiku Himu:</div>
                <div class="signature-box ms-auto">
                    ( {{ $stockOpname->user->name ?? 'Tim Roastery' }} )
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
