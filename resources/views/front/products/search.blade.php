@extends('front.layout.layout3')

@section('content')
    <div class="container py-5">
        <div class="row">
            {{-- <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Filters</h5>
                        <form action="{{ url('/search-products') }}" method="get" id="filterForm">
                            <!-- Preserve search term -->
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <!-- Category Filter -->
                            <div class="mb-4">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="section_id" onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Condition Filter -->
                            @php
                                use Illuminate\Support\Facades\Session;
                                $sessionCondition = Session::get('condition', 'new');
                                $selectedCondition = request('condition', $sessionCondition);
                            @endphp
                            <div class="mb-4">
                                <label class="form-label">Condition</label>
                                <select class="form-select" name="condition" onchange="updateConditionSessionAndSubmit(this)">
                                    <option class="text-dark" value="" {{ $selectedCondition == '' ? 'selected' : '' }}>All Conditions</option>
                                    <option class="text-dark" value="new" {{ $selectedCondition == 'new' ? 'selected' : '' }}>New</option>
                                    <option class="text-dark"value="old" {{ $selectedCondition == 'old' ? 'selected' : '' }}>Old</option>
                                </select>
                            </div>

                            <!-- Language Filter -->
                            <div class="mb-4">
                                <label class="form-label">Language</label>
                                <select class="form-select" name="language_id" onchange="this.form.submit()">
                                    <option value="">All Languages</option>
                                    @foreach ($language as $lang)
                                        <option value="{{ $lang->id }}"
                                            {{ request('language_id') == $lang->id ? 'selected' : '' }}>
                                            {{ $lang->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range Filter -->
                            <div class="mb-4">
                                <label class="form-label">Price Range (After Discount)</label>
                                <div class="price-range-display mb-2 text-center">
                                    ₹<span id="currentMinPrice">{{ request('min_price', 0) }}</span> -
                                    ₹<span id="currentMaxPrice">{{ request('max_price', 10000) }}</span>
                                </div>
                                <div class="range-slider mb-3">
                                    <input type="range" class="form-range" id="priceRangeSlider" min="0"
                                        max="10000" step="100" value="{{ request('max_price', 10000) }}">
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" class="form-control" id="minPrice" name="min_price"
                                                placeholder="Min" min="0" value="{{ request('min_price', '') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" class="form-control" id="maxPrice" name="max_price"
                                                placeholder="Max" min="0" value="{{ request('max_price', '') }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary w-100 mt-2"
                                    style="background-color: #6c5dd4; border-color: #6c5dd4;">
                                    Apply Price Filter
                                </button>
                            </div>

                            <!-- Clear All Filters -->
                            @if (request('search') || request('section_id') || request('condition') || request('language_id') || request('min_price') || request('max_price'))
                                <a href="{{ url('/search-products') }}{{ request('search') ? '?search=' . request('search') : '' }}"
                                    class="btn btn-outline-secondary btn-sm w-100">
                                    Clear All Filters
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div> --}}

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

                <!-- Active Filters -->
                {{-- @if (request('search') || request('section_id') || request('condition') || request('language_id') || request('min_price') || request('max_price'))
                    <div class="mb-4">
                        <div class="d-flex flex-wrap gap-2">
                            @if (request('search'))
                                <span class="badge bg-light text-dark p-2">
                                    Search: {{ request('search') }}
                                </span>
                            @endif
                            @if (request('section_id'))
                                <span class="badge bg-light text-dark p-2">
                                    Category: {{ \App\Models\Section::find(request('section_id'))->name }}
                                </span>
                            @endif
                            @if (request('condition'))
                                <span class="badge bg-light text-dark p-2">
                                    Condition: {{ ucfirst(request('condition')) }}
                                </span>
                            @endif
                            @if (request('language_id'))
                                <span class="badge bg-light text-dark p-2">
                                    Language: {{ \App\Models\Language::find(request('language_id'))->name }}
                                </span>
                            @endif
                            @if (request('min_price') || request('max_price'))
                                <span class="badge bg-light text-dark p-2">
                                    Price:
                                    @if (request('min_price'))
                                        ₹{{ request('min_price') }}
                                    @endif
                                    @if (request('min_price') && request('max_price'))
                                        -
                                    @endif
                                    @if (request('max_price'))
                                        ₹{{ request('max_price') }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                @endif --}}

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


