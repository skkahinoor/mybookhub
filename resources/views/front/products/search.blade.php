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

                <!-- Products Grid -->
                <div class="row g-4">
                    @forelse($products as $product)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm product-card">
                                <div class="position-relative">
                                    <a href="{{ url('product/' . $product->id) }}">
                                        <img src="{{ asset('front/images/product_images/small/' . $product->product_image) }}"
                                            class="card-img-top" alt="{{ $product->product_name }}"
                                            style="height: 200px; object-fit: cover;">
                                    </a>
                                    @php
                                        $discountedPrice = \App\Models\Product::getDiscountPrice($product->id);
                                        $hasDiscount = $discountedPrice > 0;
                                    @endphp
                                    @if ($hasDiscount)
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-danger">
                                                -{{ round((($product->product_price - $discountedPrice) / $product->product_price) * 100) }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-1">
                                        <a href="{{ url('product/' . $product->id) }}"
                                            class="text-decoration-none text-dark">
                                            {{ Str::limit($product->product_name, 50) }}
                                        </a>
                                    </h5>
                                    {{-- <p class="text-muted small mb-2">
                                    {{ Str::limit($product->description, 80) }}
                                </p> --}}

                                    <p class="text-muted small mb-2">Publisher: {{ $product->publisher->name ?? 'N/A' }}
                                    </p>
                                    <p class="text-muted small mb-2">Authors:
                                        @if ($product->authors->isNotEmpty())
                                            @foreach ($product->authors as $author)
                                                {{ $author->name }}@if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="price-block">
                                            @if ($hasDiscount)
                                                <span class="text-danger"><del>₹{{ $product->product_price }}</del></span>
                                                <span class="h5 mb-0 ms-2">₹{{ $discountedPrice }}</span>
                                            @else
                                                <span class="h5 mb-0">₹{{ $product->product_price }}</span>
                                            @endif
                                        </div>
                                        <span class="badge" style="background-color: #cf8938;">
                                            {{ ucfirst($product->condition) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No products found matching your criteria.
                            </div>

                            <div class="card border-0 shadow-sm mt-3">
                                <div class="card-body">
                                    <h5 class="card-title">Request a Book</h5>

                                    @auth
                                        @if(session('success'))
                                            <div class="alert alert-success">{{ session('success') }}</div>
                                        @endif

                                        <form method="POST" action="{{ route('book.request') }}">
                                            @csrf
                                            <div class="mb-3">
                                            <label class="form-label">Book Title <span class="text-danger">*</span></label>
                                                <input type="text" name="book_title" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Author Name</label>
                                                <input type="text" name="author_name" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Message (optional)</label>
                                                <textarea name="message" rows="3" class="form-control"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary" style="background-color: #cf8938;">Submit Request</button>
                                        </form>
                                    @else
                                        <div class="alert alert-warning">69
                                            Please
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">login</a> or
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">register</a>
                                            to request a book.
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>

                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
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


