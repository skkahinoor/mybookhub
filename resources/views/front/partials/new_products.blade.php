@php $newPrices = $newProductDiscountPrices ?? []; @endphp
@foreach ($newProducts as $product)
    <div class="col-md-6 col-lg-3 d-flex justify-content-center">
        <div class="card h-100 border-0 shadow-sm product-card">
            <div class="position-relative">
                @if (!empty($product['product_image']))
                    <a href="{{ url('product/' . $product['id']) }}">
                        <img src="{{ asset('front/images/product_images/small/' . $product['product_image']) }}"
                            class="card-img-top" alt="product_name" style="height: 200px; object-fit: cover;">
                    </a>
                @endif
                @php
                    $listPrice = (float) ($product['product_price'] ?? 0);
                    $discountedPrice = (float) ($newPrices[$product->id] ?? \App\Models\Product::getDiscountPrice($product->id));
                    $hasDiscount = $listPrice > 0 && $discountedPrice < $listPrice;
                    $discountPct = $hasDiscount ? (int) round((1 - $discountedPrice / $listPrice) * 100) : 0;
                @endphp
                @if ($hasDiscount && $discountPct > 0)
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-danger">-{{ $discountPct }}%</span>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <h5 class="card-title mb-1">
                    <a href="{{ url('product/' . $product['id']) }}" class="text-decoration-none text-dark">
                        {{ $product['product_name'] }}
                    </a>
                </h5>

                <p class="text-muted small mb-2">Publisher: {{ $product->publisher->name ?? 'N/A' }}</p>
                @php
                    $allAuthorNames = $product->authors->pluck('name')->join(', ');
                @endphp

                <p class="text-muted small mb-2" title="{{ $allAuthorNames }}">
                    Authors:
                    @if ($product->authors->isNotEmpty())
                        {{ $product->authors->first()->name }}
                        @if ($product->authors->count() > 1)
                            ...
                        @endif
                    @else
                        N/A
                    @endif
                </p>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="price-block">
                        <span class="text-danger"><del>₹{{ $product['product_price'] }}</del></span>
                        <span class="h5 mb-0 ms-2">₹{{ (int) $discountedPrice }}</span>
                    </div>
                    <span class="badge" style="background-color: #6c5dd4;">
                        {{ $product['condition'] }}
                    </span>


                </div>


            </div>
        </div>
    </div>
@endforeach
