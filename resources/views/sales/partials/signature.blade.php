<div class="doc-closing">
    <div class="doc-thanks">Terima kasih atas kerja samanya.</div>

    <div class="doc-signatures">
        <div class="signature-col">
            <div class="signature-role">Penerima,</div>
            <div class="signature-space"></div>
            <div class="signature-line"></div>
            <div class="signature-name">( {{ $sale->store->penanggung_jawab ?: $sale->store->name }} )</div>
            <div class="signature-sub">{{ $sale->store->penanggung_jawab ? $sale->store->name : 'Toko Mitra' }}</div>
            <div class="signature-date">Tanggal: </div>
        </div>

        <div class="signature-col signature-col-right">
            <div class="signature-role">Hormat kami,</div>
            <div class="signature-space"></div>
            <div class="signature-line"></div>
            <div class="signature-name">( {{ config('business.signer.name') ?: 'Pengelola Roastery' }} )</div>
            <div class="signature-sub">{{ config('business.signer.title') ?: config('business.name') }}</div>
            <div class="signature-date">Tanggal: </div>
        </div>
    </div>

    <div class="doc-system-footer">
        Dicetak dari sistem pada {{ formatDocDateTime(now()) }}
    </div>
</div>
