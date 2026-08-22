@extends('layouts.app')
@section('title', 'Atur Harga Kopi')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('stores.index') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('stores.index') }}">Daftar Toko</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('stores.show', $store) }}">{{ $store->name }}</a>
        <i data-lucide="chevron-right"></i>
        <span>Atur Harga</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Harga Kopi - {{ $store->name }}</h3>
            <p class="page-subtitle">Tentukan harga jual untuk masing-masing jenis kopi.</p>
        </div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern">
            <form action="{{ route('stores.prices.update', $store) }}" method="POST" class="form-modern">
                @csrf
                @method('PUT')

                <div class="table-responsive mb-4">
                    <table class="table-modern w-100">
                        <thead>
                            <tr>
                                <th>Nama Kopi</th>
                                <th>Kategori</th>
                                <th style="width: 300px;">Harga (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coffeeTypes as $coffee)
                                <tr>
                                    <td class="fw-bold">{{ $coffee->name }}</td>
                                    <td>
                                        <span class="badge-modern {{ $coffee->category === 'robusta' ? 'bg-dark text-white' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($coffee->category) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-group-modern m-0">
                                            <input type="number" step="0.01" min="0"
                                                name="prices[{{ $coffee->id }}]"
                                                class="form-control-modern w-100"
                                                value="{{ $existingPrices[$coffee->id]->price ?? '' }}"
                                                placeholder="0">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-actions border-top pt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i data-lucide="save"></i> Simpan Harga
                    </button>
                    <a href="{{ route('stores.show', $store) }}" class="btn btn-outline-modern">Batal</a>
                </div>
            </form>
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
