@extends('layouts.app')

@section('title', 'Kalender')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Home</a>
    <i data-lucide="chevron-right"></i>
    <span>Kalender</span>
@endsection

@section('content')
<div class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Kalender</h1>
            <p class="page-subtitle">Jadwal stok, keuangan, dan penjualan</p>
        </div>
        <div class="page-actions d-flex align-items-center gap-3">
            <button class="btn btn-outline-modern" id="prevMonth"><i data-lucide="chevron-left"></i></button>
            <h4 class="m-0 fw-bold" id="monthYearTitle">Bulan Tahun</h4>
            <button class="btn btn-outline-modern" id="nextMonth"><i data-lucide="chevron-right"></i></button>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-body card-body-modern p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 calendar-table" style="table-layout: fixed; min-width: 800px;">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center py-3">Minggu</th>
                            <th class="text-center py-3">Senin</th>
                            <th class="text-center py-3">Selasa</th>
                            <th class="text-center py-3">Rabu</th>
                            <th class="text-center py-3">Kamis</th>
                            <th class="text-center py-3">Jumat</th>
                            <th class="text-center py-3">Sabtu</th>
                        </tr>
                    </thead>
                    <tbody id="calendarBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title" id="eventModalLabel">Detail Kegiatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="eventModalBody">
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<style>
    .calendar-table td { height: 120px; vertical-align: top; padding: 10px; }
    .calendar-day-number { font-weight: 600; margin-bottom: 5px; text-align: right; color: var(--text-secondary); }
    .calendar-day.today .calendar-day-number { color: var(--accent); font-weight: 800; font-size: 1.1rem; }
    .calendar-day.today { background-color: var(--accent-light); }
    .calendar-event { 
        padding: 4px 8px; margin-bottom: 4px; border-radius: 4px; font-size: 0.8rem; cursor: pointer;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .event-danger { background-color: var(--danger-light); color: var(--danger); border-left: 3px solid var(--danger); }
    .event-info { background-color: var(--info-light); color: var(--info); border-left: 3px solid var(--info); }
    .event-success { background-color: var(--success-light); color: var(--success); border-left: 3px solid var(--success); }
    .calendar-day.other-month { opacity: 0.4; background-color: #f8fafc; }
    [data-theme="dark"] .calendar-day.other-month { background-color: #0f172a; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    let currentDate = new Date();
    
    function renderCalendar(date) {
        const month = date.getMonth();
        const year = date.getFullYear();
        
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        document.getElementById('monthYearTitle').textContent = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();
        
        const calendarBody = document.getElementById('calendarBody');
        calendarBody.innerHTML = '';
        
        let dateCount = 1;
        let nextMonthCount = 1;
        
        fetch(`/calendar/events?month=${month + 1}&year=${year}`)
            .then(res => res.json())
            .then(events => { buildGrid(events || {}); })
            .catch(() => {
                const mockEvents = {};
                const today = new Date();
                if(today.getMonth() === month && today.getFullYear() === year) {
                    const todayStr = `${year}-${String(month+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
                    mockEvents[todayStr] = [
                        {title: 'Stok Kopi Arabica hampir habis', type: 'danger', description: 'Sisa stok 5 kg, segera restock.'},
                        {title: 'Rekap Keuangan Mingguan', type: 'success', description: 'Laba minggu ini naik 10%'}
                    ];
                }
                buildGrid(mockEvents);
            });

        function buildGrid(events) {
            const today = new Date();
            for (let i = 0; i < 6; i++) {
                let row = document.createElement('tr');
                for (let j = 0; j < 7; j++) {
                    let cell = document.createElement('td');
                    let cellDate;
                    let isOtherMonth = false;
                    
                    if (i === 0 && j < firstDay) {
                        cellDate = prevMonthDays - firstDay + j + 1;
                        isOtherMonth = true;
                    } else if (dateCount > daysInMonth) {
                        cellDate = nextMonthCount++;
                        isOtherMonth = true;
                    } else {
                        cellDate = dateCount++;
                    }
                    
                    cell.className = `calendar-day ${isOtherMonth ? 'other-month' : ''}`;
                    const isToday = !isOtherMonth && cellDate === today.getDate() && month === today.getMonth() && year === today.getFullYear();
                    if(isToday) cell.classList.add('today');
                    
                    let cellContent = `<div class="calendar-day-number">${cellDate}</div>`;
                    
                    if (!isOtherMonth) {
                        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(cellDate).padStart(2, '0')}`;
                        if (events[dateString]) {
                            events[dateString].forEach(event => {
                                cellContent += `<div class="calendar-event event-${event.type}" data-title="${event.title}" data-desc="${event.description}" data-type="${event.type}">${event.title}</div>`;
                            });
                        }
                    }
                    
                    cell.innerHTML = cellContent;
                    row.appendChild(cell);
                }
                calendarBody.appendChild(row);
                if (dateCount > daysInMonth && i >= 4) break;
            }
            
            document.querySelectorAll('.calendar-event').forEach(el => {
                el.addEventListener('click', function() {
                    const title = this.getAttribute('data-title');
                    const desc = this.getAttribute('data-desc');
                    const type = this.getAttribute('data-type');
                    document.getElementById('eventModalLabel').textContent = title;
                    document.getElementById('eventModalBody').innerHTML = `<div class="alert alert-${type === 'danger' ? 'danger' : (type === 'success' ? 'success' : 'info')}">${desc}</div>`;
                    new bootstrap.Modal(document.getElementById('eventModal')).show();
                });
            });
        }
    }
    
    renderCalendar(currentDate);
    
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });
    
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });
});
</script>
@endsection
