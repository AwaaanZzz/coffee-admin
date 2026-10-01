<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Print</title>
    <style>
        body { font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif; color: #1e293b; line-height: 1.5; margin: 0; padding: 20px; }
        .print-header { text-align: center; border-bottom: 2px solid #1E3A5F; padding-bottom: 10px; margin-bottom: 20px; }
        .print-header h1 { margin: 0; font-size: 20px; font-weight: 700; color: #1E3A5F; }
        .print-header p { margin: 5px 0 0; color: #64748b; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px 12px; text-align: left; }
        th { background-color: #f8fafc; font-weight: 600; font-size: 11px; }
        .text-right { text-align: right; font-variant-numeric: tabular-nums; }
        .text-success { color: #2e7d32; }
        .text-danger { color: #c62828; }
        .print-footer { text-align: right; font-size: 10px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        
        @media print {
            body { padding: 0; margin: 0; }
            th, td { border: 1px solid #cbd5e1; }
            th { background-color: transparent !important; }
            /* Force colors in print */
            .text-success { color: #2e7d32 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .text-danger { color: #c62828 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="print-header">
        <h1>Laporan Keuangan - Kopi Hiku Himu</h1>
        <p>Tahun: {{ request('year', date('Y')) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Toko mitra</th>
                <th>Periode</th>
                <th class="text-right">Pemasukan</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Laba / rugi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($financeData ?? [] as $data)
                <tr>
                    <td>{{ $data->toko }}</td>
                    <td>{{ $data->periode }}</td>
                    <td class="text-right">Rp {{ number_format($data->pemasukan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($data->pengeluaran, 0, ',', '.') }}</td>
                    <td class="text-right fw-bold {{ $data->laba >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($data->laba, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Data tidak tersedia</td>
                </tr>
            @endforelse
        </tbody>
        @if(isset($financeTotal))
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL KESELURUHAN</th>
                <th class="text-right">Rp {{ number_format($financeTotal->pemasukan, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($financeTotal->pengeluaran, 0, ',', '.') }}</th>
                <th class="text-right {{ $financeTotal->laba >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($financeTotal->laba, 0, ',', '.') }}
                </th>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="print-footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name ?? 'Administrator' }}
    </div>

</body>
</html>
