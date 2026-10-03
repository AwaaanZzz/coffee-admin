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
<div class="doc-kop">
    <img src="{{ asset('images/logo-kopi-hiku-himu.png') }}" alt="Logo Kopi Hiku Himu" class="doc-kop-logo">
    <div class="doc-kop-details">
        <h1 class="doc-kop-brand">{{ config('business.name', 'KOPI HIKU HIMU') }}</h1>
        <div class="doc-kop-desc">{{ config('business.tagline', 'Roastery dan Distribusi Kopi Toko Mitra') }}</div>
        <div class="doc-kop-address">{{ config('business.address') }}</div>
        <div class="doc-kop-address">Telepon/WhatsApp: {{ config('business.phone', '0812-1287-8844') }}</div>
    </div>
</div>
