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
        border: 1px solid var(--border-color, #E8DFD5);
        border-radius: var(--radius-sm, 8px);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .export-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-sm, 8px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .export-filter-box {
        background: var(--bg-main, #FAF5EE);
        border: 1px solid var(--border-color, #E8DFD5);
        border-radius: var(--radius-sm, 8px);
        padding: 1rem;
    }
    .export-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted, #786C60);
        margin-bottom: 4px;
        display: block;
    }
    .btn-export-xlsx {
        background: var(--navy, #1E3A5F);
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid var(--navy, #1E3A5F);
        border-radius: var(--radius-sm, 8px);
        padding: 8px 14px;
        transition: background 0.15s ease;
    }
    .btn-export-xlsx:hover {
        background: #142841;
        color: #ffffff !important;
    }
    .btn-export-pdf {
        background: var(--bg-card, #ffffff);
        color: var(--text-main, #2C1E14) !important;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid var(--border-color, #cbd5e1);
        border-radius: var(--radius-sm, 8px);
        padding: 8px 12px;
    }
    .btn-export-pdf:hover {
        background: var(--bg-hover, #f8fafc);
        color: var(--text-main, #2C1E14) !important;
    }
    .btn-export-csv {
        background: var(--bg-card, #ffffff);
        color: var(--text-main, #2C1E14);
        border: 1px solid var(--border-color, #cbd5e1);
        border-radius: var(--radius-sm, 8px);
        padding: 8px 12px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .btn-export-csv:hover {
        background: var(--bg-hover, #f8fafc);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-2 px-md-4">
    <!-- Header -->
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="page-title mb-1">Ekspor laporan</h3>
            <p class="page-subtitle text-muted mb-0">
                Pilih jenis laporan dan parameter filter untuk mengunduh rekapitulasi data dalam format Excel (.xlsx), dokumen cetak resmi, atau CSV.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.index') }}" class="btn btn-outline-modern">
                Data penjualan
            </a>
            <a href="{{ route('stock.index') }}" class="btn btn-outline-modern">
                Data stok
            </a>
        </div>
    </div>

    <!-- 3 Export Cards Grid -->
    <div class="row g-4 mb-5">
        <!-- 1. Laporan Penjualan -->
        <div class="col-12 col-lg-4">
            <div class="export-card-modern">
                <h4 class="fw-bold mb-2" style="color: var(--text-main, #1E3A5F);">Laporan penjualan</h4>
                <p class="text-muted small mb-4" style="line-height: 1.5;">
                    Rekapitulasi transaksi penjualan konsinyasi per toko mitra, tanggal transaksi, varian kopi, volume terjual, harga satuan, dan omset pendapatan.
                </p>

                <div class="export-filter-box mb-4">
                    <div class="mb-3">
                        <label class="export-label">Toko mitra</label>
                        <select id="salesStoreSelect" class="form-control-modern form-select form-select-sm">
                            <option value="">Semua toko mitra</option>
                            @foreach($stores as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="export-label">Dari tanggal</label>
                            <input type="date" id="salesStartDate" class="form-control-modern form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="export-label">Sampai tanggal</label>
                            <input type="date" id="salesEndDate" class="form-control-modern form-control-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-auto d-flex flex-column gap-2">
                    <button type="button" class="btn btn-export-xlsx d-flex align-items-center justify-content-center" onclick="exportReport('sales', 'xlsx')">
                        <span>Unduh Excel (.xlsx)</span>
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-export-pdf flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('sales', 'pdf')">
                            <span>Cetak dokumen</span>
                        </button>
                        <button type="button" class="btn btn-export-csv flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('sales', 'csv')">
                            <span>CSV</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Laporan Laba Rugi -->
        <div class="col-12 col-lg-4">
            <div class="export-card-modern">
                <h4 class="fw-bold mb-2" style="color: var(--text-main, #1E3A5F);">Laporan laba rugi</h4>
                <p class="text-muted small mb-4" style="line-height: 1.5;">
                    Analisis margin keuntungan bersih, total omset penjualan, beban modal pokok produksi (HPP) per varian kopi, dan persentase laba kotor.
                </p>

                <div class="export-filter-box mb-4">
                    <div class="mb-3">
                        <label class="export-label">Toko mitra</label>
                        <select id="financeStoreSelect" class="form-control-modern form-select form-select-sm">
                            <option value="">Semua toko mitra</option>
                            @foreach($stores as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="export-label">Dari tanggal</label>
                            <input type="date" id="financeStartDate" class="form-control-modern form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="export-label">Sampai tanggal</label>
                            <input type="date" id="financeEndDate" class="form-control-modern form-control-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-auto d-flex flex-column gap-2">
                    <button type="button" class="btn btn-export-xlsx d-flex align-items-center justify-content-center" onclick="exportReport('finance', 'xlsx')">
                        <span>Unduh Excel (.xlsx)</span>
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-export-pdf flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('finance', 'pdf')">
                            <span>Cetak dokumen</span>
                        </button>
                        <button type="button" class="btn btn-export-csv flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('finance', 'csv')">
                            <span>CSV</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Laporan Inventaris Stok -->
        <div class="col-12 col-lg-4">
            <div class="export-card-modern">
                <h4 class="fw-bold mb-2" style="color: var(--text-main, #1E3A5F);">Laporan inventaris stok</h4>
                <p class="text-muted small mb-4" style="line-height: 1.5;">
                    Status sisa fisik rak toko konsinyasi, kode batch produksi, tanggal kedaluwarsa, jumlah laku, dan nilai estimasi aset kopi beredar.
                </p>

                <div class="export-filter-box mb-4">
                    <div class="mb-3">
                        <label class="export-label">Toko mitra</label>
                        <select id="stockStoreSelect" class="form-control-modern form-select form-select-sm">
                            <option value="">Semua toko mitra</option>
                            @foreach($stores as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="export-label">Status stok</label>
                        <select id="stockStatusSelect" class="form-control-modern form-select form-select-sm">
                            <option value="">Semua status stok</option>
                            <option value="active">Stok aktif (sisa > 0)</option>
                            <option value="expiring">Hampir kedaluwarsa (&le; 7 hari)</option>
                            <option value="expired">Sudah kedaluwarsa</option>
                        </select>
                    </div>
                </div>

                <div class="mt-auto d-flex flex-column gap-2">
                    <button type="button" class="btn btn-export-xlsx d-flex align-items-center justify-content-center" onclick="exportReport('stock', 'xlsx')">
                        <span>Unduh Excel (.xlsx)</span>
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-export-pdf flex-fill d-flex align-items-center justify-content-center" onclick="exportReport('stock', 'pdf')">
                            <span>Cetak dokumen</span>
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
        wb.lastModifiedBy = 'Admin Kopi Hiku Himu';
        wb.created = new Date();
        wb.modified = new Date();

        const sheetName = type === 'stock' ? 'Inventaris Stok' : (type === 'sales' ? 'Penjualan' : 'Laba Rugi');
        const ws = wb.addWorksheet(sheetName, {
            views: [{ showGridLines: true }]
        });

        const rawHeaders = (payload.data && payload.data.length > 0) ? Object.keys(payload.data[0]) : [];
        const totalCols = Math.max(rawHeaders.length, 7);
        const lastColLetter = getColLetter(totalCols);

        // Fetch and embed cap vintage logo in Kop
        let logoImageId = null;
        try {
            const resp = await fetch('/images/logo-kopi-hiku-himu.png');
            if (resp.ok) {
                const blob = await resp.blob();
                const reader = new FileReader();
                const base64Promise = new Promise(resolve => {
                    reader.onloadend = () => resolve(reader.result);
                    reader.readAsDataURL(blob);
                });
                const base64Data = await base64Promise;
                logoImageId = wb.addImage({
                    base64: base64Data,
                    extension: 'png'
                });
            }
        } catch (e) {
            console.warn('Logo could not be embedded in Excel', e);
        }

        // 1. Spacing Row 1
        ws.getRow(1).height = 12;

        // 2. Kop Surat: Rows 2 - 5
        if (logoImageId !== null) {
            ws.addImage(logoImageId, {
                tl: { col: 0.15, row: 1.15 },
                ext: { width: 50, height: 50 }
            });
        }

        const kopCol = 'B';
        ws.mergeCells(`${kopCol}2:${lastColLetter}2`);
        const rName = ws.getCell(`${kopCol}2`);
        rName.value = 'Kopi Hiku Himu';
        rName.font = { name: 'Segoe UI', size: 14, bold: true, color: { argb: 'FF1E3A5F' } };
        rName.alignment = { vertical: 'middle', horizontal: 'left' };
        ws.getRow(2).height = 20;

        ws.mergeCells(`${kopCol}3:${lastColLetter}3`);
        const rSub = ws.getCell(`${kopCol}3`);
        rSub.value = @json(config('business.tagline', 'Roastery & Distribusi Kopi'));
        rSub.font = { name: 'Segoe UI', size: 9.5, color: { argb: 'FF2C1E14' } };
        rSub.alignment = { vertical: 'middle', horizontal: 'left' };
        ws.getRow(3).height = 16;

        ws.mergeCells(`${kopCol}4:${lastColLetter}4`);
        const rAddr = ws.getCell(`${kopCol}4`);
        rAddr.value = @json(config('business.address'));
        rAddr.font = { name: 'Segoe UI', size: 8.5, color: { argb: 'FF64748B' } };
        rAddr.alignment = { vertical: 'middle', horizontal: 'left' };
        ws.getRow(4).height = 15;

        ws.mergeCells(`${kopCol}5:${lastColLetter}5`);
        const rContact = ws.getCell(`${kopCol}5`);
        rContact.value = 'Telepon: ' + @json(config('business.phone', '0812-1287-8844'));
        rContact.font = { name: 'Segoe UI', size: 8.5, color: { argb: 'FF64748B' } };
        rContact.alignment = { vertical: 'middle', horizontal: 'left' };
        ws.getRow(5).height = 15;

        // Thin navy line under Kop
        for (let c = 1; c <= totalCols; c++) {
            const cell = ws.getRow(5).getCell(c);
            cell.border = {
                bottom: { style: 'medium', color: { argb: 'FF1E3A5F' } }
            };
        }

        // Row 6: Spacing
        ws.getRow(6).height = 12;

        // Row 7: Document Title (Sentence case, bold, 12pt, navy)
        let docTitle = 'Laporan';
        if (type === 'sales') docTitle = 'Laporan penjualan';
        else if (type === 'finance') docTitle = 'Laporan laba rugi';
        else if (type === 'stock') docTitle = 'Laporan inventaris stok';

        ws.mergeCells(`A7:${lastColLetter}7`);
        const rTitle = ws.getCell('A7');
        rTitle.value = docTitle;
        rTitle.font = { name: 'Segoe UI', size: 12, bold: true, color: { argb: 'FF1E3A5F' } };
        rTitle.alignment = { vertical: 'middle', horizontal: 'left' };
        ws.getRow(7).height = 22;

        // Row 8: Spacing
        ws.getRow(8).height = 6;

        // Rows 9-10: Identitas Laporan (Tabel 2 kolom tanpa border)
        const curDateStr = formatTglIndo(new Date());
        const midColIdx = Math.max(Math.floor(totalCols / 2) + 1, 4);
        const rightColLetter = getColLetter(midColIdx);
        const rightValLetter = getColLetter(midColIdx + 1);

        // Row 9: Toko Mitra & Tanggal Cetak
        ws.getCell('A9').value = 'Toko mitra:';
        ws.getCell('A9').font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF64748B' } };
        ws.getCell('B9').value = payload.store || 'Semua toko mitra';
        ws.getCell('B9').font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF1E293B' } };

        ws.getCell(`${rightColLetter}9`).value = 'Tanggal cetak:';
        ws.getCell(`${rightColLetter}9`).font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF64748B' } };
        ws.getCell(`${rightValLetter}9`).value = curDateStr;
        ws.getCell(`${rightValLetter}9`).font = { name: 'Segoe UI', size: 9, color: { argb: 'FF1E293B' } };
        ws.getRow(9).height = 18;

        // Row 10: Periode & Dicetak Oleh
        ws.getCell('A10').value = 'Periode:';
        ws.getCell('A10').font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF64748B' } };
        ws.getCell('B10').value = payload.periode || 'Semua periode';
        ws.getCell('B10').font = { name: 'Segoe UI', size: 9, color: { argb: 'FF1E293B' } };

        ws.getCell(`${rightColLetter}10`).value = 'Dicetak oleh:';
        ws.getCell(`${rightColLetter}10`).font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF64748B' } };
        ws.getCell(`${rightValLetter}10`).value = 'Admin';
        ws.getCell(`${rightValLetter}10`).font = { name: 'Segoe UI', size: 9, color: { argb: 'FF1E293B' } };
        ws.getRow(10).height = 18;

        // Row 11: Spacing
        ws.getRow(11).height = 12;

        // Row 12: Data Table Header
        const tableHeaderRowNum = 12;
        const headerRow = ws.getRow(tableHeaderRowNum);
        headerRow.height = 24;

        rawHeaders.forEach((h, idx) => {
            const colNum = idx + 1;
            const cell = headerRow.getCell(colNum);
            cell.value = h;
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF1E3A5F' } }; // Navy Header
            cell.font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FFFFFFFF' } };
            cell.alignment = {
                vertical: 'middle',
                horizontal: isRightCol(h) ? 'right' : (isCenterCol(h) ? 'center' : 'left'),
                wrapText: false
            };
            cell.border = {
                top: { style: 'thin', color: { argb: 'FF1E3A5F' } },
                bottom: { style: 'thin', color: { argb: 'FF1E3A5F' } },
                left: { style: 'thin', color: { argb: 'FF2A4D7B' } },
                right: { style: 'thin', color: { argb: 'FF2A4D7B' } }
            };
        });

        // Set AutoFilter on Table Header
        ws.autoFilter = `A${tableHeaderRowNum}:${lastColLetter}${tableHeaderRowNum}`;

        // Data Rows
        let currentRowNum = tableHeaderRowNum + 1;
        const dataStartRowNum = currentRowNum;

        if (payload.data && payload.data.length > 0) {
            payload.data.forEach((item) => {
                const row = ws.getRow(currentRowNum);
                row.height = 20;

                rawHeaders.forEach((h, cIdx) => {
                    const colNum = cIdx + 1;
                    const cell = row.getCell(colNum);
                    const val = item[h];

                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFFFFF' } };
                    cell.font = { name: 'Segoe UI', size: 9, color: { argb: 'FF1E293B' } };
                    cell.border = {
                        top: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                        bottom: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                        left: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                        right: { style: 'thin', color: { argb: 'FFE2E8F0' } }
                    };

                    if (h.toLowerCase().includes('kode')) {
                        cell.value = String(val || '-');
                        cell.font = { name: 'Consolas', size: 9, bold: true, color: { argb: 'FF1E3A5F' } };
                        cell.alignment = { vertical: 'middle', horizontal: 'center' };
                    } else if (h.toLowerCase().includes('status')) {
                        cell.value = String(val || '-');
                        cell.alignment = { vertical: 'middle', horizontal: 'center' };
                        const st = String(val).toUpperCase();
                        if (st.includes('EXPIRED') || st.includes('TARIK')) {
                            cell.font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FFB91C1C' } };
                        } else if (st.includes('HAMPIR')) {
                            cell.font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FFB45309' } };
                        }
                    } else if (isCurrencyCol(h)) {
                        cell.value = Number(val) || 0;
                        cell.alignment = { vertical: 'middle', horizontal: 'right' };
                        cell.numFmt = '"Rp "#,##0;[Red]"(Rp "#,##0)";"-"';
                    } else if (isQtyCol(h)) {
                        cell.value = Number(val) || 0;
                        cell.alignment = { vertical: 'middle', horizontal: 'right' };
                        cell.numFmt = '#,##0;(#,##0);"-"';
                    } else if (h.includes('%') || h.toLowerCase().includes('margin')) {
                        const numVal = parseFloat(String(val).replace('%', ''));
                        if (!isNaN(numVal)) {
                            cell.value = numVal / 100;
                            cell.numFmt = '0.0%';
                        } else {
                            cell.value = val;
                        }
                        cell.alignment = { vertical: 'middle', horizontal: 'right' };
                    } else if (isCenterCol(h)) {
                        cell.value = val !== undefined && val !== null ? val : '-';
                        cell.alignment = { vertical: 'middle', horizontal: 'center' };
                    } else {
                        cell.value = val !== undefined && val !== null ? val : '-';
                        cell.alignment = { vertical: 'middle', horizontal: 'left' };
                    }
                });
                currentRowNum++;
            });

            // Total Row (Baris total dengan garis atas tipis dan garis bawah ganda)
            const totalRow = ws.getRow(currentRowNum);
            totalRow.height = 24;
            const lastDataRow = currentRowNum - 1;

            rawHeaders.forEach((h, cIdx) => {
                const colNum = cIdx + 1;
                const cell = totalRow.getCell(colNum);
                const colLetter = getColLetter(colNum);

                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFFFFF' } };
                cell.font = { name: 'Segoe UI', size: 9.5, bold: true, color: { argb: 'FF1E3A5F' } };
                cell.border = {
                    top: { style: 'thin', color: { argb: 'FF1E3A5F' } },
                    bottom: { style: 'double', color: { argb: 'FF1E3A5F' } }, // Double navy underline
                    left: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                    right: { style: 'thin', color: { argb: 'FFE2E8F0' } }
                };

                if (cIdx === 0) {
                    cell.value = 'Total';
                    cell.alignment = { vertical: 'middle', horizontal: 'left' };
                } else if (isQtyCol(h)) {
                    cell.value = { formula: `SUM(${colLetter}${dataStartRowNum}:${colLetter}${lastDataRow})` };
                    cell.numFmt = '#,##0;(#,##0);"-"';
                    cell.alignment = { vertical: 'middle', horizontal: 'right' };
                } else if (isCurrencyCol(h) && (h.toLowerCase().includes('total') || h.toLowerCase().includes('nilai') || h.toLowerCase().includes('omset') || h.toLowerCase().includes('laba') || h.toLowerCase().includes('beban'))) {
                    cell.value = { formula: `SUM(${colLetter}${dataStartRowNum}:${colLetter}${lastDataRow})` };
                    cell.numFmt = '"Rp "#,##0;[Red]"(Rp "#,##0)";"-"';
                    cell.alignment = { vertical: 'middle', horizontal: 'right' };
                } else {
                    cell.value = '';
                }
            });
            currentRowNum += 2;
        }

        // Summary 2-column Table (if summary exists)
        if (payload.summary && Object.keys(payload.summary).length > 0) {
            ws.getRow(currentRowNum).height = 18;
            ws.getCell(`A${currentRowNum}`).value = 'Ringkasan eksekutif';
            ws.getCell(`A${currentRowNum}`).font = { name: 'Segoe UI', size: 10, bold: true, color: { argb: 'FF1E3A5F' } };
            currentRowNum++;

            for (const [key, val] of Object.entries(payload.summary)) {
                const sRow = ws.getRow(currentRowNum);
                sRow.height = 19;
                const label = formatSummaryKey(key);

                const cLabel = sRow.getCell(1);
                cLabel.value = label;
                cLabel.font = { name: 'Segoe UI', size: 9, color: { argb: 'FF64748B' } };
                cLabel.border = {
                    top: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                    bottom: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                    left: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                    right: { style: 'thin', color: { argb: 'FFE2E8F0' } }
                };

                const cVal = sRow.getCell(2);
                cVal.border = {
                    top: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                    bottom: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                    left: { style: 'thin', color: { argb: 'FFE2E8F0' } },
                    right: { style: 'thin', color: { argb: 'FFE2E8F0' } }
                };
                cVal.alignment = { vertical: 'middle', horizontal: 'right' };
                cVal.font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF1E293B' } };

                if (typeof val === 'number') {
                    if (key.includes('nominal') || key.includes('omset') || key.includes('hpp') || key.includes('laba') || key.includes('aset')) {
                        cVal.value = val;
                        cVal.numFmt = '"Rp "#,##0;[Red]"(Rp "#,##0)";"-"';
                    } else {
                        cVal.value = val;
                        cVal.numFmt = '#,##0';
                    }
                } else {
                    cVal.value = String(val);
                }
                currentRowNum++;
            }
            currentRowNum += 2;
        }

        // Signature Block (Two columns with solid line and date)
        const signRow1 = currentRowNum;
        const signRow2 = currentRowNum + 4;
        const signRow3 = currentRowNum + 5;
        const signRow4 = currentRowNum + 6;

        ws.getRow(signRow1).height = 18;
        ws.getRow(signRow2).height = 20;
        ws.getRow(signRow3).height = 16;
        ws.getRow(signRow4).height = 16;

        // Left Signature: Toko Mitra
        ws.getCell(`A${signRow1}`).value = 'Penanggung Jawab Toko Mitra';
        ws.getCell(`A${signRow1}`).font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF64748B' } };

        ws.getCell(`A${signRow2}`).value = payload.store && payload.store !== 'Semua Toko' ? payload.store : '(                             )';
        ws.getCell(`A${signRow2}`).font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF1E293B' } };
        ws.getCell(`A${signRow2}`).border = { top: { style: 'thin', color: { argb: 'FF1E3A5F' } } };

        ws.getCell(`A${signRow3}`).value = 'Pengelola Toko Mitra';
        ws.getCell(`A${signRow3}`).font = { name: 'Segoe UI', size: 8.5, color: { argb: 'FF64748B' } };

        ws.getCell(`A${signRow4}`).value = 'Tanggal: ___________________';
        ws.getCell(`A${signRow4}`).font = { name: 'Segoe UI', size: 8.5, color: { argb: 'FF64748B' } };

        // Right Signature: Penanggung Jawab Distribusi
        const rightSigCol = getColLetter(Math.max(totalCols - 1, 4));

        ws.getCell(`${rightSigCol}${signRow1}`).value = 'Penanggung Jawab Distribusi';
        ws.getCell(`${rightSigCol}${signRow1}`).font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF64748B' } };

        ws.getCell(`${rightSigCol}${signRow2}`).value = 'Admin Kopi Hiku Himu';
        ws.getCell(`${rightSigCol}${signRow2}`).font = { name: 'Segoe UI', size: 9, bold: true, color: { argb: 'FF1E293B' } };
        ws.getCell(`${rightSigCol}${signRow2}`).border = { top: { style: 'thin', color: { argb: 'FF1E3A5F' } } };

        ws.getCell(`${rightSigCol}${signRow3}`).value = 'Operasional & Keuangan';
        ws.getCell(`${rightSigCol}${signRow3}`).font = { name: 'Segoe UI', size: 8.5, color: { argb: 'FF64748B' } };

        ws.getCell(`${rightSigCol}${signRow4}`).value = `Tanggal: ${curDateStr}`;
        ws.getCell(`${rightSigCol}${signRow4}`).font = { name: 'Segoe UI', size: 8.5, color: { argb: 'FF64748B' } };

        currentRowNum += 8;

        // Footnote: Row currentRowNum
        ws.getCell(`A${currentRowNum}`).value = 'Dokumen resmi Kopi Hiku Himu. Dicetak otomatis dari sistem manajemen distribusi kopi mitra.';
        ws.getCell(`A${currentRowNum}`).font = { name: 'Segoe UI', size: 8, italic: true, color: { argb: 'FF94A3B8' } };

        // Auto-fit column widths
        rawHeaders.forEach((h, i) => {
            const colNum = i + 1;
            let maxLen = h.length;
            if (payload.data) {
                payload.data.forEach(row => {
                    const str = row[h] ? String(row[h]) : '';
                    if (str.length > maxLen) maxLen = str.length;
                });
            }
            let width = Math.max(maxLen + 3, 11);
            if (h.toLowerCase().includes('kode')) width = Math.max(width, 16);
            if (h.toLowerCase().includes('toko') || h.toLowerCase().includes('kopi')) width = Math.max(width, 22);
            if (isCurrencyCol(h)) width = Math.max(width, 17);
            ws.getColumn(colNum).width = Math.min(width, 36);
        });

        // Ensure Col A width is at least 14 for logo/labels
        ws.getColumn(1).width = Math.max(ws.getColumn(1).width || 12, 14);

        // Download file
        const fileName = `${payload.title || 'laporan'}_${new Date().toISOString().slice(0, 10)}.xlsx`;
        const buffer = await wb.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    function formatSummaryKey(key) {
        const map = {
            'total_transaksi': 'Total transaksi',
            'total_pcs': 'Total volume penjualan',
            'total_nominal': 'Total pendapatan',
            'total_omset': 'Total omset penjualan',
            'total_hpp': 'Total beban pokok (HPP)',
            'total_laba': 'Laba kotor',
            'margin_keseluruhan': 'Margin rata-rata',
            'total_batch': 'Total batch stok',
            'total_sisa_pcs': 'Total sisa fisik',
            'total_nilai_aset': 'Total estimasi aset'
        };
        if (map[key]) return map[key];
        return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function formatTglIndo(d) {
        if (!d) return '-';
        const dateObj = new Date(d);
        if (isNaN(dateObj.getTime())) return String(d);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return `${dateObj.getDate()} ${months[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
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
            alert('Pustaka berkas Excel belum siap. Silakan coba sesaat lagi.');
            return;
        }

        const wsData = [];
        wsData.push([@json(config('business.name', 'Kopi Hiku Himu'))]);
        wsData.push([@json(config('business.tagline', 'Roastery & Distribusi Kopi Mitra'))]);
        wsData.push([@json(config('business.address'))]);
        wsData.push(['Telepon: ' + @json(config('business.phone', '0812-1287-8844'))]);
        wsData.push([]);

        let docTitle = 'Laporan';
        if (type === 'sales') docTitle = 'Laporan penjualan';
        else if (type === 'finance') docTitle = 'Laporan laba rugi';
        else if (type === 'stock') docTitle = 'Laporan inventaris stok';
        wsData.push([docTitle]);
        wsData.push([]);

        wsData.push(['Toko mitra:', payload.store || 'Semua toko mitra', '', 'Tanggal cetak:', formatTglIndo(new Date())]);
        wsData.push(['Periode:', payload.periode || 'Semua periode', '', 'Dicetak oleh:', 'Admin']);
        wsData.push([]);

        if (payload.data && payload.data.length > 0) {
            wsData.push(Object.keys(payload.data[0]));
            payload.data.forEach(row => wsData.push(Object.values(row)));
        }

        if (payload.summary) {
            wsData.push([]);
            wsData.push(['Ringkasan eksekutif:']);
            for (const [key, val] of Object.entries(payload.summary)) {
                wsData.push([formatSummaryKey(key), val]);
            }
        }

        wsData.push([]);
        wsData.push(['Penanggung Jawab Toko Mitra', '', '', 'Penanggung Jawab Distribusi']);
        wsData.push([]);
        wsData.push([]);
        wsData.push([payload.store && payload.store !== 'Semua Toko' ? payload.store : '(                             )', '', '', 'Admin Kopi Hiku Himu']);
        wsData.push(['Tanggal: ___________________', '', '', `Tanggal: ${formatTglIndo(new Date())}`]);
        wsData.push([]);
        wsData.push(['Dokumen resmi Kopi Hiku Himu. Dicetak otomatis dari sistem manajemen distribusi kopi mitra.']);

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        const wb = XLSX.utils.book_new();
        const sheetName = type === 'stock' ? 'Inventaris Stok' : (type === 'sales' ? 'Penjualan' : 'Laba Rugi');
        XLSX.utils.book_append_sheet(wb, ws, sheetName);
        const fileName = `${payload.title || 'laporan'}_${new Date().toISOString().slice(0, 10)}.xlsx`;
        XLSX.writeFile(wb, fileName);
    }
</script>
@endsection
