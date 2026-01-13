@extends('front.layout.layout3')

@section('content')
    <div class="container py-5">
        <div class="row">
            <!-- Search Results -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">Search Results</h4>
                        <div class="text-muted">
                            Found {{ $products->total() }} results
                            @if (request('search'))
                                for "{{ request('search') }}"
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <!-- Add sorting options if needed -->
                    </div>
                </div>

                <div class="row g-4">
                    @forelse($products as $product)
                        @php
                            // Master product
                            $p = $product->product;
                            if (!$p) {
                                continue;
                            }

                            // Base price from products table
                            $originalPrice = (float) $p->product_price;

                            // Vendor-specific discount from products_attributes
                            $vendorDiscount = (float) ($product->product_discount ?? 0);

                            if ($vendorDiscount > 0) {
                                $finalPrice = round($originalPrice - ($originalPrice * $vendorDiscount) / 100);
                                $discountPercent = round($vendorDiscount);
                            } else {
                                $finalPrice = round($originalPrice);
                                $discountPercent = 0;
                            }
                        @endphp

                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0 product-card">

                                {{-- IMAGE --}}
                                <div class="position-relative">
                                    <a href="{{ url('product/' . $product->id) }}">
                                        <img src="{{ asset('front/images/product_images/small/' . $p->product_image) }}"
                                            class="card-img-top" style="height:200px;object-fit:cover"
                                            onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'">
                                    </a>

                                    @if ($discountPercent > 0)
                                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                            -{{ $discountPercent }}%
                                        </span>
                                    @endif
                                </div>

                                {{-- BODY --}}
                                <div class="card-body">
                                    <h5>{{ Str::limit($p->product_name, 50) }}</h5>

                                    <p class="small text-muted">
                                        Stock: {{ $product->stock }}
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if ($discountPercent > 0)
                                                <del class="text-muted">₹{{ round($originalPrice) }}</del>
                                                <span class="fw-bold ms-2">₹{{ $finalPrice }}</span>
                                            @else
                                                <span class="fw-bold">₹{{ round($originalPrice) }}</span>
                                            @endif
                                        </div>

                                        <span class="badge bg-warning">
                                            {{ ucfirst($p->condition) }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    @empty
                        <div class="col-12 alert alert-info">
                            No products found.
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>


            </div>

            <style>
                .product-card {
                    transition: transform 0.2s;
                }

                .product-card:hover {
                    transform: translateY(-5px);
                }

                .range-slider {
                    padding: 10px 0;
                }

                .form-range::-webkit-slider-thumb {
                    background: #cf8938;
                }

                .form-range::-moz-range-thumb {
                    background: #cf8938;
                }

                .form-range::-ms-thumb {
                    background: #cf8938;
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const slider = document.getElementById('priceRangeSlider');
                    const minPriceInput = document.getElementById('minPrice');
                    const maxPriceInput = document.getElementById('maxPrice');
                    const minPriceDisplay = document.getElementById('currentMinPrice');
                    const maxPriceDisplay = document.getElementById('currentMaxPrice');

                    // Initialize with current or default values
                    let minPrice = parseInt(minPriceInput.value) || 0;
                    let maxPrice = parseInt(maxPriceInput.value) || 10000;

                    function updateDisplay() {
                        minPriceDisplay.textContent = minPrice;
                        maxPriceDisplay.textContent = maxPrice;
                        minPriceInput.value = minPrice;
                        maxPriceInput.value = maxPrice;
                        slider.value = maxPrice;
                    }

                    // Update when slider changes
                    slider.addEventListener('input', function() {
                        const value = parseInt(this.value);
                        minPrice = Math.max(0, value - 2000);
                        maxPrice = value;
                        updateDisplay();
                    });

                    // Update when min price input changes
                    minPriceInput.addEventListener('input', function() {
                        minPrice = parseInt(this.value) || 0;
                        if (minPrice > maxPrice) {
                            maxPrice = minPrice;
                            maxPriceInput.value = maxPrice;
                        }
                        updateDisplay();
                    });

                    // Update when max price input changes
                    maxPriceInput.addEventListener('input', function() {
                        maxPrice = parseInt(this.value) || 10000;
                        if (maxPrice < minPrice) {
                            minPrice = maxPrice;
                            minPriceInput.value = minPrice;
                        }
                        updateDisplay();
                    });

                    // Initialize display
                    updateDisplay();
                });

                function updateConditionSessionAndSubmit(select) {
                    fetch("{{ route('set.condition') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({
                                condition: select.value
                            }),
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                select.form.submit();
                            }
                        });
                }
            </script>
        @endsection
