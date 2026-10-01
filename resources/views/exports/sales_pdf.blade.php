<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Kopi Hiku Himu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
            background: #f8fafc;
            padding: 2rem;
        }
        .report-page {
            max-width: 960px;
            margin: 0 auto;
            background: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .header-brand h3 {
            margin: 0;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0f172a;
        }
        .header-brand span {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
        }
        .table-report th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
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
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .report-page {
                box-shadow: none !important;
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
            <button onclick="window.history.back()" class="btn btn-sm btn-outline-secondary">
                &larr; Kembali
            </button>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-sm btn-primary">
                    Cetak / Simpan PDF
                </button>
            </div>
        </div>

        <!-- Document Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="header-brand">
                <h3>KOPI HIKU HIMU</h3>
                <span>Artisan Coffee Roastery & Konsinyasi</span>
                <div class="text-muted small mt-1">Laporan Resmi Penjualan Kopi Mitra</div>
            </div>
            <div class="text-end">
                <div class="badge bg-primary px-3 py-1.5 text-uppercase">Laporan Penjualan</div>
                <div class="small text-muted mt-1">Tanggal Cetak: <strong>{{ date('d/m/Y H:i') }}</strong></div>
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
                <div class="text-muted small text-uppercase fw-semibold">Periode Transaksi:</div>
                <div class="fw-bold fs-6">
                    {{ ($startDate && $endDate) ? date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)) : 'Semua Data Penjualan' }}
                </div>
            </div>
        </div>

        <!-- Summary KPIs -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Total Transaksi</div>
                    <div class="fw-bold fs-5 text-dark">{{ number_format($sales->count()) }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Total Terjual</div>
                    <div class="fw-bold fs-5 text-primary">{{ number_format($sales->sum('jumlah')) }} pcs</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Total Omset Penjualan</div>
                    <div class="fw-bold fs-5 text-success">Rp {{ number_format($sales->sum('total'), 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table table-report table-hover align-middle mb-4">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>Tanggal</th>
                    <th>Toko Mitra</th>
                    <th>Varian Kopi</th>
                    <th>Kode Batch</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $idx => $s)
                <tr>
                    <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                    <td>{{ $s->tanggal->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $s->store->name ?? '-' }}</td>
                    <td>{{ $s->coffeeType->name ?? '-' }}</td>
                    <td class="font-monospace small">{{ $s->stockBatch->kode_produksi ?? '-' }}</td>
                    <td class="text-center font-monospace">{{ $s->jumlah }}</td>
                    <td class="text-end font-monospace">Rp {{ number_format($s->harga, 0, ',', '.') }}</td>
                    <td class="text-end font-monospace fw-bold text-dark">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada transaksi penjualan pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #f1f5f9; font-weight: 700;">
                    <td colspan="5" class="text-uppercase py-3">Total Akumulasi</td>
                    <td class="text-center py-3 font-monospace text-primary">{{ number_format($sales->sum('jumlah')) }} pcs</td>
                    <td class="text-end py-3 text-muted">-</td>
                    <td class="text-end py-3 font-monospace text-success fs-6">Rp {{ number_format($sales->sum('total'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="d-flex justify-content-between align-items-end mt-5 pt-3">
            <div>
                <div class="text-muted small">Disiapkan Oleh (Auditor / Kasir):</div>
                <div class="signature-line">
                    ( {{ auth()->user()->name ?? 'Admin Penjualan' }} )
                </div>
            </div>
            <div>
                <div class="text-muted small text-end">Disetujui Oleh:</div>
                <div class="signature-line ms-auto">
                    ( Manajemen Roastery )
                </div>
            </div>
        </div>
    </div>
</body>
</html>
