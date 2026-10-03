@php
    if (!function_exists('formatDocDate')) {
        function formatDocDate($date) {
            if (!$date) return '-';
            try {
                $c = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
                $monthNames = [
                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                    7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                ];
                return $c->day . ' ' . ($monthNames[$c->month] ?? $c->format('M')) . ' ' . $c->year;
            } catch (\Exception $e) {
                return $date;
            }
        }
    }

    $storeName = $sale->store->name ?? 'Pelanggan';
    $tglStr = formatDocDate($sale->tanggal);
    $totalFormatted = 'Rp ' . number_format((float)$grandTotal, 0, ',', '.');
    $hasDueDate = !empty($sale->jatuh_tempo ?? null);
    $bankName = config('business.bank.name');
    $bankAcc = config('business.bank.account_number');
    $bankHolder = config('business.bank.account_name');
    $hasBank = !empty($bankName) && !empty($bankAcc);

    $publicBase = config('business.public_url');
    if (empty($publicBase)) {
        $website = config('business.website', 'kopihikuhimu.id');
        $publicBase = 'https://' . preg_replace('#^https?://#', '', $website);
    }
    $publicBase = rtrim($publicBase, '/');
    $publicUrl = $publicBase . '/sales/' . $sale->id . '/invoice' . ($isBatch ? '?mode=batch' : '');
    
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
    
    if ($hasBank) {
        $bankLine = "Pembayaran ke " . $bankName . " " . $bankAcc;
        if (!empty($bankHolder)) {
            $bankLine .= ", a.n. " . $bankHolder;
        }
        if (!str_ends_with($bankLine, '.')) {
            $bankLine .= ".";
        }
        $lines[] = $bankLine;
        $lines[] = "";
    }
    
    $lines[] = "Terima kasih.";
    $lines[] = config('business.name');
    $lines[] = config('business.address');
    $lines[] = "Telepon/WA: " . config('business.phone');
    
    echo trim(implode("\n", $lines));
@endphp
