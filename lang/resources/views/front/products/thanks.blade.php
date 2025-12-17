{{-- This page is rendered by the thanks() method inside Front/ProductsController.php --}}
@extends('front.layout.layout3')


@section('content')
    <!-- Page Introduction Wrapper -->
    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h1>Thank You</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-row">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}"> Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/thanks') }}">thanks</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- Page Introduction Wrapper /- -->
    <!-- Thanks Section -->
    <section class="content-inner-1 py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="thankyou-card text-center">
                        <div class="icon-wrap">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47714 17.5228 2 12 2C6.47714 2 2 6.47714 2 12C2 17.5228 6.47714 22 12 22Z" fill="#e7f5e9"/>
                                <path d="M9.00002 12.5L11 14.5L15 10.5" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h2 class="title mb-2">Your order is confirmed!</h2>
                        <p class="subtitle mb-4">Thank you for shopping with us. A confirmation has been sent to your email.</p>

                        <div class="summary shadow-sm">
                            <div class="row g-0">
                                <div class="col-md-6 summary-item">
                                    <span class="label">Order No.</span>
                                    <span class="value">#{{ Session::get('order_id') }}</span>
                                </div>
                                <div class="col-md-6 summary-item">
                                    <span class="label">Grand Total</span>
                                    <span class="value">₹{{ number_format(Session::get('grand_total'), 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="actions mt-4 d-flex gap-2 justify-content-center">
                            <a href="{{ url('/') }}" class="btn btn-primary btnhover"><i class="fa fa-home me-2"></i>Continue Shopping</a>
                            <a href="{{ url('user/orders') }}" class="btn btn-outline-primary"><i class="fa fa-file-invoice me-2"></i>View Orders</a>
                        </div>

                        <div class="tips mt-4">
                            <small class="text-muted">Need help? <a href="{{ url('contact') }}" class="text-primary">Contact support</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .thankyou-card{background:#fff;border:1px solid #f0f0f0;border-radius:16px;padding:32px 24px;position:relative;overflow:hidden}
            .thankyou-card::after{content:"";position:absolute;right:-60px;bottom:-60px;width:180px;height:180px;background:radial-gradient( circle at center, rgba(0,123,255,.15), rgba(0,123,255,0) 70%);border-radius:50%}
            .icon-wrap{width:88px;height:88px;border-radius:50%;background:#f0fff4;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;border:2px solid #dcfce7}
            .title{font-weight:700}
            .subtitle{color:#6c757d}
            .summary{background:#fff;border-radius:12px;overflow:hidden}
            .summary-item{padding:16px 20px;border-right:1px solid #f1f3f5}
            .summary-item:last-child{border-right:0}
            .summary .label{display:block;color:#6c757d;font-size:12px;margin-bottom:4px}
            .summary .value{display:block;font-weight:700;color:#212529}
            @media(max-width:576px){.summary-item{border-right:0;border-bottom:1px solid #f1f3f5}.summary-item:last-child{border-bottom:0}}
        </style>
    </section>
@endsection



{{-- Forget/Remove some data in the Session after making/placing the order --}}
@php
    use Illuminate\Support\Facades\Session;

    Session::forget('grand_total');  // Deleting Data: https://laravel.com/docs/9.x/session#deleting-data
    Session::forget('order_id');     // Deleting Data: https://laravel.com/docs/9.x/session#deleting-data
    Session::forget('couponCode');   // Deleting Data: https://laravel.com/docs/9.x/session#deleting-data
    Session::forget('couponAmount'); // Deleting Data: https://laravel.com/docs/9.x/session#deleting-data
@endphp
