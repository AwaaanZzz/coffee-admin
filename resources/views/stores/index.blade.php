@extends('layouts.app')
@section('title', 'Daftar Toko')

@section('breadcrumbs')
    <div class="breadcrumbs">
        <a href="{{ route('stores.index') }}">Beranda</a>
        <i data-lucide="chevron-right"></i>
        <span>Daftar Toko</span>
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">Daftar Toko</h3>
            <p class="page-subtitle">Kelola semua data toko mitra kerja sama.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('stores.create') }}" class="btn btn-accent">
                <i data-lucide="plus"></i> Tambah Toko
            </a>
        </div>
    </div>

    <div class="card-modern">
        <div class="card-body-modern">
            <div class="table-responsive">
                <table class="table-modern w-100">
                    <thead>
                        <tr>
                            <th style="width: 30px;"></th>
                            <th>#</th>
                            <th>Nama Toko</th>
                            <th>Alamat</th>
                            <th>Penanggung Jawab</th>
                            <th>Tgl Kerjasama</th>
                            <th>Stock</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stores as $store)
                            <tr class="store-row" data-store-id="{{ $store->id }}" style="cursor: pointer;" onclick="toggleStock({{ $store->id }})">
                                <td>
                                    <i data-lucide="chevron-right" class="stock-chevron" id="chevron-{{ $store->id }}" style="width:16px;height:16px;transition:transform 0.2s;color:var(--text-muted);"></i>
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $store->name }}</td>
                                <td>{{ $store->alamat ?? '-' }}</td>
                                <td>{{ $store->penanggung_jawab ?? '-' }}</td>
                                <td>{{ $store->tgl_kerjasama->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge-modern bg-info-subtle text-info" style="font-size:0.75rem;padding:3px 10px;border-radius:20px;">
                                        {{ $store->stockBatches->count() }} batch
                                    </span>
                                </td>
                                <td class="text-end" onclick="event.stopPropagation();">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('stores.show', $store) }}" class="btn btn-table-action text-info" title="Detail">
                                            <i data-lucide="eye"></i>
                                        </a>
                                        <a href="{{ route('stores.edit', $store) }}" class="btn btn-table-action text-warning" title="Edit">
                                            <i data-lucide="edit"></i>
                                        </a>
                                        <a href="{{ route('stores.prices.edit', $store) }}" class="btn btn-table-action text-success" title="Atur Harga">
                                            <i data-lucide="tag"></i>
                                        </a>
                                        <form action="{{ route('stores.destroy', $store) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus toko ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-table-action text-danger" title="Hapus">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {{-- Expandable Stock Detail Row --}}
                            <tr class="stock-detail-row" id="stock-row-{{ $store->id }}" style="display:none;">
                                <td colspan="8" style="padding:0;border-top:none;">
                                    <div class="stock-detail-wrapper" style="background:var(--bg-primary);border-top:2px solid var(--accent);padding:16px 20px;margin:0;">
                                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                                            <i data-lucide="package" style="width:18px;height:18px;color:var(--accent);"></i>
                                            <span style="font-weight:700;color:var(--text-primary);font-size:0.9rem;">Stock — {{ $store->name }}</span>
                                            <span style="font-size:0.8rem;color:var(--text-muted);margin-left:auto;">{{ $store->stockBatches->count() }} batch ditemukan</span>
                                        </div>
                                        @if($store->stockBatches->count() > 0)
                                            <div class="table-responsive" style="border-radius:10px;overflow:hidden;border:1px solid var(--border);">
                                                <table class="table-modern w-100" style="margin:0;font-size:0.82rem;">
                                                    <thead>
                                                        <tr style="background:var(--bg-card);">
                                                            <th style="padding:10px 12px;">Kode Produksi</th>
                                                            <th style="padding:10px 12px;">Kopi</th>
                                                            <th style="padding:10px 12px;">Tgl Stock</th>
                                                            <th style="padding:10px 12px;">Tgl Exp</th>
                                                            <th style="padding:10px 12px;" class="text-center">Stock</th>
                                                            <th style="padding:10px 12px;" class="text-center">Laku</th>
                                                            <th style="padding:10px 12px;" class="text-center">Sisa</th>
                                                            <th style="padding:10px 12px;" class="text-end">Total (Rp)</th>
                                                            <th style="padding:10px 12px;" class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($store->stockBatches->sortByDesc('tgl_stock') as $batch)
                                                            <tr style="background:var(--bg-card);{{ $batch->isExpired ? 'opacity:0.5;' : ($batch->isExpiringSoon ? 'background:var(--danger-light);' : '') }}">
                                                                <td style="padding:8px 12px;font-weight:600;">{{ $batch->kode_produksi }}</td>
                                                                <td style="padding:8px 12px;">{{ $batch->coffeeType->name ?? '-' }}</td>
                                                                <td style="padding:8px 12px;">{{ $batch->tgl_stock->format('d/m/Y') }}</td>
                                                                <td style="padding:8px 12px;">
                                                                    <span style="{{ $batch->isExpiringSoon ? 'color:var(--danger);font-weight:600;' : '' }}">
                                                                        {{ $batch->tgl_exp->format('d/m/Y') }}
                                                                    </span>
                                                                    @if($batch->isExpiringSoon)
                                                                        <i data-lucide="alert-triangle" style="width:13px;height:13px;color:var(--danger);margin-left:4px;"></i>
                                                                    @endif
                                                                </td>
                                                                <td style="padding:8px 12px;" class="text-center">{{ $batch->jumlah_stock }}</td>
                                                                <td style="padding:8px 12px;" class="text-center">{{ $batch->laku }}</td>
                                                                <td style="padding:8px 12px;font-weight:600;" class="text-center">{{ $batch->sisa }}</td>
                                                                <td style="padding:8px 12px;" class="text-end">Rp {{ number_format($batch->total, 0, ',', '.') }}</td>
                                                                <td style="padding:8px 12px;" class="text-center">
                                                                    @php
                                                                        $statusColor = match($batch->status) {
                                                                            'normal' => 'background:var(--success-light);color:var(--success);',
                                                                            'tarik' => 'background:var(--warning-light);color:var(--warning);',
                                                                            'ganti' => 'background:var(--info-light);color:var(--info);',
                                                                            default => 'background:var(--accent-light);color:var(--accent);',
                                                                        };
                                                                    @endphp
                                                                    <span style="{{ $statusColor }}font-size:0.72rem;padding:3px 10px;border-radius:20px;font-weight:600;text-transform:capitalize;">
                                                                        {{ $batch->status }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div style="text-align:center;padding:24px;color:var(--text-muted);font-size:0.85rem;">
                                                <i data-lucide="inbox" style="width:32px;height:32px;margin-bottom:8px;opacity:0.5;"></i>
                                                <p style="margin:0;">Belum ada stock untuk toko ini.</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state text-center py-5">
                                        <i data-lucide="store" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                        <h5>Belum ada toko.</h5>
                                        <p class="text-muted">Tambahkan toko baru untuk mulai mengelola kemitraan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $stores->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();
    });

    function toggleStock(storeId) {
        const row = document.getElementById('stock-row-' + storeId);
        const chevron = document.getElementById('chevron-' + storeId);
        
        if (row.style.display === 'none') {
            // Close all other open rows first
            document.querySelectorAll('.stock-detail-row').forEach(r => {
                r.style.display = 'none';
            });
            document.querySelectorAll('.stock-chevron').forEach(c => {
                c.style.transform = 'rotate(0deg)';
            });
            
            // Open this row
            row.style.display = 'table-row';
            chevron.style.transform = 'rotate(90deg)';
            
            // Re-initialize lucide icons for the expanded content
            lucide.createIcons();
        } else {
            row.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    }
</script>
@endsection
