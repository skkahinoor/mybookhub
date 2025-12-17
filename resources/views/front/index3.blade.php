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






    <!-- Recommend Section Start -->
    <section class="content-inner-1 bg-grey reccomend py-5">
        <div class="container">
            <div class="section-head text-center mb-4">
                <h2 class="title">Recommended For You</h2>
                <p>Discover titles picked just for you — find your next great read from our curated list!</p>
            </div>
            <div class="row g-4">
                @foreach ($sliderProducts as $sliderProduct)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                        <div class="card flex-fill shadow-sm border-0 book-grid-card" style="border-radius:20px;">
                            <a href="{{ url('product/' . $sliderProduct['id']) }}" class="d-block">
                                <img src="{{ asset('front/images/product_images/small/' . $sliderProduct['product_image']) }}"
                                    alt="book" class="card-img-top"
                                    style="height:220px; object-fit:cover; border-radius:20px 20px 0 0;">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="mb-1 card-title" style="font-size:1.05rem;">
                                    <a href="{{ url('product/' . $sliderProduct['id']) }}"
                                        class="text-dark text-decoration-none">
                                        {{ $sliderProduct['product_name'] }}
                                    </a>
                                </h5>
                                <span
                                    class="price mb-2 text-primary fw-bold fs-6">₹{{ \App\Models\Product::getDiscountPrice($sliderProduct['id']) }}</span>
                                <form action="{{ url('cart/add') }}" method="POST" class="mt-auto">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $sliderProduct['id'] }}">
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
    <section class="content-inner-1">
        <div class="container">
            <div class="section-head book-align">
                <h2 class="title mb-0">Books on Sale</h2>
                <div class="pagination-align style-1">
                    <div class="swiper-button-prev"><i class="fa-solid fa-angle-left"></i></div>
                    <div class="swiper-pagination-two"></div>
                    <div class="swiper-button-next"><i class="fa-solid fa-angle-right"></i></div>
                </div>
            </div>
            <div class="swiper-container books-wrapper-3 swiper-four">
                <div class="swiper-wrapper">

                    @forelse($newProducts as $product)
                        <div class="swiper-slide">
                            @php
                                $discountedPrice = \App\Models\Product::getDiscountPrice($product->id);
                                $hasDiscount = $discountedPrice > 0;
                            @endphp
                            <div class="books-card style-3 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="dz-media">
                                    <a href="{{ url('product/' . $product['id']) }}">
                                        <img src="{{ asset('front/images/product_images/small/' . $product['product_image']) }}"
                                            style="height: 256px;  width: 357px !important; object-fit: cover;"
                                            alt="book">
                                    </a>
                                </div>
                                <div class="dz-content">
                                    <h5 class="title"><a
                                            href="{{ url('product/' . $product['id']) }}">{{ $product['product_name'] }}</a>
                                    </h5>
                                    <ul class="dz-tags">
                                        @php
                                            $allAuthorNames = $product->authors->pluck('name')->join(', ');
                                        @endphp
                                        <li title="{{ $allAuthorNames }}">
                                            Authors:
                                            @if ($product->authors->isNotEmpty())
                                                {{ $product->authors->first()->name }}
                                                @if ($product->authors->count() > 1)
                                                    ...
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </li>

                                    </ul>
                                    <div>

                                        {{-- location --}}
                                        @php
                                            $userLat = session('user_latitude');
                                            $userLng = session('user_longitude');
                                            $productLatLng = $product->location
                                                ? explode(',', $product->location)
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
                                            <svg width="24px" height="24px" viewBox="0 0 1024 1024" class="icon"
                                                version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                    stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <path
                                                        d="M309.2 584.776h105.5l-49 153.2H225.8c-7.3 0-13.3-6-13.3-13.3 0-2.6 0.8-5.1 2.2-7.3l83.4-126.7c2.5-3.6 6.7-5.9 11.1-5.9z"
                                                        fill="#FFFFFF"></path>
                                                    <path
                                                        d="M404.5 791.276H225.8c-36.7 0-66.5-29.8-66.5-66.5 0-13 3.8-25.7 11-36.6l83.4-126.7c12.3-18.7 33.1-29.9 55.5-29.9h178.4l-83.1 259.7z m-95.3-206.5c-4.5 0-8.6 2.2-11.1 6l-83.4 126.7c-1.4 2.2-2.2 4.7-2.2 7.3 0 7.3 6 13.3 13.3 13.3h139.9l49-153.2H309.2z"
                                                        fill="#333333"></path>
                                                    <path d="M454.6 584.776h109.6l25.3 153.3H429.3z" fill="#FFFFFF">
                                                    </path>
                                                    <path
                                                        d="M652.2 791.276H366.6l42.8-259.6h200l42.8 259.6z m-222.9-53.2h160.2l-25.3-153.3H454.6l-25.3 153.3z"
                                                        fill="#333333"></path>
                                                    <path
                                                        d="M618.6 584.776h105.5c4.5 0 8.6 2.2 11.1 6l83.5 126.7c4 6.1 2.3 14.4-3.8 18.4-2.2 1.4-4.7 2.2-7.3 2.2H667.7l-49.1-153.3z"
                                                        fill="#FFFFFF"></path>
                                                    <path
                                                        d="M807.6 791.276H628.9l-83.1-259.7h178.4c22.4 0 43.2 11.2 55.5 29.9l83.4 126.7c9.8 14.8 13.2 32.6 9.6 50s-13.7 32.3-28.6 42.1c-10.8 7.2-23.5 11-36.5 11z m-139.9-53.2h139.9c2.6 0 5.1-0.8 7.3-2.2 4-2.6 5.3-6.4 5.7-8.4 0.4-2 0.7-6-1.9-10l-83.4-126.6c-2.5-3.8-6.6-6-11.1-6H618.6l49.1 153.2z"
                                                        fill="#333333"></path>
                                                    <path
                                                        d="M534.1 639.7C652.5 537.4 711.7 445.8 711.7 365c0-127-102.7-212.1-195-212.1s-195 85.1-195 212.1c0 80.8 59.2 172.3 177.7 274.7 9.9 8.6 24.7 8.6 34.7 0z"
                                                        fill="#8CAAFF"></path>
                                                    <path
                                                        d="M516.7 672.7c-12.5 0-24.9-4.3-34.8-12.9C356.2 551.2 295.1 454.7 295.1 365c0-142.8 114.6-238.7 221.6-238.7S738.3 222.2 738.3 365c0 89.7-61.1 186.2-186.9 294.8-9.8 8.6-22.3 12.9-34.7 12.9z m0-493.2c-79.7 0-168.4 76.2-168.4 185.5 0 72.3 56.7 158 168.4 254.6C628.5 523 685.1 437.3 685.1 365c0-109.3-88.7-185.5-168.4-185.5z"
                                                        fill="#333333"></path>
                                                    <path
                                                        d="M516.7 348m-97.5 0a97.5 97.5 0 1 0 195 0 97.5 97.5 0 1 0-195 0Z"
                                                        fill="#FFFFFF">
                                                    </path>
                                                    <path
                                                        d="M516.7 472.1c-68.4 0-124.1-55.7-124.1-124.1s55.7-124.1 124.1-124.1S640.8 279.5 640.8 348 585.1 472.1 516.7 472.1z m0-195.1c-39.1 0-70.9 31.8-70.9 70.9 0 39.1 31.8 70.9 70.9 70.9s70.9-31.8 70.9-70.9c0-39.1-31.8-70.9-70.9-70.9z"
                                                        fill="#333333"></path>
                                                </g>
                                            </svg> :
                                            <span>
                                                @if ($distance !== null)
                                                    {{ $distance < 1 ? round($distance * 1000) . ' m' : round($distance, 2) . ' km' }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </p>
                                    </div>
                                    <div class="book-footer">
                                        {{-- <div class="rate">
                                            <i class="flaticon-star"></i> 6.8
                                        </div> --}}
                                        <div class="rate">
                                            <div class="row">
                                                <div class="col" style="padding: 0px !important;">
                                                    <svg width="20px" height="20px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M19.8978 16H7.89778C6.96781 16 6.50282 16 6.12132 16.1022C5.08604 16.3796 4.2774 17.1883 4 18.2235"
                                                                stroke="#1C274D" stroke-width="1.5"></path>
                                                            <path d="M8 7H16" stroke="#1C274D" stroke-width="1.5"
                                                                stroke-linecap="round"></path>
                                                            <path d="M8 10.5H13" stroke="#1C274D" stroke-width="1.5"
                                                                stroke-linecap="round"></path>
                                                            <path d="M19.5 19H8" stroke="#1C274D" stroke-width="1.5"
                                                                stroke-linecap="round"></path>
                                                            <path
                                                                d="M10 22C7.17157 22 5.75736 22 4.87868 21.1213C4 20.2426 4 18.8284 4 16V8C4 5.17157 4 3.75736 4.87868 2.87868C5.75736 2 7.17157 2 10 2H14C16.8284 2 18.2426 2 19.1213 2.87868C20 3.75736 20 5.17157 20 8M14 22C16.8284 22 18.2426 22 19.1213 21.1213C20 20.2426 20 18.8284 20 16V12"
                                                                stroke="#1C274D" stroke-width="1.5"
                                                                stroke-linecap="round">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="col" style="padding: 2px !important;">
                                                    <span
                                                        style="text-transform:capitalize;">{{ $product['condition'] }}</span>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="price">
                                            <span
                                                class="price-num">₹{{ \App\Models\Product::getDiscountPrice($product['id']) }}</span>
                                            <del>₹{{ $product['product_price'] }}</del>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </section>
    <!-- Book Sale End -->

    <!-- Feature Product -->
    <section class="content-inner-1 bg-grey reccomend">
        <div class="container">
            <div class="section-head text-center">
                <div class="circle style-1"></div>
                <h2 class="title">Featured Product</h2>
                <p>Discover our handpicked favorites—top-rated, trending titles curated to help you find your next great
                    read.</p>
            </div>
        </div>
        <div class="container">
            <div class="swiper-container books-wrapper-2 swiper-three">
                <div class="swiper-wrapper">

                    @foreach ($featuredProducts as $products)
                        <div class="swiper-slide">
                            <div class="books-card style-2">
                                <div class="dz-media">
                                    <img src="{{ asset('front/images/product_images/small/' . $products['product_image']) }}"
                                        alt="banner-media" style="height: 500px; width: 1000px; object-fit: cover;">
                                </div>
                                <div class="dz-content">
                                    <h6 class="font-family: poppins text-dark" data-swiper-parallax="-10">
                                        Publisher:
                                        {{ $products->publisher->name ?? 'N/A' }}
                                    </h6>
                                    <h2 class="title text-dark mb-0" data-swiper-parallax="-20">
                                        {{ $products['product_name'] }}</h2>
                                    <ul class="dz-tags" data-swiper-parallax="-30">
                                        @php
                                            $allAuthorNames = $products->authors->pluck('name')->join(', ');
                                        @endphp
                                        <li><a class="text-dark" title="{{ $allAuthorNames }}"
                                                href="javascript:void(0);">
                                                Authors:
                                                @if ($products->authors->isNotEmpty())
                                                    {{ $products->authors->first()->name }}
                                                    @if ($products->authors->count() > 1)
                                                        ...
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </a>
                                        </li>
                                    </ul>
                                    <p class="text mb-0" data-swiper-parallax="-40">Book Condition: <span
                                            style="text-transform: capitalize;">{{ $products['condition'] }}</span>
                                    </p>
                                    <p class="text mb-0" data-swiper-parallax="-40">Description:
                                        {{ $products['description'] }}</p>
                                    <div class="price">
                                        <span
                                            class="price-num">₹{{ \App\Models\Product::getDiscountPrice($products['id']) }}</span>
                                        <del>₹{{ $products['product_price'] }}</del>
                                    </div>
                                    <div class="bookcard-footer">
                                        <a class="btn btn-success btnhover"
                                            href="{{ url('product/' . $products['id']) }}">Buy
                                            Now</a>
                                        <a class="btn border btnhover ms-4 text-dark"
                                            href="{{ url('product/' . $products['id']) }}">See
                                            Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="pagination-align style-2">
                    <div class="swiper-button-prev"><i class="fa-solid fa-angle-left"></i></div>
                    <div class="swiper-pagination-three"></div>
                    <div class="swiper-button-next"><i class="fa-solid fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </section>
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



    {{-- Js Section  --}}

    <script></script>

@endsection
