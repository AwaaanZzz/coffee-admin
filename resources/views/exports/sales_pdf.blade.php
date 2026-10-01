@extends('layouts.print-document')

@section('document-title', 'Laporan Penjualan - Kopi Hiku Himu')
@section('page-orientation', 'portrait')

@section('document-content')
@php
    $metaStore = $selectedStore?->name ?? 'Semua Toko Mitra';
    $metaPeriod = ($startDate && $endDate) 
        ? formatTglIndo($startDate) . ' s/d ' . formatTglIndo($endDate) 
        : 'Semua data transaksi penjualan';
    $sigRoleLeft = 'Petugas penjualan & kasir,';
    $sigTitleLeft = 'Staf Administrasi Distribusi';
    $sigRoleRight = 'Mengetahui & menyetujui,';
    $sigNameRight = 'Kopi Hiku Himu';
    $sigTitleRight = 'Pemilik Usaha';
    $docFootnote = 'Laporan transaksi penjualan ini diterbitkan sebagai bukti mutasi distribusi kopi ke toko mitra dan acuan rekonsiliasi kas.';
    $totalQty = $sales->sum('jumlah');
    $totalNominal = $sales->sum('total');
@endphp

<div class="doc-title-block">
    <h2 class="doc-title">Laporan Penjualan</h2>
</div>

@include('reports.partials.identity')

<table class="doc-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 32px;">No</th>
            <th style="width: 80px;">Tanggal</th>
            <th>Toko mitra</th>
            <th>Varian kopi</th>
            <th style="width: 95px;">Kode batch</th>
            <th class="text-right" style="width: 75px;">Jumlah (pcs)</th>
            <th class="text-right" style="width: 105px;">Harga satuan (Rp)</th>
            <th class="text-right" style="width: 115px;">Total (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $idx => $s)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td>{{ formatTglIndo($s->tanggal) }}</td>
            <td class="fw-semibold">{{ $s->store->name ?? '-' }}</td>
            <td>{{ $s->coffeeType->name ?? '-' }}</td>
            <td class="code-batch">{{ $s->stockBatch->kode_produksi ?? '-' }}</td>
            <td class="text-right">{{ $s->jumlah > 0 ? number_format($s->jumlah, 0, ',', '.') : '-' }}</td>
            <td class="text-right text-muted">{{ $s->harga > 0 ? number_format($s->harga, 0, ',', '.') : '-' }}</td>
            <td class="text-right fw-semibold">{{ $s->total > 0 ? number_format($s->total, 0, ',', '.') : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center" style="padding: 24px; color: #6B7280;">Tidak ada catatan data transaksi penjualan pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="row-total">
            <td colspan="5" class="text-left">Total keseluruhan</td>
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
            <td class="sum-label">Jumlah transaksi penjualan</td>
            <td class="sum-val">{{ number_format($sales->count(), 0, ',', '.') }} transaksi</td>
        </tr>
        <tr>
            <td class="sum-label">Total volume produk terdistribusi</td>
            <td class="sum-val">{{ number_format($totalQty, 0, ',', '.') }} pcs</td>
        </tr>
        <tr class="sum-total">
            <td class="sum-label">Total nilai penjualan (omset)</td>
            <td class="sum-val">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>
@endsection
