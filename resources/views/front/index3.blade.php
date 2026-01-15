@extends('front.layout.layout3')

@section('content')

    <style>
        /* Ensure consistent banner height with responsive breakpoints */
        #carouselExampleAutoplaying .carousel-item img {
            width: 100%;
            height: 360px;
            object-fit: cover;
        }

        @media (min-width: 576px) {
            #carouselExampleAutoplaying .carousel-item img {
                height: 420px;
            }
        }

        @media (min-width: 992px) {
            #carouselExampleAutoplaying .carousel-item img {
                height: 520px;
            }
        }

        @media (min-width: 1400px) {
            #carouselExampleAutoplaying .carousel-item img {
                height: 600px;
            }
        }

        /* Utility in case any .dz-media images need cover behavior */
        .img-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-item .p-3 {
            padding: 1.5rem !important;
        }

        .carousel-item img {
            border-radius: 10px !important;
            padding: 0px !important;
            background: #fff;
        }



        @media (min-width: 300px) and (max-width: 399px) {
            .carousel-item .p-3 {
                padding: 0.5rem !important;
            }

            .bookhub-carousel-image {
                object-fit: cover;
                height: 150px !important;
            }
        }

        @media (min-width: 400px) and (max-width: 768px) {

            .carousel-item .p-3 {
                padding: 0.5rem !important;
            }

            .bookhub-carousel-image {
                object-fit: cover;
                height: 250px !important;
            }
        }

        @media (min-width: 769px) and (max-width: 992px) {

            .carousel-item .p-3 {
                padding: 0.5rem !important;
            }

            .bookhub-carousel-image {
                object-fit: cover;
                height: 350px !important;
            }
        }

        @media (min-width: 992px) and (max-width: 1100px) {

            .carousel-item .p-3 {
                padding: 0.5rem !important;
            }

            .bookhub-carousel-image {
                object-fit: cover;
                height: 450px !important;
            }
        }

        @media (min-width: 1101px) {

            .carousel-item .p-3 {
                padding: 0.5rem !important;
            }

            .bookhub-carousel-image {
                object-fit: cover;
                height: 550px !important;
            }
        }


        /* book recommend  */
        .book-grid-card {
            transition: box-shadow .2s, transform .2s;
        }

        .book-grid-card:hover {
            box-shadow: 0 8px 24px rgba(60, 80, 120, 0.13);
            transform: translateY(-3px) scale(1.015);
        }

        .card-img-top {
            border-radius: 20px 20px 0 0 !important;
        }

        @media (max-width: 767.98px) {
            .card-img-top {
                height: 140px !important;
            }
        }
    </style>
    <!-- Banner Section Start -->
    @php
        $hasSliderBanners = !empty($sliderBanners) && is_iterable($sliderBanners) && count($sliderBanners) > 0;
    @endphp
    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @if ($hasSliderBanners)
                @foreach ($sliderBanners as $banner)
                    @php
                        $image = $banner['image'] ?? null;
                        $alt = $banner['alt'] ?? ($banner['title'] ?? '');
                        $link = $banner['link'] ?? null;
                        $title = $banner['title'] ?? '';
                        $desc = $banner['description'] ?? '';
                    @endphp
                    @if (!empty($image))
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="p-3">
                                <a href="{{ !empty($link) ? url($link) : 'javascript:;' }}">
                                    <img src="{{ asset('front/images/banner_images/' . $image) }}"
                                        class="d-block w-100 bookhub-carousel-image rounded-4 shadow"
                                        alt="{{ $alt }}">
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="carousel-item active">
                    <div class="d-flex align-items-center justify-content-center p-3"
                        style="height: 300px; background:#f2f2f2; border-radius:20px;">
                        <span>No banners available</span>
                    </div>
                </div>
            @endif
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <!-- Banner Section End -->

    <!-- Recommend Section Start -->
    <section class="content-inner-1 bg-grey reccomend py-5">
        <div class="container">

            <div class="section-head text-center mb-4">
                <h2 class="title">Recommended For You</h2>
                <p>Discover titles picked just for you — find your next great read!</p>
            </div>

            <div class="row g-4">

                @foreach ($sliderProducts as $sliderProduct)
                    @php
                        $product = $sliderProduct->product;
                        if (!$product) {
                            continue;
                        }

                        $attributeId = $sliderProduct->id;

                        $ratings = $sliderProduct->ratings;
                        $avgRating = $ratings->count() ? round($ratings->avg('rating'), 1) : 0;
                        $fullStars = floor($avgRating);
                        $halfStar = $avgRating - $fullStars >= 0.5;

                        // PRICE
                        $originalPrice = (float) $product->product_price;
                        $discount = (float) ($sliderProduct->product_discount ?? 0);
                        $finalPrice =
                            $discount > 0
                                ? round($originalPrice - ($originalPrice * $discount) / 100)
                                : round($originalPrice);
                    @endphp

                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                        <div class="card flex-fill shadow-sm border-0" style="border-radius:20px; overflow:hidden;">

                            {{-- IMAGE --}}
                            <div class="position-relative">
                                <a href="{{ url('product/' . $attributeId) }}">
                                    <img src="{{ asset('front/images/product_images/small/' . ($product->product_image ?? 'no-image.png')) }}"
                                        class="card-img-top" style="height:220px;object-fit:cover;"
                                        onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'">
                                </a>

                                @if ($discount > 0)
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                        -{{ round($discount) }}%
                                    </span>
                                @endif
                            </div>

                            {{-- BODY --}}
                            <div class="card-body d-flex flex-column">

                                <h6 class="mb-1 book-title-truncate">
                                    <a href="{{ url('product/' . $attributeId) }}" class="text-dark text-decoration-none">
                                        {{ $product->product_name }}
                                    </a>
                                </h6>

                                {{-- PRICE --}}
                                <div class="mb-1">
                                    @if ($discount > 0)
                                        <span class="text-muted text-decoration-line-through small">
                                            ₹{{ round($originalPrice) }}
                                        </span>
                                        <span class="fw-bold text-primary ms-1">
                                            ₹{{ $finalPrice }}
                                        </span>
                                    @else
                                        <span class="fw-bold text-primary">
                                            ₹{{ $finalPrice }}
                                        </span>
                                    @endif
                                </div>

                                {{-- ⭐ STAR RATING --}}
                                <div class="d-flex align-items-center mb-2">
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

                                    <small class="text-muted">
                                        {{ $avgRating > 0 ? $avgRating . '/5' : 'No ratings' }}
                                        @if ($ratings->count())
                                            ({{ $ratings->count() }})
                                        @endif
                                    </small>
                                </div>

                                {{-- SHOP --}}
                                <small class="text-muted mb-2">
                                    Shop:
                                    {{ $sliderProduct->vendor->vendorbusinessdetails->shop_name ?? 'N/A' }}
                                </small>

                                {{-- ADD TO CART --}}
                                <form action="{{ url('cart/add') }}" method="POST" class="mt-auto">
                                    @csrf
                                    <input type="hidden" name="product_attribute_id" value="{{ $attributeId }}">
                                    <input type="hidden" name="quantity" value="1">

                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="flaticon-shopping-cart-1"></i> Add to cart
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>
    <!-- Recommend Section End -->

    <!-- icon-box1 -->
    <section class="content-inner-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="icon-bx-wraper style-1 m-b30 text-center">
                        <div class="icon-bx-sm m-b10">
                            <i class="flaticon-power icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h5 class="dz-title m-b10">Quick Delivery</h5>
                            <p>Fast delivery to your doorstep—most orders ship within 24 hours.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="icon-bx-wraper style-1 m-b30 text-center">
                        <div class="icon-bx-sm m-b10">
                            <i class="flaticon-shield icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h5 class="dz-title m-b10">Secure Payment</h5>
                            <p>Encrypted, trusted payments with multiple secure options—no card details stored.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="icon-bx-wraper style-1 m-b30 text-center">
                        <div class="icon-bx-sm m-b10">
                            <i class="flaticon-like icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h5 class="dz-title m-b10">Best Quality</h5>
                            <p>Quality‑checked new and pre‑owned books with accurate descriptions—for a great read every
                                time.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="icon-bx-wraper style-1 m-b30 text-center">
                        <div class="icon-bx-sm m-b10">
                            <i class="flaticon-star icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h5 class="dz-title m-b10">Return Guarantee</h5>
                            <p>Easy, hassle‑free returns within the eligible window—your satisfaction guaranteed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- icon-box1 End-->

    <!-- Book Sale -->
    @php
        $discountCount = $discountedProducts->count();
    @endphp
    @if ($discountCount > 0)
        <section class="content-inner-1">
            <div class="container">

                <div class="section-head book-align">
                    <h2 class="title mb-0">Books on Sale</h2>

                    @if ($discountCount > 4)
                        <div class="pagination-align style-1">
                            <div class="swiper-button-prev"><i class="fa-solid fa-angle-left"></i></div>
                            <div class="swiper-pagination-two"></div>
                            <div class="swiper-button-next"><i class="fa-solid fa-angle-right"></i></div>
                        </div>
                    @endif
                </div>

                @if ($discountCount > 4)
                    <div class="swiper-container books-wrapper-3 swiper-four">
                        <div class="swiper-wrapper">
                        @else
                            <div class="row g-4">
                @endif

                @foreach ($discountedProducts as $item)
                    @php
                        $product = $item->product;
                        if (!$product) {
                            continue;
                        }

                        $attributeId = $item->id;

                        $priceDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute($attributeId);

                        $ratings = $item->ratings;
                        $avgRating = $ratings->count() ? round($ratings->avg('rating'), 1) : 0;
                        $fullStars = floor($avgRating);
                        $halfStar = $avgRating - $fullStars >= 0.5;
                    @endphp

                    @if ($discountCount > 4)
                        <div class="swiper-slide">
                        @else
                            <div class="col-lg-3 col-md-4 col-sm-6">
                    @endif

                    <div class="books-card style-3 wow fadeInUp">

                        {{-- IMAGE --}}
                        <div class="dz-media position-relative">
                            <a href="{{ url('product/' . $attributeId) }}">
                                <img src="{{ asset('front/images/product_images/small/' . ($product->product_image ?? 'no-image.png')) }}"
                                    style="height:256px;width:100%;object-fit:cover;"
                                    onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'"
                                    alt="{{ $product->product_name }}">
                            </a>

                            {{-- DISCOUNT BADGE --}}
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                -{{ round($item->product_discount) }}%
                            </span>
                        </div>

                        <div class="dz-content">

                            <h5 class="title book-title-truncate">
                                <a href="{{ url('product/' . $attributeId) }}" title="{{ $product->product_name }}">
                                    {{ $product->product_name }}
                                </a>
                            </h5>

                            <ul class="dz-tags">
                                <li>
                                    Authors:
                                    {{ $product->authors->pluck('name')->first() ?? 'N/A' }}
                                    @if ($product->authors->count() > 1)
                                        ...
                                    @endif
                                </li>
                            </ul>

                            <div class="d-flex align-items-center mb-2">
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
                                <small class="text-muted">
                                    {{ $avgRating > 0 ? $avgRating . '/5' : 'No ratings' }}
                                    @if ($ratings->count())
                                        ({{ $ratings->count() }})
                                    @endif
                                </small>
                            </div>

                            <div class="book-footer">
                                <div class="rate">
                                    <span style="text-transform: capitalize;">
                                        {{ $product->condition }}
                                    </span>
                                </div>

                                <div class="price">
                                    <span class="price-num">
                                        ₹{{ number_format($priceDetails['final_price'], 2) }}
                                    </span>
                                    <del>
                                        ₹{{ number_format($priceDetails['product_price'], 2) }}
                                    </del>
                                </div>
                            </div>

                            {{-- ADD TO CART --}}
                            <form action="{{ url('cart/add') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="product_attribute_id" value="{{ $attributeId }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="flaticon-shopping-cart-1"></i> Add to cart
                                </button>
                            </form>

                        </div>
                    </div>
            </div>
    @endforeach
    @if ($discountCount > 4)
        </div>
        </div>
    @else
        </div>
    @endif
    </div>
    </section>
    @endif
    <!-- End Book Sale -->

    <!-- Feature Product -->
    @php
        $featuredCount = $featuredProducts->count();
    @endphp
    @if ($featuredCount > 0)
        <section class="content-inner-1 bg-grey reccomend">
            <div class="container">

                {{-- SECTION HEAD --}}
                <div class="section-head book-align">
                    <h2 class="title mb-0">Featured Product</h2>

                    {{-- SLIDER CONTROLS --}}
                    @if ($featuredCount > 4)
                        <div class="pagination-align style-1">
                            <div class="swiper-button-prev"><i class="fa-solid fa-angle-left"></i></div>
                            <div class="swiper-pagination-three"></div>
                            <div class="swiper-button-next"><i class="fa-solid fa-angle-right"></i></div>
                        </div>
                    @endif
                </div>

                {{-- WRAPPER --}}
                @if ($featuredCount > 4)
                    <div class="swiper-container books-wrapper-2 swiper-three">
                        <div class="swiper-wrapper">
                        @else
                            <div class="row g-4">
                @endif

                {{-- LOOP --}}
                @foreach ($featuredProducts as $attribute)
                    @php
                        $product = $attribute->product;
                        if (!$product) {
                            continue;
                        }

                        $attributeId = $attribute->id;

                        $priceDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute($attributeId);

                        $ratings = $attribute->ratings;
                        $avgRating = $ratings->count() ? round($ratings->avg('rating'), 1) : 0;
                        $fullStars = floor($avgRating);
                        $halfStar = $avgRating - $fullStars >= 0.5;
                    @endphp

                    @if ($featuredCount > 4)
                        <div class="swiper-slide">
                        @else
                            <div class="col-lg-3 col-md-4 col-sm-6">
                    @endif

                    <div class="books-card style-3 wow fadeInUp">

                        {{-- IMAGE --}}
                        <div class="dz-media position-relative">
                            <a href="{{ url('product/' . $attributeId) }}">
                                <img src="{{ asset('front/images/product_images/small/' . ($product->product_image ?? 'no-image.png')) }}"
                                    style="height:256px;width:100%;object-fit:cover;"
                                    onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'"
                                    alt="{{ $product->product_name }}">
                            </a>

                            @if ($attribute->product_discount > 0)
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                    -{{ round($attribute->product_discount) }}%
                                </span>
                            @endif
                        </div>

                        <div class="dz-content">

                            {{-- TITLE --}}
                            <h5 class="title book-title-truncate">
                                <a href="{{ url('product/' . $attributeId) }}">
                                    {{ $product->product_name }}
                                </a>
                            </h5>

                            {{-- AUTHORS --}}
                            <ul class="dz-tags">
                                <li>
                                    Authors:
                                    {{ $product->authors->pluck('name')->first() ?? 'N/A' }}
                                    @if ($product->authors->count() > 1)
                                        ...
                                    @endif
                                </li>
                            </ul>

                            {{-- ⭐ STAR RATING --}}
                            <div class="d-flex align-items-center mb-2">
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
                                <small class="text-muted">
                                    {{ $avgRating > 0 ? $avgRating . '/5' : 'No ratings' }}
                                    @if ($ratings->count())
                                        ({{ $ratings->count() }})
                                    @endif
                                </small>
                            </div>

                            {{-- FOOTER --}}
                            <div class="book-footer">
                                <div class="rate">
                                    <span style="text-transform: capitalize;">
                                        {{ $product->condition }}
                                    </span>
                                </div>

                                <div class="price">
                                    <span class="price-num">
                                        ₹{{ number_format($priceDetails['final_price'], 2) }}
                                    </span>

                                    @if ($priceDetails['discount'] > 0)
                                        <del>₹{{ number_format($priceDetails['product_price'], 2) }}</del>
                                    @endif
                                </div>
                            </div>

                            {{-- ADD TO CART --}}
                            <form action="{{ url('cart/add') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="product_attribute_id" value="{{ $attributeId }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="flaticon-shopping-cart-1"></i> Add to cart
                                </button>
                            </form>

                        </div>
                    </div>
            </div>
    @endforeach
    @if ($featuredCount > 4)
        </div>
        </div>
    @else
        </div>
    @endif
    </div>
    </section>
    @endif
    <!-- Feature Product End -->

    <!-- Special Offer-->
    {{-- <section class="content-inner-2">
        <div class="container">
            <div class="section-head book-align">
                <h2 class="title mb-0">Special Offers</h2>
                <div class="pagination-align style-1">
                    <div class="book-button-prev swiper-button-prev"><i class="fa-solid fa-angle-left"></i></div>
                    <div class="book-button-next swiper-button-next"><i class="fa-solid fa-angle-right"></i></div>
                </div>
            </div>
            <div class="swiper-container book-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="dz-card style-2 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="dz-media">
                                <a href="books-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/blog5.jpg') }}" alt="/"></a>
                            </div>
                            <div class="dz-info">
                                <h4 class="dz-title"><a href="books-detail.html">SECONDS [Part I]</a></h4>
                                <div class="dz-meta">
                                    <ul class="dz-tags">
                                        <li><a href="books-detail.html">BIOGRAPHY</a></li>
                                        <li><a href="books-detail.html">THRILLER</a></li>
                                        <li><a href="books-detail.html">HORROR</a></li>
                                    </ul>
                                </div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                    ut labore.</p>
                                <div class="bookcard-footer">
                                    <a href="shop-cart.html" class="btn btn-primary m-t15 btnhover2"><i
                                            class="flaticon-shopping-cart-1 m-r10"></i> Add to cart</a>
                                    <div class="price-details">
                                        $18,78 <del>$25</del>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dz-card style-2 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="dz-media">
                                <a href="books-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/blog6.jpg') }}" alt="/"></a>
                            </div>
                            <div class="dz-info">
                                <h4 class="dz-title"><a href="books-detail.html">Terrible Madness</a></h4>
                                <div class="dz-tags">
                                    <ul>
                                        <li><a href="books-detail.html">BIOGRAPHY</a></li>
                                        <li><a href="books-detail.html">THRILLER</a></li>
                                        <li><a href="books-detail.html">HORROR</a></li>
                                    </ul>
                                </div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                    ut labore.</p>
                                <div class="bookcard-footer">
                                    <a href="shop-cart.html" class="btn btn-primary m-t15 btnhover2"><i
                                            class="flaticon-shopping-cart-1 m-r10"></i> Add to cart</a>
                                    <div class="price-details">
                                        $18,78 <del>$25</del>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dz-card style-2 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="dz-media">
                                <a href="books-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/blog7.jpg') }}" alt="/"></a>
                            </div>
                            <div class="dz-info">
                                <h4 class="dz-title"><a href="books-detail.html">REWORK</a></h4>
                                <div class="dz-tags">
                                    <ul>
                                        <li><a href="books-detail.html">BIOGRAPHY</a></li>
                                        <li><a href="books-detail.html">THRILLER</a></li>
                                        <li><a href="books-detail.html">HORROR</a></li>
                                    </ul>
                                </div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                    ut labore.</p>
                                <div class="bookcard-footer">
                                    <a href="shop-cart.html" class="btn btn-primary m-t15 btnhover2"><i
                                            class="flaticon-shopping-cart-1 m-r10"></i> Add to cart</a>
                                    <div class="price-details">
                                        $18,78 <del>$25</del>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dz-card style-2 wow fadeInUp" data-wow-delay="0.4s">
                            <div class="dz-media">
                                <a href="books-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/blog5.jpg') }}" alt="/"></a>
                            </div>
                            <div class="dz-info">
                                <h4 class="dz-title"><a href="books-detail.html">SECONDS [Part I]</a></h4>
                                <div class="dz-tags">
                                    <ul>
                                        <li><a href="books-detail.html">BIOGRAPHY</a></li>
                                        <li><a href="books-detail.html">THRILLER</a></li>
                                        <li><a href="books-detail.html">HORROR</a></li>
                                    </ul>
                                </div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                    ut labore.</p>
                                <div class="bookcard-footer">
                                    <a href="shop-cart.html" class="btn btn-primary m-t15 btnhover2"><i
                                            class="flaticon-shopping-cart-1 m-r10"></i> Add to cart</a>
                                    <div class="price-details">
                                        $18,78 <del>$25</del>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dz-card style-2 wow fadeInUp" data-wow-delay="0.5s">
                            <div class="dz-media">
                                <a href="books-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/blog6.jpg') }}" alt="/"></a>
                            </div>
                            <div class="dz-info">
                                <h4 class="dz-title"><a href="books-detail.html">Terrible Madness</a></h4>
                                <div class="dz-tags">
                                    <ul>
                                        <li><a href="books-detail.html">BIOGRAPHY</a></li>
                                        <li><a href="books-detail.html">THRILLER</a></li>
                                        <li><a href="books-detail.html">HORROR</a></li>
                                    </ul>
                                </div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                    ut labore.</p>
                                <div class="bookcard-footer">
                                    <a href="shop-cart.html" class="btn btn-primary m-t15 btnhover2"><i
                                            class="flaticon-shopping-cart-1 m-r10"></i> Add to cart</a>
                                    <div class="price-details">
                                        $18,78 <del>$25</del>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dz-card style-2 wow fadeInUp" data-wow-delay="0.6s">
                            <div class="dz-media">
                                <a href="books-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/blog7.jpg') }}" alt="/"></a>
                            </div>
                            <div class="dz-info">
                                <h4 class="dz-title"><a href="books-detail.html">REWORK</a></h4>
                                <div class="dz-tags">
                                    <ul>
                                        <li><a href="books-detail.html">BIOGRAPHY</a></li>
                                        <li><a href="books-detail.html">THRILLER</a></li>
                                        <li><a href="books-detail.html">HORROR</a></li>
                                    </ul>
                                </div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                    ut labore.</p>
                                <div class="bookcard-footer">
                                    <a href="shop-cart.html" class="btn btn-primary m-t15 btnhover2"><i
                                            class="flaticon-shopping-cart-1 m-r10"></i> Add to cart</a>
                                    <div class="price-details">
                                        $18,78 <del>$25</del>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Special Offer End -->

    <!-- Testimonial -->
    {{-- <section class="content-inner-2 testimonial-wrapper">
        <div class="container">
            <div class="testimonial">
                <div class="section-head book-align">
                    <div>
                        <h2 class="title mb-0">Testimonials</h2>
                        <p class="m-b0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore et dolore magna aliqua</p>
                    </div>
                    <div class="pagination-align style-1">
                        <div class="testimonial-button-prev swiper-button-prev"><i class="fa-solid fa-angle-left"></i>
                        </div>
                        <div class="testimonial-button-next swiper-button-next"><i class="fa-solid fa-angle-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-container testimonial-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="testimonial-1 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="testimonial-info">
                            <ul class="dz-rating">
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                            </ul>
                            <div class="testimonial-text">
                                <p>Very impresive store. Your book made studying for the ABC certification exams a breeze.
                                    Thank you very much</p>
                            </div>
                            <div class="testimonial-detail">
                                <div class="testimonial-pic">
                                    <img src="{{ asset('front/newtheme/images/testimonial/testimonial1.jpg') }}"
                                        alt="">
                                </div>
                                <div class="info-right">
                                    <h6 class="testimonial-name">Jason Huang</h6>
                                    <span class="testimonial-position">Book Lovers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-1 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="testimonial-info">
                            <ul class="dz-rating">
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-muted"></i></li>
                            </ul>
                            <div class="testimonial-text">
                                <p>Very impresive store. Your book made studying for the ABC certification exams a breeze.
                                    Thank you very much</p>
                            </div>
                            <div class="testimonial-detail">
                                <div class="testimonial-pic radius">
                                    <img src="{{ asset('front/newtheme/images/testimonial/testimonial2.jpg') }}"
                                        alt="">
                                </div>
                                <div>
                                    <h6 class="testimonial-name">Miranda Lee</h6>
                                    <span class="testimonial-position">Book Lovers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-1 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="testimonial-info">
                            <ul class="dz-rating">
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-muted"></i></li>
                                <li><i class="flaticon-star text-muted"></i></li>
                            </ul>
                            <div class="testimonial-text">
                                <p>Very impresive store. Your book made studying for the ABC certification exams a breeze.
                                    Thank you very much</p>
                            </div>
                            <div class="testimonial-detail">
                                <div class="testimonial-pic radius">
                                    <img src="{{ asset('front/newtheme/images/testimonial/testimonial3.jpg') }}"
                                        alt="">
                                </div>
                                <div>
                                    <h6 class="testimonial-name">Steve Henry</h6>
                                    <span class="testimonial-position">Book Lovers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-1 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="testimonial-info">
                            <ul class="dz-rating">
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-muted"></i></li>
                            </ul>
                            <div class="testimonial-text">
                                <p>Thank you for filling a niche at an affordable price. Your book was just what I was
                                    looking for. Thanks again</p>
                            </div>
                            <div class="testimonial-detail">
                                <div class="testimonial-pic radius">
                                    <img src="{{ asset('front/newtheme/images/testimonial/testimonial4.jpg') }}"
                                        alt="">
                                </div>
                                <div>
                                    <h6 class="testimonial-name">Angela Moss</h6>
                                    <span class="testimonial-position">Book Lovers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-1 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="testimonial-info">
                            <ul class="dz-rating">
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-muted"></i></li>
                            </ul>
                            <div class="testimonial-text">
                                <p>Very impresive store. Your book made studying for the ABC certification exams a breeze.
                                    Thank you very much</p>
                            </div>
                            <div class="testimonial-detail">
                                <div class="testimonial-pic radius">
                                    <img src="{{ asset('front/newtheme/images/testimonial/testimonial2.jpg') }}"
                                        alt="">
                                </div>
                                <div>
                                    <h6 class="testimonial-name">Miranda Lee</h6>
                                    <span class="testimonial-position">Book Lovers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-1 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="testimonial-info">
                            <ul class="dz-rating">
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-yellow"></i></li>
                                <li><i class="flaticon-star text-muted"></i></li>
                                <li><i class="flaticon-star text-muted"></i></li>
                            </ul>
                            <div class="testimonial-text">
                                <p>Very impresive store. Your book made studying for the ABC certification exams a breeze.
                                    Thank you very much</p>
                            </div>
                            <div class="testimonial-detail">
                                <div class="testimonial-pic">
                                    <img src="{{ asset('front/newtheme/images/testimonial/testimonial1.jpg') }}"
                                        alt="">
                                </div>
                                <div class="info-right">
                                    <h6 class="testimonial-name">Jason Huang</h6>
                                    <span class="testimonial-position">Book Lovers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Testimonial End -->

    <!-- Latest News -->
    {{-- <section class="content-inner-2">
        <div class="container">
            <div class="section-head text-center">
                <h2 class="title">Latest News</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua</p>
            </div>
            <div class="swiper-container blog-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="dz-blog style-1 bg-white m-b30 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="dz-media">
                                <a href="blog-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/grid/blog4.jpg') }}"
                                        alt="/"></a>
                            </div>
                            <div class="dz-info p-3">
                                <h6 class="dz-title">
                                    <a href="blog-detail.html">Benefits of reading: Smart, Diligent, Happy, Intelligent</a>
                                </h6>
                                <p class="m-b0">Lorem ipsum dolor sit amet, consectetur adipiscing do eiusmod tempor</p>
                                <div class="dz-meta meta-bottom mt-3 pt-3">
                                    <ul class="">
                                        <li class="post-date"><i class="far fa-calendar fa-fw m-r10"></i>24 March, 2022
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dz-blog style-1 bg-white m-b30 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="dz-media">
                                <a href="blog-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/grid/blog3.jpg') }}"
                                        alt="/"></a>
                            </div>
                            <div class="dz-info p-3">
                                <h6 class="dz-title">
                                    <a href="blog-detail.html">10 Things you must know to improve your reading skills</a>
                                </h6>
                                <p class="m-b0">Lorem ipsum dolor sit amet, consectetur adipiscing do eiusmod tempor</p>
                                <div class="dz-meta meta-bottom mt-3 pt-3">
                                    <ul class="">
                                        <li class="post-date"><i class="far fa-calendar fa-fw m-r10"></i>18 July, 2022
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dz-blog style-1 bg-white m-b30 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="dz-media">
                                <a href="blog-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/grid/blog2.jpg') }}"
                                        alt="/"></a>
                            </div>
                            <div class="dz-info p-3">
                                <h6 class="dz-title">
                                    <a href="blog-detail.html">Benefits of reading: Smart, Diligent, Happy, Intelligent</a>
                                </h6>
                                <p class="m-b0">Lorem ipsum dolor sit amet, consectetur adipiscing do eiusmod tempor</p>
                                <div class="dz-meta meta-bottom mt-3 pt-3">
                                    <ul class="">
                                        <li class="post-date"><i class="far fa-calendar fa-fw m-r10"></i>7 June, 2022</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dz-blog style-1 bg-white m-b30 wow fadeInUp" data-wow-delay="0.4s">
                            <div class="dz-media">
                                <a href="blog-detail.html"><img
                                        src="{{ asset('front/newtheme/images/blog/grid/blog1.jpg') }}"
                                        alt="/"></a>
                            </div>
                            <div class="dz-info p-3">
                                <h6 class="dz-title">
                                    <a href="blog-detail.html">We Must know why reading is important for children?</a>
                                </h6>
                                <p class="m-b0">Lorem ipsum dolor sit amet, consectetur adipiscing do eiusmod tempor</p>
                                <div class="dz-meta meta-bottom mt-3 pt-3">
                                    <ul class="">
                                        <li class="post-date"><i class="far fa-calendar fa-fw m-r10"></i>30 May, 2022</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Latest News End -->

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

    <!-- Sales Executive CTA -->
    <section class="py-5" style="background-image: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);">
        <div class="container">
            <div class="row align-items-center text-white g-4">
                <div class="col-xl-7 col-lg-7">
                    <h2 class="display-6 text-white fw-semibold mb-3">Become a BookHub Sales Executive</h2>
                    <p class="lead mb-4">Help schools and institutions discover the right books while earning attractive
                        commissions, marketing support, and exclusive incentives from BookHub.</p>
                    <div class="d-flex flex-column flex-md-row gap-3">
                        <div class="d-flex align-items-center">
                            <span class="bg-white bg-opacity-10 rounded-circle p-3 me-3"><i
                                    class="fa-solid fa-chart-line fs-4"></i></span>
                            <div>
                                <h5 class="mb-1 text-white">Grow your network</h5>
                                <small class="text-white-50">Connect with schools, colleges, and book lovers in your
                                    city.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="bg-white bg-opacity-10 rounded-circle p-3 me-3"><i
                                    class="fa-solid fa-coins fs-4"></i></span>
                            <div>
                                <h5 class="mb-1 text-white">Earn more, faster</h5>
                                <small class="text-white-50">Enjoy competitive payouts and performance bonuses.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4">
                            <h4 class="fw-semibold mb-3 text-primary">Register today</h4>
                            <p class="mb-4 text-muted">Fill out a short application and our onboarding team will connect
                                with you within 48 hours.</p>
                            <a href="{{ url('/sales') }}" class="btn btn-primary btnhover w-100">
                                Join as Sales Executive
                                <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                            <div class="d-flex align-items-center mt-4">
                                <span class="text-primary me-3"><i class="fa-solid fa-circle-check"></i></span>
                                <small class="text-muted">Trusted by
                                    {{ number_format(App\Models\SalesExecutive::count()) }}+ executives across
                                    India.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Sales Executive CTA End -->

    {{-- Advanced Vendor CTA Section for Home Page --}}
    <section class="py-5 position-relative overflow-hidden" style="background: var(--primary);">
        <div class="container position-relative" style="z-index:2;">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7 text-white">
                    <span class="badge bg-light text-primary mb-3 px-3 py-2 rounded-pill">
                        Vendor Opportunity • Limited Effort, High Reach
                    </span>
                    <h2 class="fw-semibold mb-3" style="color: #181818; font-weight: bold;">
                        Turn Your Book Catalog into a New Revenue Channel with BookHub
                    </h2>
                    <p class="mb-3">
                        Upload your books once, and let BookHub handle the marketing, promotion, and student reach.
                        You pay only a <b>5% promotion fee</b> on successful orders—no big ad budgets, no platform
                        headaches.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2"
                                style="width:32px;height:32px;">
                                <i class="fa fa-book"></i>
                            </div>
                            <span>Upload books & editions easily</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2"
                                style="width:32px;height:32px;">
                                <i class="fa fa-bullhorn"></i>
                            </div>
                            <span>BookHub markets for you</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2"
                                style="width:32px;height:32px;">
                                <i class="fa fa-inr"></i>
                            </div>
                            <span>Only 5% promotion fee</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('vendor.register') }}" class="btn btn-light btn-sm">
                            Register as Vendor
                        </a>
                        <a href="{{ url('/vendors') }}" class="btn btn-outline-light btn-sm">
                            View Vendor Plans
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 text-lg-end text-center text-white">
                    <div
                        class="bg-white bg-opacity-10 border border-light border-opacity-25 rounded-3 p-4 d-inline-block shadow-lg">
                        <h5 class="fw-semibold mb-2" style="color: #181818; font-weight: bold;">Free Plan & Pro Plan</h5>
                        <p class="mb-2">
                            Start free with up to 100 books per month, or switch to Pro for unlimited uploads and coupons
                            at just ₹499/month.
                        </p>
                        <ul class="list-unstyled small mb-3 text-start">
                            <li class="mb-1">
                                <i class="fa fa-check text-success me-1"></i> No separate website required
                            </li>
                            <li class="mb-1">
                                <i class="fa fa-check text-success me-1"></i> We handle promotion & traffic
                            </li>
                            <li class="mb-1">
                                <i class="fa fa-check text-success me-1"></i> Transparent vendor dashboard
                            </li>
                        </ul>
                        <a href="{{ url('/contact') }}" class="btn btn-outline-light btn-sm">
                            Talk to Our Team
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- subtle background shapes --}}
        <div class="position-absolute rounded-circle bg-white"
            style="opacity:0.08;width:220px;height:220px;top:-60px;right:-40px;"></div>
        <div class="position-absolute rounded-circle bg-white"
            style="opacity:0.04;width:280px;height:280px;bottom:-80px;left:-80px;"></div>
    </section>

    <style>
        .book-title-truncate {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* show only 2 lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4em;
            min-height: 2.8em;
            /* equal card height */
        }
    </style>

    {{-- Js Section  --}}

    <script></script>

@endsection
