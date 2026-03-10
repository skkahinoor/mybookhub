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
                <div class="d-flex align-items-center gap-2">
                    <div class="quantity-controls">
                        <button type="button" class="qty-btn qty-minus" data-min="1">−</button>
                        <input type="text" class="qty-input" value="{{ $item['quantity'] ?? 1 }}" readonly>
                        <button type="button" class="qty-btn qty-plus" data-max="100">+</button>
                    </div>
                    <form action="{{ url('cart/add') }}" method="POST" class="wishlist-add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_attribute_id" value="{{ $item['product_attribute_id'] }}">
                        <input type="hidden" name="quantity" class="wishlist-hidden-qty" value="{{ $item['quantity'] ?? 1 }}">
                        <button type="submit" class="btn btn-primary btn-sm add-to-cart-btn" title="Add to Cart">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </form>
                </div>
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

