@extends('front.layout.layout3')

@section('content')
    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm"
        style="background-image:url({{ asset('front/images/background/bg3.jpg') }});">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h1>Payment Successful</h1>
            </div>
        </div>
    </div>

    <div class="content-inner py-5">
        <div class="container text-center">
            <div class="card border-0 shadow-sm mx-auto p-5" style="max-width: 600px;">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                </div>
                <h2 class="text-success mb-3">Order Placed Successfully!</h2>
                <p class="lead mb-4">Thank you for your payment. Your order has been confirmed.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ url('/') }}" class="btn btn-primary">Continue Shopping</a>
                    <a href="{{ url('/student/orders') }}" class="btn btn-outline-secondary">My Orders</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @if (Session::has('cashback_amount'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Congratulations!',
                    html: 'You earned <b style="color: #28a745;">₹{{ number_format(Session::get('cashback_amount'), 2) }}</b> cashback in your wallet!',
                    icon: 'success',
                    confirmButtonText: 'Great!',
                    confirmButtonColor: '#28a745'
                });
            });
        </script>
    @endif
@endsection
