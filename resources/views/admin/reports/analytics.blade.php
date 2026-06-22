@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    * { font-family: 'Inter', sans-serif; }

    /* ── Page Header ── */
    .analytics-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #3b82f6 100%);
        border-radius: 20px;
        padding: 32px 36px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(29,78,216,0.3);
    }
    .analytics-header::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 250px; height: 250px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .analytics-header::after {
        content: '';
        position: absolute;
        bottom: -80px; left: 40%;
        width: 320px; height: 320px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .analytics-header h1 {
        font-size: 1.9rem; font-weight: 800;
        color: #fff; margin: 0 0 6px;
    }
    .analytics-header p { color: rgba(255,255,255,0.75); margin: 0; font-size: 0.95rem; }

    /* ── Filter Bar ── */
    .filter-bar {
        background: #fff;
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;
    }
    .filter-bar .form-group { margin: 0; flex: 1 1 160px; }
    .filter-bar label { font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
    .filter-bar select,
    .filter-bar input[type="date"] {
        border: 1.5px solid #e5e7eb; border-radius: 10px;
        padding: 9px 13px; font-size: 0.9rem; color: #1f2937;
        width: 100%; background: #f9fafb; transition: border-color 0.2s;
    }
    .filter-bar select:focus,
    .filter-bar input[type="date"]:focus { border-color: #3b82f6; outline: none; background: #fff; }
    .btn-apply {
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        color: #fff; border: none;
        padding: 10px 24px; border-radius: 10px;
        font-weight: 700; font-size: 0.9rem;
        cursor: pointer; transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;
    }
    .btn-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(29,78,216,0.35); }
    .btn-reset {
        background: #f3f4f6; color: #374151;
        border: none; padding: 10px 16px;
        border-radius: 10px; font-weight: 600; font-size: 0.9rem;
        cursor: pointer; transition: all 0.2s; white-space: nowrap;
    }
    .btn-reset:hover { background: #e5e7eb; }

    /* ── Stat Cards ── */
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 24px; }
    .stat-card {
        background: #fff; border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        display: flex; align-items: center; gap: 18px;
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid transparent;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,0.12); }
    .stat-card.blue  { border-color: #3b82f6; }
    .stat-card.green { border-color: #10b981; }
    .stat-card.orange{ border-color: #f59e0b; }
    .stat-card.purple{ border-color: #8b5cf6; }
    .stat-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .stat-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .stat-icon.green  { background: #ecfdf5; color: #10b981; }
    .stat-icon.orange { background: #fffbeb; color: #f59e0b; }
    .stat-icon.purple { background: #f5f3ff; color: #8b5cf6; }
    .stat-label { font-size: 0.78rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 1.8rem; font-weight: 800; color: #1f2937; line-height: 1.1; }
    .stat-sub   { font-size: 0.8rem; color: #6b7280; margin-top: 2px; }

    /* ── Charts Grid ── */
    .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px; }
    @media (max-width: 900px) { .charts-grid { grid-template-columns: 1fr; } }
    .chart-card {
        background: #fff; border-radius: 16px; padding: 24px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    }
    .chart-card h5 {
        font-size: 1rem; font-weight: 700; color: #1f2937; margin: 0 0 20px;
        display: flex; align-items: center; gap: 8px;
    }
    .chart-card h5 .badge-module {
        font-size: 0.7rem; background: #eff6ff; color: #3b82f6;
        padding: 3px 8px; border-radius: 20px; font-weight: 600;
    }

    /* ── Module Badges ── */
    .mod-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;
    }
    .mod-badge.frontend { background: #eff6ff; color: #1d4ed8; }
    .mod-badge.student  { background: #ecfdf5; color: #065f46; }
    .mod-badge.vendor   { background: #fffbeb; color: #92400e; }
    .mod-badge.sales    { background: #fdf4ff; color: #6b21a8; }
    .mod-badge.other    { background: #f3f4f6; color: #374151; }

    /* ── Tables ── */
    .analytics-table {
        background: #fff; border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        overflow: hidden; margin-bottom: 24px;
    }
    .analytics-table-header {
        padding: 20px 24px; border-bottom: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    }
    .analytics-table-header h5 {
        font-size: 1rem; font-weight: 700; color: #1f2937; margin: 0;
        display: flex; align-items: center; gap: 8px;
    }
    .analytics-table table { width: 100%; border-collapse: collapse; }
    .analytics-table thead th {
        background: #f9fafb; padding: 12px 16px;
        font-size: 0.75rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.5px;
        border-bottom: 1px solid #e5e7eb; text-align: left;
    }
    .analytics-table tbody td {
        padding: 13px 16px; font-size: 0.875rem; color: #374151;
        border-bottom: 1px solid #f9fafb; vertical-align: middle;
    }
    .analytics-table tbody tr:hover { background: #fafbff; }
    .analytics-table tbody tr:last-child td { border-bottom: none; }

    /* ── Progress Bar ── */
    .prog-bar-wrap { background: #f3f4f6; border-radius: 20px; height: 6px; width: 100%; min-width: 80px; }
    .prog-bar { background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 20px; height: 6px; transition: width 0.5s; }

    /* ── Geo Section ── */
    .geo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    @media (max-width: 800px) { .geo-grid { grid-template-columns: 1fr; } }

    /* ── Empty State ── */
    .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .empty-state i { font-size: 3rem; margin-bottom: 16px; display: block; }
</style>

{{-- ═══ PAGE HEADER ═══ --}}
<div class="analytics-header">
    <div style="position: relative; z-index: 1;">
        <h1><i class="fas fa-chart-line" style="margin-right:10px;"></i>Page Analytics</h1>
        <p>Unique visitor insights across all website modules — powered by IP-based tracking</p>
    </div>
</div>

{{-- ═══ FILTER BAR ═══ --}}
<form method="GET" action="{{ url('admin/reports/analytics') }}" id="filterForm">
<div class="filter-bar">
    <div class="form-group">
        <label>From Date</label>
        <input type="date" name="date_from" value="{{ $dateFrom }}">
    </div>
    <div class="form-group">
        <label>To Date</label>
        <input type="date" name="date_to" value="{{ $dateTo }}">
    </div>
    <div class="form-group">
        <label>Module</label>
        <select name="module">
            <option value="">All Modules</option>
            @foreach($allModules as $mod)
                <option value="{{ $mod }}" {{ $module == $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Country</label>
        <select name="country">
            <option value="">All Countries</option>
            @foreach($allCountries as $c)
                <option value="{{ $c }}" {{ $country == $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn-apply"><i class="fas fa-filter"></i> Apply</button>
    <a href="{{ url('admin/reports/analytics') }}" class="btn-reset"><i class="fas fa-undo"></i> Reset</a>
</div>
</form>

{{-- ═══ STAT CARDS ═══ --}}
<div class="stat-grid">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-eye"></i></div>
        <div>
            <div class="stat-label">Total Unique Views</div>
            <div class="stat-value">{{ number_format($totalUniqueViews) }}</div>
            <div class="stat-sub">{{ $dateFrom }} → {{ $dateTo }}</div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-file-alt"></i></div>
        <div>
            <div class="stat-label">Top Page</div>
            <div class="stat-value" style="font-size:1.1rem; margin-top:2px;">{{ $topPage->page_title ?? 'N/A' }}</div>
            <div class="stat-sub">{{ $topPage ? number_format($topPage->views) . ' unique views' : 'No data' }}</div>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="fas fa-globe"></i></div>
        <div>
            <div class="stat-label">Top Country</div>
            <div class="stat-value" style="font-size:1.2rem; margin-top:2px;">{{ $topCountry->country ?? 'N/A' }}</div>
            <div class="stat-sub">{{ $topCountry ? number_format($topCountry->views) . ' unique visitors' : 'No data' }}</div>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon purple"><i class="fas fa-layer-group"></i></div>
        <div>
            <div class="stat-label">Top Module</div>
            <div class="stat-value" style="font-size:1.2rem; margin-top:2px;">{{ $topModule ? ucfirst($topModule->module) : 'N/A' }}</div>
            <div class="stat-sub">{{ $topModule ? number_format($topModule->views) . ' unique views' : 'No data' }}</div>
        </div>
    </div>
</div>

{{-- ═══ CHARTS ═══ --}}
<div class="charts-grid">
    {{-- Daily Trend Line Chart --}}
    <div class="chart-card">
        <h5><i class="fas fa-chart-area" style="color:#3b82f6;"></i> Daily Unique Views Trend</h5>
        <canvas id="trendChart" height="100"></canvas>
    </div>

    {{-- Module Pie Chart --}}
    <div class="chart-card">
        <h5><i class="fas fa-chart-pie" style="color:#8b5cf6;"></i> Views by Module</h5>
        <canvas id="moduleChart" height="200"></canvas>
        <div style="margin-top:16px;">
            @foreach($moduleBreakdown as $mb)
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                <span class="mod-badge {{ $mb->module }}"><i class="fas fa-circle" style="font-size:0.5rem;"></i> {{ ucfirst($mb->module) }}</span>
                <span style="font-weight:700; color:#1f2937;">{{ number_format($mb->views) }}</span>
            </div>
            @endforeach
            @if($moduleBreakdown->isEmpty())
                <div class="empty-state" style="padding:20px;"><i class="fas fa-inbox"></i> No data yet</div>
            @endif
        </div>
    </div>
</div>

{{-- ═══ TOP PAGES TABLE ═══ --}}
<div class="analytics-table" style="padding: 24px;">
    <div class="analytics-table-header" style="padding: 0 0 20px 0; border-bottom: none;">
        <h5><i class="fas fa-list-ol" style="color:#3b82f6;"></i> Top Pages by Unique Views</h5>
    </div>
    <div class="table-responsive">
        <table id="topPagesTable" class="table table-hover table-bordered" style="width:100%;">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Page</th>
                    <th>URL</th>
                    <th>Module</th>
                    <th>Countries</th>
                    <th>Unique Views</th>
                    <th>Device Breakdown</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ GEO BREAKDOWN ═══ --}}
<div class="geo-grid">
    {{-- Country + State breakdown --}}
    <div class="analytics-table">
        <div class="analytics-table-header">
            <h5><i class="fas fa-map-marker-alt" style="color:#f59e0b;"></i> Country & State Breakdown</h5>
        </div>
        @php $maxGeo = $countryBreakdown->max('views') ?: 1; @endphp
        <div style="overflow-x:auto; max-height:420px; overflow-y:auto;">
        <table>
            <thead>
                <tr>
                    <th>Country</th>
                    <th>State</th>
                    <th>Unique Views</th>
                    <th style="min-width:80px;">Share</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countryBreakdown as $geo)
                <tr>
                    <td style="font-weight:600;">
                        {{ $geo->country ?? '—' }}
                    </td>
                    <td style="color:#6b7280;">{{ $geo->state ?? '—' }}</td>
                    <td style="font-weight:700; color:#1d4ed8;">{{ number_format($geo->views) }}</td>
                    <td>
                        <div class="prog-bar-wrap">
                            <div class="prog-bar" style="width:{{ min(100, round($geo->views / $maxGeo * 100)) }}%; background: linear-gradient(135deg,#f59e0b,#d97706);"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4"><div class="empty-state"><i class="fas fa-globe"></i> No geo data yet</div></td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Module × Country matrix --}}
    <div class="analytics-table">
        <div class="analytics-table-header">
            <h5><i class="fas fa-th" style="color:#8b5cf6;"></i> Module × Country</h5>
        </div>
        @php
            $moduleCountry = \App\Models\PageView::query()
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->when($module, fn($q) => $q->where('module', $module))
                ->when($country, fn($q) => $q->where('country', $country))
                ->select('module', 'country', \Illuminate\Support\Facades\DB::raw('COUNT(*) as views'))
                ->whereNotNull('country')
                ->groupBy('module','country')
                ->orderByDesc('views')
                ->limit(30)
                ->get();
        @endphp
        <div style="overflow-x:auto; max-height:420px; overflow-y:auto;">
        <table>
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Country</th>
                    <th>Unique Views</th>
                </tr>
            </thead>
            <tbody>
                @forelse($moduleCountry as $mc)
                <tr>
                    <td><span class="mod-badge {{ $mc->module }}">{{ ucfirst($mc->module) }}</span></td>
                    <td style="font-weight:600;">{{ $mc->country }}</td>
                    <td style="font-weight:700; color:#8b5cf6;">{{ number_format($mc->views) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state"><i class="fas fa-layer-group"></i> No data yet</div></td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

</div>

{{-- ═══ CHART.JS ═══ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Trend Chart ──
(function() {
    const ctx = document.getElementById('trendChart').getContext('2d');
    const labels = @json($trendLabels);
    const data   = @json($trendData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Unique Views',
                data: data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                borderWidth: 2.5,
                pointRadius: labels.length <= 14 ? 4 : 2,
                pointBackgroundColor: '#3b82f6',
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y.toLocaleString() + ' unique views'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#9ca3af' },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    ticks: {
                        color: '#9ca3af',
                        maxRotation: 45,
                        autoSkip: true,
                        maxTicksLimit: 15
                    },
                    grid: { display: false }
                }
            }
        }
    });
})();

// ── Module Pie Chart ──
(function() {
    const ctx = document.getElementById('moduleChart').getContext('2d');
    const modules = @json($moduleBreakdown->pluck('module'));
    const views   = @json($moduleBreakdown->pluck('views'));
    const colors  = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4'];

    if (!modules.length) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: modules.map(m => m.charAt(0).toUpperCase() + m.slice(1)),
            datasets: [{
                data: views,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, font: { weight: '600' } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': ' + ctx.parsed.toLocaleString() + ' views'
                    }
                }
            }
        }
    });
})();

// ── Top Pages DataTable ──
$(document).ready(function() {
    $('#topPagesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('admin/reports/analytics') }}",
            data: function (d) {
                d.date_from = "{{ $dateFrom }}";
                d.date_to = "{{ $dateTo }}";
                d.module = "{{ $module }}";
                d.country = "{{ $country }}";
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'page_title', name: 'page_title', defaultContent: 'Unknown' },
            { data: 'url', name: 'url' },
            { data: 'module_badge', name: 'module', orderable: true },
            { data: 'countries', name: 'countries', searchable: false, className: 'text-center' },
            { data: 'unique_views', name: 'unique_views', className: 'fw-bold text-primary text-center', searchable: false },
            { data: 'device_breakdown', name: 'device_breakdown', orderable: false, searchable: false }
        ],
        order: [[5, 'desc']], // Order by unique_views by default
        language: {
            searchPlaceholder: "Search pages...",
            search: ""
        },
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        drawCallback: function(settings) {
            $('.dataTables_filter input').addClass('form-control shadow-sm border-0 bg-light px-3 py-2');
            $('.dataTables_length select').addClass('form-select shadow-sm border-0 bg-light');
        }
    });
});
</script>
@endsection
