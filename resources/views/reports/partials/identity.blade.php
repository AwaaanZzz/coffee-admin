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
