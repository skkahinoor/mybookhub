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

                            @if (Auth::guard('admin')->user()->type != 'vendor')
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

                                    <button type="submit" class="btn btn-secondary">Update</button>
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
                                        <th>Unit Price (after product discount)</th>
                                        <th>Product Qty</th>
                                        <th>Total Price</th>
                                        @if (\Illuminate\Support\Facades\Auth::guard('admin')->user()->type != 'vendor')
                                            <th>Product by</th>
                                        @endif
                                        <th>Coupon/Extra Discount (Distributed)</th>
                                        <th>Total Price</th>
                                        <th>Commission</th>
                                        <th>Final Amount</th>
                                        <th>Item Status</th>
                                    </tr>

                                    @foreach ($orderDetails['orders_products'] as $product)
                                        <tr>
                                            <td>
                                                @php
                                                    $getProductImage = \App\Models\Product::getProductImage(
                                                        $product['product_id'],
                                                    );
                                                @endphp

                                                <a target="_blank" href="{{ url('product/' . $product['product_id']) }}">
                                                    <img
                                                        src="{{ asset('front/images/product_images/small/' . $getProductImage) }}">
                                                </a>
                                            </td>
                                            {{-- <td>{{ $product['product_code'] }}</td> --}}
                                            <td>{{ $product['product_name'] }}</td>
                                            {{-- Original MSRP and Discount --}}
                                            <td>
                                                @if (isset($product['product']['product_price']))
                                                    ₹{{ $product['product']['product_price'] }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $prodAttr = collect($product['product']['attributes'] ?? [])
                                                        ->where('vendor_id', $product['vendor_id'])
                                                        ->first();
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
                                                @if ($product['vendor_id'] > 0)
                                                    {{-- if the product belongs to a 'vendor' --}}
                                                    <td>
                                                        <a href="/admin/view-vendor-details/{{ $product['admin_id'] }}"
                                                            target="_blank">Vendor</a>
                                                    </td>
                                                @else
                                                    <td>Admin</td>
                                                @endif
                                            @endif

                                            @php
                                                $originalTotalPrice =
                                                    $product['product_price'] * $product['product_qty'];
                                            @endphp

                                            <td>
                                                @if ($product['vendor_id'] > 0)
                                                    {{-- if the product belongs to a 'vendor', not 'admin' --}}
                                                    @php
                                                        $hasCoupon =
                                                            !empty($orderDetails['coupon_amount']) &&
                                                            $orderDetails['coupon_amount'] > 0;
                                                        $hasExtraDiscount =
                                                            !empty($orderDetails['extra_discount']) &&
                                                            $orderDetails['extra_discount'] > 0;
                                                        $appliedDistributedDiscount = 0;
                                                    @endphp

                                                    @if ($hasCoupon || $hasExtraDiscount)
                                                        @if ($hasCoupon)
                                                            @if (\App\Models\Coupon::couponDetails($orderDetails['coupon_code'])['vendor_id'] > 0)
                                                                @php $appliedDistributedDiscount = $item_discount; @endphp
                                                            @elseif ($hasExtraDiscount)
                                                                @php $appliedDistributedDiscount = $orderDetails['extra_discount'] / ($total_items ?: 1); @endphp
                                                            @endif
                                                        @elseif ($hasExtraDiscount)
                                                            @php $appliedDistributedDiscount = $item_discount; @endphp
                                                        @endif
                                                    @endif
                                                    ₹{{ round($appliedDistributedDiscount, 2) }}
                                                    @php $total_price = $originalTotalPrice - $appliedDistributedDiscount; @endphp
                                                @else
                                                    {{-- if the product belongs to an 'admin', not 'vendor' --}}
                                                    ₹{{ round($item_discount, 2) }}
                                                    @php $total_price = $originalTotalPrice - $item_discount; @endphp
                                                @endif
                                            </td>

                                            <td>₹{{ round($total_price, 2) }}</td>

                                            @if ($product['vendor_id'] > 0)
                                                <td>₹{{ $commission = round(($total_price * $product['commission']) / 100, 2) }}
                                                </td>
                                                <td>₹{{ round($total_price - $commission, 2) }}</td>
                                            @else
                                                <td>₹0</td>
                                                <td>₹{{ round($total_price, 2) }}</td>
                                            @endif
                                            <td>
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
                                                </form>
                                            </td>
                                            <td><button type="submit" class="btn btn-primary btn-sm">Update</button></td>
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
@endsection
