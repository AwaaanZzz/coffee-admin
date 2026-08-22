@extends('layouts.app')
@section('title', 'Laporan Keuangan')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Laporan Keuangan</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Laporan Keuangan</h3>
            <p class="page-subtitle">Pantau pemasukan dan pengeluaran per toko.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('finance.create') }}" class="btn btn-accent">
                <i data-lucide="plus"></i> Buat Laporan
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
                            <th>Toko</th>
                            <th>Periode</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th>Laba/Rugi</th>
                            <th>Catatan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $r)
                            <tr class="{{ $r->laba >= 0 ? 'table-success-tint' : 'table-danger-tint' }}">
                                <td class="fw-bold">{{ $r->store->name }}</td>
                                <td>{{ $r->periode_awal->format('d-m-Y') }} &ndash; {{ $r->periode_akhir->format('d-m-Y') }}</td>
                                <td>Rp {{ number_format($r->pemasukan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($r->pengeluaran, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge-modern {{ $r->laba >= 0 ? 'badge-profit bg-success text-white' : 'badge-loss bg-danger text-white' }}">
                                        Rp {{ number_format($r->laba, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $r->catatan ?? '-' }}</td>
                                <td class="text-end">
                                    <form action="{{ route('finance.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?')">
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
                                        <i data-lucide="bar-chart-2" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                        <h5>Belum ada laporan keuangan.</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($reports->count() > 0)
                    <tfoot>
                        <tr class="bg-light fw-bold">
                            <td colspan="2" class="text-end">Total Keseluruhan:</td>
                            <td>Rp {{ number_format($reports->sum('pemasukan'), 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($reports->sum('pengeluaran'), 0, ',', '.') }}</td>
                            @php $totalLaba = $reports->sum('laba'); @endphp
                            <td class="{{ $totalLaba >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($totalLaba, 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $reports->links() }}
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
<style>
    .table-success-tint { background-color: rgba(74, 124, 89, 0.05); }
    .table-danger-tint { background-color: rgba(192, 57, 43, 0.05); }
</style>
@endsection
