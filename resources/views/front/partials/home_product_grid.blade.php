@foreach ($sliderProducts as $product)
    @php
        if (!$product) {
            continue;
        }

        $minPrice = \App\Models\Product::getDiscountPrice($product->id);
        $minPriceText = (isset($minPrice) && $minPrice > 0) ? number_format($minPrice, 0) : ($product->product_price ? number_format($product->product_price, 0) : null);
        
        $condition = strtolower(trim($product->condition ?? 'new'));
        $isNew = $condition == 'new';
        $conditionClass = $isNew ? 'new' : 'old';
        $conditionText = $isNew ? 'NEW' : 'OLD';
    @endphp
    <div class="book-item">
        <div class="cover">
            <span class="condition {{ $conditionClass }}">
                {{ $conditionText }}
            </span>
            <a href="{{ url('product/' . $product->id) }}">
                <img src="{{ asset('front/images/product_images/small/' . ($product->product_image ?? 'no-image.png')) }}"
                    onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'"
                    alt="{{ $product->product_name }}" loading="lazy">
            </a>
        </div>

        <div class="info">
            <a href="{{ url('product/' . $product->id) }}" class="title" style="text-decoration: none;">
                {{ $product->product_name }}
            </a>
            <div class="author">
                {{ $product->authors && $product->authors->count() > 0 ? $product->authors->pluck('name')->implode(', ') : 'Unknown Author' }}
            </div>
            <div class="price">
                @if ($minPriceText)
                    <span class="final-price text-danger">From ₹{{ $minPriceText }}</span>
                @else
                    <span class="final-price">Price Unavailable</span>
                @endif
            </div>
            
            {{-- Removed "Add to Cart" button as requested, user will click through to details to see sellers --}}
        </div>
    </div>
@endforeach
