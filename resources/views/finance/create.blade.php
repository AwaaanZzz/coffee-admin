@extends('layouts.app')
@section('title', 'Buat Laporan Keuangan')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('finance.index') }}">Laporan Keuangan</a>
        <i data-lucide="chevron-right"></i>
        <span>Buat Laporan</span>
    </div>
@endsection

@section('content')
    <div class="page-header mb-4">
        <div>
            <h3 class="page-title mb-1">Buat laporan keuangan</h3>
            <p class="page-subtitle text-muted mb-0">Hitung dan simpan laporan pemasukan dan pengeluaran toko mitra.</p>
        </div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern">
            <form action="{{ route('finance.store') }}" method="POST" class="form-modern">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3 form-group-modern">
                        <label class="form-label-modern">Toko mitra <span class="text-danger">*</span></label>
                        <select name="store_id" id="storeSelect" class="form-control-modern form-select w-100" required>
                            <option value="">-- Pilih toko mitra --</option>
                            @foreach ($stores as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3 form-group-modern">
                        <label class="form-label-modern">Periode awal <span class="text-danger">*</span></label>
                        <input type="date" name="periode_awal" id="periodeAwal" class="form-control-modern w-100" required>
                    </div>

                    <div class="col-md-4 mb-3 form-group-modern">
                        <label class="form-label-modern">Periode akhir <span class="text-danger">*</span></label>
                        <input type="date" name="periode_akhir" id="periodeAkhir" class="form-control-modern w-100" required>
                    </div>

                    <div class="col-12 mb-3">
                        <button type="button" id="btnHitung" class="btn btn-outline-modern d-inline-flex align-items-center gap-2">
                            <i data-lucide="calculator" style="width: 15px; height: 15px;"></i>
                            <span>Hitung pemasukan otomatis</span>
                        </button>
                    </div>

                    <div class="col-md-6 mb-3 form-group-modern">
                        <label class="form-label-modern">Pemasukan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="pemasukan" id="pemasukanInput" class="form-control-modern w-100" required placeholder="0">
                        <small class="text-muted mt-1 d-block"><i data-lucide="info" style="width:13px;height:13px;vertical-align:-1px;"></i> Dapat diisi otomatis dari total transaksi penjualan, atau disunting manual.</small>
                    </div>

                    <div class="col-md-6 mb-3 form-group-modern">
                        <label class="form-label-modern">Pengeluaran (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="pengeluaran" class="form-control-modern w-100" value="0" required placeholder="0">
                    </div>

                    <div class="col-12 mb-3 form-group-modern">
                        <label class="form-label-modern">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control-modern w-100" rows="3" placeholder="Masukkan keterangan tambahan jika ada"></textarea>
                    </div>
                </div>

                <div class="form-actions mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-accent d-flex align-items-center gap-2">
                        <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                        <span>Simpan laporan</span>
                    </button>
                    <a href="{{ route('finance.index') }}" class="btn btn-outline-modern">Batal</a>
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

    document.getElementById('btnHitung').addEventListener('click', async function () {
        const storeId = document.getElementById('storeSelect').value;
        const awal = document.getElementById('periodeAwal').value;
        const akhir = document.getElementById('periodeAkhir').value;

        if (!storeId || !awal || !akhir) {
            alert('Isi toko, periode awal, dan periode akhir dulu.');
            return;
        }

        try {
            const res = await fetch(`{{ route('finance.hitung') }}?store_id=${storeId}&periode_awal=${awal}&periode_akhir=${akhir}`);
            const data = await res.json();
            document.getElementById('pemasukanInput').value = data.pemasukan;
        } catch (error) {
            console.error('Error fetching data:', error);
            alert('Terjadi kesalahan saat menghitung pemasukan.');
        }
    });
</script>
@endsection
