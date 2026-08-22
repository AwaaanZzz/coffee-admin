@extends('layouts.app')
@section('title', 'Catat Penjualan')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('sales.index') }}">Data Penjualan</a>
        <i data-lucide="chevron-right"></i>
        <span>Catat Penjualan</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Catat Penjualan</h3>
            <p class="page-subtitle">Masukkan data kopi yang telah terjual.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert-modern alert-danger mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i data-lucide="alert-circle"></i>
                <strong>Terjadi Kesalahan:</strong>
            </div>
            <ul class="m-0 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-modern">
        <div class="card-body-modern">
            <form action="{{ route('sales.store') }}" method="POST" class="form-modern">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Toko <span class="text-danger">*</span></label>
                        <select name="store_id" id="storeSelect" class="form-control-modern w-100" required>
                            <option value="">-- Pilih Toko --</option>
                            @foreach ($stores as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Stock / Kopi <span class="text-danger">*</span></label>
                        <select name="stock_batch_id" id="batchSelect" class="form-control-modern w-100" required disabled>
                            <option value="">-- Pilih Toko Dulu --</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Jumlah Terjual <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" id="jumlahInput" min="1" class="form-control-modern w-100" required>
                        <small class="text-muted mt-1 d-block" id="sisaInfo"></small>
                    </div>

                    <div class="col-md-6 mb-4 form-group-modern">
                        <label class="form-label-modern">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control-modern w-100" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-actions mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i data-lucide="save"></i> Simpan
                    </button>
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-modern">Batal</a>
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

    const storeSelect = document.getElementById('storeSelect');
    const batchSelect = document.getElementById('batchSelect');
    const sisaInfo = document.getElementById('sisaInfo');
    const jumlahInput = document.getElementById('jumlahInput');

    storeSelect.addEventListener('change', async function () {
        const storeId = this.value;
        batchSelect.innerHTML = '<option value="">Memuat...</option>';
        batchSelect.disabled = true;

        if (!storeId) {
            batchSelect.innerHTML = '<option value="">-- Pilih Toko Dulu --</option>';
            return;
        }

        const res = await fetch(`/sales/available-stock/${storeId}`);
        const data = await res.json();

        if (data.length === 0) {
            batchSelect.innerHTML = '<option value="">Tidak ada stock tersedia</option>';
            return;
        }

        batchSelect.innerHTML = '<option value="">-- Pilih Kopi --</option>' +
            data.map(b => `<option value="${b.id}" data-sisa="${b.sisa}">${b.label}</option>`).join('');
        batchSelect.disabled = false;
    });

    batchSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const sisa = selected.dataset.sisa;
        if (sisa) {
            sisaInfo.innerHTML = `<i data-lucide="info" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> Sisa stock: <strong>${sisa}</strong>`;
            lucide.createIcons();
            jumlahInput.max = sisa;
        } else {
            sisaInfo.innerHTML = '';
        }
    });
</script>
@endsection
