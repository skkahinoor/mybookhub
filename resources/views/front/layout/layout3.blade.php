<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="" />
    <meta name="robots" content="" />

    @if (!empty($meta_description))
        <meta name="description" content="{{ $meta_description }}">
    @endif
    @if (!empty($meta_keywords))
        <meta name="keywords" content="{{ $meta_keywords }}">
    @endif

    <meta property="og:title" content="" />
    <meta property="og:description" content="" />
    <meta property="og:image" content="" />
    <meta name="format-detection" content="telephone=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('') }}">
    <meta name="app-url" content="{{ config('app.url') }}">

    <!-- FAVICONS ICON -->
    <link rel="icon" type="image/x-icon" href="{{ asset('uploads/favicons/' . $logos->first()->favicon) }}" />

    <!-- PAGE TITLE HERE -->
    <title>
        @if (!empty($meta_title))
            {{ $meta_title }}
        @else
            BookHub - The place to Buy & sell Books
        @endif
    </title>

    <!-- MOBILE SPECIFIC -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- STYLESHEETS -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('front/newtheme/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('front/newtheme/icons/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('front/newtheme/vendor/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('front/newtheme/vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('front/newtheme/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('front/css/custom-wishlist.css') }}">

    <!-- GOOGLE FONTS-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">


    <style>
        .ul-menu {
            cursor: pointer;
        }

        .submenu {
            background: white;
            position: absolute;
            top: 100%;
            left: 0;
            border: 1px solid rgb(219, 219, 219);
            width: 200px;
            display: none;
        }

        .submenu li {
            line-height: 40px;
        }

        .submenu li:hover {
            background: var(--primary);

        }

        .submenu li:hover a {
            color: white;
        }

        .submenu1>li:not(:last-child) {
            /* border-bottom: 1px solid rgb(212, 211, 211); */
        }

        .submenu li a {
            padding-inline: 10px;
        }

        .submenu1 {
            position: absolute;
            top: 0;
            left: 100%;
            display: none;
            background: white;
            border: 1px solid rgb(219, 219, 219);
            width: 150px;
        }

        .submenu>li:hover>.submenu1 {
            display: block;
        }

        .submenu1 li a {
            /* color: var(--primary) !important; */
            width: 149px !important;
            display: block !important;
            white-space: normal !important;
            line-height: 20px !important;
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        .submenu1 li:hover a {
            color: var(--primary) !important;
        }

        /* Ensure mini-cart images stay small even after AJAX updates */
        .cart-list .media-left img,
        .cart-list img,
        .headerCartItems img,
        .cart-item img {
            width: 60px !important;
            height: 60px !important;
            object-fit: cover !important;
            border-radius: 6px;
        }
    </style>

</head>

<body>

    <div class="page-wraper">
        <div id="loading-area" class="preloader-wrapper-1">
            <div class="preloader-inner">
                <div class="preloader-shade"></div>
                <div class="preloader-wrap"></div>
                <div class="preloader-wrap wrap2"></div>
                <div class="preloader-wrap wrap3"></div>
                <div class="preloader-wrap wrap4"></div>
                <div class="preloader-wrap wrap5"></div>
            </div>
        </div>

        <!-- Header -->
        <header class="site-header mo-left header style-1">
            <!-- Main Header -->
            <div class="header-info-bar">
                <div class="container clearfix">
                    <!-- Website Logo -->
                    <div class="logo-header logo-dark">
                        <a href="{{ url('/') }}"><img src="{{ asset('uploads/logos/' . $logos->first()->logo) }}"
                                alt="BookHub"></a>
                    </div>

                    <!-- EXTRA NAV -->
                    <div class="extra-nav">
                        <div class="extra-cell">
                            <ul class="navbar-nav header-right">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/wishlist') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24"
                                            width="24px" fill="#000000">
                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                            <path
                                                d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z" />
                                        </svg>
                                        <span class="badge totalWishlistItems">{{ isset($headerWishlistItemsCount) ? $headerWishlistItemsCount : 0 }}</span>
                        {{-- Debug: {{ var_dump($headerWishlistItemsCount ?? 'not set') }} --}}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link box cart-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24"
                                            width="24px" fill="#000000">
                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                            <path
                                                d="M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
                                        </svg>
                                        <span class="badge totalCartItems">{{ isset($headerCartItems) ? count($headerCartItems) : 0 }}</span>
                                    </button>
                                    <ul class="dropdown-menu cart-list headerCartItems">
                                        @if(isset($headerCartItems) && count($headerCartItems) > 0)
                                            @foreach ($headerCartItems as $item)
                                                @php
                                                       $getDiscountPriceDetail = \App\Models\Product::getDiscountPriceDetails($item['product_id']);
                                                @endphp
                                                <li class="cart-item">
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <a href="{{ asset('front/images/product_images/large/' . ($item['product']['product_image'] ?? 'no-image.png')) }}"
                                class="main-image-link">
                                                                <img src="{{ asset('front/images/product_images/large/' . ($item['product']['product_image'] ?? 'no-image.png')) }}"
                                                                    alt="{{ $item['product']['product_name'] ?? 'Product' }}"
                                                                    class="img-fluid rounded shadow-sm"
                                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                                            </a>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="dz-title"><a
                                                                    href="{{ url('product/' . $item['product_id']) }}"
                                                                    class="media-heading">{{ $item['product']['product_name'] ?? 'Product' }}</a>
                                                            </h6>
                                                            <span class="dz-price">
                                                                @if ($getDiscountPriceDetail['discount'] > 0)
                                                                    ₹{{ $getDiscountPriceDetail['final_price'] }} x {{ ($item['quantity'] ?? 1) }}
                                                                @else
                                                                    ₹{{ $getDiscountPriceDetail['final_price'] }} x {{ ($item['quantity'] ?? 1) }}
                                                                @endif
                                                            </span>

                                                            <a href="javascript:void(0);"><span class="item-close" data-cartid="{{ $item['id'] }}">&times;</span></a>

                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                            <li class="cart-item text-center">
                                                <h6 class="text-secondary">Total = ₹{{ $headerCartTotal }}</h6>
                                            </li>
                                            <li class="text-center d-flex">
                                                <a href="{{ url('/cart') }}"
                                                    class="btn btn-sm btn-primary me-2 btnhover w-100">View Cart</a>
                                                <a href="{{ url('/checkout') }}"
                                                    class="btn btn-sm btn-outline-primary btnhover w-100">Checkout</a>
                                            </li>
                                        @else
                                            <li class="cart-item text-center">
                                                <h6 class="text-secondary">Your cart is empty</h6>
                                            </li>
                                            <li class="text-center">
                                                <a href="{{ url('/') }}" class="btn btn-sm btn-primary btnhover w-100">Start Shopping</a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                                @guest
                                    <li class="nav-item">
                                        <div class="row">
                                            <div class="col" style="padding-right: 3px;">
                                                <a href="#" class="btn btn-primary btnhover" data-bs-toggle="modal"
                                                    data-bs-target="#loginModal" class="btn text-white"
                                                    style="font-size: 12px;">
                                                    <i class="fas fa-user" style="margin-right:8px;"></i> Login
                                                </a>
                                            </div>
                                            <div class="col" style="padding-left: 3px;">
                                                <a href="#" class="btn btn-primary btnhover" data-bs-toggle="modal"
                                                    data-bs-target="#registerModal" class="btn text-white"
                                                    style="font-size: 12px;">
                                                    <i class="fas fa-user-plus" style="margin-right:4px;"></i> Register
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @else
                                    <li class="nav-item dropdown profile-dropdown  ms-4">
                                        <a class="nav-link" href="javascript:void(0);" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <img src="{{ asset(Auth::user()->ImageUpload->filename ?? 'assets/images/avatar.png') }}"
                                                alt="/">
                                            <div class="profile-info">
                                                <h6 class="title">{{ Auth::user()->name }}</h6>
                                                <span>{{ Auth::user()->email ?? 'Email Not Set' }}</span>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu py-0 dropdown-menu-end">
                                            <div class="dropdown-header">
                                                <h6 class="m-0">{{ Auth::user()->name }}</h6>
                                                <span>{{ Auth::user()->email ?? 'Email Not Set' }}</span>
                                            </div>
                                            <div class="dropdown-body">
                                                <a href="{{ route('useraccount') }}"
                                                    class="dropdown-item d-flex justify-content-between align-items-center ai-icon">
                                                    <div>
                                                        <svg xmlns="http://www.w3.org/2000/svg" height="20px"
                                                            viewBox="0 0 24 24" width="20px" fill="#000000">
                                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                                            <path
                                                                d="M12 6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2m0 10c2.7 0 5.8 1.29 6 2H6c.23-.72 3.31-2 6-2m0-12C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                        </svg>
                                                        <span class="ms-2">Profile</span>
                                                    </div>
                                                </a>
                                                <a href="{{ url('user/orders') }}"
                                                    class="dropdown-item d-flex justify-content-between align-items-center ai-icon">
                                                    <div>
                                                        <svg xmlns="http://www.w3.org/2000/svg" height="20px"
                                                            viewBox="0 0 24 24" width="20px" fill="#000000">
                                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                                            <path
                                                                d="M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
                                                        </svg>
                                                        <span class="ms-2">My Order</span>
                                                    </div>
                                                </a>
                                                <a href="{{ url('wishlist') }}" method="POST"
                                                    class="dropdown-item d-flex justify-content-between align-items-center ai-icon">
                                                    <div>
                                                        <svg xmlns="http://www.w3.org/2000/svg" height="20px"
                                                            viewBox="0 0 24 24" width="20px" fill="#000000">
                                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                                            <path
                                                                d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z" />
                                                        </svg>
                                                        <span class="ms-2">Wishlist</span>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="dropdown-footer">
                                                <a class="btn btn-primary w-100 btnhover btn-sm"
                                                    href="{{ route('logout') }}"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
                                                    Out</a>
                                            </div>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>
                                        </div>
                                    </li>
                                @endguest

                            </ul>
                        </div>
                    </div>

                    <!-- header search nav -->
                    <div class="header-search-nav">
                        <div class="header-item-search">
                            <div class="input-group search-input position-relative">
                                <ul class="ul-menu"
                                    style="background:#F5F5F5; height:48px; width:150px; padding-left:20px;padding-right:10px; margin-right:5px;">
                                    <li class="text-dark"
                                        style="font-weight:600; height:100%; font-size:0.9rem;line-height:48px; pointer:cursor;">
                                        <span style="font-size: 13px;">Books Category</span> <i
                                            class="fa-solid fa-angle-down"
                                            style="font-size:0.6rem; margin-left:3px; vertical-align:middle;"></i>

                                        <ul class="submenu">
                                            <li class="position-relative"><a href="{{ url('/category-products') }}"
                                                    class="d-flex justify-content-between text-dark align-items-center">All
                                                    Books
                                                </a>
                                            </li>
                                            @foreach ($sections as $section)
                                                @if (!empty($section['categories']) && count($section['categories']) > 0)
                                                    <li class="position-relative"><a href="#"
                                                            class="d-flex justify-content-between text-dark align-items-center">{{ $section['name'] }}
                                                            <i class="fa-solid fa-angle-right"></i></a>
                                                        <ul class="submenu1">
                                                            @foreach ($section['categories'] as $category)
                                                                @if (!empty($category['sub_categories']) && count($category['sub_categories']) > 0)
                                                                    <li class="dropdown-submenu position-relative">
                                                                        <a class="dropdown-item dropdown-toggle"
                                                                            href="{{ url('/category-products/' . $category['id']) }}">{{ $category['category_name'] }}</a>
                                                                        <ul class="dropdown-menu">
                                                                            @foreach ($category['sub_categories'] as $subcategory)
                                                                                <li>
                                                                                    <a class="dropdown-item text-dark"
                                                                                        href="{{ url('/category-products/' . $subcategory['id']) }}">{{ $subcategory['category_name'] }}</a>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </li>
                                                                @else
                                                                    <li>
                                                                        <a class="dropdown-item text-dark"
                                                                            href="{{ url('/category-products/' . $category['id']) }}">{{ $category['category_name'] }}</a>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @else
                                                    <li class="position-relative"><a
                                                            href="{{ url('/category-products?section_id=' . $section['id']) }}"
                                                            class="d-flex justify-content-between text-dark align-items-center">{{ $section['name'] }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>

                                <input type="text" class="form-control" id="headerSearchInput"
                                    aria-label="Text input with dropdown button" placeholder="Search Books Here"
                                    style="border-top-left-radius:0px !important; border-bottom-left-radius:0px !important;">
                                <button class="btn" id="headerSearchButton" type="button"><i class="flaticon-loupe"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Header End -->

            <!-- Main Header -->
            <div class="sticky-header main-bar-wraper navbar-expand-lg">
                <div class="main-bar clearfix">
                    <div class="container clearfix">
                        <!-- Website Logo -->
                        <div class="logo-header logo-dark">
                            <a href="{{ url('/') }}"><img
                                    src="{{ asset('uploads/logos/' . $logos->first()->logo) }}" alt="BookHub"></a>
                        </div>

                        <!-- Nav Toggle Button -->
                        <button class="navbar-toggler collapsed navicon justify-content-end" type="button"
                            data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>

                        <!-- EXTRA NAV -->
                        <div class="extra-nav">
                            <div class="extra-cell">
                                <a href="{{ url('/contact') }}" class="btn btn-primary btnhover">Get In Touch</a>
                            </div>
                        </div>

                        <!-- Main Nav -->
                        <div class="header-nav navbar-collapse collapse justify-content-start" id="navbarNavDropdown">
                            <div class="logo-header logo-dark">
                                <a href="{{ url('/') }}"><img
                                        src="{{ asset('uploads/logos/' . $logos->first()->logo) }}"
                                        alt=""></a>
                            </div>
                            <div class="search-input">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="mobileSearchInput"
                                        aria-label="Text input with dropdown button" placeholder="Search Books Here">
                                    <button class="btn" id="mobileSearchButton" type="button"><i class="flaticon-loupe"></i></button>
                                </div>
                            </div>
                            <ul class="nav navbar-nav">
                                <li class="nav-item"><a href="{{ url('/') }}"
                                        class="nav-link"><span>Home</span></a>
                                </li>
                                <li class="nav-item"><a href="{{ url('/about') }}" class="nav-link"><span>About Us</span></a></li>
                                <li class="sub-menu-down"><a href="javascript:void(0);"><span><i
                                                class="fas fa-tag me-2"></i> Condition
                                            ({{ ucfirst($condition) }})</span></a>
                                    <ul class="sub-menu">
                                        <li><a href="javascript:void(0);" onclick="set('all')">All</a></li>
                                        <li><a href="javascript:void(0);" onclick="set('new')">New</a></li>
                                        <li><a href="javascript:void(0);" onclick="set('old')">Old</a></li>
                                    </ul>
                                </li>
                                <li class="sub-menu-down"><a href="javascript:void(0);"><span><i
                                                class="fas fa-globe me-2"></i>
                                            Language ({{ ucfirst($selectedLanguage->name ?? 'All') }})</span></a>
                                    <ul class="sub-menu">
                                        <li><a href="javascript:void(0);" onclick="setLanguage('all')">All</a></li>
                                        @foreach ($language as $lang)
                                            <li><a href="javascript:void(0);"
                                                    onclick="setLanguage('{{ $lang->id }}')">{{ $lang->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                {{-- <li class="sub-menu-down"><a href="javascript:void(0);"><span>Blog</span></a>
                                    <ul class="sub-menu">
                                        <li><a href="blog-grid.html">Blog Grid</a></li>
                                        <li><a href="blog-large-sidebar.html">Blog Large Sidebar</a></li>
                                        <li><a href="blog-list-sidebar.html">Blog List Sidebar</a></li>
                                        <li><a href="blog-detail.html">Blog Details</a></li>
                                    </ul>
                                </li>
                                <li><a href="contact-us.html"><span>Contact Us</span></a></li> --}}
                            </ul>
                            <div class="dz-social-icon">
                                <ul>
                                    <li><a class="fab fa-facebook-f" target="_blank"
                                            href="https://www.facebook.com/dexignzone"></a></li>
                                    <li><a class="fab fa-twitter" target="_blank"
                                            href="https://www.twitter.com/dexignzones"></a></li>
                                    <li><a class="fab fa-linkedin-in" target="_blank"
                                            href="https://www.linkedin.com/showcase/3686700/admin/"></a></li>
                                    <li><a class="fab fa-instagram" target="_blank"
                                            href="https://www.instagram.com/website_templates__/"></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Header End -->

        </header>
        <!-- Header End -->

        <div class="page-content bg-white">

            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="site-footer style-1">
            <!-- Footer Category -->
            <div class="footer-category">
                <div class="container">
                    <div class="category-toggle">
                        <a href="javascript:void(0);" class="toggle-btn">Books categories</a>
                        <div class="toggle-items row">
                            <div class="footer-col-book">
                                <ul>
                                    {{-- <li><a href="{{ url('/category-products') }}">All Books</a></li> --}}
                                    @foreach ($sections as $section)
                                        @if (!empty($section['categories']) && count($section['categories']) > 0)
                                            @foreach ($section['categories'] as $category)
                                                <li>
                                                    <a href="{{ url('/category-products/' . $category['id']) }}">{{ $category['category_name'] }}</a>
                                                </li>
                                            @endforeach
                                        @else
                                            <li>
                                                <a href="{{ url('/category-products?section_id=' . $section['id']) }}">{{ $section['name'] }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Category End -->

            <!-- Footer Top -->
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3 col-lg-12 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="widget widget_about">
                                <div class="footer-logo logo-white">
                                    <a href="index.html"><img
                                            src="{{ asset('uploads/logos/' . $logos->first()->logo) }}"
                                            alt=""></a>
                                </div>
                                <p class="text">Bookhub - BookStore Script System is an online Discovering great
                                    books website
                                    filled with the latest and best selling Books.</p>
                                <div class="dz-social-icon style-1">
                                    <ul>
                                        <li><a href="#" target="_blank"><i
                                                    class="fa-brands fa-facebook-f"></i></a></li>
                                        <li><a href="#" target="_blank"><i
                                                    class="fa-brands fa-twitter"></i></a></li>

                                        <li><a href="#" target="_blank"><i
                                                    class="fa-brands fa-instagram"></i></a></li>
                                        <li><a href="#" target="_blank"><i
                                                    class="fa-brands fa-linkedin"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-4 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="widget widget_services">
                                <h5 class="footer-title">Our Links</h5>
                                <ul>
                                    <li><a href="{{ url('/about') }}">About us</a></li>
                                    <li><a href="{{ url('/contact') }}">Contact us</a></li>
                                    <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-sm-4 col-4 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="widget widget_services">
                                <h5 class="footer-title">Bookhub ?</h5>
                                <ul>
                                    <li><a href="{{ url('/') }}">Bookhub</a></li>
                                    <li><a href="{{ url('/services') }}">Services</a></li>
                                    {{-- <li><a href="{{ url('product/' . $products['id']) }}">Book Details</a></li> --}}
                                    <li><a href="blog-detail.html">Blog Details</a></li>
                                    <li><a href="#">Shop</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-4 wow fadeInUp" data-wow-delay="0.4s">
                            <div class="widget widget_services">
                                <h5 class="footer-title">Resources</h5>
                                <ul>
                                    <li><a href="services.html">Download</a></li>
                                    <li><a href="help-desk.html">Help Center</a></li>
                                    <li><a href="shop-cart.html">Shop Cart</a></li>
                                    <li><a href="{{ url('/user/login-register') }}">Login</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 wow fadeInUp" data-wow-delay="0.5s">
                            <div class="widget widget_getintuch">
                                <h5 class="footer-title">Get in Touch With Us</h5>
                                <ul>
                                    <li>
                                        <i class="flaticon-placeholder"></i>
                                        <span>Plot No-325, Baramunda ISBT,
                                            Above MRF Tyre Showroom
                                            Bhubaneswar-751003</span>
                                    </li>
                                    <li>
                                        <i class="flaticon-phone"></i>
                                        <span>+91 (0) 84807 46394, +91 (0) 84807 46395</span>
                                    </li>
                                    <li>
                                        <i class="flaticon-email"></i>
                                        <span>support@srdcindia.co.in</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Top End -->

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="container">
                    <div class="row fb-inner">
                        <div class="col-lg-6 col-md-12 text-start">
                            <p class="copyright-text">Bookhub Book Store Ecommerce Website - © 2025 All Rights
                                Reserved</p>
                        </div>
                        <div class="col-lg-6 col-md-12 text-end">
                            <p>Made with <span class="heart"></span> by <a href="https://srdcindia.co.in/">Sridipta
                                    research & development consultancy pvt. ltd.</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Bottom End -->

        </footer>
        <!-- Footer End -->

        <button class="scroltop" type="button"><i class="fas fa-arrow-up"></i></button>
    </div>

    {{-- Modals  --}}

    {{-- Login --}}
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="loginForm" method="POST" action="{{ route('user.login') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Email or Mobile -->
                        <div class="mb-3">
                            <label>Email or Mobile</label>
                            <input type="text" name="login" class="form-control" placeholder="Enter email or 10-digit mobile" required>
                        </div>
                        <!-- Password -->
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Register --}}
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="registerForm" method="POST" action="{{ route('user.register') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Register</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Name -->
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <!-- Email -->
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <!-- Password -->
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <!-- Mobile -->
                        <div class="mb-3">
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- JAVASCRIPT FILES ========================================= -->
    <script src="{{ asset('front/newtheme/js/jquery.min.js') }}"></script><!-- JQUERY MIN JS -->
    <script src="{{ asset('front/newtheme/vendor/wow/wow.min.js') }}"></script><!-- WOW JS -->
    <script src="{{ asset('front/newtheme/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script><!-- BOOTSTRAP MIN JS -->
    <script src="{{ asset('front/newtheme/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script><!-- BOOTSTRAP SELECT MIN JS -->
    <script src="{{ asset('front/newtheme/vendor/counter/waypoints-min.js') }}"></script><!-- WAYPOINTS JS -->
    <script src="{{ asset('front/newtheme/vendor/counter/counterup.min.js') }}"></script><!-- COUNTERUP JS -->
    <script src="{{ asset('front/newtheme/vendor/swiper/swiper-bundle.min.js') }}"></script><!-- SWIPER JS -->
    <script src="{{ asset('front/newtheme/js/dz.carousel.js') }}"></script><!-- DZ CAROUSEL JS -->
    <script src="{{ asset('front/newtheme/js/dz.ajax.js') }}"></script><!-- AJAX -->
    <script src="{{ asset('front/newtheme/js/custom.js') }}"></script><!-- CUSTOM JS -->


    <script>
        // Global search handlers for header and sticky inputs
        (function() {
            function goToSearch(term) {
                var base = document.querySelector('meta[name="base-url"]').getAttribute('content') || '';
                var url = base.replace(/\/$/, '') + '/search-products';
                if (term && term.trim().length) {
                    window.location.href = url + '?search=' + encodeURIComponent(term.trim());
                } else {
                    window.location.href = url;
                }
            }

            function bindSearch(inputId, buttonId) {
                var input = document.getElementById(inputId);
                var button = document.getElementById(buttonId);
                if (!input || !button) return;

                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    goToSearch(input.value);
                });
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        goToSearch(input.value);
                    }
                });
            }

            bindSearch('headerSearchInput', 'headerSearchButton');
            bindSearch('mobileSearchInput', 'mobileSearchButton');
        })();
    </script>
    <script>
        // Header mini-cart: delete item via AJAX
        $(document).on('click', '.cart-list .item-close', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var cartId = $(this).data('cartid');
            var $li = $(this).closest('li.cart-item');
            if (!cartId) return;

            $.ajax({
                url: '{{ route('cartDelete') }}',
                type: 'POST',
                data: {
                    cartid: cartId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resp) {
                    if (resp.status) {
                        // Auto refresh the page to ensure all UI, totals, and fragments are fully synced
                        window.location.reload();
                        return;
                    } else {
                        alert(resp.message || 'Could not delete item.');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Something went wrong.');
                }
            });
        });
    </script>
    <script>
        const li = document.querySelector(".ul-menu li");
        const submenu = document.querySelector(".submenu");
        li.addEventListener("click", function() {
            submenu.classList.add("d-block");
        });

        document.addEventListener("click", function(e) {

            if (!e.target.closest(".ul-menu")) {
                submenu.classList.remove("d-block");
            }

            if (e.target.closest(".submenu1")) {
                submenu.classList.remove("d-block");
            }

        });
    </script>
    <script>
        function set(condition) {
            fetch("{{ route('set.condition') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        condition: condition
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('firstVisitModal'));
                        if (modal) modal.hide(); // Bootstrap 5
                        localStorage.setItem('firstVisitShown', true);
                        location.reload();
                    }
                });
        }
    </script>

    <script>
        function setLanguage(languageId) {
            fetch("{{ url('/set-language') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        language: languageId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // or redirect if needed
                    }
                });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Send the coordinates to the backend via AJAX
                    fetch("{{ url('/set-location-session') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        })
                    });
                });
            }
        });
    </script>


</body>

</html>
