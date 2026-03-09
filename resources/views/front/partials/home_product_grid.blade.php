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
        $condition = $product->condition; // 'new' or 'used'/'old'
        $isNew = $condition == 'new';
    @endphp
    <div class="book-item">
        <div class="cover">
            <span class="condition-badge {{ $isNew ? 'badge-new' : 'badge-used' }}">
                {{ strtoupper($condition == 'old' ? 'used' : $condition) }}
            </span>
            <a href="{{ url('product/' . $attributeId) }}">
                <img src="{{ asset('front/images/product_images/small/' . ($product->product_image ?? 'no-image.png')) }}"
                    onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'"
                    alt="{{ $product->product_name }}" loading="lazy">
            </a>
        </div>

        <div class="info">
            <a href="{{ url('product/' . $attributeId) }}" class="title">
                {{ $product->product_name }}
            </a>
            <div class="author">{{ $product->authors->pluck('name')->first() ?? 'NCERT' }}</div>

            <div class="price">₹{{ $finalPrice }}</div>

            <form action="{{ url('cart/add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_attribute_id" value="{{ $attributeId }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="cart-btn">Add</button>
            </form>
        </div>
    </div>
@endforeach
