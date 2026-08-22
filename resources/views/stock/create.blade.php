@extends('layouts.app')
@section('title', 'Tambah Stock')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('stock.index') }}">Data Stock</a>
        <i data-lucide="chevron-right"></i>
        <span>Tambah Stock</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Tambah Stock Baru</h3>
            <p class="page-subtitle">Alokasikan stock kopi ke toko mitra.</p>
        </div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern">
            <form action="{{ route('stock.store') }}" method="POST" class="form-modern">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Toko <span class="text-danger">*</span></label>
                        <select name="store_id" class="form-control-modern w-100" required>
                            <option value="">-- Pilih Toko --</option>
                            @foreach ($stores as $s)
                                <option value="{{ $s->id }}" {{ old('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('store_id') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Jenis Kopi <span class="text-danger">*</span></label>
                        <select name="coffee_type_id" class="form-control-modern w-100" required>
                            <option value="">-- Pilih Kopi --</option>
                            @foreach ($coffeeTypes as $c)
                                <option value="{{ $c->id }}" {{ old('coffee_type_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ ucfirst($c->category) }})</option>
                            @endforeach
                        </select>
                        @error('coffee_type_id') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Kode Produksi <span class="text-danger">*</span></label>
                        <input type="text" name="kode_produksi" class="form-control-modern w-100" value="{{ old('kode_produksi') }}" required>
                        @error('kode_produksi') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Jumlah Stock <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_stock" min="1" class="form-control-modern w-100" value="{{ old('jumlah_stock') }}" required>
                        @error('jumlah_stock') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Tanggal Stock Masuk <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_stock" class="form-control-modern w-100" value="{{ old('tgl_stock') }}" required>
                        @error('tgl_stock') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Tanggal Kadaluarsa (Exp) <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_exp" class="form-control-modern w-100" value="{{ old('tgl_exp') }}" required>
                        @error('tgl_exp') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-actions mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i data-lucide="save"></i> Simpan
                    </button>
                    <a href="{{ route('stock.index') }}" class="btn btn-outline-modern">Batal</a>
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
