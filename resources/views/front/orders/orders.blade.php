{{-- This page is rendered by orders() method inside Front/OrderController.php (depending on if the order id Optional Parameter (slug) is passed in or not) --}}


@extends('front.layout.layout3')



@section('content')
    <!-- Page Introduction Wrapper -->
    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h1>My Orders</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-row">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}"> Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('user/orders') }}">Orders</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <br><br>

    <!-- Page Introduction Wrapper /- -->
    <!-- Cart-Page -->
    <div class="page-cart u-s-p-t-80">
        <div class="container">
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-danger">
                            <tr>
                                <th>Order ID</th>
                                <th>Ordered Products</th> 
                                <th>Order Status</th>
                                <th>Payment Method</th>
                                <th>Grand Total</th>
                                <th>Created on</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ url('user/orders/' . $order['id']) }}">{{ $order['id'] }}</a>
                                    </td>
                                    <td> {{-- We'll display products names, comma-separated --}}
                                        @foreach ($order['orders_products'] as $product)
                                            {{ $product['product_name'] }}@if(!$loop->last), @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @php
                                            $status = strtolower($order['order_status']);
                                            $badgeClass = 'badge-secondary';
                                            
                                            if (strpos($status, 'pending') !== false) {
                                                $badgeClass = 'badge-warning';
                                            } elseif (strpos($status, 'shipped') !== false || strpos($status, 'delivered') !== false) {
                                                $badgeClass = 'badge-success';
                                            } elseif (strpos($status, 'cancel') !== false) {
                                                $badgeClass = 'badge-danger';
                                            } elseif (strpos($status, 'progress') !== false || strpos($status, 'processing') !== false) {
                                                $badgeClass = 'badge-info';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}" style="padding: 6px 12px; font-size: 12px;">
                                            {{ $order['order_status'] }}
                                        </span>
                                    </td>
                                    <td>{{ $order['payment_method'] }}</td>
                                    <td>{{ $order['grand_total'] }}</td>
                                    <td>{{ date('Y-m-d h:i:s', strtotime($order['created_at'])) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart-Page /- -->
@endsection
