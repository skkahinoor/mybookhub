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
                                    <div class="d-lg-flex d-sm-inline-flex d-flex align-items-center">
                                        <ul class="dz-rating">
                                            <li><i class="flaticon-star text-yellow"></i></li>
                                            <li><i class="flaticon-star text-yellow"></i></li>
                                            <li><i class="flaticon-star text-yellow"></i></li>
                                            <li><i class="flaticon-star text-yellow"></i></li>
                                            <li><i class="flaticon-star text-muted"></i></li>
                                        </ul>
                                        <h6 class="m-b0">4.0</h6>
                                    </div>
                                    <div class="social-area">
                                        <ul class="dz-social-icon style-3">
                                            <li><a href="https://www.facebook.com/dexignzone" target="_blank"><i
                                                        class="fa-brands fa-facebook-f"></i></a></li>
                                            <li><a href="https://twitter.com/dexignzones" target="_blank"><i
                                                        class="fa-brands fa-twitter"></i></a></li>
                                            <li><a href="https://www.whatsapp.com/" target="_blank"><i
                                                        class="fa-brands fa-whatsapp"></i></a></li>
                                            <li><a href="https://www.google.com/intl/en-GB/gmail/about/" target="_blank"><i
                                                        class="fa-solid fa-envelope"></i></a></li>
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
                            <div id="developement-1" class="tab-pane">
                                <div class="clear" id="comment-list">
                                    <div class="post-comments comments-area style-1 clearfix">
                                        <h4 class="comments-title">4 COMMENTS</h4>
                                        <div id="comment">
                                            <ol class="comment-list">
                                                <li class="comment even thread-even depth-1 comment" id="comment-2">
                                                    <div class="comment-body">
                                                        <div class="comment-author vcard">
                                                            <img src="images/profile4.jpg" alt=""
                                                                class="avatar" />
                                                            <cite class="fn">Michel Poe</cite> <span
                                                                class="says">says:</span>
                                                            <div class="comment-meta">
                                                                <a href="javascript:void(0);">December 28, 2022 at 6:14
                                                                    am</a>
                                                            </div>
                                                        </div>
                                                        <div class="comment-content dlab-page-text">
                                                            <p>Donec suscipit porta lorem eget condimentum. Morbi vitae
                                                                mauris in leo venenatis varius. Aliquam nunc enim, egestas
                                                                ac dui in, aliquam vulputate erat.</p>
                                                        </div>
                                                        <div class="reply">
                                                            <a rel="nofollow" class="comment-reply-link"
                                                                href="javascript:void(0);"><i class="fa fa-reply"></i>
                                                                Reply</a>
                                                        </div>
                                                    </div>
                                                    <ol class="children">
                                                        <li class="comment byuser comment-author-w3itexpertsuser bypostauthor odd alt depth-2 comment"
                                                            id="comment-3">
                                                            <div class="comment-body" id="div-comment-3">
                                                                <div class="comment-author vcard">
                                                                    <img src="images/profile3.jpg" alt=""
                                                                        class="avatar" />
                                                                    <cite class="fn">Celesto Anderson</cite> <span
                                                                        class="says">says:</span>
                                                                    <div class="comment-meta">
                                                                        <a href="javascript:void(0);">December 28, 2022 at
                                                                            6:14 am</a>
                                                                    </div>
                                                                </div>
                                                                <div class="comment-content dlab-page-text">
                                                                    <p>Donec suscipit porta lorem eget condimentum. Morbi
                                                                        vitae mauris in leo venenatis varius. Aliquam nunc
                                                                        enim, egestas ac dui in, aliquam vulputate erat.</p>
                                                                </div>
                                                                <div class="reply">
                                                                    <a class="comment-reply-link"
                                                                        href="javascript:void(0);"><i
                                                                            class="fa fa-reply"></i>
                                                                        Reply</a>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </li>
                                                <li class="comment even thread-odd thread-alt depth-1 comment"
                                                    id="comment-4">
                                                    <div class="comment-body" id="div-comment-4">
                                                        <div class="comment-author vcard">
                                                            <img src="images/profile2.jpg" alt=""
                                                                class="avatar" />
                                                            <cite class="fn">Ryan</cite> <span
                                                                class="says">says:</span>
                                                            <div class="comment-meta">
                                                                <a href="javascript:void(0);">December 28, 2022 at 6:14
                                                                    am</a>
                                                            </div>
                                                        </div>
                                                        <div class="comment-content dlab-page-text">
                                                            <p>Donec suscipit porta lorem eget condimentum. Morbi vitae
                                                                mauris in leo venenatis varius. Aliquam nunc enim, egestas
                                                                ac dui in, aliquam vulputate erat.</p>
                                                        </div>
                                                        <div class="reply">
                                                            <a class="comment-reply-link" href="javascript:void(0);"><i
                                                                    class="fa fa-reply"></i> Reply</a>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="comment odd alt thread-even depth-1 comment" id="comment-5">
                                                    <div class="comment-body" id="div-comment-5">
                                                        <div class="comment-author vcard">
                                                            <img src="images/profile1.jpg" alt=""
                                                                class="avatar" />
                                                            <cite class="fn">Stuart</cite> <span
                                                                class="says">says:</span>
                                                            <div class="comment-meta">
                                                                <a href="javascript:void(0);">December 28, 2022 at 6:14
                                                                    am</a>
                                                            </div>
                                                        </div>
                                                        <div class="comment-content dlab-page-text">
                                                            <p>Donec suscipit porta lorem eget condimentum. Morbi vitae
                                                                mauris in leo venenatis varius. Aliquam nunc enim, egestas
                                                                ac dui in, aliquam vulputate erat.</p>
                                                        </div>
                                                        <div class="reply">
                                                            <a rel="nofollow" class="comment-reply-link"
                                                                href="javascript:void(0);"><i class="fa fa-reply"></i>
                                                                Reply</a>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ol>
                                        </div>
                                        <div class="default-form comment-respond style-1" id="respond">
                                            <h4 class="comment-reply-title" id="reply-title">LEAVE A REPLY <small> <a
                                                        rel="nofollow" id="cancel-comment-reply-link"
                                                        href="javascript:void(0)" style="display:none;">Cancel reply</a>
                                                </small></h4>
                                            <div class="clearfix">
                                                <form method="post" id="comments_form" class="comment-form" novalidate>
                                                    <p class="comment-form-author"><input id="name"
                                                            placeholder="Author" name="author" type="text"
                                                            value=""></p>
                                                    <p class="comment-form-email"><input id="email"
                                                            required="required" placeholder="Email" name="email"
                                                            type="email" value=""></p>
                                                    <p class="comment-form-comment">
                                                        <textarea id="comments" placeholder="Type Comment Here" class="form-control4" name="comment" cols="45"
                                                            rows="3" required="required"></textarea>
                                                    </p>
                                                    <p class="col-md-12 col-sm-12 col-xs-12 form-submit">
                                                        <button id="submit" type="submit"
                                                            class="submit btn btn-primary filled">
                                                            Submit Now <i class="fa fa-angle-right m-l10"></i>
                                                        </button>
                                                    </p>
                                                </form>
                                            </div>
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
                            @if (count($similarProducts) > 0)
                                @foreach ($similarProducts as $similarProduct)
                                    @php
                                        $getDiscountPriceDetails = \App\Models\Product::getDiscountPriceDetails(
                                            $similarProduct['id'],
                                        );
                                    @endphp
                                    <div class="col-xl-12 col-lg-6">
                                        <div class="dz-shop-card style-5">
                                            <div class="dz-media">
                                                @if (!empty($similarProduct['product_image']))
                                                    <img src="{{ asset('front/images/product_images/small/' . $similarProduct['product_image']) }}"
                                                        alt="{{ $similarProduct['product_name'] ?? 'Product' }}"
                                                        class="img-fluid">
                                                @else
                                                    <img src="{{ asset('front/images/product_images/small/no-image.png') }}"
                                                        alt="No Image" class="img-fluid">
                                                @endif
                                            </div>
                                            <div class="dz-content">
                                                <h5 class="subtitle">
                                                    <a href="{{ url('product/' . $similarProduct['id']) }}"
                                                        class="text-dark">
                                                        {{ $similarProduct['product_name'] ?? 'Product Name Not Available' }}
                                                    </a>
                                                </h5>
                                                <ul class="dz-tags">
                                                    @if (isset($similarProduct['publisher']['name']))
                                                        <li>{{ strtoupper($similarProduct['publisher']['name']) }},</li>
                                                    @endif
                                                    @if (isset($similarProduct['category']['category_name']))
                                                        <li>{{ strtoupper($similarProduct['category']['category_name']) }},
                                                        </li>
                                                    @endif
                                                    @if (isset($similarProduct['authors']) && count($similarProduct['authors']) > 0)
                                                        <li>{{ strtoupper($similarProduct['authors'][0]['name'] ?? 'Unknown Author') }}
                                                        </li>
                                                    @endif
                                                </ul>
                                                <div class="price">
                                                    @if (isset($getDiscountPriceDetails['discount']) && $getDiscountPriceDetails['discount'] > 0)
                                                        <span
                                                            class="price-num">₹{{ $getDiscountPriceDetails['final_price'] ?? 0 }}</span>
                                                        <del>₹{{ $getDiscountPriceDetails['product_price'] ?? 0 }}</del>
                                                    @else
                                                        <span
                                                            class="price-num">₹{{ $getDiscountPriceDetails['final_price'] ?? 0 }}</span>
                                                    @endif
                                                </div>
                                                <form action="{{ url('cart/add') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id"
                                                        value="{{ $similarProduct['id'] }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-primary btnhover2"><i
                                                            class="flaticon-shopping-cart-1"></i> <span>&nbsp;&nbsp;Add to
                                                            cart</span></button>
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
