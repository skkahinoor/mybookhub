@extends('admin.layout.layout')


@section('content')

    {{-- Fonts for Mazer Theme --}}
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    {{-- Styles for professional metric cards (Mazer Style) --}}
    <style>

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .metric-card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255,255,255,0.8);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        }

        .metric-card a {
            display: flex;
            align-items: center;
            text-decoration: none !important;
            gap: 1.2rem;
            height: 100%;
        }

        .metric-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
            color: #ffffff;
        }

        .metric-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .metric-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.2rem;
            text-transform: capitalize;
            letter-spacing: 0.2px;
        }

        .metric-value {
            font-size: 1.45rem;
            font-weight: 800;
            color: #25396f;
            line-height: 1.2;
            margin: 0;
        }

        /* Card Icon Background Colors - Mazer Inspired */
        .icon-purple { background-color: #798bff; }
        .icon-cyan { background-color: #55c6e8; }
        .icon-green { background-color: #5ddab4; }
        .icon-red { background-color: #ff7976; }
        .icon-darkblue { background-color: #435ebe; }
        .icon-orange { background-color: #ffa82e; }

        .dashboard-header-title {
            color: #25396f;
            font-weight: 800;
            font-size: 1.6rem;
            margin-bottom: 0.25rem;
            font-family: 'Nunito', sans-serif;
        }
        
        .dashboard-header-subtitle {
            color: #7e8299;
            font-size: 1rem;
            font-weight: 600;
        }

        .page-title-box {
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .metric-grid {
                grid-template-columns: 1fr;
            }
            .content-wrapper {
                padding: 0.5rem 1rem 1.5rem 1rem !important;
            }
            .dashboard-header-title {
                font-size: 1.3rem;
            }
            .metric-card {
                padding: 1.25rem;
            }
            .metric-icon-wrapper {
                width: 45px;
                height: 45px;
                font-size: 1.2rem;
            }
            .metric-text h3 {
                font-size: 1.3rem;
            }        
        }
    </style>

    <div class="main-panel">
        <div class="content-wrapper">

            {{-- Header --}}
            <div class="row page-title-box">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h2 class="dashboard-header-title">
                                Profile Statistics
                                @php $userType = Auth::guard('admin')->user()->type; @endphp
                                @if ($userType === 'vendor' && $vendor)
                                    @if ($vendor->plan === 'pro')
                                        <span class="badge badge-primary ml-2"
                                            style="font-size: 0.75rem; border-radius: 6px; vertical-align: middle;">PRO</span>
                                    @else
                                        <span class="badge badge-secondary ml-2"
                                            style="font-size: 0.75rem; border-radius: 6px; vertical-align: middle;">FREE</span>
                                        <form method="POST" action="{{ route('vendor.plan.upgrade') }}"
                                            style="display: inline-block; margin-left: 10px;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="border-radius: 8px; font-weight: 600;">
                                                <i class="mdi mdi-crown mr-1"></i> Upgrade to Pro
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </h2>
                            <p class="dashboard-header-subtitle mb-0">
                                Welcome back, {{ Auth::guard('admin')->user()->name }}! Here is a quick overview of your store.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metrics Grid --}}
            <div class="row">
                @if ($userType == 'superadmin' || $userType == 'admin')
                    <div class="col-md-12">
                        <div class="metric-grid">
                            {{-- Vendors Card --}}
                            <div class="metric-card">
                                <a href="{{ url('admin/admins/vendor') }}">
                                    <div class="metric-icon-wrapper icon-purple">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="metric-details">
                                        <div class="metric-label">Vendors</div>
                                        <div class="metric-value">{{ number_format($vendorsCount) }}</div>
                                    </div>
                                </a>
                            </div>

                             {{-- Students Card --}}
                            <div class="metric-card">
                                <a href="{{ url('admin/students') }}">
                                    <div class="metric-icon-wrapper icon-cyan">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div class="metric-details">
                                        <div class="metric-label">Students</div>
                                        <div class="metric-value">{{ number_format($studentsCount) }}</div>
                                    </div>
                                </a>
                            </div>

                            {{-- Sales Executives Card --}}
                            <div class="metric-card">
                                <a href="{{ url('admin/sales-executive') }}">
                                    <div class="metric-icon-wrapper icon-green">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="metric-details">
                                        <div class="metric-label">Sales Executives</div>
                                        <div class="metric-value">{{ number_format($salesExecutivesCount) }}</div>
                                    </div>
                                </a>
                            </div>

                            {{-- Products Card --}}
                            <div class="metric-card">
                                <a href="{{ url('admin/products') }}">
                                    <div class="metric-icon-wrapper icon-red">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <div class="metric-details">
                                        <div class="metric-label">Products</div>
                                        <div class="metric-value">{{ number_format($productsCount) }}</div>
                                    </div>
                                </a>
                            </div>

                            {{-- Orders Card --}}
                            <div class="metric-card">
                                <a href="{{ url('admin/orders') }}">
                                    <div class="metric-icon-wrapper icon-darkblue">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="metric-details">
                                        <div class="metric-label">Orders</div>
                                        <div class="metric-value">{{ number_format($ordersCount) }}</div>
                                    </div>
                                </a>
                            </div>

                            {{-- Coupons Card --}}
                            <div class="metric-card">
                                <a href="{{ url('admin/coupons') }}">
                                    <div class="metric-icon-wrapper icon-orange">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <div class="metric-details">
                                        <div class="metric-label">Coupons</div>
                                        <div class="metric-value">{{ number_format($couponsCount) }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif ($userType == 'vendor')
                    <div class="col-md-12">
                        <div class="metric-grid">
                            {{-- Products Card --}}
                            @if (Auth::guard('admin')->user()->can('view_products'))
                                <div class="metric-card">
                                    <a href="{{ url('vendor/products') }}">
                                        <div class="metric-icon-wrapper icon-red">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                        <div class="metric-details">
                                            <div class="metric-label">Products</div>
                                            <div class="metric-value">{{ number_format($productsCount) }}</div>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            {{-- Orders Card --}}
                            @if (Auth::guard('admin')->user()->can('view_orders'))
                                <div class="metric-card">
                                    <a href="{{ url('vendor/orders') }}">
                                        <div class="metric-icon-wrapper icon-darkblue">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                        <div class="metric-details">
                                            <div class="metric-label">Orders</div>
                                            <div class="metric-value">{{ number_format($ordersCount) }}</div>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            {{-- Coupons Card --}}
                            @if (Auth::guard('admin')->user()->can('view_coupons'))
                                <div class="metric-card">
                                    <a href="{{ url('vendor/coupons') }}">
                                        <div class="metric-icon-wrapper icon-orange">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div class="metric-details">
                                            <div class="metric-label">Coupons</div>
                                            <div class="metric-value">{{ number_format($couponsCount) }}</div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Charts & Activity Row --}}
            <div class="row mt-4">
                <div class="col-12 col-lg-9 mb-4">
                    <div class="card shadow-sm border-0" style="border-radius: 12px; height: 100%;">
                        <div class="card-header bg-white border-0 pt-4 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title m-0" style="font-weight: 700; color: #25396f; font-family: 'Nunito', sans-serif;">Order Performance Overview</h4>
                                <i class="fas fa-bars text-muted" style="cursor: pointer;"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="profile-visit-chart"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-3 mb-4">
                    <div class="card shadow-sm border-0" style="border-radius: 12px; height: 100%;">
                        <div class="card-header bg-white border-0 pt-4 pb-0">
                            <h4 class="card-title m-0" style="font-weight: 700; color: #25396f; font-family: 'Nunito', sans-serif;">Recent Orders</h4>
                        </div>
                        <div class="card-body px-4 pt-4">
                            @forelse($recentOrders as $order)
                            <div class="recent-message d-flex align-items-center mb-4">
                                <div class="avatar rounded-circle d-flex justify-content-center align-items-center" style="width: 45px; height: 45px; background: #e8eaff;">
                                    <i class="fas fa-shopping-bag" style="color: #435ebe; font-size: 1.1rem;"></i>
                                </div>
                                <div class="name ms-3 ml-3 overflow-hidden" style="flex: 1;">
                                    <h6 class="mb-0 text-truncate" style="font-weight: 700; color: #25396f; font-size: 0.95rem;">Order #{{ $order->id }}</h6>
                                    <small class="text-muted text-truncate d-block" style="font-size: 0.8rem;">{{ $order->user ? $order->user->name : 'N/A' }} • ₹{{ number_format($order->grand_total, 2) }}</small>
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted w-100 py-3">
                                <p style="font-size: 0.9rem;">No recent orders found.</p>
                            </div>
                            @endforelse
                            <div class="mt-4">
                                @php
                                   $ordersUrl = Auth::guard('admin')->user()->type === 'vendor' ? url('vendor/orders') : url('admin/orders');
                                @endphp
                                <a href="{{ $ordersUrl }}" class="btn w-100 text-center" style="background-color: #f2f7ff; color: #435ebe; border: none; font-weight: 700; padding: 12px; border-radius: 8px; text-decoration: none; display: block;">View All Orders</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- content-wrapper ends -->
            @include('admin.layout.footer')
            <!-- partial -->
        </div>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var options = {
                    series: [{
                        name: 'Orders',
                        data: {!! json_encode($ordersPerMonth ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!}
                    }],
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Nunito, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 4,
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val > 0 ? val : '';
                        },
                        style: {
                            fontSize: '12px',
                            colors: ["#fff"]
                        }
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#a1a5b7',
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#a1a5b7',
                                fontSize: '12px'
                            }
                        }
                    },
                    fill: {
                        opacity: 1,
                        colors: ['#435ebe']
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " orders"
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f2f7',
                        strokeDashArray: 4,
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    }
                };

                if (document.querySelector("#profile-visit-chart")) {
                    var chart = new ApexCharts(document.querySelector("#profile-visit-chart"), options);
                    chart.render();
                }
            });
        </script>
    @endsection
