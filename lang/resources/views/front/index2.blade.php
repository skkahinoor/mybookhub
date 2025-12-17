@extends('front.layout.layout2')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap');

        /* code by sumit */




        #frontpage-slider .owl-item {
            width: 200px !important;
            margin-right: 10px;
        }

        #frontpage-slider .owl-stage {
            display: flex;
            align-items: stretch;
        }

        #frontpage-slider .slider-item {
            width: 100%;
        }


        .frontpage-slider-posts.slider-style-two #frontpage-slider .owl-item.center .slider-item .content-entry-wrap {
            bottom: 9px !important;
            width: 86%;
            left: 8%;
            padding: unset !important;
            height: 160px !important;
        }


        .frontpage-slider-posts.slider-style-two #frontpage-slider .owl-item.center .slider-item .content-entry-wrap .entry-content {
            height: 160px !important;
        }

        .frontpage-slider-posts.slider-style-two #frontpage-slider .owl-item.center .slider-item .content-entry-wrap .entry-content h3 {
            font-size: 17px;
        }

        .frontpage-slider-posts.slider-style-two #frontpage-slider .owl-item.center .slider-item .content-entry-wrap .entry-content p {
            font-size: 13px;
            line-height: 16px;
        }

        .frontpage-slider-posts.slider-style-two #frontpage-slider .slider-item {
            background-color: unset !important;
            border-radius: 20px;
            border: 3px solid rgb(75, 59, 59);
            box-shadow: 5px 5px 10px 0px #aea3a3;

        }

        .frontpage-slider-posts.slider-style-two #frontpage-slider .slider-item figure {
            margin-bottom: 0px !important;

        }

        .site-content {

            background: #f0f0f0;
        }

        .top-stories-block {
            margin-top: 100px;
            margin-bottom: 150px;
        }

        .top-stories-block .container {

            background: #e6e4e4;
            width: fit-content;
            margin: 40px auto;
            padding-inline: 30px;
            border-radius: 30px;
            box-shadow: 5px 5px 10px 0px #757373;

        }

        .top-stories-block h2 {

            color: black;

        }

        .entry-category span {
            color: rgb(119, 119, 119) !important;
            font-weight: bold !important;
        }

        .content-entry-wrap .entry-title a {
            color: black;

        }

        .content-entry-wrap .entry-title a:hover {
            color: black !important;
        }

        .frontpage-popular-posts {
            width: 100%;
            position: relative;

        }


        .frontpage-popular-posts h2 {


            color: black !important;
            font-weight: bolder;
            text-align: left;
        }

        .frontpage-popular-posts .card {
            border: 2px solid #C0C0C0;
            transition: all 0.4s ease-in-out;
            height: 460px !important;


        }

        .frontpage-popular-posts .card img {
            height: 200px !important;
        }

        .frontpage-popular-posts .card:hover {
            scale: 1.03;
            cursor: pointer;
        }

        .oswald-title {
            font-family: "Oswald", sans-serif !important;
            font-optical-sizing: auto !important;
            font-weight: 700 !important;
            font-style: normal !important;
        }


        .slider-item .slider-thumb:before {
            content: "";
            height: 100% !important;
            width: 100%;
            left: 0;
            top: 0;
            background-color: rgba(0, 0, 0, 0.4);
            position: absolute;
        }


        .author {
            line-height: 18px;
        }


        .frontpage-popular-posts .card {
            width: 300px;
            height: 400px !important;
        }







        @media only screen and (min-width:1024px) and (max-width:1200px) {
            .featured-badge-list {
                margin-top: -9px;
                scale: 0.8;
            }
        }

        @media only screen and (min-width:1024px) {
            .top-stories-block {
                width: 90vw !important;
                margin: 4px auto 100px;
            }



        }



        @media only screen and (min-width:768px) and (max-width:1023px) {
            .top-stories-block {
                width: 65vw !important;
                margin: 4px auto 100px;
            }



        }


        @media only screen and (max-width:1024px) {
            .oswald-title {
                text-align: center !important;

            }
        }



        @media only screen and (max-width:768px) {

            .frontpage-popular-posts .card {

                height: 460px !important;

            }


            .top-stories-block {
                margin: 0px auto 100px;
                width: 90vw;
            }


            .frontpage-popular-posts .card p.author {
                width: 100%;
                max-height: 50px;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }


            .frontpage-popular-posts .card {
                height: 350px !important;
            }


        }



        @media (max-width: 767px) {
            #frontpage-slider .owl-item {
                width: 100% !important;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            #frontpage-slider .owl-item {
                width: 33.3333% !important;
            }
        }


        #scroll-loader {
            display: none;
        }
    </style>


    <div class="site-content">
        <!-- Frontpage Slider -->
        <div class="frontpage-slider-posts slider-style-two mb-5">
            <div id="frontpage-slider" class="owl-carousel frontpage-slider-two carousel-nav-align-center">
                @foreach ($sliderProducts as $product)
                    <div class="slider-item text-center">
                        <figure class="slider-thumb glass-effect">
                            @if (!empty($product['product_image']))
                                <img src="{{ asset('front/images/product_images/small/' . $product['product_image']) }}"
                                    alt="{{ $product['product_name'] }}" class="img-fluid">
                            @endif
                        </figure>
                        <div class="content-entry-wrap">
                            <div class="entry-content card p-2 glass-effect">
                                <h3 class="entry-title">
                                    <a href="{{ url('product/' . $product->id) }}" style="text-decoration: none;">
                                        {{ $product->product_name }} ({{ $product->edition->edition ?? 'not set' }} Edition)
                                    </a>
                                </h3>
                                <p>Publisher: {{ $product->publisher->name ?? 'N/A' }}</p>
                                <p>Authors:
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

                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Stories -->
        <section class="top-stories-block style-two ">
            <div class="container" style="padding-block:40px;">
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <h2 class="section-title oswald-title"><b>Recommended for You</b></h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-12">
                        <article class="post hentry post-list post-list-small">
                            <!--./ =============== entry-thumb =============== -->
                            <div class="entry-thumb">
                                <figure class="thumb-wrap">
                                    <a>
                                        <img src="{!! asset('assets/images/s1.jpg') !!}">
                                    </a>
                                    <div class="featured-badge-list">
                                        <a class="trending" href="#">
                                            <span class="fa fa-bolt"></span>
                                        </a>
                                    </div>
                                </figure>
                            </div>
                            <!--./ =============== entry-thumb =============== -->
                            <div class="content-entry-wrap">
                                <div class="entry-content">
                                    <h3 class="entry-title">
                                        <a>Quick delivery</a>
                                    </h3>
                                    <!--./ entry-title -->
                                </div>
                                <!--./ entry-content -->
                                <div class="entry-meta-content">
                                    <div class="entry-category">
                                        <span class="text-light">we swing into action, ensuring that your package reaches
                                            your doorstep in the
                                            shortest possible time.</span>
                                    </div>
                                </div>
                            </div>
                            <!--./ =============== entry-thumb =============== -->
                        </article>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <article class="post hentry post-list post-list-small">
                            <div class="entry-thumb">
                                <figure class="thumb-wrap">
                                    <a>
                                        <img src="{!! asset('assets/images/s2.jpg') !!}" alt="post">
                                    </a>
                                    <div class="featured-badge-list">
                                        <a class="trending" href="#">
                                            <span class="fas fa-money-check"></span>
                                        </a>
                                    </div>
                                </figure>
                            </div>
                            <!--./ ================= entry-thumb ================= -->
                            <div class="content-entry-wrap">
                                <div class="entry-content">
                                    <h3 class="entry-title">
                                        <a>Secure Payment</a>
                                    </h3>
                                    <!--./ ================= entry-title ========= -->
                                </div>
                                <!--./ ============== entry-content ============== -->
                                <div class="entry-meta-content">
                                    <div class="entry-category">
                                        <span class="text-light">This encryption process safeguards your information from
                                            unauthorized access,
                                            ensuring that your data.</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <article class="post hentry post-list post-list-small">
                            <div class="entry-thumb">
                                <figure class="thumb-wrap">
                                    <a>
                                        <img src="{!! asset('assets/images/s4.jpg') !!}">
                                    </a>
                                    <div class="featured-badge-list">
                                        <a class="trending" href="#">
                                            <span class="fas fa-truck-loading"></span>
                                        </a>
                                    </div>
                                    <!--./ ====== featured-badge-list ============== -->
                                </figure>
                            </div>
                            <div class="content-entry-wrap">
                                <div class="entry-content">
                                    <h3 class="entry-title">
                                        <a>Best Quality</a>
                                    </h3><!--./ entry-title -->
                                </div>
                                <!--./ entry-content -->
                                <div class="entry-meta-content">
                                    <div class="entry-category">
                                        <span class="text-light">We have relationships with trusted suppliers who share our
                                            commitment to
                                            excellence.</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest Books -->
        <section class="frontpage-popular-posts pb-5">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <h2 class="section-title oswald-title"><b>Latest Book</b></h2>
                    </div>
                </div>

                <div id="new-products-container" class="row g-4 flex-wrap">
                    {{-- @forelse($newProducts as $product)
                        <div class="col-md-6 col-lg-3 d-flex justify-content-center">
                            <div class="card h-100 border-0 shadow-sm product-card">
                                <div class="position-relative">
                                    @if (!empty($product['product_image']))
                                        <a href="{{ url('product/' . $product['id']) }}">
                                            <img src="{{ asset('front/images/product_images/small/' . $product['product_image']) }}"
                                                class="card-img-top" alt="product_name"
                                                style="height: 200px; object-fit: cover;">
                                        </a>
                                    @endif
                                    @php
                                        $discountedPrice = \App\Models\Product::getDiscountPrice($product->id);
                                        $hasDiscount = $discountedPrice > 0;
                                    @endphp
                                    @if ($hasDiscount)
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-danger">
                                                -{{ $product['product_discount'] }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-1">
                                        <a href="{{ url('product/' . $product['id']) }}"
                                            class="text-decoration-none text-dark">
                                            {{ $product['product_name'] }}
                                        </a>
                                    </h5>

                                    <p class="text-muted small mb-2">Publisher: {{ $product->publisher->name ?? 'N/A' }}
                                    </p>
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
                                            <span
                                                class="h5 mb-0 ms-2">₹{{ \App\Models\Product::getDiscountPrice($product['id']) }}</span>
                                        </div>
                                        <span class="badge" style="background-color: #6c5dd4;">
                                            {{ $product['condition'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No products found in this category. Try adjusting your filters.
                            </div>
                        </div>
                    @endforelse --}}

                    @include('front.partials.new_products')
                </div>

                <div id="scroll-loader" class="text-center mt-4">
                    <p class="text-muted">Loading more books...</p>
                </div>

                <!-- Pagination -->
                {{-- <div class="d-flex justify-content-center mt-4">
                    {{ $newProducts->links() }}
                </div> --}}
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#load-more-btn').on('click', function() {
                let button = $(this);
                let page = button.data('page');

                $.ajax({
                    url: "{{ url('/') }}" + "?page=" + page,
                    type: "GET",
                    beforeSend: function() {
                        button.text('Loading...');
                    },
                    success: function(data) {
                        if (data.trim() === '') {
                            button.hide(); // No more products
                        } else {
                            $('#new-products-container').append(data);
                            button.data('page', page + 1).text('Load More');
                        }
                    },
                    error: function() {
                        button.text('Try Again');
                    }
                });
            });
        });
    </script>

    <script>
        let page = 2;
        let loading = false;
        let endOfData = false;

        function loadMoreProducts() {
            if (loading || endOfData) return;

            loading = true;
            $('#scroll-loader').show();

            $.ajax({
                url: "{{ url('/') }}" + "?page=" + page,
                type: "GET",
                success: function(data) {
                    if (data.trim() === '') {
                        endOfData = true;
                        $('#scroll-loader').html("<p class='text-muted'>No more books to load.</p>");
                    } else {
                        $('#new-products-container').append(data);
                        page++;
                        $('#scroll-loader').hide();
                        loading = false;
                    }
                },
                error: function() {
                    $('#scroll-loader').html("<p class='text-danger'>Error loading more products.</p>");
                    loading = false;
                }
            });
        }

        $(window).on('scroll', function() {
            let scrollTop = $(window).scrollTop();
            let windowHeight = $(window).height();
            let documentHeight = $(document).height();

            if (scrollTop + windowHeight + 100 >= documentHeight) {
                loadMoreProducts();
            }
        });
    </script>
@endsection
