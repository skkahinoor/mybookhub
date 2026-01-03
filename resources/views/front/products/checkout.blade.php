{{-- Note: This page (view) is rendered by the checkout() method in the Front/ProductsController.php --}}
@extends('front.layout.layout3')

@section('content')

<div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1>Checkout</h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/checkout') }}">Checkout</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- inner page banner End-->

{{-- New UI static block removed in favor of functional checkout implementation below --}}

<!-- Checkout Progress Steps -->
<section class="content-inner py-4">
    <div class="container">
        <div class="checkout-steps">
            <div class="step active">
                <div class="circle">1</div>
                <div class="label">Address</div>
                        </div>
            <div class="separator"></div>
            <div class="step active">
                <div class="circle">2</div>
                <div class="label">Summary</div>
            </div>
            <div class="separator"></div>
            <div class="step">
                <div class="circle">3</div>
                <div class="label">Payment</div>
            </div>
            <div class="separator"></div>
            <div class="step">
                <div class="circle">4</div>
                <div class="label">Place Order</div>
            </div>
        </div>
    </div>
    <style>
        .checkout-steps{display:flex;align-items:center;justify-content:center;gap:16px}
        .checkout-steps .step{display:flex;flex-direction:column;align-items:center;gap:8px}
        .checkout-steps .circle{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;background:#e9ecef;color:#6c757d;border:2px solid #dee2e6}
        .checkout-steps .label{font-size:12px;color:#6c757d}
        .checkout-steps .step.active .circle{background:linear-gradient(135deg,#007bff,#0056b3);color:#fff;border-color:#007bff}
        .checkout-steps .step.active .label{color:#0056b3;font-weight:600}
        .checkout-steps .separator{height:2px;width:40px;background:#e9ecef}
        @media(max-width:576px){.checkout-steps{gap:8px}.checkout-steps .separator{width:24px}}
    </style>
</section>


{{-- old code  --}}
<!-- Page Introduction Wrapper -->
{{-- <div class="page-style-a">
    <div class="container">
        <div class="page-intro">
            <h2>Checkout</h2>
            <ul class="bread-crumb">
                <li class="has-separator">
                    <i class="ion ion-md-home"></i>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li class="is-marked">
                    <a href="{{ url('/checkout') }}">Checkout</a>
                </li>
            </ul>
        </div>
    </div>
</div> --}}
<!-- Page Introduction Wrapper /- -->

<!-- Checkout-Page -->
<div class="page-checkout u-s-p-t-80">
    <div class="container">
        {{-- Error Messages --}}
        @if (Session::has('error_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error:</strong> {{ Session::get('error_message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <!-- Delivery Addresses Section -->
                    <div class="col-lg-6" id="deliveryAddresses">
                        <div class="delivery-section">
                            <div class="section-header">
                                <h4><i class="fas fa-map-marker-alt"></i> Delivery Information</h4>
                            </div>
                            @include('front.products.delivery_addresses')
                        </div>
                    </div>

                    <!-- Order Summary & Payment -->
                    <div class="col-lg-6">
                        <form name="checkoutForm" id="checkoutForm" action="{{ url('/checkout') }}" method="post">
                            @csrf

                            <!-- Delivery Addresses Selection -->
                            @if (collect($deliveryAddresses)->count() > 0)
                                <div class="checkout-section">
                                    <div class="section-header">
                                        <h4><i class="fas fa-home"></i> Select Delivery Address</h4>
                                    </div>
                                    <div class="address-options">
                                        @foreach ($deliveryAddresses as $address)
                                            <div class="address-item">
                                                <div class="address-radio">
                                                    <input type="radio"
                                                           id="address{{ $address['id'] }}"
                                                           name="address_id"
                                                           value="{{ $address['id'] }}"
                                                           shipping_charges="{{ $address['shipping_charges'] }}"
                                                           total_price="{{ $total_price }}"
                                                           coupon_amount="{{ \Illuminate\Support\Facades\Session::get('couponAmount') }}"
                                                           codpincodeCount="{{ $address['codpincodeCount'] }}"
                                                           prepaidpincodeCount="{{ $address['prepaidpincodeCount'] }}">
                                                    <label for="address{{ $address['id'] }}" class="address-label">
                                                        <div class="address-info">
                                                            <h6>{{ $address['name'] }}</h6>
                                                            <p>{{ $address['address'] }}, {{ $address['city'] }}, {{ $address['state'] }}, {{ $address['country'] }}</p>
                                                            <span class="phone">📞 {{ $address['mobile'] }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="address-actions">
                                                    <a href="javascript:;" data-addressid="{{ $address['id'] }}" class="editAddress action-btn edit-btn">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="javascript:;" data-addressid="{{ $address['id'] }}" class="removeAddress action-btn remove-btn">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Order Summary -->
                            <div class="checkout-section">
                                <div class="section-header">
                                    <h4><i class="fas fa-shopping-cart"></i> Order Summary</h4>
                                </div>

                                <div class="order-summary sticky-summary">
                                    <!-- Products List -->
                                    <div class="products-list">
                                        @php $total_price = 0 @endphp
                                        @foreach ($getCartItems as $item)
                                            @php
                                                $getDiscountAttributePrice = \App\Models\Product::getDiscountAttributePrice($item['product_id'], $item['size']);
                                            @endphp
                                            <div class="product-item">
                                                <div class="product-info">
                                                    <img src="{{ asset('front/images/product_images/large/' . ($item['product']['product_image'] ?? 'no-image.png')) }}"
                                                                    alt="{{ $item['product']['product_name'] ?? 'Product' }}"
                                                                    class="img-fluid rounded shadow-sm"
                                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                                    <div class="product-details">
                                                        <h6>{{ $item['product']['product_name'] }}</h6>
                                                        <small>Size: {{ $item['size'] }} | Qty: {{ $item['quantity'] }}</small>
                                                    </div>
                                                </div>
                                                <div class="product-price">
                                                    ₹{{ $getDiscountAttributePrice['final_price'] * $item['quantity'] }}
                                                </div>
                                            </div>
                                            @php $total_price = $total_price + ($getDiscountAttributePrice['final_price'] * $item['quantity']) @endphp
                                        @endforeach
                                    </div>

                                    <!-- Price Breakdown -->
                                    <div class="price-breakdown">
                                        <div class="price-row">
                                            <span>Subtotal</span>
                                            <span>₹{{ $total_price }}</span>
                                        </div>
                                        <div class="price-row">
                                            <span>Shipping Charges</span>
                                            <span class="shipping_charges">₹0</span>
                                        </div>
                                        <div class="price-row">
                                            <span>Coupon Discount</span>
                                            <span>
                                                @if (\Illuminate\Support\Facades\Session::has('couponAmount'))
                                                <span id="couponDiscount">{{ number_format((float) \Illuminate\Support\Facades\Session::get('couponAmount', 0), 2) }}</span>
                                                @else
                                                    ₹0
                                                @endif
                                            </span>
                                                <script>
                                                    (function(){
                                                        const fmt2 = n => (Number(n) || 0).toFixed(2);
                                                        const el = document.getElementById('couponDiscount');
                                                        if (el && el.textContent) {
                                                            el.textContent = fmt2(el.textContent);
                                                        }
                                                    })();
                                                </script>
                                        </div>
                                        <div class="price-row total-row">
                                            <span><strong>Grand Total</strong></span>
                                            <span><strong class="grand_total">₹{{ $total_price - \Illuminate\Support\Facades\Session::get('couponAmount') }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Methods -->
                            <div class="checkout-section">
                                <div class="section-header">
                                    <h4><i class="fas fa-credit-card"></i> Payment Method</h4>
                                </div>

                                <div class="payment-methods">
                                    <div class="payment-option codMethod">
                                        <input type="radio" class="radio-box" name="payment_gateway" id="cash-on-delivery" value="COD">
                                        <label class="payment-label" for="cash-on-delivery">
                                            <div class="payment-info">
                                                <div class="payment-title">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                    <span>Cash on Delivery</span>
                                                </div>
                                                <small class="payment-note">Pay with cash upon delivery</small>
                                            </div>
                                            <div class="payment-badge">No extra fee</div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms & Conditions -->
                            <div class="checkout-section">
                                <div class="terms-section">
                                    <input type="checkbox" class="check-box" id="accept" name="accept" value="Yes" title="Please agree to T&C">
                                    <label class="terms-label" for="accept">
                                        I've read and accept the <a href="terms-and-conditions.html" class="terms-link">terms & conditions</a>
                                    </label>
                                </div>
                            </div>

                            <!-- Place Order Button -->
                            <div class="checkout-section">
                                <button type="submit" id="placeOrder" class="place-order-btn">                                                <i class="fas fa-lock"></i> Place Order Securely
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Checkout Page Styles */
.checkout-section {
    background: #fff;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: 1px solid #f0f0f0;
}

.section-header {
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.section-header h4 {
    color: #333;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

.section-header i {
    color: #007bff;
    margin-right: 8px;
}

/* Address Selection */
.address-options {
    space-y: 15px;
}

.address-item {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    position: relative;
}

.address-item:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}

.address-radio input[type="radio"] {
    position: absolute;
    top: 15px;
    left: 15px;
}

.address-label {
    cursor: pointer;
    padding-left: 35px;
    display: block;
    margin: 0;
}

.address-info h6 {
    color: #333;
    font-weight: 600;
    margin-bottom: 5px;
}

.address-info p {
    color: #666;
    margin-bottom: 5px;
    line-height: 1.4;
}

.address-info .phone {
    color: #007bff;
    font-size: 14px;
}

.address-actions {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    gap: 10px;
}

.action-btn {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.edit-btn {
    background: #28a745;
    color: white;
}

.edit-btn:hover {
    background: #218838;
    color: white;
}

.remove-btn {
    background: #dc3545;
    color: white;
}

.remove-btn:hover {
    background: #c82333;
    color: white;
}

/* Order Summary */
.order-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.sticky-summary { position: sticky; top: 90px; }

.products-list {
    margin-bottom: 20px;
}

.product-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #dee2e6;
}

.product-item:last-child {
    border-bottom: none;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.product-info img {
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.product-details h6 {
    margin: 0 0 5px 0;
    color: #333;
    font-weight: 500;
}

.product-details small {
    color: #666;
}

.product-price {
    font-weight: 600;
    color: #007bff;
}

/* Price Breakdown */
.price-breakdown {
    border-top: 1px solid #dee2e6;
    padding-top: 15px;
}

.price-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.total-row {
    border-top: 1px solid #dee2e6;
    padding-top: 10px;
    margin-top: 10px;
    font-size: 18px;
}

/* Payment Methods */
.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
}

.payment-option:hover {
    border-color: #007bff;
}

.payment-option input[type="radio"] {
    margin-right: 15px;
}

.payment-label {
    cursor: pointer;
    display: flex;
    align-items: center;
    margin: 0;
}

.payment-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.payment-info i {
    font-size: 20px;
    color: #007bff;
}

/* Terms & Conditions */
.terms-section {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 4px solid #007bff;
}

.terms-label {
    cursor: pointer;
    margin-left: 10px;
    color: #333;
}

.terms-link {
    color: #007bff;
    text-decoration: none;
}

.terms-link:hover {
    text-decoration: underline;
}

/* Place Order Button */
.place-order-btn {
    width: 100%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border: none;
    padding: 15px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.place-order-btn:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,123,255,0.3);
}

.place-order-btn i {
    margin-right: 8px;
}

/* Delivery Section */
.delivery-section {
    background: #fff;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: 1px solid #f0f0f0;
    height: fit-content;
}

/* Fancy radio inputs */
.payment-option { position: relative; cursor: pointer; }
.payment-option .radio-box { position: absolute; left: 16px; top: 18px; }
.payment-option .payment-label { padding-left: 32px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
.payment-option .payment-title { display: flex; align-items: center; gap: 10px; font-weight: 600; color: #2f3d4a; }
.payment-option .payment-note { display: block; color: #6c757d; margin-top: 2px; }
.payment-option .brand-logos img { height: 18px; margin-left: 8px; opacity: 0.9; filter: grayscale(20%); }
.payment-option .payment-badge { background: #eaf7ea; color: #1e7e34; font-size: 12px; padding: 4px 8px; border-radius: 999px; margin-left: 12px; white-space: nowrap; }
.payment-option:hover { box-shadow: 0 6px 14px rgba(0,0,0,0.06); border-color: #bcd0ff; }
.payment-option input[type="radio"]:checked + label { border: 2px solid #007bff; border-radius: 8px; background: #f8fbff; }
.payment-option:hover { box-shadow: 0 6px 14px rgba(0,0,0,0.06); }

/* Address cards hover */
.address-item:hover { transform: translateY(-2px); transition: transform 0.2s ease; }

/* Responsive Design */
@media (max-width: 768px) {
    .checkout-section {
        padding: 20px 15px;
    }

    .address-actions {
        position: static;
        margin-top: 10px;
        justify-content: flex-end;
    }

    .product-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .product-info {
        width: 100%;
    }

    .payment-methods {
        gap: 10px;
    }

    .place-order-btn {
        padding: 12px 20px;
        font-size: 14px;
    }
}

/* Alert Improvements */
.alert {
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert i {
    margin-right: 8px;
}
</style>

<!-- Checkout-Page /- -->
@endsection
