<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Print</title>
    <style>
        body { font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif; color: #1e293b; line-height: 1.5; margin: 0; padding: 20px; }
        .print-header { text-align: center; border-bottom: 2px solid #1E3A5F; padding-bottom: 10px; margin-bottom: 20px; }
        .print-header h1 { margin: 0; font-size: 20px; font-weight: 700; color: #1E3A5F; }
        .print-header p { margin: 5px 0 0; color: #64748b; font-size: 13px; }
        .summary-box { display: flex; justify-content: space-between; margin-bottom: 20px; padding: 12px 16px; border: 1px solid #e2e8f0; background: #FAF5EE; border-radius: 8px; }
        .summary-item { text-align: center; }
        .summary-item h4 { margin: 0; font-size: 11px; color: #64748b; font-weight: 600; }
        .summary-item p { margin: 4px 0 0; font-size: 16px; font-weight: 700; color: #1e293b; font-variant-numeric: tabular-nums; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 12px; text-align: left; }
        th { background-color: #f8fafc; font-weight: 600; font-size: 11px; }
        .text-right { text-align: right; font-variant-numeric: tabular-nums; }
        .print-footer { text-align: right; font-size: 10px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        
        @media print {
            body { padding: 0; margin: 0; }
            .summary-box { border: 1px solid #cbd5e1; background: transparent; }
            th, td { border: 1px solid #cbd5e1; }
            th { background-color: transparent !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="print-header">
        <h1>Laporan Penjualan - Kopi Hiku Himu</h1>
        <p>Periode: {{ request('from_date', '01/01/2023') }} s/d {{ request('to_date', '31/12/2023') }}</p>
        <p>Toko: {{ request('store') ? 'Cabang ' . request('store') : 'Semua toko mitra' }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <h4>Total pendapatan</h4>
            <p>Rp {{ number_format($totalRevenue ?? 15000000, 0, ',', '.') }}</p>
        </div>
        <div class="summary-item">
            <h4>Total unit terjual</h4>
            <p>{{ number_format($totalUnit ?? 1250, 0, ',', '.') }}</p>
        </div>
        <div class="summary-item">
            <h4>Rata-rata per transaksi</h4>
            <p>Rp {{ number_format($avgTransaction ?? 45000, 0, ',', '.') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Toko mitra</th>
                <th>Varian kopi</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesData ?? [] as $sale)
                <tr>
                    <td>{{ $sale->date }}</td>
                    <td>{{ $sale->store }}</td>
                    <td>{{ $sale->coffee }}</td>
                    <td class="text-right">{{ $sale->qty }}</td>
                    <td class="text-right">Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Data tidak tersedia</td>
                </tr>
            @endforelse
        </tbody>
        @if(isset($salesData) && count($salesData) > 0)
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">TOTAL</th>
                <th class="text-right">{{ $totalUnit ?? 0 }}</th>
                <th></th>
                <th class="text-right">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="print-footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name ?? 'Administrator' }}
    </div>

</body>
</html>
