@extends('layouts.app')

@section('title', 'Ekspor Laporan Komprehensif')

@section('breadcrumbs')
<a href="{{ route('dashboard') }}">Beranda</a>
<i data-lucide="chevron-right"></i>
<span>Ekspor Laporan</span>
@endsection

@section('styles')
<style>
    .export-card-modern {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, #E8DFD5);
        border-radius: var(--radius-lg, 18px);
        padding: 1.75rem;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .export-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--accent, #C88A4E);
    }
    .export-icon-wrapper {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .export-filter-box {
        background: var(--bg-input, #FBF7F0);
        border: 1px solid var(--border, #E8DFD5);
        border-radius: var(--radius, 14px);
        padding: 1.25rem;
    }
    .export-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary, #5A6B7D);
        margin-bottom: 6px;
        display: block;
    }
    .btn-export-xlsx {
        background: #1B4D3E;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.88rem;
        border: 1px solid #163E32;
        border-radius: 10px;
        padding: 10px 16px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(27, 77, 62, 0.15);
        letter-spacing: 0.2px;
    }
    .btn-export-xlsx:hover {
        background: #143B2F;
        border-color: #0E2920;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(27, 77, 62, 0.22);
    }
    .btn-export-pdf {
        background: #842029;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.88rem;
        border: 1px solid #6E1B22;
        border-radius: 10px;
        padding: 10px 16px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(132, 32, 41, 0.15);
        letter-spacing: 0.2px;
    }
    .btn-export-pdf:hover {
        background: #6E1B22;
        border-color: #55151A;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(132, 32, 41, 0.22);
    }
    .btn-export-csv {
        background: #FFFFFF;
        color: var(--text-primary, #1E3A5F);
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        padding: 10px 14px;
        font-weight: 600;
        font-size: 0.88rem;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }
    .btn-export-csv:hover {
        background: #F8FAFC;
        color: #0F172A;
        border-color: #94A3B8;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-2 px-md-4">
    <!-- Header Banner -->
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <span class="badge" style="background: rgba(200,138,78,0.15); color: var(--accent, #C88A4E); font-weight: 700; font-size: 0.75rem;">
                    Export & Download Center
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.75rem; font-weight: 600;">
                    Excel (.xlsx) Murni & PDF Resmi
                </span>
            </div>
            <h2 class="page-title m-0">Ekspor Laporan Komprehensif</h2>
            <p class="page-subtitle mt-1 mb-0">
                Unduh rekapitulasi data operasional, penjualan konsinyasi, dan neraca laba rugi dalam format berkas Excel (.xlsx asli), CSV, atau dokumen PDF resmi berkop Kopi Hiku Himu.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.index') }}" class="btn btn-outline-modern">
                Data Penjualan
            </a>
            <a href="{{ route('stock.index') }}" class="btn btn-outline-modern">
                Data Stock
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-modern p-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.72rem; letter-spacing:0.5px;">Data Penjualan Terdata</div>
                    <div class="fs-4 fw-bold mt-1" style="color: var(--text-primary, #1E3A5F); font-family: 'Manrope', sans-serif;">
                        {{ number_format($totalSalesCount) }} <span style="font-size:0.85rem; font-family:'Manrope',sans-serif;" class="text-muted fw-normal">transaksi</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-modern p-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.72rem; letter-spacing:0.5px;">Batch Stok Fisik</div>
                    <div class="fs-4 fw-bold mt-1" style="color: var(--text-primary, #1E3A5F); font-family: 'Manrope', sans-serif;">
                        {{ number_format($totalStockCount) }} <span style="font-size:0.85rem; font-family:'Manrope',sans-serif;" class="text-muted fw-normal">batch terdaftar</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-modern p-3">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.72rem; letter-spacing:0.5px;">Varian Kopi Roastery</div>
                    <div class="fs-4 fw-bold mt-1" style="color: var(--text-primary, #1E3A5F); font-family: 'Manrope', sans-serif;">
                        {{ number_format($totalCoffeeCount) }} <span style="font-size:0.85rem; font-family:'Manrope',sans-serif;" class="text-muted fw-normal">jenis kopi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 Export Cards Grid -->
    <div class="row g-4 mb-5">
        <!-- 1. Laporan Penjualan -->
        <div class="col-12 col-lg-4">
            <div class="export-card-modern">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-light text-primary border" style="font-size: 0.72rem; font-weight: 600;">Harian / Bulanan</span>
                </div>
                <h4 class="fw-bold mb-2" style="color: var(--text-primary, #1E3A5F); font-family: 'Manrope', sans-serif;">Laporan Penjualan</h4>
                <p class="text-muted small mb-4" style="line-height: 1.5;">
                    Rekapitulasi transaksi penjualan konsinyasi per toko mitra, tanggal transaksi, jenis kopi terjual, harga satuan, dan omset pendapatan.
                </p>

                <div class="export-filter-box mb-4">
                    <div class="mb-3">
                        <label class="export-label">Filter Toko Mitra:</label>
                        <select id="salesStoreSelect" class="form-select form-select-sm" style="border-radius: 8px; border-color: var(--border);">
                            <option value="">Semua Toko Mitra</option>
                            @foreach($stores as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="export-label">Dari Tanggal:</label>
                            <input type="date" id="salesStartDate" class="form-control form-control-sm" style="border-radius: 8px; border-color: var(--border);">
                        </div>
                        <div class="col-6">
                            <label class="export-label">Sampai Tanggal:</label>
                            <input type="date" id="salesEndDate" class="form-control form-control-sm" style="border-radius: 8px; border-color: var(--border);">
                        </div>
                    </div>
                </div>

                <div class="mt-auto d-flex flex-column gap-2">
                    <button type="button" class="btn btn-export-xlsx d-flex align-items-center justify-content-center" onclick="exportReport('sales', 'xlsx')">
                        <span>Download Excel (.xlsx)</span>
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-export-pdf flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('sales', 'pdf')">
                            <span>Cetak PDF</span>
                        </button>
                        <button type="button" class="btn btn-export-csv flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('sales', 'csv')">
                            <span>CSV</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Laporan Laba Rugi (Profit & Loss) -->
        <div class="col-12 col-lg-4">
            <div class="export-card-modern">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-light text-success border" style="font-size: 0.72rem; font-weight: 600;">HPP & Margin</span>
                </div>
                <h4 class="fw-bold mb-2" style="color: var(--text-primary, #1E3A5F); font-family: 'Manrope', sans-serif;">Neraca Laba Rugi</h4>
                <p class="text-muted small mb-4" style="line-height: 1.5;">
                    Analisis margin keuntungan bersih, total omset penjualan, beban modal pokok produksi (HPP) per varian kopi, dan persentase laba kotor.
                </p>

                <div class="export-filter-box mb-4">
                    <div class="mb-3">
                        <label class="export-label">Filter Toko Mitra:</label>
                        <select id="financeStoreSelect" class="form-select form-select-sm" style="border-radius: 8px; border-color: var(--border);">
                            <option value="">Semua Toko Mitra</option>
                            @foreach($stores as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="export-label">Dari Tanggal:</label>
                            <input type="date" id="financeStartDate" class="form-control form-control-sm" style="border-radius: 8px; border-color: var(--border);">
                        </div>
                        <div class="col-6">
                            <label class="export-label">Sampai Tanggal:</label>
                            <input type="date" id="financeEndDate" class="form-control form-control-sm" style="border-radius: 8px; border-color: var(--border);">
                        </div>
                    </div>
                </div>

                <div class="mt-auto d-flex flex-column gap-2">
                    <button type="button" class="btn btn-export-xlsx d-flex align-items-center justify-content-center" onclick="exportReport('finance', 'xlsx')">
                        <span>Download Excel (.xlsx)</span>
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-export-pdf flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('finance', 'pdf')">
                            <span>Cetak PDF</span>
                        </button>
                        <button type="button" class="btn btn-export-csv flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('finance', 'csv')">
                            <span>CSV</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Laporan Inventaris Stok Kopi -->
        <div class="col-12 col-lg-4">
            <div class="export-card-modern">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-light text-warning border" style="font-size: 0.72rem; font-weight: 600;">Audit & Sisa</span>
                </div>
                <h4 class="fw-bold mb-2" style="color: var(--text-primary, #1E3A5F); font-family: 'Manrope', sans-serif;">Inventaris Stok Kopi</h4>
                <p class="text-muted small mb-4" style="line-height: 1.5;">
                    Status sisa fisik rak toko konsinyasi, kode batch produksi, tanggal kedaluwarsa, jumlah laku, dan nilai estimasi aset kopi beredar.
                </p>

                <div class="export-filter-box mb-4">
                    <div class="mb-3">
                        <label class="export-label">Filter Toko Mitra:</label>
                        <select id="stockStoreSelect" class="form-select form-select-sm" style="border-radius: 8px; border-color: var(--border);">
                            <option value="">Semua Toko Mitra</option>
                            @foreach($stores as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="export-label">Filter Status Stok:</label>
                        <select id="stockStatusSelect" class="form-select form-select-sm" style="border-radius: 8px; border-color: var(--border);">
                            <option value="">Semua Status Stok</option>
                            <option value="active">Stok Aktif (Sisa > 0)</option>
                            <option value="expiring">Hampir Kadaluarsa (&le; 7 Hari)</option>
                            <option value="expired">Sudah Kadaluarsa (Expired)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-auto d-flex flex-column gap-2">
                    <button type="button" class="btn btn-export-xlsx d-flex align-items-center justify-content-center" onclick="exportReport('stock', 'xlsx')">
                        <span>Download Excel (.xlsx)</span>
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-export-pdf flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('stock', 'pdf')">
                            <span>Cetak PDF</span>
                        </button>
                        <button type="button" class="btn btn-export-csv flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('stock', 'csv')">
                            <span>CSV</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- ExcelJS CDN for Ultra-Professional Styled Binary .xlsx Generation --}}
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
{{-- SheetJS CDN as Fallback --}}
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    function exportReport(type, format) {
        let storeId = '';
        let startDate = '';
        let endDate = '';
        let status = '';

        if (type === 'sales') {
            storeId = document.getElementById('salesStoreSelect').value;
            startDate = document.getElementById('salesStartDate').value;
            endDate = document.getElementById('salesEndDate').value;
        } else if (type === 'finance') {
            storeId = document.getElementById('financeStoreSelect').value;
            startDate = document.getElementById('financeStartDate').value;
            endDate = document.getElementById('financeEndDate').value;
        } else if (type === 'stock') {
            storeId = document.getElementById('stockStoreSelect').value;
            status = document.getElementById('stockStatusSelect').value;
        }

        const params = new URLSearchParams();
        if (storeId) params.append('store_id', storeId);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (status) params.append('status', status);

        if (format === 'csv') {
            window.location.href = `/export/${type}/csv?` + params.toString();
            return;
        }

        if (format === 'pdf') {
            window.open(`/export/${type}/print?` + params.toString(), '_blank');
            return;
        }

        if (format === 'xlsx') {
            const btn = document.activeElement;
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn && btn.tagName === 'BUTTON') {
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Menyiapkan Template...`;
                btn.disabled = true;
            }

            fetch(`/export/${type}/json?` + params.toString())
                .then(res => res.json())
                .then(async resData => {
                    await generateGenuineExcel(type, resData);
                })
                .catch(err => {
                    console.error('Export error:', err);
                    alert('Gagal mengambil data untuk ekspor Excel. Mengalihkan ke format CSV...');
                    window.location.href = `/export/${type}/csv?` + params.toString();
                })
                .finally(() => {
                    if (btn && btn.tagName === 'BUTTON') {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    }
                });
        }
    }

    async function generateGenuineExcel(type, payload) {
        if (typeof ExcelJS === 'undefined') {
            generateSheetJsFallback(type, payload);
            return;
        }

        const wb = new ExcelJS.Workbook();
        wb.creator = 'Kopi Hiku Himu';
        wb.lastModifiedBy = 'Admin System';
        wb.created = new Date();
        wb.modified = new Date();

        const sheetName = type === 'stock' ? 'Stok Inventaris' : (type === 'sales' ? 'Penjualan' : 'Laba Rugi');
        const ws = wb.addWorksheet(sheetName, {
            views: [{ state: 'frozen', ySplit: payload.summary ? 9 : 6, showGridLines: true }]
        });

        const rawHeaders = (payload.data && payload.data.length > 0) ? Object.keys(payload.data[0]) : [];
        const totalCols = Math.max(rawHeaders.length, 8);
        const lastColLetter = getColLetter(totalCols);

        // 1. Spacing Row 1
        ws.getRow(1).height = 10;

        // 2. Clean Executive Header: Row 2 (No gaudy dark background, clean modern corporate)
        ws.mergeCells(`A2:${lastColLetter}2`);
        const r2 = ws.getCell('A2');
        r2.value = 'KOPI HIKU HIMU  •  ARTISAN ROASTERY';
        r2.font = { name: 'Segoe UI', size: 13, bold: true, color: { argb: 'FF0F172A' } };
        r2.alignment = { vertical: 'middle', horizontal: 'left' };
        ws.getRow(2).height = 24;

        // Subtitle: Row 3
        ws.mergeCells(`A3:${lastColLetter}3`);
        const r3 = ws.getCell('A3');
        const titleText = payload.title.replace(/_/g, ' ').toUpperCase();
        r3.value = titleText;
        r3.font = { name: 'Segoe UI', size: 10.5, bold: true, color: { argb: 'FF334155' } };
        r3.alignment = { vertical: 'middle', horizontal: 'left' };
        ws.getRow(3).height = 20;

        // Metadata: Row 4
        ws.mergeCells(`A4:${lastColLetter}4`);
        const r4 = ws.getCell('A4');
        r4.value = `Toko Mitra: ${payload.store || 'Semua Toko'}   |   Periode: ${payload.periode || '-'}   |   Diunduh: ${new Date().toLocaleString('id-ID')}`;
        r4.font = { name: 'Segoe UI', size: 9, italic: true, color: { argb: 'FF64748B' } };
        r4.alignment = { vertical: 'middle', horizontal: 'left' };
        r4.border = { bottom: { style: 'thin', color: { argb: 'FFE2E8F0' } } };
        ws.getRow(4).height = 20;

        // 3. Compact Minimalist Summary Metrics: Rows 6-7
        let tableHeaderRowNum = 6;
        if (payload.summary) {
            ws.getRow(5).height = 10;
            renderKpiCards(ws, payload.summary, totalCols, type);
            ws.getRow(8).height = 10;
            tableHeaderRowNum = 9;
        } else {
            ws.getRow(5).height = 10;
            tableHeaderRowNum = 6;
        }

        // 4. Data Table Header: Clean Executive Slate
        const headerRow = ws.getRow(tableHeaderRowNum);
        headerRow.height = 25;

        rawHeaders.forEach((h, idx) => {
            const colNum = idx + 1;
            const cell = headerRow.getCell(colNum);
            cell.value = h;
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF1E293B' } }; // Crisp Executive Slate
            cell.font = { name: 'Segoe UI', size: 9.5, bold: true, color: { argb: 'FFFFFFFF' } };
            cell.alignment = { 
                vertical: 'middle', 
                horizontal: isRightCol(h) ? 'right' : (isCenterCol(h) ? 'center' : 'left'),
                wrapText: false
            };
            cell.border = {
                top: { style: 'thin', color: { argb: 'FF0F172A' } },
                bottom: { style: 'thin', color: { argb: 'FF0F172A' } },
                left: { style: 'thin', color: { argb: 'FF334155' } },
                right: { style: 'thin', color: { argb: 'FF334155' } }
            };
        });

        // Set AutoFilter on Table Header
        ws.autoFilter = `A${tableHeaderRowNum}:${lastColLetter}${tableHeaderRowNum}`;

        // 5. Data Rows
        let currentRowNum = tableHeaderRowNum + 1;
        const dataStartRowNum = currentRowNum;

        if (payload.data && payload.data.length > 0) {
            payload.data.forEach((item, rIdx) => {
                const row = ws.getRow(currentRowNum);
                row.height = 20;
                const isOdd = rIdx % 2 === 1;
                const rowBg = isOdd ? 'FFF8FAFC' : 'FFFFFFFF'; // Clean Subtle Zebra

                rawHeaders.forEach((h, cIdx) => {
                    const colNum = cIdx + 1;
                    const cell = row.getCell(colNum);
                    const val = item[h];

                    // Base Style
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: rowBg } };
                    cell.font = { name: 'Segoe UI', size: 9.5, color: { argb: 'FF1E293B' } };
                    cell.border = {
                        top: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                        bottom: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                        left: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                        right: { style: 'thin', color: { argb: 'FFE2E8F0' } }
                    };

                    // Format by Column Type
                    if (h.toLowerCase().includes('kode')) {
                        cell.value = String(val || '');
                        cell.font = { name: 'Consolas', size: 9.5, bold: true, color: { argb: 'FF0F172A' } };
                        cell.alignment = { vertical: 'middle', horizontal: 'center' };
                        cell.numFmt = '@';
                    } else if (h.toLowerCase().includes('status')) {
                        cell.value = String(val || '');
                        cell.alignment = { vertical: 'middle', horizontal: 'center' };
                        const st = String(val).toUpperCase();
                        if (st.includes('AMAN') || st.includes('NORMAL')) {
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFDCFCE7' } };
                            cell.font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF15803D' } };
                        } else if (st.includes('HAMPIR') || st.includes('SEGERA') || st.includes('EXPIRING')) {
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFEF3C7' } };
                            cell.font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FFB45309' } };
                        } else if (st.includes('EXPIRED') || st.includes('TARIK') || st.includes('DITARIK')) {
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFEE2E2' } };
                            cell.font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FFB91C1C' } };
                        }
                    } else if (isCurrencyCol(h)) {
                        cell.value = Number(val) || 0;
                        cell.alignment = { vertical: 'middle', horizontal: 'right' };
                        cell.numFmt = '"Rp "#,##0';
                    } else if (isQtyCol(h)) {
                        cell.value = Number(val) || 0;
                        cell.alignment = { vertical: 'middle', horizontal: 'right' };
                        cell.numFmt = '#,##0';
                    } else if (h.includes('%') || h.toLowerCase().includes('margin')) {
                        cell.value = typeof val === 'string' ? val : (val + '%');
                        cell.alignment = { vertical: 'middle', horizontal: 'right' };
                    } else if (h.toLowerCase().includes('tgl') || h.toLowerCase().includes('tanggal') || h.toLowerCase() === 'no') {
                        cell.value = val;
                        cell.alignment = { vertical: 'middle', horizontal: 'center' };
                    } else {
                        cell.value = val;
                        cell.alignment = { vertical: 'middle', horizontal: 'left' };
                    }
                });
                currentRowNum++;
            });

            // 6. Summary Total Row (Standard Clean Accounting Finish)
            const totalRow = ws.getRow(currentRowNum);
            totalRow.height = 24;
            const lastDataRow = currentRowNum - 1;

            rawHeaders.forEach((h, cIdx) => {
                const colNum = cIdx + 1;
                const cell = totalRow.getCell(colNum);
                const colLetter = getColLetter(colNum);

                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF1F5F9' } };
                cell.font = { name: 'Segoe UI', size: 10, bold: true, color: { argb: 'FF0F172A' } };
                cell.border = {
                    top: { style: 'thin', color: { argb: 'FF94A3B8' } },
                    bottom: { style: 'double', color: { argb: 'FF334155' } }, // Clean double accounting underline
                    left: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                    right: { style: 'thin', color: { argb: 'FFE2E8F0' } }
                };

                if (cIdx === 0) {
                    cell.value = 'TOTAL';
                    cell.alignment = { vertical: 'middle', horizontal: 'center' };
                } else if (isQtyCol(h)) {
                    cell.value = { formula: `SUM(${colLetter}${dataStartRowNum}:${colLetter}${lastDataRow})` };
                    cell.numFmt = '#,##0';
                    cell.alignment = { vertical: 'middle', horizontal: 'right' };
                } else if (isCurrencyCol(h) && (h.toLowerCase().includes('total') || h.toLowerCase().includes('nilai') || h.toLowerCase().includes('omset') || h.toLowerCase().includes('laba') || h.toLowerCase().includes('beban'))) {
                    cell.value = { formula: `SUM(${colLetter}${dataStartRowNum}:${colLetter}${lastDataRow})` };
                    cell.numFmt = '"Rp "#,##0';
                    cell.alignment = { vertical: 'middle', horizontal: 'right' };
                } else {
                    cell.value = '';
                }
            });
            currentRowNum += 3;
        }

        // 7. Clean Signature Block
        renderSignatures(ws, currentRowNum, totalCols, payload.store);

        // 8. Auto-fit column widths
        rawHeaders.forEach((h, i) => {
            const colNum = i + 1;
            let maxLen = h.length;
            if (payload.data) {
                payload.data.forEach(row => {
                    const str = row[h] ? String(row[h]) : '';
                    if (str.length > maxLen) maxLen = str.length;
                });
            }
            let width = Math.max(maxLen + 4, 12);
            if (h.toLowerCase().includes('kode')) width = Math.max(width, 16);
            if (h.toLowerCase().includes('toko') || h.toLowerCase().includes('kopi')) width = Math.max(width, 22);
            if (isCurrencyCol(h)) width = Math.max(width, 18);
            ws.getColumn(colNum).width = Math.min(width, 40);
        });

        // 9. Download file
        const fileName = `${payload.title}_${new Date().toISOString().slice(0, 10)}.xlsx`;
        const buffer = await wb.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    function renderKpiCards(ws, summary, totalCols, type) {
        ws.getRow(6).height = 18;
        ws.getRow(7).height = 22;

        const entries = Object.entries(summary);
        let curCol = 1;

        entries.forEach(([key, val], idx) => {
            if (curCol > totalCols) return;
            const endCol = Math.min(curCol + 1, totalCols);
            const colA = getColLetter(curCol);
            const colB = getColLetter(endCol);

            const label = key.replace(/_/g, ' ').toUpperCase();
            let displayVal = val;
            if (typeof val === 'number') {
                if (key.includes('nominal') || key.includes('omset') || key.includes('hpp') || key.includes('laba') || key.includes('aset')) {
                    displayVal = `Rp ${new Intl.NumberFormat('id-ID').format(val)}`;
                } else if (key.includes('pcs') || key.includes('qty')) {
                    displayVal = `${new Intl.NumberFormat('id-ID').format(val)} Pcs`;
                } else if (key.includes('batch')) {
                    displayVal = `${val} Batch`;
                } else if (key.includes('transaksi')) {
                    displayVal = `${val} Transaksi`;
                }
            }

            // Header Row 6
            ws.mergeCells(`${colA}6:${colB}6`);
            const hCell = ws.getCell(`${colA}6`);
            hCell.value = label;
            hCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF1F5F9' } };
            hCell.font = { name: 'Segoe UI', size: 8, bold: true, color: { argb: 'FF475569' } };
            hCell.alignment = { vertical: 'middle', horizontal: 'center' };
            hCell.border = {
                top: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                left: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                right: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                bottom: { style: 'thin', color: { argb: 'FFE2E8F0' } }
            };

            // Value Row 7
            ws.mergeCells(`${colA}7:${colB}7`);
            const vCell = ws.getCell(`${colA}7`);
            vCell.value = displayVal;
            vCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFFFFF' } };
            vCell.font = { name: 'Segoe UI', size: 10.5, bold: true, color: { argb: 'FF0F172A' } };
            vCell.alignment = { vertical: 'middle', horizontal: 'center' };
            vCell.border = {
                left: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                right: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                bottom: { style: 'thin', color: { argb: 'FFE2E8F0' } }
            };

            curCol += 2;
        });
    }

    function renderSignatures(ws, startRow, totalCols, storeName) {
        const r1 = startRow;
        const r2 = startRow + 4;
        ws.getRow(r1).height = 18;
        ws.getRow(r2).height = 20;

        const colA = 'A';
        const colB = 'C';
        ws.mergeCells(`${colA}${r1}:${colB}${r1}`);
        const c1 = ws.getCell(`${colA}${r1}`);
        c1.value = 'Penanggung Jawab Mitra:';
        c1.font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF64748B' } };
        c1.alignment = { horizontal: 'center', vertical: 'middle' };

        ws.mergeCells(`${colA}${r2}:${colB}${r2}`);
        const c2 = ws.getCell(`${colA}${r2}`);
        c2.value = `( ${storeName || 'Pihak Toko Mitra'} )`;
        c2.font = { name: 'Segoe UI', size: 9.5, bold: true, color: { argb: 'FF0F172A' } };
        c2.alignment = { horizontal: 'center', vertical: 'middle' };
        c2.border = { top: { style: 'thin', color: { argb: 'FF94A3B8' } } };

        const rightStartCol = Math.max(totalCols - 2, 5);
        const colR1 = getColLetter(rightStartCol);
        const colR2 = getColLetter(totalCols);

        ws.mergeCells(`${colR1}${r1}:${colR2}${r1}`);
        const cr1 = ws.getCell(`${colR1}${r1}`);
        cr1.value = 'Diverifikasi Oleh:';
        cr1.font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF64748B' } };
        cr1.alignment = { horizontal: 'center', vertical: 'middle' };

        ws.mergeCells(`${colR1}${r2}:${colR2}${r2}`);
        const cr2 = ws.getCell(`${colR1}${r2}`);
        cr2.value = '( Admin Kopi Hiku Himu )';
        cr2.font = { name: 'Segoe UI', size: 9.5, bold: true, color: { argb: 'FF0F172A' } };
        cr2.alignment = { horizontal: 'center', vertical: 'middle' };
        cr2.border = { top: { style: 'thin', color: { argb: 'FF94A3B8' } } };
    }

    function isCurrencyCol(header) {
        const h = (header || '').toLowerCase();
        return h.includes('(rp)') || h.includes('harga') || h.includes('total') || h.includes('omset') || h.includes('hpp') || h.includes('laba') || h.includes('nilai') || h.includes('modal');
    }

    function isQtyCol(header) {
        const h = (header || '').toLowerCase();
        return h.includes('(pcs)') || h.includes('jumlah') || h.includes('terjual') || h.includes('sisa') || h.includes('stok masuk') || h.includes('qty');
    }

    function isCenterCol(header) {
        const h = (header || '').toLowerCase();
        return h === 'no' || h.includes('kode') || h.includes('tgl') || h.includes('tanggal') || h.includes('kategori') || h.includes('status');
    }

    function isRightCol(header) {
        return isCurrencyCol(header) || isQtyCol(header) || (header && header.includes('%'));
    }

    function getColLetter(colIdx) {
        let temp = '';
        let letter = '';
        while (colIdx > 0) {
            temp = (colIdx - 1) % 26;
            letter = String.fromCharCode(temp + 65) + letter;
            colIdx = Math.floor((colIdx - temp - 1) / 26);
        }
        return letter || 'A';
    }

    function generateSheetJsFallback(type, payload) {
        if (typeof XLSX === 'undefined') {
            alert('Library Excel belum siap. Silakan coba sesaat lagi.');
            return;
        }

        const wsData = [];
        const titleText = payload.title.replace(/_/g, ' ').toUpperCase();
        wsData.push(['KOPI HIKU HIMU - ARTISAN ROASTERY']);
        wsData.push([titleText]);
        wsData.push([`Toko Mitra: ${payload.store} | Periode: ${payload.periode || '-'}`]);
        wsData.push([`Waktu Unduh: ${new Date().toLocaleString('id-ID')}`]);
        wsData.push([]);

        if (payload.summary) {
            wsData.push(['RINGKASAN LAPORAN:']);
            for (const [key, val] of Object.entries(payload.summary)) {
                const label = key.replace(/_/g, ' ').toUpperCase();
                wsData.push([label, val]);
            }
            wsData.push([]);
        }

        if (payload.data && payload.data.length > 0) {
            wsData.push(Object.keys(payload.data[0]));
            payload.data.forEach(row => wsData.push(Object.values(row)));
        }

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Laporan");
        const fileName = `${payload.title}_${new Date().toISOString().slice(0,10)}.xlsx`;
        XLSX.writeFile(wb, fileName);
    }
</script>
@endsection
