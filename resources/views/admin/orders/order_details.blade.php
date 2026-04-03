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
                            <h4 class="card-title">Order Details</h4>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Order ID: </label>
                                <label>#{{ $orderDetails['id'] }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Order Date: </label>
                                <label>{{ date('Y-m-d h:i:s', strtotime($orderDetails['created_at'])) }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Order Status: </label>
                                <label>{{ $orderDetails['order_status'] }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Order Total: </label>
                                <label>₹{{ $orderDetails['grand_total'] }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Shipping Charges: </label>
                                <label>₹{{ $orderDetails['shipping_charges'] }}</label>
                            </div>
                            @if (!empty($orderDetails['coupon_code']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Coupon Code: </label>
                                    <label>{{ $orderDetails['coupon_code'] }}</label>
                                </div>
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Coupon Amount: </label>
                                    <label>₹{{ $orderDetails['coupon_amount'] }}</label>
                                </div>
                            @endif

                            @if (!empty($orderDetails['extra_discount']) && $orderDetails['extra_discount'] > 0)
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Extra Discount: </label>
                                    <label>₹{{ $orderDetails['extra_discount'] }}</label>
                                </div>
                            @endif

                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Payment Method: </label>
                                <label>{{ $orderDetails['payment_method'] }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Payment Gateway: </label>
                                <label>{{ $orderDetails['payment_gateway'] }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($orderDetails['user_id'] == 0)
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Customer Details</h4>
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Name: </label>
                                    <label>{{ $userDetails['name'] }}</label>
                                </div>

                                @if (!empty($userDetails['address']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">Address: </label>
                                        <label>{{ $userDetails['address'] }}</label>
                                    </div>
                                @endif

                                @if (!empty($userDetails['city']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">City: </label>
                                        <label>{{ $userDetails['city'] }}</label>
                                    </div>
                                @endif

                                @if (!empty($userDetails['state']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">State: </label>
                                        <label>{{ $userDetails['state'] }}</label>
                                    </div>
                                @endif

                                @if (!empty($userDetails['country']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">Country: </label>
                                        <label>{{ $userDetails['country'] }}</label>
                                    </div>
                                @endif

                                @if (!empty($userDetails['pincode']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">Pincode: </label>
                                        <label>{{ $userDetails['pincode'] }}</label>
                                    </div>
                                @endif

                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Mobile: </label>
                                    <label>{{ $userDetails['mobile'] }}</label>
                                </div>
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Email: </label>
                                    <label>{{ $userDetails['email'] }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Customer Details</h4>
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Name: </label>
                                    <label>{{ $userDetails['name'] }}</label>
                                </div>

                                @if (!empty($userDetails['address']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">Address: </label>
                                        <label>{{ $userDetails['address'] }}</label>
                                    </div>
                                @endif

                                @if (!empty($userDetails['city']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">City: </label>
                                        <label>{{ $userDetails['city'] }}</label>
                                    </div>
                                @endif

                                @if (!empty($userDetails['state']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">State: </label>
                                        <label>{{ $userDetails['state'] }}</label>
                                    </div>
                                @endif

                                @if (!empty($userDetails['country']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">Country: </label>
                                        <label>{{ $userDetails['country'] }}</label>
                                    </div>
                                @endif

                                @if (!empty($userDetails['pincode']))
                                    <div class="form-group" style="height: 15px">
                                        <label style="font-weight: 550">Pincode: </label>
                                        <label>{{ $userDetails['pincode'] }}</label>
                                    </div>
                                @endif

                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Mobile: </label>
                                    <label>{{ $userDetails['phone'] }}</label>
                                </div>
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Email: </label>
                                    <label>{{ $userDetails['email'] }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Delivery Address</h4>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Name: </label>
                                <label>{{ $orderDetails['name'] }}</label>
                            </div>

                            @if (!empty($orderDetails['address']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Address: </label>
                                    <label>{{ $orderDetails['address'] }}</label>
                                </div>
                            @endif

                            @if (!empty($orderDetails['city']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">City: </label>
                                    <label>{{ $orderDetails['city'] }}</label>
                                </div>
                            @endif

                            @if (!empty($orderDetails['state']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">State: </label>
                                    <label>{{ $orderDetails['state'] }}</label>
                                </div>
                            @endif

                            @if (!empty($orderDetails['country']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Country: </label>
                                    <label>{{ $orderDetails['country'] }}</label>
                                </div>
                            @endif

                            @if (!empty($orderDetails['pincode']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Pincode: </label>
                                    <label>{{ $orderDetails['pincode'] }}</label>
                                </div>
                            @endif

                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Mobile: </label>
                                <label>{{ $orderDetails['mobile'] }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Update Order Status</h4> {{-- determined by 'admin'-s ONLY, not 'vendor'-s --}}

                            @if (Auth::guard('admin')->user()->can('update_order_status'))
                                <form action="{{ url('admin/update-order-status') }}" method="post">
                                    @csrf

                                    <input type="hidden" name="order_id" value="{{ $orderDetails['id'] }}">

                                    <select name="order_status" id="order_status" required>
                                        <option value="" selected>Select</option>
                                        @foreach ($orderStatuses as $status)
                                            <option value="{{ $status['name'] }}"
                                                @if (!empty($orderDetails['order_status']) && $orderDetails['order_status'] == $status['name']) selected @endif>{{ $status['name'] }}
                                            </option>
                                        @endforeach
                                    </select>


                                    <input type="text" name="courier_name" id="courier_name"
                                        placeholder="Courier Name"> {{-- This input field will only show up when 'Shipped' <option> is selected. Check admin/js/custom.js --}}
                                    <input type="text" name="tracking_number" id="tracking_number"
                                        placeholder="Tracking Number"> {{-- This input field will only show up when 'Shipped' <option> is selected. Check admin/js/custom.js --}}

                                    <button type="submit">Update</button>
                                </form>
                                <br>

                                {{-- Show the "Update Order Status" History/Log in admin/orders/order_details.blade.php     --}}
                                @foreach ($orderLog as $key => $log)
                                    @php
                                        // echo '<pre>', var_dump($log), '</pre>';
                                        // echo '<pre>', var_dump($log['orders_products']), '</pre>';
                                        // echo '<pre>', var_dump($key), '</pre>';
                                        // echo '<pre>', var_dump($log['orders_products'][$key]), '</pre>';
                                        // echo '<pre>', var_dump($log['orders_products'][$key]['product_code']), '</pre>';
                                    @endphp

                                    <strong>{{ $log['order_status'] }}</strong>

                                    {{-- Shiprocket API integration --}}
                                    @if ($orderDetails['is_pushed'] == 1)
                                        {{-- If the Order has been pushed to Shiprocket, state this --}}
                                        <span style="color: #cf8938;">(Order Pushed to Shiprocket)</span>
                                    @endif


                                    @if (isset($log['order_item_id']) && $log['order_item_id'] > 0)
                                        @php
                                            $getItemDetails = \App\Models\OrdersLog::getItemDetails(
                                                $log['order_item_id'],
                                            );
                                        @endphp
                                        {{-- - for item {{ $getItemDetails['product_code'] }} --}}

                                        @if (!empty($getItemDetails['courier_name']))
                                            <br>
                                            <span>Courier Name: {{ $getItemDetails['courier_name'] }}</span>
                                        @endif

                                        @if (!empty($getItemDetails['tracking_number']))
                                            <br>
                                            <span>Tracking Number: {{ $getItemDetails['tracking_number'] }}</span>
                                        @endif
                                    @endif

                                    <br>
                                    {{ date('Y-m-d h:i:s', strtotime($log['created_at'])) }}
                                    <br>
                                    <hr>
                                @endforeach
                            @else
                                {{-- If the authenticated/logged-in user is 'vendor', restrict the "Update Order Status" feature --}}
                                This feature is restricted.
                            @endif

                        </div>
                    </div>
                </div>

                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Ordered Products</h4>

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
                                        <th>Item Total</th>
                                        <th>Commission</th>
                                        <th>Final Amount</th>
                                        <th>Payout Status</th>
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
                                                $total_price = $originalTotalPrice - $appliedDistributedDiscount;
                                            @endphp
                                            <td>₹{{ round($appliedDistributedDiscount, 2) }}</td>
                                            <td>₹{{ round($total_price, 2) }}</td>

                                            @if ($product['vendor_id'] > 0)
                                                @php $commission = round(($total_price * $product['commission']) / 100, 2); @endphp
                                                <td>₹{{ $commission }}</td>
                                                <td>₹{{ round($total_price - $commission, 2) }}</td>
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
                                            @else
                                                <td>₹0</td>
                                                <td>₹{{ round($total_price, 2) }}</td>
                                                <td>N/A</td>
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

                                            <td>                      <td>
                                                @if (Auth::guard('admin')->user()->can('update_order_item_status'))
                                                    <form action="{{ url('admin/update-order-item-status') }}"
                                                        method="post">
                                                        @csrf

                                                        <input type="hidden" name="order_item_id"
                                                            value="{{ $product['id'] }}">

                                                        <select id="order_item_status" name="order_item_status" required>
                                                            <option value="">Select</option>
                                                            @foreach ($orderItemStatuses as $status)
                                                                <option value="{{ $status['name'] }}"
                                                                    @if (!empty($product['item_status']) && $product['item_status'] == $status['name']) selected @endif>
                                                                    {{ $status['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <input style="width: 110px" type="text" name="item_courier_name"
                                                            id="item_courier_name" placeholder="Item Courier Name"
                                                            @if (!empty($product['courier_name'])) value="{{ $product['courier_name'] }}" @endif>
                                                        <input style="width: 110px" type="text"
                                                            name="item_tracking_number" id="item_tracking_number"
                                                            placeholder="Item Tracking Number"
                                                            @if (!empty($product['tracking_number'])) value="{{ $product['tracking_number'] }}" @endif> --}}
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

    @push('scripts')
    <script>
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
