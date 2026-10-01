@extends('layouts.app')

@section('title', 'Pusat Notifikasi & Alert Operasional')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <i data-lucide="chevron-right"></i>
    <span>Notifikasi</span>
@endsection

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="page-title m-0">Pusat Notifikasi & Alert Operasional</h1>
        <p class="page-subtitle mt-1 mb-0">Peringatan otomatis stok kadaluarsa, stok kritis, dan status inventaris.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if($unreadCount > 0)
        <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="btnMarkAllReadPage">
            <i data-lucide="check-check" style="width: 14px; height: 14px; display:inline-block; vertical-align:-1px;"></i>
            Tandai Semua Dibaca
        </button>
        @endif
        @if($totalCount > 0)
        <form action="{{ route('notifications.clear-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan semua riwayat notifikasi?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                <i data-lucide="trash-2" style="width: 14px; height: 14px; display:inline-block; vertical-align:-1px;"></i>
                Bersihkan Riwayat
            </button>
        </form>
        @endif
    </div>
</div>

<div class="card-modern shadow-sm">
    <div class="card-header-modern d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('notifications.index') }}" class="btn btn-sm {{ !request('filter') ? 'btn-primary' : 'btn-outline-secondary' }} py-1 px-3" style="border-radius: 20px; font-size: 0.78rem;">
                Semua ({{ $totalCount }})
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="btn btn-sm {{ request('filter') === 'unread' ? 'btn-primary' : 'btn-outline-secondary' }} py-1 px-3" style="border-radius: 20px; font-size: 0.78rem;">
                Belum Dibaca ({{ $unreadCount }})
            </a>
        </div>
        <span class="badge bg-light text-muted border font-monospace">{{ $notifications->total() }} Menampilkan</span>
    </div>

    <div class="card-body-modern p-0">
        <div class="list-group list-group-flush" id="notificationList">
            @forelse($notifications as $item)
                <div class="list-group-item p-3 d-flex justify-content-between align-items-start gap-3 border-bottom {{ $item->is_read ? 'bg-white opacity-75' : 'bg-light' }}" id="notif-row-{{ $item->id }}">
                    <div class="d-flex flex-column gap-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge {{ $item->type === 'danger' ? 'bg-danger text-white' : ($item->type === 'warning' ? 'bg-warning text-dark' : 'bg-info text-white') }}" style="font-size:0.68rem; font-weight:700;">
                                {{ strtoupper($item->type) }}
                            </span>
                            <span class="fw-bold text-dark">{{ $item->title }}</span>
                            @if(!$item->is_read)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size:0.65rem;" id="badge-baru-{{ $item->id }}">Baru</span>
                            @endif
                        </div>
                        <p class="text-secondary small mb-1">{{ $item->message }}</p>
                        <small class="text-muted font-monospace" style="font-size:0.72rem;">{{ $item->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <a href="{{ route('notifications.go', $item->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2.5" style="font-size:0.75rem;">
                            Buka Detail &rarr;
                        </a>
                        @if(!$item->is_read)
                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 text-muted btn-mark-read" data-url="{{ route('notifications.read', $item->id) }}" data-id="{{ $item->id }}" style="font-size:0.75rem;" title="Tandai sudah dibaca">
                                &check; Dibaca
                            </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-light border text-danger py-1 px-2 btn-delete-notif" data-url="{{ route('notifications.destroy', $item->id) }}" data-id="{{ $item->id }}" title="Hapus notifikasi ini">
                            <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i data-lucide="bell-off" style="width: 44px; height: 44px; opacity: 0.35;" class="mb-2"></i>
                    <h6>Tidak ada notifikasi</h6>
                    <p class="small mb-0">Semua operasional toko dan stok roastery berjalan aman.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="p-3 border-top">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Mark Single Read
        document.querySelectorAll('.btn-mark-read').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const id = this.getAttribute('data-id');
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                }).then(r => r.json()).then(res => {
                    if (res.success) {
                        const row = document.getElementById(`notif-row-${id}`);
                        if (row) {
                            row.classList.remove('bg-light');
                            row.classList.add('bg-white', 'opacity-75');
                            const badgeBaru = document.getElementById(`badge-baru-${id}`);
                            if (badgeBaru) badgeBaru.remove();
                            this.remove();
                        }
                    }
                }).catch(e => console.error(e));
            });
        });

        // Delete Single Notification
        document.querySelectorAll('.btn-delete-notif').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Hapus notifikasi ini?')) return;
                const url = this.getAttribute('data-url');
                const id = this.getAttribute('data-id');
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                }).then(r => r.json()).then(res => {
                    if (res.success) {
                        const row = document.getElementById(`notif-row-${id}`);
                        if (row) {
                            row.style.transition = 'opacity 0.25s, transform 0.25s';
                            row.style.opacity = '0';
                            row.style.transform = 'translateY(-6px)';
                            setTimeout(() => row.remove(), 250);
                        }
                    }
                }).catch(e => console.error(e));
            });
        });

        // Mark All Read
        const btnMarkAll = document.getElementById('btnMarkAllReadPage');
        if (btnMarkAll) {
            btnMarkAll.addEventListener('click', function() {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                }).then(r => r.json()).then(res => {
                    if (res.success) {
                        window.location.reload();
                    }
                }).catch(e => console.error(e));
            });
        }
    });
</script>
@endsection
