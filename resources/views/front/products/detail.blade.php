{{-- Note: front/products/detail.blade.php is the page that opens when you click on a product in the FRONT home page --}} {{-- $productDetails, categoryDetails and $totalStock are passed in from detail() method in Front/ProductsController.php --}}
@extends('front.layout.layout3')

@section('content')
    <section class="content-inner-1">
        <div class="container">
            <div class="row book-grid-row style-4 m-b60">
                <div class="col">
                    @if (Session::has('error_message'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>{{ Session::get('error_message') }}</strong>
                        </div>
                    @endif
                    <div class="dz-box">
                        <div class="dz-media">
                            <a href="{{ asset('front/images/product_images/large/' . $productDetails['product_image']) }}"
                                class="main-image-link">
                                <img src="{{ asset('front/images/product_images/large/' . $productDetails['product_image']) }}"
                                    alt="{{ $productDetails['product_name'] }}" class="img-fluid rounded shadow-sm"
                                    style="width: 398px; height: 572px; object-fit: fill;">
                            </a>

                        </div>
                        <div class="dz-content">
                            <div class="dz-header">
                                <h3 class="title">{{ $productDetails['product_name'] }}</h3>
                                <div class="shop-item-rating">
                                    @php
                                        $ratingCount = $ratingCount ?? 0;
                                        $avgRating = $avgRating ?? 0;

                                        $fullStars = floor($avgRating);
                                        $halfStar = $avgRating - $fullStars >= 0.5;
                                    @endphp

                                    <div class="d-lg-flex d-sm-inline-flex d-flex align-items-center">

                                        <div class="text-warning me-2">
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

                                        <h6 class="m-b0 me-1">
                                            {{ $avgRating > 0 ? number_format($avgRating, 1) : '0.0' }}
                                        </h6>

                                        <span class="text-muted">
                                            ({{ $ratingCount }} {{ $ratingCount === 1 ? 'Review' : 'Reviews' }})
                                        </span>
                                    </div>

                                    {{-- SOCIAL SHARE --}}
                                    <div class="social-area">
                                        <ul class="dz-social-icon style-3">
                                            <li>
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                                                    target="_blank">
                                                    <i class="fa-brands fa-facebook-f"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}"
                                                    target="_blank">
                                                    <i class="fa-brands fa-twitter"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://wa.me/?text={{ url()->current() }}" target="_blank">
                                                    <i class="fa-brands fa-whatsapp"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="mailto:?subject=Check this book&body={{ url()->current() }}">
                                                    <i class="fa-solid fa-envelope"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                            <div class="dz-body">
                                <div class="book-detail">
                                    <ul class="book-info">

                                        <li><span>Writen
                                                by</span>{{ isset($productDetails['authors']) && count($productDetails['authors']) > 0 ? collect($productDetails['authors'])->pluck('name')->join(', ') : 'N/A' }}
                                        </li>
                                        <li><span>Publisher</span>{{ $productDetails['publisher']['name'] ?? 'N/A' }}</li>
                                        <li><span>Language</span>{{ $productDetails['language']['name'] ?? 'N/A' }}</li>
                                        <li><span>ISBN</span>ISBN-{{ $productDetails['product_isbn'] }}</li>
                                        <li><span>Year</span>{{ $productDetails['product_year'] ?? 'N/A' }}</li>
                                        <li>
                                            <span>Stock</span>
                                            @if ($totalStock > 0)
                                                {{ $totalStock }}
                                            @else
                                                <span class="text-danger">Out of Stock</span>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                                <div class="book-detail">
                                    <ul class="book-info">
                                        <li>Description<span>{{ $productDetails['description'] }}</span></li>
                                    </ul>
                                </div>


                                {{-- location --}}
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
                                <p>
                                    <i class="fa-solid fa-location-dot"></i> :
                                    <span>
                                        @if ($distance !== null)
                                            {{ $distance < 1 ? round($distance * 1000) . ' m' : round($distance, 2) . ' km' }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </p>
                                @php
                                    $attributeId = $productAttribute->id;

                                    $priceDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute(
                                        $attributeId,
                                    );

                                    $originalPrice = $priceDetails['product_price'];
                                    $finalPrice = $priceDetails['final_price'];
                                    $discount = $priceDetails['discount'];
                                @endphp

                                <div class="book-footer">
                                    <div class="price">
                                        <h5>₹{{ number_format($finalPrice, 2) }}</h5>
                                        @if ($discount)
                                            <p class="p-lr10">
                                                <del>₹{{ number_format($originalPrice, 2) }}</del>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="product-num">
                                        <form action="{{ url('cart/add') }}" method="POST"
                                            class="d-flex align-items-center">
                                            @csrf
                                            {{-- <input type="hidden" name="product_id" value="{{ $productDetails['id'] }}"> --}}
                                            <input type="hidden" name="product_attribute_id"
                                                value="{{ $productAttribute->id }}">
                                            <div class="quantity btn-quantity style-1 me-3">
                                                <input id="quantity_top" type="number" value="1" min="1"
                                                    name="quantity" />
                                            </div>
                                          <button type="submit" class="btn btn-primary btnhover2"><i
                                                    class="flaticon-shopping-cart-1"></i> <span>&nbsp;&nbsp;Add to
                                                    cart</span></button>
                                        </form>

                                        <form action="{{ url('wishlist/add') }}" method="POST"
                                            class="d-flex align-items-center">
                                            @csrf

                                            {{-- IMPORTANT: vendor-specific --}}
                                            <input type="hidden" name="product_attribute_id"
                                                value="{{ $productAttribute->id }}">
                                            <input type="hidden" name="quantity" value="1">

                                            <button type="submit" class="btn btn-outline-danger ms-2 item-addwishlist"
                                                title="Add to Wishlist">
                                                <i class="flaticon-heart"></i>
                                                <span>&nbsp;&nbsp;Add to Wishlist</span>
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="product-description tabs-site-button">
                        <ul class="nav nav-tabs">
                            <li><a data-bs-toggle="tab" href="#graphic-design-1" class="active">Details Product</a></li>
                            <li><a data-bs-toggle="tab" href="#developement-1">Customer Reviews</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="graphic-design-1" class="tab-pane show active">
                                <table class="table border book-overview">
                                    <tr>
                                        <th>Book Title</th>
                                        <td>{{ $productDetails['product_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Author</th>
                                        <td>{{ isset($productDetails['authors']) && count($productDetails['authors']) > 0 ? collect($productDetails['authors'])->pluck('name')->join(', ') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>ISBN</th>
                                        <td>{{ $productDetails['product_isbn'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Language</th>
                                        <td>{{ $productDetails['language']['name'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Categories</th>
                                        <td>{{ $productDetails['category']['category_name'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Book Format</th>
                                        <td>Paperback, 450 Pages</td>
                                    </tr>
                                    <tr>
                                        <th>Date Published</th>
                                        <td>August 10th 2019</td>
                                    </tr>
                                    <tr>
                                        <th>Publisher</th>
                                        <td>{{ $productDetails['publisher']['name'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Pages</th>
                                        <td>520</td>
                                    </tr>
                                    <tr>
                                        <th>Lesson</th>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <th>Topic</th>
                                        <td>360</td>
                                    </tr>
                                    <tr class="tags">
                                        <th>Tags</th>
                                        <td>
                                            <a href="javascript:void(0);" class="badge">Drama</a>
                                            <a href="javascript:void(0);" class="badge">Advanture</a>
                                            <a href="javascript:void(0);" class="badge">Survival</a>
                                            <a href="javascript:void(0);" class="badge">Biography</a>
                                            <a href="javascript:void(0);" class="badge">Trending2022</a>
                                            <a href="javascript:void(0);" class="badge">Bestseller</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div id="developement-1" class="tab-pane active">
                                <div class="clear" id="comment-list">
                                    <div class="post-comments comments-area style-1 clearfix">

                                        {{-- TITLE --}}
                                        <h4 class="comments-title">
                                            {{ $ratingCount }} {{ $ratingCount === 1 ? 'Review' : 'Reviews' }}
                                        </h4>

                                        {{-- COMMENTS --}}
                                        <div id="comment">
                                            <ol class="comment-list">

                                                @forelse ($ratings as $rating)
                                                    @php
                                                        $stars = (int) $rating['rating'];
                                                    @endphp

                                                    <li class="comment even depth-1">
                                                        <div class="comment-body">

                                                            {{-- AUTHOR --}}
                                                            <div class="comment-author vcard">
                                                                <img src="{{ asset('front/images/profile-default.png') }}"
                                                                    alt="avatar" class="avatar" />

                                                                <cite class="fn">
                                                                    {{ $rating['user']['name'] ?? 'Anonymous' }}
                                                                </cite>
                                                                <span class="says">says:</span>

                                                                <div class="comment-meta">
                                                                    <a href="javascript:void(0);">
                                                                        {{ \Carbon\Carbon::parse($rating['created_at'])->format('F d, Y \a\t h:i A') }}
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            {{-- ⭐ STAR RATING --}}
                                                            <div class="d-flex align-items-center mb-2">

                                                                {{-- ⭐ STARS --}}
                                                                <div class="d-flex me-2">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= $stars)
                                                                            <i class="flaticon-star me-1"
                                                                                style="color:#f4b400;font-size:16px;"></i>
                                                                        @else
                                                                            <i class="flaticon-star me-1"
                                                                                style="color:#ddd;font-size:16px;"></i>
                                                                        @endif
                                                                    @endfor
                                                                </div>

                                                                {{-- ⭐ RATING TEXT --}}
                                                                <small class="text-muted">
                                                                    {{ number_format($stars, 1) }}/5
                                                                </small>

                                                            </div>


                                                            {{-- REVIEW TEXT --}}
                                                            <div class="comment-content dlab-page-text">
                                                                <p>{{ $rating['review'] }}</p>
                                                            </div>

                                                        </div>
                                                    </li>
                                                @empty
                                                    <li>
                                                        <p class="text-muted">No reviews yet. Be the first to review this
                                                            book.</p>
                                                    </li>
                                                @endforelse

                                            </ol>
                                        </div>

                                        {{-- LEAVE A REVIEW --}}
                                        <div class="default-form comment-respond style-1" id="respond">
                                            <h4 class="comment-reply-title">
                                                LEAVE A REVIEW
                                            </h4>

                                            @auth
                                                <form method="POST" action="{{ url('rating/add') }}" class="comment-form">
                                                    @csrf

                                                    <input type="hidden" name="product_id"
                                                        value="{{ $productDetails['id'] }}">
                                                    <input type="hidden" name="product_attribute_id"
                                                        value="{{ $productAttribute->id }}">

                                                    {{-- STAR SELECT --}}
                                                    <p>
                                                        <label class="d-block mb-1">Your Rating</label>
                                                        <select name="rating" class="form-control" required>
                                                            <option value="">Select Rating</option>
                                                            @for ($i = 5; $i >= 1; $i--)
                                                                <option value="{{ $i }}">{{ $i }} Star
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </p>

                                                    {{-- REVIEW --}}
                                                    <p class="comment-form-comment">
                                                        <textarea name="review" placeholder="Write your review here..." class="form-control4" rows="3" required></textarea>
                                                    </p>

                                                    <p class="form-submit">
                                                        <button type="submit" class="submit btn btn-primary filled">
                                                            Submit Review <i class="fa fa-angle-right m-l10"></i>
                                                        </button>
                                                    </p>
                                                </form>
                                            @else
                                                <p class="text-muted">
                                                    Please <a href="{{ url('user/login') }}">login</a> to write a review.
                                                </p>
                                            @endauth
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-xl-4 mt-5 mt-xl-0">
                    <div class="widget">
                        <h4 class="widget-title">Related Books</h4>

                        <div class="row">
                            @if ($similarProducts->count() > 0)
                                @foreach ($similarProducts as $similarProduct)
                                    @php
                                        $product = $similarProduct->product;

                                        // 💰 PRICE
                                        $priceDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute(
                                            $similarProduct->id,
                                        );

                                        // ⭐ RATING LOGIC
                                        $ratingQuery = \App\Models\Rating::where('product_id', $product->id)
                                            ->where('product_attribute_id', $similarProduct->id)
                                            ->where('status', 1);

                                        $ratingCount = $ratingQuery->count();
                                        $avgRating = $ratingCount ? round($ratingQuery->avg('rating'), 1) : 0;
                                        $fullStars = floor($avgRating);
                                        $halfStar = $avgRating - $fullStars >= 0.5;
                                    @endphp

                                    <div class="col-xl-12 col-lg-6">
                                        <div class="dz-shop-card style-5">

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
                                                        <li>{{ strtoupper($product->publisher->name) }},</li>
                                                    @endif
                                                    @if ($product->category)
                                                        <li>{{ strtoupper($product->category->category_name) }},</li>
                                                    @endif
                                                    @if ($product->authors->isNotEmpty())
                                                        <li>{{ strtoupper($product->authors->first()->name) }}</li>
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
                                                {{-- <form action="{{ url('cart/add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_attribute_id"
                                                        value="{{ $similarProduct->id }}">
                                                    <input type="hidden" name="quantity" value="1">

                                                    <button type="submit" class="btn btn-primary btnhover2">
                                                        <i class="flaticon-shopping-cart-1"></i>
                                                        <span>&nbsp;&nbsp;Add to cart</span>
                                                    </button>
                                                </form> --}}

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


    <!-- Feature Box -->
    <section class="content-inner">
        <div class="container">
            <div class="row sp15">
                <div class="col-lg-3 col-md-6 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="icon-bx-wraper style-2 m-b30 text-center">
                        <div class="icon-bx-lg">
                            <i class="fa-solid fa-users icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h2 class="dz-title counter m-b0">{{ number_format($totalUsers) }}</h2>
                            <p class="font-20">Happy Customers</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="icon-bx-wraper style-2 m-b30 text-center">
                        <div class="icon-bx-lg">
                            <i class="fa-solid fa-book icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h2 class="dz-title counter m-b0">{{ number_format($totalProducts) }}</h2>
                            <p class="font-20">Book Collections</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="icon-bx-wraper style-2 m-b30 text-center">
                        <div class="icon-bx-lg">
                            <i class="fa-solid fa-store icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h2 class="dz-title counter m-b0">{{ number_format($totalVendors) }}</h2>
                            <p class="font-20">Our Stores</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="icon-bx-wraper style-2 m-b30 text-center">
                        <div class="icon-bx-lg">
                            <i class="fa-solid fa-pen icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h2 class="dz-title counter m-b0">{{ number_format($totalAuthors) }}</h2>
                            <p class="font-20">Famous Writers</p>
                        </div>
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
    <!-- Newsletter End -->

@endsection
