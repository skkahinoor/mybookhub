{{-- Sales Concept Page --}}
@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sales Concept</h4>

                            {{-- Flash Messages --}}
                            @if (Session::has('success_message'))
                                <div class="alert alert-success">{{ Session::get('success_message') }}</div>
                            @endif
                            @if (Session::has('error_message'))
                                <div class="alert alert-danger">{{ Session::get('error_message') }}</div>
                            @endif

                            {{-- ================= ISBN SEARCH ================= --}}
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5>Search Book by ISBN</h5>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <input type="text" id="isbn_search" class="form-control"
                                                placeholder="Enter ISBN">
                                        </div>
                                        <div class="col-md-4">
                                            <button id="search_btn" class="btn btn-primary btn-block">Search</button>
                                        </div>
                                    </div>

                                    {{-- Search Result --}}
                                    <div id="search_result" class="mt-3" style="display:none;">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <img id="product_image" class="img-fluid"
                                                            style="display:none;max-height:150px;">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <h5 id="product_name"></h5>
                                                        <p><strong>ISBN:</strong> <span id="product_isbn"></span></p>
                                                        <p><strong>MRP:</strong> ₹<span id="base_price"></span></p>
                                                        {{-- <p><strong>Global Discount:</strong>
                                                        <span id="discount_percent"></span>% (₹<span id="discount_amount"></span>)
                                                    </p> --}}
                                                        {{-- <p><strong>Final Price:</strong> ₹<span id="final_price"></span></p> --}}
                                                        <p><strong>Stock:</strong> <span id="product_stock"></span></p>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label>Quantity</label>
                                                        <input type="number" id="quantity" class="form-control"
                                                            min="1" value="1">

                                                        <small id="stock_warning" class="text-danger" style="display:none;">
                                                            Insufficient stock available
                                                        </small>

                                                        <button id="add_to_cart_btn" class="btn btn-success btn-block mt-2">
                                                            Add to Cart
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="search_error" class="alert alert-danger mt-3" style="display:none;"></div>
                                </div>
                            </div>

                            {{-- ================= CART ================= --}}
                            <div class="card">
                                <div class="card-body">
                                    <h5>Cart Items</h5>

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Book</th>
                                                <th>ISBN</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $subTotal = 0;
                                            @endphp

                                            @forelse($cart ?? [] as $item)
                                                @php $subTotal += $item['total']; @endphp
                                                <tr>
                                                    <td>{{ $item['product_name'] }}</td>
                                                    <td>{{ $item['product_isbn'] }}</td>
                                                    <td>₹{{ $item['price'] }}</td>
                                                    <td>{{ $item['quantity'] }}</td>
                                                    <td>₹{{ $item['total'] }}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-sm remove-item"
                                                            data-product-id="{{ $item['product_id'] }}">
                                                            Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No items in cart</td>
                                                </tr>
                                            @endforelse
                                        </tbody>

                                        @php
                                            $extraDiscount = session('sales_extra_discount_amount', 0);
                                            $couponDiscount = session('sales_coupon.discount', 0);
                                        @endphp

                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-right">Sub Total</th>
                                                <th colspan="2">₹{{ $subTotal }}</th>
                                            </tr>

                                            @if ($extraDiscount > 0)
                                                <tr>
                                                    <th colspan="4" class="text-right">
                                                        Extra Discount ({{ session('sales_extra_discount_percent') }}%)
                                                    </th>
                                                    <th colspan="2">- ₹{{ $extraDiscount }}</th>
                                                </tr>
                                            @endif

                                            @if ($couponDiscount > 0)
                                                <tr>
                                                    <th colspan="4" class="text-right">
                                                        Coupon ({{ session('sales_coupon.code') }})
                                                    </th>
                                                    <th colspan="2">- ₹{{ $couponDiscount }}</th>
                                                </tr>
                                            @endif

                                            <tr>
                                                <th colspan="4" class="text-right"><strong>Grand Total</strong></th>
                                                <th colspan="2">
                                                    <strong>₹{{ max(0, $subTotal - $extraDiscount - $couponDiscount) }}</strong>
                                                </th>
                                            </tr>
                                        </tfoot>

                                    </table>
                                </div>
                            </div>

                            {{-- ================= EXTRA DISCOUNT ================= --}}
                            @if (!empty($cart))
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h5>Extra Discount (%)</h5>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <input type="number" id="extra_discount_input" class="form-control"
                                                    placeholder="Enter discount %" min="0" max="100">
                                            </div>
                                            <div class="col-md-4">
                                                <button id="apply_extra_discount" class="btn btn-warning btn-block">
                                                    Apply Extra Discount
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- ================= COUPON ================= --}}
                            @if (!empty($cart))
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h5>Apply Coupon</h5>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <input type="text" id="coupon_code" class="form-control"
                                                    placeholder="Enter coupon code">
                                            </div>
                                            <div class="col-md-4">
                                                <button id="apply_coupon_btn" class="btn btn-info btn-block">
                                                    Apply Coupon
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- ================= CUSTOMER DETAILS ================= --}}
                            @if (!empty($cart))
                                <div class="card mt-4">
                                    <div class="card-body">
                                        <h5>Customer Details</h5>

                                        <form method="POST" action="{{ url('admin/sales-concept/process-sale') }}">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Name</label>
                                                    <input type="text" name="customer_name" class="form-control"
                                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Mobile</label>
                                                    <input type="text" name="customer_mobile" class="form-control"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <label>Email</label>
                                                    <input type="email" name="customer_email" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Address</label>
                                                    <textarea name="customer_address" class="form-control"></textarea>
                                                </div>
                                            </div>

                                            <button class="btn btn-success btn-lg mt-3">
                                                Process Sale
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- ================= SCRIPTS ================= --}}
@push('scripts')
<script>
    let currentProductId = null;
    let currentStock = 0; // 🔥 NEW

    /* ================= ISBN SEARCH ================= */
    $('#search_btn').click(function () {
        $.post('{{ url('admin/sales-concept/search-isbn') }}', {
            _token: '{{ csrf_token() }}',
            isbn: $('#isbn_search').val()
        }, function (res) {

            currentProductId = res.data.product_id;
            currentStock = parseInt(res.data.stock); // 🔥 STORE STOCK

            $('#product_name').text(res.data.product_name);
            $('#product_isbn').text(res.data.product_isbn);
            $('#base_price').text(res.data.base_price);
            $('#product_stock').text(currentStock);

            if (res.data.product_image) {
                $('#product_image')
                    .attr('src', '{{ url('front/images/product_images/small') }}/' + res.data.product_image)
                    .show();
            }

            // 🔥 RESET QUANTITY & WARNINGS
            $('#quantity').val(1);
            $('#quantity').attr('max', currentStock);
            $('#stock_warning').hide();
            $('#add_to_cart_btn').prop('disabled', false);

            $('#search_result').show();
            $('#search_error').hide();
        }).fail(function (xhr) {
            $('#search_error').text(xhr.responseJSON.message).show();
            $('#search_result').hide();
        });
    });

    /* ================= LIVE QUANTITY CHECK ================= */
    $(document).on('input', '#quantity', function () {
        const qty = parseInt($(this).val()) || 0;

        if (qty < 1) {
            $('#stock_warning').hide();
            $('#add_to_cart_btn').prop('disabled', true);
            return;
        }

        if (qty > currentStock) {
            $('#stock_warning').show();
            $('#add_to_cart_btn').prop('disabled', true);
        } else {
            $('#stock_warning').hide();
            $('#add_to_cart_btn').prop('disabled', false);
        }
    });

    /* ================= ADD TO CART ================= */
    $('#add_to_cart_btn').click(function () {
        $.post('{{ url('admin/sales-concept/add-to-cart') }}', {
            _token: '{{ csrf_token() }}',
            product_id: currentProductId,
            quantity: $('#quantity').val()
        }, function () {
            location.reload();
        }).fail(function (xhr) {
            alert(xhr.responseJSON.message);
        });
    });

    /* ================= APPLY EXTRA DISCOUNT ================= */
    $(document).on('click', '#apply_extra_discount', function () {
        $.post('{{ url('vendor/sales-concept/apply-extra-discount') }}', {
            _token: '{{ csrf_token() }}',
            extra_discount: $('#extra_discount_input').val()
        }, function () {
            location.reload();
        }).fail(function (xhr) {
            alert(xhr.responseJSON.message);
        });
    });

    /* ================= APPLY COUPON ================= */
    $(document).on('click', '#apply_coupon_btn', function () {
        $.post('{{ url('vendor/sales-concept/apply-coupon') }}', {
            _token: '{{ csrf_token() }}',
            coupon_code: $('#coupon_code').val()
        }, function (res) {
            alert(res.message);
            location.reload();
        }).fail(function (xhr) {
            alert(xhr.responseJSON.message);
        });
    });

    /* ================= REMOVE ITEM ================= */
    $(document).on('click', '.remove-item', function () {
        $.post('{{ url('admin/sales-concept/remove-from-cart') }}', {
            _token: '{{ csrf_token() }}',
            product_id: $(this).data('product-id')
        }, function () {
            location.reload();
        });
    });
</script>
@endpush

