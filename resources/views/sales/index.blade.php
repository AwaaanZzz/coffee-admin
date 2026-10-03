@extends('layouts.app')
@section('title', 'Penjualan')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Data Penjualan</span>
    </div>
@endsection

@section('content')
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="page-title mb-1">Data penjualan</h3>
            <p class="page-subtitle text-muted mb-0">Rekapitulasi transaksi penjualan kopi di semua toko mitra.</p>
        </div>
        <div class="page-actions d-flex align-items-center gap-2">
            <a href="{{ route('exports.index') }}" class="btn btn-outline-modern d-flex align-items-center gap-2">
                <i data-lucide="file-spreadsheet" style="width: 16px; height: 16px;"></i>
                <span>Ekspor Excel & PDF</span>
            </a>
            <a href="{{ route('sales.create') }}" class="btn btn-accent d-flex align-items-center gap-2">
                <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                <span>Catat penjualan</span>
            </a>
        </div>
    </div>

    <div class="card-modern p-3 mb-4">
        <form method="GET" class="d-flex flex-wrap align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 text-muted small fw-semibold">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter toko mitra:</span>
            </div>
            <select name="store_id" class="form-control-modern form-select form-select-sm" style="max-width:240px;" onchange="this.form.submit()">
                <option value="">Semua toko mitra</option>
                @foreach ($stores as $s)
                    <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="card-modern">
        <div class="card-body-modern p-0">
            <div class="table-responsive">
                <table class="table-modern w-100 m-0">
                    <thead>
                        <tr>
                            <th style="min-width: 110px;">Tanggal</th>
                            <th>Toko mitra</th>
                            <th>Varian kopi</th>
                            <th class="text-end" style="min-width: 80px;">Jumlah</th>
                            <th class="text-end" style="min-width: 120px;">Harga satuan</th>
                            <th class="text-end" style="min-width: 130px;">Total</th>
                            <th class="text-end" style="min-width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            @php
                                $key = $sale->store_id . '_' . $sale->tanggal->format('Y-m-d');
                                $sameDayTotal = $sameDayCounts[$key] ?? 1;
                            @endphp
                            <tr>
                                <td class="text-muted" style="font-variant-numeric: tabular-nums;">{{ $sale->tanggal->format('d/m/Y') }}</td>
                                <td class="fw-semibold text-main">
                                    {{ $sale->store->name }}
                                    @if($sameDayTotal > 1)
                                        <span class="badge bg-light text-primary border ms-1" style="font-size: 0.72rem; font-weight: 500;" title="Ada {{ $sameDayTotal }} produk terjual untuk toko ini pada tanggal ini">
                                            {{ $sameDayTotal }} produk
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $sale->coffeeType->name }}</td>
                                <td class="text-end fw-semibold" style="font-variant-numeric: tabular-nums;">{{ number_format($sale->jumlah, 0, ',', '.') }}</td>
                                <td class="text-end text-muted" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($sale->harga, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold" style="font-variant-numeric: tabular-nums; color: var(--text-main);">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        @if($sameDayTotal > 1)
                                            <a href="{{ route('sales.thermal', [$sale, 'mode' => 'batch']) }}" target="_blank" class="btn btn-table-action text-primary" title="Cetak struk thermal gabungan ({{ $sameDayTotal }} produk)">
                                                <i data-lucide="layers"></i>
                                            </a>
                                            <a href="{{ route('sales.invoice', [$sale, 'mode' => 'batch']) }}" target="_blank" class="btn btn-table-action text-primary" title="Cetak faktur A4 gabungan ({{ $sameDayTotal }} produk)">
                                                <i data-lucide="files"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('sales.thermal', $sale) }}" target="_blank" class="btn btn-table-action" title="Cetak struk thermal varian ini (58/80mm)">
                                            <i data-lucide="receipt"></i>
                                        </a>
                                        <a href="{{ route('sales.invoice', $sale) }}" target="_blank" class="btn btn-table-action" title="Faktur A4 & nota WhatsApp varian ini">
                                            <i data-lucide="printer"></i>
                                        </a>
                                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Hapus data penjualan ini? Stok akan dikembalikan.')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-table-action text-danger" title="Hapus transaksi">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state text-center py-5">
                                        <i data-lucide="shopping-cart" class="text-muted mb-2 opacity-50" style="width: 40px; height: 40px;"></i>
                                        <p class="text-muted mb-0 small">Belum ada data penjualan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($sales->hasPages())
                <div class="p-3 border-top">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();
    });
</script>
@endsection
