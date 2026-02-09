@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Sales Executive Details</h4>
                        <p class="text-muted mb-0">
                            {{ $salesExecutive->user->name ?? 'N/A' }} 
                            ({{ $salesExecutive->user->phone ?? 'N/A' }})
                        </p>
                    </div>
                    <a href="{{ route('admin.reports.sales_reports.index') }}" class="btn btn-light border">
                        Back to Reports
                    </a>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <ul class="nav nav-tabs" id="salesExecTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                                aria-controls="profile" aria-selected="true">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="institution-tab" data-toggle="tab" href="#institution" role="tab"
                                aria-controls="institution" aria-selected="false">Institution</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="student-tab" data-toggle="tab" href="#student" role="tab"
                                aria-controls="student" aria-selected="false">Student</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab"
                                aria-controls="report" aria-selected="false">Report</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content" id="salesExecTabsContent">
                {{-- Profile Tab --}}
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Profile Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> {{ $salesExecutive->user->name ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $salesExecutive->user->email ?? 'N/A' }}</p>
                                    <p><strong>Phone:</strong> {{ $salesExecutive->user->phone ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong>
                                        @if ($salesExecutive->status)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Address:</strong> {{ $salesExecutive->user->address ?? 'N/A' }}</p>
                                    <p><strong>Block:</strong> {{ optional($salesExecutive->user->block)->name ?? 'N/A' }}</p>
                                    <p><strong>District:</strong> {{ optional($salesExecutive->user->district)->name ?? 'N/A' }}</p>
                                    <p><strong>State:</strong> {{ optional($salesExecutive->user->state)->name ?? 'N/A' }}</p>
                                    <p><strong>Country:</strong> {{ optional($salesExecutive->user->country)->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Institution Tab --}}
                <div class="tab-pane fade" id="institution" role="tabpanel" aria-labelledby="institution-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Institutions Added</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Contact</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($institutions as $institution)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $institution->name }}</td>
                                                <td>{{ $institution->type }}</td>
                                                <td>{{ $institution->contact_number }}</td>
                                                <td>
                                                    {{ optional($institution->block)->name }},
                                                    {{ optional($institution->district)->name }},
                                                    {{ optional($institution->state)->name }},
                                                    {{ optional($institution->country)->name }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No institutions found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Student Tab --}}
                <div class="tab-pane fade" id="student" role="tabpanel" aria-labelledby="student-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Students Added</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Institution</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($students as $student)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->phone }}</td>
                                                <td>{{ optional($student->institution)->name }}</td>
                                                <td>
                                                    @if ($student->status)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No students found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Report Tab --}}
                <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card shadow-sm border-0 h-100 bg-success text-white">
                                <div class="card-body">
                                    <p class="mb-1">Today's Earning</p>
                                    <h3 class="fw-semibold mb-0">₹{{ number_format($todayEarning, 2) }}</h3>
                                    <small class="opacity-75">{{ $todayStudentsCount }} students today</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card shadow-sm border-0 h-100 bg-info text-white">
                                <div class="card-body">
                                    <p class="mb-1">Weekly Earning</p>
                                    <h3 class="fw-semibold mb-0">₹{{ number_format($weeklyEarning, 2) }}</h3>
                                    <small class="opacity-75">{{ $weeklyStudentsCount }} students this week</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card shadow-sm border-0 h-100 bg-warning text-white">
                                <div class="card-body">
                                    <p class="mb-1">Monthly Earning</p>
                                    <h3 class="fw-semibold mb-0">₹{{ number_format($monthlyEarning, 2) }}</h3>
                                    <small class="opacity-75">{{ $monthlyStudentsCount }} students this month</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card shadow-sm border-0 h-100 bg-primary text-white">
                                <div class="card-body">
                                    <p class="mb-1">Total Earning</p>
                                    <h3 class="fw-semibold mb-0">₹{{ number_format($totalEarning, 2) }}</h3>
                                    <small class="opacity-75">{{ $totalStudentsCount }} total students</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
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
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        $(document).ready(function() {
            const ctx = document.getElementById('studentEnrollmentChart').getContext('2d');

            const labels = @json($dates);
            const studentsData = @json($studentsCount);
            const earningsData = @json($earningsData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
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
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false,
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush

