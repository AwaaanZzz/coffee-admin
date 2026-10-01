@extends('layouts.app')
@section('title', 'Edit Toko - ' . $store->name)

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('stores.index') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('stores.index') }}">Daftar Toko</a>
        <i data-lucide="chevron-right"></i>
        <span>Edit Toko</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Edit Toko: {{ $store->name }}</h3>
            <p class="page-subtitle">Perbarui informasi toko mitra.</p>
        </div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern">
            <form action="{{ route('stores.update', $store) }}" method="POST" class="form-modern">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Nama Toko <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control-modern w-100" value="{{ old('name', $store->name) }}" required>
                        @error('name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Tanggal Kerjasama <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_kerjasama" class="form-control-modern w-100" value="{{ old('tgl_kerjasama', $store->tgl_kerjasama->format('Y-m-d')) }}" required>
                        @error('tgl_kerjasama') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" class="form-control-modern w-100" value="{{ old('penanggung_jawab', $store->penanggung_jawab) }}">
                    </div>

                    <div class="col-md-12 mb-4 form-group-modern">
                        <label class="form-label-modern">Alamat</label>
                        <textarea name="alamat" id="storeAlamatInput" class="form-control-modern w-100" rows="2" placeholder="Alamat lengkap toko...">{{ old('alamat', $store->alamat) }}</textarea>
                    </div>

                    {{-- Interactive Map Location Picker --}}
                    <div class="col-md-12 mb-4">
                        <div class="card border rounded-3 p-3" style="background: var(--bg-card);">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <div>
                                    <label class="form-label-modern fw-bold mb-1 d-flex align-items-center gap-2">
                                        <i data-lucide="map-pin" class="text-accent" style="width:18px;height:18px;"></i>
                                        <span>Titik Lokasi Peta (Latitude & Longitude)</span>
                                    </label>
                                    <p class="text-muted small m-0">Klik pada peta atau geser pin untuk menentukan titik presisi toko mitra.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-accent" id="btnGeolocate" title="Gunakan koordinat GPS perangkat Anda">
                                        <i data-lucide="crosshair" style="width:14px;height:14px;"></i> Deteksi GPS Saya
                                    </button>
                                </div>
                            </div>

                            {{-- Search Location Geocoder Input --}}
                            <div class="input-group input-group-sm mb-3">
                                <span class="input-group-text bg-light border-end-0">
                                    <i data-lucide="search" style="width:14px;height:14px;color:var(--accent);"></i>
                                </span>
                                <input type="text" id="mapSearchInput" class="form-control border-start-0" placeholder="Ketik nama jalan, kelurahan, atau kota untuk mencari di peta...">
                                <button type="button" class="btn btn-outline-secondary px-3" id="btnSearchMap">Cari di Peta</button>
                            </div>

                            {{-- Leaflet Map Container --}}
                            <div id="locationPickerMap" style="height: 320px; width: 100%; border-radius: 8px; border: 1px solid var(--border); z-index: 1;"></div>

                            {{-- Coordinate Inputs --}}
                            <div class="row g-2 mt-2">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light font-monospace small" style="width: 85px;">Latitude</span>
                                        <input type="text" name="latitude" id="latInput" class="form-control font-monospace" value="{{ old('latitude', $store->latitude) }}" placeholder="-7.9797000" step="any">
                                    </div>
                                    @error('latitude') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light font-monospace small" style="width: 85px;">Longitude</span>
                                        <input type="text" name="longitude" id="lngInput" class="form-control font-monospace" value="{{ old('longitude', $store->longitude) }}" placeholder="112.6304000" step="any">
                                    </div>
                                    @error('longitude') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i data-lucide="save"></i> Update Toko
                    </button>
                    <a href="{{ route('stores.show', $store) }}" class="btn btn-outline-modern">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();

        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');
        const searchInput = document.getElementById('mapSearchInput');
        const btnSearch = document.getElementById('btnSearchMap');
        const btnGeolocate = document.getElementById('btnGeolocate');

        // Default initial coordinates: store coords or East Java / Malang center
        const defaultLat = {{ $store->latitude ? $store->latitude : -7.9797 }};
        const defaultLng = {{ $store->longitude ? $store->longitude : 112.6304 }};
        const initialZoom = {{ $store->has_coordinates ? 16 : 13 }};

        const map = L.map('locationPickerMap', {
            scrollWheelZoom: true
        }).setView([defaultLat, defaultLng], initialZoom);

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

        // Draggable Marker
        let marker = L.marker([defaultLat, defaultLng], {
            draggable: true,
            icon: coffeeIcon
        }).addTo(map);

        marker.bindPopup('<strong>Lokasi Toko</strong><br>Geser pin atau klik peta untuk ubah titik.').openPopup();

        function updateInputs(lat, lng) {
            latInput.value = parseFloat(lat).toFixed(7);
            lngInput.value = parseFloat(lng).toFixed(7);
        }

        // If store already had coords, populate inputs
        if (!latInput.value) {
            updateInputs(defaultLat, defaultLng);
        }

        // Marker dragend event
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });

        // Map click event
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
            marker.openPopup();
        });

        // Manual coordinate input change
        function syncFromInputs() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.panTo([lat, lng]);
            }
        }
        latInput.addEventListener('change', syncFromInputs);
        lngInput.addEventListener('change', syncFromInputs);

        // Geolocate button
        if (btnGeolocate) {
            btnGeolocate.addEventListener('click', function() {
                if (navigator.geolocation) {
                    btnGeolocate.disabled = true;
                    btnGeolocate.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mendeteksi...';
                    navigator.geolocation.getCurrentPosition(
                        function(pos) {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;
                            marker.setLatLng([lat, lng]);
                            map.setView([lat, lng], 17);
                            updateInputs(lat, lng);
                            marker.bindPopup('<strong>Lokasi GPS Anda Ditemukan!</strong>').openPopup();
                            btnGeolocate.disabled = false;
                            btnGeolocate.innerHTML = '<i data-lucide="crosshair" style="width:14px;height:14px;"></i> Deteksi GPS Saya';
                            lucide.createIcons();
                        },
                        function(err) {
                            alert('Gagal mendeteksi lokasi GPS: ' + err.message);
                            btnGeolocate.disabled = false;
                            btnGeolocate.innerHTML = '<i data-lucide="crosshair" style="width:14px;height:14px;"></i> Deteksi GPS Saya';
                            lucide.createIcons();
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                } else {
                    alert('Browser Anda tidak mendukung geolokasi GPS.');
                }
            });
        }

        // Address Search Geocoder
        function performSearch() {
            const query = searchInput.value.trim() || document.getElementById('storeAlamatInput').value.trim();
            if (!query) {
                alert('Ketik kata kunci lokasi atau alamat yang ingin dicari.');
                return;
            }

            btnSearch.disabled = true;
            btnSearch.innerText = 'Mencari...';

            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query + ', Indonesia'))
                .then(res => res.json())
                .then(data => {
                    btnSearch.disabled = false;
                    btnSearch.innerText = 'Cari di Peta';
                    if (data && data.length > 0) {
                        const top = data[0];
                        const lat = parseFloat(top.lat);
                        const lng = parseFloat(top.lon);
                        marker.setLatLng([lat, lng]);
                        map.setView([lat, lng], 16);
                        updateInputs(lat, lng);
                        marker.bindPopup('<strong>Ditemukan:</strong><br>' + top.display_name.substring(0, 70) + '...').openPopup();
                    } else {
                        alert('Lokasi tidak ditemukan di peta. Silakan klik langsung pada area peta.');
                    }
                })
                .catch(err => {
                    btnSearch.disabled = false;
                    btnSearch.innerText = 'Cari di Peta';
                    console.error(err);
                    alert('Gagal menghubungi layanan pencarian peta.');
                });
        }

        if (btnSearch) btnSearch.addEventListener('click', performSearch);
        if (searchInput) {
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });
        }

        setTimeout(() => { map.invalidateSize(); }, 300);
    });
</script>
@endsection
