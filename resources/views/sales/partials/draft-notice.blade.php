@php
    $draftReasons = getDocDraftReasons($sale, $items);
    $isDraft = count($draftReasons) > 0;
@endphp

@if($isDraft)
    <div class="draft-watermark" aria-hidden="true">DRAFT</div>
    <div class="draft-notice">
        <strong>Catatan:</strong> Dokumen berstatus DRAFT karena {{ implode(', ', $draftReasons) }}.
    </div>
@endif
