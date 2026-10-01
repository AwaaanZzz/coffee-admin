@extends('layouts.print-document')

@section('document-title', 'Laporan Rekapitulasi Penjualan - Kopi Hiku Himu')
@section('page-orientation', 'portrait')

@section('document-content')
@php
    $metaStore = request('store_id') && isset($stores) ? ($stores->firstWhere('id', request('store_id'))->name ?? 'Toko Mitra') : 'Semua Toko Mitra';
    $metaPeriod = (request('date_from') && request('date_to'))
        ? formatTglIndo(request('date_from')) . ' s/d ' . formatTglIndo(request('date_to'))
        : 'Semua periode transaksi';
    $sigRoleLeft = 'Penyusun rekapitulasi,';
    $sigTitleLeft = 'Staf Administrasi';
    $sigRoleRight = 'Mengetahui & menyetujui,';
    $sigNameRight = 'Kopi Hiku Himu';
    $sigTitleRight = 'Pemilik Usaha';
    $docFootnote = 'Laporan rekapitulasi transaksi penjualan ini dicetak secara resmi dari sistem pencatatan Kopi Hiku Himu.';
    $totalQty = $sales->sum('jumlah');
    $totalNominal = $sales->sum('total');
@endphp

<div class="doc-title-block">
    <h2 class="doc-title">Laporan Rekapitulasi Penjualan</h2>
</div>

@include('reports.partials.identity')

<table class="doc-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 32px;">No</th>
            <th style="width: 80px;">Tanggal</th>
            <th>Toko mitra</th>
            <th>Varian kopi</th>
            <th class="text-right" style="width: 85px;">Jumlah (pcs)</th>
            <th class="text-right" style="width: 110px;">Harga satuan (Rp)</th>
            <th class="text-right" style="width: 120px;">Total penjualan (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $idx => $s)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td>{{ formatTglIndo($s->tanggal) }}</td>
            <td class="fw-semibold">{{ $s->store->name ?? '-' }}</td>
            <td>{{ $s->coffeeType->name ?? '-' }}</td>
            <td class="text-right">{{ $s->jumlah > 0 ? number_format($s->jumlah, 0, ',', '.') : '-' }}</td>
            <td class="text-right text-muted">{{ $s->harga > 0 ? number_format($s->harga, 0, ',', '.') : '-' }}</td>
            <td class="text-right fw-semibold">{{ $s->total > 0 ? number_format($s->total, 0, ',', '.') : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center" style="padding: 24px; color: #6B7280;">Tidak ada catatan data transaksi penjualan pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="row-total">
            <td colspan="4" class="text-left">Total keseluruhan</td>
            <td class="text-right">{{ number_format($totalQty, 0, ',', '.') }}</td>
            <td class="text-right">-</td>
            <td class="text-right">{{ number_format($totalNominal, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

<!-- Blok Ringkasan Eksekutif 2 Kolom -->
<div class="doc-summary-box">
    <table class="doc-summary-table">
        <tr>
            <td class="sum-label">Jumlah transaksi</td>
            <td class="sum-val">{{ number_format($sales->count(), 0, ',', '.') }} transaksi</td>
        </tr>
        <tr>
            <td class="sum-label">Total volume produk terjual</td>
            <td class="sum-val">{{ number_format($totalQty, 0, ',', '.') }} pcs</td>
        </tr>
        <tr class="sum-total">
            <td class="sum-label">Total nilai penjualan (omset)</td>
            <td class="sum-val">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>
@endsection
