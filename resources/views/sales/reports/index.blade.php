@extends('layouts.app')
@section('title')
    Sales Report
@endsection
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title mb-0">Sales Report</h2>
            <p class="text-muted mb-0">View your earnings and student enrollment statistics</p>
        </div>
    </div>

    <!-- Earnings Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-success text-white">
                <div class="card-body d-flex flex-column align-items-start">
                    <div class="mb-2">
                        <i class="bi bi-cash-stack fs-2"></i>
                    </div>
                    <p class="mb-1">Today's Earning</p>
                    <h2 class="fw-semibold mb-0">₹{{ number_format($todayEarning, 2) }}</h2>
                    <small class="mt-2 opacity-75">{{ $todayStudentsCount }} students enrolled today</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-info text-white">
                <div class="card-body d-flex flex-column align-items-start">
                    <div class="mb-2">
                        <i class="bi bi-calendar-week fs-2"></i>
                    </div>
                    <p class="mb-1">Weekly Earning</p>
                    <h2 class="fw-semibold mb-0">₹{{ number_format($weeklyEarning, 2) }}</h2>
                    <small class="mt-2 opacity-75">{{ $weeklyStudentsCount }} students this week</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-warning text-white">
                <div class="card-body d-flex flex-column align-items-start">
                    <div class="mb-2">
                        <i class="bi bi-calendar fs-2"></i>
                    </div>
                    <p class="mb-1">Monthly Earning</p>
                    <h2 class="fw-semibold mb-0">₹{{ number_format($monthlyEarning, 2) }}</h2>
                    <small class="mt-2 opacity-75">{{ $monthlyStudentsCount }} students this month</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-primary text-white">
                <div class="card-body d-flex flex-column align-items-start">
                    <div class="mb-2">
                        <i class="bi bi-graph-up-arrow fs-2"></i>
                    </div>
                    <p class="mb-1">Total Earning</p>
                    <h2 class="fw-semibold mb-0">₹{{ number_format($totalEarning, 2) }}</h2>
                    <small class="mt-2 opacity-75">{{ $totalStudentsCount }} total students enrolled</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Enrollment Graph -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Student Enrollment & Earnings (Last 30 Days)</h5>
                    <small class="text-muted">Income per Target: ₹{{ number_format($incomePerTarget, 2) }}</small>
                </div>
                <div class="card-body">
                    <canvas id="studentEnrollmentChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill fs-1 text-primary mb-2"></i>
                    <h3 class="fw-semibold">{{ number_format($totalStudentsCount) }}</h3>
                    <p class="text-muted mb-0">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-day fs-1 text-success mb-2"></i>
                    <h3 class="fw-semibold">{{ number_format($todayStudentsCount) }}</h3>
                    <p class="text-muted mb-0">Today's Enrollments</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-currency-rupee fs-1 text-info mb-2"></i>
                    <h3 class="fw-semibold">₹{{ number_format($incomePerTarget, 2) }}</h3>
                    <p class="text-muted mb-0">Income Per Target</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('studentEnrollmentChart').getContext('2d');

    const labels = @json($dates);
    const studentsData = @json($studentsCount);
    const earningsData = @json($earningsData);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Students Enrolled',
                    data: studentsData,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgb(13, 110, 253)',
                    borderWidth: 1,
                    yAxisID: 'y',
                    order: 2
                },
                {
                    label: 'Earnings (₹)',
                    data: earningsData,
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: 'rgb(25, 135, 84)',
                    borderWidth: 1,
                    yAxisID: 'y1',
                    order: 1
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
                            if (context.datasetIndex === 1) {
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
                    stacked: false,
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
                        text: 'Students Count'
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
});
</script>

<style>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
</style>
@endsection

