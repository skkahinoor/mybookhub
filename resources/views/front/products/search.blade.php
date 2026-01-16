@extends('front.layout.layout3')

@section('content')
    <div class="container py-5">
        <!-- Login/Register Prompt for Guest Users -->
        @guest
            <div class="alert alert-info alert-dismissible fade show" role="alert" style="background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460;">
                <strong><i class="fas fa-info-circle"></i> Notice:</strong> Please
                <a href="{{ route('user.login') }}" class="alert-link fw-bold">login</a> or
                <a href="{{ route('user.register') }}" class="alert-link fw-bold">register</a>
                your account to request books.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endguest

        <div class="row">

            <!-- Search Results -->
            <div class="col-12">
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
                <div class="row {{ $products->total() > 0 ? 'g-4' : '' }}">
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
                       <!-- Message -->
                       <div class="alert alert-info mb-4" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Your book is not here?</h6>
                        <p class="mb-0">You can also request for this book. Fill out the form below and we'll help you find it!</p>
                    </div>
                    @endforelse
                </div>

                @if($products->total() > 0)
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                @endif

            </div>
        </div>

        <!-- Book Request Form (Only for logged-in users when no products found) -->
        @auth
            @if($products->total() == 0)
            <div class="row mt-0">
                <div class="col-12">
                    <div class="card shadow-sm border-0 mt-0">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-book"></i> Request a Book</h5>
                        </div>
                        <div class="card-body">


                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0" style="font-size: 0.9rem;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('user.book.request.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="book_title" class="form-label">
                                            Book Title <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('book_title') is-invalid @enderror"
                                               id="book_title"
                                               name="book_title"
                                               placeholder="Enter book title"
                                               value="{{ old('book_title', request('search')) }}"
                                               required>
                                        @error('book_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="author_name" class="form-label">
                                            Author Name <small class="text-muted">(Optional)</small>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('author_name') is-invalid @enderror"
                                               id="author_name"
                                               name="author_name"
                                               placeholder="Enter author name"
                                               value="{{ old('author_name') }}">
                                        @error('author_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">
                                        Additional Message <small class="text-muted">(Optional)</small>
                                    </label>
                                    <textarea class="form-control @error('message') is-invalid @enderror"
                                              id="message"
                                              name="message"
                                              rows="4"
                                              placeholder="Provide any additional details about the book you're looking for...">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-primary" style="background-color:#cf8938;border:none;">
                                        <i class="fas fa-paper-plane"></i> Submit Request
                                    </button>
                                    <a href="{{ route('user.book.indexrequest') }}" class="text-decoration-none">
                                        View My Requests <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endauth

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
