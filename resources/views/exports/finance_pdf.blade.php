@extends('layouts.print-document')

@section('document-title', 'Laporan Laba Rugi - Kopi Hiku Himu')
@section('page-orientation', 'landscape')
@section('sheet-max-width', '297mm')

@section('document-content')
@php
    $metaStore = $selectedStore?->name ?? 'Semua Toko Mitra';
    $metaPeriod = ($startDate && $endDate) 
        ? formatTglIndo($startDate) . ' s/d ' . formatTglIndo($endDate) 
        : 'Akumulasi seluruh waktu operasional';
    $sigRoleLeft = 'Penyusun laporan keuangan,';
    $sigTitleLeft = 'Staf Akuntansi & Keuangan';
    $sigRoleRight = 'Mengetahui & menyetujui,';
    $sigNameRight = 'Kopi Hiku Himu';
    $sigTitleRight = 'Pemilik Usaha';
    $docFootnote = 'Laporan Laba Rugi (Profit & Loss) ini dihitung berdasarkan transaksi penjualan konsinyasi dan beban pokok penjualan (HPP) modal varian kopi.';
@endphp

<div class="doc-title-block">
    <h2 class="doc-title">Laporan Laba Rugi</h2>
</div>

@include('reports.partials.identity')

<table class="doc-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 32px;">No</th>
            <th>Varian kopi</th>
            <th style="width: 80px;">Kategori</th>
            <th class="text-right" style="width: 80px;">Terjual (pcs)</th>
            <th class="text-right" style="width: 105px;">Harga rata-rata (Rp)</th>
            <th class="text-right" style="width: 110px;">Penjualan (Rp)</th>
            <th class="text-right" style="width: 100px;">HPP satuan (Rp)</th>
            <th class="text-right" style="width: 110px;">Beban HPP (Rp)</th>
            <th class="text-right" style="width: 110px;">Laba kotor (Rp)</th>
            <th class="text-right" style="width: 75px;">Margin (%)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reportItems as $idx => $it)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="fw-semibold">{{ $it['name'] }}</td>
            <td>
                @if(strtolower($it['category'] ?? '') === 'robusta')
                    <span class="badge-robusta" style="font-size: 7pt; padding: 2px 6px;">Robusta</span>
                @elseif(strtolower($it['category'] ?? '') === 'arabika')
                    <span class="badge-arabika" style="font-size: 7pt; padding: 2px 6px;">Arabika</span>
                @else
                    <span class="badge-neutral" style="font-size: 7pt; padding: 2px 6px;">{{ ucfirst(strtolower($it['category'] ?? '-')) }}</span>
                @endif
            </td>
            <td class="text-right">{{ $it['total_qty'] > 0 ? number_format($it['total_qty'], 0, ',', '.') : '-' }}</td>
            <td class="text-right">{{ $it['avg_price'] > 0 ? number_format($it['avg_price'], 0, ',', '.') : '-' }}</td>
            <td class="text-right fw-semibold">{{ $it['revenue'] > 0 ? number_format($it['revenue'], 0, ',', '.') : '-' }}</td>
            <td class="text-right text-muted">{{ $it['modal'] > 0 ? number_format($it['modal'], 0, ',', '.') : '-' }}</td>
            <td class="text-right">{{ $it['total_hpp'] > 0 ? number_format($it['total_hpp'], 0, ',', '.') : '-' }}</td>
            <td class="text-right fw-semibold {{ $it['laba_kotor'] < 0 ? 'num-neg' : ($it['laba_kotor'] > 0 ? 'num-pos' : '') }}">
                @if($it['laba_kotor'] < 0)
                    ({{ number_format(abs($it['laba_kotor']), 0, ',', '.') }})
                @elseif($it['laba_kotor'] > 0)
                    {{ number_format($it['laba_kotor'], 0, ',', '.') }}
                @else
                    -
                @endif
            </td>
            <td class="text-right {{ $it['margin_pct'] < 0 ? 'num-neg' : ($it['margin_pct'] > 0 ? 'num-pos' : '') }}">
                {{ $it['revenue'] > 0 ? number_format($it['margin_pct'], 1, ',', '.') . '%' : '-' }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="text-center" style="padding: 24px; color: #6B7280;">Tidak ada data transaksi penjualan pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="row-total">
            <td colspan="3" class="text-left">Total keseluruhan</td>
            <td class="text-right">{{ number_format($reportItems->sum('total_qty'), 0, ',', '.') }}</td>
            <td class="text-right">-</td>
            <td class="text-right">{{ number_format($totalRevenue, 0, ',', '.') }}</td>
            <td class="text-right">-</td>
            <td class="text-right">{{ number_format($totalHpp, 0, ',', '.') }}</td>
            <td class="text-right {{ $totalLaba < 0 ? 'num-neg' : ($totalLaba > 0 ? 'num-pos' : '') }}">
                @if($totalLaba < 0)
                    ({{ number_format(abs($totalLaba), 0, ',', '.') }})
                @elseif($totalLaba > 0)
                    {{ number_format($totalLaba, 0, ',', '.') }}
                @else
                    -
                @endif
            </td>
            <td class="text-right {{ $overallMargin < 0 ? 'num-neg' : ($overallMargin > 0 ? 'num-pos' : '') }}">
                {{ number_format($overallMargin, 1, ',', '.') }}%
            </td>
        </tr>
    </tfoot>
</table>

<!-- Blok Ringkasan Eksekutif 2 Kolom (Bukan Kotak KPI Warna-warni) -->
<div class="doc-summary-box">
    <table class="doc-summary-table">
        <tr>
            <td class="sum-label">Total penjualan kotor (omset)</td>
            <td class="sum-val">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="sum-label">Total beban pokok penjualan (HPP)</td>
            <td class="sum-val">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
        </tr>
        <tr class="sum-total">
            <td class="sum-label">Laba kotor operasional</td>
            <td class="sum-val {{ $totalLaba < 0 ? 'num-neg' : ($totalLaba > 0 ? 'num-pos' : '') }}">
                @if($totalLaba < 0)
                    -Rp {{ number_format(abs($totalLaba), 0, ',', '.') }}
                @else
                    Rp {{ number_format($totalLaba, 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="sum-label">Margin laba kotor rata-rata</td>
            <td class="sum-val {{ $overallMargin < 0 ? 'num-neg' : ($overallMargin > 0 ? 'num-pos' : '') }}">
                {{ number_format($overallMargin, 1, ',', '.') }}%
            </td>
        </tr>
    </table>
</div>
@endsection
