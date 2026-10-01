@extends('layouts.app')

@section('title', 'Notifikasi')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <i data-lucide="chevron-right"></i>
    <span>Notifikasi</span>
@endsection

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="page-title m-0">Notifikasi</h1>
        <p class="page-subtitle mt-1 mb-0">Peringatan otomatis stok kedaluwarsa, stok kritis, dan status inventaris.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if($unreadCount > 0)
        <button type="button" class="btn btn-sm btn-outline-modern fw-semibold" id="btnMarkAllReadPage">
            <i data-lucide="check-check" style="width: 14px; height: 14px; display:inline-block; vertical-align:-1px;"></i>
            Tandai semua dibaca
        </button>
        @endif
        @if($totalCount > 0)
        <form action="{{ route('notifications.clear-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan semua riwayat notifikasi?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                <i data-lucide="trash-2" style="width: 14px; height: 14px; display:inline-block; vertical-align:-1px;"></i>
                Bersihkan riwayat
            </button>
        </form>
        @endif
    </div>
</div>

<div class="card-modern">
    <div class="card-header-modern d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('notifications.index') }}" class="btn btn-sm {{ !request('filter') ? 'btn-accent' : 'btn-outline-modern' }} py-1 px-3" style="border-radius: var(--radius-sm, 8px); font-size: 0.8125rem;">
                Semua (<span style="font-variant-numeric: tabular-nums;">{{ $totalCount }}</span>)
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="btn btn-sm {{ request('filter') === 'unread' ? 'btn-accent' : 'btn-outline-modern' }} py-1 px-3" style="border-radius: var(--radius-sm, 8px); font-size: 0.8125rem;">
                Belum dibaca (<span style="font-variant-numeric: tabular-nums;">{{ $unreadCount }}</span>)
            </a>
        </div>
        <span class="badge badge-subtle-secondary" style="font-variant-numeric: tabular-nums;">Menampilkan {{ $notifications->total() }}</span>
    </div>

    <div class="card-body-modern p-0">
        <div class="list-group list-group-flush" id="notificationList">
            @forelse($notifications as $item)
                <div class="list-group-item p-3 d-flex justify-content-between align-items-start gap-3 border-bottom {{ $item->is_read ? 'opacity-75' : '' }}" id="notif-row-{{ $item->id }}" style="background: {{ $item->is_read ? 'var(--card-bg, #ffffff)' : 'var(--bg-secondary, #faf7f2)' }};">
                    <div class="d-flex flex-column gap-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($item->type === 'danger')
                                <span class="badge badge-subtle-danger">Kritis</span>
                            @elseif($item->type === 'warning')
                                <span class="badge badge-subtle-warning">Peringatan</span>
                            @else
                                <span class="badge badge-subtle-secondary">Informasi</span>
                            @endif
                            <span class="fw-semibold text-main">{{ $item->title }}</span>
                            @if(!$item->is_read)
                                <span class="badge badge-subtle-accent" id="badge-baru-{{ $item->id }}">Baru</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-1">{{ $item->message }}</p>
                        <small class="text-muted" style="font-size:0.75rem; font-variant-numeric: tabular-nums;">{{ $item->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <a href="{{ route('notifications.go', $item->id) }}" class="btn btn-sm btn-outline-modern py-1 px-2.5" style="font-size:0.8125rem;">
                            Buka detail
                        </a>
                        @if(!$item->is_read)
                            <button type="button" class="btn btn-sm btn-outline-modern py-1 px-2 text-muted btn-mark-read" data-url="{{ route('notifications.read', $item->id) }}" data-id="{{ $item->id }}" style="font-size:0.8125rem;" title="Tandai sudah dibaca">
                                &check; Dibaca
                            </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-modern text-danger py-1 px-2 btn-delete-notif" data-url="{{ route('notifications.destroy', $item->id) }}" data-id="{{ $item->id }}" title="Hapus notifikasi ini">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i data-lucide="bell-off" style="width: 40px; height: 40px; opacity: 0.35;" class="mb-2"></i>
                    <h6 class="fw-semibold text-main">Tidak ada notifikasi</h6>
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
                            row.style.background = 'var(--card-bg, #ffffff)';
                            row.classList.add('opacity-75');
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
                            row.style.opacity = '0';
                            row.remove();
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
