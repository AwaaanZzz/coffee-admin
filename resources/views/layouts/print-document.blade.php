<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('document-title', 'Laporan Kopi Hiku Himu')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 @yield('page-orientation', 'portrait');
            margin: 15mm;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        :root {
            --c-navy: #1E3A5F;
            --c-black: #111827;
            --c-gray-muted: #6B7280;
            --c-border: #D1D5DB;
            --c-line-navy: #1E3A5F;
            --c-danger: #DC2626;
            --c-success: #16A34A;
        }

        body {
            margin: 0;
            padding: 24px;
            background-color: #F3F4F6;
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 9pt;
            line-height: 1.45;
            color: var(--c-black);
            -webkit-font-smoothing: antialiased;
        }

        /* Screen Preview Paper Sheet */
        .sheet {
            max-width: @yield('sheet-max-width', '210mm');
            min-height: 297mm;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 15mm;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
        }

        /* Non-Print Action Bar */
        .screen-actions {
            max-width: @yield('sheet-max-width', '210mm');
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-action-back {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            font-size: 8.5pt;
            font-weight: 500;
            color: #374151;
            background: #FFFFFF;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-action-back:hover {
            background: #F9FAFB;
        }

        .btn-action-print {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            font-size: 8.5pt;
            font-weight: 600;
            color: #FFFFFF;
            background: var(--c-navy);
            border: 1px solid var(--c-navy);
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-action-print:hover {
            background: #152943;
        }

        /* 1. Kop Surat */
        .doc-kop {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 12px;
            border-bottom: 1.5px solid var(--c-line-navy);
            margin-bottom: 16px;
        }

        .doc-kop-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .doc-kop-details {
            flex-grow: 1;
        }

        .doc-kop-brand {
            font-size: 13pt;
            font-weight: 800;
            color: var(--c-navy);
            margin: 0 0 2px 0;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .doc-kop-desc {
            font-size: 8.5pt;
            font-weight: 500;
            color: #4B5563;
            margin: 0 0 2px 0;
        }

        .doc-kop-address {
            font-size: 8pt;
            color: var(--c-gray-muted);
            margin: 0;
            line-height: 1.35;
        }

        /* 2. Judul Dokumen */
        .doc-title-block {
            margin-bottom: 12px;
        }

        .doc-title {
            font-size: 12.5pt;
            font-weight: 700;
            color: var(--c-black);
            margin: 0;
            line-height: 1.2;
        }

        /* 3. Identitas Laporan (Tabel 2 Kolom Tanpa Border) */
        .doc-meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }

        .doc-meta-table td {
            padding: 2px 0;
            vertical-align: top;
            border: none;
        }

        .doc-meta-table .meta-label {
            width: 110px;
            color: #4B5563;
            font-weight: 500;
        }

        .doc-meta-table .meta-separator {
            width: 12px;
            color: #6B7280;
        }

        .doc-meta-table .meta-value {
            color: var(--c-black);
            font-weight: 600;
        }

        /* 4. Tabel Data Utama */
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }

        .doc-table th {
            background-color: var(--c-navy);
            color: #FFFFFF;
            font-weight: 600;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid var(--c-navy);
            font-size: 8pt;
            line-height: 1.3;
        }

        .doc-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #E5E7EB;
            border-left: 1px solid #F3F4F6;
            border-right: 1px solid #F3F4F6;
            color: var(--c-black);
            vertical-align: middle;
        }

        .doc-table tbody tr:nth-child(even) td {
            background-color: #FAFAFA;
        }

        /* Baris Total: Garis atas tipis, garis bawah ganda */
        .doc-table tfoot tr td,
        .doc-table tr.row-total td {
            font-weight: 700;
            color: var(--c-black);
            background-color: #FFFFFF !important;
            border-top: 1px solid var(--c-line-navy) !important;
            border-bottom: 3px double var(--c-line-navy) !important;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        /* Utilitas Teks & Angka */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .num-pos { color: var(--c-success); }
        .num-neg { color: var(--c-danger); }
        .num-zero { color: #9CA3AF; }

        .code-batch {
            font-family: 'JetBrains Mono', monospace;
            font-size: 7.5pt;
        }

        /* 5. Tabel Ringkasan Eksekutif (2 Kolom Kecil di Bawah Tabel) */
        .doc-summary-box {
            margin-top: 12px;
            margin-bottom: 14px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }

        .doc-summary-table {
            width: 330px;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .doc-summary-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #E5E7EB;
        }

        .doc-summary-table .sum-label {
            color: #4B5563;
            font-weight: 500;
            text-align: left;
        }

        .doc-summary-table .sum-val {
            text-align: right;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            color: var(--c-black);
        }

        .doc-summary-table tr.sum-total td {
            border-top: 1px solid var(--c-line-navy);
            border-bottom: 3px double var(--c-line-navy);
            font-weight: 700;
        }

        /* 6. Catatan Kaki */
        .doc-footnote {
            font-size: 7.5pt;
            color: var(--c-gray-muted);
            margin-top: 8px;
            margin-bottom: 18px;
            border-top: 1px dashed #E5E7EB;
            padding-top: 6px;
            page-break-inside: avoid;
        }

        /* 7. Blok Tanda Tangan */
        .doc-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .doc-sig-col {
            width: 210px;
            text-align: left;
            font-size: 8.5pt;
        }

        .doc-sig-role {
            font-weight: 500;
            color: #374151;
            margin-bottom: 50px;
        }

        .doc-sig-line {
            border-bottom: 1px solid var(--c-navy);
            margin-bottom: 4px;
            width: 100%;
        }

        .doc-sig-name {
            font-weight: 700;
            color: var(--c-black);
            margin: 0;
            line-height: 1.3;
        }

        .doc-sig-title {
            font-size: 8pt;
            color: var(--c-gray-muted);
            margin: 0;
        }

        .doc-sig-date {
            font-size: 8pt;
            color: #4B5563;
            margin-top: 2px;
        }

        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
            }

            .sheet {
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
                min-height: auto !important;
            }

            .screen-actions {
                display: none !important;
            }
        }
    </style>
    @yield('document-styles')
</head>
<body>

    <!-- Tombol Aksi Layar (Tersembunyi saat Cetak) -->
    <div class="screen-actions">
        <a href="javascript:history.back()" class="btn-action-back">Kembali</a>
        <button onclick="window.print()" class="btn-action-print">Cetak dokumen (A4)</button>
    </div>

    <!-- Halaman Kertas A4 -->
    <div class="sheet">
        <!-- 1. Kop Surat Resmi -->
        @include('reports.partials.header')

        <!-- 2. Konten Dokumen Utama -->
        @yield('document-content')

        <!-- 3. Blok Tanda Tangan -->
        @include('reports.partials.signatures')

        <!-- 4. Catatan Kaki / Footer -->
        @include('reports.partials.footer')
    </div>

</body>
</html>
