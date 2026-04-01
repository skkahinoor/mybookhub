@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">Order #{{ $orderDetails->id }}</h4>
                            <p class="text-muted mb-0">Placed on {{ $orderDetails->created_at->format('F d, Y h:i A') }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            @php
                                $firstVendorId = collect($orderDetails->orders_products ?? [])
                                    ->pluck('vendor_id')
                                    ->filter(fn($v) => (int) $v > 0)
                                    ->first();
                                $vendorDetails = $firstVendorId
                                    ? \App\Models\VendorsBusinessDetail::where('vendor_id', $firstVendorId)->first()
                                    : null;
                                $mapsQuery = null;
                                if ($vendorDetails) {
                                    $mapsQuery = trim(implode(', ', array_filter([
                                        $vendorDetails->shop_name,
                                        $vendorDetails->shop_address,
                                        $vendorDetails->shop_city,
                                        $vendorDetails->shop_state,
                                        $vendorDetails->shop_country,
                                        $vendorDetails->shop_pincode,
                                    ])));
                                }
                            @endphp
                            @if ($orderDetails->payment_gateway === 'PICKUP' && $mapsQuery)
                                <a class="btn btn-outline-primary"
                                    href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapsQuery) }}"
                                    target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-location-arrow me-2"></i> Directions
                                </a>
                            @endif
                            <a href="{{ route('student.orders.index') }}" class="btn btn-secondary"
                                style="background-color: rgb(63, 61, 61); color: white;">
                                <i class="fas fa-arrow-circle-left me-2"></i> Back to Orders
                            </a>
                        </div>

                    </div>
                </div>
            </div>


            <div class="row">
                <!-- Left Column - Order Details -->
                <div class="col-lg-8">
                    <!-- Order Status Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Order Status</h5>
                            @php
                                $status = strtolower($orderDetails->order_status);
                                $badgeClass = 'badge-secondary';

                                if (strpos($status, 'pending') !== false) {
                                    $badgeClass = 'badge-warning';
                                } elseif (
                                    strpos($status, 'shipped') !== false ||
                                    strpos($status, 'delivered') !== false
                                ) {
                                    $badgeClass = 'badge-success';
                                } elseif (strpos($status, 'cancel') !== false) {
                                    $badgeClass = 'badge-danger';
                                } elseif (
                                    strpos($status, 'progress') !== false ||
                                    strpos($status, 'processing') !== false
                                ) {
                                    $badgeClass = 'badge-info';
                                }
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge {{ $badgeClass }} p-3" style="font-size: 14px;">
                                    {{ $orderDetails->order_status }}
                                </span>
                            </div>

                            <!-- Order Timeline -->
                            <div class="order-timeline" style="position: relative; padding-left: 30px;">
                                <div class="timeline-item" style="position: relative; padding-bottom: 20px;">
                                    <div
                                        style="position: absolute; left: -24px; top: 4px; width: 16px; height: 16px; background: #28a745; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px #28a745;">
                                    </div>
                                    <div>
                                        <strong>Order Placed</strong>
                                        <p class="text-muted mb-0 small">
                                            {{ $orderDetails->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                @if (strpos($status, 'shipped') !== false || strpos($status, 'delivered') !== false)
                                    <div class="timeline-item" style="position: relative; padding-bottom: 20px;">
                                        <div
                                            style="position: absolute; left: -24px; top: 4; width: 16px; height: 16px; background: #28a745; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px #28a745;">
                                        </div>
                                        <div>
                                            <strong>Shipped</strong>
                                            <p class="text-muted mb-0 small">
                                                {{ $orderDetails->updated_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if (strpos($status, 'delivered') !== false)
                                    <div class="timeline-item" style="position: relative;">
                                        <div
                                            style="position: absolute; left: -24px; top: 4; width: 16px; height: 16px; background: #28a745; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px #28a745;">
                                        </div>
                                        <div>
                                            <strong>Delivered</strong>
                                            <p class="text-muted mb-0 small">
                                                {{ $orderDetails->updated_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="timeline-item" style="position: relative;">
                                        <div
                                            style="position: absolute; left: -24px; top: 4; width: 16px; height: 16px; background: #e0e0e0; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px #e0e0e0;">
                                        </div>
                                        <div>
                                            <strong class="text-muted">Delivered</strong>
                                            <p class="text-muted mb-0 small">Pending</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Products Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Order Items</h5>
                            @foreach ($orderDetails->orders_products as $product)
                                <div class="d-flex align-items-center mb-4 pb-4"
                                    style="border-bottom: 1px solid #e5e5e5;">
                                    @php
                                        $productImage = \App\Models\Product::getProductImage($product->product_id);
                                    @endphp
                                    <div style="position: relative;">
                                        <img src="{{ asset('front/images/product_images/small/' . $productImage) }}"
                                            alt="{{ $product->product_name }}"
                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-right: 20px;">
                                        <span class="badge badge-primary" style="position: absolute; top: -10px; left: 0; font-size: 10px;">
                                            {{ $product->item_status ?? 'Ordered' }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $product->product_name }}</h6>
                                        <p class="mb-1" style="font-size: 11px;">
                                            <span class="text-muted">Vendor:</span>
                                            @php
                                                $vendor = \App\Models\Vendor::where('id', $product->vendor_id)->first();
                                                $shopName = $vendor ? $vendor->shop_name : 'Admin';
                                            @endphp
                                            <span class="badge badge-light text-dark border">{{ $shopName }}</span>
                                        </p>
                                        <p class="text-muted mb-1" style="font-size: 13px;">Quantity: {{ $product->product_qty }}</p>
                                        <p class="text-muted mb-0" style="font-size: 13px;">Price:
                                            ₹{{ number_format($product->product_price, 2) }}</p>
                                        @if ($product->courier_name)
                                            <p class="text-info mb-0 mt-2">
                                                <small><i class="fas fa-truck me-1"></i> {{ $product->courier_name }} | Tracking:
                                                    {{ $product->tracking_number }}</small>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <h5 class="mb-0">
                                            ₹{{ number_format($product->product_price * $product->product_qty, 2) }}
                                        </h5>
                                        @if (strpos(strtolower($orderDetails->order_status), 'cancel') === false)
                                            <button type="button" class="btn btn-sm btn-outline-warning mt-2 raise-query-btn" 
                                                data-order-id="{{ $orderDetails->id }}" 
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->product_name }}">
                                                Raise a Query
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Delivery Address Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Delivery Address</h5>
                            <div class="address-box" style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <p class="mb-1"><strong>{{ $orderDetails->name }}</strong></p>
                                <p class="mb-1">{{ $orderDetails->address }}</p>
                                <p class="mb-1">{{ $orderDetails->city }}, {{ $orderDetails->state }}</p>
                                <p class="mb-1">{{ $orderDetails->country }} - {{ $orderDetails->pincode }}</p>
                                <p class="mb-0"><strong>Mobile:</strong> {{ $orderDetails->mobile }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Summary & Charts -->
                <div class="col-lg-4">
                    <!-- Order Summary Card -->
                    <div class="card mb-4" style="position: sticky; top: 20px;">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>₹{{ number_format($orderDetails->grand_total - $orderDetails->shipping_charges + ($orderDetails->wallet_amount ?? 0) + ($orderDetails->coupon_amount ?? 0), 2) }}</span>
                            </div>
                            @if ($orderDetails->coupon_code)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Coupon ({{ $orderDetails->coupon_code }}):</span>
                                    <span
                                        class="text-success">-₹{{ number_format($orderDetails->coupon_amount ?? 0, 2) }}</span>
                                </div>
                            @endif
                            @if ($orderDetails->wallet_amount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Wallet Deduction:</span>
                                    <span
                                        class="text-success">-₹{{ number_format($orderDetails->wallet_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>₹{{ number_format($orderDetails->shipping_charges, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong
                                    style="font-size: 18px; color: #28a745;">₹{{ number_format($orderDetails->grand_total, 2) }}</strong>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <small class="text-muted">Payment Method:</small>
                                <p class="mb-0"><strong>{{ $orderDetails->payment_method }}</strong></p>
                            </div>
                            @if ($orderDetails->courier_name)
                                <div class="mb-2">
                                    <small class="text-muted">Courier:</small>
                                    <p class="mb-0"><strong>{{ $orderDetails->courier_name }}</strong></p>
                                </div>
                            @endif
                            @if ($orderDetails->tracking_number)
                                <div>
                                    <small class="text-muted">Tracking Number:</small>
                                    <p class="mb-0"><strong>{{ $orderDetails->tracking_number }}</strong></p>
                                </div>
                            @endif

                            <div class="mt-4">
                                @php
                                    $showReturn = false;
                                    if ($orderDetails->order_status == 'Delivered' && $orderDetails->delivered_at) {
                                        $deliveredAt = \Carbon\Carbon::parse($orderDetails->delivered_at);
                                        if (!$deliveredAt->addDays(7)->isPast() && empty($orderDetails->return_status)) {
                                            $showReturn = true;
                                        }
                                    }
                                @endphp

                                @if (in_array($orderDetails->order_status, ['New', 'Pending', 'Paid']) && empty($orderDetails->return_status))
                                    <a href="{{ route('student.orders.cancel', $orderDetails->id) }}"
                                        class="btn btn-danger w-100 mb-2"
                                        onclick="return confirm('Are you sure you want to cancel this order?')">
                                        Cancel Order
                                    </a>
                                @elseif($showReturn)
                                    <button type="button" class="btn btn-warning w-100 mb-2" data-toggle="modal" data-target="#returnModal">
                                        Return Order
                                    </button>
                                @endif

                                @if (!empty($orderDetails->return_status))
                                    <div class="alert alert-info py-2 px-3 mb-2" style="font-size: 13px;">
                                        <strong>Return Status:</strong> {{ $orderDetails->return_status }}
                                    </div>
                                @endif

                                @if ($orderDetails->order_status == 'Pending' && in_array($orderDetails->payment_gateway, ['Razorpay', 'PICKUP']))
                                    <a href="{{ route('student.orders.payNow', $orderDetails->id) }}"
                                        class="btn btn-success w-100">
                                        Pay Online
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Return Order Modal -->
                    <div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('student.orders.return', $orderDetails->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="returnModalLabel">Return Order #{{ $orderDetails->id }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning small">
                                            <i class="fas fa-info-circle mr-1"></i> You can return this item within 7 days of delivery.
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="return_reason">Reason for Return <span class="text-danger">*</span></label>
                                            <select name="return_reason" class="form-control" required>
                                                <option value="">Select Reason</option>
                                                <option value="Damaged Product">Damaged Product</option>
                                                <option value="Wrong Product Delivered">Wrong Product Delivered</option>
                                                <option value="Quality Issue">Quality Issue</option>
                                                <option value="Not as described">Not as described</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="return_comment">Comments (Optional)</label>
                                            <textarea name="return_comment" class="form-control" rows="3" placeholder="Additional details..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit Return Request</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>

                    {{-- <!-- Spending Chart Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Monthly Spending</h5>
                            <div style="position: relative; height: 250px;">
                                <canvas id="spendingChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Pie Chart -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Order Statistics</h5>
                            <div style="position: relative; height: 250px;">
                                <canvas id="orderStatusChart"></canvas>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Raise Query Modal -->
<div class="modal fade" id="queryModal" tabindex="-1" role="dialog" aria-labelledby="queryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('student.orders.raise-query') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="queryModalLabel">Raise a Query</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="query_order_id">
                    <input type="hidden" name="order_product_id" id="query_product_id">
                    
                    <div class="form-group mb-3">
                        <label>Product</label>
                        <input type="text" id="query_product_name" class="form-control" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label for="subject">Subject <span class="text-danger">*</span></label>
                        <select name="subject" class="form-control" required>
                            <option value="">Select Subject</option>
                            <option value="Damaged Product">Damaged Product</option>
                            <option value="Wrong Product Delivered">Wrong Product Delivered</option>
                            <option value="Missing Item">Missing Item</option>
                            <option value="Quality Issue">Quality Issue</option>
                            <option value="Delay in Delivery">Delay in Delivery</option>
                            <option value="Payment Issue">Payment Issue</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Describe your issue in detail..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Query</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('user.layout.footer')
    </div>
    <!-- plugins:js -->
    <script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('user/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('user/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('user/js/off-canvas.js') }}"></script>
    <script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('user/js/template.js') }}"></script>
    <script src="{{ asset('user/js/settings.js') }}"></script>
    <script src="{{ asset('user/js/todolist.js') }}"></script>
    <!-- endinject -->

<script>
$(document).ready(function() {
    $('.raise-query-btn').on('click', function() {
        const orderId = $(this).data('order-id');
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');

        $('#query_order_id').val(orderId);
        $('#query_product_id').val(productId);
        $('#query_product_name').val(productName);
        
        $('#queryModal').modal('show');
    });
});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

{{-- <script>
    // Monthly Spending Line Chart
    const spendingCtx = document.getElementById('spendingChart').getContext('2d');
    const spendingChart = new Chart(spendingCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
            datasets: [{
                label: 'Spending (₹)',
                data: {!! json_encode(array_column($monthlyData, 'amount')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Order Status Pie Chart
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Delivered', 'Other'],
            datasets: [{
                data: [{{ $pendingOrders }}, {{ $deliveredOrders }}, {{ $totalOrders - $pendingOrders - $deliveredOrders }}],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script> --}}

<style>
    .order-timeline::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .card {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 8px;
    }

    .card-title {
        color: #333;
        font-weight: 600;
    }
</style>
