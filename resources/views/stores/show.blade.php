@extends('layouts.app')
@section('title', 'Detail Toko - ' . $store->name)

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('stores.index') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('stores.index') }}">Daftar Toko</a>
        <i data-lucide="chevron-right"></i>
        <span>Detail: {{ $store->name }}</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">{{ $store->name }}</h3>
            <p class="page-subtitle">Informasi lengkap toko mitra.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('stores.edit', $store) }}" class="btn btn-accent">
                <i data-lucide="edit"></i> Edit Toko
            </a>
            <a href="{{ route('stores.index') }}" class="btn btn-outline-modern">
                <i data-lucide="arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i data-lucide="calendar"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Kerja sama sejak</div>
                    <div class="stat-value fs-5">{{ $store->tgl_kerjasama->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i data-lucide="user"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Penanggung jawab</div>
                    <div class="stat-value fs-5">{{ $store->penanggung_jawab ?: 'Belum diisi' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i data-lucide="map-pin"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Alamat</div>
                    <div class="stat-value fs-6 text-truncate" title="{{ $store->alamat }}">{{ $store->alamat ?: 'Belum diisi' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Real Interactive Map Card --}}
    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="card-title-modern m-0">Lokasi Toko Mitra di Peta</h5>
                <p class="text-muted small m-0 mt-1">Peta geografis titik toko untuk rute kurir distribusi & navigasi pengiriman stok.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ $store->google_maps_url }}" target="_blank" class="btn btn-sm btn-outline-modern d-inline-flex align-items-center gap-1">
                    <i data-lucide="navigation" style="width:14px;height:14px;"></i>
                    <span>Buka Google Maps</span>
                    <i data-lucide="external-link" style="width:12px;height:12px;"></i>
                </a>
                @if($store->has_coordinates)
                    <a href="{{ $store->waze_url }}" target="_blank" class="btn btn-sm btn-outline-modern d-none d-sm-inline-flex align-items-center gap-1">
                        <i data-lucide="compass" style="width:14px;height:14px;"></i>
                        <span>Waze</span>
                    </a>
                @endif
                <a href="{{ route('stores.edit', $store) }}" class="btn btn-sm btn-outline-modern" title="Ubah koordinat / titik pin peta">
                    <i data-lucide="edit-3" style="width:14px;height:14px;"></i> Atur Titik
                </a>
            </div>
        </div>
        <div class="card-body-modern p-0">
            @if($store->has_coordinates)
                <div id="storeDetailMap" style="height: 380px; width: 100%; z-index: 1;"></div>
                <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-bottom-left-radius: var(--radius); border-bottom-right-radius: var(--radius);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-1 text-muted small">
                            <i data-lucide="map-pinned" style="width:15px;height:15px;color:var(--text-secondary);"></i>
                            <span>Koordinat GPS:</span>
                            <strong class="text-dark font-monospace" id="coordText">{{ $store->latitude }}, {{ $store->longitude }}</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-link p-0 text-muted" onclick="copyCoordinates('{{ $store->latitude }}, {{ $store->longitude }}')" title="Salin Koordinat">
                            <i data-lucide="copy" style="width:13px;height:13px;"></i>
                        </button>
                    </div>
                    <div class="text-muted small d-flex align-items-center gap-1">
                        <i data-lucide="info" style="width:13px;height:13px;color:var(--text-secondary);"></i>
                        <span>Peta jalan OpenStreetMap interaktif. Geser dan perbesar bebas.</span>
                    </div>
                </div>
            @else
                <div class="text-center py-5 px-3">
                    <div class="stat-icon mx-auto mb-3" style="width:48px;height:48px;">
                        <i data-lucide="map-pin-off" style="width:24px;height:24px;"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Titik Koordinat Peta Belum Diatur</h6>
                    <p class="text-muted small mb-3">Tentukan titik lokasi toko ini di peta agar rute pengiriman dan navigasi GPS pengantar tampil otomatis.</p>
                    <a href="{{ route('stores.edit', $store) }}" class="btn btn-sm btn-outline-modern">
                        <i data-lucide="map-pin"></i> Tentukan Titik Peta
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h5 class="m-0">Harga Kopi di Toko Ini</h5>
            <a href="{{ route('stores.prices.edit', $store) }}" class="btn btn-sm btn-outline-modern">
                <i data-lucide="tag"></i> Atur Harga
            </a>
        </div>
        <div class="card-body-modern p-0">
            <table class="table-modern w-100 m-0">
                <thead>
                    <tr>
                        <th>Nama kopi</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga jual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($store->coffeePrices as $price)
                        <tr>
                            <td class="fw-bold">{{ $price->coffeeType->name }}</td>
                            <td>
                                <span class="badge-modern badge-neutral">
                                    {{ ucfirst($price->coffeeType->category) }}
                                </span>
                            </td>
                            <td class="text-end tabular-nums">Rp {{ number_format($price->price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state text-center py-4">
                                    <p class="text-muted m-0">Belum ada harga kopi yang diatur untuk toko ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Grafik Produk Terlaris di Toko Ini --}}
    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h5 class="card-title-modern m-0">
                Produk terlaris di {{ $store->name }}
            </h5>
            <a href="{{ route('stock.create') }}?store_id={{ $store->id }}" class="btn btn-sm btn-outline-modern">
                <i data-lucide="plus"></i> Tambah stok toko
            </a>
        </div>
        <div class="card-body-modern">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div style="height: 280px; position: relative;">
                        <canvas id="storeDetailChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-5">
                    <h6 class="fw-bold mb-3 text-muted" style="font-size:0.85rem;">Peringkat penjualan produk</h6>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table-modern w-100" style="font-size:0.8125rem;">
                            <thead>
                                <tr>
                                    <th style="width: 36px;">#</th>
                                    <th>Nama kopi</th>
                                    <th class="text-end">Terjual</th>
                                    <th class="text-end">Omzet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $i => $p)
                                    <tr>
                                        <td>
                                            <span class="badge-modern badge-neutral">{{ $i + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $p['name'] }}</div>
                                            <span class="badge-modern badge-neutral" style="font-size:0.7rem;">{{ ucfirst($p['category']) }}</span>
                                        </td>
                                        <td class="text-end tabular-nums">{{ $p['qty'] }} pcs</td>
                                        <td class="text-end tabular-nums">Rp {{ number_format($p['revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data penjualan kopi di toko ini.</td>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();

        @if($store->has_coordinates)
            const mapEl = document.getElementById('storeDetailMap');
            if (mapEl) {
                const storeLat = {{ $store->latitude }};
                const storeLng = {{ $store->longitude }};
                const map = L.map('storeDetailMap', {
                    scrollWheelZoom: false
                }).setView([storeLat, storeLng], 16);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

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

                const popupHtml = `
                    <div class="store-map-popup">
                        <div class="popup-title">{{ $store->name }}</div>
                        <div class="popup-sub">{{ $store->alamat ?? 'Alamat belum diatur' }}</div>
                        <div class="popup-badge">PJ: {{ $store->penanggung_jawab ?? '-' }}</div>
                        <a href="{{ $store->google_maps_url }}" target="_blank" class="popup-btn">
                            Buka Petunjuk Arah ↗
                        </a>
                    </div>
                `;

                const marker = L.marker([storeLat, storeLng], { icon: coffeeIcon }).addTo(map);
                marker.bindPopup(popupHtml).openPopup();
                
                // Invalidate size once visible
                setTimeout(() => { map.invalidateSize(); }, 300);
            }
        @endif

        const ctx = document.getElementById('storeDetailChart');
        if (ctx) {
            const labels = [];
            const data = [];
            @foreach($topProducts as $p)
                labels.push({!! json_encode($p['name']) !!});
                data.push({{ $p['qty'] }});
            @endforeach

            const palette = ['#C88A4E', '#1E3A5F', '#4A7C59', '#2E86AB', '#A67261', '#8C6D58'];
            const barColors = data.map((_, i) => palette[i % palette.length]);

            const storeBarValuePlugin = {
                id: 'storeBarValuePlugin',
                afterDatasetsDraw(chart) {
                    const { ctx, chartArea: { top, bottom } } = chart;
                    const dark = document.documentElement.getAttribute('data-theme') === 'dark';

                    chart.data.datasets.forEach((dataset, datasetIdx) => {
                        const meta = chart.getDatasetMeta(datasetIdx);
                        meta.data.forEach((bar, index) => {
                            const val = dataset.data[index];
                            if (val === undefined || val === null) return;

                            ctx.save();
                            const barCenterX = bar.x;
                            const barTopY = bar.y;
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';

                            if (val === 0) {
                                ctx.font = '500 11px "Manrope", sans-serif';
                                ctx.fillStyle = dark ? '#64748b' : '#94a3b8';
                                ctx.fillText('0 pcs', barCenterX, Math.min(barTopY - 4, bottom - 4));
                            } else {
                                ctx.font = '600 12px "Manrope", sans-serif';
                                ctx.fillStyle = dark ? '#f8fafc' : '#0f172a';
                                ctx.fillText(`${val} pcs`, barCenterX, barTopY - 5);
                            }
                            ctx.restore();
                        });
                    });
                }
            };

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.length ? labels : ['Belum ada produk'],
                    datasets: [{
                        label: 'Terjual (Pcs)',
                        data: data.length ? data : [0],
                        backgroundColor: barColors,
                        borderRadius: { topLeft: 6, topRight: 6 },
                        borderSkipped: false,
                        maxBarThickness: 48
                    }]
                },
                options: {
                    indexAxis: 'x',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 25, bottom: 4 } },
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
                                label: function(c) {
                                    return ' Terjual: ' + c.raw + ' pcs';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Manrope', sans-serif", size: 12, weight: '600' },
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
                            grace: '25%',
                            grid: { color: 'rgba(0, 0, 0, 0.04)' },
                            ticks: {
                                font: { family: "'Manrope', sans-serif", size: 11 },
                                callback: function(v) { return v + ' pcs'; }
                            }
                        }
                    }
                },
                plugins: [storeBarValuePlugin]
            });
        }
    });

    function copyCoordinates(coords) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(coords).then(() => {
                alert('Koordinat GPS disalin: ' + coords);
            });
        }
    }
</script>
@endsection
