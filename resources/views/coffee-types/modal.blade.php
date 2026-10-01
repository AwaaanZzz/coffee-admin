@extends('layouts.app')

@section('title', 'Modal & HPP Tiap Jenis Kopi')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span>/</span>
    <a href="{{ route('coffee-types.index') }}">Jenis Kopi</a>
    <span>/</span>
    <span>Modal & HPP Roastery</span>
@endsection

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="page-title m-0">Modal & HPP Tiap Jenis Kopi</h3>
        <p class="page-subtitle">Kelola harga pokok produksi untuk evaluasi margin roastery.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('exports.index') }}" class="btn btn-sm btn-outline-modern">
            Ekspor data
        </a>
        <a href="{{ route('coffee-types.index') }}" class="btn btn-sm btn-outline-modern">
            Kelola jenis kopi
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-modern">
            Dashboard
        </a>
    </div>
</div>

{{-- Top 4 KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="coffee"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Varian terdaftar</div>
                <div class="stat-value fs-5 tabular-nums">{{ $kpis['total_varian'] }} <span style="font-size:0.85rem; font-weight:normal;" class="text-muted">varian</span></div>
                <small class="text-muted" style="font-size:0.72rem;">{{ $kpis['configured_count'] }} varian telah diset</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="calculator"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Rata-rata modal (HPP)</div>
                <div class="stat-value fs-5 tabular-nums">Rp {{ number_format($kpis['avg_modal'], 0, ',', '.') }}</div>
                <small class="text-muted" style="font-size:0.72rem;">Biaya roastery per pack</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="tag"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Rata-rata harga jual</div>
                <div class="stat-value fs-5 tabular-nums">Rp {{ number_format($kpis['avg_price'], 0, ',', '.') }}</div>
                <small class="text-muted" style="font-size:0.72rem;">Harga titip jual mitra</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="percent"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Estimasi margin roastery</div>
                <div class="stat-value fs-5 tabular-nums {{ $kpis['avg_margin'] >= 30 ? 'text-success' : 'text-warning' }}">
                    {{ $kpis['avg_margin'] }}%
                </div>
                <small class="text-muted" style="font-size:0.72rem;">Margin laba kotor rata-rata</small>
            </div>
        </div>
    </div>
</div>

{{-- Main Form & Table Card --}}
<form action="{{ route('coffee-types.modal.update') }}" method="POST">
    @csrf
    <div class="card-modern">
        <div class="card-header-modern d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="card-title-modern m-0">Daftar Modal Pokok per Jenis Kopi</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-accent btn-sm px-3 shadow-sm">
                    Simpan Perubahan Modal
                </button>
            </div>
        </div>

        <div class="card-body-modern p-0">
            <div class="table-responsive">
                <table class="table-modern w-100 m-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 40px; padding-left: 20px;">#</th>
                            <th style="min-width: 180px;">Nama jenis kopi</th>
                            <th style="min-width: 110px;">Kategori</th>
                            <th style="min-width: 190px;" class="text-end">Modal roastery (HPP)</th>
                            <th style="min-width: 140px;" class="text-end">Harga jual mitra</th>
                            <th style="min-width: 140px;" class="text-end">Laba per pack</th>
                            <th style="min-width: 110px;" class="text-center">Margin</th>
                            <th style="min-width: 110px;" class="text-center">Terjual</th>
                            <th style="min-width: 150px; padding-right: 20px;" class="text-end">Beban HPP riil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coffeeTypes as $index => $coffee)
                            @php
                                $currentModal = (float) ($coffee->modal ?? 0);
                                $avgPrice = (float) $coffee->avg_price;
                                $labaUnit = $avgPrice > 0 ? ($avgPrice - $currentModal) : 0;
                                $marginPct = $avgPrice > 0 ? round((($avgPrice - $currentModal) / $avgPrice) * 100, 1) : 0;
                                $totalBeban = $currentModal * $coffee->total_laku;
                            @endphp
                            <tr id="row-coffee-{{ $coffee->id }}" data-price="{{ $avgPrice }}" data-sold="{{ $coffee->total_laku }}">
                                <td class="text-muted small" style="padding-left: 20px;">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $coffee->name }}
                                    </div>
                                    @if($coffee->stores_count > 0)
                                        <small class="text-muted" style="font-size:0.72rem;">
                                            Tersedia di {{ $coffee->stores_count }} toko mitra
                                        </small>
                                    @else
                                        <small class="text-muted" style="font-size:0.72rem;">
                                            Belum ditentukan harga di toko
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if(strtolower($coffee->category) === 'robusta')
                                        <span class="badge-robusta">Robusta</span>
                                    @elseif(strtolower($coffee->category) === 'arabika')
                                        <span class="badge-arabika">Arabika</span>
                                    @else
                                        <span class="badge-neutral">{{ ucfirst($coffee->category) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="input-group input-group-sm ms-auto" style="max-width: 175px;">
                                        <span class="input-group-text bg-light fw-bold text-muted border-end-0" style="font-size:0.8rem;">Rp</span>
                                        <input type="number" 
                                               name="modals[{{ $coffee->id }}]" 
                                               class="form-control form-control-sm text-end fw-bold font-monospace modal-input" 
                                               value="{{ $currentModal > 0 ? (int)$currentModal : '' }}" 
                                               placeholder="0"
                                               min="0" 
                                               step="100"
                                               data-id="{{ $coffee->id }}"
                                               style="font-size:0.92rem; color:var(--danger); background:var(--bg-input);">
                                    </div>
                                    @if($currentModal == 0)
                                        <small class="text-muted fst-italic d-block mt-1" style="font-size:0.68rem;">* Belum diset (default: 48%)</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($avgPrice > 0)
                                        <span class="fw-semibold text-dark font-monospace" style="font-size:0.9rem;">
                                            Rp {{ number_format($avgPrice, 0, ',', '.') }}
                                        </span>
                                        @if($coffee->min_price != $coffee->max_price)
                                            <div class="text-muted" style="font-size:0.68rem;">
                                                Rentang: {{ number_format($coffee->min_price / 1000, 0) }}k - {{ number_format($coffee->max_price / 1000, 0) }}k
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold font-monospace laba-unit-display {{ $labaUnit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.9rem;">
                                        Rp {{ number_format($labaUnit, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge margin-badge {{ $marginPct >= 30 ? 'bg-success' : ($marginPct > 0 ? 'bg-warning text-dark' : 'bg-secondary') }}" style="font-size:0.75rem; padding: 4px 8px;">
                                        {{ $marginPct }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold {{ $coffee->total_laku > 0 ? 'text-dark' : 'text-muted' }}">
                                        {{ number_format($coffee->total_laku) }} <small style="font-size:0.7rem;">pcs</small>
                                    </span>
                                </td>
                                <td class="text-end" style="padding-right: 20px;">
                                    <span class="fw-bold font-monospace total-beban-display text-danger" style="font-size:0.9rem;">
                                        Rp {{ number_format($totalBeban, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    Belum ada varian jenis kopi terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Card Footer with Actions & Explanatory Notes --}}
            <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-3 bg-light" style="border-bottom-left-radius: var(--radius); border-bottom-right-radius: var(--radius);">
                <div class="text-muted small">
                    <span>Data modal yang Anda simpan akan seketika mengubah nilai <strong>Total Pengeluaran (Beban HPP Pokok)</strong> dan <strong>Laba Bersih</strong> pada laporan keuangan Dashboard.</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="reset" class="btn btn-sm btn-outline-modern">
                        Reset
                    </button>
                    <button type="submit" class="btn btn-sm btn-outline-modern">
                        Simpan perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();

        // Real-time calculation on input changes
        document.querySelectorAll('.modal-input').forEach(function(input) {
            input.addEventListener('input', function() {
                const coffeeId = this.dataset.id;
                const row = document.getElementById('row-coffee-{{ $coffee->id ?? "" }}') || this.closest('tr');
                if (!row) return;

                const price = parseFloat(row.dataset.price) || 0;
                const sold = parseInt(row.dataset.sold) || 0;
                const modal = parseFloat(this.value) || 0;

                const laba = price > 0 ? (price - modal) : 0;
                const margin = price > 0 ? ((price - modal) / price * 100) : 0;
                const totalBeban = modal * sold;

                // Update Laba Unit
                const labaEl = row.querySelector('.laba-unit-display');
                if (labaEl) {
                    labaEl.innerText = 'Rp ' + Math.round(laba).toLocaleString('id-ID');
                    labaEl.className = 'fw-bold font-monospace laba-unit-display ' + (laba >= 0 ? 'text-success' : 'text-danger');
                }

                // Update Margin Badge
                const marginEl = row.querySelector('.margin-badge');
                if (marginEl) {
                    marginEl.innerText = margin.toFixed(1) + '%';
                    marginEl.className = 'badge margin-badge ' + (margin >= 30 ? 'bg-success' : (margin > 0 ? 'bg-warning text-dark' : 'bg-secondary'));
                }

                // Update Total Beban
                const bebanEl = row.querySelector('.total-beban-display');
                if (bebanEl) {
                    bebanEl.innerText = 'Rp ' + Math.round(totalBeban).toLocaleString('id-ID');
                }
            });
        });
    });
</script>
@endsection
