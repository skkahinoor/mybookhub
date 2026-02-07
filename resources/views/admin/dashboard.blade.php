@extends('admin.layout.layout')


@section('content')

 {{-- Styles for professional metric cards --}}
 <style>
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .metric-card {
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 160px;
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        text-decoration: none !important;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }

    .metric-card a {
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        height: 100%;
        justify-content: space-between;
    }

    .metric-label {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
        opacity: 0.8;
    }

    .metric-value {
        font-size: 2.2rem;
        font-weight: 620;
        line-height: 1;
        margin-bottom: 1rem;
    }

    .metric-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: all 0.3s ease;
    }

    /* Card Themes */
    .metric-card.vendors { background-color: #fef2f2; color: #991b1b; }
    .metric-card.vendors .metric-icon-wrapper { background-color: #fee2e2; color: #ef4444; }
    
    .metric-card.users { background-color: #f5f3ff; color: #5b21b6; }
    .metric-card.users .metric-icon-wrapper { background-color: #ede9fe; color: #8b5cf6; }
    
    .metric-card.subscribers { background-color: #ecfdf5; color: #065f46; }
    .metric-card.subscribers .metric-icon-wrapper { background-color: #d1fae5; color: #10b981; }
    
    .metric-card.products { background-color: #fffaf0; color: #9a3412; }
    .metric-card.products .metric-icon-wrapper { background-color: #ffedd5; color: #f97316; }
    
    .metric-card.orders { background-color: #eff6ff; color: #1e40af; }
    .metric-card.orders .metric-icon-wrapper { background-color: #dbeafe; color: #3b82f6; }
    
    .metric-card.coupons { background-color: #f0fdfa; color: #115e59; }
    .metric-card.coupons .metric-icon-wrapper { background-color: #ccfbf1; color: #14b8a6; }

    @media (max-width: 768px) {
        .metric-grid {
            grid-template-columns: 1fr;
        }
    }
</style> 

    <div class="main-panel">
        <div class="content-wrapper">

            {{-- Header --}}
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h2 class="font-weight-bold mb-1" style="color: #1a1a1a;">
                                Welcome, {{ Auth::guard('admin')->user()->name }}
                                @php $userType = Auth::guard('admin')->user()->type; @endphp
                                @if($userType === 'vendor' && $vendor)
                                    @if($vendor->plan === 'pro')
                                        <span class="badge badge-primary ml-2" style="font-size: 0.75rem; border-radius: 6px;">PRO</span>
                                    @else
                                        <span class="badge badge-secondary ml-2" style="font-size: 0.75rem; border-radius: 6px;">FREE</span>
                                        <form method="POST" action="{{ route('vendor.plan.upgrade') }}" style="display: inline-block; margin-left: 10px;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 8px; font-weight: 600;">
                                                <i class="mdi mdi-crown mr-1"></i> Upgrade to Pro
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </h2>
                            <p class="text-muted mb-0" style="font-size: 1rem; opacity: 0.8;">
                                All systems are running smoothly. Here is a quick overview of your store.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metrics Grid --}}
            <div class="row">
                @if ($userType == 'superadmin')
                <div class="col-md-12">
                    <div class="metric-grid">
                        {{-- Vendors Card --}}
                        <div class="metric-card vendors">
                            <a href="{{ url('admin/admins/vendor') }}">
                                <div class="w-100">
                                    <div class="metric-label">Vendors</div>
                                    <div class="metric-value">{{ number_format($vendorsCount) }}</div>
                                </div>
                                <div class="metric-icon-wrapper">
                                    <i class="fas fa-store"></i>
                                </div>
                            </a>
                        </div>

                        {{-- Users Card --}}
                        <div class="metric-card users">
                            <a href="{{ url('admin/users') }}">
                                <div class="w-100">
                                    <div class="metric-label">Users</div>
                                    <div class="metric-value">{{ number_format($usersCount) }}</div>
                                </div>
                                <div class="metric-icon-wrapper">
                                    <i class="fas fa-users"></i>
                                </div>
                            </a>
                        </div>

                        {{-- Sales Executives Card --}}
                        <div class="metric-card subscribers">
                            <a href="{{ url('admin/sales-executive') }}">
                                <div class="w-100">
                                    <div class="metric-label">Sales Executives</div>
                                    <div class="metric-value">{{ number_format($salesExecutivesCount) }}</div>
                                </div>
                                <div class="metric-icon-wrapper">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            </a>
                        </div>

                        {{-- Products Card --}}
                        <div class="metric-card products">
                            <a href="{{ url('admin/products') }}">
                                <div class="w-100">
                                    <div class="metric-label">Products</div>
                                    <div class="metric-value">{{ number_format($productsCount) }}</div>
                                </div>
                                <div class="metric-icon-wrapper">
                                    <i class="fas fa-box-open"></i>
                                </div>
                            </a>
                        </div>

                        {{-- Orders Card --}}
                        <div class="metric-card orders">
                            <a href="{{ url('admin/orders') }}">
                                <div class="w-100">
                                    <div class="metric-label">Orders</div>
                                    <div class="metric-value">{{ number_format($ordersCount) }}</div>
                                </div>
                                <div class="metric-icon-wrapper">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </a>
                        </div>

                        {{-- Coupons Card --}}
                        <div class="metric-card coupons">
                            <a href="{{ url('admin/coupons') }}">
                                <div class="w-100">
                                    <div class="metric-label">Coupons</div>
                                    <div class="metric-value">{{ number_format($couponsCount) }}</div>
                                </div>
                                <div class="metric-icon-wrapper">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
                @elseif ($userType == 'vendor')
                <div class="col-md-12">
                    <div class="metric-grid">
                        {{-- Products Card --}}
                        @if(Auth::guard('admin')->user()->can('view_products'))
                        <div class="metric-card products">
                            <a href="{{ url('vendor/products') }}">
                                <div class="w-100">
                                    <div class="metric-label">Products</div>
                                    <div class="metric-value">{{ number_format($productsCount) }}</div>
                                </div>
                                <div class="metric-icon-wrapper">
                                    <i class="fas fa-box-open"></i>
                                </div>
                            </a>
                        </div>
                        @endif

                        {{-- Orders Card --}}
                        @if(Auth::guard('admin')->user()->can('view_orders'))
                        <div class="metric-card orders">
                            <a href="{{ url('vendor/orders') }}">
                                <div class="w-100">
                                    <div class="metric-label">Orders</div>
                                    <div class="metric-value">{{ number_format($ordersCount) }}</div>
                                </div>
                                <div class="metric-icon-wrapper">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </a>
                        </div>
                        @endif

                        {{-- Coupons Card --}}
                        @if(Auth::guard('admin')->user()->can('view_coupons'))
                        <div class="metric-card coupons">
                            <a href="{{ url('vendor/coupons') }}">
                                <div class="w-100">
                                    <div class="metric-label">Coupons</div>
                                    <div class="metric-value">{{ number_format($couponsCount) }}</div>
                                </div>
                                <div class="metric-icon-wrapper">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div> 
                @endif
            </div>

        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>
@endsection
