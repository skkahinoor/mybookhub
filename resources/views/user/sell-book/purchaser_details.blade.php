@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">Purchaser Details</h4>
                                <a href="{{ route('student.sell-book.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Back to My Books
                                </a>
                            </div>

                            <div class="row">
                                <!-- Book Details -->
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title text-primary">Book Details</h5>
                                            <hr>
                                            <div class="d-flex mb-3">
                                                <div class="mr-3">
                                                    @if(!empty($attribute->user_old_book_image))
                                                        <img src="{{ asset('front/images/product_images/small/'.$attribute->user_old_book_image) }}" alt="image" style="width:100px; height:130px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <img src="{{ asset('front/images/product_images/small/no-image.png') }}" alt="image" style="width:100px; height:130px; object-fit: cover; border-radius: 4px;">
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6>{{ $attribute->product->product_name }}</h6>
                                                    <p class="mb-1"><strong>ISBN:</strong> {{ $attribute->product->product_isbn }}</p>
                                                    <p class="mb-1"><strong>Category:</strong> {{ $attribute->product->category->category_name }}</p>
                                                    <p class="mb-1"><strong>Selling Price:</strong> ₹{{ number_format($attribute->user_product_price, 2) }}</p>
                                                    <span class="badge badge-secondary">Sold Out</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purchaser Details -->
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title text-success">Purchaser Info</h5>
                                            <hr>
                                            <div class="mb-3">
                                                <p class="mb-1"><strong>Name:</strong> {{ $order->name }}</p>
                                                <p class="mb-1"><strong>Phone:</strong> {{ $order->mobile }}</p>
                                                <p class="mb-1"><strong>Email:</strong> {{ $order->email }}</p>
                                            </div>
                                            
                                            <h5 class="card-title text-info mt-4">Delivery Location</h5>
                                            <hr>
                                            <div class="mb-3">
                                                <p class="mb-1"><strong>Address:</strong> {{ $order->address }}</p>
                                                <p class="mb-1"><strong>City:</strong> {{ $order->city }}</p>
                                                <p class="mb-1"><strong>State:</strong> {{ $order->state }}</p>
                                                <p class="mb-1"><strong>Pincode:</strong> {{ $order->pincode }}</p>
                                            </div>

                                            @php
                                                $fullAddress = $order->address . ', ' . $order->city . ', ' . $order->state . ' ' . $order->pincode;
                                                $mapUrl = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode($fullAddress);
                                            @endphp

                                            <a href="{{ $mapUrl }}" target="_blank" class="btn btn-primary btn-block mt-3">
                                                <i class="mdi mdi-directions"></i> Get Directions
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">Order Information</h5>
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <td>#{{ $order->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Order Date</th>
                                                    <td>{{ date('d M Y, h:i A', strtotime($order->created_at)) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Payment Method</th>
                                                    <td>{{ $order->payment_method }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Order Status</th>
                                                    <td>
                                                        <span class="badge badge-info">{{ $order->order_status }}</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-primary">
                                        <div class="card-body">
                                            <h5 class="card-title text-primary">Earnings & Payout</h5>
                                            <div class="p-3 bg-light rounded">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Your Listing Price:</span>
                                                    <strong>₹{{ number_format($attribute->user_product_price, 2) }}</strong>
                                                </div>
                                                {{-- <div class="d-flex justify-content-between mb-2 text-muted">
                                                    <span style="font-size: 0.9rem;">Platform Fee (Buyer Paid):</span>
                                                    <span style="font-size: 0.9rem;">₹{{ number_format($orderProduct->commission, 2) }}</span>
                                                </div> --}}
                                                <hr>
                                                <div class="d-flex justify-content-between mb-3">
                                                    <span class="h6 mb-0">Total Payout:</span>
                                                    <strong class="h5 mb-0 text-primary">₹{{ number_format($netPayout, 2) }}</strong>
                                                </div>

                                                <div class="mt-3">
                                                    <p class="mb-1"><strong>Status:</strong> 
                                                        @if($orderProduct->vendor_payout_status == 'Released')
                                                            <span class="badge badge-success">Released</span>
                                                        @else
                                                            <span class="badge badge-warning">Pending Release</span>
                                                        @endif
                                                    </p>
                                                    @if($orderProduct->vendor_payout_status == 'Released')
                                                        <div class="alert alert-success p-2 mt-2" style="font-size: 13px;">
                                                            <strong>Note from Admin:</strong><br>
                                                            {{ $orderProduct->vendor_payout_note ?? 'Payment has been processed.' }}
                                                        </div>
                                                    @else
                                                        <p class="text-muted small mt-2">
                                                            <i class="mdi mdi-information-outline mr-1"></i>
                                                            Admin will release the payout after verifying the delivery.
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')
