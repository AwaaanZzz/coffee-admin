<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Print</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        .print-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .print-header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .print-header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        .summary-box { display: flex; justify-content: space-between; margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; }
        .summary-item { text-align: center; }
        .summary-item h4 { margin: 0; font-size: 12px; color: #666; text-transform: uppercase; }
        .summary-item p { margin: 5px 0 0; font-size: 18px; font-weight: bold; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; text-transform: uppercase; }
        .text-right { text-align: right; }
        .print-footer { text-align: right; font-size: 10px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
        
        @media print {
            body { padding: 0; margin: 0; }
            .summary-box { border: 1px solid #000; background: transparent; }
            th, td { border: 1px solid #000; }
            th { background-color: transparent !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="print-header">
        <h1>LAPORAN PENJUALAN - Coffee Admin</h1>
        <p>Periode: {{ request('from_date', '01-01-2023') }} s/d {{ request('to_date', '31-12-2023') }}</p>
        <p>Toko: {{ request('store') ? 'Cabang ' . request('store') : 'Semua Toko' }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <h4>Total Revenue</h4>
            <p>Rp {{ number_format($totalRevenue ?? 15000000, 0, ',', '.') }}</p>
        </div>
        <div class="summary-item">
            <h4>Total Units Terjual</h4>
            <p>{{ number_format($totalUnit ?? 1250, 0, ',', '.') }}</p>
        </div>
        <div class="summary-item">
            <h4>Rata-rata per Transaksi</h4>
            <p>Rp {{ number_format($avgTransaction ?? 45000, 0, ',', '.') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Toko</th>
                <th>Jenis Kopi</th>
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
