@extends('layouts.app')
@section('title', 'Daftar Toko Mitra')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <i data-lucide="chevron-right"></i>
    <span>Daftar Toko</span>
@endsection

@section('content')
    @php
        $storesWithStock = $stores->filter(function($s) {
            return $s->stockBatches->where('status', '!=', 'tarik')->count() > 0;
        })->count();
        $totalStores = $stores->total() ?? $stores->count();
        $storesNoStock = $totalStores - $storesWithStock;
    @endphp

    {{-- Page Header --}}
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
        <div>
            <h3 class="page-title mb-1">Daftar Toko Mitra</h3>
            <p class="page-subtitle text-muted mb-0">Kelola direktori kemitraan toko, titik koordinat peta distribusi, dan monitoring stok.</p>
        </div>
        <div class="page-actions d-flex align-items-center gap-2">
            <a href="{{ route('stores.create') }}" class="btn btn-accent d-flex align-items-center gap-1.5">
                <i data-lucide="plus" style="width:16px;height:16px;"></i>
                <span>Tambah Toko Mitra</span>
            </a>
        </div>
    </div>

    {{-- Main Container Card --}}
    <div class="card-modern shadow-none border">
        {{-- Unified Card Header: Search, Filter Tabs, and View Switcher --}}
        <div class="card-header-modern p-3 bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-top-left-radius: var(--radius); border-top-right-radius: var(--radius);">
            {{-- Left Side: Live Search & Filter Pills --}}
            <div class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                <div class="position-relative" style="min-width: 240px; max-width: 320px;">
                    <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted); pointer-events: none;"></i>
                    <input type="text" id="storeLiveSearch" class="form-control form-control-sm ps-4" placeholder="Cari nama toko, alamat, PJ..." autocomplete="off" style="border-radius: 6px;">
                    <button type="button" id="btnResetSearch" class="btn btn-link text-muted p-0 d-none" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); text-decoration: none;" title="Bersihkan">
                        <i data-lucide="x" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <button type="button" class="filter-store-btn active" onclick="filterStoreStock('all', this)">
                        <span>Semua</span>
                        <span class="badge bg-secondary text-white rounded-pill px-1.5" style="font-size: 0.65rem;">{{ $totalStores }}</span>
                    </button>
                    <button type="button" class="filter-store-btn" onclick="filterStoreStock('has_stock', this)">
                        <span>Punya Stok</span>
                        <span class="badge bg-success text-white rounded-pill px-1.5" style="font-size: 0.65rem;">{{ $storesWithStock }}</span>
                    </button>
                    <button type="button" class="filter-store-btn" onclick="filterStoreStock('no_stock', this)">
                        <span>Stok Kosong</span>
                        <span class="badge bg-light text-dark rounded-pill px-1.5 border" style="font-size: 0.65rem;">{{ $storesNoStock }}</span>
                    </button>
                </div>
            </div>

            {{-- Right Side: View Switcher (Tabel vs Peta) --}}
            <div class="d-flex align-items-center gap-2">
                <div class="view-switch-group">
                    <button type="button" class="view-switch-btn active" id="tabBtnTable" onclick="switchStoreView('table')">
                        <i data-lucide="list" style="width: 14px; height: 14px;"></i>
                        <span>Tabel</span>
                    </button>
                    <button type="button" class="view-switch-btn" id="tabBtnMap" onclick="switchStoreView('map')">
                        <i data-lucide="map" style="width: 14px; height: 14px;"></i>
                        <span>Peta Mitra</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Pane 1: Table --}}
        <div id="storeTablePane" style="display: block;">
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table-modern w-100 m-0 align-middle" id="storeMasterTable">
                        <thead>
                            <tr>
                                <th style="width: 48px; text-align: center; padding-left: 14px;">#</th>
                                <th style="width: 27%;">Toko Mitra</th>
                                <th style="width: 27%;">Alamat & Peta</th>
                                <th style="width: 16%;">Penanggung Jawab</th>
                                <th style="width: 13%;">Tgl. Kerja Sama</th>
                                <th style="width: 14%;">Status Stok</th>
                                <th class="text-end" style="width: 100px; padding-right: 18px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stores as $store)
                                @php
                                    $activeBatchCount = $store->stockBatches->where('status', '!=', 'tarik')->count();
                                    $hasStock = $activeBatchCount > 0;
                                @endphp
                                <tr class="store-row" data-store-id="{{ $store->id }}" data-has-stock="{{ $hasStock ? '1' : '0' }}" style="cursor: pointer;" onclick="toggleStock({{ $store->id }})">
                                    {{-- # & Chevron --}}
                                    <td class="text-center" style="padding-left: 14px;">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <i data-lucide="chevron-right" class="stock-chevron text-muted" id="chevron-{{ $store->id }}" style="width:15px;height:15px;transition:transform 0.2s;" title="Klik untuk rincian stok"></i>
                                            <span class="text-muted tabular-nums small" style="font-size:0.75rem;">{{ $loop->iteration }}</span>
                                        </div>
                                    </td>

                                    {{-- Store Name --}}
                                    <td>
                                        <a href="{{ route('stores.show', $store) }}" class="fw-bold text-dark text-decoration-none hover-accent" style="font-size:0.875rem;" onclick="event.stopPropagation();">
                                            {{ $store->name }}
                                        </a>
                                    </td>

                                    {{-- Address & Maps Link --}}
                                    <td>
                                        @if($store->alamat)
                                            @if($store->has_coordinates)
                                                <a href="{{ $store->google_maps_url }}" target="_blank" class="store-address-link" title="Buka lokasi di Google Maps" onclick="event.stopPropagation();">
                                                    <i data-lucide="map-pin" class="store-map-icon"></i>
                                                    <span class="store-address-text">{{ $store->alamat }}</span>
                                                    <i data-lucide="external-link" class="store-ext-icon"></i>
                                                </a>
                                            @else
                                                <div class="d-flex align-items-center gap-1.5 text-secondary small">
                                                    <i data-lucide="map-pin" style="width:13px;height:13px;color:var(--text-muted);opacity:0.6;"></i>
                                                    <span>{{ $store->alamat }}</span>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small fst-italic">Belum diisi</span>
                                        @endif
                                    </td>

                                    {{-- Person in Charge --}}
                                    <td>
                                        @if($store->penanggung_jawab)
                                            <div class="d-flex align-items-center gap-1.5 text-secondary small">
                                                <i data-lucide="user" style="width:13px;height:13px;color:var(--text-muted);"></i>
                                                <span>{{ $store->penanggung_jawab }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted small fst-italic">Belum diisi</span>
                                        @endif
                                    </td>

                                    {{-- Cooperation Date --}}
                                    <td class="tabular-nums text-secondary small">
                                        {{ $store->tgl_kerjasama->format('d M Y') }}
                                    </td>

                                    {{-- Stock Badge --}}
                                    <td>
                                        @if($hasStock)
                                            <span class="badge-store-stock badge-has-stock" title="Klik baris untuk melihat rincian batch">
                                                <i data-lucide="package-check" style="width:13px;height:13px;"></i>
                                                <span>{{ $activeBatchCount }} Batch Aktif</span>
                                            </span>
                                        @else
                                            <span class="badge-store-stock badge-no-stock" title="Klik baris untuk rincian">
                                                <i data-lucide="package" style="width:13px;height:13px;"></i>
                                                <span>0 Batch</span>
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="text-end" style="padding-right: 18px;" onclick="event.stopPropagation();">
                                        <div class="d-flex justify-content-end align-items-center gap-1.5">
                                            <a href="{{ route('stores.show', $store) }}" class="btn-action-icon" title="Detail Toko & Peta">
                                                <i data-lucide="eye"></i>
                                            </a>
                                            <a href="{{ route('stores.edit', $store) }}" class="btn-action-icon" title="Edit Toko">
                                                <i data-lucide="pencil"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button type="button" class="btn-action-icon" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Opsi Lainnya">
                                                    <i data-lucide="more-horizontal"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-1" style="min-width: 180px;">
                                                    <li>
                                                        <a class="dropdown-item py-1.5 px-3 small d-flex align-items-center gap-2" href="{{ route('stores.prices.edit', $store) }}">
                                                            <i data-lucide="tag" style="width:14px;height:14px;color:var(--text-muted);"></i>
                                                            <span>Atur Harga Kopi</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-1.5 px-3 small d-flex align-items-center gap-2" href="{{ route('stock.index', ['store_id' => $store->id]) }}">
                                                            <i data-lucide="package" style="width:14px;height:14px;color:var(--text-muted);"></i>
                                                            <span>Kelola Stok Toko</span>
                                                        </a>
                                                    </li>
                                                    @if($store->has_coordinates)
                                                        <li>
                                                            <a class="dropdown-item py-1.5 px-3 small d-flex align-items-center gap-2" href="{{ $store->google_maps_url }}" target="_blank">
                                                                <i data-lucide="navigation" style="width:14px;height:14px;color:var(--text-muted);"></i>
                                                                <span>Buka Google Maps ↗</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <form action="{{ route('stores.destroy', $store) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus toko {{ $store->name }}?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item py-1.5 px-3 small text-danger d-flex align-items-center gap-2">
                                                                <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                                                <span>Hapus Toko</span>
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Expandable Stock Detail Row (Accordion) --}}
                                <tr class="stock-detail-row" id="stock-row-{{ $store->id }}" style="display:none;">
                                    <td colspan="7" class="p-0 border-0">
                                        <div class="stock-accordion-container">
                                            <div class="stock-accordion-header">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="accordion-icon-box">
                                                        <i data-lucide="package"></i>
                                                    </div>
                                                    <div>
                                                        <span class="accordion-store-title">Rincian Stok Batch ({{ $store->stockBatches->count() }} Batch) — {{ $store->name }}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ route('stock.index', ['store_id' => $store->id]) }}" class="btn btn-sm btn-outline-modern d-flex align-items-center gap-1.5" style="font-size:0.75rem; padding: 4px 10px;">
                                                        <span>Kelola di Modul Stok</span>
                                                        <i data-lucide="arrow-right" style="width:13px;height:13px;"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            @if($store->stockBatches->count() > 0)
                                                <div class="table-responsive bg-white rounded border" style="overflow: hidden;">
                                                    <table class="table-modern w-100 m-0" style="font-size:0.8125rem;">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 15%; padding-left: 14px;">Kode Produksi</th>
                                                                <th style="width: 18%;">Jenis Kopi</th>
                                                                <th style="width: 12%;">Tgl. Masuk</th>
                                                                <th style="width: 15%;">Tgl. Kedaluwarsa</th>
                                                                <th class="text-end" style="width: 8%;">Awal</th>
                                                                <th class="text-end" style="width: 8%;">Laku</th>
                                                                <th class="text-end" style="width: 8%;">Sisa</th>
                                                                <th class="text-end" style="width: 12%;">Total Nilai</th>
                                                                <th class="text-center" style="width: 10%; padding-right: 14px;">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($store->stockBatches->sortByDesc('tgl_stock') as $batch)
                                                                @php
                                                                    $isExp = $batch->is_expired;
                                                                    $isExpSoon = $batch->is_expiring_soon;
                                                                @endphp
                                                                <tr style="{{ $isExp ? 'background: rgba(220, 38, 38, 0.04);' : ($isExpSoon ? 'background: rgba(217, 119, 6, 0.04);' : '') }}">
                                                                    <td style="padding-left: 14px;">
                                                                        <span class="font-monospace fw-bold text-dark" style="font-size:0.78rem;">
                                                                            {{ $batch->kode_produksi ?? '-' }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center gap-1.5">
                                                                            <span class="fw-semibold text-dark">{{ $batch->coffeeType->name ?? 'Belum diisi' }}</span>
                                                                            @if(isset($batch->coffeeType->kategori))
                                                                                @php
                                                                                    $kat = strtolower($batch->coffeeType->kategori);
                                                                                @endphp
                                                                                <span class="badge-mini {{ $kat == 'robusta' ? 'badge-mini-robusta' : 'badge-mini-arabika' }}">
                                                                                    {{ ucfirst($kat) }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="tabular-nums text-muted small">{{ $batch->tgl_stock ? $batch->tgl_stock->format('d/m/Y') : '-' }}</td>
                                                                    <td class="tabular-nums small">
                                                                        <span class="{{ $isExp ? 'text-danger fw-bold' : ($isExpSoon ? 'text-warning fw-bold' : 'text-secondary') }}">
                                                                            {{ $batch->tgl_exp ? $batch->tgl_exp->format('d/m/Y') : '-' }}
                                                                        </span>
                                                                        @if($isExpSoon)
                                                                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Segera Exp</span>
                                                                        @elseif($isExp)
                                                                            <span class="badge bg-danger text-white ms-1" style="font-size:0.65rem;">Expired</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end tabular-nums text-muted">{{ $batch->jumlah_stock }}</td>
                                                                    <td class="text-end tabular-nums text-muted">{{ $batch->laku }}</td>
                                                                    <td class="text-end tabular-nums fw-bold text-dark" style="font-size:0.875rem;">{{ $batch->sisa }}</td>
                                                                    <td class="text-end tabular-nums fw-semibold">Rp {{ number_format($batch->total, 0, ',', '.') }}</td>
                                                                    <td class="text-center" style="padding-right: 14px;">
                                                                        @php
                                                                            $statusBadge = match($batch->status) {
                                                                                'normal' => 'badge-subtle-success',
                                                                                'tarik' => 'badge-subtle-warning',
                                                                                'ganti' => 'badge-subtle-info',
                                                                                default => 'badge-subtle-secondary',
                                                                            };
                                                                        @endphp
                                                                        <span class="badge-modern {{ $statusBadge }} text-capitalize" style="font-size:0.7rem; padding: 3px 8px;">
                                                                            {{ $batch->status }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="empty-stock-box p-3 text-center rounded bg-white border">
                                                    <p class="text-muted small mb-2">Belum ada batch stok aktif yang tercatat untuk toko mitra ini.</p>
                                                    <a href="{{ route('stock.create', ['store_id' => $store->id]) }}" class="btn btn-sm btn-outline-modern" style="font-size:0.75rem;">
                                                        <i data-lucide="plus" style="width:12px;height:12px;"></i> Tambah Batch Stok
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state text-center py-5">
                                            <i data-lucide="store" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                            <h5>Belum ada data toko mitra.</h5>
                                            <p class="text-muted">Tambahkan toko baru untuk mulai mengelola kemitraan dan stok distribusi.</p>
                                            <a href="{{ route('stores.create') }}" class="btn btn-sm btn-accent mt-2">
                                                <i data-lucide="plus"></i> Tambah Toko Sekarang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($stores->hasPages())
                    <div class="p-3 border-top d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan <strong>{{ $stores->firstItem() }}</strong> - <strong>{{ $stores->lastItem() }}</strong> dari <strong>{{ $stores->total() }}</strong> toko
                        </div>
                        <div>
                            {{ $stores->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Pane 2: Store Network Map --}}
        <div id="storeMapPane" style="display: none;">
            <div class="card-header-modern d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 bg-white border-bottom">
                <div>
                    <h5 class="card-title-modern m-0">Peta Jaringan & Persebaran Toko Mitra</h5>
                    <p class="text-muted small m-0 mt-1">Pemetaan geografis lokasi toko mitra kerja sama Kopi Hiku Himu.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-modern badge-success">
                        <i data-lucide="map-pin" style="width:12px;height:12px;display:inline-block;vertical-align:-1px;"></i>
                        <span id="mapStoreCount">{{ $allStoresForMap->filter->has_coordinates->count() }}</span> Toko berkoordinat
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-modern d-flex align-items-center gap-1" id="btnFitAllStores">
                        <i data-lucide="maximize-2" style="width:13px;height:13px;"></i>
                        <span>Fokuskan Semua</span>
                    </button>
                </div>
            </div>
            <div class="card-body-modern p-0 position-relative">
                <div id="allStoresNetworkMap" style="height: 520px; width: 100%; z-index: 1;"></div>
            </div>
            <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-bottom-left-radius: var(--radius); border-bottom-right-radius: var(--radius);">
                <div class="text-muted small d-flex align-items-center gap-2">
                    <i data-lucide="info" style="width:15px;height:15px;color:var(--text-secondary);"></i>
                    <span>Klik pin kopi untuk melihat detail toko, penanggung jawab, jumlah batch stok, dan navigasi arah.</span>
                </div>
                <a href="{{ route('stores.create') }}" class="btn btn-sm btn-outline-modern">
                    <i data-lucide="plus" style="width:14px;height:14px;"></i> Tambah Toko Baru
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();

        // 1. Live Instant Search Filter
        const searchInput = document.getElementById('storeLiveSearch');
        const resetBtn = document.getElementById('btnResetSearch');
        const rows = document.querySelectorAll('.store-row');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                
                if (resetBtn) {
                    if (query.length > 0) {
                        resetBtn.classList.remove('d-none');
                    } else {
                        resetBtn.classList.add('d-none');
                    }
                }

                rows.forEach(row => {
                    const storeId = row.getAttribute('data-store-id');
                    const detailRow = document.getElementById('stock-row-' + storeId);
                    const rowText = row.innerText.toLowerCase();

                    if (rowText.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                        if (detailRow) detailRow.style.display = 'none';
                    }
                });
            });

            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    resetBtn.classList.add('d-none');
                    rows.forEach(row => row.style.display = '');
                    searchInput.focus();
                });
            }
        }

        // 2. Quick Stock Filter
        window.filterStoreStock = function(type, element) {
            document.querySelectorAll('.filter-store-btn').forEach(btn => btn.classList.remove('active'));
            if (element) element.classList.add('active');

            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

            rows.forEach(row => {
                const storeId = row.getAttribute('data-store-id');
                const detailRow = document.getElementById('stock-row-' + storeId);
                const hasStock = row.getAttribute('data-has-stock') === '1';
                const rowText = row.innerText.toLowerCase();

                let matchFilter = true;
                if (type === 'has_stock') matchFilter = hasStock;
                if (type === 'no_stock') matchFilter = !hasStock;

                let matchSearch = query.length === 0 || rowText.includes(query);

                if (matchFilter && matchSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                    if (detailRow) detailRow.style.display = 'none';
                }
            });
        };

        // 3. Leaflet Map Initialization
        const storesData = @json($storesMapData ?? []);
        let networkMap = null;
        let mapMarkersGroup = null;

        function initNetworkMap() {
            if (networkMap) return;

            networkMap = L.map('allStoresNetworkMap', {
                scrollWheelZoom: true
            }).setView([-7.9797, 112.6304], 12);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(networkMap);

            mapMarkersGroup = L.featureGroup().addTo(networkMap);

            const coffeeIcon = L.divIcon({
                className: 'custom-coffee-pin',
                html: `<div class="coffee-pin-marker">
                         <div class="coffee-pin-inner">
                           <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"></path><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"></path><line x1="6" y1="2" x2="6" y2="4"></line><line x1="10" y1="2" x2="10" y2="4"></line><line x1="14" y1="2" x2="14" y2="4"></line></svg>
                         </div>
                       </div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -36]
            });

            let validMarkersCount = 0;

            storesData.forEach(function(store) {
                if (store.has_coords && store.latitude && store.longitude) {
                    const lat = parseFloat(store.latitude);
                    const lng = parseFloat(store.longitude);

                    const popupContent = `
                        <div class="store-map-popup">
                            <div class="popup-title">${store.name}</div>
                            <div class="popup-sub">${store.alamat}</div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="popup-badge">PJ: ${store.penanggung_jawab}</span>
                                <span class="badge bg-light text-dark border" style="font-size:0.7rem;">${store.batches_count} batch stok</span>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="${store.show_url}" class="popup-btn flex-fill text-center">
                                    Detail Toko
                                </a>
                                <a href="${store.gmaps_url}" target="_blank" class="btn btn-sm btn-outline-secondary flex-fill text-center" style="font-size:0.75rem;">
                                    Google Maps ↗
                                </a>
                            </div>
                        </div>
                    `;

                    const marker = L.marker([lat, lng], { icon: coffeeIcon }).bindPopup(popupContent);
                    mapMarkersGroup.addLayer(marker);
                    validMarkersCount++;
                }
            });

            if (validMarkersCount > 0) {
                networkMap.fitBounds(mapMarkersGroup.getBounds(), { padding: [50, 50], maxZoom: 15 });
            }
        }

        window.switchStoreView = function(view) {
            const tablePane = document.getElementById('storeTablePane');
            const mapPane = document.getElementById('storeMapPane');
            const btnTable = document.getElementById('tabBtnTable');
            const btnMap = document.getElementById('tabBtnMap');

            if (view === 'table') {
                tablePane.style.display = 'block';
                mapPane.style.display = 'none';
                btnTable.classList.add('active');
                btnMap.classList.remove('active');
            } else {
                tablePane.style.display = 'none';
                mapPane.style.display = 'block';
                btnTable.classList.remove('active');
                btnMap.classList.add('active');
                
                initNetworkMap();
                setTimeout(() => {
                    if (networkMap) {
                        networkMap.invalidateSize();
                        if (mapMarkersGroup && mapMarkersGroup.getLayers().length > 0) {
                            networkMap.fitBounds(mapMarkersGroup.getBounds(), { padding: [50, 50], maxZoom: 15 });
                        }
                    }
                }, 150);
            }
        };

        const btnFitAll = document.getElementById('btnFitAllStores');
        if (btnFitAll) {
            btnFitAll.addEventListener('click', function() {
                if (networkMap && mapMarkersGroup && mapMarkersGroup.getLayers().length > 0) {
                    networkMap.fitBounds(mapMarkersGroup.getBounds(), { padding: [50, 50], maxZoom: 15 });
                }
            });
        }
    });

    // 4. Smooth Toggle Accordion
    function toggleStock(storeId) {
        const row = document.getElementById('stock-row-' + storeId);
        const chevron = document.getElementById('chevron-' + storeId);
        const parentRow = document.querySelector(`.store-row[data-store-id="${storeId}"]`);
        
        if (row.style.display === 'none') {
            document.querySelectorAll('.stock-detail-row').forEach(r => {
                r.style.display = 'none';
            });
            document.querySelectorAll('.stock-chevron').forEach(c => {
                c.style.transform = 'rotate(0deg)';
            });
            document.querySelectorAll('.store-row').forEach(sr => {
                sr.classList.remove('is-open');
            });
            
            row.style.display = 'table-row';
            chevron.style.transform = 'rotate(90deg)';
            if (parentRow) parentRow.classList.add('is-open');
            lucide.createIcons();
        } else {
            row.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
            if (parentRow) parentRow.classList.remove('is-open');
        }
    }
</script>
@endsection
