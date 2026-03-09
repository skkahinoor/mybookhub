@foreach ($sliderProducts as $sliderProduct)
    @php
        $product = $sliderProduct->product;
        if (!$product) {
            continue;
        }

        $attributeId = $sliderProduct->id;
        $originalPrice = (float) $product->product_price;
        $discount = (float) ($sliderProduct->product_discount ?? 0);
        $finalPrice =
            $discount > 0 ? round($originalPrice - ($originalPrice * $discount) / 100) : round($originalPrice);
        $condition = $product->condition;
        $isNew = $condition == 'new';
        $isBestseller = $sliderProduct->is_bestseller == 'Yes';
    @endphp
    <div class="card-premium">
        @if ($isBestseller)
            <span class="p-badge-premium b-bestseller-premium">Bestseller</span>
        @elseif($discount > 0)
            <span class="p-badge-premium b-discount-premium">{{ round($discount) }}% OFF</span>
        @elseif(!$isNew)
            <span class="p-badge-premium b-used-premium">Used</span>
        @endif

        <a href="{{ url('product/' . $attributeId) }}" class="card-img-premium">
            <img src="{{ asset('front/images/product_images/small/' . ($product->product_image ?? 'no-image.png')) }}"
                onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'"
                alt="{{ $product->product_name }}" loading="lazy">
        </a>

        <a href="{{ url('product/' . $attributeId) }}" class="card-title-premium" style="text-decoration: none;">
            {{ $product->product_name }}
        </a>
        <div class="card-stars-premium">★★★★★</div>
        <div class="card-price-premium">₹{{ number_format($finalPrice, 0) }}</div>

        <form action="{{ url('cart/add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_attribute_id" value="{{ $attributeId }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn-cart-premium">Add to Cart</button>
        </form>
    </div>
@endforeach
