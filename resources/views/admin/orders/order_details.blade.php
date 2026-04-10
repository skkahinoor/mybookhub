{{-- This page is rendered by orderDetails() method inside Admin/OrderController.php --}}
@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">

            @if (Session::has('error_message'))
                <!-- Check AdminController.php, updateAdminPassword() method -->
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> {{ Session::get('error_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{-- <strong>Error:</strong> {{ Session::get('error_message') }} --}}

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif



            @if (Session::has('success_message'))
                <!-- Check vendorRegister() method in Front/VendorController.php -->
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success:</strong> {{ Session::get('success_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif



            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Order Details</h3>
                            @if ($adminType == 'vendor')
                                <h6 class="font-weight-normal mb-0"><a href="{{ url('vendor/orders') }}">Back to Orders</a>
                                </h6>
                            @else
                                <h6 class="font-weight-normal mb-0"><a href="{{ url('admin/orders') }}">Back to Orders</a>
                                </h6>
                            @endif

                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="justify-content-end d-flex">
                                <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                        id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="true">
                                        <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                        <a class="dropdown-item" href="#">January - March</a>
                                        <a class="dropdown-item" href="#">March - June</a>
                                        <a class="dropdown-item" href="#">June - August</a>
                                        <a class="dropdown-item" href="#">August - November</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Order Details</h4>
                            <div class="table-responsive">
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="font-weight-bold" style="width: 40%">Order ID</td>
                                            <td>#{{ $orderDetails['id'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Order Date</td>
                                            <td>{{ date('d M Y, h:i A', strtotime($orderDetails['created_at'])) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Order Status</td>
                                            <td><span class="badge badge-primary">{{ $orderDetails['order_status'] }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Order Total</td>
                                            <td>₹{{ $orderDetails['grand_total'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Shipping Charges</td>
                                            <td>₹{{ $orderDetails['shipping_charges'] }}</td>
                                        </tr>
                                        @if (!empty($orderDetails['coupon_code']))
                                            <tr>
                                                <td class="font-weight-bold">Coupon Code</td>
                                                <td><span class="badge badge-success">{{ $orderDetails['coupon_code'] }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Coupon Amount</td>
                                                <td>₹{{ $orderDetails['coupon_amount'] }}</td>
                                            </tr>
                                        @endif
                                        @if (!empty($orderDetails['extra_discount']) && $orderDetails['extra_discount'] > 0)
                                            <tr>
                                                <td class="font-weight-bold">Extra Discount</td>
                                                <td>₹{{ $orderDetails['extra_discount'] }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="font-weight-bold">Payment Method</td>
                                            <td>{{ $orderDetails['payment_method'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Payment Gateway</td>
                                            <td>{{ $orderDetails['payment_gateway'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Customer Details</h4>
                                <div class="table-responsive">
                                    <table class="table table-borderless table-sm">
                                        <tbody>
                                            <tr><td class="font-weight-bold" style="width: 40%">Name</td><td>{{ $userDetails['name'] }}</td></tr>
                                            @if (!empty($userDetails['address']))
                                                <tr><td class="font-weight-bold">Address</td><td>{{ $userDetails['address'] }}</td></tr>
                                            @endif
                                            @if (!empty($userDetails['city']))
                                                <tr><td class="font-weight-bold">City</td><td>{{ $userDetails['city'] }}</td></tr>
                                            @endif
                                            @if (!empty($userDetails['state']))
                                                <tr><td class="font-weight-bold">State</td><td>{{ $userDetails['state'] }}</td></tr>
                                            @endif
                                            @if (!empty($userDetails['country']))
                                                <tr><td class="font-weight-bold">Country</td><td>{{ $userDetails['country'] }}</td></tr>
                                            @endif
                                            @if (!empty($userDetails['pincode']))
                                                <tr><td class="font-weight-bold">Pincode</td><td>{{ $userDetails['pincode'] }}</td></tr>
                                            @endif
                                            <tr><td class="font-weight-bold">Mobile</td><td>{{ $orderDetails['user_id'] == 0 ? $userDetails['mobile'] : $userDetails['phone'] }}</td></tr>
                                            <tr><td class="font-weight-bold">Email</td><td>{{ $userDetails['email'] }}</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Delivery Address</h4>
                            <div class="table-responsive">
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr><td class="font-weight-bold" style="width: 40%">Name</td><td>{{ $orderDetails['name'] }}</td></tr>
                                        @if (!empty($orderDetails['address']))
                                            <tr><td class="font-weight-bold">Address</td><td>{{ $orderDetails['address'] }}</td></tr>
                                        @endif
                                        @if (!empty($orderDetails['city']))
                                            <tr><td class="font-weight-bold">City</td><td>{{ $orderDetails['city'] }}</td></tr>
                                        @endif
                                        @if (!empty($orderDetails['state']))
                                            <tr><td class="font-weight-bold">State</td><td>{{ $orderDetails['state'] }}</td></tr>
                                        @endif
                                        @if (!empty($orderDetails['country']))
                                            <tr><td class="font-weight-bold">Country</td><td>{{ $orderDetails['country'] }}</td></tr>
                                        @endif
                                        @if (!empty($orderDetails['pincode']))
                                            <tr><td class="font-weight-bold">Pincode</td><td>{{ $orderDetails['pincode'] }}</td></tr>
                                        @endif
                                        <tr><td class="font-weight-bold">Mobile</td><td>{{ $orderDetails['mobile'] }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Update Order Status</h4>

                            @if (Auth::guard('admin')->user()->can('update_order_status'))
                                <form action="{{ url('admin/update-order-status') }}" method="post" class="forms-sample">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $orderDetails['id'] }}">
                                    
                                    <div class="form-group row">
                                        <label for="order_status" class="col-sm-3 col-form-label font-weight-bold">Status</label>
                                        <div class="col-sm-9">
                                            <select name="order_status" id="order_status" class="form-control form-control-sm" required>
                                                <option value="" selected>Select</option>
                                                @foreach ($orderStatuses as $status)
                                                    <option value="{{ $status['name'] }}"
                                                        @if (!empty($orderDetails['order_status']) && $orderDetails['order_status'] == $status['name']) selected @endif>{{ $status['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <input type="text" name="courier_name" id="courier_name"
                                                class="form-control form-control-sm mb-2" placeholder="Courier Name">
                                            <input type="text" name="tracking_number" id="tracking_number"
                                                class="form-control form-control-sm" placeholder="Tracking Number">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm mb-4">Update Status</button>
                                </form>

                                <hr>
                                <h5 class="mb-3">Order History Log</h5>
                                <div class="mt-3">
                                    @foreach ($orderLog as $key => $log)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="mr-3">
                                                <div class="icon-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="mdi mdi-check"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 font-weight-bold">{{ $log['order_status'] }}</h6>
                                                <small class="text-muted">{{ date('d M Y, h:i A', strtotime($log['created_at'])) }}</small>
                                                
                                                @if ($orderDetails['is_pushed'] == 1)
                                                    <br><small class="text-warning font-weight-bold">(Order Pushed to Shiprocket)</small>
                                                @endif
                                                
                                                @if (isset($log['order_item_id']) && $log['order_item_id'] > 0)
                                                    @php $getItemDetails = \App\Models\OrdersLog::getItemDetails($log['order_item_id']); @endphp
                                                    @if (!empty($getItemDetails['courier_name']))
                                                        <br><small><strong>Courier:</strong> {{ $getItemDetails['courier_name'] }}</small>
                                                    @endif
                                                    @if (!empty($getItemDetails['tracking_number']))
                                                        <br><small><strong>Tracking:</strong> {{ $getItemDetails['tracking_number'] }}</small>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    This feature is restricted for vendors.
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Ordered Products</h4>
                            @php
                                $pm = strtolower(trim((string) ($orderDetails['payment_method'] ?? '')));
                                $pg = strtolower(trim((string) ($orderDetails['payment_gateway'] ?? '')));
                                $isPickupLike = str_contains($pm, 'pickup') || in_array($pg, ['pickup', 'pickup from store'], true);
                                $showVendorPayout = ! $isPickupLike;
                            @endphp

                            <div class="table-responsive">
                                {{-- Order products info table --}}
                                <table class="table table-striped table-borderless">
                                    <tr class="table-danger">
                                        <th>Product Image</th>
                                        <th>Name</th>
                                        <th>Original MRP</th>
                                        <th>Product Discount</th>
                                        <th>Unit Price</th>
                                        <th>Qty</th>
                                        @if (\Illuminate\Support\Facades\Auth::guard('admin')->user()->type != 'vendor')
                                            <th>Product by</th>
                                        @endif
                                        <th>Distributed Discount</th>
                                        <th>Wallet Deduction</th>
                                        <th>Item Total</th>
                                        <th>Commission</th>
                                        <th>Final Amount</th>
                                        @if ($showVendorPayout)
                                            <th>Payout Status</th>
                                        @endif
                                        <th>Return Info</th>
                                        <th>Item Status</th>
                                    </tr>

                                    @foreach ($orderDetails['orders_products'] as $product)
                                        <tr>
                                            <td>
                                                @php
                                                    $getProductImage = \App\Models\Product::getProductImage($product['product_id']);
                                                @endphp
                                                <a target="_blank" href="{{ url('product/' . $product['product_id']) }}">
                                                    <img src="{{ asset('front/images/product_images/small/' . $getProductImage) }}">
                                                </a>
                                            </td>
                                            <td>{{ $product['product_name'] }}</td>
                                            <td>₹{{ $product['product']['product_price'] ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $prodAttr = collect($product['product']['attributes'] ?? [])->where('vendor_id', $product['vendor_id'])->first();
                                                    $prodDiscount = $prodAttr['product_discount'] ?? 0;
                                                @endphp
                                                @if ($prodDiscount > 0)
                                                    <span class="badge badge-danger">{{ $prodDiscount }}% OFF</span>
                                                @else
                                                    0%
                                                @endif
                                            </td>
                                            <td>₹{{ $product['product_price'] }}</td>
                                            <td>{{ $product['product_qty'] }}</td>

                                            @if (\Illuminate\Support\Facades\Auth::guard('admin')->user()->type != 'vendor')
                                                <td>
                                                    @if ($product['vendor_id'] > 0)
                                                        <a href="/admin/view-vendor-details/{{ $product['admin_id'] }}" target="_blank">Vendor</a>
                                                    @else
                                                        Admin
                                                    @endif
                                                </td>
                                            @endif

                                            @php
                                                $originalTotalPrice = $product['product_price'] * $product['product_qty'];
                                                $appliedDistributedDiscount = 0;
                                                $hasCoupon = !empty($orderDetails['coupon_amount']) && $orderDetails['coupon_amount'] > 0;
                                                $hasExtraDiscount = !empty($orderDetails['extra_discount']) && $orderDetails['extra_discount'] > 0;

                                                if ($product['vendor_id'] > 0) {
                                                    if ($hasCoupon || $hasExtraDiscount) {
                                                        if ($hasCoupon) {
                                                            if (\App\Models\Coupon::couponDetails($orderDetails['coupon_code'])['vendor_id'] > 0) {
                                                                $appliedDistributedDiscount = $item_discount;
                                                            } elseif ($hasExtraDiscount) {
                                                                $appliedDistributedDiscount = $orderDetails['extra_discount'] / ($total_items ?: 1);
                                                            }
                                                        } elseif ($hasExtraDiscount) {
                                                            $appliedDistributedDiscount = $item_discount;
                                                        }
                                                    }
                                                } else {
                                                    $appliedDistributedDiscount = $item_discount;
                                                }
                                                $walletTotal = (float) ($orderDetails['wallet_amount'] ?? 0);
                                                $appliedWallet = $walletTotal > 0 && $total_items > 0
                                                    ? round($walletTotal * ($product['product_qty'] / ($total_items ?: 1)), 2)
                                                    : 0;

                                                $total_price = $originalTotalPrice - $appliedDistributedDiscount - $appliedWallet;
                                            @endphp
                                            <td>₹{{ round($appliedDistributedDiscount, 2) }}</td>
                                            <td class="text-success">-₹{{ round($appliedWallet, 2) }}</td>
                                            <td>₹{{ round($total_price, 2) }}</td>

                                            @if ($product['vendor_id'] > 0)
                                                @php $commission = round(($total_price * $product['commission']) / 100, 2); @endphp
                                                <td>₹{{ $commission }}</td>
                                                <td>₹{{ round($total_price - $commission, 2) }}</td>
                                                @if ($showVendorPayout)
                                                    <td>
                                                        @if (!empty($product['vendor_payout_status']) && $product['vendor_payout_status'] == 'Released')
                                                            <span class="badge badge-success">Released</span>
                                                            @if (!empty($product['vendor_payout_note']))
                                                                <br><small class="text-muted mt-1">Note/Payment Id: {{ $product['vendor_payout_note'] }}</small>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-warning">Pending</span>
                                                            @if ($product['item_status'] == 'Delivered' && Auth::guard('admin')->user()->type != 'vendor')
                                                                <br>
                                                                <button type="button" class="btn btn-primary btn-sm mt-2 release-payout-btn"
                                                                    data-item-id="{{ $product['id'] }}"
                                                                    data-product-name="{{ $product['product_name'] }}"
                                                                    data-vendor-id="{{ $product['vendor_id'] }}"
                                                                    data-vendor-amount="{{ round($total_price - $commission, 2) }}"
                                                                    data-toggle="modal" data-target="#releasePayoutModal">
                                                                    <i class="mdi mdi-cash"></i> Release
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </td>
                                                @endif
                                            @else
                                                <td>₹0</td>
                                                <td>₹{{ round($total_price, 2) }}</td>
                                                @if ($showVendorPayout)
                                                    <td>N/A</td>
                                                @endif
                                            @endif

                                            <td>
                                                @if (!empty($product['return_status']))
                                                    <div class="alert alert-warning p-1" style="font-size: 11px;">
                                                        <strong>Status:</strong> {{ $product['return_status'] }}<br>
                                                        <strong>Reason:</strong> {{ $product['return_reason'] }}<br>
                                                        <strong>Comm:</strong> {{ $product['return_comments'] }}
                                                    </div>

                                                    {{-- Payment Status Badge --}}
                                                    @if (!empty($product['return_payment_status']))
                                                        <span class="badge badge-{{ $product['return_payment_status'] == 'Payment Initiated' ? 'info' : 'success' }} mt-1">
                                                            {{ $product['return_payment_status'] }}
                                                        </span>
                                                        @if (!empty($product['return_payment_note']))
                                                            <br><small class="text-muted mt-1">Note: {{ $product['return_payment_note'] }}</small>
                                                        @endif
                                                    @endif

                                                    {{-- Initiate Payment Button (only for Returned items without payment) --}}
                                                    @if ($product['return_status'] == 'Returned' && empty($product['return_payment_status']))
                                                        <button type="button" class="btn btn-success btn-sm mt-2 initiate-payment-btn"
                                                            data-item-id="{{ $product['id'] }}"
                                                            data-product-name="{{ $product['product_name'] }}"
                                                            data-product-price="{{ $product['product_price'] }}"
                                                            data-user-id="{{ $product['user_id'] }}"
                                                            data-toggle="modal" data-target="#initiatePaymentModal">
                                                            <i class="mdi mdi-cash-refund"></i> Initiate Payment
                                                        </button>
                                                    @endif
                                                @else
                                                    No return request
                                                @endif
                                            </td>

                                            <td>
                                                @if (Auth::guard('admin')->user()->can('update_order_item_status'))
                                                    <form action="{{ url('admin/update-order-item-status') }}"
                                                        method="post" class="d-flex align-items-center">
                                                        @csrf

                                                        <input type="hidden" name="order_item_id"
                                                            value="{{ $product['id'] }}">

                                                        <select id="order_item_status" name="order_item_status" class="form-control form-control-sm mr-2" style="width: 130px;" required>
                                                            <option value="">Select</option>
                                                            @foreach ($orderItemStatuses as $status)
                                                                <option value="{{ $status['name'] }}"
                                                                    @if (!empty($product['item_status']) && $product['item_status'] == $status['name']) selected @endif>
                                                                    {{ $status['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit"
                                                            class="btn btn-primary btn-sm">Update</button>
                                                    </form>
                                                @else
                                                    {{ $product['item_status'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


        </div>
        <!-- content-wrapper ends -->

        {{-- Footer --}}
        @include('admin.layout.footer')
        <!-- partial -->
    </div>

    {{-- Initiate Return Payment Modal --}}
    <div class="modal fade" id="initiatePaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="mdi mdi-cash-refund"></i> Initiate Return Payment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ url($adminType . '/initiate-return-payment') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_item_id" id="payment_item_id">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6><strong>Product:</strong> <span id="payment_product_name"></span></h6>
                                <h6><strong>Refund Amount:</strong> ₹<span id="payment_amount"></span></h6>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary"><strong>User Bank Details</strong></h6>
                                <div id="user_bank_details">
                                    <p class="text-muted">Loading...</p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="return_payment_note"><strong>Payment Note / Transaction Reference</strong></label>
                            <textarea class="form-control" name="return_payment_note" id="return_payment_note" rows="3"
                                placeholder="Enter transaction ID, payment method details, or any notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-check-circle"></i> Confirm Payment Initiated
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Release Vendor Payout Modal --}}
    @if ($showVendorPayout ?? true)
        <div class="modal fade" id="releasePayoutModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="mdi mdi-cash"></i> Release Vendor Payout</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form action="{{ url('admin/release-vendor-payout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_item_id" id="payout_item_id">
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6><strong>Product:</strong> <span id="payout_product_name"></span></h6>
                                    <h6><strong>Amount to Vendor:</strong> ₹<span id="payout_amount"></span></h6>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary"><strong>Vendor Bank Details</strong></h6>
                                    <div id="vendor_bank_details">
                                        <p class="text-muted">Loading...</p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="vendor_payout_note"><strong>Payment Reference / Note</strong></label>
                                <textarea class="form-control" name="vendor_payout_note" id="vendor_payout_note" rows="3"
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

    {{-- Vendor Pickup: auto mark as delivered --}}
    @if (($adminType ?? 'admin') === 'vendor' && ($isPickupLike ?? false) && (($orderDetails['order_status'] ?? '') !== 'Delivered'))
        <div class="modal fade" id="pickupDeliverModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="mdi mdi-store"></i> Pickup Order</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" data-pickup-cancel><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">
                            This order is <strong>Pickup from store</strong>. Click <strong>Mark Delivered Now</strong> after confirming details.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-bordered mb-0">
                                    <tr><td><strong>Order ID</strong></td><td>#{{ $orderDetails['id'] }}</td></tr>
                                    <tr><td><strong>Name</strong></td><td>{{ $orderDetails['name'] }}</td></tr>
                                    <tr><td><strong>Mobile</strong></td><td>{{ $orderDetails['mobile'] }}</td></tr>
                                    <tr><td><strong>Total</strong></td><td>₹{{ $orderDetails['grand_total'] }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-bordered mb-0">
                                    <tr><td><strong>Payment Method</strong></td><td>{{ $orderDetails['payment_method'] ?? '' }}</td></tr>
                                    <tr><td><strong>Payment Gateway</strong></td><td>{{ $orderDetails['payment_gateway'] ?? '' }}</td></tr>
                                    <tr><td><strong>Status</strong></td><td>{{ $orderDetails['order_status'] ?? '' }}</td></tr>
                                </table>
                            </div>
                        </div>
                        <form id="pickupDeliverForm" method="POST" action="{{ url('vendor/orders/' . $orderDetails['id'] . '/pickup-mark-delivered') }}" class="d-none">
                            @csrf
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-pickup-cancel>Cancel</button>
                        <button type="button" class="btn btn-info" onclick="document.getElementById('pickupDeliverForm').submit();">
                            Mark Delivered Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        // Vendor pickup orders: show modal (manual mark delivered)
        (function () {
            try {
                var adminType = @json($adminType ?? 'admin');
                var isPickupLike = @json($isPickupLike ?? false);
                var orderStatus = @json($orderDetails['order_status'] ?? '');
                var orderId = @json($orderDetails['id'] ?? null);

                if (adminType === 'vendor' && isPickupLike && orderId && orderStatus !== 'Delivered') {
                    var modal = $('#pickupDeliverModal');
                    if (modal.length) {
                        modal.modal('show');
                    }
                }
            } catch (e) {}
        })();

        $(document).on('click', '.initiate-payment-btn', function() {
            var itemId = $(this).data('item-id');
            var productName = $(this).data('product-name');
            var productPrice = $(this).data('product-price');
            var userId = $(this).data('user-id');

            $('#payment_item_id').val(itemId);
            $('#payment_product_name').text(productName);
            $('#payment_amount').text(productPrice);
            $('#return_payment_note').val('');

            // Fetch user bank details via AJAX
            $('#user_bank_details').html('<p class="text-muted">Loading...</p>');
            $.ajax({
                url: '{{ url($adminType . "/get-user-bank-details") }}',
                type: 'GET',
                data: { user_id: userId },
                success: function(response) {
                    if (response.status === 'success') {
                        var d = response.data;
                        var html = '<table class="table table-sm table-bordered mb-0">';
                        html += '<tr><td><strong>Name</strong></td><td>' + (d.account_holder_name || 'N/A') + '</td></tr>';
                        html += '<tr><td><strong>Bank</strong></td><td>' + (d.bank_name || 'N/A') + '</td></tr>';
                        html += '<tr><td><strong>Account No</strong></td><td>' + (d.account_number || 'N/A') + '</td></tr>';
                        html += '<tr><td><strong>IFSC</strong></td><td>' + (d.ifsc_code || 'N/A') + '</td></tr>';
                        html += '<tr><td><strong>UPI</strong></td><td>' + (d.upi_id || 'N/A') + '</td></tr>';
                        html += '</table>';
                        $('#user_bank_details').html(html);
                    } else {
                        $('#user_bank_details').html('<p class="text-danger">Bank details not available.</p>');
                    }
                },
                error: function() {
                    $('#user_bank_details').html('<p class="text-danger">Failed to load bank details.</p>');
                }
            });
        });
        $(document).on('click', '.release-payout-btn', function() {
            var itemId = $(this).data('item-id');
            var productName = $(this).data('product-name');
            var vendorAmount = $(this).data('vendor-amount');
            var vendorId = $(this).data('vendor-id');

            $('#payout_item_id').val(itemId);
            $('#payout_product_name').text(productName);
            $('#payout_amount').text(vendorAmount);
            $('#vendor_payout_note').val('');

            // Fetch vendor bank details via AJAX
            $('#vendor_bank_details').html('<p class="text-muted">Loading...</p>');
            $.ajax({
                url: '{{ url("admin/get-vendor-bank-details") }}',
                type: 'GET',
                data: { vendor_id: vendorId },
                success: function(response) {
                    if (response.status === 'success') {
                        var d = response.data;
                        var html = '<table class="table table-sm table-bordered mb-0">';
                        html += '<tr><td><strong>Name</strong></td><td>' + (d.account_holder_name || 'N/A') + '</td></tr>';
                        html += '<tr><td><strong>Bank</strong></td><td>' + (d.bank_name || 'N/A') + '</td></tr>';
                        html += '<tr><td><strong>Account No</strong></td><td>' + (d.account_number || 'N/A') + '</td></tr>';
                        html += '<tr><td><strong>IFSC</strong></td><td>' + (d.bank_ifsc_code || 'N/A') + '</td></tr>';
                        html += '</table>';
                        $('#vendor_bank_details').html(html);
                    } else {
                        $('#vendor_bank_details').html('<p class="text-danger">Bank details not available.</p>');
                    }
                },
                error: function() {
                    $('#vendor_bank_details').html('<p class="text-danger">Failed to load bank details.</p>');
                }
            });
        });
    </script>
    @endpush
@endsection
