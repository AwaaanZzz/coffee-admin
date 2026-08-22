@extends('layouts.app')
@section('title', 'Update Stock')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('stock.index') }}">Data Stock</a>
        <i data-lucide="chevron-right"></i>
        <span>Update Stock</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Update Stock</h3>
            <p class="page-subtitle text-muted">
                {{ $batch->store->name }} &bull; {{ $batch->coffeeType->name }} &bull; Kode: {{ $batch->kode_produksi }}
            </p>
        </div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern">
            <form action="{{ route('stock.update', $batch) }}" method="POST" class="form-modern">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4 mb-4 form-group-modern">
                        <label class="form-label-modern">Jumlah Stock Awal</label>
                        <input type="text" class="form-control-modern w-100 bg-light" value="{{ $batch->jumlah_stock }}" disabled>
                    </div>

                    <div class="col-md-4 mb-4 form-group-modern">
                        <label class="form-label-modern">Laku (Terjual)</label>
                        <input type="number" name="laku" min="0" max="{{ $batch->jumlah_stock }}" class="form-control-modern w-100" value="{{ old('laku', $batch->laku) }}" required>
                        @error('laku') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-4 form-group-modern">
                        <label class="form-label-modern">Sisa (otomatis)</label>
                        <input type="text" class="form-control-modern w-100 bg-light" value="{{ $batch->sisa }}" disabled>
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control-modern w-100" required>
                            <option value="normal" {{ $batch->status === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="tarik" {{ $batch->status === 'tarik' ? 'selected' : '' }}>Tarik</option>
                            <option value="ganti" {{ $batch->status === 'ganti' ? 'selected' : '' }}>Ganti</option>
                        </select>
                        @error('status') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Keterangan (opsional)</label>
                        <input type="text" name="keterangan" class="form-control-modern w-100" placeholder="Cth: ditarik karena mendekati exp" value="{{ old('keterangan', $batch->keterangan ?? '') }}">
                    </div>
                </div>

                <div class="form-actions mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i data-lucide="save"></i> Update
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
