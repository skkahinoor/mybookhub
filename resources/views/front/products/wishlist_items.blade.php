{{-- Wishlist Items for AJAX Updates --}}
@if (!empty($getWishlistItems) && count($getWishlistItems) > 0)
    @foreach ($getWishlistItems as $item)
        @php
            $priceDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute($item['product_attribute_id']);
        @endphp

        <tr>
            {{-- Image --}}
            <td>
                <img src="{{ asset('front/images/product_images/small/' . ($item['product']['product_image'] ?? 'no-image.png')) }}"
                    width="80">
            </td>

            {{-- Name --}}
            <td>
                <a href="{{ url('product/' . $item['product_id']) }}">
                    {{ $item['product']['product_name'] ?? 'N/A' }}
                </a>
            </td>

            {{-- Price --}}
            <td>
                <strong>₹{{ number_format($priceDetails['final_price'], 2) }}</strong>

                @if ($priceDetails['discount'] > 0)
                    <br>
                    <del>₹{{ number_format($priceDetails['product_price'], 2) }}</del>
                @endif
            </td>

            {{-- Add to Cart --}}
            <td>
                {{-- <form action="{{ url('cart/add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_attribute_id" value="{{ $item['product_attribute_id'] }}">
                    <input type="hidden" name="quantity" value="{{ $item['quantity'] }}">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </form> --}}
            </td>

            {{-- Remove --}}
            <td>
                <button class="btn btn-danger btn-sm deleteWishlistItem" data-wishlist-id="{{ $item['id'] }}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" class="text-center">Wishlist is empty</td>
    </tr>
@endif

{{-- <tr>
    <td colspan="6" class="text-center">
        <p>No items in wishlist</p>
    </td>
</tr>
@endif --}}

<style>
    .product-thumb {
        max-width: 72px;
        height: auto;
    }

    .wishlist-row td {
        vertical-align: middle;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 4px;
        width: fit-content;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .qty-btn:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
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
        border-left: none;
        border-right: none;
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

    @media (max-width: 768px) {
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
</style>

<script>
    function initializeWishlistScripts() {
        // For each wishlist row, sync the qty controls with the hidden quantity for Add to Cart
        document.querySelectorAll('tr').forEach(function(row) {
            const qtyInput = row.querySelector('.qty-input');
            const minusBtn = row.querySelector('.qty-minus');
            const plusBtn = row.querySelector('.qty-plus');
            const hiddenQty = row.querySelector('.wishlist-hidden-qty');
            const form = row.querySelector('.wishlist-add-to-cart-form');

            if (!qtyInput || !minusBtn || !plusBtn || !hiddenQty) return;

            function clamp(val, min, max) {
                if (min !== null && val < min) return min;
                if (max !== null && val > max) return max;
                return val;
            }

            function updateButtons() {
                const min = parseInt(minusBtn.getAttribute('data-min') || '1', 10);
                const max = parseInt(plusBtn.getAttribute('data-max') || '1000', 10);
                const current = parseInt(qtyInput.value || '1', 10);
                minusBtn.disabled = current <= min;
                plusBtn.disabled = current >= max;
            }

            function setQty(newQty) {
                const min = parseInt(minusBtn.getAttribute('data-min') || '1', 10);
                const max = parseInt(plusBtn.getAttribute('data-max') || '1000', 10);
                const clamped = clamp(newQty, min, max);
                qtyInput.value = clamped;
                hiddenQty.value = clamped;
                updateButtons();
            }

            minusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const current = parseInt(qtyInput.value || '1', 10);
                setQty(current - 1);
            });

            plusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const current = parseInt(qtyInput.value || '1', 10);
                setQty(current + 1);
            });

            // Ensure initial sync
            hiddenQty && setQty(parseInt(hiddenQty.value || qtyInput.value || '1', 10));

            // Optional: ensure form uses current qty on submit
            if (form) {
                form.addEventListener('submit', function() {
                    hiddenQty.value = qtyInput.value;
                });
            }
        });

        // Bind delete handlers (unbind previous to prevent duplicates)
        document.querySelectorAll('.deleteWishlistItem').forEach(function(btn) {
            btn.onclick = function(e) {
                e.preventDefault();
                var id = this.getAttribute('data-wishlist-id');
                if (!id) return;

                fetch("{{ route('wishlist.remove') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            wishlist_id: id
                        })
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error('Bad response');
                        return res.json();
                    })
                    .then(function(resp) {
                        if (resp.status) {
                            // Force full page refresh so header counts and summaries stay in sync
                            window.location.reload();
                            return;
                        }
                        alert(resp.message || 'Could not remove item.');
                    })
                    .catch(function() {
                        alert('Something went wrong.');
                    });
            };
        });
    }

    document.addEventListener('DOMContentLoaded', initializeWishlistScripts);
</script>
