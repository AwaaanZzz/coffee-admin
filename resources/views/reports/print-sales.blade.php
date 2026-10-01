@extends('layouts.print-document')

@section('document-title', 'Laporan Penjualan - Kopi Hiku Himu')
@section('page-orientation', 'portrait')

@section('document-content')
@php
    $fromD = request('from_date');
    $toD = request('to_date');
    $metaStore = request('store') ? 'Cabang ' . request('store') : 'Semua Toko Mitra';
    $metaPeriod = ($fromD && $toD) 
        ? formatTglIndo($fromD) . ' s/d ' . formatTglIndo($toD) 
        : 'Semua data operasional';
    $sigRoleLeft = 'Petugas penjualan,';
    $sigTitleLeft = 'Staf Administrasi';
    $sigRoleRight = 'Mengetahui & menyetujui,';
    $sigNameRight = 'Kopi Hiku Himu';
    $sigTitleRight = 'Pemilik Usaha';
    $docFootnote = 'Laporan penjualan kopi ini dicatat sebagai acuan rekonsiliasi stok konsinyasi dan pembukuan kas usaha.';
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
            <th class="text-right" style="width: 85px;">Jumlah (pcs)</th>
            <th class="text-right" style="width: 110px;">Harga satuan (Rp)</th>
            <th class="text-right" style="width: 120px;">Total (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salesData ?? [] as $idx => $sale)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td>{{ formatTglIndo($sale->date ?? $sale->tanggal ?? '') }}</td>
            <td class="fw-semibold">{{ $sale->store ?? '-' }}</td>
            <td>{{ $sale->coffee ?? '-' }}</td>
            <td class="text-right">{{ ($sale->qty ?? 0) > 0 ? number_format($sale->qty, 0, ',', '.') : '-' }}</td>
            <td class="text-right text-muted">{{ ($sale->price ?? 0) > 0 ? number_format($sale->price, 0, ',', '.') : '-' }}</td>
            <td class="text-right fw-semibold">{{ ($sale->total ?? 0) > 0 ? number_format($sale->total, 0, ',', '.') : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center" style="padding: 24px; color: #6B7280;">Tidak ada catatan data transaksi penjualan pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
    @if(isset($salesData) && count($salesData) > 0)
    <tfoot>
        <tr class="row-total">
            <td colspan="4" class="text-left">Total keseluruhan</td>
            <td class="text-right">{{ number_format($totalUnit ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">-</td>
            <td class="text-right">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
    @endif
</table>

@if(isset($salesData) && count($salesData) > 0)
<div class="doc-summary-box">
    <table class="doc-summary-table">
        <tr>
            <td class="sum-label">Total volume produk terjual</td>
            <td class="sum-val">{{ number_format($totalUnit ?? 0, 0, ',', '.') }} pcs</td>
        </tr>
        <tr class="sum-total">
            <td class="sum-label">Total nilai penjualan (omset)</td>
            <td class="sum-val">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>
@endif
@endsection
