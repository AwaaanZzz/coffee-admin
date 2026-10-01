<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi (Profit & Loss) - Kopi Hiku Himu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #1e293b;
            background: #f8fafc;
            padding: 2rem;
        }
        .report-page {
            max-width: 960px;
            margin: 0 auto;
            background: #fff;
            padding: 2.5rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .header-brand h3 {
            margin: 0;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #1E3A5F;
        }
        .header-brand span {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
        }
        .table-report th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.8rem;
            border-bottom: 2px solid #cbd5e1;
        }
        .table-report td {
            font-size: 0.85rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .signature-line {
            width: 170px;
            border-top: 1px dashed #64748b;
            margin-top: 60px;
            padding-top: 6px;
            text-align: center;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .btn-print-accent {
            background: #C88A4E;
            color: #ffffff;
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .btn-print-accent:hover {
            background: #b57a3e;
            color: #ffffff;
        }
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .report-page {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="report-page">
        <!-- Floating Actions for non-print -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print">
            <button onclick="window.history.back()" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                &larr; Kembali
            </button>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn-print-accent">
                    Cetak / simpan PDF
                </button>
            </div>
        </div>

        <!-- Document Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="header-brand">
                <h3>KOPI HIKU HIMU</h3>
                <span>Artisan Coffee Roastery & Konsinyasi</span>
                <div class="text-muted small mt-1">Laporan laba rugi & neraca margin HPP</div>
            </div>
            <div class="text-end">
                <div class="badge bg-light text-dark border px-3 py-1.5" style="border-radius: 6px;">Profit & Loss Report</div>
                <div class="small text-muted mt-1">Tanggal cetak: <strong>{{ date('d/m/Y H:i') }}</strong></div>
            </div>
        </div>

        <hr style="border-top: 2px solid #cbd5e1; margin: 1.5rem 0;">

        <!-- Meta info -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="text-muted small text-uppercase fw-semibold">Toko Mitra:</div>
                <div class="fw-bold fs-6">{{ $selectedStore?->name ?? 'Semua Toko Mitra' }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="text-muted small text-uppercase fw-semibold">Periode Perhitungan:</div>
                <div class="fw-bold fs-6">
                    {{ ($startDate && $endDate) ? date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)) : 'Akumulasi Seluruh Waktu' }}
                </div>
            </div>
        </div>

        <!-- Summary KPIs -->
        <div class="row g-3 mb-4">
            <div class="col-3">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Total Omset Penjualan</div>
                    <div class="fw-bold fs-6 text-dark">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-3">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Beban Pokok (HPP)</div>
                    <div class="fw-bold fs-6 text-danger">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-3">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Laba Kotor (Gross)</div>
                    <div class="fw-bold fs-6 text-success">Rp {{ number_format($totalLaba, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-3">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Margin Rata-rata</div>
                    <div class="fw-bold fs-6 text-primary">{{ round($overallMargin, 1) }}%</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table table-report table-hover align-middle mb-4">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>Varian Kopi</th>
                    <th>Kategori</th>
                    <th class="text-center">Terjual</th>
                    <th class="text-end">Omset</th>
                    <th class="text-end">Modal HPP</th>
                    <th class="text-end">Total HPP</th>
                    <th class="text-end">Laba Kotor</th>
                    <th class="text-center">Margin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportItems as $idx => $it)
                <tr>
                    <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                    <td class="fw-bold text-dark">{{ $it['name'] }}</td>
                    <td><span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $it['category'] }}</span></td>
                    <td class="text-center font-monospace">{{ $it['total_qty'] }} pcs</td>
                    <td class="text-end font-monospace">Rp {{ number_format($it['revenue'], 0, ',', '.') }}</td>
                    <td class="text-end font-monospace text-muted">Rp {{ number_format($it['modal'], 0, ',', '.') }}</td>
                    <td class="text-end font-monospace text-danger">Rp {{ number_format($it['total_hpp'], 0, ',', '.') }}</td>
                    <td class="text-end font-monospace fw-bold text-success">Rp {{ number_format($it['laba_kotor'], 0, ',', '.') }}</td>
                    <td class="text-center font-monospace fw-bold {{ $it['margin_pct'] >= 40 ? 'text-success' : 'text-primary' }}">
                        {{ $it['margin_pct'] }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f1f5f9; font-weight: 700;">
                    <td colspan="3" class="text-uppercase py-3">Total Konsolidasi</td>
                    <td class="text-center py-3 font-monospace text-primary">{{ number_format($reportItems->sum('total_qty')) }} pcs</td>
                    <td class="text-end py-3 font-monospace text-dark">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    <td class="text-end py-3 text-muted">-</td>
                    <td class="text-end py-3 font-monospace text-danger">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                    <td class="text-end py-3 font-monospace text-success fs-6">Rp {{ number_format($totalLaba, 0, ',', '.') }}</td>
                    <td class="text-center py-3 font-monospace text-primary">{{ round($overallMargin, 1) }}%</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="d-flex justify-content-between align-items-end mt-5 pt-3">
            <div>
                <div class="text-muted small">Manajer Keuangan:</div>
                <div class="signature-line">
                    ( {{ auth()->user()->name ?? 'Finance Officer' }} )
                </div>
            </div>
            <div>
                <div class="text-muted small text-end">Direktur Utama:</div>
                <div class="signature-line ms-auto">
                    ( Kopi Hiku Himu )
                </div>
            </div>
        </div>
    </div>
</body>
</html>
