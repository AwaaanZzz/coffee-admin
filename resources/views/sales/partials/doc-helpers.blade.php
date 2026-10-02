@php
if (!function_exists('formatDocDate')) {
    function formatDocDate($date) {
        if (!$date) return '-';
        try {
            $c = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
            $months = [
                1 => 'Okt', 2 => 'Nov', // placeholder safety
            ];
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

if (!function_exists('formatDocTime')) {
    function formatDocTime($date) {
        if (!$date) return '-';
        try {
            $c = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
            return $c->format('H.i') . ' WIB';
        } catch (\Exception $e) {
            return '';
        }
    }
}

if (!function_exists('formatDocDateTime')) {
    function formatDocDateTime($date) {
        if (!$date) return '-';
        return formatDocDate($date) . ', ' . formatDocTime($date);
    }
}

if (!function_exists('formatDocRupiah')) {
    function formatDocRupiah($amount, $withSymbol = true) {
        $val = (float) $amount;
        if (abs($val) < 0.0001) {
            return '-';
        }
        $formatted = number_format($val, 0, ',', '.');
        return $withSymbol ? ('Rp ' . $formatted) : $formatted;
    }
}

if (!function_exists('getDocDraftReasons')) {
    function getDocDraftReasons($sale, $items) {
        $reasons = [];
        $hasZeroPrice = false;
        foreach ($items as $item) {
            if ($item->jumlah > 0 && (float)$item->harga <= 0) {
                $hasZeroPrice = true;
                break;
            }
        }
        if ($hasZeroPrice) {
            $reasons[] = 'Terdapat barang dengan harga satuan 0';
        }
        if (empty($sale->store?->alamat)) {
            $reasons[] = 'Alamat toko mitra belum diisi';
        }
        return $reasons;
    }
}
@endphp
