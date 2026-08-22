@extends('layouts.app')
@section('title', 'Tambah Toko Baru')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('stores.index') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('stores.index') }}">Daftar Toko</a>
        <i data-lucide="chevron-right"></i>
        <span>Tambah Toko Baru</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Tambah Toko Baru</h3>
            <p class="page-subtitle">Masukkan data toko mitra yang baru.</p>
        </div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern">
            <form action="{{ route('stores.store') }}" method="POST" class="form-modern">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Nama Toko <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control-modern w-100" value="{{ old('name') }}" required placeholder="Masukkan nama toko">
                        @error('name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Tanggal Kerjasama <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_kerjasama" class="form-control-modern w-100" value="{{ old('tgl_kerjasama') }}" required>
                        @error('tgl_kerjasama') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" class="form-control-modern w-100" value="{{ old('penanggung_jawab') }}" placeholder="Nama penanggung jawab">
                    </div>

                    <div class="col-md-12 mb-4 form-group-modern">
                        <label class="form-label-modern">Alamat</label>
                        <textarea name="alamat" class="form-control-modern w-100" rows="3" placeholder="Alamat lengkap toko">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <div class="form-actions mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i data-lucide="save"></i> Simpan
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
