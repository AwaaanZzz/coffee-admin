<div class="doc-signatures">
    <div class="doc-sig-col">
        <div class="doc-sig-role">{{ $sigRoleLeft ?? 'Petugas operasional,' }}</div>
        <div class="doc-sig-line"></div>
        <div class="doc-sig-name">{{ $sigNameLeft ?? (auth()->user()->name ?? 'Petugas Administrasi') }}</div>
        <div class="doc-sig-title">Jabatan: {{ $sigTitleLeft ?? 'Staf Administrasi' }}</div>
        <div class="doc-sig-date">Tanggal: {{ formatTglIndo(now()) }}</div>
    </div>
    <div class="doc-sig-col">
        <div class="doc-sig-role">{{ $sigRoleRight ?? 'Penanggung jawab usaha,' }}</div>
        <div class="doc-sig-line"></div>
        <div class="doc-sig-name">{{ $sigNameRight ?? 'Kopi Hiku Himu' }}</div>
        <div class="doc-sig-title">Jabatan: {{ $sigTitleRight ?? 'Pemilik Usaha' }}</div>
        <div class="doc-sig-date">Tanggal: {{ formatTglIndo(now()) }}</div>
    </div>
</div>
