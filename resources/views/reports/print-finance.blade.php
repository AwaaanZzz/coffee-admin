@extends('layouts.print-document')

@section('document-title', 'Laporan Keuangan Toko - Kopi Hiku Himu')
@section('page-orientation', 'portrait')

@section('document-content')
@php
    $metaStore = request('store') ? 'Cabang ' . request('store') : 'Semua Toko Mitra';
    $metaPeriod = 'Tahun ' . request('year', date('Y'));
    $sigRoleLeft = 'Penyusun pembukuan,';
    $sigTitleLeft = 'Staf Keuangan';
    $sigRoleRight = 'Mengetahui & menyetujui,';
    $sigNameRight = 'Kopi Hiku Himu';
    $sigTitleRight = 'Pemilik Usaha';
    $docFootnote = 'Laporan pembukuan keuangan toko ini mencatat realisasi arus pemasukan, pengeluaran, dan laba operasional mitra Kopi Hiku Himu.';
@endphp

<div class="doc-title-block">
    <h2 class="doc-title">Laporan Keuangan Toko</h2>
</div>

@include('reports.partials.identity')

<table class="doc-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 36px;">No</th>
            <th>Toko mitra</th>
            <th>Periode</th>
            <th class="text-right" style="width: 140px;">Pemasukan (Rp)</th>
            <th class="text-right" style="width: 140px;">Pengeluaran (Rp)</th>
            <th class="text-right" style="width: 140px;">Laba operasional (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($financeData ?? [] as $idx => $data)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="fw-semibold">{{ $data->toko }}</td>
            <td>{{ $data->periode }}</td>
            <td class="text-right">{{ $data->pemasukan > 0 ? number_format($data->pemasukan, 0, ',', '.') : '-' }}</td>
            <td class="text-right text-muted">{{ $data->pengeluaran > 0 ? number_format($data->pengeluaran, 0, ',', '.') : '-' }}</td>
            <td class="text-right fw-semibold {{ $data->laba < 0 ? 'num-neg' : ($data->laba > 0 ? 'num-pos' : '') }}">
                @if($data->laba < 0)
                    ({{ number_format(abs($data->laba), 0, ',', '.') }})
                @elseif($data->laba > 0)
                    {{ number_format($data->laba, 0, ',', '.') }}
                @else
                    -
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center" style="padding: 24px; color: #6B7280;">Tidak ada catatan data keuangan pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
    @if(isset($financeTotal))
    <tfoot>
        <tr class="row-total">
            <td colspan="3" class="text-left">Total keseluruhan</td>
            <td class="text-right">{{ number_format($financeTotal->pemasukan ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($financeTotal->pengeluaran ?? 0, 0, ',', '.') }}</td>
            <td class="text-right {{ ($financeTotal->laba ?? 0) < 0 ? 'num-neg' : (($financeTotal->laba ?? 0) > 0 ? 'num-pos' : '') }}">
                @if(($financeTotal->laba ?? 0) < 0)
                    ({{ number_format(abs($financeTotal->laba), 0, ',', '.') }})
                @elseif(($financeTotal->laba ?? 0) > 0)
                    {{ number_format($financeTotal->laba, 0, ',', '.') }}
                @else
                    -
                @endif
            </td>
        </tr>
    </tfoot>
    @endif
</table>

@if(isset($financeTotal))
<div class="doc-summary-box">
    <table class="doc-summary-table">
        <tr>
            <td class="sum-label">Total realisasi pemasukan</td>
            <td class="sum-val">Rp {{ number_format($financeTotal->pemasukan ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="sum-label">Total realisasi pengeluaran</td>
            <td class="sum-val">Rp {{ number_format($financeTotal->pengeluaran ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr class="sum-total">
            <td class="sum-label">Laba bersih operasional</td>
            <td class="sum-val {{ ($financeTotal->laba ?? 0) < 0 ? 'num-neg' : (($financeTotal->laba ?? 0) > 0 ? 'num-pos' : '') }}">
                @if(($financeTotal->laba ?? 0) < 0)
                    -Rp {{ number_format(abs($financeTotal->laba), 0, ',', '.') }}
                @else
                    Rp {{ number_format($financeTotal->laba ?? 0, 0, ',', '.') }}
                @endif
            </td>
        </tr>
    </table>
</div>
@endif
@endsection
