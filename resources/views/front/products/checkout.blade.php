{{-- Note: This page (view) is rendered by the checkout() method in the Front/ProductsController.php --}}
@extends('front.layout.layout3')

@section('content')
    {{-- css code   --}}
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #22c55e;
            --bg-light: #f8fafc;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --card-border: #e2e8f0;
        }

        .checkout-page-wrapper {
            background-color: var(--bg-light);
            padding: 40px 0;
            min-height: 100vh;
        }

        /* Progress Steps */
        .checkout-progress {
            display: flex;
            justify-content: space-between;
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            flex: 1;
        }

        .step-icon {
            width: 32px;
            height: 32px;
            background: #fff;
            border: 2px solid var(--card-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .progress-step.active .step-icon {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .step-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--secondary-color);
        }

        .progress-line {
            position: absolute;
            top: 16px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: var(--card-border);
            z-index: 1;
        }

        /* Cards */
        .modern-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header-modern {
            padding: 20px 24px;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fcfdfe;
        }

        .card-header-modern h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-content {
            padding: 24px;
        }

        /* Address Section */
        .address-card-modern {
            position: relative;
            background: #fbfcfe;
            border: 2px solid var(--primary-color);
            border-radius: 10px;
            padding: 20px;
            display: flex;
            gap: 16px;
        }

        .address-check {
            padding-top: 4px;
        }

        .address-check input {
            width: 20px;
            height: 20px;
            accent-color: var(--primary-color);
        }

        .address-details h6 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1e293b;
        }

        .address-details p {
            color: var(--secondary-color);
            line-height: 1.6;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .address-badge {
            background: #dbeafe;
            color: var(--primary-dark);
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        /* Order Summary */
        .summary-product-list {
            max-height: 350px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding-right: 8px;
        }

        .summary-product-list::-webkit-scrollbar {
            width: 4px;
        }

        .summary-product-list::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .product-modern {
            display: flex;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .product-modern:last-child {
            border-bottom: none;
        }

        .product-img {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #f1f5f9;
        }

        .product-info-modern {
            flex: 1;
        }

        .product-info-modern h6 {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 4px;
            color: #334155;
        }

        .product-meta {
            font-size: 12px;
            color: var(--secondary-color);
        }

        .product-price-modern {
            font-weight: 700;
            color: #1e293b;
            font-size: 15px;
        }

        /* Totals */
        .totals-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .total-row-modern {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            color: var(--secondary-color);
        }

        .total-row-modern.grand-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #e2e8f0;
            color: #1e293b;
            font-size: 18px;
            font-weight: 800;
        }

        /* Payment */
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .payment-item-modern {
            cursor: pointer;
        }

        .payment-item-modern input {
            display: none;
        }

        .payment-box-modern {
            border: 2px solid var(--card-border);
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .payment-item-modern input:checked+.payment-box-modern {
            border-color: var(--primary-color);
            background: #eff6ff;
        }

        .payment-label-modern {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            color: #334155;
        }

        .payment-label-modern i {
            font-size: 20px;
            color: var(--primary-color);
        }

        .payment-status-badge {
            font-size: 11px;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 20px;
            color: var(--secondary-color);
        }

        /* Place Order Button */
        .place-order-wrapper {
            margin-top: 30px;
        }

        .btn-premium {
            width: 100%;
            padding: 18px;
            background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        .btn-edit-modern {
            font-size: 13px;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Responsive Improvements */
        @media (max-width: 991px) {
            .checkout-page-wrapper {
                padding: 20px 0;
            }

            .sticky-summary {
                position: static !important;
            }
        }

        @media (max-width: 576px) {
            .progress-line {
                display: none;
            }

            .step-label {
                font-size: 11px;
            }

            .card-header-modern {
                padding: 15px 18px;
            }

            .card-content {
                padding: 18px;
            }
        }
    </style>

    {{-- css code ends here  --}}
    <div class="checkout-page-wrapper">
        <div class="container">
            <!-- Progress Tracker -->
            <div class="checkout-progress">
                <div class="progress-line"></div>
                <div class="progress-step active">
                    <div class="step-icon">1</div>
                    <span class="step-label">Account</span>
                </div>
                <div class="progress-step active">
                    <div class="step-icon">2</div>
                    <span class="step-label">Shipping</span>
                </div>
                <div class="progress-step active">
                    <div class="step-icon">3</div>
                    <span class="step-label">Review</span>
                </div>
                <div class="progress-step">
                    <div class="step-icon">4</div>
                    <span class="step-label">Payment</span>
                </div>
            </div>

            <!-- Page Introduction Wrapper /- -->

            <!-- Checkout-Page -->
            <div class="page-checkout u-s-p-t-80">
                <div class="container">
                    @if (Session::has('error_message'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-3" style="font-size: 20px;"></i>
                                <div>{{ Session::get('error_message') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form name="checkoutForm" id="checkoutForm" action="{{ url('/checkout') }}" method="post">
                        @csrf
                        <div class="row g-4">
                            <!-- Left Column: Address & Payment -->
                            <div class="col-lg-7">
                                <!-- Shipping Address Card -->
                                <div class="modern-card">
                                    <div class="card-header-modern">
                                        <h4><i class="fas fa-map-marker-alt"></i> Shipping Address</h4>
                                        <a href="{{ url('user/account') }}" class="btn-edit-modern">
                                            <i class="fas fa-pen"></i> Edit Profile
                                        </a>
                                    </div>
                                    <div class="card-content">
                                        <div class="address-card-modern">
                                            @php $address = $deliveryAddresses[0]; @endphp
                                            <div class="address-check">
                                                <input type="radio" name="address_id" id="address{{ $address['id'] }}"
                                                    value="{{ $address['id'] }}" checked
                                                    shipping_charges="{{ $address['shipping_charges'] }}"
                                                    total_price="{{ $total_price }}"
                                                    coupon_amount="{{ \Illuminate\Support\Facades\Session::get('couponAmount') }}"
                                                    codpincodeCount="{{ $address['codpincodeCount'] }}"
                                                    prepaidpincodeCount="{{ $address['prepaidpincodeCount'] }}">
                                            </div>
                                            <div class="address-details">
                                                <div class="d-flex align-items-center gap-3 mb-2">
                                                    <h6 class="mb-0">{{ $address['name'] }}</h6>
                                                    <span class="address-badge">Primary Delivery</span>
                                                </div>
                                                <p>
                                                    {{ $address['address'] }}<br>
                                                    {{ $address['city'] }}, {{ $address['state'] }},
                                                    {{ $address['country'] }}<br>
                                                    <strong>PIN:</strong> {{ $address['pincode'] }}
                                                </p>
                                                <div class="phone-box">
                                                    <i class="fas fa-phone-alt me-2 text-primary"></i>
                                                    <span>{{ $address['mobile'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Method Card -->
                                <div class="modern-card mt-4">
                                    <div class="card-header-modern">
                                        <h4><i class="fas fa-credit-card"></i> Payment Method</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="payment-grid">
                                            <div class="payment-item-modern codMethod">
                                                <input type="radio" name="payment_gateway" id="cash-on-delivery"
                                                    value="COD">
                                                <label for="cash-on-delivery" class="payment-box-modern">
                                                    <div class="payment-label-modern">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                        <div>
                                                            <div>Cash on Delivery</div>
                                                            <small class="text-muted d-block"
                                                                style="font-weight: 400; font-size: 12px;">Pay when you
                                                                receive</small>
                                                        </div>
                                                    </div>
                                                    <span class="payment-status-badge">Available</span>
                                                </label>
                                            </div>

                                            <div class="payment-item-modern razorpayMethod">
                                                <input type="radio" name="payment_gateway" id="razorpay"
                                                    value="Razorpay">
                                                <label for="razorpay" class="payment-box-modern">
                                                    <div class="payment-label-modern">
                                                        <i class="fas fa-shield-check"></i>
                                                        <div>
                                                            <div>Online Payment</div>
                                                            <small class="text-muted d-block"
                                                                style="font-weight: 400; font-size: 12px;">Cards, UPI,
                                                                Netbanking</small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="https://razorpay.com/favicon.png" width="16"
                                                            alt="RZP">
                                                        <span class="payment-status-badge">Secure</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-1">
                                    <div class="form-check custom-checkbox mb-3">
                                        <input type="checkbox" class="form-check-input" id="accept" name="accept"
                                            value="Yes">
                                        <label class="form-check-label ms-2" for="accept"
                                            style="font-size: 13px; color: #64748b;">
                                            I agree to the <a href="javascript:void(0)" data-toggle="modal"
                                                data-target="#termsModal" class="text-primary fw-bold">Terms &
                                                Conditions</a>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Order Summary -->
                            <div class="col-lg-5">
                                <div class="sticky-summary">
                                    <div class="modern-card">
                                        <div class="card-header-modern" style="background: #1e293b; border-bottom: none;">
                                            <h4 style="color: #fff;"><i class="fas fa-shopping-basket text-primary"></i>
                                                Order Summary</h4>
                                            <span class="badge bg-primary rounded-pill">{{ count($getCartItems) }}
                                                Items</span>
                                        </div>
                                        <div class="card-content">
                                            <div class="summary-product-list">
                                                @php $total_price = 0 @endphp
                                                @foreach ($getCartItems as $item)
                                                    @php
                                                        $getDiscountAttributePrice = \App\Models\Product::getDiscountAttributePrice(
                                                            $item['product_id'],
                                                            $item['size'],
                                                        );
                                                        $total_price +=
                                                            $getDiscountAttributePrice['final_price'] *
                                                            $item['quantity'];
                                                    @endphp
                                                    <div class="product-modern">
                                                        <img src="{{ asset('front/images/product_images/large/' . ($item['product']['product_image'] ?? 'no-image.png')) }}"
                                                            class="product-img" alt="item">
                                                        <div class="product-info-modern">
                                                            <h6>{{ $item['product']['product_name'] }}</h6>
                                                            <div class="product-meta">Qty: {{ $item['quantity'] }} | Size:
                                                                {{ $item['size'] }}</div>
                                                            <div class="product-price-modern mt-1">
                                                                ₹{{ number_format($getDiscountAttributePrice['final_price'] * $item['quantity'], 2) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            @if (Auth::check() && Auth::user()->wallet_balance > 0)
                                                <div class="wallet-promo mb-4 p-3 rounded-3"
                                                    style="background: #f0f7ff; border: 1px solid #dbeafe;">
                                                    <div class="form-check d-flex align-items-center mb-0">
                                                        <input class="form-check-input me-3" type="checkbox"
                                                            id="useWallet" name="use_wallet" value="1"
                                                            data-balance="{{ Auth::user()->wallet_balance }}"
                                                            data-max-use="20">
                                                        <label class="form-check-label" for="useWallet">
                                                            <span class="d-block fw-bold"
                                                                style="font-size: 14px; color: #1e3a8a;">Use Wallet
                                                                Credit</span>
                                                            <small class="text-muted">Balance:
                                                                ₹{{ number_format(Auth::user()->wallet_balance, 2) }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="totals-box">
                                                <div class="total-row-modern">
                                                    <span>Subtotal</span>
                                                    <span id="subtotalValue"
                                                        data-subtotal="{{ $total_price }}">₹{{ number_format($total_price, 2) }}</span>
                                                </div>
                                                <div class="total-row-modern">
                                                    <span>Shipping</span>
                                                    <span class="shipping_charges">₹0.00</span>
                                                </div>
                                                <div class="total-row-modern" id="walletRow" style="display: none;">
                                                    <span class="text-success">Wallet Credit</span>
                                                    <span class="text-success" id="walletAmountDisplay">-₹0.00</span>
                                                </div>
                                                @if (\Illuminate\Support\Facades\Session::has('couponAmount'))
                                                    <div class="total-row-modern">
                                                        <span class="text-success">Coupon Discount</span>
                                                        <span
                                                            class="text-success">-₹{{ number_format((float) Session::get('couponAmount'), 2) }}</span>
                                                    </div>
                                                @endif
                                                <div class="total-row-modern grand-total">
                                                    <span>Payable Amount</span>
                                                    <span id="grandTotalDisplay"
                                                        class="grand_total">₹{{ number_format($total_price - Session::get('couponAmount', 0), 2) }}</span>
                                                </div>
                                            </div>

                                            <div class="place-order-wrapper">
                                                <button type="submit" id="placeOrderBtn" class="btn-premium">
                                                    <span>Complete Order</span>
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>
                                                <p class="text-center mt-3 text-muted" style="font-size: 12px;">
                                                    <i class="fas fa-lock me-1"></i> SSL Encrypted & Secure Checkout
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Terms Modal -->
                    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title fw-bold"><i class="fas fa-file-contract text-primary me-2"></i>
                                        Terms & Conditions</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p style="line-height: 1.8; color: #475569;">
                                        By completing a purchase on BookHub, you agree to abide by our terms designed to
                                        provide a trusted experience.
                                        This includes policies on product availability, pricing accuracy, shipping
                                        timelines, and our returns/refund process.
                                        <br><br>
                                        We strive for excellence in every delivery. However, in cases of unforeseen
                                        circumstances, we will keep you informed of any updates to your order status.
                                    </p>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary px-4"
                                        data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>




    <script>
        $(document).ready(function() {
            function calculateTotals() {
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
                $('#grandTotalDisplay').html('₹' + finalTotal);
                $('.grand_total').html('₹' + finalTotal);

                var paymentMethod = $('input[name="payment_gateway"]:checked').val();

                // Update Place Order button with premium style
                var btnText = 'Complete Order';
                var icon = 'fa-arrow-right';

                if (paymentMethod == 'Razorpay') {
                    btnText = 'Pay Securely';
                    icon = 'fa-shield-check';
                } else if (paymentMethod == 'COD') {
                    btnText = 'Confirm Order';
                    icon = 'fa-check-circle';
                }

                $('#placeOrderBtn').html('<span>' + btnText + ' - ₹' + finalTotal + '</span><i class="fas ' + icon +
                    ' ms-2"></i>');

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

            calculateTotals();
        });
    </script>
@endsection
