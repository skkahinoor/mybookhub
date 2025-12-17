@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">

            {{-- Header --}}
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h3 class="font-weight-bold mb-1">Welcome {{ Auth::guard('admin')->user()->name }}</h3>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                All systems are running smoothly. Here is a quick overview of your store.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Styles for professional metric cards --}}
            <style>
                .metric-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
                    gap: 1rem;
                }

                .metric-card {
                    background: #e6d8d8;
                    border-radius: 12px;
                    border: 1px solid #e5e7eb;
                    padding: 0.9rem 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04);
                    transition: transform 0.15s ease, box-shadow 0.15s ease;
                }

                .metric-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.12);
                }

                .metric-info {
                    display: flex;
                    flex-direction: column;
                    gap: 0.15rem;
                }

                .metric-label {
                    font-size: 0.8rem;
                    font-weight: 500;
                    text-transform: uppercase;
                    letter-spacing: 0.06em;
                    color: #6b7280;
                }

                .metric-value {
                    font-size: 1.4rem;
                    font-weight: 600;
                    color: #111827;
                }

                .metric-icon {
                    width: 38px;
                    height: 38px;
                    border-radius: 999px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1rem;
                }

                .metric-card.sections .metric-icon {
                    background: rgba(40, 92, 177, 0.12);
                    color: #1d4ed8;
                }

                .metric-card.categories .metric-icon {
                    background: rgba(16, 185, 129, 0.12);
                    color: #047857;
                }

                .metric-card.products .metric-icon {
                    background: rgba(234, 179, 8, 0.12);
                    color: #b45309;
                }

                .metric-card.vendors .metric-icon {
                    background: rgba(239, 68, 68, 0.12);
                    color: #b91c1c;
                }

                .metric-card.orders .metric-icon {
                    background: rgba(96, 165, 250, 0.12);
                    color: #1d4ed8;
                }

                .metric-card.coupons .metric-icon {
                    background: rgba(56, 189, 248, 0.12);
                    color: #0369a1;
                }

                .metric-card.users .metric-icon {
                    background: rgba(129, 140, 248, 0.12);
                    color: #4338ca;
                }

                .metric-card.subscribers .metric-icon {
                    background: rgba(52, 211, 153, 0.12);
                    color: #047857;
                }

                @media (max-width: 576px) {
                    .metric-value {
                        font-size: 1.2rem;
                    }
                }
            </style>

            {{-- Metrics Grid --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="metric-grid">
                        <div class="metric-card vendors">
                            <a href="{{ url('admin/admins/vendor') }}">
                                <div class="metric-info">
                                    <span class="metric-label">Vendors</span>
                                    <span class="metric-value">{{ number_format($vendorsCount) }}</span>
                                </div>
                                <div class="metric-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                            </a>
                        </div>

                        <div class="metric-card users">
                            <a href="{{ url('admin/users') }}">
                                <div class="metric-info">
                                    <span class="metric-label">Users</span>
                                    <span class="metric-value">{{ number_format($usersCount) }}</span>
                                </div>
                                <div class="metric-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </a>
                        </div>

                        <div class="metric-card subscribers">
                            <a href="{{ url('admin/sales-executive') }}">
                                <div class="metric-info">
                                    <span class="metric-label">Sales Executives</span>
                                    <span class="metric-value">{{ number_format($salesExecutivesCount) }}</span>
                                </div>
                                <div class="metric-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="metric-grid">
                        <div class="metric-card products">
                            <a href="{{ url('admin/products') }}">
                                <div class="metric-info">
                                    <span class="metric-label">Products</span>
                                    <span class="metric-value">{{ number_format($productsCount) }}</span>
                                </div>
                                <div class="metric-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                            </a>
                        </div>

                        <div class="metric-card orders">
                            <a href="{{ url('admin/orders') }}">
                                <div class="metric-info">
                                    <span class="metric-label">Orders</span>
                                    <span class="metric-value">{{ number_format($ordersCount) }}</span>
                                </div>
                                <div class="metric-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </a>
                        </div>

                        <div class="metric-card coupons">
                            <a href="{{ url('admin/coupons') }}">
                                <div class="metric-info">
                                    <span class="metric-label">Coupons</span>
                                    <span class="metric-value">{{ number_format($couponsCount) }}</span>
                                </div>
                                <div class="metric-icon">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div> 
            </div>

        </div>

        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>
@endsection
