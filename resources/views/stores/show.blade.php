@extends('layouts.app')
@section('title', 'Detail Toko')

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
                <div class="stat-icon bg-info">
                    <i data-lucide="calendar"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Kerjasama Sejak</div>
                    <div class="stat-value fs-5">{{ $store->tgl_kerjasama->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i data-lucide="user"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Penanggung Jawab</div>
                    <div class="stat-value fs-5">{{ $store->penanggung_jawab ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i data-lucide="map-pin"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Alamat</div>
                    <div class="stat-value fs-6 text-truncate" title="{{ $store->alamat }}">{{ $store->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h5 class="m-0">Harga Kopi di Toko Ini</h5>
            <a href="{{ route('stores.prices.edit', $store) }}" class="btn btn-sm btn-accent">
                <i data-lucide="tag"></i> Atur Harga
            </a>
        </div>
        <div class="card-body-modern p-0">
            <table class="table-modern w-100 m-0">
                <thead>
                    <tr>
                        <th>Nama Kopi</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($store->coffeePrices as $price)
                        <tr>
                            <td class="fw-bold">{{ $price->coffeeType->name }}</td>
                            <td>
                                <span class="badge-modern {{ $price->coffeeType->category === 'robusta' ? 'bg-dark text-white' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($price->coffeeType->category) }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($price->price, 0, ',', '.') }}</td>
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
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();
    });
</script>
@endsection
