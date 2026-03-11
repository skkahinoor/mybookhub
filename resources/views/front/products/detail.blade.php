{{-- Note: front/products/detail.blade.php is the page that opens when you click on a product in the FRONT home page --}} {{-- $productDetails, categoryDetails and $totalStock are passed in from detail() method in Front/ProductsController.php --}}
@extends('front.layout.layout3')

@section('content')
    <style>
        .product-detail-wrapper {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
        }

        .main-media-col {
            padding: 20px;
            background: #F8F9FA;
            display: flex;
            align-items: center;
            justify-content: center;

        }

        .main-image-link {
            display: block;
            width: 100%;
            text-align: center;
        }

        .main-image-preview {
            width: 100%;
            max-width: 400px;
            aspect-ratio: 2/3;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
            margin: 0 auto;
        }

        .main-image-preview:hover {
            transform: scale(1.02);
        }

        .detail-content-col {
            padding: 40px;
        }

        .product-badge-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .status-badge {
            padding: 6px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 8px;
            letter-spacing: 0.5px;
        }

        .badge-cat {
            background: rgba(41, 121, 255, 0.1);
            color: var(--primary-blue);
        }

        .badge-condition {
            background: rgba(255, 107, 0, 0.1);
            color: var(--primary-orange);
        }

        .product-title {
            font-size: 28px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .rating-summary {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .stars-container {
            color: #FFB400;
            font-size: 14px;
        }

        .review-count {
            font-size: 13px;
            color: #888;
            font-weight: 500;
        }

        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding: 20px;
            background: #F8F9FB;
            border-radius: 16px;
            margin-bottom: 30px;
        }

        .highlight-item {
            display: flex;
            flex-direction: column;
        }

        .highlight-item span:first-child {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .highlight-item span:last-child {
            font-size: 14px;
            color: #333;
            font-weight: 600;
        }

        .price-block {
            margin-bottom: 30px;
        }

        .current-price {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary-orange);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .old-price {
            font-size: 18px;
            color: #bbb;
            text-decoration: line-through;
            font-weight: 500;
        }

        .action-row {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .qty-selector {
            width: 100px;
            height: 54px;
            border: 2px solid #eee;
            border-radius: 12px;
            padding: 0 10px;
            font-weight: 700;
            text-align: center;
            outline: none;
        }

        .btn-add-cart-custom {
            flex: 1;
            background: var(--primary-orange);
            color: #fff;
            border: none;
            height: 54px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.2);
        }

        .btn-add-cart-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 107, 0, 0.3);
            color: #fff;
        }

        .btn-wishlist-detail {
            width: 54px;
            height: 54px;
            border: 2px solid #eee;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff4757;
            background: #fff;
            transition: all 0.2s;
        }

        .btn-wishlist-detail:hover {
            background: #fff5f6;
            border-color: #ff4757;
        }

        @media (max-width: 991px) {
            .product-detail-wrapper {
                border-radius: 0;
                margin-bottom: 20px;
            }

            .detail-content-col {
                padding: 25px;
            }

            .product-title {
                font-size: 22px;
            }

            .highlights-grid {
                grid-template-columns: 1fr;
            }

            .action-row {
                position: fixed;
                bottom: 58px;
                left: 0;
                width: 100%;
                background: #fff;
                padding: 12px 20px;
                margin-bottom: 0;
                z-index: 10000;
                box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.05);
                gap: 10px;
                border-radius: 20px 20px 0 0;
            }

            .action-row .qty-selector {
                display: none;
            }

            .content-inner-1 {
                padding-bottom: 140px;
            }
        }
    </style>

    <section class="content-inner-1" style="background: #F4F6FB;">
        <div class="container">
            <div class="product-detail-wrapper">
                <div class="row g-0">
                    <div class="col-lg-5 main-media-col">
                        <a href="{{ asset('front/images/product_images/large/' . $productDetails['product_image']) }}"
                            class="main-image-link" style="padding: 20px;">
                            <img src="{{ asset('front/images/product_images/large/' . $productDetails['product_image']) }}"
                                alt="{{ $productDetails['product_name'] }}" class="main-image-preview">
                        </a>
                    </div>
                    <div class="col-lg-7 detail-content-col">
                        <div class="product-badge-row">
                            <span
                                class="status-badge badge-cat">{{ $productDetails['category']['category_name'] ?? 'Book' }}</span>
                            <span
                                class="status-badge badge-condition">{{ $productDetails['product_condition'] ?? 'New' }}</span>
                        </div>

                        <h1 class="product-title">{{ $productDetails['product_name'] }}</h1>

                        <div class="rating-summary">
                            <div class="stars-container">
                                @php
                                    $ratingCount = $ratingCount ?? 0;
                                    $avgRating = $avgRating ?? 0;
                                    $fullStars = floor($avgRating);
                                    $halfStar = $avgRating - $fullStars >= 0.5;
                                @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $fullStars)
                                        <i class="fas fa-star"></i>
                                    @elseif ($halfStar && $i == $fullStars + 1)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="review-count">({{ $ratingCount }} reviews)</span>

                            @php
                                $userLat = session('user_latitude');
                                $userLng = session('user_longitude');
                                $productLatLng =
                                    isset($productDetails['location']) && $productDetails['location']
                                        ? explode(',', $productDetails['location'])
                                        : [null, null];
                                $distance = null;
                                if ($userLat && $userLng && $productLatLng[0] && $productLatLng[1]) {
                                    $distance = \App\Helpers\Helper::getDistance(
                                        $userLat,
                                        $userLng,
                                        $productLatLng[0],
                                        $productLatLng[1],
                                    );
                                }
                            @endphp

                            <div class="ms-auto d-none d-md-block">
                                <i class="fa-solid fa-location-dot text-primary"></i>
                                <span class="text-muted small fw-bold">
                                    @if ($distance !== null)
                                        {{ $distance < 1 ? round($distance * 1000) . ' m' : round($distance, 2) . ' km' }}
                                    @else
                                        Location N/A
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="highlights-grid">
                            <div class="highlight-item">
                                <span>Author</span>
                                <span>{{ isset($productDetails['authors']) && count($productDetails['authors']) > 0 ? collect($productDetails['authors'])->pluck('name')->join(', ') : 'Unknown' }}</span>
                            </div>
                            <div class="highlight-item">
                                <span>Publisher</span>
                                <span>{{ $productDetails['publisher']['name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="highlight-item">
                                <span>Language</span>
                                <span>{{ $productDetails['language']['name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="highlight-item">
                                <span>ISBN</span>
                                <span>{{ $productDetails['product_isbn'] }}</span>
                            </div>
                            <div class="highlight-item">
                                <span>Availability</span>
                                <span class="{{ $totalStock > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $totalStock > 0 ? $totalStock . ' units available' : 'Out of Stock' }}
                                </span>
                            </div>
                        </div>

                        @php
                            $priceDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute(
                                $productAttribute->id,
                            );
                            $originalPrice = $priceDetails['product_price'];
                            $finalPrice = $priceDetails['final_price'];
                            $discount = $priceDetails['discount'];
                        @endphp

                        <div class="price-block">
                            <div class="current-price">
                                ₹{{ number_format($finalPrice, 2) }}
                                @if ($discount)
                                    <span class="old-price">₹{{ number_format($originalPrice, 2) }}</span>
                                    <span
                                        style="font-size: 11px; background: #FF3B30; color: white; padding: 4px 10px; border-radius: 20px; font-weight: 700; margin-left: 10px; display: inline-flex; align-items: center; justify-content: center; height: 24px;">SALE</span>
                                @endif
                            </div>
                        </div>

                        <div class="action-row">
                            <form action="{{ url('cart/add') }}" method="POST" class="d-flex flex-grow-1 gap-2">
                                @csrf
                                <input type="hidden" name="product_attribute_id" value="{{ $productAttribute->id }}">
                                <input type="number" name="quantity" value="1" min="1" max="{{ $productAttribute->stock }}" class="qty-selector">
                                <button type="submit" class="btn-add-cart-custom">
                                    <i class="fas fa-shopping-bag"></i> Add to Cart
                                </button>
                            </form>
                            <form action="{{ url('wishlist/add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_attribute_id" value="{{ $productAttribute->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-wishlist-detail" title="Add to Wishlist">
                                    <i class="far fa-heart"></i>
                                </button>
                            </form>
                        </div>

                        <div class="short-desc">
                            <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
                                {{ Str::limit($productDetails['description'], 180) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .tabs-container {
                    background: #fff;
                    border-radius: 20px;
                    padding: 30px;
                    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.03);
                }

                .nav-tabs-custom {
                    border: none;
                    display: flex;
                    gap: 30px;
                    border-bottom: 2px solid #F0F2F5;
                    margin-bottom: 30px;
                }

                .nav-tabs-custom li a {
                    font-size: 16px;
                    font-weight: 700;
                    color: #888;
                    padding: 15px 0;
                    display: block;
                    position: relative;
                    text-decoration: none;
                }

                .nav-tabs-custom li a.active {
                    color: var(--primary-blue);
                }

                .nav-tabs-custom li a.active::after {
                    content: '';
                    position: absolute;
                    bottom: -2px;
                    left: 0;
                    width: 100%;
                    height: 2px;
                    background: var(--primary-blue);
                }

                .review-card {
                    display: flex;
                    gap: 15px;
                    margin-bottom: 25px;
                    padding-bottom: 25px;
                    border-bottom: 1px solid #F0F2F5;
                }

                .review-avatar {
                    width: 48px;
                    height: 48px;
                    border-radius: 50%;
                    object-fit: cover;
                }

                .review-content-box {
                    flex: 1;
                }

                .review-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 5px;
                }

                .reviewer-name {
                    font-size: 15px;
                    font-weight: 700;
                    color: #333;
                }

                .review-date {
                    font-size: 12px;
                    color: #AAA;
                }

                .review-stars {
                    color: #FFB400;
                    font-size: 12px;
                    margin-bottom: 8px;
                }

                .review-text {
                    font-size: 14px;
                    color: #666;
                    line-height: 1.6;
                }

                .review-form-box {
                    background: #F8F9FB;
                    padding: 25px;
                    border-radius: 16px;
                    margin-top: 40px;
                }

                .review-form-title {
                    font-size: 18px;
                    font-weight: 800;
                    margin-bottom: 20px;
                }

                /* Related Books Custom CSS */
                .related-book-card {
                    background: #fff;
                    border: 1px solid #eee;
                    border-radius: 12px;
                    padding: 12px;
                    transition: all 0.2s;
                    text-decoration: none !important;
                }

                .related-book-card:hover {
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                    transform: translateY(-2px);
                    border-color: #ddd;
                }

                .related-book-card .dz-media {
                    width: 75px;
                    height: 100px;
                    border-radius: 6px;
                    overflow: hidden;
                    background: #fff;
                    flex-shrink: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 4px;
                }

                .related-book-card .dz-media img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain;
                }

                .related-book-card .dz-content {
                    flex: 1;
                    min-width: 0;
                }

                .related-book-card .subtitle {
                    font-size: 14px;
                    font-weight: 700;
                    margin-bottom: 4px;
                    line-height: 1.3;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .related-book-card .dz-tags {
                    list-style: none;
                    padding: 0;
                    margin: 0 0 6px 0;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 4px;
                    font-size: 11px;
                    color: #888;
                }

                .related-book-card .dz-tags li {
                    background: #f0f2f5;
                    padding: 2px 6px;
                    border-radius: 4px;
                }

                .related-book-card .price {
                    font-size: 15px;
                    font-weight: 700;
                    color: var(--primary-orange);
                    display: flex;
                    align-items: center;
                }

                .related-book-card .price del {
                    font-size: 12px;
                    color: #aaa;
                    font-weight: 400;
                    margin-left: 6px;
                }
            </style>

            <div class="row">
                <div class="col-xl-8">
                    <div class="tabs-container mb-5">
                        <ul class="nav nav-tabs-custom" role="tablist">
                            <li><a data-bs-toggle="tab" href="#details-tab" class="active">Product Details</a></li>
                            <li><a data-bs-toggle="tab" href="#reviews-tab">Reviews ({{ $ratingCount }})</a></li>
                        </ul>

                        <div class="tab-content">
                            <!-- Details Tab -->
                            <div id="details-tab" class="tab-pane show active">
                                <div class="table-responsive">
                                    <table class="table table-borderless book-overview">
                                        <tr style="border-bottom: 1px solid #f8f9fa;">
                                            <th width="30%" class="text-muted fw-bold small text-uppercase">Title</th>
                                            <td class="fw-bold">{{ $productDetails['product_name'] }}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #f8f9fa;">
                                            <th class="text-muted fw-bold small text-uppercase">Author</th>
                                            <td>{{ isset($productDetails['authors']) && count($productDetails['authors']) > 0 ? collect($productDetails['authors'])->pluck('name')->join(', ') : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #f8f9fa;">
                                            <th class="text-muted fw-bold small text-uppercase">ISBN</th>
                                            <td>{{ $productDetails['product_isbn'] }}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #f8f9fa;">
                                            <th class="text-muted fw-bold small text-uppercase">Publisher</th>
                                            <td>{{ $productDetails['publisher']['name'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #f8f9fa;">
                                            <th class="text-muted fw-bold small text-uppercase">Full Info</th>
                                            <td>{{ $productDetails['description'] }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            

                            <!-- Reviews Tab -->
                            <div id="reviews-tab" class="tab-pane fade">
                                <div class="reviews-list">
                                    @forelse ($ratings as $rating)
                                        <div class="review-card">
                                            <img src="{{ asset('front/images/profile-default.png') }}"
                                                class="review-avatar" alt="User">
                                            <div class="review-content-box">
                                                <div class="review-header">
                                                    <span
                                                        class="reviewer-name">{{ $rating['user']['name'] ?? 'Anonymous' }}</span>
                                                    <span
                                                        class="review-date">{{ \Carbon\Carbon::parse($rating['created_at'])->format('M d, Y') }}</span>
                                                </div>
                                                <div class="review-stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="{{ $i <= $rating['rating'] ? 'fas' : 'far' }} fa-star"></i>
                                                    @endfor
                                                </div>
                                                <p class="review-text">{{ $rating['review'] }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted text-center py-4">No reviews yet. Be the first to share your
                                            thoughts!</p>
                                    @endforelse
                                </div>

                                <!-- Post Review Form -->
                                <div class="review-form-box">
                                    <h4 class="review-form-title">Leave a Review</h4>
                                    @auth
                                        <form method="POST" action="{{ url('rating/add') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $productDetails['id'] }}">
                                            <input type="hidden" name="product_attribute_id"
                                                value="{{ $productAttribute->id }}">

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">YOUR RATING</label>
                                                <select name="rating" class="form-select"
                                                    style="border-radius: 10px; border: 1px solid #eee;" required>
                                                    <option value="5">5 Stars (Excellent)</option>
                                                    <option value="4">4 Stars (Great)</option>
                                                    <option value="3">3 Stars (Average)</option>
                                                    <option value="2">2 Stars (Poor)</option>
                                                    <option value="1">1 Star (Very Bad)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">YOUR REVIEW</label>
                                                <textarea name="review" class="form-control" rows="4" style="border-radius: 12px; border: 1px solid #eee;"
                                                    placeholder="Tell us what you liked or disliked about this book..." required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100"
                                                style="padding: 12px; border-radius: 12px; font-weight: 700;">
                                                Submit Review
                                            </button>
                                        </form>
                                    @else
                                        <div class="text-center py-3">
                                            <p class="text-muted mb-3">Please login to write a review.</p>
                                            <a href="{{ url('user/login') }}" class="btn btn-outline-primary btn-sm">Login to
                                                Review</a>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 mt-5 mt-xl-0">
                    <div class="widget"
                        style="background: #fff; padding: 25px; border-radius: 20px; box-shadow: 0 5px 25px rgba(0,0,0,0.03);">
                        <h4 class="widget-title mb-4" style="font-weight: 800; font-size: 18px;">Related Books</h4>

                        <div class="row">
                            @if ($similarProducts->count() > 0)
                                @foreach ($similarProducts as $similarProduct)
                                    @php
                                        $product = $similarProduct->product;
                                        $priceDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute(
                                            $similarProduct->id,
                                        );

                                        $ratingQuerySimilar = \App\Models\Rating::where('product_id', $product->id)
                                            ->where('product_attribute_id', $similarProduct->id)
                                            ->where('status', 1);
                                        $ratingCount = $ratingQuerySimilar->count();
                                        $avgRating = $ratingCount ? round($ratingQuerySimilar->avg('rating'), 1) : 0;
                                        $fullStars = floor($avgRating);
                                        $halfStar = $avgRating - $fullStars >= 0.5;
                                        $priceDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute(
                                            $similarProduct->id,
                                        );
                                    @endphp

                                    <div class="col-xl-12 col-lg-6 mb-4">
                                        <div class="related-book-card d-flex gap-3 align-items-center">

                                            {{-- IMAGE --}}
                                            <div class="dz-media">
                                                <a href="{{ url('product/' . $similarProduct->id) }}">
                                                    <img src="{{ asset('front/images/product_images/small/' . ($product->product_image ?? 'no-image.png')) }}"
                                                        class="img-fluid"
                                                        onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'">
                                                </a>
                                            </div>

                                            <div class="dz-content">

                                                {{-- TITLE --}}
                                                <h5 class="subtitle">
                                                    <a href="{{ url('product/' . $similarProduct->id) }}"
                                                        class="text-dark">
                                                        {{ $product->product_name }}
                                                    </a>
                                                </h5>

                                                {{-- META --}}
                                                <ul class="dz-tags">
                                                    @if ($product->publisher)
                                                        <li>{{ strtoupper($product->publisher->name) }}</li>
                                                    @endif
                                                    @if ($product->category)
                                                        <li>{{ strtoupper($product->category->category_name) }}</li>
                                                    @endif
                                                    @if ($product->authors->isNotEmpty())
                                                        <li>{{ strtoupper($product->authors->pluck('name')->join(', ')) }}
                                                        </li>
                                                    @endif
                                                </ul>

                                                {{-- ⭐ STAR RATING --}}
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="d-flex me-2">

                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= $fullStars)
                                                                {{-- FULL STAR --}}
                                                                <i class="flaticon-star me-1"
                                                                    style="color:#f4b400;font-size:14px;"></i>
                                                            @elseif ($halfStar && $i == $fullStars + 1)
                                                                {{-- TRUE HALF STAR (INLINE OVERLAY) --}}
                                                                <span class="me-1"
                                                                    style="position:relative;display:inline-block;width:14px;height:14px;">
                                                                    {{-- Empty star --}}
                                                                    <i class="flaticon-star"
                                                                        style="color:#ddd;font-size:14px;position:absolute;left:0;top:0;"></i>

                                                                    {{-- Half filled star --}}
                                                                    <i class="flaticon-star"
                                                                        style="color:#f4b400;font-size:14px;position:absolute;left:0;top:0;
                              clip-path: inset(0 50% 0 0);"></i>
                                                                </span>
                                                            @else
                                                                {{-- EMPTY STAR --}}
                                                                <i class="flaticon-star me-1"
                                                                    style="color:#ddd;font-size:14px;"></i>
                                                            @endif
                                                        @endfor

                                                    </div>

                                                    <small class="text-muted">
                                                        {{ $avgRating > 0 ? number_format($avgRating, 1) . '/5' : 'No ratings' }}
                                                        @if ($ratingCount > 0)
                                                            ({{ $ratingCount }})
                                                        @endif
                                                    </small>
                                                </div>



                                                {{-- PRICE --}}
                                                <div class="price mb-2">
                                                    <span class="price-num">
                                                        ₹{{ number_format($priceDetails['final_price'], 2) }}
                                                    </span>
                                                    @if ($priceDetails['discount'] > 0)
                                                        <del>₹{{ number_format($priceDetails['product_price'], 2) }}</del>
                                                    @endif
                                                </div>

                                                {{-- ADD TO CART --}}
                                                <form action="{{ url('cart/add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_attribute_id"
                                                        value="{{ $similarProduct->id }}">
                                                    <input type="hidden" name="quantity" value="1">

                                                    <button type="submit" class="btn btn-sm"
                                                        style="background:var(--primary-orange);color:white;font-weight:600;padding:6px 14px;border-radius:6px;border:none;font-size:12px;transition:0.2s;">
                                                        <i class="flaticon-shopping-cart-1" style="font-size:12px"></i>
                                                        <span>Add to cart</span>
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">No related books found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </section>


    {{-- <!-- Feature Box -->
    <section class="content-inner" style="background: #F4F6FB; padding: 60px 0;">
        <div class="container">
            <div class="row sp15 justify-content-center">
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="feature-card text-center p-4"
                        style="background: #fff; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); height: 100%;">
                        <div class="icon-box mb-3 mx-auto"
                            style="width: 50px; height: 50px; background: rgba(41, 121, 255, 0.1); color: var(--primary-blue); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="font-size: 22px;">{{ number_format($totalUsers) }}</h3>
                        <p class="text-muted small mb-0">Happy Customers</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="feature-card text-center p-4"
                        style="background: #fff; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); height: 100%;">
                        <div class="icon-box mb-3 mx-auto"
                            style="width: 50px; height: 50px; background: rgba(255, 107, 0, 0.1); color: var(--primary-orange); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fa-solid fa-book"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="font-size: 22px;">{{ number_format($totalProducts) }}</h3>
                        <p class="text-muted small mb-0">Book Collections</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="feature-card text-center p-4"
                        style="background: #fff; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); height: 100%;">
                        <div class="icon-box mb-3 mx-auto"
                            style="width: 50px; height: 50px; background: rgba(0, 200, 83, 0.1); color: #00C853; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="font-size: 22px;">{{ number_format($totalVendors) }}</h3>
                        <p class="text-muted small mb-0">Our Stores</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="feature-card text-center p-4"
                        style="background: #fff; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); height: 100%;">
                        <div class="icon-box mb-3 mx-auto"
                            style="width: 50px; height: 50px; background: rgba(156, 39, 176, 0.1); color: #9C27B0; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fa-solid fa-pen"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="font-size: 22px;">{{ number_format($totalAuthors) }}</h3>
                        <p class="text-muted small mb-0">Famous Writers</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Feature Box End -->

    <!-- Newsletter -->
    <section class="py-5 newsletter-wrapper"
        style="background-image: url('images/background/bg1.jpg'); background-size: cover;">
        <div class="container">
            <div class="subscride-inner">
                <div
                    class="row style-1 justify-content-xl-between justify-content-lg-center align-items-center text-xl-start text-center">
                    <div class="col-xl-7 col-lg-12">
                        <div class="section-head mb-0">
                            <h2 class="title text-white my-lg-3 mt-0">Subscribe our newsletter for newest books updates
                            </h2>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-6">
                        <form class="dzSubscribe style-1" action="script/mailchamp.php" method="post">
                            <div class="dzSubscribeMsg"></div>
                            <div class="form-group">
                                <div class="input-group mb-0">
                                    <input name="dzEmail" required="required" type="email"
                                        class="form-control bg-transparent text-white" placeholder="Your Email Address">
                                    <div class="input-group-addon">
                                        <button name="submit" value="Submit" type="submit"
                                            class="btn btn-primary btnhover">
                                            <span>SUBSCRIBE</span>
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Newsletter End --> --}}

@endsection
