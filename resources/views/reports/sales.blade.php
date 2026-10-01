@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Laporan penjualan</span>
    </div>
@endsection

@section('content')
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="page-title mb-1">Laporan penjualan</h3>
            <p class="page-subtitle text-muted mb-0">Analisis kinerja distribusi dan volume penjualan kopi ke toko mitra.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('reports.sales.export', 'csv') }}?{{ http_build_query(request()->all()) }}" class="btn btn-outline-modern d-flex align-items-center gap-2">
                <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                <span>Ekspor CSV</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-modern mb-4">
        <div class="card-body-modern p-3">
            <form method="GET" action="{{ route('reports.sales') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label-modern small mb-1">Toko mitra</label>
                    <select name="store_id" class="form-control-modern form-select form-select-sm">
                        <option value="">Semua toko mitra</option>
                        @foreach($stores ?? [] as $store)
                            <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern small mb-1">Dari tanggal</label>
                    <input type="date" name="date_from" class="form-control-modern form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern small mb-1">Sampai tanggal</label>
                    <input type="date" name="date_to" class="form-control-modern form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label-modern small mb-1">Varian kopi</label>
                    <select name="coffee_type_id" class="form-control-modern form-select form-select-sm">
                        <option value="">Semua varian kopi</option>
                        @foreach($coffeeTypes ?? [] as $ct)
                            <option value="{{ $ct->id }}" {{ request('coffee_type_id') == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-accent w-100 d-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="filter" style="width: 14px; height: 14px;"></i>
                        <span>Terapkan filter</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Cards (Maksimal 4) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total pendapatan</div>
                    <div class="stat-value" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Unit terjual</div>
                    <div class="stat-value" style="font-variant-numeric: tabular-nums;">{{ number_format($totalUnits ?? 0, 0, ',', '.') }} pcs</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Rata-rata transaksi</div>
                    <div class="stat-value" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($avgPerTransaction ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total transaksi</div>
                    <div class="stat-value" style="font-variant-numeric: tabular-nums;">{{ number_format($sales->count(), 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    @if($dailyBreakdown->count() > 0)
    <div class="card-modern mb-4">
        <div class="card-header-modern">
            <h5 class="card-title-modern m-0">Tren pendapatan harian</h5>
        </div>
        <div class="card-body-modern">
            <div style="height: 250px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Sales Table -->
        <div class="col-lg-8">
            <div class="card-modern h-100">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">Rincian transaksi penjualan</h5>
                </div>
                <div class="card-body-modern p-0">
                    <div class="table-responsive">
                        <table class="table-modern w-100 m-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;">Tanggal</th>
                                    <th>Toko mitra</th>
                                    <th>Varian kopi</th>
                                    <th class="text-end" style="min-width: 80px;">Jumlah</th>
                                    <th class="text-end" style="min-width: 120px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td class="text-muted" style="font-variant-numeric: tabular-nums;">{{ \Carbon\Carbon::parse($sale->tanggal)->format('d/m/Y') }}</td>
                                        <td class="fw-semibold text-main">{{ $sale->store->name ?? '-' }}</td>
                                        <td>{{ $sale->coffeeType->name ?? '-' }}</td>
                                        <td class="text-end fw-semibold" style="font-variant-numeric: tabular-nums;">{{ number_format($sale->jumlah, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold" style="font-variant-numeric: tabular-nums; color: var(--text-main);">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4 small">Belum ada data penjualan pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Sellers -->
        <div class="col-lg-4">
            <div class="card-modern h-100">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">Varian terlaris</h5>
                </div>
                <div class="card-body-modern p-0">
                    <div class="table-responsive">
                        <table class="table-modern w-100 m-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Varian kopi</th>
                                    <th class="text-end">Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @forelse($topSellers ?? [] as $coffeeTypeId => $totalQty)
                                    @php $coffeeName = $coffeeTypes->firstWhere('id', $coffeeTypeId)->name ?? '-'; @endphp
                                    <tr>
                                        <td class="text-muted small" style="font-variant-numeric: tabular-nums;">{{ $rank++ }}</td>
                                        <td class="fw-semibold text-main">{{ $coffeeName }}</td>
                                        <td class="text-end fw-semibold" style="font-variant-numeric: tabular-nums;">{{ number_format($totalQty, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4 small">Belum ada data varian terlaris.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    const chartEl = document.getElementById('salesChart');
    if (!chartEl) return;

    function checkIsDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }
    const isDark = checkIsDark();
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';

    const ctx = chartEl.getContext('2d');

    const labels = [];
    const data = [];
    @foreach($dailyBreakdown ?? [] as $date => $total)
        labels.push({!! json_encode(\Carbon\Carbon::parse($date)->format('d M')) !!});
        data.push({{ $total }});
    @endforeach

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan Harian',
                data: data,
                borderColor: '#C88A4E',
                backgroundColor: 'rgba(200, 138, 78, 0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#C88A4E',
                pointBorderColor: isDark ? '#1e293b' : '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                    titleFont: { family: "'Manrope', sans-serif", weight: 'bold' },
                    bodyFont: { family: "'Manrope', sans-serif", size: 12 },
                    callbacks: {
                        label: function(ctx) {
                            return ' Omset: Rp ' + Number(ctx.raw || 0).toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: isDark ? '#94a3b8' : '#64748b',
                        font: { family: "'Manrope', sans-serif", size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.04)'
                    },
                    ticks: {
                        color: isDark ? '#94a3b8' : '#64748b',
                        font: { family: "'Manrope', sans-serif", size: 11 },
                        callback: function(v) {
                            if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(1) + ' jt';
                            if (v >= 1000) return 'Rp ' + (v / 1000).toFixed(0) + ' rb';
                            return 'Rp ' + v;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
