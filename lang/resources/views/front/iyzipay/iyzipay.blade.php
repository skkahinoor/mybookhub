{{-- This page is rendered by iyzipay() method inside Front/IyzipayController.php --}}


@extends('front.layout.layout3')



@section('content')

    <style>
        .button {
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        }

        .button1 {background-color: #4CAF50;} /* Green */
        .button2 {background-color: #008CBA;} /* Blue */
    </style>



    <!-- Page Introduction Wrapper -->

    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h1>Payment</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-row">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Proceed to Payment</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div><br><br>
    <!-- Page Introduction Wrapper /- -->
    <!-- Cart-Page -->
    <div class="page-cart u-s-p-t-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12" align="center">
                    <h3>PLEASE MAKE <span style="color: red">INR {{ Session::get('grand_total') }}</span> PAYMENT FOR YOUR ORDER</h3>
                    <a href="{{ url('iyzipay/pay') }}">
                        <button class="button button2">Pay Now</button> {{-- Interacting With The Session: Retrieving Data: https://laravel.com/docs/9.x/session#retrieving-data --}}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart-Page /- -->
@endsection
