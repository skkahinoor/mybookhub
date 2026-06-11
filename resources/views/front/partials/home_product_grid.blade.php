@foreach ($sliderProducts as $product)
    @php
        if (!$product || !$product->product) {
            continue;
        }

        $isNew = empty($product->old_book_condition_id);
        $conditionClass = $isNew ? 'new' : 'old';
        $conditionText = $isNew ? 'NEW' : ($product->condition ? strtoupper($product->condition->name) : 'USED');

        $discountDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute($product->id, $product);
        $originalPrice = $discountDetails['product_price'] ?? 0;
        $finalPrice = $discountDetails['final_price'] ?? 0;
        $discountPercent = $discountDetails['discount_percent'] ?? 0;

        $productUrl = url('product/' . $product->product->id);
    @endphp
    <div class="book-item">
        <div class="cover">
            <span class="condition {{ $conditionClass }}">
                {{ $conditionText }}
            </span>
            <a href="{{ $productUrl }}">
                <img src="{{ getBookCoverUrl(($product->product->product_image ?? 'no-image.png')) }}"
                    onerror="this.src='{{ getBookCoverUrl('no-image.png') }}'"
                    alt="{{ $product->product->product_name }}" loading="lazy">
            </a>
        </div>

        <div class="info">
            <a href="{{ $productUrl }}" class="title" style="text-decoration: none;">
                {{ $product->product->product_name }}
            </a>
            <div class="author">
                {{ $product->product->authors && $product->product->authors->count() > 0 ? $product->product->authors->pluck('name')->implode(', ') : 'Unknown Author' }}
            </div>
            <div class="price">
                @if ($finalPrice > 0)
                    @if ($discountPercent > 0 && $originalPrice > $finalPrice)
                        <span class="original-price">₹{{ number_format($originalPrice, 0) }}</span>
                        <span class="final-price text-danger">₹{{ number_format($finalPrice, 0) }}</span>
                    @else
                        <span class="final-price">₹{{ number_format($finalPrice, 0) }}</span>
                    @endif
                @else
                    <span class="final-price">Price Unavailable</span>
                @endif
            </div>
        </div>
    </div>
@endforeach
