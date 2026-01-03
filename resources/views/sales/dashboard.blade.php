@extends('layouts.app')
@section('title')
    Sales Dashboard
@endsection
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-0">Welcome, {{ Auth::guard('sales')->user()->name ?? 'Sales Executive' }}</h2>
                    <p class="text-muted mb-0">Here's your performance overview</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row g-4 mb-4">
         <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100 text-white" style="background-color: rgb(83 155 122)" !important>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1 opacity-75 small">Today's Students</p>
                            <h3 class="fw-bold mb-0">{{ number_format($todayStudents ?? 0) }}</h3>
                        </div>
                        <div class="bg-secondary bg-opacity-20 rounded-circle p-3">
                            <i class="bi bi-calendar fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4" >
            <div class="card shadow-sm border-0 h-100" style="background-color: #b3d9e1" !important>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted mb-1 small">Total Institutions</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalInstitutions ?? 0) }}</h3>
                        </div>
                        <div class="bg-secondary bg-opacity-20 rounded-circle p-3">
                            <i class="bi bi-building fs-4 text-light"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1 opacity-75 small">Total Students</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalStudents ?? 0) }}</h3>
                        </div>
                        <div class="bg-secondary bg-opacity-20 rounded-circle p-3">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        
    </div>

    <!-- Earnings Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1 opacity-75 small">Today's Earning</p>
                            <h2 class="fw-bold mb-0">₹{{ number_format($todayEarning ?? 0, 2) }}</h2>
                            <small class="mt-2 opacity-75 d-block">{{ number_format($todayStudents ?? 0) }} students enrolled today</small>
                        </div>
                        <div class="bg-secondary bg-opacity-20 rounded-circle p-3">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1 opacity-75 small">Total Earning</p>
                            <h2 class="fw-bold mb-0">₹{{ number_format($totalEarning ?? 0, 2) }}</h2>
                            <small class="mt-2 opacity-75 d-block">{{ number_format($totalStudents ?? 0) }} total students</small>
                        </div>
                        <div class="bg-secondary bg-opacity-20 rounded-circle p-3">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 text-white" style="background-color: #ab9461" !important>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1 opacity-75 small">Income Per Target</p>
                            <h2 class="fw-bold mb-0">₹{{ number_format($incomePerTarget ?? 0, 2) }}</h2>
                            <small class="mt-2 opacity-75 d-block">Per student enrolled</small>
                        </div>
                        <div class="bg-secondary bg-opacity-20 rounded-circle p-3">
                            <i class="bi bi-tag fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Bar Graph -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2 text-primary"></i>
                        Performance Overview - Last 30 Days
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="dashboardBarChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Bar Graph -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2 text-success"></i>
                        Overall Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="summaryBarChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Bar Chart (Last 30 Days)
    const ctx1 = document.getElementById('dashboardBarChart').getContext('2d');

    const labels = @json($dates ?? []);
    const studentsData = @json($studentsCount ?? []);
    const institutionsData = @json($institutionsCount ?? []);
    const earningsData = @json($earningsData ?? []);

    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Students Enrolled',
                    data: studentsData,
                    backgroundColor: 'rgba(13, 110, 253, 0.8)',
                    borderColor: 'rgb(13, 110, 253)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Institutions Added',
                    data: institutionsData,
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgb(255, 193, 7)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Earnings (₹)',
                    data: earningsData,
                    backgroundColor: 'rgba(25, 135, 84, 0.8)',
                    borderColor: 'rgb(25, 135, 84)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.datasetIndex === 2) {
                                label += '₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            } else {
                                label += context.parsed.y;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Count'
                    },
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Earnings (₹)'
                    },
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString('en-IN');
                        }
                    }
                }
            }
        }
    });

    // Summary Statistics Bar Chart
    const ctx2 = document.getElementById('summaryBarChart').getContext('2d');

    const summaryLabels = ['Institutions', 'Students', 'Total Earnings'];
    const summaryData = [
        {{ $totalInstitutions ?? 0 }},
        {{ $totalStudents ?? 0 }},
        {{ round($totalEarning ?? 0) }}
    ];

    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: summaryLabels,
            datasets: [
                {
                    label: 'Count',
                    data: summaryData,
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.8)',
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgb(13, 110, 253)',
                        'rgb(25, 135, 84)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)'
                    ],
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataIndex === 3) {
                                label += '₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            } else {
                                label += context.parsed.y.toLocaleString('en-IN');
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count / Amount'
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000) {
                                return (value / 1000).toFixed(1) + 'K';
                            }
                            return value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
