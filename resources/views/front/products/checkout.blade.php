{{-- Note: This page (view) is rendered by the checkout() method in the Front/ProductsController.php --}}
@extends('front.layout.layout3')

@section('content')
    {{-- css code   --}}
    <style>
        .checkout-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px
        }

        .checkout-steps .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px
        }

        .checkout-steps .circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            background: #e9ecef;
            color: #6c757d;
            border: 2px solid #dee2e6
        }

        .checkout-steps .label {
            font-size: 12px;
            color: #6c757d
        }

        .checkout-steps .step.active .circle {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
            border-color: #007bff
        }

        .checkout-steps .step.active .label {
            color: #0056b3;
            font-weight: 600
        }

        .checkout-steps .separator {
            height: 2px;
            width: 40px;
            background: #e9ecef
        }

        @media(max-width:576px) {
            .checkout-steps {
                gap: 8px
            }

            .checkout-steps .separator {
                width: 24px
            }
        }

        /* Checkout Page Styles */
        .checkout-section {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
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

        .sticky-summary {
            position: sticky;
            top: 90px;
        }

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
            margin-right: 20px;
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
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.3);
        }

        .place-order-btn i {
            margin-right: 8px;
        }

        /* Delivery Section */
        .delivery-section {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #f0f0f0;
            height: fit-content;
        }

        /* Fancy radio inputs */
        .payment-option {
            position: relative;
            cursor: pointer;
        }

        .payment-option .radio-box {
            position: absolute;
            left: 16px;
            top: 18px;
        }

        .payment-option .payment-label {
            padding-left: 32px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .payment-option .payment-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #2f3d4a;
        }

        .payment-option .payment-note {
            display: block;
            color: #6c757d;
            margin-top: 2px;
        }

        .payment-option .brand-logos img {
            height: 18px;
            margin-left: 8px;
            opacity: 0.9;
            filter: grayscale(20%);
        }

        .payment-option .payment-badge {
            background: #eaf7ea;
            color: #1e7e34;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 999px;
            margin-left: 12px;
            white-space: nowrap;
        }

        .payment-option:hover {
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.06);
            border-color: #bcd0ff;
        }

        .payment-option input[type="radio"]:checked+label {
            border: 2px solid #007bff;
            border-radius: 8px;
            background: #f8fbff;
        }

        .payment-option:hover {
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.06);
        }

        /* Address cards hover */
        .address-item:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        }

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

    {{-- css code ends here  --}}
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


    </section>

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
                        <!-- Order Summary & Payment -->
                        <form name="checkoutForm" id="checkoutForm" action="{{ url('/checkout') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-7">
                                    <!-- Delivery Addresses Selection -->
                                    <div class="checkout-section">
                                        <div class="section-header">
                                            <h4><i class="fas fa-map-marker-alt"></i> Delivery Address</h4>
                                            <a href="{{ url('user/account') }}" class="add-btn">
                                                <i class="fas fa-edit"></i> Edit Profile Address
                                            </a>
                                        </div>
                                        <div class="address-list">
                                            @php $address = $deliveryAddresses[0]; @endphp
                                            <div class="address-card selected">
                                                <div class="address-selection">
                                                    <input type="radio" name="address_id" id="address{{ $address['id'] }}"
                                                        value="{{ $address['id'] }}" checked
                                                        shipping_charges="{{ $address['shipping_charges'] }}"
                                                        total_price="{{ $total_price }}"
                                                        coupon_amount="{{ \Illuminate\Support\Facades\Session::get('couponAmount') }}"
                                                        codpincodeCount="{{ $address['codpincodeCount'] }}"
                                                        prepaidpincodeCount="{{ $address['prepaidpincodeCount'] }}">
                                                    <label for="address{{ $address['id'] }}" class="address-label">
                                                        <div class="address-info">
                                                            <h6>{{ $address['name'] }} <span class="badge bg-primary ms-2"
                                                                    style="font-size: 10px;">Primary Profile Address</span>
                                                            </h6>
                                                            <p>{{ $address['address'] }}, {{ $address['city'] }},
                                                                {{ $address['state'] }}, {{ $address['country'] }} -
                                                                {{ $address['pincode'] }}</p>
                                                            <span class="phone">📞 {{ $address['mobile'] }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-5">
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
                                                            $getDiscountAttributePrice = \App\Models\Product::getDiscountAttributePrice(
                                                                $item['product_id'],
                                                                $item['size'],
                                                            );
                                                        @endphp
                                                        <div class="product-item">
                                                            <div class="product-info">
                                                                <img src="{{ asset('front/images/product_images/large/' . ($item['product']['product_image'] ?? 'no-image.png')) }}"
                                                                    alt="{{ $item['product']['product_name'] ?? 'Product' }}"
                                                                    class="img-fluid rounded shadow-sm"
                                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                                                <div class="product-details">
                                                                    <h6>{{ $item['product']['product_name'] }}</h6>
                                                                    <small>Size: {{ $item['size'] }} | Qty:
                                                                        {{ $item['quantity'] }}</small>
                                                                </div>
                                                            </div>
                                                            <div class="product-price">
                                                                ₹{{ $getDiscountAttributePrice['final_price'] * $item['quantity'] }}
                                                            </div>
                                                        </div>
                                                        @php $total_price = $total_price + ($getDiscountAttributePrice['final_price'] * $item['quantity']) @endphp
                                                    @endforeach
                                                </div>

                                                <!-- Wallet Section -->
                                                @if (Auth::check() && Auth::user()->wallet_balance > 0)
                                                    <div class="wallet-section-box mt-3 mb-3"
                                                        style="padding: 15px; background: #eef7ff; border-radius: 8px; border-left: 4px solid #007bff;">
                                                        <div class="d-flex align-items-center">
                                                            <input type="checkbox" id="useWallet" name="use_wallet"
                                                                value="1"
                                                                style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer;"
                                                                data-balance="{{ Auth::user()->wallet_balance }}"
                                                                data-max-use="20">
                                                            <label for="useWallet" style="margin: 0; cursor: pointer;">
                                                                <strong>Use Wallet Balance</strong> (Available:
                                                                ₹{{ Auth::user()->wallet_balance }})
                                                                <div style="font-size: 11px; color: #555;">Max ₹20 will be
                                                                    deducted
                                                                    from
                                                                    your wallet for this order</div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Price Breakdown -->
                                                <div class="price-breakdown">
                                                    <div class="price-row">
                                                        <span>Subtotal</span>
                                                        <span id="subtotalValue"
                                                            data-subtotal="{{ $total_price }}">₹{{ $total_price }}</span>
                                                    </div>
                                                    <div class="price-row">
                                                        <span>Shipping Charges</span>
                                                        <span class="shipping_charges">₹0</span>
                                                    </div>
                                                    <div class="price-row" id="walletRow"
                                                        style="display: none; color: #28a745;">
                                                        <span>Wallet Discount</span>
                                                        <span id="walletAmountDisplay">-₹0</span>
                                                    </div>

                                                    <div class="price-row">
                                                        <span>Coupon Discount</span>
                                                        <span>
                                                            @if (\Illuminate\Support\Facades\Session::has('couponAmount'))
                                                                <span
                                                                    id="couponDiscount">{{ number_format((float) \Illuminate\Support\Facades\Session::get('couponAmount', 0), 2) }}</span>
                                                            @else
                                                                ₹0
                                                            @endif
                                                        </span>
                                                        <script>
                                                            (function() {
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
                                                        <span><strong id="grandTotalDisplay"
                                                                class="grand_total">₹{{ $total_price - \Illuminate\Support\Facades\Session::get('couponAmount') }}</strong></span>
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
                                                    <input type="radio" class="radio-box" name="payment_gateway"
                                                        id="cash-on-delivery" value="COD">
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

                                                <div class="payment-option razorpayMethod">
                                                    <input type="radio" class="radio-box" name="payment_gateway"
                                                        id="razorpay" value="Razorpay">
                                                    <label class="payment-label" for="razorpay">
                                                        <div class="payment-info">
                                                            <div class="payment-title">
                                                                <i class="fas fa-credit-card"></i>
                                                                <span>Razorpay</span>
                                                            </div>
                                                            <small class="payment-note">Pay securely with Razorpay</small>
                                                        </div>
                                                        <div class="payment-badge">Online Payment</div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Terms & Conditions -->
                                        <div class="checkout-section">
                                            <div class="terms-section">
                                                <input type="checkbox" class="check-box" id="accept" name="accept"
                                                    value="Yes" title="Please agree to T&C">
                                                <label class="terms-label" for="accept">
                                                    I've read and accept the
                                                    <a href="javascript:void(0)" class="terms-link" data-toggle="modal"
                                                        data-target="#termsModal">
                                                        terms & conditions
                                                    </a>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Terms & Conditions Modal -->
                                        <div class="modal fade" id="termsModal" tabindex="-1" role="dialog"
                                            aria-labelledby="termsModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <a href="javascript:void(0)" class="terms-link"
                                                            data-toggle="modal" data-target="#termsModal">
                                                            terms & conditions
                                                        </a>

                                                        {{-- <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button> --}}
                                                    </div>

                                                    <div class="modal-body">
                                                        <!-- Customize your T&C content here -->
                                                        <p>
                                                            By completing a purchase on BookHub, you agree to abide by our
                                                            Terms
                                                            &
                                                            Conditions, which are designed to provide
                                                            clarity, fairness, and a trusted experience for every customer.
                                                            These
                                                            terms outline important policies related to
                                                            product availability, pricing, order confirmation, payment
                                                            processing,
                                                            shipping timelines, returns and refunds,
                                                            cancellations, and the use of your customer information.
                                                            <br><br>
                                                            We recommend reviewing these guidelines before placing an order
                                                            to
                                                            ensure complete understanding of your rights
                                                            and responsibilities as a BookHub user. Our team works
                                                            continuously
                                                            to
                                                            maintain accurate product listings, timely
                                                            deliveries, and secure transactions; however, occasional delays
                                                            or
                                                            changes may occur due to unforeseen circumstances.
                                                            <br><br>
                                                            By continuing, you acknowledge these conditions and consent to
                                                            follow
                                                            the policies set forth. For any clarification
                                                            or assistance, our customer support team is always ready to
                                                            help.
                                                        </p>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary"
                                                            data-dismiss="modal">Close</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>



                                        <!-- Place Order Button -->
                                        <div class="checkout-section">
                                            <button type="submit" id="placeOrderBtn"
                                                class="btn btn-primary btn-lg w-100"
                                                style="padding: 15px; font-weight: 600;">
                                                Place Order Securely -
                                                ₹{{ $total_price - \Illuminate\Support\Facades\Session::get('couponAmount') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>




    <script>
        $(document).ready(function() {
            // Dynamic Shipping calculation (Keep this small JS for UX)
            function calculateTotals() {
                console.log('Calculating totals...');

                var addressEl = $('input[name="address_id"]:checked');
                var useWalletEl = $('#useWallet');
                var subtotalValueEl = $('#subtotalValue');

                var shipping = parseFloat(addressEl.attr('shipping_charges')) || 0;
                var baseTotal = parseFloat(subtotalValueEl.data('subtotal')) || 0;
                var total = baseTotal;

                if (addressEl.length > 0) {
                    var addressTotal = parseFloat(addressEl.attr('total_price'));
                    if (!isNaN(addressTotal)) {
                        total = addressTotal;
                    }
                }

                var coupon = parseFloat("{{ \Illuminate\Support\Facades\Session::get('couponAmount') ?? 0 }}") ||
                    0;
                if (addressEl.length > 0) {
                    var addressCoupon = parseFloat(addressEl.attr('coupon_amount'));
                    if (!isNaN(addressCoupon)) {
                        coupon = addressCoupon;
                    }
                }

                // Wallet Calculation
                var walletDiscount = 0;
                if (useWalletEl.length > 0 && useWalletEl.is(':checked')) {
                    var balance = parseFloat(useWalletEl.data('balance')) || 0;
                    var maxUse = parseFloat(useWalletEl.data('max-use')) || 20;
                    walletDiscount = Math.min(balance, maxUse);
                    $('#walletRow').show();
                    $('#walletAmountDisplay').html('-₹' + walletDiscount.toFixed(2));
                } else {
                    $('#walletRow').hide();
                }

                $('.shipping_charges').html('₹' + shipping.toFixed(2));

                var grand = total + shipping - coupon - walletDiscount;
                if (grand < 0) grand = 0;

                var finalTotal = grand.toFixed(2);
                console.log('Grand Total recalculated:', finalTotal);

                $('#grandTotalDisplay').html('₹' + finalTotal);
                $('.grand_total').html('₹' + finalTotal); // Keep class update for other elements

                var paymentMethod = $('input[name="payment_gateway"]:checked').val();

                // Update Place Order button
                var btnText = 'Place Order Securely - ₹' + finalTotal;
                if (paymentMethod == 'Razorpay') {
                    btnText = 'Proceed to Pay - ₹' + finalTotal;
                } else if (paymentMethod == 'COD') {
                    btnText = 'Confirm COD Order - ₹' + finalTotal;
                }
                $('#placeOrderBtn').html(btnText);

                // Pincode availability toggle
                if (addressEl.length > 0) {
                    var cod = parseInt(addressEl.attr('codpincodeCount')) || 0;
                    var prepaid = parseInt(addressEl.attr('prepaidpincodeCount')) || 0;
                    if (cod > 0) $('.codMethod').show();
                    else $('.codMethod').hide();
                    if (prepaid > 0) $('.razorpayMethod').show();
                    else $('.razorpayMethod').hide();
                }
            }

            $(document).on('change', 'input[name="address_id"], #useWallet, input[name="payment_gateway"]',
                function() {
                    calculateTotals();
                });

            // Initial calculation
            calculateTotals();

            // Re-trigger on address radio checked (for fallback)
            if ($('input[name="address_id"]:checked').length > 0) {
                $('input[name="address_id"]:checked').trigger('change');
            }
        });
    </script>
@endsection
