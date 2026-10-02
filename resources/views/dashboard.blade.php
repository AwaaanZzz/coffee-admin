@extends('layouts.app')

@section('title', 'Beranda Operasional')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
@endsection

@section('styles')
<style>
    /* Design Tokens Integration & Operational Style */
    .op-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .op-title {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--text-primary);
        margin: 0 0 4px 0;
        line-height: 1.2;
    }

    .op-subtitle {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin: 0;
    }

    .op-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Metric Cards (Calm, Typographic, No Icon Boxes) */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    @media (max-width: 991px) {
        .metric-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575px) {
        .metric-grid {
            grid-template-columns: 1fr;
        }
    }

    .metric-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-decoration: none;
        color: inherit;
        transition: border-color var(--transition);
    }

    .metric-card:hover {
        color: inherit;
        border-color: var(--text-secondary);
    }

    .metric-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--text-primary);
        font-variant-numeric: tabular-nums;
    }

    .metric-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    /* Attention Box */
    .attention-box {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-left: 3px solid var(--warning);
        border-radius: var(--radius);
        padding: 12px 18px;
        margin-bottom: 20px;
    }

    .attention-header {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
    }

    .attention-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .attention-item {
        font-size: 0.82rem;
        color: var(--text-secondary);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 3px 0;
        gap: 12px;
    }

    .attention-link {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-decoration: underline;
        white-space: nowrap;
    }

    .attention-link:hover {
        color: var(--text-primary);
    }

    /* Summary Bar */
    .period-summary-bar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 12px 18px;
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 20px;
    }

    .period-summary-bar strong {
        color: var(--text-primary);
    }

    /* Section Containers */
    .section-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .section-header {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .section-body {
        padding: 18px;
    }

    /* Operational Table */
    .table-operational {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.84rem;
        color: var(--text-primary);
    }

    .table-operational thead th {
        padding: 10px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
        background: transparent;
        white-space: nowrap;
    }

    .table-operational thead th.text-end {
        text-align: right;
    }

    .table-operational thead th.text-center {
        text-align: center;
    }

    .table-operational tbody tr {
        border-bottom: 1px solid var(--border);
    }

    .table-operational tbody td {
        padding: 11px 12px;
        vertical-align: middle;
    }

    .font-tabular {
        font-variant-numeric: tabular-nums;
    }

    /* Portion Bar */
    .portion-bar-container {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: flex-end;
    }

    .portion-bar-track {
        width: 44px;
        height: 5px;
        background: var(--border);
        border-radius: 2px;
        overflow: hidden;
    }

    .portion-bar-fill {
        height: 100%;
        background: #B8742F;
        border-radius: 2px;
    }

    .portion-pct {
        font-size: 0.78rem;
        font-variant-numeric: tabular-nums;
        min-width: 40px;
        text-align: right;
        color: var(--text-secondary);
    }

    /* Total Row */
    .table-total-row td {
        font-weight: 700;
        color: var(--text-primary);
        border-top: 1px solid var(--border);
        border-bottom: 3px double var(--border);
        padding-top: 12px;
        padding-bottom: 12px;
        background: transparent;
    }

    /* Store Sub-row Breakdown */
    .store-subrow {
        background: var(--bg-subtle);
    }

    .store-subrow-inner {
        padding: 14px 16px;
    }

    .store-toggle-btn {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        color: inherit;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .store-toggle-btn:hover {
        color: var(--accent);
    }

    /* Tooltip helper */
    .info-tooltip-icon {
        display: inline-block;
        color: var(--text-muted);
        cursor: help;
        margin-left: 3px;
        vertical-align: -2px;
    }

    .info-tooltip-icon:hover {
        color: var(--text-primary);
    }
</style>
@endsection

@section('content')
    {{-- 1. Header --}}
    <div class="op-header">
        <div>
            <h1 class="op-title">Beranda operasional</h1>
            <p class="op-subtitle">Monitoring persediaan, kinerja penjualan toko mitra, dan laba operasional.</p>
            <span id="realtimeGreeting" class="d-none"></span>
            <span id="realtimeDateSubtitle" class="d-none"></span>
        </div>
        <div class="op-actions">
            <a href="{{ route('sales.create') }}" class="btn btn-accent">
                <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                <span>Catat penjualan</span>
            </a>
            <a href="{{ route('stock.index') }}" class="btn btn-outline-modern">
                <i data-lucide="printer" style="width: 15px; height: 15px;"></i>
                <span>Cetak barcode</span>
            </a>
            <button type="button" class="btn btn-outline-modern" onclick="window.location.reload()">
                <i data-lucide="refresh-cw" style="width: 15px; height: 15px;"></i>
                <span>Segarkan data</span>
            </button>
        </div>
    </div>

    {{-- 2. Baris Metrik (Maksimal 4, Tanpa Kotak Ikon) --}}
    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-value">{{ $totalToko ?? 0 }}</div>
            <div class="metric-label">Toko mitra aktif</div>
        </div>
        <div class="metric-card">
            <div class="metric-value">{{ number_format($totalStock ?? 0, 0, ',', '.') }}</div>
            <div class="metric-label">Stok tersedia (pcs)</div>
        </div>
        <div class="metric-card">
            <div class="metric-value">{{ number_format($totalLaku ?? 0, 0, ',', '.') }}</div>
            <div class="metric-label">Terjual periode ini (pcs)</div>
        </div>
        <a href="{{ route('stock.index') }}" class="metric-card">
            <div class="metric-value {{ ($expiringSoon ?? 0) > 0 ? 'text-danger' : '' }}">
                {{ $expiringSoon ?? 0 }}
            </div>
            <div class="metric-label">Mendekati kedaluwarsa</div>
        </a>
    </div>

    {{-- 3. Perlu Perhatian (Hanya tampil jika ada isinya) --}}
    @php
        $attentionItems = [];
        if (($expiringSoon ?? 0) > 0) {
            $attentionItems[] = [
                'text' => ($expiringSoon) . ' batch stok mendekati tanggal kedaluwarsa dalam 7 hari ke depan.',
                'link' => route('stock.index'),
                'link_text' => 'Periksa stok',
            ];
        }

        $zeroSaleStores = [];
        foreach ($storeAnalytics ?? [] as $sa) {
            if (($sa['total_qty'] ?? 0) === 0) {
                $zeroSaleStores[] = $sa['store_name'];
            }
        }
        if (count($zeroSaleStores) > 0) {
            $storeNamesStr = implode(', ', array_slice($zeroSaleStores, 0, 3));
            if (count($zeroSaleStores) > 3) {
                $storeNamesStr .= ' dan ' . (count($zeroSaleStores) - 3) . ' toko lainnya';
            }
            $attentionItems[] = [
                'text' => count($zeroSaleStores) . ' toko mitra belum memiliki catatan penjualan periode ini (' . $storeNamesStr . ').',
                'link' => route('sales.create'),
                'link_text' => 'Catat penjualan',
            ];
        }

        $hasZeroPriceOrModal = false;
        foreach ($storeAnalytics ?? [] as $sa) {
            foreach ($sa['products'] ?? [] as $pr) {
                if ($pr['qty'] > 0 && ($pr['price'] <= 0)) {
                    $hasZeroPriceOrModal = true;
                    break 2;
                }
            }
        }
        if ($hasZeroPriceOrModal) {
            $attentionItems[] = [
                'text' => 'Terdapat varian kopi terjual dengan harga satuan Rp 0.',
                'link' => route('sales.index'),
                'link_text' => 'Periksa data penjualan',
            ];
        }
    @endphp

    @if(count($attentionItems) > 0)
    <div class="attention-box">
        <div class="attention-header">
            <i data-lucide="alert-circle" style="width: 14px; height: 14px; color: var(--warning);"></i>
            <span>Perlu perhatian</span>
        </div>
        <ul class="attention-list">
            @foreach($attentionItems as $item)
            <li class="attention-item">
                <span>{{ $item['text'] }}</span>
                <a href="{{ $item['link'] }}" class="attention-link">{{ $item['link_text'] }}</a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- 4. Kalimat Ringkasan Periode (Satu Baris) --}}
    @php
        $periodeStr = $profitLossData->first()?->periode ?? \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY');
        $sumPemasukan = $financeSummary['total_pemasukan'] ?? 0;
        $sumLaba = $financeSummary['total_laba'] ?? 0;
        $sumMargin = $financeSummary['average_margin'] ?? 0;
        $sumPcs = $totalLaku ?? 0;
    @endphp
    <div class="period-summary-bar">
        <strong>{{ $periodeStr }}:</strong> {{ number_format($sumPcs, 0, ',', '.') }} pcs terjual, penjualan Rp {{ number_format($sumPemasukan, 0, ',', '.') }}, laba kotor Rp {{ number_format($sumLaba, 0, ',', '.') }} ({{ number_format($sumMargin, 1, ',', '.') }}%).
    </div>

    {{-- 5. Tabel Penjualan dan Laba per Toko --}}
    @php
        $plMap = ($profitLossData ?? collect())->keyBy('store_name');
        $totalAllRevenue = $sumPemasukan;
        if ($totalAllRevenue <= 0 && !empty($storeAnalytics)) {
            $totalAllRevenue = collect($storeAnalytics)->sum('total_revenue');
        }

        $sortedStoreAnalytics = collect($storeAnalytics ?? [])->sortByDesc('total_revenue');

        $calcTotalQty = 0;
        $calcTotalRev = 0;
        $calcTotalHpp = 0;
        $calcTotalLaba = 0;
    @endphp

    <div class="section-card">
        <div class="section-header">
            <h2 class="section-title">Penjualan dan laba per toko</h2>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Periode: <strong>{{ $periodeStr }}</strong></span>
                <a href="{{ route('coffee-types.modal') }}" class="btn btn-sm btn-outline-modern" title="Kelola modal HPP tiap jenis kopi">
                    Kelola modal HPP
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-operational">
                <thead>
                    <tr>
                        <th>Toko mitra</th>
                        <th>Penanggung jawab</th>
                        <th class="text-end">Terjual (pcs)</th>
                        <th class="text-end">Penjualan</th>
                        <th class="text-end">HPP & kemasan</th>
                        <th class="text-end">
                            Laba kotor
                            <span class="info-tooltip-icon" title="Laba kotor = Penjualan dikurangi HPP dan kemasan" data-bs-toggle="tooltip">
                                <i data-lucide="info" style="width: 12px; height: 12px;"></i>
                            </span>
                        </th>
                        <th class="text-end">
                            Margin
                            <span class="info-tooltip-icon" title="Margin = Laba kotor dibagi Penjualan" data-bs-toggle="tooltip">
                                <i data-lucide="info" style="width: 12px; height: 12px;"></i>
                            </span>
                        </th>
                        <th class="text-end">
                            Porsi
                            <span class="info-tooltip-icon" title="Porsi = Penjualan toko dibagi total penjualan semua toko" data-bs-toggle="tooltip">
                                <i data-lucide="info" style="width: 12px; height: 12px;"></i>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sortedStoreAnalytics as $sid => $sa)
                        @php
                            $qty = (int) ($sa['total_qty'] ?? 0);
                            $rev = (float) ($sa['total_revenue'] ?? 0);
                            $pl = $plMap->get($sa['store_name']);
                            $hpp = (float) ($pl?->pengeluaran ?? 0);
                            $laba = (float) ($pl?->laba ?? ($rev - $hpp));
                            $margin = $rev > 0 ? (($laba / $rev) * 100) : null;
                            $porsi = $totalAllRevenue > 0 ? (($rev / $totalAllRevenue) * 100) : 0;

                            $calcTotalQty += $qty;
                            $calcTotalRev += $rev;
                            $calcTotalHpp += $hpp;
                            $calcTotalLaba += $laba;
                        @endphp

                        @if($qty === 0 && $rev <= 0)
                            <tr>
                                <td>
                                    <span class="text-muted fw-medium">{{ $sa['store_name'] }}</span>
                                </td>
                                <td colspan="7" class="text-muted">Belum ada penjualan</td>
                            </tr>
                        @else
                            <tr>
                                <td>
                                    <button type="button" class="store-toggle-btn store-row-toggle" data-target="breakdown{{ $sid }}" aria-expanded="false" title="Klik untuk lihat rincian varian">
                                        <i data-lucide="chevron-right" style="width: 13px; height: 13px;"></i>
                                        <span>{{ $sa['store_name'] }}</span>
                                    </button>
                                </td>
                                <td class="text-muted">{{ $sa['penanggung_jawab'] ?: '-' }}</td>
                                <td class="text-end font-tabular">{{ number_format($qty, 0, ',', '.') }}</td>
                                <td class="text-end font-tabular">{{ number_format($rev, 0, ',', '.') }}</td>
                                <td class="text-end font-tabular">{{ number_format($hpp, 0, ',', '.') }}</td>
                                <td class="text-end font-tabular {{ $laba < 0 ? 'text-danger' : '' }}">{{ number_format($laba, 0, ',', '.') }}</td>
                                <td class="text-end font-tabular">{{ $margin !== null ? (number_format($margin, 1, ',', '.') . '%') : '-' }}</td>
                                <td class="text-end">
                                    <div class="portion-bar-container">
                                        <div class="portion-bar-track">
                                            <div class="portion-bar-fill" style="width: {{ min(100, $porsi) }}%;"></div>
                                        </div>
                                        <span class="portion-pct">{{ number_format($porsi, 1, ',', '.') }}%</span>
                                    </div>
                                </td>
                            </tr>
                            {{-- Expandable Product Breakdown Subrow --}}
                            <tr id="breakdown{{ $sid }}" class="store-subrow d-none">
                                <td colspan="8" class="p-0">
                                    <div class="store-subrow-inner">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small text-muted fw-semibold">Rincian varian kopi di {{ $sa['store_name'] }}:</span>
                                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none switch-store-chart" data-store-id="{{ $sid }}" style="font-size: 0.78rem;">
                                                Tampilkan di grafik varian
                                            </button>
                                        </div>
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.8rem;">
                                            <thead>
                                                <tr class="text-muted border-bottom">
                                                    <th>Varian</th>
                                                    <th>Kategori</th>
                                                    <th class="text-end">Terjual (pcs)</th>
                                                    <th class="text-end">Harga satuan (Rp)</th>
                                                    <th class="text-end">Penjualan (Rp)</th>
                                                    <th class="text-end">Sisa stok</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sa['products'] as $p)
                                                <tr>
                                                    <td class="fw-semibold">{{ $p['name'] }}</td>
                                                    <td class="text-muted">{{ ucfirst($p['category']) }}</td>
                                                    <td class="text-end font-tabular">{{ number_format($p['qty'], 0, ',', '.') }}</td>
                                                    <td class="text-end font-tabular">{{ number_format($p['price'], 0, ',', '.') }}</td>
                                                    <td class="text-end font-tabular">{{ number_format($p['revenue'], 0, ',', '.') }}</td>
                                                    <td class="text-end font-tabular text-muted">{{ number_format($p['sisa_stock'], 0, ',', '.') }} pcs</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data toko mitra.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($sortedStoreAnalytics->isNotEmpty())
                <tfoot>
                    <tr class="table-total-row">
                        <td colspan="2">Total</td>
                        <td class="text-end font-tabular">{{ number_format($calcTotalQty, 0, ',', '.') }}</td>
                        <td class="text-end font-tabular">{{ number_format($calcTotalRev, 0, ',', '.') }}</td>
                        <td class="text-end font-tabular">{{ number_format($calcTotalHpp, 0, ',', '.') }}</td>
                        <td class="text-end font-tabular {{ $calcTotalLaba < 0 ? 'text-danger' : '' }}">{{ number_format($calcTotalLaba, 0, ',', '.') }}</td>
                        <td class="text-end font-tabular">{{ $calcTotalRev > 0 ? (number_format(($calcTotalLaba / $calcTotalRev) * 100, 1, ',', '.') . '%') : '-' }}</td>
                        <td class="text-end font-tabular">{{ $calcTotalRev > 0 ? '100,0%' : '-' }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- 6. Bagian Varian Terlaris (Dropdown Toko + Switch Pcs/Rp + Tabel Peringkat & Grafik Batang Bersyarat) --}}
    <div class="section-card" id="variantSection">
        <div class="section-header">
            <h2 class="section-title">Varian terlaris</h2>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Store Selector Dropdown --}}
                <select id="storeSelectDropdown" class="form-select form-select-sm" style="width: auto; min-width: 170px;">
                    <option value="all">Semua toko</option>
                    @foreach($storeAnalytics ?? [] as $sid => $sa)
                        <option value="{{ $sid }}">{{ $sa['store_name'] }}</option>
                    @endforeach
                </select>

                {{-- Metric Switcher --}}
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-sm btn-outline-modern active" id="btnMetricQty">
                        Pcs
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-modern" id="btnMetricRev">
                        Rupiah
                    </button>
                </div>
            </div>
        </div>

        <div class="section-body">
            {{-- Hidden elements preserved for JS safety --}}
            <span id="kpiStoreName" class="d-none"></span>
            <span id="kpiTopCoffee" class="d-none"></span>
            <span id="kpiTotalQty" class="d-none"></span>
            <span id="kpiTotalRevenue" class="d-none"></span>
            <span id="chartStoreTitle" class="d-none"></span>
            <span id="chartMetricTitle" class="d-none"></span>
            <span id="tableStoreTitle" class="d-none"></span>
            <span id="colMetricHeader" class="d-none"></span>

            <div class="row g-4 align-items-start">
                {{-- Conditional Bar Chart Container (Only shown when >= 3 variants sold) --}}
                <div class="col-lg-7 d-none" id="chartCol">
                    <div style="height: 320px; position: relative;">
                        <canvas id="storeBestSellerChart"></canvas>
                    </div>
                </div>

                {{-- Leaderboard Table --}}
                <div class="col-12" id="tableCol">
                    <div class="table-responsive">
                        <table class="table-operational">
                            <thead>
                                <tr>
                                    <th style="width: 38px;">#</th>
                                    <th>Varian</th>
                                    <th class="text-end">Terjual (pcs)</th>
                                    <th class="text-end">Penjualan (Rp)</th>
                                    <th class="text-end" style="width: 120px;">
                                        Porsi
                                        <span class="info-tooltip-icon" title="Porsi = Penjualan varian dibagi total penjualan" data-bs-toggle="tooltip">
                                            <i data-lucide="info" style="width: 12px; height: 12px;"></i>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="storeLeaderboardBody">
                                {{-- Dynamically populated via JS --}}
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
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Initialize Bootstrap tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }

    function checkIsDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        Chart.defaults.animation = false; // No bouncing / jumping
    }

    // Expand / collapse store breakdown rows
    document.querySelectorAll('.store-row-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const targetRow = document.getElementById(targetId);
            if (targetRow) {
                const isHidden = targetRow.classList.contains('d-none');
                targetRow.classList.toggle('d-none');
                this.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                const chevron = this.querySelector('[data-lucide="chevron-right"], [data-lucide="chevron-down"]');
                if (chevron) {
                    chevron.setAttribute('data-lucide', isHidden ? 'chevron-down' : 'chevron-right');
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            }
        });
    });

    // ==========================================
    // Analisis Varian Terlaris (Chart & Leaderboard)
    // ==========================================
    const storeAnalyticsData = {!! json_encode($storeAnalytics ?? []) !!};
    const globalProductsAgg = {!! json_encode($allStoreProductsAgg ?? []) !!};

    let activeStoreId = 'all';
    let activeMetric = 'qty'; // 'qty' | 'revenue'
    let storeChartInstance = null;

    const ctxStoreBest = document.getElementById('storeBestSellerChart');
    const storeLeaderboardBody = document.getElementById('storeLeaderboardBody');
    const chartCol = document.getElementById('chartCol');
    const tableCol = document.getElementById('tableCol');
    const storeSelectDropdown = document.getElementById('storeSelectDropdown');
    const btnMetricQty = document.getElementById('btnMetricQty');
    const btnMetricRev = document.getElementById('btnMetricRev');

    function updateStoreBestSellerView() {
        let isDark = checkIsDark();
        let gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.04)';

        let items = [];
        let totalRev = 0;

        if (activeStoreId === 'all') {
            items = globalProductsAgg ? [...globalProductsAgg] : [];
            totalRev = items.reduce((acc, curr) => acc + (curr.total_revenue || 0), 0);
        } else {
            const sData = storeAnalyticsData[activeStoreId];
            items = (sData && sData.products) ? [...sData.products] : [];
            totalRev = sData ? (sData.total_revenue || 0) : 0;
        }

        // Sort items by active metric
        items.sort(function(a, b) {
            const valA = activeMetric === 'qty' ? (a.qty ?? a.total_qty ?? 0) : (a.revenue ?? a.total_revenue ?? 0);
            const valB = activeMetric === 'qty' ? (b.qty ?? b.total_qty ?? 0) : (b.revenue ?? b.total_revenue ?? 0);
            return valB - valA;
        });

        // Filter variants that have sales
        const activeSoldItems = items.filter(function(it) {
            const q = it.qty ?? it.total_qty ?? 0;
            return q > 0;
        });

        // Render Leaderboard Table
        let tableHtml = '';
        if (items.length === 0 || activeSoldItems.length === 0) {
            tableHtml = '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada penjualan tercatat pada pilihan ini.</td></tr>';
        } else {
            items.forEach(function(item, idx) {
                const qty = item.qty ?? item.total_qty ?? 0;
                const rev = item.revenue ?? item.total_revenue ?? 0;
                const sharePct = totalRev > 0 ? ((rev / totalRev) * 100).toFixed(1) : 0;
                const catStr = item.category ? (item.category.charAt(0).toUpperCase() + item.category.slice(1)) : '';

                tableHtml += `
                    <tr>
                        <td class="text-muted font-tabular" style="font-size: 0.8rem;">${idx + 1}</td>
                        <td>
                            <div class="fw-semibold text-dark">${item.name}</div>
                            <div class="text-muted" style="font-size: 0.74rem;">${catStr}</div>
                        </td>
                        <td class="text-end font-tabular">${Number(qty).toLocaleString('id-ID')}</td>
                        <td class="text-end font-tabular">${Number(rev).toLocaleString('id-ID')}</td>
                        <td class="text-end">
                            <div class="portion-bar-container">
                                <div class="portion-bar-track">
                                    <div class="portion-bar-fill" style="width: ${Math.min(100, sharePct)}%;"></div>
                                </div>
                                <span class="portion-pct">${String(sharePct).replace('.', ',')}%</span>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        if (storeLeaderboardBody) {
            storeLeaderboardBody.innerHTML = tableHtml;
        }

        // Conditional Bar Chart: ONLY rendered if >= 3 sold variants exist
        if (activeSoldItems.length >= 3 && ctxStoreBest) {
            if (chartCol) chartCol.classList.remove('d-none');
            if (tableCol) {
                tableCol.classList.remove('col-12');
                tableCol.classList.add('col-lg-5');
            }

            const chartLabels = activeSoldItems.slice(0, 8).map(it => it.name);
            const chartData = activeSoldItems.slice(0, 8).map(it => activeMetric === 'qty' ? (it.qty ?? it.total_qty ?? 0) : (it.revenue ?? it.total_revenue ?? 0));

            if (storeChartInstance) {
                storeChartInstance.destroy();
            }

            storeChartInstance = new Chart(ctxStoreBest, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: '#B8742F', // Single roasted copper data accent
                        borderRadius: 4,
                        maxBarThickness: 38
                    }]
                },
                options: {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 10,
                            cornerRadius: 6,
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            callbacks: {
                                label: function(ctx) {
                                    const val = ctx.raw || 0;
                                    return activeMetric === 'qty'
                                        ? ` Terjual: ${Number(val).toLocaleString('id-ID')} pcs`
                                        : ` Penjualan: Rp ${Number(val).toLocaleString('id-ID')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: isDark ? '#94A3B8' : '#64748B',
                                font: { size: 11, weight: '500' },
                                maxRotation: 0,
                                autoSkip: true
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: {
                                color: isDark ? '#94A3B8' : '#64748B',
                                font: { size: 10 },
                                callback: function(val) {
                                    if (activeMetric === 'revenue') {
                                        if (val >= 1000000) return (val / 1000000).toFixed(1) + ' jt';
                                        if (val >= 1000) return (val / 1000).toFixed(0) + ' rb';
                                        return val;
                                    }
                                    return val;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // Less than 3 variants sold: Hide chart space completely
            if (chartCol) chartCol.classList.add('d-none');
            if (tableCol) {
                tableCol.classList.remove('col-lg-5');
                tableCol.classList.add('col-12');
            }
            if (storeChartInstance) {
                storeChartInstance.destroy();
                storeChartInstance = null;
            }
        }
    }

    // Dropdown change listener
    if (storeSelectDropdown) {
        storeSelectDropdown.addEventListener('change', function() {
            activeStoreId = this.value;
            updateStoreBestSellerView();
        });
    }

    // Metric toggle listeners
    if (btnMetricQty) {
        btnMetricQty.addEventListener('click', function() {
            activeMetric = 'qty';
            this.classList.add('active');
            if (btnMetricRev) btnMetricRev.classList.remove('active');
            updateStoreBestSellerView();
        });
    }

    if (btnMetricRev) {
        btnMetricRev.addEventListener('click', function() {
            activeMetric = 'revenue';
            this.classList.add('active');
            if (btnMetricQty) btnMetricQty.classList.remove('active');
            updateStoreBestSellerView();
        });
    }

    // Switch store chart from store table subrows
    document.querySelectorAll('.switch-store-chart').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const sid = this.dataset.storeId;
            activeStoreId = sid;
            if (storeSelectDropdown) {
                storeSelectDropdown.value = sid;
            }
            updateStoreBestSellerView();
            const variantSec = document.getElementById('variantSection');
            if (variantSec) {
                variantSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Dark mode toggle listener
    const themeToggleBtn = document.getElementById('themeToggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            setTimeout(updateStoreBestSellerView, 150);
        });
    }

    // Initial render
    updateStoreBestSellerView();
});
</script>
@endsection
