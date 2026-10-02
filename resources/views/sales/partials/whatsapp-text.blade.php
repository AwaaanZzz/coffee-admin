@include('sales.partials.doc-helpers')@php
    $storeName = $sale->store->name ?? 'Pelanggan';
    $tglStr = formatDocDate($sale->tanggal);
    $totalFormatted = (float)$grandTotal > 0 ? ('Rp ' . number_format($grandTotal, 0, ',', '.')) : '-';
    $hasDueDate = !empty($sale->jatuh_tempo ?? null);
    $bankName = config('business.bank.name');
    $bankAcc = config('business.bank.account_number');
    $bankHolder = config('business.bank.account_name');
    $hasBank = !empty($bankName) && !empty($bankAcc);
    $publicUrl = route('sales.invoice', $sale->id) . ($isBatch ? '?mode=batch' : '');
    
    $lines = [];
    $lines[] = "Yth. " . $storeName . ",";
    $lines[] = "";
    $lines[] = "Berikut faktur dari " . config('business.name') . ".";
    $lines[] = "";
    $lines[] = "No. Faktur  : " . $invoiceNumber;
    $lines[] = "Tanggal     : " . $tglStr;
    
    if ($items->count() === 1) {
        $firstItem = $items->first();
        $vName = $firstItem->coffeeType->name ?? 'Kopi';
        $lines[] = "Rincian     : " . $vName . " " . $firstItem->jumlah . " pcs";
    } else {
        $lines[] = "Rincian     :";
        foreach ($items->take(5) as $it) {
            $vName = $it->coffeeType->name ?? 'Kopi';
            $lines[] = "- " . $vName . " " . $it->jumlah . " pcs";
        }
        if ($items->count() > 5) {
            $lines[] = "- dan " . ($items->count() - 5) . " varian lain";
        }
    }
    
    $lines[] = "Total       : " . $totalFormatted;
    
    if ($hasDueDate) {
        $lines[] = "Jatuh tempo : " . formatDocDate($sale->jatuh_tempo);
    }
    
    $lines[] = "";
    $lines[] = "Faktur lengkap (PDF): " . $publicUrl;
    $lines[] = "";
    
    if ($hasBank) {
        $bankLine = "Pembayaran ke " . $bankName . " " . $bankAcc;
        if (!empty($bankHolder)) {
            $bankLine .= ", a.n. " . $bankHolder;
        }
        $bankLine .= ".";
        $lines[] = $bankLine;
    }
    
    $lines[] = "Terima kasih.";
    $lines[] = config('business.name') . ", " . config('business.phone');
    
    echo implode("\n", $lines);
@endphp
