@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Home</a>
    <i data-lucide="chevron-right"></i>
    <span>Laporan Penjualan</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Laporan Penjualan</h1>
            <p class="page-subtitle">Analisis dan laporan penjualan kopi</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('reports.sales.export', 'csv') }}?{{ http_build_query(request()->all()) }}" class="btn btn-accent">Export CSV</a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-modern mb-4">
        <div class="card-body-modern filter-bar">
            <form method="GET" action="{{ route('reports.sales') }}" class="row g-3 align-items-end">
                <div class="col-md-3 filter-group">
                    <label class="form-label form-label-modern">Toko</label>
                    <select name="store_id" class="form-select form-control-modern">
                        <option value="">Semua Toko</option>
                        @foreach($stores ?? [] as $store)
                            <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 filter-group">
                    <label class="form-label form-label-modern">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control form-control-modern" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2 filter-group">
                    <label class="form-label form-label-modern">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control form-control-modern" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 filter-group">
                    <label class="form-label form-label-modern">Jenis Kopi</label>
                    <select name="coffee_type_id" class="form-select form-control-modern">
                        <option value="">Semua Kopi</option>
                        @foreach($coffeeTypes ?? [] as $ct)
                            <option value="{{ $ct->id }}" {{ request('coffee_type_id') == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-accent w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-value" style="font-size:0.95rem">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-value">{{ $totalUnits ?? 0 }}</div>
                    <div class="stat-label">Total Unit Terjual</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-value" style="font-size:0.95rem">Rp {{ number_format($avgPerTransaction ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Rata-rata/Transaksi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-value">{{ $sales->count() }}</div>
                    <div class="stat-label">Total Transaksi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    @if($dailyBreakdown->count() > 0)
    <div class="card-modern mb-4">
        <div class="card-header-modern">
            <h5 class="card-title-modern m-0">Grafik Penjualan Harian</h5>
        </div>
        <div class="card-body-modern">
            <canvas id="salesChart" height="250"></canvas>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Sales Table -->
        <div class="col-lg-8">
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">Detail Penjualan</h5>
                </div>
                <div class="card-body-modern p-0">
                    <div class="table-responsive">
                        <table class="table-modern w-100 m-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Toko</th>
                                    <th>Jenis Kopi</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($sale->tanggal)->format('d-m-Y') }}</td>
                                        <td>{{ $sale->store->name ?? '-' }}</td>
                                        <td>{{ $sale->coffeeType->name ?? '-' }}</td>
                                        <td>{{ $sale->jumlah }}</td>
                                        <td class="fw-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada data penjualan.</td>
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
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">Top Sellers</h5>
                </div>
                <div class="card-body-modern p-0">
                    <div class="table-responsive">
                        <table class="table-modern w-100 m-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jenis Kopi</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @forelse($topSellers ?? [] as $coffeeTypeId => $totalQty)
                                    @php $coffeeName = $coffeeTypes->firstWhere('id', $coffeeTypeId)->name ?? '-'; @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $rank++ }}</td>
                                        <td>{{ $coffeeName }}</td>
                                        <td class="fw-bold text-success">{{ $totalQty }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Belum ada data.</td>
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
