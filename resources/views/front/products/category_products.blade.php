@extends('front.layout.layout3')
@section('content')
<style>
.card {
  transition: box-shadow .2s cubic-bezier(.4,0,.2,1);
}
.card:hover {
  box-shadow: 0 8px 24px rgb(24 39 75 / 13%);
  border-color: #e1e1e1;
}
.sticky-top {
  z-index: 1;
}
input[type="checkbox"]:checked + .form-check-label {
  color: #1890ff;
  font-weight: bold;
}
.badge.bg-light {
    background: #f0f3fa!important;
}
</style>

<section class="content-inner border-bottom py-5" style="background-color:#f9fafb;">
    <div class="container">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">
                {{ $categoryDetails->category_name ?? 'All Books' }}
            </h4>
            @isset($products)
                <span class="text-muted small">
                    Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() ?? 0 }}
                </span>
            @endisset
        </div>

        <div class="row">
            <!-- Sidebar: Categories -->
            <div class="col-lg-3 mb-4">
                <div class="bg-white rounded-3 shadow-sm p-3 mb-3 sticky-top" style="top:80px;">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-list me-2"></i>Categories
                    </h6>
                    <form method="GET" id="category-filter-form">
                        @foreach($categories as $cat)
                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       value="{{ $cat->id }}"
                                       id="category{{ $cat->id }}"
                                       name="category_ids[]"
                                       {{ request()->has('category_ids') && in_array($cat->id, request()->category_ids) ? 'checked' : '' }}>
                                <label class="form-check-label" for="category{{ $cat->id }}">
                                    {{ $cat->category_name }}
                                </label>
                            </div>
                        @endforeach
                        <button class="btn btn-primary w-100 mt-3" type="submit">Filter</button>
                    </form>
                </div>

                <!-- Optional: Add extra filters here -->
            </div>

            <!-- Main Content: Product Listing -->
            <div class="col-lg-9">
                <!-- Sorting Bar -->
                <div class="d-flex justify-content-end align-items-center mb-3">
                    <form class="d-flex gap-2" method="GET">
                        <label for="sort" class="me-1 mt-1">Sort by</label>
                        <select name="sort" id="sort" class="form-select form-select-sm w-auto"
                                onchange="this.form.submit()">
                            <option value="product_latest" {{ request('sort') == 'product_latest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_lowest" {{ request('sort') == 'price_lowest' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_highest" {{ request('sort') == 'price_highest' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name_a_z" {{ request('sort') == 'name_a_z' ? 'selected' : '' }}>Name: A to Z</option>
                            <option value="name_z_a" {{ request('sort') == 'name_z_a' ? 'selected' : '' }}>Name: Z to A</option>
                        </select>
                    </form>
                </div>
                <!-- Product Cards -->
                <div class="row g-4">
                  @forelse($products as $product)
                    <div class="col-md-6 col-xl-4">
                      <div class="card border-0 shadow-sm h-100">
                        <div class="position-relative">
                          <a href="{{ url('product/' . $product->id) }}">
                            <img src="{{ asset('front/images/product_images/small/' . $product->product_image) }}"
                                 alt="{{ $product->product_name }}"
                                 class="card-img-top rounded-3"
                                 style="aspect-ratio:3/4;object-fit:cover;">
                          </a>
                          @php
                              $discountedPrice = \App\Models\Product::getDiscountPrice($product->id);
                              $hasDiscount = $discountedPrice > 0;
                          @endphp
                          @if ($hasDiscount)
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                              {{ round((($product->product_price - $discountedPrice) / $product->product_price) * 100) }}% off
                            </span>
                          @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                          <div class="mb-2">
                            @if (isset($product->category))
                              <span class="badge bg-light text-secondary me-1">{{ strtoupper($product->category->category_name) }}</span>
                            @endif
                            @if (isset($product->section))
                              <span class="badge bg-light text-secondary me-1">{{ strtoupper($product->section->name) }}</span>
                            @endif
                            @if (isset($product->language))
                              <span class="badge bg-light text-secondary">{{ strtoupper($product->language->name) }}</span>
                            @endif
                          </div>
                          <h6 class="card-title mb-1">
                            <a href="{{ url('product/' . $product->id) }}" class="text-decoration-none text-dark">
                              {{ Str::limit($product->product_name, 64) }}
                            </a>
                          </h6>
                          <p class="text-muted small mb-2" style="min-height:2.5em;">
                            {{ Str::limit($product['description'], 80) }}
                          </p>
                          <ul class="list-inline small text-secondary mb-1">
                            <li class="list-inline-item"><b>By:</b>
                              {{ $product->authors->first()->name ?? 'N/A' }}
                              @if ($product->authors->count() > 1) +{{ $product->authors->count()-1 }} more @endif
                            </li>
                            <li class="list-inline-item"><b>Publisher:</b> {{ $product->publisher->name ?? 'N/A' }}</li>
                            <li class="list-inline-item"><b>Year:</b> {{ $product->created_at?->format('Y') }}</li>
                          </ul>
                          <div class="mt-auto d-flex flex-wrap gap-2 align-items-center">
                            @if($hasDiscount)
                                <span class="fs-5 fw-bold text-primary">₹{{ number_format($discountedPrice, 2) }}</span>
                                <del class="text-muted fs-6">₹{{ number_format($product->product_price, 2) }}</del>
                            @else
                                <span class="fs-5 fw-bold text-primary">₹{{ number_format($product->product_price, 2) }}</span>
                            @endif
                          </div>
                          <div class="d-flex gap-2 mt-3">
                              <form action="{{ url('cart/add') }}" method="POST">
                                  @csrf
                                  <input type="hidden" name="product_id" value="{{ $product->id }}">
                                  <input type="hidden" name="quantity" value="1">
                                  <button type="submit" class="btn btn-sm btn-primary" title="Add to card"><i class="flaticon-shopping-cart-1"></i></button>
                              </form>
                              <a href="{{ url('product/' . $product->id) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                              
                              <form action="{{ url('wishlist/add') }}" method="POST" class="d-flex align-items-center">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-sm bg-danger text-white "
                                    title="Add to Wishlist">
                                <i class="flaticon-heart"></i> 
                            </button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  @empty
                    <div class="col-12">
                        <div class="p-5 text-center bg-light rounded-3 shadow-sm">
                            <span style="font-size:3rem">📚</span>
                            <h5>No products found</h5>
                            <p class="text-muted">Try adjusting your filters or explore other categories.</p>
                            <a href="{{ url('/') }}" class="btn btn-secondary">Back to Home</a>
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
</section>

<!-- Newsletter remains the same; style on brand as needed -->

@endsection
