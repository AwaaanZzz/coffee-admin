@extends('layouts.app')
@section('title', 'Tambah Jenis Kopi')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('coffee-types.index') }}">Jenis Kopi</a>
        <i data-lucide="chevron-right"></i>
        <span>Tambah</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Tambah Jenis Kopi</h3>
            <p class="page-subtitle">Masukkan data jenis kopi yang baru.</p>
        </div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern">
            <form action="{{ route('coffee-types.store') }}" method="POST" class="form-modern">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Nama Kopi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control-modern w-100" value="{{ old('name') }}" required placeholder="Contoh: Kopi Bubuk">
                        @error('name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Kategori <span class="text-danger">*</span></label>
                        <select name="category" class="form-control-modern w-100" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="robusta" {{ old('category') === 'robusta' ? 'selected' : '' }}>Robusta</option>
                            <option value="arabika" {{ old('category') === 'arabika' ? 'selected' : '' }}>Arabika</option>
                        </select>
                        @error('category') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-actions mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i data-lucide="save"></i> Simpan
                    </button>
                    <a href="{{ route('coffee-types.index') }}" class="btn btn-outline-modern">Batal</a>
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
