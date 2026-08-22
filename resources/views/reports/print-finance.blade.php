<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Print</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        .print-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .print-header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .print-header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 10px 12px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-success { color: #059669; }
        .text-danger { color: #dc2626; }
        .print-footer { text-align: right; font-size: 10px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
        
        @media print {
            body { padding: 0; margin: 0; }
            th, td { border: 1px solid #000; }
            th { background-color: transparent !important; }
            /* Force colors in print */
            .text-success { color: #059669 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .text-danger { color: #dc2626 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="print-header">
        <h1>LAPORAN KEUANGAN - Coffee Admin</h1>
        <p>Tahun: {{ request('year', date('Y')) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Toko</th>
                <th>Periode</th>
                <th class="text-right">Pemasukan</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Laba/Rugi</th>
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
