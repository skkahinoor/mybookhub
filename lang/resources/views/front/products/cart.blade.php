{{-- Card-Based Modern Layout --}}
@extends('front.layout.layout3')

@section('content')
    <!-- inner page banner -->
    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h1>Cart</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-row">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item">Cart</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- inner page banner End-->

    <!-- contact area -->
    <section class="content-inner shop-account">
        <!-- Product -->
        <div class="container">
            @if(count($getCartItems) > 0)
                <div class="row mb-5">
                    <div class="col-lg-12">
                        <div id="appendCartItems">
                            @include('front.products.cart_items')
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="widget">
                            <form class="shop-form" id="applyCoupon">
                                <h4 class="widget-title">Apply Coupon</h4>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="code" placeholder="Enter Coupon Code" required>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btnhover" type="submit">Apply Coupon</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="widget">
                            <h4 class="widget-title">Cart Summary</h4>
                            <table class="table-bordered check-tbl m-b25">
                                <tbody>
                                    <tr>
                                        <td>Order Subtotal</td>
                                        <td>₹<span id="subtotal">{{ $total_price ?? 0 }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>Shipping</td>
                                        <td>Free Shipping</td>
                                    </tr>
                                    <tr>
                                        <td>Coupon Discount</td>
                                        <td>₹<span id="couponDiscount">{{ number_format((float) \Illuminate\Support\Facades\Session::get('couponAmount', 0), 2) }}</span>
                                            <script>
                                                (function(){
                                                    const fmt2 = n => (Number(n) || 0).toFixed(2);
                                                    const el = document.getElementById('couponDiscount');
                                                    if (el && el.textContent) {
                                                        el.textContent = fmt2(el.textContent);
                                                    }
                                                })();
                                            </script></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong>₹<span id="grandTotal">{{ number_format((($total_price ?? 0) - \Illuminate\Support\Facades\Session::get('couponAmount', 0)), 2) }}</span></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group m-b25">
                                <a href="{{ route('checkout') }}" class="btn btn-primary btnhover">Proceed to Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center py-5">
                            <h3>Your cart is empty!</h3>
                            <p>Add some products to your cart to continue shopping.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary btnhover">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- Product END -->
    </section>
    <!-- contact area End-->

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Apply coupon form submission
            $('#applyCoupon').on('submit', function(e) {
                e.preventDefault();

                let couponCode = $(this).find('input[name="code"]').val();

                $.ajax({
                    url: '{{ route("applyCoupon") }}',
                    method: 'POST',
                    data: {
                        code: couponCode,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(resp) {
                        if (resp.status) {
                            // Update coupon discount with 2 decimals
                            const fmt = (n) => (Number(n) || 0).toFixed(2);
                            $('#couponDiscount').text(fmt(resp.couponAmount));
                            $('#grandTotal').text(fmt(resp.grand_total));

                            // Show success message
                            alert('Coupon applied successfully!');
                        } else {
                            alert(resp.message || 'Invalid coupon code!');
                        }

                        // Update cart view if provided
                        if (resp.view) {
                            $('#appendCartItems').html(resp.view);
                        }

                        // Update header cart view if provided
                        if (resp.headerview) {
                            $('.headerCartItems').html(resp.headerview);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Something went wrong. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection
