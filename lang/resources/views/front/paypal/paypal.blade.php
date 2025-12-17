{{-- This page is rendered by paypal() method inside Front/PaypalController.php --}}
@extends('front.layout.layout3')


@section('content')
    <!-- Page Introduction Wrapper -->
    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h2 style="color: #fff;">Cart</h2>
                <nav aria-label="breadcrumb" class="breadcrumb-row">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Proceed to Payment</a></li>
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
                <div class="col-lg-12" align="center">
                    <h3>PLEASE MAKE PAYMENT FOR YOUR ORDER</h3>
                    <form action="{{ url('/payment') }}" method="post"> {{-- This is a Named Route. Check web.php --}} {{-- Generating URLs To Named Routes: https://laravel.com/docs/9.x/routing#generating-urls-to-named-routes --}}
                        @csrf {{-- Preventing CSRF Requests: https://laravel.com/docs/9.x/csrf#preventing-csrf-requests --}}

                        <input type="hidden" name="amount" value="{{ round(Session::get('grand_total') / 80, 2) }}"> {{-- 'grand_total' was stored in Session in checkout() method in Front/ProductsController.php --}} {{-- Interacting With The Session: Retrieving Data: https://laravel.com/docs/9.x/session#retrieving-data --}} {{-- Note: PayPal accepts world major currencies ONLY, so we divided INR by 80 to convert INR to USD --}}
                        <input type="image" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-large.png">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart-Page /- -->
@endsection
