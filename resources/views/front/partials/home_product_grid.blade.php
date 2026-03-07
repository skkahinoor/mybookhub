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
        $isNew = $product->condition == 'new';
    @endphp
    <div class="book-card-v2">
        <div class="book-thumb">
            <a href="{{ url('product/' . $attributeId) }}">
                <img src="{{ asset('front/images/product_images/small/' . ($product->product_image ?? 'no-image.png')) }}"
                    onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'"
                    alt="{{ $product->product_name }}">
            </a>
            <div class="condition-badge {{ $isNew ? 'badge-new' : 'badge-used' }}">
                {{ $isNew ? 'New' : 'Old' }}
            </div>
        </div>

        <div class="book-info">
            <h3 class="book-title">
                <a href="{{ url('product/' . $attributeId) }}" class="text-dark text-decoration-none">
                    {{ $product->product_name }}
                </a>
            </h3>
            <div class="book-category">{{ $product->authors->pluck('name')->first() ?? 'NCERT' }}</div>
            <div class="book-price-row">
                <span class="current-price">₹{{ $finalPrice }}</span>
                @if ($discount > 0)
                    <span class="old-price">₹{{ round($originalPrice) }}</span>
                @endif
            </div>

            <form action="{{ url('cart/add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_attribute_id" value="{{ $attributeId }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn-quick-add" title="Add to Cart">
                    Add
                </button>
            </form>
        </div>
    </div>
@endforeach
