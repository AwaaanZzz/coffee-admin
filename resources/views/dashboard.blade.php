@extends('layouts.app')

@section('title', 'Beranda')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Beranda Operasional</h1>
            <p class="page-subtitle">Ringkasan stok kopi, penjualan mitra, dan pergerakan persediaan.</p>
            <span id="realtimeGreeting" class="d-none"></span>
            <span id="realtimeDateSubtitle" class="d-none"></span>
        </div>
        <div class="page-actions">
            <a href="{{ route('sales.create') }}" class="btn btn-accent">
                <i data-lucide="plus"></i> Catat Penjualan
            </a>
            <a href="{{ route('stock.index') }}" class="btn btn-outline-modern">
                <i data-lucide="printer"></i> Cetak Barcode
            </a>
            <button type="button" class="btn btn-outline-modern" onclick="window.location.reload()">
                <i data-lucide="refresh-cw"></i> Segarkan Data
            </button>
        </div>
    </div>

    {{-- Stats Row (Maksimal 4 KPI untuk Keputusan Operasional) --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i data-lucide="store"></i></div>
                <div class="stat-info">
                    <div class="stat-value tabular-nums">{{ $totalToko ?? 0 }}</div>
                    <div class="stat-label">Toko mitra aktif</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i data-lucide="package"></i></div>
                <div class="stat-info">
                    <div class="stat-value tabular-nums">{{ number_format($totalStock ?? 0) }}</div>
                    <div class="stat-label">Total stok tersedia (pcs)</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i data-lucide="shopping-cart"></i></div>
                <div class="stat-info">
                    <div class="stat-value tabular-nums">{{ number_format($totalLaku ?? 0) }}</div>
                    <div class="stat-label">Total produk terjual (pcs)</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i data-lucide="alert-triangle"></i></div>
                <div class="stat-info">
                    <div class="stat-value tabular-nums {{ ($expiringSoon ?? 0) > 0 ? 'text-danger' : '' }}">{{ $expiringSoon ?? 0 }}</div>
                    <div class="stat-label">Stok mendekati kedaluwarsa</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Analisis Produk Paling Laku Tiap Toko --}}
    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="card-title-modern m-0">Analisis Produk Terlaris Tiap Toko</h5>
                <p class="text-muted small m-0 mt-1">Performa dan peringkat varian kopi di setiap toko mitra</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-sm btn-outline-modern active" id="btnMetricQty">
                        Unit Terjual (Pcs)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-modern" id="btnMetricRev">
                        Omzet (Rp)
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body-modern">
            {{-- Highlight Juara 1 Best Seller per Toko Mitra --}}
            <div class="row g-3 mb-4">
                @foreach($storeAnalytics ?? [] as $sid => $sa)
                    <div class="col-md-6 col-xl-4">
                        <div class="p-3 border h-100 position-relative" style="background: var(--bg-card); border-color: var(--border); border-radius: var(--radius-sm);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-semibold m-0 text-dark">{{ $sa['store_name'] }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Penanggung jawab: {{ $sa['penanggung_jawab'] ?: 'Belum diisi' }}</small>
                                </div>
                                <span class="badge badge-secondary" style="font-size:0.7rem;">
                                    Terlaris
                                </span>
                            </div>

                            @if($sa['top_product'])
                                <div class="mt-2 pt-2 border-top">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <div class="fw-semibold text-dark" style="font-size: 0.95rem;">{{ $sa['top_product']['name'] }}</div>
                                        <span class="badge badge-secondary">
                                            {{ ucfirst($sa['top_product']['category']) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 small">
                                        <span class="text-muted">Terjual: <strong class="text-dark tabular-nums">{{ $sa['top_product']['qty'] }} pcs</strong></span>
                                        <span class="text-muted">Omzet: <strong class="text-dark tabular-nums">Rp {{ number_format($sa['top_product']['revenue'], 0, ',', '.') }}</strong></span>
                                    </div>
                                    <div class="progress mt-2" style="height: 4px; background: var(--border); border-radius: 2px;">
                                        <div class="progress-bar bg-accent" role="progressbar" style="width: {{ $sa['top_product']['share_pct'] }}%;" aria-valuenow="{{ $sa['top_product']['share_pct'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1" style="font-size:0.7rem; color:var(--text-muted);">
                                        <span>Pangsa penjualan toko</span>
                                        <span class="tabular-nums">{{ $sa['top_product']['share_pct'] }}%</span>
                                    </div>
                                </div>
                            @else
                                <div class="mt-2 pt-2 border-top text-center py-3 text-muted">
                                    <small class="text-empty">Belum ada transaksi penjualan tercatat di toko ini.</small>
                                </div>
                            @endif

                            <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                                <small class="text-muted">Total penjualan: <strong class="tabular-nums">{{ $sa['total_qty'] }} pcs</strong></small>
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold text-secondary switch-store-chart" data-store-id="{{ $sid }}" style="font-size:0.75rem;">
                                    Tampilkan di grafik &rarr;
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Store Filter Pills --}}
            <div class="d-flex align-items-center gap-2 overflow-x-auto pb-2 mb-3 border-bottom">
                <span class="text-muted small fw-bold text-nowrap">Pilih Toko:</span>
                <button type="button" class="btn btn-sm btn-outline-modern store-tab-btn active text-nowrap" data-store-id="all">
                    Semua Toko (Perbandingan)
                </button>
                @foreach($storeAnalytics ?? [] as $sid => $sa)
                    <button type="button" class="btn btn-sm btn-outline-modern store-tab-btn text-nowrap" data-store-id="{{ $sid }}">
                        {{ $sa['store_name'] }} ({{ $sa['total_qty'] }} pcs)
                    </button>
                @endforeach
            </div>

            {{-- Dynamic Quick KPI Mini-Summary for Selected Store --}}
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <div class="p-2 px-3 rounded-3 border" style="background: var(--bg-hover);">
                        <small class="text-muted d-block" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">Fokus Toko</small>
                        <strong class="text-dark fs-6" id="kpiStoreName">Semua Toko</strong>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 px-3 rounded-3 border" style="background: var(--bg-hover);">
                        <small class="text-muted d-block" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">Terlaris #1</small>
                        <strong class="text-accent fs-6 text-truncate d-block" id="kpiTopCoffee">-</strong>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 px-3 rounded-3 border" style="background: var(--bg-hover);">
                        <small class="text-muted d-block" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">Total Terjual</small>
                        <strong class="text-success fs-6" id="kpiTotalQty">0 pcs</strong>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 px-3 rounded-3 border" style="background: var(--bg-hover);">
                        <small class="text-muted d-block" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">Total Omset</small>
                        <strong class="text-primary fs-6" id="kpiTotalRevenue">Rp 0</strong>
                    </div>
                </div>
            </div>

            {{-- Main Chart & Leaderboard Table --}}
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="p-3 rounded-3 border" style="background: var(--bg-card); min-height: 380px; position: relative;">
                        <canvas id="storeBestSellerChart"></canvas>
                    </div>
                    <div id="chartSummaryNote" class="text-muted small mt-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <span id="chartStoreTitle">Menampilkan data: <strong>Semua Toko</strong></span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-muted border" style="font-size:0.7rem;">
                                Angka langsung tertera di atas bar grafik
                            </span>
                            <span id="chartMetricTitle" class="fw-semibold">Metrik: Unit Terjual (Pcs)</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="border rounded-3 p-3 h-100 d-flex flex-column" style="background: var(--bg-card);">
                        <h6 class="fw-bold mb-3 text-dark">
                            <span id="tableStoreTitle">Peringkat Produk</span>
                        </h6>
                        <div class="table-responsive flex-grow-1" style="max-height: 290px; overflow-y: auto;">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                <thead>
                                    <tr class="text-muted">
                                        <th style="width: 32px;">#</th>
                                        <th>Kopi</th>
                                        <th class="text-end" id="colMetricHeader">Terjual</th>
                                        <th class="text-end">Omset</th>
                                    </tr>
                                </thead>
                                <tbody id="storeLeaderboardBody">
                                    {{-- Generated dynamically via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary & Best Seller Doughnut --}}
    <div class="row g-4 mb-4">
        {{-- Section: Ringkasan Keuntungan & Kerugian (Balanced & Real-time) --}}
        <div class="col-lg-8">
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="card-title-modern m-0">Ringkasan Keuntungan & Kerugian</h5>
                        <p class="text-muted small m-0 mt-1">Laporan keuangan berbasis Modal/HPP riil tiap jenis kopi (Pemasukan, Beban Pokok Roastery, & Laba Bersih)</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('coffee-types.modal') }}" class="btn btn-sm btn-outline-accent d-inline-flex align-items-center gap-1" style="font-size:0.75rem;">
                            <i data-lucide="coins" style="width:13px;height:13px;"></i> Kelola Modal HPP &rarr;
                        </a>
                        <span class="badge badge-success px-2 py-1">
                            <i data-lucide="check" style="width:12px;height:12px;display:inline-block;vertical-align:-1px;"></i> Data seimbang
                        </span>
                    </div>
                </div>

                <div class="card-body-modern p-3">
                    {{-- 4 Financial KPI Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 border" style="background: var(--bg-card); border-color: var(--border); border-radius: var(--radius-sm);">
                                <div class="text-secondary small fw-medium mb-1">Total pemasukan</div>
                                <div class="fs-5 fw-semibold text-dark tabular-nums">Rp {{ number_format($financeSummary['total_pemasukan'] ?? 0, 0, ',', '.') }}</div>
                                <small class="text-muted" style="font-size:0.75rem;">Omzet kotor mitra</small>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 border" style="background: var(--bg-card); border-color: var(--border); border-radius: var(--radius-sm);">
                                <div class="text-secondary small fw-medium mb-1">Total pengeluaran</div>
                                <div class="fs-5 fw-semibold text-danger tabular-nums">Rp {{ number_format($financeSummary['total_pengeluaran'] ?? 0, 0, ',', '.') }}</div>
                                <small class="text-muted" style="font-size:0.75rem;">Beban HPP & kemasan</small>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 border" style="background: var(--bg-card); border-color: var(--border); border-radius: var(--radius-sm);">
                                <div class="text-secondary small fw-medium mb-1">Laba bersih</div>
                                <div class="fs-5 fw-semibold text-success tabular-nums">Rp {{ number_format($financeSummary['total_laba'] ?? 0, 0, ',', '.') }}</div>
                                <small class="text-muted" style="font-size:0.75rem;">Pemasukan - beban</small>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 border" style="background: var(--bg-card); border-color: var(--border); border-radius: var(--radius-sm);">
                                <div class="text-secondary small fw-medium mb-1">Margin keuntungan</div>
                                <div class="fs-5 fw-semibold text-dark tabular-nums">{{ number_format($financeSummary['average_margin'] ?? 0, 1) }}%</div>
                                <small class="text-muted" style="font-size:0.75rem;">Rata-rata profit margin</small>
                            </div>
                        </div>
                    </div>

                    {{-- Financial Table per Store --}}
                    <div class="table-responsive border" style="border-radius: var(--radius-sm);">
                        <table class="table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Toko mitra</th>
                                    <th>Periode</th>
                                    <th class="text-end">Pemasukan</th>
                                    <th class="text-end">Pengeluaran</th>
                                    <th class="text-end">Laba bersih</th>
                                    <th class="text-center">Margin</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($profitLossData ?? [] as $report)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                {{ $report->store_name }}
                                            </div>
                                        </td>
                                        <td class="text-muted small">{{ $report->periode }}</td>
                                        <td class="text-end fw-semibold text-info">
                                            Rp {{ number_format($report->pemasukan, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-semibold text-danger">
                                            Rp {{ number_format($report->pengeluaran, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold {{ $report->laba >= 0 ? 'text-success' : 'text-danger' }}">
                                            Rp {{ number_format($report->laba, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $report->margin >= 25 ? 'bg-success' : ($report->margin > 0 ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                {{ number_format($report->margin, 1) }}%
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($report->pemasukan > 0)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    Surplus
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-1">
                                                    Aktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada data laporan keuangan per toko.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Top 5 Kopi Terlaris (Doughnut) --}}
        <div class="col-lg-4">
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">Top 5 Kopi Terlaris</h5>
                </div>
                <div class="card-body-modern d-flex flex-column justify-content-between">
                    <div style="height: 270px; position: relative;">
                        <canvas id="topCoffeeChart"></canvas>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <small class="text-muted d-block text-center mb-2">Pangsa volume penjualan antar varian kopi</small>
                        <div class="d-flex flex-column gap-1" style="font-size:0.8rem;">
                            @foreach(($topProducts ?? []) as $i => $tp)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-truncate" style="max-width:180px;">{{ $i+1 }}. {{ $tp->coffeeType->name ?? '-' }}</span>
                                    <strong class="text-dark">{{ $tp->total_qty }} pcs</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Rekomendasi Restock Toko Mitra (Smart Inventory Insights) --}}
    @if(isset($lowStockBatches) && $lowStockBatches->count() > 0)
    <div class="card-modern mb-4" style="background: #ffffff; border: 1px solid rgba(212, 160, 23, 0.3);">
        <div class="card-header-modern d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: rgba(212, 160, 23, 0.04);">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark px-2.5 py-1 fw-bold" style="font-size:0.72rem;">Perlu Tindakan Segera</span>
                <h5 class="card-title-modern m-0">Rekomendasi Restock Toko Mitra</h5>
            </div>
            <div>
                <a href="{{ route('stock.create') }}" class="btn btn-sm btn-accent">
                    + Tambah Pasokan Batch Baru
                </a>
            </div>
        </div>
        <div class="card-body-modern p-3">
            <div class="row g-3">
                @foreach($lowStockBatches as $lb)
                    <div class="col-md-6 col-xl-4">
                        <div class="p-3 rounded-3 border d-flex justify-content-between align-items-center" style="background: #FFFDF8; border-color: rgba(212, 160, 23, 0.25) !important;">
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $lb->coffeeType->name ?? 'Kopi' }}</div>
                                <div class="text-muted small">Toko: <strong>{{ $lb->store->name ?? '-' }}</strong></div>
                                <small class="text-muted font-monospace" style="font-size:0.72rem;">Batch: {{ $lb->kode_produksi }}</small>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-danger-subtle text-danger border border-danger-subtle mb-1.5" style="font-size:0.8rem; font-weight:700;">
                                    Sisa {{ $lb->sisa }} pcs
                                </div>
                                <div>
                                    <a href="{{ route('stock.index', ['store_id' => $lb->store_id, 'search' => $lb->kode_produksi]) }}" class="btn btn-sm btn-outline-accent py-0.5 px-2" style="font-size:0.72rem;">
                                        Restock &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    function checkIsDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    }

    let isDark = checkIsDark();
    let gridColor = isDark ? '#334155' : '#e2e8f0';

    const coffeePalette = [
        '#C88A4E', // Caramel Amber
        '#1E3A5F', // Dark Espresso Navy
        '#4A7C59', // Sage Green
        '#2E86AB', // Ocean Blue
        '#A67261', // Terracotta
        '#8C6D58', // Warm Bronze
        '#6C4F82', // Berry Velvet
        '#D4A017'  // Golden Honey
    ];

    // ==========================================
    // 1. Top 5 Kopi Terlaris Doughnut Chart
    // ==========================================
    const ctxCoffee = document.getElementById('topCoffeeChart');
    let topCoffeeChartInstance = null;

    if (ctxCoffee) {
        const topLabels = [];
        const topData = [];
        @foreach(($topProducts ?? []) as $p)
            topLabels.push({!! json_encode($p->coffeeType->name ?? 'Unknown') !!});
            topData.push({{ $p->total_qty ?? 0 }});
        @endforeach

        const totalTopVolume = topData.reduce((acc, curr) => acc + curr, 0);

        const centerTextPlugin = {
            id: 'doughnutCenterText',
            beforeDraw(chart) {
                if (chart.config.type !== 'doughnut') return;
                const { width, height, ctx } = chart;
                ctx.save();
                
                const fontSizeNum = Math.min(Math.round(height / 7), 24);
                ctx.font = `700 ${fontSizeNum}px 'Manrope', sans-serif`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = checkIsDark() ? '#f8fafc' : '#1e293b';
                ctx.fillText(totalTopVolume.toLocaleString('id-ID'), width / 2, height / 2 - 8);

                ctx.font = `600 11px 'Manrope', sans-serif`;
                ctx.fillStyle = checkIsDark() ? '#94a3b8' : '#64748b';
                ctx.fillText('Total Unit', width / 2, height / 2 + 12);

                ctx.restore();
            }
        };

        topCoffeeChartInstance = new Chart(ctxCoffee, {
            type: 'doughnut',
            data: {
                labels: topLabels.length ? topLabels : ['Belum ada data'],
                datasets: [{
                    data: topData.length ? topData : [1],
                    backgroundColor: coffeePalette,
                    borderWidth: 2,
                    borderColor: checkIsDark() ? '#1e293b' : '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            padding: 10,
                            color: checkIsDark() ? '#cbd5e1' : '#475569',
                            font: { family: "'Manrope', sans-serif", size: 11, weight: '500' }
                        }
                    },
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
                                const val = ctx.raw || 0;
                                const pct = totalTopVolume > 0 ? ((val / totalTopVolume) * 100).toFixed(1) : 0;
                                return ` Terjual: ${val} pcs (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            },
            plugins: [centerTextPlugin]
        });
    }

    // ==========================================
    // 2. Analisis Produk Paling Laku Tiap Toko
    // ==========================================
    const storeAnalyticsData = {!! json_encode($storeAnalytics ?? []) !!};
    const globalProductsAgg = {!! json_encode($allStoreProductsAgg ?? []) !!};
    
    let activeStoreId = 'all';
    let activeMetric = 'qty'; // 'qty' | 'revenue'
    let storeChartInstance = null;
    let currentChartItems = [];

    const ctxStoreBest = document.getElementById('storeBestSellerChart');
    const storeLeaderboardBody = document.getElementById('storeLeaderboardBody');
    const chartStoreTitle = document.getElementById('chartStoreTitle');
    const chartMetricTitle = document.getElementById('chartMetricTitle');
    const tableStoreTitle = document.getElementById('tableStoreTitle');
    const colMetricHeader = document.getElementById('colMetricHeader');
    const btnMetricQty = document.getElementById('btnMetricQty');
    const btnMetricRev = document.getElementById('btnMetricRev');

    // Quick KPI Elements
    const kpiStoreName = document.getElementById('kpiStoreName');
    const kpiTopCoffee = document.getElementById('kpiTopCoffee');
    const kpiTotalQty = document.getElementById('kpiTotalQty');
    const kpiTotalRevenue = document.getElementById('kpiTotalRevenue');

    // Custom Chart.js Plugin for Direct Value Labels on Bars (Vertical Columns)
    const barValueLabelsPlugin = {
        id: 'barValueLabelsPlugin',
        afterDatasetsDraw(chart) {
            const { ctx, chartArea: { top, bottom, left, right } } = chart;
            const dark = checkIsDark();

            chart.data.datasets.forEach((dataset, datasetIdx) => {
                const meta = chart.getDatasetMeta(datasetIdx);
                meta.data.forEach((bar, index) => {
                    const val = dataset.data[index];
                    if (val === undefined || val === null) return;

                    const item = currentChartItems[index] || {};
                    const isZero = (val === 0);
                    
                    let mainText = '';
                    let subText = '';

                    if (activeMetric === 'qty') {
                        mainText = `${Number(val).toLocaleString('id-ID')} pcs`;
                        const rev = item.revenue || item.total_revenue || 0;
                        if (rev > 0) {
                            subText = `Rp ${Number(rev).toLocaleString('id-ID')}`;
                        }
                    } else {
                        mainText = `Rp ${Number(val).toLocaleString('id-ID')}`;
                        const q = item.qty || item.total_qty || 0;
                        if (q > 0) {
                            subText = `${Number(q).toLocaleString('id-ID')} pcs`;
                        }
                    }

                    ctx.save();
                    const barCenterX = bar.x;
                    const barTopY = bar.y;

                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';

                    if (isZero) {
                        ctx.font = '500 11px "Manrope", sans-serif';
                        ctx.fillStyle = dark ? '#64748b' : '#94a3b8';
                        ctx.fillText('0 pcs', barCenterX, Math.min(barTopY - 4, bottom - 4));
                    } else {
                        // Main Bold Text (Quantity or Revenue)
                        ctx.font = '700 12px "Manrope", sans-serif';
                        ctx.fillStyle = dark ? '#f8fafc' : '#0f172a';
                        
                        if (subText) {
                            // Two-line layout above the column
                            ctx.fillText(mainText, barCenterX, barTopY - 15);
                            ctx.font = '500 10px "Manrope", sans-serif';
                            ctx.fillStyle = dark ? '#94a3b8' : '#64748b';
                            ctx.fillText(subText, barCenterX, barTopY - 3);
                        } else {
                            ctx.fillText(mainText, barCenterX, barTopY - 6);
                        }
                    }

                    ctx.restore();
                });
            });
        }
    };

    function updateStoreBestSellerView() {
        if (!ctxStoreBest) return;

        isDark = checkIsDark();
        gridColor = isDark ? 'rgba(255, 255, 255, 0.07)' : 'rgba(0, 0, 0, 0.05)';

        let labels = [];
        let dataValues = [];
        let tableRowsHtml = '';
        currentChartItems = [];

        let currentStoreName = '';
        let currentTopCoffee = '-';
        let currentTotalQty = 0;
        let currentTotalRev = 0;

        if (activeStoreId === 'all') {
            currentStoreName = 'Semua Toko (Komparasi)';
            chartStoreTitle.innerHTML = 'Menampilkan data: <strong>Semua Toko Mitra</strong>';
            tableStoreTitle.innerText = 'Peringkat Produk (Semua Toko)';
            colMetricHeader.innerText = activeMetric === 'qty' ? 'Total Terjual' : 'Total Omset';

            if (!globalProductsAgg || globalProductsAgg.length === 0) {
                labels = ['Belum ada data penjualan'];
                dataValues = [0];
                tableRowsHtml = '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi penjualan di toko manapun.</td></tr>';
            } else {
                currentChartItems = [...globalProductsAgg];
                currentTopCoffee = globalProductsAgg[0]?.name ? `${globalProductsAgg[0].name} (${globalProductsAgg[0].total_qty} pcs)` : '-';
                currentTotalQty = globalProductsAgg.reduce((acc, curr) => acc + (curr.total_qty || 0), 0);
                currentTotalRev = globalProductsAgg.reduce((acc, curr) => acc + (curr.total_revenue || 0), 0);

                globalProductsAgg.forEach((item, index) => {
                    labels.push(item.name);
                    const val = activeMetric === 'qty' ? item.total_qty : item.total_revenue;
                    dataValues.push(val);

                    const color = coffeePalette[index % coffeePalette.length];
                    let medal = `<span class="badge rounded-pill bg-light text-muted border fw-semibold" style="font-size:0.75rem; min-width:22px;">${index + 1}</span>`;
                    if (index === 0) medal = `<span class="badge rounded-pill bg-warning text-dark fw-bold" style="font-size:0.75rem; min-width:22px;">1</span>`;
                    else if (index === 1) medal = `<span class="badge rounded-pill bg-secondary text-white fw-bold" style="font-size:0.75rem; min-width:22px;">2</span>`;
                    else if (index === 2) medal = `<span class="badge rounded-pill border fw-bold text-dark" style="font-size:0.75rem; min-width:22px; background:#F5E5D3;">3</span>`;

                    const formattedMetric = activeMetric === 'qty' 
                        ? `<strong class="text-success">${item.total_qty} pcs</strong>`
                        : `<strong class="text-dark">Rp ${Number(item.total_revenue).toLocaleString('id-ID')}</strong>`;

                    const omsetFormatted = `Rp ${Number(item.total_revenue).toLocaleString('id-ID')}`;

                    tableRowsHtml += `
                        <tr>
                            <td class="text-center">${medal}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <span style="width:8px;height:8px;border-radius:50%;background:${color};display:inline-block;"></span>
                                    <span class="fw-bold">${item.name}</span>
                                </div>
                                <span class="badge ${item.category === 'robusta' ? 'badge-info' : 'badge-warning'}" style="font-size:0.65rem;">${item.category}</span>
                            </td>
                            <td class="text-end">${formattedMetric}</td>
                            <td class="text-end text-muted small">${omsetFormatted}</td>
                        </tr>
                    `;
                });
            }
        } else {
            const storeData = storeAnalyticsData[activeStoreId];
            currentStoreName = storeData ? storeData.store_name : 'Toko';
            chartStoreTitle.innerHTML = `Menampilkan data: <strong>${currentStoreName}</strong>`;
            tableStoreTitle.innerText = `Peringkat Produk (${currentStoreName})`;
            colMetricHeader.innerText = activeMetric === 'qty' ? 'Terjual' : 'Omset';

            if (!storeData || !storeData.products || storeData.products.length === 0) {
                labels = ['Belum ada penjualan di toko ini'];
                dataValues = [0];
                tableRowsHtml = `<tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi penjualan di toko ${currentStoreName}.</td></tr>`;
            } else {
                currentChartItems = [...storeData.products];
                currentTotalQty = storeData.total_qty || 0;
                currentTotalRev = storeData.total_revenue || 0;
                currentTopCoffee = storeData.top_product ? `${storeData.top_product.name} (${storeData.top_product.qty} pcs)` : '-';

                storeData.products.forEach((p, index) => {
                    labels.push(p.name);
                    const val = activeMetric === 'qty' ? p.qty : p.revenue;
                    dataValues.push(val);

                    const color = coffeePalette[index % coffeePalette.length];
                    let medal = `<span class="badge rounded-pill bg-light text-muted border fw-semibold" style="font-size:0.75rem; min-width:22px;">${index + 1}</span>`;
                    if (index === 0) medal = `<span class="badge rounded-pill bg-warning text-dark fw-bold" style="font-size:0.75rem; min-width:22px;">1</span>`;
                    else if (index === 1) medal = `<span class="badge rounded-pill bg-secondary text-white fw-bold" style="font-size:0.75rem; min-width:22px;">2</span>`;
                    else if (index === 2) medal = `<span class="badge rounded-pill border fw-bold text-dark" style="font-size:0.75rem; min-width:22px; background:#F5E5D3;">3</span>`;

                    const formattedMetric = activeMetric === 'qty' 
                        ? `<strong class="text-success">${p.qty} pcs</strong>`
                        : `<strong class="text-dark">Rp ${Number(p.revenue).toLocaleString('id-ID')}</strong>`;

                    const omsetFormatted = `Rp ${Number(p.revenue).toLocaleString('id-ID')}`;

                    tableRowsHtml += `
                        <tr>
                            <td class="text-center">${medal}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <span style="width:8px;height:8px;border-radius:50%;background:${color};display:inline-block;"></span>
                                    <span class="fw-bold">${p.name}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge ${p.category === 'robusta' ? 'badge-info' : 'badge-warning'}" style="font-size:0.65rem;">${p.category}</span>
                                    <small class="text-muted" style="font-size:0.7rem;">(${p.share_pct}%)</small>
                                </div>
                            </td>
                            <td class="text-end">${formattedMetric}</td>
                            <td class="text-end text-muted small">${omsetFormatted}</td>
                        </tr>
                    `;
                });
            }
        }

        // Update KPI Strip
        if (kpiStoreName) kpiStoreName.innerText = currentStoreName;
        if (kpiTopCoffee) kpiTopCoffee.innerText = currentTopCoffee;
        if (kpiTotalQty) kpiTotalQty.innerText = `${currentTotalQty.toLocaleString('id-ID')} pcs`;
        if (kpiTotalRevenue) kpiTotalRevenue.innerText = `Rp ${Number(currentTotalRev).toLocaleString('id-ID')}`;

        if (storeLeaderboardBody) storeLeaderboardBody.innerHTML = tableRowsHtml;
        if (chartMetricTitle) chartMetricTitle.innerHTML = `Metrik: <strong>${activeMetric === 'qty' ? 'Unit Terjual (Pcs)' : 'Total Omset (Rp)'}</strong>`;

        if (storeChartInstance) {
            storeChartInstance.destroy();
        }

        // Generate bar colors matching our theme palette
        const barColors = dataValues.map((_, i) => coffeePalette[i % coffeePalette.length]);

        storeChartInstance = new Chart(ctxStoreBest, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: activeMetric === 'qty' ? 'Jumlah Terjual (Pcs)' : 'Total Omset (Rp)',
                    data: dataValues,
                    backgroundColor: barColors,
                    borderRadius: { topLeft: 8, topRight: 8 },
                    borderSkipped: false,
                    maxBarThickness: 54
                }]
            },
            options: {
                indexAxis: 'x', // Grafik bar naik ke atas (vertikal)
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 32, // Ruang atas agar angka label nilai tidak terpotong
                        bottom: 4,
                        left: 10,
                        right: 10
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        titleColor: '#ffffff',
                        titleFont: { size: 13, weight: 'bold', family: "'Manrope', sans-serif" },
                        bodyFont: { size: 12, family: "'Manrope', sans-serif" },
                        bodySpacing: 6,
                        callbacks: {
                            title: function(items) {
                                return items[0].label;
                            },
                            label: function(ctx) {
                                const idx = ctx.dataIndex;
                                const item = currentChartItems[idx];
                                if (!item) return ` Nilai: ${ctx.raw}`;
                                
                                const lines = [];
                                const qty = activeStoreId === 'all' ? (item.total_qty || 0) : (item.qty || 0);
                                const rev = activeStoreId === 'all' ? (item.total_revenue || 0) : (item.revenue || 0);
                                const share = item.share_pct !== undefined ? item.share_pct : (currentTotalQty > 0 ? ((qty / currentTotalQty) * 100).toFixed(1) : 0);
                                
                                lines.push(` Unit Terjual: ${qty} pcs`);
                                lines.push(` Total Omset: Rp ${Number(rev).toLocaleString('id-ID')}`);
                                if (share > 0) lines.push(` Pangsa Penjualan: ${share}%`);
                                if (item.sisa_stock !== undefined) lines.push(` Sisa Stok: ${item.sisa_stock} pcs`);
                                return lines;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: isDark ? '#f1f5f9' : '#1e293b',
                            font: {
                                family: "'Manrope', sans-serif",
                                size: 12,
                                weight: '600'
                            },
                            maxRotation: 0,
                            autoSkip: false,
                            callback: function(val) {
                                if (typeof this.getLabelForValue === 'function') {
                                    return this.getLabelForValue(val);
                                }
                                return labels[val] !== undefined ? labels[val] : val;
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grace: '25%', // Memberi ruang ekstra di atas bar tertinggi
                        grid: {
                            color: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.04)'
                        },
                        ticks: {
                            color: isDark ? '#94a3b8' : '#64748b',
                            font: { family: "'Manrope', sans-serif", size: 11 },
                            callback: function(val) {
                                if (activeMetric === 'revenue') {
                                    if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
                                    if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + ' rb';
                                    return 'Rp ' + val;
                                }
                                return val + ' pcs';
                            }
                        }
                    }
                }
            },
            plugins: [barValueLabelsPlugin]
        });
    }

    if (btnMetricQty) {
        btnMetricQty.addEventListener('click', function() {
            activeMetric = 'qty';
            this.classList.add('btn-accent', 'active');
            this.classList.remove('btn-outline-modern');
            btnMetricRev.classList.remove('btn-accent', 'active');
            btnMetricRev.classList.add('btn-outline-modern');
            updateStoreBestSellerView();
        });
    }

    if (btnMetricRev) {
        btnMetricRev.addEventListener('click', function() {
            activeMetric = 'revenue';
            this.classList.add('btn-accent', 'active');
            this.classList.remove('btn-outline-modern');
            btnMetricQty.classList.remove('btn-accent', 'active');
            btnMetricQty.classList.add('btn-outline-modern');
            updateStoreBestSellerView();
        });
    }

    document.querySelectorAll('.store-tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.store-tab-btn').forEach(b => b.classList.remove('active', 'btn-accent'));
            this.classList.add('active');
            activeStoreId = this.dataset.storeId;
            updateStoreBestSellerView();
        });
    });

    document.querySelectorAll('.switch-store-chart').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const sid = this.dataset.storeId;
            activeStoreId = sid;
            document.querySelectorAll('.store-tab-btn').forEach(b => {
                b.classList.remove('active', 'btn-accent');
                if (b.dataset.storeId == sid) b.classList.add('active');
            });
            updateStoreBestSellerView();
            ctxStoreBest.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    // Handle Theme Switch dynamically
    const themeToggleBtn = document.getElementById('themeToggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            setTimeout(() => {
                updateStoreBestSellerView();
                if (topCoffeeChartInstance) {
                    topCoffeeChartInstance.update();
                }
            }, 100);
        });
    }

    // Initial render
    updateStoreBestSellerView();

    // Keep greeting and date in sync every 60s
    setInterval(function() {
        try {
            var now = new Date();
            var h = now.getHours();
            var g = 'Selamat Malam';
            if (h >= 4 && h < 11) g = 'Selamat Pagi';
            else if (h >= 11 && h < 15) g = 'Selamat Siang';
            else if (h >= 15 && h < 18) g = 'Selamat Sore';
            var el = document.getElementById('realtimeGreeting');
            if (el) el.textContent = g;
            var dt = document.getElementById('realtimeDateSubtitle');
            if (dt) dt.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        } catch(e) {}
    }, 60000);
});
</script>
@endsection

