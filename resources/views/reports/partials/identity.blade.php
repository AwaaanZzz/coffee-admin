@php
if (!function_exists('formatTglIndo')) {
    function formatTglIndo($date) {
        if (!$date) return '-';
        try {
            $c = \Carbon\Carbon::parse($date);
            $months = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];
            return $c->day . ' ' . ($months[$c->month] ?? $c->format('M')) . ' ' . $c->year;
        } catch (\Exception $e) {
            return $date;
        }
    }
}
@endphp
<table class="doc-meta-table">
    <tr>
        <td class="meta-label">Toko mitra</td>
        <td class="meta-separator">:</td>
        <td class="meta-value">{{ $metaStore ?? 'Semua Toko Mitra' }}</td>
        <td style="width: 36px;"></td>
        <td class="meta-label">Tanggal cetak</td>
        <td class="meta-separator">:</td>
        <td class="meta-value">{{ formatTglIndo(now()) }}</td>
    </tr>
    <tr>
        <td class="meta-label">Periode</td>
        <td class="meta-separator">:</td>
        <td class="meta-value">{{ $metaPeriod ?? 'Semua data operasional' }}</td>
        <td></td>
        <td class="meta-label">Dicetak oleh</td>
        <td class="meta-separator">:</td>
        <td class="meta-value">{{ auth()->user()->name ?? 'Petugas Administrasi' }}</td>
    </tr>
    @if(isset($metaExtra) && !empty($metaExtra))
    <tr>
        <td class="meta-label">{{ $metaExtra['label'] }}</td>
        <td class="meta-separator">:</td>
        <td class="meta-value" colspan="5">{{ $metaExtra['value'] }}</td>
    </tr>
    @endif
</table>
