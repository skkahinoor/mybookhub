@foreach ($sliderProducts as $sliderProduct)
    @php
        $product = $sliderProduct->product;
        if (!$product) {
            continue;
        }

        $attributeId = $sliderProduct->id;
        $discountDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute($attributeId);
        
        $condition = strtolower(trim($product->condition ?? 'new'));
        $isNew = $condition == 'new';
        $conditionClass = $isNew ? 'new' : 'used';
        $conditionText = $isNew ? 'NEW' : 'USED';
    @endphp
    <div class="book-item">
        <div class="cover">
            <span class="condition {{ $conditionClass }}">
                {{ $conditionText }}
            </span>
            <a href="{{ url('product/' . $attributeId) }}">
                <img src="{{ asset('front/images/product_images/small/' . ($product->product_image ?? 'no-image.png')) }}"
                    onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'"
                    alt="{{ $product->product_name }}" loading="lazy">
            </a>
        </div>

        <div class="info">
            <a href="{{ url('product/' . $attributeId) }}" class="title" style="text-decoration: none;">
                {{ $product->product_name }}
            </a>
            <div class="author">
                {{ $product->authors && $product->authors->count() > 0 ? $product->authors->pluck('name')->implode(', ') : 'Unknown Author' }}
            </div>
            <div class="price">
                @if ($discountDetails['discount'] > 0)
                    <span class="original-price">₹{{ number_format($discountDetails['product_price'], 0) }}</span>
                    <span class="final-price text-danger">₹{{ number_format($discountDetails['final_price'], 0) }}</span>
                @else
                    <span class="final-price">₹{{ number_format($discountDetails['product_price'], 0) }}</span>
                @endif
            </div>

            <form action="{{ url('cart/add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_attribute_id" value="{{ $attributeId }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="cart-btn">Add</button>
            </form>
        </div>
    </div>
@endforeach
