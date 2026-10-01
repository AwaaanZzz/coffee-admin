@extends('layouts.print-document')

@section('document-title', 'Laporan Inventaris Stok - Kopi Hiku Himu')
@section('page-orientation', 'landscape')
@section('sheet-max-width', '297mm')

@section('document-content')
@php
    $metaStore = $selectedStore?->name ?? 'Semua Toko Mitra';
    $metaPeriod = 'Posisi stok per ' . formatTglIndo(now());
    $sigRoleLeft = 'Petugas gudang & roastery,';
    $sigTitleLeft = 'Staf Pengelola Inventaris';
    $sigRoleRight = 'Mengetahui & menyetujui,';
    $sigNameRight = 'Kopi Hiku Himu';
    $sigTitleRight = 'Pemilik Usaha';
    $docFootnote = 'Laporan inventaris stok ini mencatat mutasi fisik kopi konsinyasi di rak toko mitra dan estimasi nilai aset beredar.';
    $totalAwal = $stockItems->sum('jumlah_stock');
    $totalLaku = $stockItems->sum('laku');
    $totalSisa = $stockItems->sum('sisa');
    $totalNilaiAset = $stockItems->sum('nilai_aset');
@endphp

<div class="doc-title-block">
    <h2 class="doc-title">Laporan Inventaris Stok</h2>
</div>

@include('reports.partials.identity')

<table class="doc-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 32px;">No</th>
            <th style="width: 100px;">Kode batch</th>
            <th>Toko mitra</th>
            <th>Varian kopi</th>
            <th style="width: 75px;">Kategori</th>
            <th class="text-right" style="width: 75px;">Awal (pcs)</th>
            <th class="text-right" style="width: 75px;">Laku (pcs)</th>
            <th class="text-right" style="width: 75px;">Sisa (pcs)</th>
            <th class="text-right" style="width: 95px;">Harga (Rp)</th>
            <th class="text-right" style="width: 110px;">Nilai aset (Rp)</th>
            <th class="text-center" style="width: 80px;">Kedaluwarsa</th>
            <th class="text-center" style="width: 75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($stockItems as $idx => $it)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="code-batch">{{ $it['kode_produksi'] }}</td>
            <td class="fw-semibold">{{ $it['store_name'] }}</td>
            <td>{{ $it['coffee_name'] }}</td>
            <td>
                @if(strtolower($it['category'] ?? '') === 'robusta')
                    <span class="badge-robusta" style="font-size: 7pt; padding: 2px 6px;">Robusta</span>
                @elseif(strtolower($it['category'] ?? '') === 'arabika')
                    <span class="badge-arabika" style="font-size: 7pt; padding: 2px 6px;">Arabika</span>
                @else
                    <span class="badge-neutral" style="font-size: 7pt; padding: 2px 6px;">{{ ucfirst(strtolower($it['category'] ?? '-')) }}</span>
                @endif
            </td>
            <td class="text-right">{{ $it['jumlah_stock'] > 0 ? number_format($it['jumlah_stock'], 0, ',', '.') : '-' }}</td>
            <td class="text-right text-muted">{{ $it['laku'] > 0 ? number_format($it['laku'], 0, ',', '.') : '-' }}</td>
            <td class="text-right fw-semibold">{{ $it['sisa'] > 0 ? number_format($it['sisa'], 0, ',', '.') : '-' }}</td>
            <td class="text-right text-muted">{{ $it['harga_jual'] > 0 ? number_format($it['harga_jual'], 0, ',', '.') : '-' }}</td>
            <td class="text-right fw-semibold">{{ $it['nilai_aset'] > 0 ? number_format($it['nilai_aset'], 0, ',', '.') : '-' }}</td>
            <td class="text-center text-muted" style="font-size: 8pt;">{{ formatTglIndo($it['tgl_exp'] ?? '') }}</td>
            <td class="text-center" style="font-size: 7.5pt;">
                @if($it['is_expired'])
                    <span class="num-neg fw-semibold">Kedaluwarsa</span>
                @elseif($it['is_expiring'])
                    <span class="fw-semibold" style="color: #D97706;">Segera</span>
                @else
                    <span class="num-pos">Aman</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="12" class="text-center" style="padding: 24px; color: #6B7280;">Tidak ada catatan data inventaris stok yang sesuai.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="row-total">
            <td colspan="5" class="text-left">Total keseluruhan</td>
            <td class="text-right">{{ number_format($totalAwal, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($totalLaku, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($totalSisa, 0, ',', '.') }}</td>
            <td class="text-right">-</td>
            <td class="text-right">{{ number_format($totalNilaiAset, 0, ',', '.') }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

<!-- Blok Ringkasan Eksekutif 2 Kolom -->
<div class="doc-summary-box">
    <table class="doc-summary-table">
        <tr>
            <td class="sum-label">Jumlah batch stok terdata</td>
            <td class="sum-val">{{ number_format($stockItems->count(), 0, ',', '.') }} batch</td>
        </tr>
        <tr>
            <td class="sum-label">Total sisa fisik di rak mitra</td>
            <td class="sum-val">{{ number_format($totalSisa, 0, ',', '.') }} pcs</td>
        </tr>
        <tr class="sum-total">
            <td class="sum-label">Estimasi nilai aset beredar</td>
            <td class="sum-val">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>
@endsection
