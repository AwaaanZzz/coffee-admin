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
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="page-title mb-1">Laporan keuangan</h3>
            <p class="page-subtitle text-muted mb-0">Pantau pemasukan, pengeluaran, dan margin laba bersih per toko mitra.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('finance.create') }}" class="btn btn-accent d-flex align-items-center gap-2">
                <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                <span>Buat laporan</span>
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
                            <th>Toko mitra</th>
                            <th style="min-width: 170px;">Periode</th>
                            <th class="text-end" style="min-width: 120px;">Pemasukan</th>
                            <th class="text-end" style="min-width: 120px;">Pengeluaran</th>
                            <th class="text-end" style="min-width: 130px;">Laba / rugi</th>
                            <th>Catatan</th>
                            <th class="text-end" style="min-width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $r)
                            <tr>
                                <td class="fw-semibold text-main">{{ $r->store->name }}</td>
                                <td class="text-muted" style="font-variant-numeric: tabular-nums;">{{ $r->periode_awal->format('d/m/Y') }} &ndash; {{ $r->periode_akhir->format('d/m/Y') }}</td>
                                <td class="text-end" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($r->pemasukan, 0, ',', '.') }}</td>
                                <td class="text-end text-muted" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($r->pengeluaran, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <span class="badge {{ $r->laba >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}" style="font-variant-numeric: tabular-nums; font-weight: 600;">
                                        Rp {{ number_format($r->laba, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $r->catatan ?? 'Belum diisi' }}</td>
                                <td class="text-end">
                                    <form action="{{ route('finance.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus laporan keuangan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-modern text-danger p-1" title="Hapus laporan">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state text-center py-5">
                                        <i data-lucide="bar-chart-2" class="text-muted mb-2 opacity-50" style="width: 40px; height: 40px;"></i>
                                        <p class="text-muted mb-0 small">Belum ada laporan keuangan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($reports->count() > 0)
                    <tfoot>
                        <tr class="fw-bold" style="background: var(--bg-input, #FBF7F0); border-top: 2px solid var(--border-color, #E8DFD5);">
                            <td colspan="2" class="text-end">Total keseluruhan:</td>
                            <td class="text-end" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($reports->sum('pemasukan'), 0, ',', '.') }}</td>
                            <td class="text-end text-muted" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($reports->sum('pengeluaran'), 0, ',', '.') }}</td>
                            @php $totalLaba = $reports->sum('laba'); @endphp
                            <td class="text-end {{ $totalLaba >= 0 ? 'text-success' : 'text-danger' }}" style="font-variant-numeric: tabular-nums;">
                                Rp {{ number_format($totalLaba, 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @if($reports->hasPages())
                <div class="p-3 border-top">
                    {{ $reports->links() }}
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
<style>
    .table-success-tint { background-color: rgba(74, 124, 89, 0.05); }
    .table-danger-tint { background-color: rgba(192, 57, 43, 0.05); }
</style>
@endsection
