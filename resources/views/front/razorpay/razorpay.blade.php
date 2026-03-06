@extends('front.layout.layout3')

@section('content')

<div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm"
    style="background-image:url({{ asset('front/images/background/bg3.jpg') }});">

    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1>Review & Pay</h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item">Payment</li>
                </ul>
            </nav>
        </div>
    </div>

</div>


<div class="content-inner py-4">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-6 text-center">

                <div class="card shadow-sm border-0">

                    <div class="card-body p-5">

                        <h3 class="mb-4">Total Amount: ₹{{ number_format($grand_total,2) }}</h3>

                        <p class="text-muted mb-4">
                            Please complete your payment securely using Razorpay.
                        </p>

                        <button id="rzp-button1" class="btn btn-primary btn-lg w-100">
                            Pay with Razorpay
                        </button>

                        <form action="{{ route('razorpay.payment') }}" method="POST" id="razorpay-form">

                            @csrf

                            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">

                            <input type="hidden" name="razorpay_signature" id="razorpay_signature">

                            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $order->razorpay_order_id }}">

                            <input type="hidden" name="totalAmount" value="{{ $grand_total }}">

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>

var options = {

    key: "{{ env('RAZORPAY_KEY_ID') }}",

    amount: "{{ $grand_total * 100 }}",

    currency: "INR",

    name: "BookHub",

    description: "Order Payment #{{ $order->id }}",

    image: "{{ asset('front/images/logo.png') }}",

    order_id: "{{ $order->razorpay_order_id }}",

    handler: function (response) {

        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;

        document.getElementById('razorpay_signature').value = response.razorpay_signature;

        document.getElementById('razorpay_order_id').value = response.razorpay_order_id;

        document.getElementById('razorpay-form').submit();

    },

    prefill: {

        name: "{{ Auth::user()->name }}",

        email: "{{ Auth::user()->email }}",

        contact: "{{ Auth::user()->phone }}"

    },

    theme: {

        color: "#3399cc"

    }

};

var rzp1 = new Razorpay(options);

document.getElementById('rzp-button1').onclick = function(e){

    e.preventDefault();

    rzp1.open();

};

</script>

@endsection
