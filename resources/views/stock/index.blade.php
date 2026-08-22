@extends('layouts.app')
@section('title', 'Stock')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Data Stock</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Kelola Stock</h3>
            <p class="page-subtitle">Pantau dan atur ketersediaan stock di setiap toko.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('stock.create') }}" class="btn btn-accent">
                <i data-lucide="plus"></i> Tambah Stock
            </a>
        </div>
    </div>

    <div class="filter-bar mb-4">
        <form method="GET" class="d-flex align-items-center gap-3 bg-white p-3 rounded shadow-sm border">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="filter" class="text-muted"></i>
                <span class="fw-bold">Filter:</span>
            </div>
            <select name="store_id" class="form-control-modern" style="max-width: 260px;" onchange="this.form.submit()">
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
                            <th>Kode Produksi</th>
                            <th>Toko</th>
                            <th>Kopi</th>
                            <th>Tgl Stock</th>
                            <th>Tgl Exp</th>
                            <th>Stock</th>
                            <th>Laku</th>
                            <th>Sisa</th>
                            <th>Total (Rp)</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockBatches as $batch)
                            <tr style="{{ $batch->is_expired ? 'background: rgba(220,38,38,0.1);' : ($batch->is_expiring_soon ? 'background: rgba(245,158,11,0.1);' : '') }}">
                                <td class="fw-bold">{{ $batch->kode_produksi }}</td>
                                <td>{{ $batch->store->name }}</td>
                                <td>
                                    {{ $batch->coffeeType->name }}
                                    <span class="badge-modern {{ $batch->coffeeType->category === 'robusta' ? 'badge-info' : 'badge-warning' }} ms-1">
                                        {{ ucfirst($batch->coffeeType->category) }}
                                    </span>
                                </td>
                                <td>{{ $batch->tgl_stock->format('d-m-Y') }}</td>
                                <td>
                                    {{ $batch->tgl_exp->format('d-m-Y') }}
                                    @if ($batch->is_expired)
                                        <span class="badge-modern badge-danger ms-1">❌ Expired</span>
                                    @elseif ($batch->is_expiring_soon)
                                        <span class="badge-modern badge-warning ms-1">⚠️ Segera Exp</span>
                                    @endif
                                </td>
                                <td>{{ $batch->jumlah_stock }}</td>
                                <td>{{ $batch->laku }}</td>
                                <td class="fw-bold">{{ $batch->sisa }}</td>
                                <td>Rp {{ number_format($batch->total, 0, ',', '.') }}</td>
                                <td>
                                    @if ($batch->status === 'normal')
                                        <span class="badge-modern bg-success text-white">Normal</span>
                                    @elseif ($batch->status === 'tarik')
                                        <span class="badge-modern bg-danger text-white">Ditarik</span>
                                    @else
                                        <span class="badge-modern bg-info text-dark">Diganti</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('stock.edit', $batch) }}" class="btn btn-sm btn-outline-modern" title="Update">
                                            <i data-lucide="edit"></i>
                                        </a>
                                        <form action="{{ route('stock.tambah', $batch) }}" method="POST" class="d-flex gap-1" onsubmit="return confirmTambah(event, this)">
                                            @csrf
                                            <input type="number" name="jumlah_tambahan" min="1" class="form-control-modern form-control-sm" style="width:70px; height: 32px;" placeholder="qty">
                                            <button type="submit" class="btn btn-sm btn-outline-modern" title="Tambah Stock"><i data-lucide="plus"></i></button>
                                        </form>
                                        <form action="{{ route('stock.destroy', $batch) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-table-action text-danger" title="Hapus"><i data-lucide="trash-2"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state text-center py-5">
                                        <i data-lucide="package" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                        <h5>Belum ada data stock.</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $stockBatches->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();
    });

    function confirmTambah(e, form) {
        const qty = form.querySelector('input[name=jumlah_tambahan]').value;
        if (!qty || qty < 1) {
            e.preventDefault();
            alert('Isi jumlah tambahan stock dulu.');
            return false;
        }
        return true;
    }
</script>
@endsection
