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
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-sm);
        padding: 2.5rem;
        border: 1px solid var(--border);
    }
    .receipt-header-divider {
        border-top: 1px dashed var(--border);
        margin: 1.5rem 0;
    }
    .receipt-table {
        color: #1e293b;
    }
    .receipt-table th {
        background: var(--bg-subtle);
        border-bottom: 1px solid var(--border);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: none;
        color: var(--text-secondary);
    }
    .receipt-table td {
        border-bottom: 1px solid var(--border);
        font-size: 0.8125rem;
    }
    .signature-box {
        border-top: 1px dashed var(--border);
        width: 180px;
        margin-top: 65px;
        text-align: center;
        padding-top: 6px;
        font-weight: 600;
        font-size: 0.8125rem;
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
        <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-modern d-flex align-items-center gap-2">
            <i data-lucide="arrow-left"></i> Kembali ke riwayat
        </a>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-accent d-flex align-items-center gap-2">
                <i data-lucide="printer"></i> Cetak berita acara
            </button>
        </div>
    </div>

    <!-- Official Printable Berita Acara -->
    <div class="receipt-card">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 44px; height: 44px; background: var(--bg-sidebar); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="coffee" style="color: #ffffff; width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">Kopi Hiku Himu</h4>
                    <span class="text-muted small">Roastery kopi & distribusi konsinyasi</span>
                </div>
            </div>
            <div class="text-end">
                <span class="badge-modern badge-success" style="font-size: 0.75rem;">Audit terverifikasi</span>
                <div class="font-monospace fw-bold mt-1 text-dark fs-5">#SO-{{ str_pad($stockOpname->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="text-muted small">{{ $stockOpname->created_at->format('d F Y, H:i') }} WIB</div>
            </div>
        </div>

        <div class="receipt-header-divider"></div>

        <!-- Meta Information -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="text-muted small fw-semibold mb-1">Mitra konsinyasi:</div>
                <h5 class="fw-bold text-dark mb-1">{{ $stockOpname->store->name }}</h5>
                <div class="text-muted small">
                    <i data-lucide="map-pin" style="width: 13px; height: 13px;"></i> {{ $stockOpname->store->alamat ?: 'Belum diisi' }}<br>
                    @if($stockOpname->store->penanggung_jawab)
                    <i data-lucide="user" style="width: 13px; height: 13px;"></i> PJ: {{ $stockOpname->store->penanggung_jawab }}
                    @endif
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="text-muted small fw-semibold mb-1">Auditor lapangan:</div>
                <h5 class="fw-bold text-dark mb-1">{{ $stockOpname->user->name ?? 'Admin Roastery' }}</h5>
                <div class="text-muted small">
                    Tanggal audit: <strong>{{ $stockOpname->tanggal_audit->format('d/m/Y') }}</strong><br>
                    Metode: <strong>Scan barcode lapangan</strong>
                </div>
            </div>
        </div>

        <!-- Table of Audited Items -->
        <div class="table-responsive mb-4">
            <table class="table receipt-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">No</th>
                        <th>Kode batch</th>
                        <th>Jenis kopi</th>
                        <th class="text-end">Stok awal</th>
                        <th class="text-end">Sisa rak</th>
                        <th class="text-end">Laku terjual</th>
                        <th class="text-end">Harga satuan</th>
                        <th class="text-end">Total laku</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOpname->items as $idx => $it)
                    <tr>
                        <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                        <td>
                            <span class="font-monospace fw-semibold text-dark">{{ $it->stockBatch->kode_produksi ?? 'Belum diisi' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $it->coffeeType->name ?? 'Belum diisi' }}</div>
                            @php $rcpCat = strtolower($it->coffeeType->category ?? 'robusta'); @endphp
                            @if($rcpCat === 'robusta')
                                <span class="badge-robusta" style="font-size: 0.7rem;">Robusta</span>
                            @elseif($rcpCat === 'arabika')
                                <span class="badge-arabika" style="font-size: 0.7rem;">Arabika</span>
                            @else
                                <span class="badge-neutral" style="font-size: 0.7rem;">{{ ucfirst($it->coffeeType->category ?? '-') }}</span>
                            @endif
                        </td>
                        <td class="text-end tabular-nums font-monospace">{{ $it->stok_sistem }}</td>
                        <td class="text-end tabular-nums font-monospace fw-semibold">{{ $it->fisik_terhitung }}</td>
                        <td class="text-end tabular-nums font-monospace fw-semibold text-success">
                            {{ $it->selisih_laku > 0 ? '+' . $it->selisih_laku : '0' }}
                        </td>
                        <td class="text-end tabular-nums font-monospace text-muted">
                            Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="text-end tabular-nums font-monospace fw-semibold text-dark">
                            Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: var(--bg-subtle); font-weight: 600;">
                        <td colspan="3" class="text-dark py-3">Total rekonsiliasi</td>
                        <td class="text-end tabular-nums font-monospace py-3">{{ $stockOpname->total_stok_sistem }}</td>
                        <td class="text-end tabular-nums font-monospace py-3">{{ $stockOpname->total_fisik_terhitung }} pcs</td>
                        <td class="text-end tabular-nums font-monospace py-3 text-success">{{ $stockOpname->total_selisih_laku }} pcs</td>
                        <td class="text-end py-3 text-muted">Total setor:</td>
                        <td class="text-end tabular-nums font-monospace fs-5 fw-bold text-dark py-3">
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
