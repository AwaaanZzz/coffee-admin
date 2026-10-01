@extends('layouts.app')
@section('title', 'Daftar Toko Mitra')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('stores.index') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Daftar Toko</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Daftar Toko</h3>
            <p class="page-subtitle">Kelola semua data toko mitra kerja sama.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('stores.create') }}" class="btn btn-accent">
                <i data-lucide="plus"></i> Tambah Toko
            </a>
        </div>
    </div>

    {{-- View Mode Switcher --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-modern active d-flex align-items-center gap-2" id="tabBtnTable" onclick="switchStoreView('table')">
                <i data-lucide="list" style="width:15px;height:15px;"></i>
                <span>Daftar tabel toko</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-modern d-flex align-items-center gap-2" id="tabBtnMap" onclick="switchStoreView('map')">
                <i data-lucide="map" style="width:15px;height:15px;"></i>
                <span>Peta persebaran mitra</span>
            </button>
        </div>
        <div class="text-muted small d-none d-md-flex align-items-center gap-2">
            <i data-lucide="store" style="width:14px;height:14px;color:var(--text-muted);"></i>
            <span>Total <strong>{{ $stores->total() ?? $stores->count() }}</strong> toko mitra terdaftar</span>
        </div>
    </div>

    {{-- Pane 1: Table --}}
    <div id="storeTablePane" style="display: block;">
        <div class="card-modern">
            <div class="card-body-modern">
                <div class="table-responsive">
                        <table class="table-modern w-100">
                            <thead>
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th>#</th>
                                    <th>Nama toko</th>
                                    <th>Alamat & peta</th>
                                    <th>Penanggung jawab</th>
                                    <th>Tgl. kerja sama</th>
                                    <th>Stok</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stores as $store)
                                    <tr class="store-row" data-store-id="{{ $store->id }}" style="cursor: pointer;" onclick="toggleStock({{ $store->id }})">
                                        <td>
                                            <i data-lucide="chevron-right" class="stock-chevron" id="chevron-{{ $store->id }}" style="width:16px;height:16px;transition:transform 0.2s;color:var(--text-muted);"></i>
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-bold">
                                            <a href="{{ route('stores.show', $store) }}" class="text-dark" onclick="event.stopPropagation();">
                                                {{ $store->name }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($store->alamat)
                                                <span>{{ $store->alamat }}</span>
                                            @else
                                                <span class="text-muted">Belum diisi</span>
                                            @endif
                                            @if($store->has_coordinates)
                                                <a href="{{ $store->google_maps_url }}" target="_blank" class="badge-modern badge-neutral ms-1 text-decoration-none" title="Buka di Google Maps" onclick="event.stopPropagation();">
                                                    <i data-lucide="navigation" style="width:10px;height:10px;display:inline-block;vertical-align:-1px;"></i> Maps
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($store->penanggung_jawab)
                                                {{ $store->penanggung_jawab }}
                                            @else
                                                <span class="text-muted">Belum diisi</span>
                                            @endif
                                        </td>
                                        <td>{{ $store->tgl_kerjasama->format('d-m-Y') }}</td>
                                        <td>
                                            <span class="badge-modern badge-neutral">
                                                {{ $store->stockBatches->count() }} batch
                                            </span>
                                        </td>
                                        <td class="text-end" onclick="event.stopPropagation();">
                                            <div class="d-flex justify-content-end gap-2">
                                                @if($store->has_coordinates)
                                                    <a href="{{ $store->google_maps_url }}" target="_blank" class="btn btn-table-action" title="Buka di Google Maps">
                                                        <i data-lucide="navigation"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('stores.show', $store) }}" class="btn btn-table-action" title="Detail & Peta Toko">
                                                    <i data-lucide="eye"></i>
                                                </a>
                                                <a href="{{ route('stores.edit', $store) }}" class="btn btn-table-action" title="Edit Toko & Titik Peta">
                                                    <i data-lucide="edit"></i>
                                                </a>
                                                <a href="{{ route('stores.prices.edit', $store) }}" class="btn btn-table-action" title="Atur Harga">
                                                    <i data-lucide="tag"></i>
                                                </a>
                                                <form action="{{ route('stores.destroy', $store) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus toko ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-table-action text-danger" title="Hapus">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Expandable Stock Detail Row --}}
                                    <tr class="stock-detail-row" id="stock-row-{{ $store->id }}" style="display:none;">
                                        <td colspan="8" style="padding:0;border-top:none;">
                                            <div class="stock-detail-wrapper" style="background:var(--bg-primary);border-top:2px solid var(--accent);padding:16px 20px;margin:0;">
                                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                                                    <i data-lucide="package" style="width:18px;height:18px;color:var(--text-secondary);"></i>
                                                    <span style="font-weight:600;color:var(--text-primary);font-size:0.875rem;">Stok — {{ $store->name }}</span>
                                                    <span style="font-size:0.8rem;color:var(--text-muted);margin-left:auto;">{{ $store->stockBatches->count() }} batch ditemukan</span>
                                                </div>
                                                @if($store->stockBatches->count() > 0)
                                                    <div class="table-responsive" style="border-radius:var(--radius-sm);overflow:hidden;border:1px solid var(--border);">
                                                        <table class="table-modern w-100" style="margin:0;font-size:0.8125rem;">
                                                            <thead>
                                                                <tr style="background:var(--bg-subtle);">
                                                                    <th>Kode produksi</th>
                                                                    <th>Jenis kopi</th>
                                                                    <th>Tgl. stok</th>
                                                                    <th>Tgl. kedaluwarsa</th>
                                                                    <th class="text-end">Stok</th>
                                                                    <th class="text-end">Laku</th>
                                                                    <th class="text-end">Sisa</th>
                                                                    <th class="text-end">Total (Rp)</th>
                                                                    <th class="text-center">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($store->stockBatches->sortByDesc('tgl_stock') as $batch)
                                                                    <tr style="background:var(--bg-card);{{ $batch->isExpired ? 'opacity:0.5;' : ($batch->isExpiringSoon ? 'background:var(--danger-light);' : '') }}">
                                                                        <td style="font-weight:600;">{{ $batch->kode_produksi }}</td>
                                                                        <td>{{ $batch->coffeeType->name ?? 'Belum diisi' }}</td>
                                                                        <td>{{ $batch->tgl_stock->format('d/m/Y') }}</td>
                                                                        <td>
                                                                            <span style="{{ $batch->isExpiringSoon ? 'color:var(--danger);font-weight:600;' : '' }}">
                                                                                {{ $batch->tgl_exp->format('d/m/Y') }}
                                                                            </span>
                                                                            @if($batch->isExpiringSoon)
                                                                                <i data-lucide="alert-triangle" style="width:13px;height:13px;color:var(--danger);margin-left:4px;"></i>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-end tabular-nums">{{ $batch->jumlah_stock }}</td>
                                                                        <td class="text-end tabular-nums">{{ $batch->laku }}</td>
                                                                        <td class="text-end tabular-nums fw-bold">{{ $batch->sisa }}</td>
                                                                        <td class="text-end tabular-nums">Rp {{ number_format($batch->total, 0, ',', '.') }}</td>
                                                                        <td class="text-center">
                                                                            @php
                                                                                $badgeClass = match($batch->status) {
                                                                                    'normal' => 'badge-success',
                                                                                    'tarik' => 'badge-warning',
                                                                                    'ganti' => 'badge-info',
                                                                                    default => 'badge-neutral',
                                                                                };
                                                                            @endphp
                                                                            <span class="badge-modern {{ $badgeClass }}" style="text-transform:capitalize;">
                                                                                {{ $batch->status }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div style="text-align:center;padding:24px;color:var(--text-muted);font-size:0.85rem;">
                                                        <i data-lucide="inbox" style="width:32px;height:32px;margin-bottom:8px;opacity:0.5;"></i>
                                                        <p style="margin:0;">Belum ada stok untuk toko ini.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="empty-state text-center py-5">
                                                <i data-lucide="store" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                                <h5>Belum ada toko.</h5>
                                                <p class="text-muted">Tambahkan toko baru untuk mulai mengelola kemitraan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $stores->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Pane 2: Store Network Map --}}
        <div id="storeMapPane" style="display: none;">
            <div class="card-modern">
                <div class="card-header-modern d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="card-title-modern m-0">Peta Jaringan & Persebaran Seluruh Toko Mitra</h5>
                        <p class="text-muted small m-0 mt-1">Pemetaan geografis lokasi toko mitra kerja sama Kopi Hiku Himu.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-modern badge-success">
                            <i data-lucide="map-pin" style="width:12px;height:12px;display:inline-block;vertical-align:-1px;"></i>
                            <span id="mapStoreCount">{{ $allStoresForMap->filter->has_coordinates->count() }}</span> Toko berkoordinat
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-modern d-flex align-items-center gap-1" id="btnFitAllStores">
                            <i data-lucide="maximize-2" style="width:13px;height:13px;"></i>
                            <span>Fokuskan semua</span>
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

        // Stores map network data
        const storesData = @json($storesMapData ?? []);

        let networkMap = null;
        let mapMarkersGroup = null;

        function initNetworkMap() {
            if (networkMap) return;

            // Default center: Malang / East Java
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
                btnTable.className = 'btn btn-sm btn-outline-modern active d-flex align-items-center gap-2';
                btnMap.className = 'btn btn-sm btn-outline-modern d-flex align-items-center gap-2';
            } else {
                tablePane.style.display = 'none';
                mapPane.style.display = 'block';
                btnTable.className = 'btn btn-sm btn-outline-modern d-flex align-items-center gap-2';
                btnMap.className = 'btn btn-sm btn-outline-modern active d-flex align-items-center gap-2';
                
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

    function toggleStock(storeId) {
        const row = document.getElementById('stock-row-' + storeId);
        const chevron = document.getElementById('chevron-' + storeId);
        
        if (row.style.display === 'none') {
            document.querySelectorAll('.stock-detail-row').forEach(r => {
                r.style.display = 'none';
            });
            document.querySelectorAll('.stock-chevron').forEach(c => {
                c.style.transform = 'rotate(0deg)';
            });
            
            row.style.display = 'table-row';
            chevron.style.transform = 'rotate(90deg)';
            lucide.createIcons();
        } else {
            row.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    }
</script>
@endsection
