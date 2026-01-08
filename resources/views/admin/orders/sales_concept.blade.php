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

                            {{-- Success/Error Messages --}}
                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- ISBN Search Section --}}
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Search Book by ISBN</h5>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <input type="text" id="isbn_search" class="form-control" placeholder="Enter ISBN Number" />
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="button" id="search_btn" class="btn btn-primary btn-block">Search</button>
                                                </div>
                                            </div>
                                            <div id="search_result" class="mt-3" style="display: none;">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                        <img id="product_image" src="" alt="Product Image" class="img-fluid" style="max-height: 150px; display: none;">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h5 id="product_name"></h5>
                                                                <p><strong>ISBN:</strong> <span id="product_isbn"></span></p>
                                                                <p><strong>Price:</strong> ₹<span id="product_price"></span></p>
                                                                <p><strong>Stock:</strong> <span id="product_stock"></span></p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="quantity">Quantity</label>
                                                                    <input type="number" id="quantity" class="form-control" min="1" value="1" />
                                                                </div>
                                                                <button type="button" id="add_to_cart_btn" class="btn btn-success btn-block">Add to Cart</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="search_error" class="alert alert-danger mt-3" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Cart Table Section --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Cart Items</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="cart_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Book Name</th>
                                                            <th>ISBN</th>
                                                            <th>Price</th>
                                                            <th>Quantity</th>
                                                            <th>Stock</th>
                                                            <th>Total</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="cart_tbody">
                                                        @if (!empty($cartItems))
                                                            @foreach ($cartItems as $item)
                                                                <tr data-product-id="{{ $item['product_id'] }}">
                                                                    <td>{{ $item['product_name'] }}</td>
                                                                    <td>{{ $item['product_isbn'] }}</td>
                                                                    <td>₹{{ number_format($item['price'], 2) }}</td>
                                                                    <td>{{ $item['quantity'] }}</td>
                                                                    <td>{{ $item['stock'] }}</td>
                                                                    <td>₹{{ number_format($item['total'], 2) }}</td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger btn-sm remove-item" data-product-id="{{ $item['product_id'] }}">Remove</button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="7" class="text-center">No items in cart</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="5" class="text-right">Grand Total:</th>
                                                            <th id="grand_total">₹{{ number_format(array_sum(array_column($cartItems ?? [], 'total')), 2) }}</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Customer Details and Process Sale Section --}}
                            @if (!empty($cartItems))
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Customer Details</h5>
                                            <form id="process_sale_form" action="{{ url('admin/sales-concept/process-sale') }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="customer_name" id="customer_name" class="form-control" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="customer_mobile">Mobile Number <span class="text-danger">*</span></label>
                                                            <input type="text" name="customer_mobile" id="customer_mobile" class="form-control" required />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="customer_email">Email</label>
                                                            <input type="email" name="customer_email" id="customer_email" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="customer_address">Address</label>
                                                            <textarea name="customer_address" id="customer_address" class="form-control" rows="2"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-success btn-lg">Process Sale</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022. All rights reserved.</span>
            </div>
        </footer>
        <!-- partial -->
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let currentProductId = null;

        // Search by ISBN
        $('#search_btn').on('click', function() {
            const isbn = $('#isbn_search').val().trim();
            
            if (!isbn) {
                alert('Please enter an ISBN number');
                return;
            }

            $.ajax({
                url: '{{ url("admin/sales-concept/search-isbn") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    isbn: isbn
                },
                success: function(response) {
                    if (response.status) {
                        currentProductId = response.data.id;
                        $('#product_name').text(response.data.product_name);
                        $('#product_isbn').text(response.data.product_isbn);
                        $('#product_price').text(response.data.product_price);
                        $('#product_stock').text(response.data.stock);
                                                        if (response.data.product_image) {
                                                            $('#product_image').attr('src', '{{ url("front/images/product_images/small/") }}/' + response.data.product_image).show();
                                                        } else {
                                                            $('#product_image').hide();
                                                        }
                        $('#quantity').val(1);
                        $('#quantity').attr('max', response.data.stock);
                        $('#search_result').show();
                        $('#search_error').hide();
                    } else {
                        $('#search_error').text(response.message).show();
                        $('#search_result').hide();
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    $('#search_error').text(error.message || 'Error searching for book').show();
                    $('#search_result').hide();
                }
            });
        });

        // Add to Cart
        $('#add_to_cart_btn').on('click', function() {
            if (!currentProductId) {
                alert('Please search for a book first');
                return;
            }

            const quantity = parseInt($('#quantity').val());
            
            if (quantity < 1) {
                alert('Quantity must be at least 1');
                return;
            }

            $.ajax({
                url: '{{ url("admin/sales-concept/add-to-cart") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: currentProductId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    alert(error.message || 'Error adding to cart');
                }
            });
        });

        // Remove from Cart
        $(document).on('click', '.remove-item', function() {
            const productId = $(this).data('product-id');
            
            if (!confirm('Are you sure you want to remove this item from cart?')) {
                return;
            }

            $.ajax({
                url: '{{ url("admin/sales-concept/remove-from-cart") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId
                },
                success: function(response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    alert(error.message || 'Error removing from cart');
                }
            });
        });

        // Process Sale
        $('#process_sale_form').on('submit', function(e) {
            if (!confirm('Are you sure you want to process this sale? This will update stock and create an order.')) {
                e.preventDefault();
                return false;
            }
            // Let the form submit normally so session flash messages work
        });

        // Allow Enter key to trigger search
        $('#isbn_search').on('keypress', function(e) {
            if (e.which === 13) {
                $('#search_btn').click();
            }
        });
    });
</script>
@endpush

