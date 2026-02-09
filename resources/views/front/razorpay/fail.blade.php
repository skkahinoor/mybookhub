@extends('front.layout.layout3')

@section('content')
    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm"
        style="background-image:url({{ asset('front/images/background/bg3.jpg') }});">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h1>Payment Failed</h1>
            </div>
        </div>
    </div>

    <div class="content-inner py-5">
        <div class="container text-center">
            <div class="card border-0 shadow-sm mx-auto p-5" style="max-width: 600px;">
                <div class="mb-4">
                    <i class="fas fa-times-circle text-danger" style="font-size: 80px;"></i>
                </div>
                <h2 class="text-danger mb-3">Transaction Failed</h2>
                <p class="lead mb-4">We couldn't process your payment. Please try again or use a different payment method.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ url('/checkout') }}" class="btn btn-warning text-white">Retry Payment</a>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
@endsection
