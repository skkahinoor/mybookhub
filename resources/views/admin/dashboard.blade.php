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

            @if ($userType == 'superadmin' || $userType == 'admin')
                {{-- Eligible Vendor Payouts --}}
                <div class="row mt-4">
                    <div class="col-12 mb-4">
                        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; font-family: 'Nunito', sans-serif;">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center"
                                style="background: linear-gradient(to right, #f8faff, #ffffff); padding: 1.25rem 1.5rem;">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex justify-content-center align-items-center shadow-sm"
                                        style="width: 48px; height: 48px; min-width: 48px; border-radius: 12px; background: linear-gradient(135deg, #28a745, #5ddab4); color: white; margin-right: 15px;">
                                        <i class="fas fa-hand-holding-usd" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="card-title m-0" style="font-weight: 800; color: #25396f; font-size: 1.25rem;">
                                            Eligible Vendor Payouts
                                        </h4>
                                        <p class="text-muted mt-1 mb-0" style="font-size: 0.85rem; font-weight: 600;">
                                            Delivered vendor items pending payout release.
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ url('admin/orders') }}" class="btn btn-sm"
                                    style="background-color: #f2f7ff; color: #435ebe; border: none; font-weight: 700; padding: 8px 16px; border-radius: 8px; box-shadow: inset 0 0 0 1px rgba(67, 94, 190, 0.1);">
                                    View Orders <i class="fas fa-arrow-right ml-1" style="font-size: 0.75rem;"></i>
                                </a>
                            </div>
                            <div class="card-body px-0 py-0">
                                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                                    <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                                        <thead style="position: sticky; top: 0; background: #fbfdff; z-index: 10;">
                                            <tr>
                                                <th class="border-0 px-4 py-3 text-uppercase text-muted"
                                                    style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">
                                                    Order / Item
                                                </th>
                                                <th class="border-0 py-3 text-uppercase text-muted"
                                                    style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">
                                                    Product
                                                </th>
                                                <th class="border-0 py-3 text-uppercase text-muted"
                                                    style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">
                                                    Vendor
                                                </th>
                                                <th class="border-0 py-3 text-uppercase text-muted"
                                                    style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">
                                                    Vendor Amount
                                                </th>
                                                <th class="border-0 px-4 py-3 text-center text-uppercase text-muted"
                                                    style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($eligibleVendorPayoutItems ?? collect()) as $item)
                                                @php
                                                    $orderId = $item->order_id;
                                                    $vendorName = $item->vendor_details->user->name ?? ('Vendor #' . ($item->vendor_id ?? ''));
                                                    $vendorId = $item->vendor_id;
                                                    $amount = (float) ($item->vendor_payout_amount ?? 0);
                                                @endphp
                                                <tr style="transition: all 0.2s;" onmouseover="this.style.backgroundColor='#fcfdff';" onmouseout="this.style.backgroundColor='';">
                                                    <td class="px-4 py-3" style="border-bottom: 1px solid #edf2f9;">
                                                        <div class="d-flex flex-column">
                                                            <span style="font-weight: 800; color: #25396f;">#{{ $orderId }}</span>
                                                            <small class="text-muted" style="font-weight: 700;">Item #{{ $item->id }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="py-3" style="border-bottom: 1px solid #edf2f9; max-width: 320px;">
                                                        <div class="d-flex flex-column">
                                                            <span class="text-truncate" style="font-weight: 800; color: #435ebe;">
                                                                {{ $item->product_name ?? 'N/A' }}
                                                            </span>
                                                            <small class="text-muted" style="font-weight: 700;">Delivered • Pending payout</small>
                                                        </div>
                                                    </td>
                                                    <td class="py-3" style="border-bottom: 1px solid #edf2f9;">
                                                        <span style="font-weight: 800; color: #25396f;">{{ $vendorName }}</span>
                                                    </td>
                                                    <td class="py-3" style="border-bottom: 1px solid #edf2f9;">
                                                        <span style="font-weight: 900; color: #28a745;">₹{{ number_format($amount, 2) }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center" style="border-bottom: 1px solid #edf2f9;">
                                                        <div class="d-flex justify-content-center flex-wrap" style="gap: 8px;">
                                                            <a href="{{ url('admin/orders/' . $orderId) }}" class="btn btn-sm btn-outline-primary"
                                                                style="border-radius: 8px; font-weight: 800;">
                                                                Details
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary dashboard-release-payout-btn"
                                                                style="border-radius: 8px; font-weight: 800;"
                                                                data-item-id="{{ $item->id }}"
                                                                data-product-name="{{ $item->product_name }}"
                                                                data-vendor-id="{{ $vendorId }}"
                                                                data-vendor-amount="{{ number_format($amount, 2, '.', '') }}"
                                                                data-toggle="modal" data-target="#dashboardReleasePayoutModal">
                                                                <i class="mdi mdi-cash"></i> Release
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5">
                                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                                            <div style="width: 64px; height: 64px; background: rgba(40, 167, 69, 0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; box-shadow: inset 0 0 0 1px rgba(40, 167, 69, 0.12);">
                                                                <i class="fas fa-check text-success" style="font-size: 1.4rem;"></i>
                                                            </div>
                                                            <h6 style="color: #25396f; font-weight: 800; font-size: 1.15rem; margin-bottom: 5px;">No eligible payouts</h6>
                                                            <p class="text-muted" style="font-size: 0.9rem; font-weight: 600;">There are no delivered vendor items pending payout right now.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Release Vendor Payout Modal (Dashboard) --}}
                <div class="modal fade" id="dashboardReleasePayoutModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="mdi mdi-cash"></i> Release Vendor Payout</h5>
                                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <form action="{{ url('admin/release-vendor-payout') }}" method="POST">
                                @csrf
                                <input type="hidden" name="order_item_id" id="dash_payout_item_id">
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6><strong>Product:</strong> <span id="dash_payout_product_name"></span></h6>
                                            <h6><strong>Amount to Vendor:</strong> ₹<span id="dash_payout_amount"></span></h6>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary"><strong>Vendor Bank Details</strong></h6>
                                            <div id="dash_vendor_bank_details">
                                                <p class="text-muted">Loading...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="dash_vendor_payout_note"><strong>Payment Reference / Note</strong></label>
                                        <textarea class="form-control" name="vendor_payout_note" id="dash_vendor_payout_note" rows="3"
                                            placeholder="Enter transaction ID, reference number, or any notes..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-check-circle"></i> Mark as Released
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
             @if ($userType == 'superadmin' || $userType == 'admin')
            {{-- Sell Old Book Requests Row --}}
            <div class="row mt-4">
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; font-family: 'Nunito', sans-serif;">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center" style="background: linear-gradient(to right, #f8faff, #ffffff); padding: 1.5rem;">
                            <div class="d-flex align-items-center">
                                <div class="d-flex justify-content-center align-items-center shadow-sm" style="width: 48px; height: 48px; min-width: 48px; border-radius: 12px; background: linear-gradient(135deg, #435ebe, #5974d6); color: white; margin-right: 15px;">
                                    <i class="fas fa-hand-holding-usd" style="font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <h4 class="card-title m-0" style="font-weight: 800; color: #25396f; font-size: 1.25rem;">Sell Old Book Requests</h4>
                                    <p class="text-muted mt-1 mb-0" style="font-size: 0.85rem; font-weight: 600;">Review & manage book listings from your marketplace.</p>
                                </div>
                            </div>
                            <a href="{{ url('admin/sell-book-requests') }}" class="btn btn-sm" style="background-color: #f2f7ff; color: #435ebe; border: none; font-weight: 700; padding: 8px 16px; border-radius: 8px; box-shadow: inset 0 0 0 1px rgba(67, 94, 190, 0.1);">
                                View All <i class="fas fa-arrow-right ml-1" style="font-size: 0.75rem;"></i>
                            </a>
                        </div>
                        <div class="card-body px-0 py-0">
                            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                                <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                                    <thead style="position: sticky; top: 0; background: #fbfdff; z-index: 10;">
                                        <tr>
                                            <th class="border-0 px-4 py-3 text-uppercase text-muted" style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">#</th>
                                            <th class="border-0 py-3 text-uppercase text-muted" style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">Type / Seller</th>
                                            <th class="border-0 py-3 text-uppercase text-muted" style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">Book Details</th>
                                            <th class="border-0 py-3 text-uppercase text-muted" style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">Pricing</th>
                                            <th class="border-0 py-3 text-uppercase text-muted" style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">Status</th>
                                            <th class="border-0 px-4 py-3 text-center text-uppercase text-muted" style="font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f9 !important;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sellBookRequests as $key => $request)
                                            <tr style="transition: all 0.2s;" onmouseover="this.style.backgroundColor='#fcfdff';" onmouseout="this.style.backgroundColor='';">
                                                <td class="px-4 py-3" style="border-bottom: 1px solid #edf2f9; font-weight: 600; color: #a1a5b7;">{{ $key + 1 }}</td>
                                                <td class="py-3" style="border-bottom: 1px solid #edf2f9;">
                                                    <div class="d-flex flex-column">
                                                        <span style="font-weight: 700; color: #25396f; font-size: 0.95rem;">
                                                            @if($request->admin_type === 'vendor')
                                                                {{ $request->vendor->user->name ?? 'Vendor #'.$request->vendor_id }}
                                                            @else
                                                                {{ $request->user->name ?? 'Unknown User' }}
                                                            @endif
                                                        </span>
                                                        <span class="mt-1">
                                                            @if($request->admin_type === 'vendor')
                                                                <span class="badge" style="background:#fff3cd; color:#856404; font-size: 0.65rem; padding: 4px 8px; border-radius: 6px; font-weight: 800; letter-spacing: 0.5px;">VENDOR</span>
                                                            @else
                                                                <span class="badge" style="background:#e8eaff; color:#435ebe; font-size: 0.65rem; padding: 4px 8px; border-radius: 6px; font-weight: 800; letter-spacing: 0.5px;">STUDENT</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="py-3" style="border-bottom: 1px solid #edf2f9; max-width: 250px;">
                                                    <div class="d-flex flex-column">
                                                        <span style="font-weight: 700; color: #435ebe; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $request->product->product_name ?? 'N/A' }}</span>
                                                        <span class="mt-1">
                                                            @if($request->condition)
                                                                <span style="font-size: 0.75rem; font-weight: 700; color: #6c757d; background: #f8f9fa; padding: 3px 8px; border-radius: 4px; border: 1px solid #edf2f9;"><i class="fas fa-layer-group text-muted mr-1" style="font-size: 0.65rem;"></i>{{ $request->condition->name }}</span>
                                                            @else
                                                                <span class="text-muted" style="font-size: 0.75rem;">N/A</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="py-3" style="border-bottom: 1px solid #edf2f9;">
                                                    @php
                                                        $finalPrice = $request->price;
                                                        if (!$finalPrice && $request->product && $request->product->product_price > 0) {
                                                            if ($request->condition) {
                                                                $finalPrice = ($request->product->product_price * $request->condition->percentage) / 100;
                                                            } else {
                                                                $finalPrice = $request->product->product_price;
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="d-flex flex-column">
                                                        <span style="font-weight: 800; color: #25396f; font-size: 1.05rem;">&#8377;{{ $finalPrice ?? 'N/A' }}</span>
                                                        <span style="font-size: 0.75rem; font-weight: 700; color: #a1a5b7; text-decoration: line-through;">&#8377;{{ $request->product->product_price ?? '0' }} Base</span>
                                                    </div>
                                                </td>
                                                <td class="py-3" style="border-bottom: 1px solid #edf2f9;">
                                                    @if($request->admin_approved == 1)
                                                        <span style="background: rgba(40, 167, 69, 0.1); color: #28a745; font-size: 0.8rem; font-weight: 800; padding: 5px 10px; border-radius: 8px; border: 1px solid rgba(40, 167, 69, 0.2); display: inline-flex; align-items: center;"><i class="fas fa-check-circle mr-1" style="font-size:0.75rem;"></i>Approved</span>
                                                    @else
                                                        <span style="background: rgba(255, 193, 7, 0.1); color: #d39e00; font-size: 0.8rem; font-weight: 800; padding: 5px 10px; border-radius: 8px; border: 1px solid rgba(255, 193, 7, 0.2); display: inline-flex; align-items: center;"><i class="fas fa-clock mr-1" style="font-size:0.75rem;"></i>Pending</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center" style="border-bottom: 1px solid #edf2f9;">
                                                    @php
                                                        // Prep data for modal
                                                        $sellerName = $request->admin_type === 'vendor' ? ($request->vendor->user->name ?? 'Vendor') : ($request->user->name ?? 'N/A');
                                                        $sellerEmail = $request->admin_type === 'vendor' ? ($request->vendor->user->email ?? 'N/A') : ($request->user->email ?? 'N/A');
                                                        $sellerPhone = $request->admin_type === 'vendor' ? ($request->vendor->user->mobile ?? 'N/A') : ($request->user->mobile ?? 'N/A');
                                                        $bookImg = $request->user_old_book_image ? asset('front/images/product_images/medium/'.$request->user_old_book_image) : '';
                                                        $bookVid = $request->video_upload ? asset('front/videos/product_videos/'.$request->video_upload) : '';
                                                        
                                                        $reqData = [
                                                            'id' => $request->id,
                                                            'bookName' => $request->product->product_name ?? 'N/A',
                                                            'isbn' => $request->product->product_isbn ?? 'N/A',
                                                            'basePrice' => $request->product->product_price ?? 'N/A',
                                                            'sellPrice' => $finalPrice ?? 'N/A',
                                                            'condition' => $request->condition->name ?? 'N/A',
                                                            'type' => $request->admin_type === 'vendor' ? 'Vendor' : 'User (Student)',
                                                            'sellerName' => $sellerName,
                                                            'sellerEmail' => $sellerEmail,
                                                            'sellerPhone' => $sellerPhone,
                                                            'requestedOn' => $request->created_at ? $request->created_at->format('d M Y, h:i A') : 'N/A',
                                                            'status' => $request->admin_approved == 1 ? 'Approved' : 'Pending',
                                                            'img' => $bookImg,
                                                            'vid' => $bookVid,
                                                            'isApproved' => $request->admin_approved == 1
                                                        ];
                                                    @endphp
                                                    <button type="button" class="btn btn-sm shadow-sm" style="font-weight: 800; color: white; border-radius: 8px; padding: 6px 14px; background: linear-gradient(135deg, #435ebe, #5a75ce); border: none; font-size: 0.8rem; letter-spacing: 0.3px; transition: transform 0.2s;" onmousedown="this.style.transform='scale(0.95)';" onmouseup="this.style.transform='scale(1)';" onclick='openSellRequestModal(@json($reqData))'>
                                                        @if($request->admin_approved == 1)
                                                            View <i class="fas fa-eye ml-1" style="font-size: 0.75rem;"></i>
                                                        @else
                                                            Review <i class="fas fa-clipboard-check ml-1" style="font-size: 0.75rem;"></i>
                                                        @endif
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                                        <div style="width: 64px; height: 64px; background: #f2f7ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; box-shadow: inset 0 0 0 1px rgba(67, 94, 190, 0.1);">
                                                            <i class="fas fa-inbox text-muted" style="font-size: 1.5rem; color:#435ebe !important;"></i>
                                                        </div>
                                                        <h6 style="color: #25396f; font-weight: 800; font-size: 1.15rem; margin-bottom: 5px;">No requests found</h6>
                                                        <p class="text-muted" style="font-size: 0.9rem; font-weight: 600;">There are no sell old book requests at the moment.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sell Request Modal -->
            <div class="modal fade" id="sellRequestModal" tabindex="-1" role="dialog" aria-hidden="true" style="font-family: 'Nunito', sans-serif;">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                        <div class="modal-header border-0 pb-0 px-4 pt-4">
                            <h5 class="modal-title" style="font-weight: 800; color: #25396f; font-size: 1.25rem;">Book Listing Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body px-4 pb-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 style="color: #6c757d; font-weight: 700; margin-bottom: 12px; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Book Information</h6>
                                    <table class="table table-sm table-borderless m-0" style="table-layout: fixed; width: 100%; word-wrap: break-word;">
                                        <tr><th class="px-0 py-1" style="width: 30%; color: #4b5563;">Name:</th><td class="px-0 py-1" style="font-weight: 600; color: #25396f; white-space: normal;" id="m-bookName"></td></tr>
                                        <tr><th class="px-0 py-1" style="color: #4b5563;">ISBN:</th><td class="px-0 py-1" style="white-space: normal;" id="m-isbn"></td></tr>
                                        <tr><th class="px-0 py-1" style="color: #4b5563;">Base Price:</th><td class="px-0 py-1" style="white-space: normal;">₹<span id="m-basePrice"></span></td></tr>
                                        <tr><th class="px-0 py-1" style="color: #4b5563;">Selling Price:</th><td class="px-0 py-1" style="color: #435ebe; font-weight: 700; white-space: normal;">₹<span id="m-sellPrice"></span></td></tr>
                                        <tr><th class="px-0 py-1" style="color: #4b5563;">Condition:</th><td class="px-0 py-1" style="white-space: normal;"><span class="badge badge-info" id="m-condition"></span></td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 style="color: #6c757d; font-weight: 700; margin-bottom: 12px; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Seller Information</h6>
                                    <table class="table table-sm table-borderless m-0" style="table-layout: fixed; width: 100%; word-wrap: break-word;">
                                        <tr><th class="px-0 py-1" style="width: 35%; color: #4b5563;">Type:</th><td class="px-0 py-1" style="white-space: normal;"><span class="badge badge-secondary" id="m-type"></span></td></tr>
                                        <tr><th class="px-0 py-1" style="color: #4b5563;">Name:</th><td class="px-0 py-1" style="font-weight: 600; color: #25396f; white-space: normal;" id="m-sellerName"></td></tr>
                                        <tr><th class="px-0 py-1" style="color: #4b5563;">Email:</th><td class="px-0 py-1" style="white-space: normal; word-break: break-all;" id="m-sellerEmail"></td></tr>
                                        <tr><th class="px-0 py-1" style="color: #4b5563;">Phone:</th><td class="px-0 py-1" style="white-space: normal;" id="m-sellerPhone"></td></tr>
                                        <tr><th class="px-0 py-1" style="color: #4b5563;">Requested On:</th><td class="px-0 py-1" style="white-space: normal;" id="m-requestedOn"></td></tr>
                                    </table>
                                </div>
                            </div>
                            
                            <hr style="border-top: 1px dashed #e2e8f0; margin: 15px 0;">

                            <div class="row align-items-center">
                                <div class="col-md-6 text-center mb-3">
                                    <h6 style="color: #6c757d; font-weight: 700; margin-bottom: 12px; font-size: 0.9rem;">Book Image</h6>
                                    <div id="m-img-container" style="min-height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0;">
                                        <img id="m-img" src="" style="max-height: 150px; border-radius: 6px; display: none;">
                                        <span id="m-img-txt" class="text-muted" style="font-size: 0.85rem;">No Image Provided</span>
                                    </div>
                                </div>
                                <div class="col-md-6 text-center mb-3">
                                    <h6 style="color: #6c757d; font-weight: 700; margin-bottom: 12px; font-size: 0.9rem;">Book Video</h6>
                                    <div id="m-vid-container" style="min-height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0;">
                                        <div id="m-vid-player-wrapper" style="width: 100%; display: none;">
                                            <video id="m-video" controls style="width: 100%; max-height: 150px; border-radius: 6px;">
                                                <source id="m-vid-src" src="" type="video/mp4">
                                            </video>
                                        </div>
                                        <span id="m-vid-txt" class="text-muted" style="font-size: 0.85rem;">No Video Provided</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3" id="m-actions" style="display: none;">
                                <div class="col-12 d-flex justify-content-end">
                                    <form id="m-approve-form" method="POST" action="" style="display:inline;" class="mr-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success" style="border-radius: 8px; font-weight: 600; padding: 10px 20px;"><i class="fas fa-check-circle mr-1"></i> Approve Listing</button>
                                    </form>
                                    <form id="m-reject-form" method="POST" action="" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger" style="border-radius: 8px; font-weight: 600; padding: 10px 20px;" onclick="return confirm('Reject and delete this request?')"><i class="fas fa-times-circle mr-1"></i> Reject</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
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
                            @php
                                $orderDetailsUrl = Auth::guard('admin')->user()->type === 'vendor'
                                    ? url('vendor/orders/' . $order->id)
                                    : url('admin/orders/' . $order->id);
                            @endphp
                            <a href="{{ $orderDetailsUrl }}" class="d-block" style="text-decoration: none;">
                                <div class="recent-message d-flex align-items-center mb-4">
                                    <div class="avatar rounded-circle d-flex justify-content-center align-items-center" style="width: 45px; height: 45px; background: #e8eaff;">
                                        <i class="fas fa-shopping-bag" style="color: #435ebe; font-size: 1.1rem;"></i>
                                    </div>
                                    <div class="name ms-3 ml-3 overflow-hidden" style="flex: 1;">
                                        <h6 class="mb-0 text-truncate" style="font-weight: 700; color: #25396f; font-size: 0.95rem;">Order #{{ $order->id }}</h6>
                                        <small class="text-muted text-truncate d-block" style="font-size: 0.8rem;">{{ $order->user ? $order->user->name : 'N/A' }} • ₹{{ number_format($order->grand_total, 2) }}</small>
                                    </div>
                                </div>
                            </a>
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

            function openSellRequestModal(data) {
                // Populate text fields
                document.getElementById('m-bookName').innerText = data.bookName;
                document.getElementById('m-isbn').innerText = data.isbn;
                document.getElementById('m-basePrice').innerText = data.basePrice;
                document.getElementById('m-sellPrice').innerText = data.sellPrice;
                document.getElementById('m-condition').innerText = data.condition;
                document.getElementById('m-type').innerText = data.type;
                document.getElementById('m-sellerName').innerText = data.sellerName;
                document.getElementById('m-sellerEmail').innerText = data.sellerEmail;
                document.getElementById('m-sellerPhone').innerText = data.sellerPhone;
                document.getElementById('m-requestedOn').innerText = data.requestedOn;
                
                // Image
                var img = document.getElementById('m-img');
                var imgTxt = document.getElementById('m-img-txt');
                if(data.img) {
                    img.src = data.img;
                    img.style.display = 'block';
                    imgTxt.style.display = 'none';
                } else {
                    img.src = '';
                    img.style.display = 'none';
                    imgTxt.style.display = 'block';
                }

                // Video
                var vidWrapper = document.getElementById('m-vid-player-wrapper');
                var vidTxt = document.getElementById('m-vid-txt');
                var video = document.getElementById('m-video');
                var vidSrc = document.getElementById('m-vid-src');
                if(data.vid) {
                    vidSrc.src = data.vid;
                    video.load();
                    vidWrapper.style.display = 'block';
                    vidTxt.style.display = 'none';
                } else {
                    vidSrc.src = '';
                    video.load();
                    vidWrapper.style.display = 'none';
                    vidTxt.style.display = 'block';
                }

                // Actions display & form routing
                var actionsDiv = document.getElementById('m-actions');
                if(!data.isApproved) {
                    actionsDiv.style.display = 'flex';
                    // The URLs for approve/reject match routes logic
                    document.getElementById('m-approve-form').action = "{{ url('admin/sell-book-requests') }}/" + data.id + "/approve";
                    document.getElementById('m-reject-form').action = "{{ url('admin/sell-book-requests') }}/" + data.id + "/reject";
                } else {
                    actionsDiv.style.display = 'none';
                }

                // Open Modal
                $('#sellRequestModal').modal('show');
            }

            // Stop video on modal close
            document.addEventListener('DOMContentLoaded', function() {
                $('#sellRequestModal').on('hidden.bs.modal', function () {
                    var videoPlayer = document.getElementById('m-video');
                    if (videoPlayer) {
                        videoPlayer.pause();
                        videoPlayer.currentTime = 0;
                    }
                });
            });
        </script>

        @if ($userType == 'superadmin' || $userType == 'admin')
            <script>
                (function () {
                    function setText(id, value) {
                        var el = document.getElementById(id);
                        if (el) el.textContent = value ?? '';
                    }
                    function setValue(id, value) {
                        var el = document.getElementById(id);
                        if (el) el.value = value ?? '';
                    }
                    function setHtml(id, html) {
                        var el = document.getElementById(id);
                        if (el) el.innerHTML = html ?? '';
                    }

                    document.addEventListener('click', function (e) {
                        var btn = e.target && e.target.closest ? e.target.closest('.dashboard-release-payout-btn') : null;
                        if (!btn) return;

                        var itemId = btn.getAttribute('data-item-id') || '';
                        var productName = btn.getAttribute('data-product-name') || '';
                        var vendorAmount = btn.getAttribute('data-vendor-amount') || '';
                        var vendorId = btn.getAttribute('data-vendor-id') || '';

                        setValue('dash_payout_item_id', itemId);
                        setText('dash_payout_product_name', productName);
                        setText('dash_payout_amount', vendorAmount);
                        setValue('dash_vendor_payout_note', '');

                        setHtml('dash_vendor_bank_details', '<p class="text-muted">Loading...</p>');

                        var endpoint = @json(url('admin/get-vendor-bank-details'));
                        var url = endpoint + '?vendor_id=' + encodeURIComponent(vendorId);

                        fetch(url, { headers: { 'Accept': 'application/json' } })
                            .then(function (r) { return r.json(); })
                            .then(function (response) {
                                if (response && response.status === 'success') {
                                    var d = response.data || {};
                                    var html = '<table class="table table-sm table-bordered mb-0">';
                                    html += '<tr><td><strong>Name</strong></td><td>' + (d.account_holder_name || 'N/A') + '</td></tr>';
                                    html += '<tr><td><strong>Bank</strong></td><td>' + (d.bank_name || 'N/A') + '</td></tr>';
                                    html += '<tr><td><strong>Account No</strong></td><td>' + (d.account_number || 'N/A') + '</td></tr>';
                                    html += '<tr><td><strong>IFSC</strong></td><td>' + (d.bank_ifsc_code || 'N/A') + '</td></tr>';
                                    html += '</table>';
                                    setHtml('dash_vendor_bank_details', html);
                                } else {
                                    setHtml('dash_vendor_bank_details', '<p class="text-danger">Bank details not available.</p>');
                                }
                            })
                            .catch(function () {
                                setHtml('dash_vendor_bank_details', '<p class="text-danger">Failed to load bank details.</p>');
                            });
                    });
                })();
            </script>
        @endif
    @endsection
