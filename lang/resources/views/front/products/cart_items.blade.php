{{-- Note: This whole file is 'include'-ed in front/products/cart.blade.php (to allow the AJAX call when updating orders quantities in the Cart) --}}

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.quantity-controls {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 4px;
    width: fit-content;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.qty-btn {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #495057;
    font-size: 12px;
}

.qty-btn:hover {
    background: #e9ecef;
    border-color: #adb5bd;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.qty-btn:active {
    transform: translateY(0);
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.qty-btn:disabled {
    background: #f8f9fa;
    color: #adb5bd;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.qty-input {
    width: 50px;
    height: 32px;
    text-align: center;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin: 0 8px;
    font-weight: 600;
    color: #495057;
    background: #ffffff;
    font-size: 14px;
}

.qty-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.qty-minus {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: none;
}

.qty-plus {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-left: none;
}

.qty-input {
    border-radius: 0;
    border-left: none;
    border-right: none;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .quantity-controls {
        padding: 2px;
    }

    .qty-btn {
        width: 28px;
        height: 28px;
    }

    .qty-input {
        width: 40px;
        height: 28px;
        margin: 0 4px;
        font-size: 12px;
    }
}

/* Notification styles */
.qty-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 9999;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    max-width: 300px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.qty-notification.show {
    transform: translateX(0);
}

.qty-notification-info {
    background: #17a2b8;
}

.qty-notification-success {
    background: #28a745;
}

.qty-notification-warning {
    background: #ffc107;
    color: #212529;
}

.qty-notification-error {
    background: #dc3545;
}

/* Disabled button styles */
.qty-btn.disabled {
    background: #f8f9fa;
    color: #adb5bd;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Prevent multiple clicks during update */
.product-item-quantity.updating .qty-btn {
    pointer-events: none;
    opacity: 0.6;
}

.product-item-quantity.updating .qty-input {
    background: #f8f9fa;
    color: #6c757d;
}
</style>

<!-- Products-List-Wrapper -->
<div class="table-responsive">
    <table class="table check-tbl">
        <thead>
            <tr>
                <th>Product</th>
                <th>Product name</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th class="text-end">Close</th>
            </tr>
        </thead>
        <tbody>
            {{-- We'll place this $total_price inside the foreach loop to calculate the total price of all products in Cart. Check the end of the next foreach loop before @endforeach --}}
            @php $total_price = 0 @endphp

            @foreach ($getCartItems as $item)
                {{-- $getCartItems is passed in from cart() method in Front/ProductsController.php --}}
                @php
                    $getDiscountPriceDetails = \App\Models\Product::getDiscountPriceDetails($item['product_id']);
                @endphp

                <tr>
                    <td class="product-item-img">
                        @if (!empty($item['product']['product_image']))
                            <img src="{{ asset('front/images/product_images/small/' . $item['product']['product_image']) }}" alt="{{ $item['product']['product_name'] ?? 'Product' }}" class="img-fluid" style="max-width: 80px; height: auto;">
                        @else
                            <img src="{{ asset('front/images/product_images/small/no-image.png') }}" alt="No Image" class="img-fluid" style="max-width: 80px; height: auto;">
                        @endif
                    </td>
                    <td class="product-item-name">
                        <a href="{{ url('product/' . $item['product_id']) }}">
                            {{ $item['product']['product_name'] ?? 'Product Name Not Available' }}
                        </a>
                    </td>
                    <td class="product-item-price">
                        @if (isset($getDiscountPriceDetails['discount']) && $getDiscountPriceDetails['discount'] > 0)
                            <div class="item-new-price">
                                ₹{{ $getDiscountPriceDetails['final_price'] ?? 0 }}
                            </div>
                            <div class="item-old-price" style="text-decoration: line-through; color: #999;">
                                ₹{{ $getDiscountPriceDetails['product_price'] ?? 0 }}
                            </div>
                        @else
                            <div class="item-new-price">
                                ₹{{ $getDiscountPriceDetails['final_price'] ?? 0 }}
                            </div>
                        @endif
                    </td>
                    <td class="product-item-quantity">
                        <div class="quantity-controls">
                            <button type="button" class="qty-btn qty-minus" data-min="1" data-cartid="{{ $item['id'] }}" title="Decrease quantity">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="text" class="qty-input" value="{{ $item['quantity'] }}" data-original-qty="{{ $item['quantity'] }}" readonly>
                            <button type="button" class="qty-btn qty-plus" data-max="1000" data-cartid="{{ $item['id'] }}" title="Increase quantity">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="product-item-totle">
                        ₹{{ ($getDiscountPriceDetails['final_price'] ?? 0) * ($item['quantity'] ?? 1) }}
                    </td>
                    <td class="product-item-close">
                        <button class="btn btn-outline-danger btn-sm deleteCartItem" data-cartid="{{ $item['id'] }}" title="Remove item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                {{-- This is placed here INSIDE the foreach loop to calculate the total price of all products in Cart --}}
                @php
                    $itemTotal = ($getDiscountPriceDetails['final_price'] ?? 0) * ($item['quantity'] ?? 1);
                    $total_price = $total_price + $itemTotal;
                @endphp
            @endforeach
        </tbody>
    </table>
</div>
<!-- Products-List-Wrapper /- -->

<!-- Billing -->

<!-- Billing /- -->

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize button states for all quantity controls
        $('.product-item-quantity').each(function() {
            updateButtonStates($(this));
        });

        // Plus button click handler
        $(document).on('click', '.qty-plus', function(e) {
            e.preventDefault();

            let $btn = $(this);
            let $row = $btn.closest('.product-item-quantity');

            // Prevent multiple rapid clicks
            if ($row.hasClass('updating')) {
                return;
            }

            let $input = $row.find('.qty-input');
            let $minusBtn = $row.find('.qty-minus');
            let maxQty = parseInt($btn.data('max'));
            let cartId = $btn.data('cartid');

            let currentQty = parseInt($input.val());
            if (currentQty < maxQty) {
                let newQty = currentQty + 1;
                updateCartQuantity(cartId, newQty, $input, $row);
            } else {
                showNotification('Maximum stock limit reached.', 'warning');
            }
        });

        // Minus button click handler
        $(document).on('click', '.qty-minus', function(e) {
            e.preventDefault();

            let $btn = $(this);
            let $row = $btn.closest('.product-item-quantity');

            // Prevent multiple rapid clicks
            if ($row.hasClass('updating')) {
                return;
            }

            let $input = $row.find('.qty-input');
            let $plusBtn = $row.find('.qty-plus');
            let minQty = parseInt($btn.data('min'));
            let cartId = $btn.data('cartid');

            let currentQty = parseInt($input.val());
            if (currentQty > minQty) {
                let newQty = currentQty - 1;
                updateCartQuantity(cartId, newQty, $input, $row);
            } else {
                showNotification('Minimum quantity is 1.', 'warning');
            }
        });

        // Update button states based on quantity
        function updateButtonStates($row) {
            let $input = $row.find('.qty-input');
            let $minusBtn = $row.find('.qty-minus');
            let $plusBtn = $row.find('.qty-plus');
            let currentQty = parseInt($input.val());
            let minQty = parseInt($minusBtn.data('min'));
            let maxQty = parseInt($plusBtn.data('max'));

            // Disable/enable minus button
            if (currentQty <= minQty) {
                $minusBtn.prop('disabled', true).addClass('disabled');
            } else {
                $minusBtn.prop('disabled', false).removeClass('disabled');
            }

            // Disable/enable plus button
            if (currentQty >= maxQty) {
                $plusBtn.prop('disabled', true).addClass('disabled');
            } else {
                $plusBtn.prop('disabled', false).removeClass('disabled');
            }
        }

                // Show notification
        function showNotification(message, type = 'info') {
            // Remove any existing notifications first
            $('.qty-notification').remove();

            // Create notification element
            let $notification = $(`
                <div class="qty-notification qty-notification-${type}">
                    <span>${message}</span>
                </div>
            `);

            $('body').append($notification);

            // Show notification
            setTimeout(() => {
                $notification.addClass('show');
            }, 100);

            // Hide notification after 3 seconds
            setTimeout(() => {
                $notification.removeClass('show');
                setTimeout(() => {
                    $notification.remove();
                }, 300);
            }, 3000);
        }

        // Delete cart item click handler
        $(document).on('click', '.deleteCartItem', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to remove this item?')) {
                return false;
            }

            let cartId = $(this).data('cartid');
            deleteCartItem(cartId);
        });

        // Function to update cart quantity
        function updateCartQuantity(cartId, qty, $input, $row) {
            // Prevent multiple calls
            if ($row.hasClass('updating')) {
                return;
            }

            // Add updating class to prevent multiple calls
            $row.addClass('updating');

            $.ajax({
                url: '{{ route('cart.update') }}',
                method: 'POST',
                data: {
                    cartid: cartId,
                    qty: qty,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resp) {
                    if (resp.status) {
                        // Update the input field
                        $input.val(qty);

                        // Update button states
                        updateButtonStates($row);

                        // Show success notification only if quantity actually changed
                        let currentQty = parseInt($input.attr('data-original-qty') || $input.val());
                        if (qty !== currentQty) {
                            showNotification('Quantity updated successfully!', 'success');
                            $input.attr('data-original-qty', qty);
                        }

                        // Update total cart items count
                        if (resp.totalCartItems !== undefined) {
                            $('.totalCartItems').text(resp.totalCartItems);
                        }

                        // Update the entire cart view if provided
                        if (resp.view) {
                            $('#appendCartItems').html(resp.view);
                        }

                        // Update header cart view if provided
                        if (resp.headerview) {
                            $('.headerCartItems').html(resp.headerview);
                        }
                    } else {
                        showNotification(resp.message || 'Could not update quantity.', 'error');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    showNotification('Something went wrong. Please try again.', 'error');
                },
                complete: function() {
                    // Remove updating class
                    $row.removeClass('updating');
                }
            });
        }

        // Function to delete cart item
        function deleteCartItem(cartId) {
            $.ajax({
                url: '{{ route('cartDelete') }}',
                type: 'POST',
                data: {
                    cartid: cartId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resp) {
                    if (resp.status) {
                        // Update the entire cart view
                        if (resp.view) {
                            $('#appendCartItems').html(resp.view);
                        }

                        // Update header cart view
                        if (resp.headerview) {
                            $('.headerCartItems').html(resp.headerview);
                        }

                        // Update total cart items count
                        if (resp.totalCartItems !== undefined) {
                            $('.totalCartItems').text(resp.totalCartItems);
                        }
                    } else {
                        alert('Could not delete item.');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Something went wrong.');
                }
            });
        }
    });
</script>
