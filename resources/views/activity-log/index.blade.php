@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <i data-lucide="chevron-right"></i>
    <span>Log aktivitas</span>
@endsection

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title m-0">Log aktivitas</h1>
            <p class="page-subtitle mt-1 mb-0">Pantau riwayat aktivitas operasional pengguna sistem</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-modern mb-4">
        <div class="card-body-modern p-4">
            <form method="GET" action="{{ route('activity-log.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label form-label-modern">Tanggal mulai</label>
                    <input type="date" name="date_from" class="form-control form-control-modern" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-modern">Tanggal akhir</label>
                    <input type="date" name="date_to" class="form-control form-control-modern" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-accent flex-grow-1">Terapkan filter</button>
                    @if(request('date_from') || request('date_to'))
                        <a href="{{ route('activity-log.index') }}" class="btn btn-outline-modern">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="card-modern">
        <div class="card-body-modern p-4">
            <div class="activity-timeline position-relative" style="padding-left: 28px;">
                <div class="position-absolute h-100" style="width: 2px; left: 11px; top: 8px; background: var(--border-color, #e2e8f0);"></div>

                @forelse($logs as $log)
                    <div class="activity-item position-relative mb-4">
                        <div class="position-absolute rounded-circle d-flex justify-content-center align-items-center" style="width: 24px; height: 24px; left: -28px; top: 2px; z-index: 1; background: var(--card-bg, #ffffff); border: 2px solid var(--primary, #1E3A5F);">
                            <div style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary, #1E3A5F);"></div>
                        </div>
                        <div class="activity-content p-3" style="margin-left: 12px; background: var(--bg-secondary, #faf7f2); border: 1px solid var(--border-color, #e2e8f0); border-radius: var(--radius-sm, 8px);">
                            <div class="d-flex justify-content-between align-items-start mb-1 flex-wrap gap-2">
                                <div>
                                    <span class="fw-semibold text-main">{{ $log->user->name ?? 'Sistem' }}</span>
                                    <span class="text-muted ms-1" style="font-size: 0.8125rem;">{{ $log->action }}</span>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted fw-medium" style="font-size: 0.8125rem; font-variant-numeric: tabular-nums;">{{ $log->created_at->diffForHumans() }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem; font-variant-numeric: tabular-nums;">{{ $log->created_at->format('d/m/Y, H:i') }}</div>
                                </div>
                            </div>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">{{ $log->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i data-lucide="activity" style="width: 40px; height: 40px; opacity: 0.35;" class="text-muted mb-2"></i>
                        <h6 class="fw-semibold text-main">Belum ada aktivitas tercatat</h6>
                        <p class="text-muted small mb-0">Aktivitas akan otomatis muncul setelah ada aksi operasional di sistem.</p>
                    </div>
                @endforelse
            </div>

            @if($logs->hasPages())
            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection
