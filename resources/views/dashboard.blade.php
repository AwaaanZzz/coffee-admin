@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Home</a>
    <i data-lucide="chevron-right"></i>
    <span>Dashboard</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="greeting-text">{{ $greeting ?? 'Selamat Datang' }}, {{ auth()->user()->name ?? 'Admin' }}!</h1>
            <p class="greeting-subtitle">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-outline-modern" onclick="window.location.reload()"><i data-lucide="refresh-cw"></i> Refresh</button>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon bg-primary"><i data-lucide="store"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ $totalToko ?? 0 }}</div>
                    <div class="stat-label">Total Toko</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon bg-info"><i data-lucide="package"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ $totalStock ?? 0 }}</div>
                    <div class="stat-label">Total Stock</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon bg-success"><i data-lucide="shopping-cart"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ $totalLaku ?? 0 }}</div>
                    <div class="stat-label">Total Laku</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon bg-danger"><i data-lucide="alert-triangle"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ $expiringSoon ?? 0 }}</div>
                    <div class="stat-label">Mendekati Exp</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon bg-purple"><i data-lucide="wallet"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="font-size:0.95rem">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon bg-warning"><i data-lucide="{{ ($revenueGrowth ?? 0) >= 0 ? 'trending-up' : 'trending-down' }}"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($revenueGrowth ?? 0, 0) }}%</div>
                    <div class="stat-label">Revenue Growth</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern d-flex justify-content-between align-items-center">
                    <h5 class="card-title-modern m-0">Trend Pendapatan (30 Hari)</h5>
                </div>
                <div class="card-body-modern">
                    <canvas id="revenueChart" height="280"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">Top 5 Kopi Terlaris</h5>
                </div>
                <div class="card-body-modern">
                    <canvas id="topCoffeeChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">Penjualan per Toko</h5>
                </div>
                <div class="card-body-modern">
                    <canvas id="salesPerStoreChart" height="280"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">✅ Tugas Harian</h5>
                </div>
                <div class="card-body-modern">
                    <form id="todoForm" class="mb-3 d-flex gap-2">
                        <input type="text" id="newTodo" class="form-control form-control-modern" placeholder="Tambah tugas baru...">
                        <button type="submit" class="btn btn-accent btn-sm"><i data-lucide="plus"></i></button>
                    </form>
                    <ul class="list-group list-group-flush" id="todoList">
                        @forelse($todos ?? [] as $todo)
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 border-bottom">
                                <div class="form-check">
                                    <input class="form-check-input todo-checkbox" type="checkbox" id="todo{{ $todo->id }}" data-id="{{ $todo->id }}" {{ $todo->is_completed ? 'checked' : '' }}>
                                    <label class="form-check-label {{ $todo->is_completed ? 'text-decoration-line-through text-muted' : '' }}" for="todo{{ $todo->id }}">
                                        {{ $todo->title }}
                                    </label>
                                </div>
                                <button class="btn btn-sm text-danger delete-todo" data-id="{{ $todo->id }}"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button>
                            </li>
                        @empty
                            <li class="list-group-item bg-transparent px-0 text-muted text-center py-3" id="todoEmpty">Tidak ada tugas.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Profit/Loss Table --}}
    <div class="card-modern mb-4">
        <div class="card-header-modern">
            <h5 class="card-title-modern m-0">💰 Ringkasan Keuntungan & Kerugian</h5>
        </div>
        <div class="card-body-modern p-0">
            <div class="table-responsive">
                <table class="table-modern w-100 m-0">
                    <thead>
                        <tr>
                            <th>Toko</th>
                            <th>Periode</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th>Laba/Rugi</th>
                            <th>Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profitLossData ?? [] as $report)
                            <tr>
                                <td class="fw-bold">{{ $report->store->name ?? '-' }}</td>
                                <td>{{ $report->periode ?? '-' }}</td>
                                <td>Rp {{ number_format($report->pemasukan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($report->pengeluaran, 0, ',', '.') }}</td>
                                <td class="{{ $report->laba >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                    Rp {{ number_format($report->laba, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge-modern {{ $report->margin >= 20 ? 'badge-success' : ($report->margin > 0 ? 'badge-warning' : 'badge-danger') }}">
                                        {{ number_format($report->margin, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data laporan keuangan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Sales + Stock Forecasting --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">🛒 Penjualan Terakhir</h5>
                </div>
                <div class="card-body-modern p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentSales ?? [] as $sale)
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-bottom p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-primary" style="width:36px;height:36px;border-radius:10px;min-width:36px">
                                        <i data-lucide="coffee" style="width:16px;height:16px"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size:0.9rem">{{ $sale->coffeeType->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $sale->store->name ?? '-' }} &bull; {{ \Carbon\Carbon::parse($sale->tanggal)->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success" style="font-size:0.9rem">+Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
                                    <small class="text-muted">{{ $sale->jumlah }} unit</small>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item bg-transparent text-center text-muted py-4">Belum ada penjualan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-modern" style="height:100%">
                <div class="card-header-modern">
                    <h5 class="card-title-modern m-0">📦 Estimasi Stok Habis</h5>
                </div>
                <div class="card-body-modern p-0">
                    <div class="table-responsive">
                        <table class="table-modern w-100 m-0">
                            <thead>
                                <tr>
                                    <th>Kopi</th>
                                    <th>Sisa</th>
                                    <th>Rata-rata/Hari</th>
                                    <th>Estimasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($stockForecasting ?? collect())->take(8) as $batch)
                                    <tr>
                                        <td>{{ $batch->coffeeType->name ?? '-' }}</td>
                                        <td>{{ $batch->sisa }}</td>
                                        <td>{{ number_format($batch->avg_daily_sales, 1) }}</td>
                                        <td>
                                            @if($batch->days_until_empty <= 3)
                                                <span class="badge-modern badge-danger">{{ $batch->days_until_empty }} Hari ⚠️</span>
                                            @elseif($batch->days_until_empty <= 7)
                                                <span class="badge-modern badge-warning">{{ $batch->days_until_empty }} Hari</span>
                                            @elseif($batch->days_until_empty >= 999)
                                                <span class="badge-modern badge-success">Aman</span>
                                            @else
                                                <span class="badge-modern badge-success">{{ $batch->days_until_empty }} Hari</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Tidak ada data stok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? '#334155' : '#e2e8f0';

    const chartColors = {
        primary: '#1E3A5F', primaryLight: 'rgba(30,58,95,0.15)',
        success: '#4A7C59',
        info: '#2E86AB',
        palette: ['#1E3A5F','#C88A4E','#4A7C59','#2E86AB','#A67261','#6C4F82','#C0392B','#D4A017']
    };

    // Revenue Chart
    const ctxRevenue = document.getElementById('revenueChart');
    if (ctxRevenue) {
        const ctx = ctxRevenue.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, chartColors.primaryLight);
        gradient.addColorStop(1, 'rgba(30,58,95,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels ?? []) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($chartValues ?? []) !!},
                    borderColor: chartColors.primary,
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: chartColors.primary,
                    pointRadius: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { grid: { color: gridColor }, beginAtZero: true } }
            }
        });
    }

    // Top Coffee Doughnut
    const ctxCoffee = document.getElementById('topCoffeeChart');
    if (ctxCoffee) {
        const topLabels = [];
        const topData = [];
        @foreach(($topProducts ?? []) as $p)
            topLabels.push({!! json_encode($p->coffeeType->name ?? 'Unknown') !!});
            topData.push({{ $p->total_qty ?? 0 }});
        @endforeach

        new Chart(ctxCoffee, {
            type: 'doughnut',
            data: {
                labels: topLabels.length ? topLabels : ['No data'],
                datasets: [{
                    data: topData.length ? topData : [1],
                    backgroundColor: chartColors.palette,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { padding: 15 } } },
                cutout: '65%'
            }
        });
    }

    // Sales per Store Bar
    const ctxStore = document.getElementById('salesPerStoreChart');
    if (ctxStore) {
        const storeLabels = [];
        const storeData = [];
        @foreach(($salesByStore ?? []) as $s)
            storeLabels.push({!! json_encode($s->store->name ?? 'Unknown') !!});
            storeData.push({{ $s->total_sales ?? 0 }});
        @endforeach

        new Chart(ctxStore, {
            type: 'bar',
            data: {
                labels: storeLabels.length ? storeLabels : ['No data'],
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: storeData.length ? storeData : [0],
                    backgroundColor: chartColors.info,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { grid: { color: gridColor }, beginAtZero: true } }
            }
        });
    }

    // Todo AJAX
    const todoForm = document.getElementById('todoForm');
    const newTodo = document.getElementById('newTodo');
    const todoList = document.getElementById('todoList');

    if (todoList) {
        todoList.addEventListener('change', function(e) {
            if (e.target.classList.contains('todo-checkbox')) {
                const id = e.target.dataset.id;
                fetch(`/todos/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ is_completed: e.target.checked })
                }).then(res => {
                    if (res.ok) {
                        const label = e.target.nextElementSibling;
                        e.target.checked ? label.classList.add('text-decoration-line-through','text-muted') : label.classList.remove('text-decoration-line-through','text-muted');
                    }
                });
            }
        });

        todoList.addEventListener('click', function(e) {
            const btn = e.target.closest('.delete-todo');
            if (btn) {
                fetch(`/todos/${btn.dataset.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(res => { if (res.ok) btn.closest('li').remove(); });
            }
        });
    }

    if (todoForm) {
        todoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const title = newTodo.value.trim();
            if (!title) return;
            fetch('/todos', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ title: title })
            }).then(r => r.json()).then(data => {
                if (data.todo) {
                    const empty = document.getElementById('todoEmpty');
                    if (empty) empty.remove();
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 border-bottom';
                    li.innerHTML = `<div class="form-check"><input class="form-check-input todo-checkbox" type="checkbox" id="todo${data.todo.id}" data-id="${data.todo.id}"><label class="form-check-label" for="todo${data.todo.id}">${data.todo.title}</label></div><button class="btn btn-sm text-danger delete-todo" data-id="${data.todo.id}"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button>`;
                    todoList.prepend(li);
                    lucide.createIcons();
                    newTodo.value = '';
                }
            });
        });
    }
});
</script>
@endsection
