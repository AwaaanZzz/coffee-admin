@extends('layouts.app')

@section('title', 'Activity Log')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Home</a>
    <i data-lucide="chevron-right"></i>
    <span>Activity Log</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Activity Log</h1>
            <p class="page-subtitle">Pantau aktivitas pengguna sistem</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-modern mb-4">
        <div class="card-body-modern filter-bar">
            <form method="GET" action="{{ route('activity-log.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3 filter-group">
                    <label class="form-label form-label-modern">Tanggal Mulai</label>
                    <input type="date" name="date_from" class="form-control form-control-modern" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 filter-group">
                    <label class="form-label form-label-modern">Tanggal Akhir</label>
                    <input type="date" name="date_to" class="form-control form-control-modern" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-accent w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="card-modern">
        <div class="card-body-modern">
            <div class="activity-timeline position-relative" style="padding-left: 30px;">
                <div class="position-absolute h-100" style="width: 2px; left: 14px; top: 0; background: var(--border-color);"></div>

                @forelse($logs as $log)
                    <div class="activity-item position-relative mb-4">
                        <div class="position-absolute border border-2 border-primary rounded-circle d-flex justify-content-center align-items-center" style="width: 30px; height: 30px; left: -30px; top: 0; z-index: 1; background: var(--card-bg);">
                            <i data-lucide="activity" class="text-primary" style="width: 14px; height: 14px;"></i>
                        </div>
                        <div class="activity-content p-3 rounded" style="margin-left: 20px; background: var(--bg-secondary);">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <span class="fw-bold">{{ $log->user->name ?? 'System' }}</span>
                                    <span class="text-muted">{{ $log->action }}</span>
                                </div>
                                <div class="text-end">
                                    <div class="activity-time text-primary fw-medium" style="font-size: 0.85rem;">{{ $log->created_at->diffForHumans() }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                            <p class="mb-0 text-muted" style="font-size: 0.9rem;">{{ $log->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i data-lucide="activity" style="width:48px;height:48px" class="text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada aktivitas tercatat.</h5>
                        <p class="text-muted">Aktivitas akan muncul setelah ada aksi di sistem.</p>
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
