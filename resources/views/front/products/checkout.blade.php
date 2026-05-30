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

        .payment-item-modern input:checked:not(:disabled)+.payment-box-modern {
            border-color: var(--primary-color);
            background: #eff6ff;
        }

        .payment-item-modern input:disabled+.payment-box-modern {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f8fafc;
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
        /* Map Picker Styles */
        .search-box-map {
            flex: 1;
        }
        .search-box-map input:focus {
            box-shadow: none;
        }
        .map-pin-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -100%);
            z-index: 1000;
            pointer-events: none;
        }
        .pin-wrapper {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .pin-wrapper svg {
            animation: bouncePin 0.6s infinite alternate;
            filter: drop-shadow(0 8px 4px rgba(0,0,0,0.2));
        }
        .pin-dot {
            width: 12px;
            height: 6px;
            background: rgba(0,0,0,0.15);
            border-radius: 50%;
            margin-top: -4px;
            transition: all 0.3s;
        }
        @keyframes bouncePin {
            from { transform: translateY(0) scale(1); }
            to { transform: translateY(-12px) scale(1.05); }
        }
        .map-selection-card {
            position: absolute;
            bottom: 24px;
            left: 24px;
            right: 24px;
            background: white;
            padding: 24px;
            border-radius: 16px;
            z-index: 1001;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        .modal-body.position-relative {
            position: relative !important;
            background: #f8f9fa;
        }
        .confirm-btn-modern {
            background: #2874f0 !important; /* Flipkart Blue */
            color: white !important;
            border: none;
            border-radius: 8px;
            width: 100%;
            padding: 14px;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 20px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(40, 116, 240, 0.2);
        }
        .confirm-btn-modern:hover {
            background: #1259cc !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(40, 116, 240, 0.3);
        }
        /* Flipkart Style Address Cards */
        .address-card-modern {
            border: 1px solid #f0f0f0 !important;
            padding: 16px 20px !important;
            background: #fff;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
        }
        .address-card-modern:hover {
            background: #fcfcfc;
        }
        .address-card-modern.active-address {
            border-color: #f0f0f0 !important;
            background: #f5faff;
        }
        .address-card-modern .deliver-here-btn {
            display: none;
            background: #fb641b; /* Flipkart Orange */
            color: #fff;
            border: none;
            padding: 10px 24px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            border-radius: 2px;
            margin-top: 15px;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,.2);
        }
        .address-card-modern.active-address .deliver-here-btn {
            display: block;
        }
        .address-card-modern .address-radio {
            margin-top: 4px;
        }
        .address-card-modern .name-text {
            font-weight: 600;
            font-size: 14px;
            color: #212121;
        }
        .address-card-modern .addr-text {
            font-size: 14px;
            color: #212121;
            margin-top: 8px;
            line-height: 1.4;
        }
        .address-card-modern .badge-home {
            background: #f0f0f0;
            color: #878787;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 2px;
            font-weight: 600;
            margin-left: 8px;
        }
        .current-location-btn.spinning svg {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        #mapPickerModal {
            z-index: 9999 !important;
        }
        #mapPickerModal .modal-dialog {
            z-index: 10000 !important;
        }
        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
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
                                        <a href="{{ url('student/account') }}" class="btn-edit-modern">
                                            <i class="fas fa-pen"></i> Edit Profile
                                        </a>
                                    </div>
                                    <div class="card-content">
                                        <div class="row g-3">
                                            @foreach ($deliveryAddresses as $address)
                                                <div class="col-md-12">
                                                    <div class="address-card-modern {{ $address->is_default ? 'active-address' : '' }}"
                                                        onclick="document.getElementById('address{{ $address->id }}').click(); this.parentElement.parentElement.querySelectorAll('.address-card-modern').forEach(el => el.classList.remove('active-address')); this.classList.add('active-address');">
                                                        <div class="d-flex gap-3">
                                                            <div class="address-radio">
                                                                <input type="radio" name="address_id"
                                                                    id="address{{ $address->id }}"
                                                                    value="{{ $address->id }}"
                                                                    {{ $address->is_default ? 'checked' : '' }}
                                                                    shipping_charges="{{ $address->shipping_charges }}"
                                                                    total_price="{{ $total_price }}"
                                                                    coupon_amount="{{ \Illuminate\Support\Facades\Session::get('couponAmount') }}"
                                                                    codpincodeCount="{{ $address->codpincodeCount }}"
                                                                    prepaidpincodeCount="{{ $address->prepaidpincodeCount }}">
                                                            </div>
                                                            <div class="address-info w-100">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <span class="name-text">{{ $address->name }}</span>
                                                                    <span class="badge-home">HOME</span>
                                                                    <span class="name-text ml-3">{{ $address->mobile }}</span>
                                                                </div>
                                                                <div class="addr-text">
                                                                    {{ $address->address }}, 
                                                                    {{ optional($address->block)->name ? $address->block->name . ', ' : '' }}
                                                                    {{ optional($address->district)->name ? $address->district->name . ', ' : '' }}
                                                                    {{ optional($address->state)->name ? $address->state->name : '' }}
                                                                    - <span class="fw-bold">{{ $address->pincode }}</span>
                                                                </div>
                                                                
                                                                <button type="submit" class="deliver-here-btn">
                                                                    Deliver Here
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-4 pt-3 border-top text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="clearAddressForm('{{ Auth::user()->name }}', '{{ Auth::user()->phone }}')">
                                                <i class="fas fa-plus me-2"></i> Add or Manage Addresses
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Method Card -->
                                <div class="modern-card">
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
                                                    <span class="payment-status-badge cod-badge">Available</span>
                                                </label>
                                            </div>

                                            @if(isset($hasStudentItem) && $hasStudentItem)
                                                <div class="payment-item-modern pickupMethod opacity-50" title="Pickup not available for peer-to-peer sales">
                                                    <div class="payment-box-modern bg-light border-dashed">
                                                        <div class="payment-label-modern text-muted">
                                                            <i class="fas fa-store-slash"></i>
                                                            <div>
                                                                <div class="text-decoration-line-through">Pickup from Store</div>
                                                                <small class="d-block" style="font-size: 11px;">Not available for student-to-student books</small>
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-secondary" style="font-size: 10px;">Unavailable</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="payment-item-modern pickupMethod">
                                                    <input type="radio" name="payment_gateway" id="pickup-from-store"
                                                        value="PICKUP">
                                                    <label for="pickup-from-store" class="payment-box-modern">
                                                        <div class="payment-label-modern">
                                                            <i class="fas fa-store"></i>
                                                            <div>
                                                                <div>Pickup from Store</div>
                                                                <small class="text-muted d-block"
                                                                    style="font-weight: 400; font-size: 12px;">Pickup yourself
                                                                    and pay online</small>
                                                            </div>
                                                        </div>
                                                        <span class="payment-status-badge">Pickup</span>
                                                    </label>
                                                </div>
                                            @endif

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
                                                        <span class="payment-status-badge razorpay-badge">Secure</span>
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
                                                            null,
                                                            $item['product_attribute_id']
                                                        );
                                                        $total_price +=
                                                            $getDiscountAttributePrice['final_price'] *
                                                            $item['quantity'];
                                                    @endphp
                                                    <div class="product-modern">
                                                        <img src="{{ asset('book_covers/' . ($item['product']['product_image'] ?? 'no-image.png')) }}"
                                                            class="product-img" alt="item">
                                                        <div class="product-info-modern">
                                                            <h6>{{ $item['product']['product_name'] }}</h6>
                                                            <div class="product-meta">Qty: {{ $item['quantity'] }}</div>
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
                                                        data-subtotal="{{ $total_price }}">₹{{ number_format($total_price, 2) }}
                                                    </span>
                                                </div>
                                                <div class="total-row-modern">
                                                    <span>Shipping</span>
                                                    <span class="shipping_charges">
                                                        @if($shippingCharges > 0)
                                                            ₹{{ number_format($shippingCharges, 2) }}
                                                        @else
                                                            Free Shipping
                                                        @endif
                                                    </span>
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
                                                        class="grand_total">₹{{ number_format($total_price + $shippingCharges - Session::get('couponAmount', 0), 2) }}</span>
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

                    <!-- Address Modal -->
                    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="addressForm">
                                    @csrf
                                    <input type="hidden" name="address_id" id="address_id">
                                    <input type="hidden" name="location" id="addr_location">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Full Name <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white"><i class="fas fa-user text-primary"></i></span>
                                                    <input type="text" name="name" id="addr_name" class="form-control"
                                                        placeholder="Enter full name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Mobile Number <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white"><i class="fas fa-phone text-primary"></i></span>
                                                    <input type="text" name="mobile" id="addr_mobile"
                                                        class="form-control" placeholder="Enter 10 digit mobile" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label mb-2">Pin Your Location on Map <span class="text-danger">*</span></label>
                                                <div class="map-wrapper-inline shadow-sm" style="position: relative; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; background: #f8f9fa;">
                                                    <!-- Search Box inside Map -->
                                                    <div class="inline-map-search" style="position: absolute; top: 12px; left: 12px; right: 12px; z-index: 5;">
                                                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                                            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                                                            <input type="text" id="mapSearchInput" class="form-control border-0" placeholder="Search for area, street name..." style="height: 40px; font-size: 14px;">
                                                        </div>
                                                    </div>
                                                    
                                                    <div id="mapContainer" style="height: 300px; width: 100%;"></div>
                                                    
                                                    <!-- Current Location Button -->
                                                    <button type="button" class="current-location-btn" onclick="getCurrentLocation()" title="My Location" style="top: 65px; right: 12px; width: 36px; height: 36px;">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V1h-2v2.06C6.83 3.52 3.52 6.83 3.06 11H1v2h2.06c.46 4.17 3.77 7.48 7.94 7.94V23h2v-2.06c4.17-.46 7.48-3.77 7.94-7.94H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Full Address <span class="text-danger">*</span></label>
                                                <textarea name="address" id="addr_address" class="form-control" rows="2" required placeholder="House No, Building, Street..."></textarea>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Country <span
                                                        class="text-danger">*</span></label>
                                                <select name="country_id" id="addr_country_id" class="form-select"
                                                    required>
                                                    <option value="">Select Country</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country['id'] }}">{{ $country['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">State <span
                                                        class="text-danger">*</span></label>
                                                <select name="state_id" id="addr_state_id" class="form-select"
                                                    required>
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">District <span
                                                        class="text-danger">*</span></label>
                                                <select name="district_id" id="addr_district_id" class="form-select"
                                                    required>
                                                    <option value="">Select District</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Block</label>
                                                <select name="block_id" id="addr_block_id" class="form-select">
                                                    <option value="">Select Block</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Pincode <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="pincode" id="addr_pincode"
                                                    class="form-control" placeholder="Enter 6 digit pincode" required>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="update_profile_checkout" name="update_profile" value="1">
                                                    <label class="form-check-label small text-muted fw-bold" for="update_profile_checkout">
                                                        <i class="fas fa-sync-alt me-1 text-primary"></i> Also update my account profile name and mobile
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" id="saveAddressBtn">Save
                                            Address</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    @section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB8--PjUrEFqHQYLm1TQK37FKnbLwpSqWY&libraries=places"></script>
    <script>
        let map, marker, geocoder, autocomplete;
        let selectedLat, selectedLng;
        let mapInitialized = false;

        function openMapPicker() {
            // Map is now inline, this is kept for compatibility
        }

        // Initialize map when the address modal is shown
        $('#addressModal').on('shown.bs.modal', function () {
            const existingLoc = $('#addr_location').val();
            if (existingLoc && existingLoc.includes(',')) {
                const parts = existingLoc.split(',');
                selectedLat = parseFloat(parts[0]);
                selectedLng = parseFloat(parts[1]);
            } else {
                selectedLat = null;
                selectedLng = null;
            }
            
            // If already initialized, just re-center and resize
            if (mapInitialized && map) {
                google.maps.event.trigger(map, 'resize');
                if (selectedLat && selectedLng) {
                    const pos = {lat: selectedLat, lng: selectedLng};
                    map.setCenter(pos);
                    if (marker) {
                        marker.setPosition(pos);
                    }
                    map.setZoom(17);
                    updateAddressFromCoordinates(selectedLat, selectedLng);
                } else {
                    getCurrentLocation();
                }
            } else {
                initMap();
            }
        });


        function initMap() {
            if (mapInitialized) return;

            // Default to Bhubaneswar if geolocation fails or is pending
            const defaultPos = (selectedLat && selectedLng) 
                ? { lat: selectedLat, lng: selectedLng } 
                : { lat: 20.2961, lng: 85.8245 }; 
            
            map = new google.maps.Map(document.getElementById("mapContainer"), {
                center: defaultPos,
                zoom: (selectedLat && selectedLng) ? 17 : 12,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                zoomControl: true,
            });

            geocoder = new google.maps.Geocoder();

            // 📍 Create Draggable Marker
            marker = new google.maps.Marker({
                position: defaultPos,
                map: map,
                draggable: true,
                title: "Drag marker to select location"
            });

            // If it's a new address (no selectedLat), try to get current location immediately
            if (!selectedLat || !selectedLng) {
                getCurrentLocation();
            } else {
                updateAddressFromCoordinates(selectedLat, selectedLng);
            }

            // Search Autocomplete
            const input = document.getElementById("mapSearchInput");
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) return;

                map.setCenter(place.geometry.location);
                map.setZoom(17);
                marker.setPosition(place.geometry.location);
                
                selectedLat = place.geometry.location.lat();
                selectedLng = place.geometry.location.lng();
                updateAddressFromCoordinates(selectedLat, selectedLng);
            });

            // Handle marker dragend
            google.maps.event.addListener(marker, 'dragend', function(event) {
                selectedLat = event.latLng.lat();
                selectedLng = event.latLng.lng();
                updateAddressFromCoordinates(selectedLat, selectedLng);
            });

            mapInitialized = true;
        }

        function updateAddressFromCoordinates(lat, lng) {
            document.getElementById("addr_location").value = `${lat},${lng}`;
            geocoder.geocode({ location: { lat: lat, lng: lng } }, (results, status) => {
                if (status === "OK") {
                    if (results[0]) {
                        document.getElementById("addr_address").value = results[0].formatted_address;
                        
                        // Try to extract pincode from address if possible
                        const pincodeMatch = results[0].formatted_address.match(/\b\d{6}\b/);
                        if (pincodeMatch) {
                            document.getElementById("addr_pincode").value = pincodeMatch[0];
                        }
                    }
                }
            });
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                const btn = $('.current-location-btn');
                btn.addClass('spinning');
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        selectedLat = pos.lat;
                        selectedLng = pos.lng;
                        map.setCenter(pos);
                        map.setZoom(17);
                        if (marker) {
                            marker.setPosition(pos);
                        }
                        updateAddressFromCoordinates(pos.lat, pos.lng);
                        btn.removeClass('spinning');
                    },
                    () => {
                        alert("Error: The Geolocation service failed.");
                        btn.removeClass('spinning');
                    }
                );
            } else {
                alert("Error: Your browser doesn't support geolocation.");
            }
        }

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

                var paymentMethod = $('input[name="payment_gateway"]:checked').val();

                // Pickup from store: no shipping charges
                if (paymentMethod === 'PICKUP') {
                    shipping = 0;
                }

                if (shipping > 0) {
                    $('.shipping_charges').html('₹' + shipping.toFixed(2));
                } else {
                    $('.shipping_charges').html('Free Shipping');
                }

                var grand = total + shipping - coupon - walletDiscount;
                if (grand < 0) grand = 0;

                var finalTotal = grand.toFixed(2);
                $('#grandTotalDisplay').html('₹' + finalTotal);
                $('.grand_total').html('₹' + finalTotal);

                // Update Place Order button with premium style
                var btnText = 'Complete Order';
                var icon = 'fa-arrow-right';

                if (paymentMethod == 'Razorpay') {
                    btnText = 'Pay Securely';
                    icon = 'fa-shield-check';
                } else if (paymentMethod == 'COD') {
                    btnText = 'Confirm Order';
                    icon = 'fa-check-circle';
                } else if (paymentMethod == 'PICKUP') {
                    btnText = 'Place Pickup Order';
                    icon = 'fa-store';
                }

                $('#placeOrderBtn').html('<span>' + btnText + ' - ₹' + finalTotal + '</span><i class="fas ' +
                    icon +
                    ' ms-2"></i>');

                // Always show all payment methods as available
                if (addressEl.length > 0) {
                    $('.codMethod').show();
                    $('#cash-on-delivery').prop('disabled', false);
                    $('.cod-badge').text('Available').css({background: '#f1f5f9', color: 'var(--secondary-color)'});

                    $('.razorpayMethod').show();
                    $('#razorpay').prop('disabled', false);
                    $('.razorpay-badge').text('Secure').css({background: '#f1f5f9', color: 'var(--secondary-color)'});

                    $('.pickupMethod').show();
                }
            }

            $(document).on('change', 'input[name="address_id"], #useWallet, input[name="payment_gateway"]',
                function() {
                    calculateTotals();
                });

            calculateTotals();

            // Cascading Dropdowns for Modal
            $('#addr_country_id').on('change', function() {
                loadModalStates($(this).val());
            });
            $('#addr_state_id').on('change', function() {
                loadModalDistricts($(this).val());
            });
            $('#addr_district_id').on('change', function() {
                loadModalBlocks($(this).val());
            });

            function loadModalStates(countryId, stateId = null) {
                return new Promise((resolve) => {
                    $.ajax({
                        url: '{{ route('user_states') }}',
                        data: {
                            country: countryId
                        },
                        success: function(response) {
                            let options = '<option value="">Select State</option>';
                            $.each(response, function(id, name) {
                                options += `<option value="${id}">${name}</option>`;
                            });
                            $('#addr_state_id').html(options);
                            if (stateId) $('#addr_state_id').val(stateId);
                            resolve();
                        }
                    });
                });
            }

            function loadModalDistricts(stateId, districtId = null) {
                return new Promise((resolve) => {
                    if (!stateId) return resolve();
                    $.ajax({
                        url: '{{ route('user_districts') }}',
                        data: {
                            state: stateId
                        },
                        success: function(response) {
                            let options = '<option value="">Select District</option>';
                            $.each(response, function(id, name) {
                                options += `<option value="${id}">${name}</option>`;
                            });
                            $('#addr_district_id').html(options);
                            if (districtId) $('#addr_district_id').val(districtId);
                            resolve();
                        }
                    });
                });
            }

            function loadModalBlocks(districtId, blockId = null) {
                return new Promise((resolve) => {
                    if (!districtId) return resolve();
                    $.ajax({
                        url: '{{ route('user_blocks') }}',
                        data: {
                            district: districtId
                        },
                        success: function(response) {
                            let options = '<option value="">Select Block</option>';
                            $.each(response, function(id, name) {
                                options += `<option value="${id}">${name}</option>`;
                            });
                            $('#addr_block_id').html(options);
                            if (blockId) $('#addr_block_id').val(blockId);
                            resolve();
                        }
                    });
                });
            }

            // Save Address
            $('#addressForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('saveAddress') }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.type == 'success') {
                            location.reload();
                        } else {
                            alert('Something went wrong. Please check your inputs.');
                        }
                    }
                });
            });
        });

        function clearAddressForm(name = '', mobile = '') {
            $('#address_id').val('');
            $('#addressForm')[0].reset();
            $('#addr_name').val(name);
            $('#addr_mobile').val(mobile);
            $('#addr_state_id').empty().append('<option value="">Select State</option>');
            $('#addr_district_id').empty().append('<option value="">Select District</option>');
            $('#addr_block_id').empty().append('<option value="">Select Block</option>');
            $('#addressModalLabel').text('Add New Address');
        }
    </script>
    @endsection
@endsection
