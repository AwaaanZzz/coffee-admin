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
    <div class="page-header">
        <div>
            <h3 class="page-title">Data Penjualan</h3>
            <p class="page-subtitle">Rekapitulasi penjualan kopi di semua toko mitra.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('sales.create') }}" class="btn btn-accent">
                <i data-lucide="plus"></i> Catat Penjualan
            </a>
        </div>
    </div>

    <div class="filter-bar mb-4">
        <form method="GET" class="d-flex align-items-center gap-3 bg-white p-3 rounded shadow-sm border">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="filter" class="text-muted"></i>
                <span class="fw-bold">Filter Toko:</span>
            </div>
            <select name="store_id" class="form-control-modern" style="max-width:260px;" onchange="this.form.submit()">
                <option value="">Semua Toko</option>
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
                            <th>Tanggal</th>
                            <th>Toko</th>
                            <th>Kopi</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Total</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr>
                                <td>{{ $sale->tanggal->format('d-m-Y') }}</td>
                                <td class="fw-bold">{{ $sale->store->name }}</td>
                                <td>{{ $sale->coffeeType->name }}</td>
                                <td>{{ $sale->jumlah }}</td>
                                <td>Rp {{ number_format($sale->harga, 0, ',', '.') }}</td>
                                <td class="fw-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Hapus data ini? Stock akan dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-table-action text-danger" title="Hapus">
                                            <i data-lucide="trash-2"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state text-center py-5">
                                        <i data-lucide="shopping-cart" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                        <h5>Belum ada data penjualan.</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $sales->links() }}
            </div>
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
