<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris Stok Kopi - Kopi Hiku Himu</title>
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
            max-width: 1020px;
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
                <div class="text-muted small mt-1">Laporan fisik inventaris stok & mutasi batch</div>
            </div>
            <div class="text-end">
                <div class="badge bg-light text-dark border px-3 py-1.5" style="border-radius: 6px;">Laporan inventaris stok</div>
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
                <div class="text-muted small text-uppercase fw-semibold">Status Audit:</div>
                <div class="fw-bold fs-6 text-primary">Rekonsiliasi Fisik Konsinyasi</div>
            </div>
        </div>

        <!-- Summary KPIs -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Total Batch Terdata</div>
                    <div class="fw-bold fs-5 text-dark">{{ number_format($stockItems->count()) }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Total Sisa Fisik Rak</div>
                    <div class="fw-bold fs-5 text-primary">{{ number_format($stockItems->sum('sisa')) }} pcs</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small text-uppercase">Estimasi Nilai Aset Beredar</div>
                    <div class="fw-bold fs-5 text-success">Rp {{ number_format($stockItems->sum('nilai_aset'), 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table table-report table-hover align-middle mb-4">
            <thead>
                <tr>
                    <th class="text-center" style="width: 35px;">No</th>
                    <th>Kode Batch</th>
                    <th>Toko Mitra</th>
                    <th>Varian Kopi</th>
                    <th class="text-center">Awal</th>
                    <th class="text-center">Laku</th>
                    <th class="text-center">Sisa</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Nilai Sisa</th>
                    <th class="text-center">Exp Date</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockItems as $idx => $it)
                <tr>
                    <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                    <td class="font-monospace fw-semibold">{{ $it['kode_produksi'] }}</td>
                    <td>{{ $it['store_name'] }}</td>
                    <td>
                        <div class="fw-bold">{{ $it['coffee_name'] }}</div>
                        <small class="text-muted">{{ $it['category'] }}</small>
                    </td>
                    <td class="text-center font-monospace">{{ $it['jumlah_stock'] }}</td>
                    <td class="text-center font-monospace text-muted">{{ $it['laku'] }}</td>
                    <td class="text-center font-monospace fw-bold text-primary">{{ $it['sisa'] }}</td>
                    <td class="text-end font-monospace text-muted">Rp {{ number_format($it['harga_jual'], 0, ',', '.') }}</td>
                    <td class="text-end font-monospace fw-bold text-dark">Rp {{ number_format($it['nilai_aset'], 0, ',', '.') }}</td>
                    <td class="text-center font-monospace small">{{ $it['tgl_exp'] }}</td>
                    <td class="text-center">
                        @if($it['is_expired'])
                            <span class="badge bg-danger">Expired</span>
                        @elseif($it['is_expiring'])
                            <span class="badge bg-warning text-dark">Hampir Exp</span>
                        @else
                            <span class="badge bg-success">Aman</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-4 text-muted">Tidak ada data stok inventaris yang sesuai.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #f1f5f9; font-weight: 700;">
                    <td colspan="4" class="text-uppercase py-3">Total Akumulasi Fisik</td>
                    <td class="text-center py-3 font-monospace">{{ number_format($stockItems->sum('jumlah_stock')) }}</td>
                    <td class="text-center py-3 font-monospace text-muted">{{ number_format($stockItems->sum('laku')) }}</td>
                    <td class="text-center py-3 font-monospace text-primary">{{ number_format($stockItems->sum('sisa')) }} pcs</td>
                    <td class="text-end py-3 text-muted">-</td>
                    <td class="text-end py-3 font-monospace text-success fs-6">Rp {{ number_format($stockItems->sum('nilai_aset'), 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="d-flex justify-content-between align-items-end mt-5 pt-3">
            <div>
                <div class="text-muted small">Petugas Gudang & Roastery:</div>
                <div class="signature-line">
                    ( {{ auth()->user()->name ?? 'Inventory Staff' }} )
                </div>
            </div>
            <div>
                <div class="text-muted small text-end">Head of Roastery:</div>
                <div class="signature-line ms-auto">
                    ( Kopi Hiku Himu )
                </div>
            </div>
        </div>
    </div>
</body>
</html>
