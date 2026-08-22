@extends('layouts.app')
@section('title', 'Edit Toko')

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
                        <textarea name="alamat" class="form-control-modern w-100" rows="3">{{ old('alamat', $store->alamat) }}</textarea>
                    </div>
                </div>

                <div class="form-actions mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i data-lucide="save"></i> Update
                    </button>
                    <a href="{{ route('stores.index') }}" class="btn btn-outline-modern">Batal</a>
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
