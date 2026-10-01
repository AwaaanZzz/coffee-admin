@extends('layouts.app')
@section('title', 'Stok Opname Mitra — Audit Lapangan')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Stok Opname Mitra</span>
    </div>
@endsection

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h3 class="page-title m-0">Stok Opname & Audit Konsinyasi</h3>
                <span class="badge bg-accent text-white px-2 py-1" style="font-size:0.75rem;">Lapangan</span>
            </div>
            <p class="page-subtitle mt-1 mb-0">Audit fisik berkala dan rekonsiliasi penjualan toko mitra.</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('stock.index') }}" class="btn btn-outline-modern">
                Kelola Stok Fisik
            </a>
        </div>
    </div>

    {{-- Section 1: Toko Mitra Ready for Audit --}}
    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h5 class="card-title-modern m-0">Pilih Toko Mitra</h5>
            <span class="badge bg-light text-muted border">{{ $stores->count() }} Toko</span>
        </div>
        <div class="card-body-modern p-4">
            <div class="row g-3">
                @forelse($stores as $store)
                    <div class="col-md-6 col-xl-4">
                        <div class="p-3 rounded-3 border h-100 d-flex flex-column justify-content-between" style="background: var(--bg-card); border-color: var(--border) !important;">
                            <div>
                                @php
                                    $lastOpname = $store->stockOpnames->first();
                                    $daysSinceAudit = $lastOpname ? (int) now()->diffInDays($lastOpname->tanggal_audit) : null;
                                @endphp
                                <div class="d-flex justify-content-between align-items-start mb-1.5">
                                    <h6 class="fw-bold text-dark m-0">
                                        {{ $store->name }}
                                    </h6>
                                    <span class="badge {{ $store->active_batches_count > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' }}" style="font-size:0.7rem;">
                                        {{ $store->active_batches_count }} batch
                                    </span>
                                </div>
                                <div class="mb-2.5">
                                    @if($lastOpname)
                                        @if($daysSinceAudit <= 7)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">
                                                Diaudit {{ $daysSinceAudit == 0 ? 'Hari ini' : $daysSinceAudit . ' hari lalu' }}
                                            </span>
                                        @elseif($daysSinceAudit <= 14)
                                            <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">
                                                Diaudit {{ $daysSinceAudit }} hari lalu
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem;">
                                                Perlu Audit ({{ $daysSinceAudit }} hr lalu)
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">
                                            Belum pernah diaudit
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted small mb-3 d-flex flex-column gap-1" style="font-size: 0.8rem;">
                                    @if($store->penanggung_jawab)
                                    <div>PJ: {{ $store->penanggung_jawab }}</div>
                                    @endif
                                    @if($store->alamat)
                                    <div class="text-truncate" style="max-width: 250px;">{{ $store->alamat }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="border-top pt-2.5">
                                <a href="{{ route('stock-opname.create', $store) }}" class="btn btn-sm btn-accent w-100 text-center fw-semibold py-1.5" style="letter-spacing: 0.2px;">
                                    Mulai Audit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <p class="mb-2">Belum ada toko mitra terdaftar.</p>
                        <a href="{{ route('stores.create') }}" class="btn btn-sm btn-accent">Tambah Toko Mitra</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Section 2: Riwayat Audit Stok Opname --}}
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h5 class="card-title-modern m-0">Riwayat Audit Stok Opname Terakhir</h5>
        </div>
        <div class="card-body-modern p-0">
            <div class="table-responsive">
                <table class="table-modern w-100 m-0 align-middle">
                    <thead>
                        <tr>
                            <th style="padding-left: 20px;">ID & Tgl Audit</th>
                            <th>Toko Mitra</th>
                            <th>Auditor</th>
                            <th class="text-center">Stok Sistem</th>
                            <th class="text-center">Fisik Ditemukan</th>
                            <th class="text-center">Terjual Otomatis</th>
                            <th class="text-end">Total Uang Penjualan</th>
                            <th class="text-end" style="padding-right: 20px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOpnames as $opname)
                            <tr>
                                <td style="padding-left: 20px;">
                                    <div class="fw-bold font-monospace text-dark">#OPN-{{ str_pad($opname->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    <small class="text-muted">{{ $opname->tanggal_audit->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        <span>{{ $opname->store->name ?? '-' }}</span>
                                    </div>
                                    <small class="text-muted" style="font-size:0.72rem;">PJ: {{ $opname->store->penanggung_jawab ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border" style="font-size:0.75rem;">
                                        {{ $opname->user->name ?? 'Admin' }}
                                    </span>
                                </td>
                                <td class="text-center tabular-nums">{{ $opname->total_stok_sistem }} pcs</td>
                                <td class="text-center tabular-nums fw-semibold text-info">{{ $opname->total_fisik_terhitung }} pcs</td>
                                <td class="text-center tabular-nums">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.78rem;">
                                        +{{ $opname->total_selisih_laku }} pcs laku
                                    </span>
                                </td>
                                <td class="text-end tabular-nums fw-bold text-primary">
                                    Rp {{ number_format($opname->total_nilai_penjualan, 0, ',', '.') }}
                                </td>
                                <td class="text-end" style="padding-right: 20px;">
                                    <a href="{{ route('stock-opname.receipt', $opname->id) }}" class="btn btn-sm btn-outline-modern">
                                        Berita Acara &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada catatan audit stok opname. Pilih salah satu toko mitra di atas untuk memulai audit perdana.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentOpnames->hasPages())
                <div class="p-3 border-top">
                    {{ $recentOpnames->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
