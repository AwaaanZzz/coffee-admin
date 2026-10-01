<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Penjualan - Kopi Hiku Himu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --font-family: 'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }
        body {
            font-family: var(--font-family);
            color: #0f172a;
            background: #f8fafc;
            padding: 2.5rem 1.5rem;
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }
        .report-sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            padding: 3rem;
        }
        .header-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
            margin: 0;
        }
        .header-sub {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
            margin-top: 4px;
        }
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        .table-custom th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-custom td {
            padding: 10px 14px;
            font-size: 0.86rem;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-custom tr:last-child td {
            border-bottom: none;
        }
        .font-mono {
            font-family: var(--font-mono);
        }
        .btn-print {
            background: #C88A4E;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-print:hover {
            background: #b57a3e;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .report-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 1.5rem 0 !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="report-sheet">
    <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
        <div>
            <h1 class="header-title">Kopi Hiku Himu</h1>
            <div class="header-sub">Laporan rekapitulasi data transaksi penjualan</div>
            <div class="small text-muted mt-2">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
        </div>
        <div class="text-end no-print">
            <button class="btn-print" onclick="window.print()">
                Cetak dokumen
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">#</th>
                    <th>Toko mitra</th>
                    <th>Varian kopi</th>
                    <th class="text-end">Jumlah</th>
                    <th class="text-end">Harga satuan</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $i => $s)
                <tr>
                    <td class="text-center text-muted small" style="font-variant-numeric: tabular-nums;">{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $s->store->name ?? '-' }}</td>
                    <td>{{ $s->coffeeType->name ?? '-' }}</td>
                    <td class="text-end fw-semibold" style="font-variant-numeric: tabular-nums;">{{ $s->jumlah }}</td>
                    <td class="text-end text-muted" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($s->harga, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold" style="font-variant-numeric: tabular-nums;">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                    <td class="text-center small text-muted" style="font-variant-numeric: tabular-nums;">{{ $s->tanggal ? \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted small">Tidak ada data transaksi penjualan yang sesuai filter.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: 700;">
                    <td colspan="3" class="text-end" style="font-size: 0.85rem;">Total keseluruhan:</td>
                    <td class="text-end" style="font-variant-numeric: tabular-nums;">{{ $sales->sum('jumlah') }}</td>
                    <td></td>
                    <td class="text-end text-dark" style="font-size: 0.95rem; font-variant-numeric: tabular-nums;">Rp {{ number_format($sales->sum('total'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-end mt-5 pt-4 text-muted small">
        <div>
            <div>Kopi Hiku Himu Admin Management System</div>
            <div>Dokumen ini sah dan diterbitkan secara digital oleh sistem.</div>
        </div>
        <div class="text-center" style="width: 180px;">
            <div class="mb-5">Penanggung jawab,</div>
            <div class="border-top pt-1 fw-bold text-dark">{{ auth()->user()->name ?? 'Administrator' }}</div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // Auto open print dialog if requested with ?auto=1
        if (new URLSearchParams(window.location.search).get('auto') === '1') {
            window.print();
        }
    });
</script>
</body>
</html>
