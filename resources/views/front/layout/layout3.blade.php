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
    @if (!empty($logos))
        <link rel="icon" type="image/x-icon" href="{{ asset('uploads/favicons/' . $logos->first()->favicon) }}">
    @endif


    <!-- PAGE TITLE HERE -->
    <title>
        @if (!empty($meta_title))
            {{ $meta_title }}
        @else
            BookHub - The place to Buy & sell Books
        @endif
    </title>

    <!-- MOBILE SPECIFIC -->
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, viewport-fit=cover">

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
        :root {
            --primary-orange: #FF6B00;
            --primary-blue: #2979FF;
            --bg-light: #F8F9FB;
            --text-dark: #1A1A1A;
            --text-muted: #8E8E93;
        }

        body {
            background-color: var(--bg-light) !important;
            font-family: 'Poppins', sans-serif !important;
            padding-bottom: 60px !important;
            margin: 0 !important;
            width: 100%;
            overflow-x: hidden;
        }

        /* ============================
           Premium Unified Header
           ============================ */
        .layout-app-header {
                background: #EEF5FD;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    position: sticky;
    top: 0;
    z-index: 2000;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        .header-logo img {
            height: 35px;
            width: auto;
            object-fit: contain;
        }

        .header-search {
            display:flex;
background:white;
border-radius:8px;
overflow:hidden;
width:420px;
box-shadow:0 2px 10px rgba(0,0,0,0.08);
        }

        .header-search input {
            flex:1;
border:none;
padding:10px;
outline:none;
        }

        .header-search input:focus {
            background: #fff;
            border-color: var(--primary-orange);
            box-shadow: 0 8px 20px rgba(255, 107, 0, 0.08);
        }
.header-search button{
background:#2f6fc2;
color:white;
border:none;
padding:10px 16px;
}

.header-icons{
display:flex;
gap:25px;
}

.icon{
position:relative;
cursor:pointer;
}

.badge{
position:absolute;
top:-6px;
right:-8px;
background:#ff6b00;
color:white;
font-size:10px;
padding:3px 6px;
border-radius:50%;
}
        .header-search .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 14px;
        }

        .header-search .scan-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 18px;
            cursor: pointer;
        }

        .header-icons {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-left: auto;
        }

        .header-icons .icon-btn {
            position: relative;
            color: #4A4A4A;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-icons .icon-btn:hover {
            color: var(--primary-orange);
            transform: translateY(-2px);
        }

        .header-icons .badge-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: #FF3B30;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .user-name-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            margin-left: 8px;
            white-space: nowrap;
        }

        .header-icons .icon-badge-num {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--primary-orange);
            color: #fff;
            font-size: 10px;
            min-width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border: 1.5px solid #fff;
        }

        @media (max-width: 991px) {

            .desktop-only-icon,
            .user-name-text,
            .header-filters {
                display: none !important;
            }
        }

        /* Category Menu Styling */
        .category-menu-wrapper {
            position: relative;
            margin-left: 15px;
            margin-right: 5px;
        }

        .category-trigger {
            background: #F1F3F6;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: 1.5px solid transparent;
            min-height: 44px;
        }

        .category-dropdown {
            position: absolute;
            top: 110%;
            left: 0;
            width: 650px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            display: none;
            z-index: 3000;
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            padding: 0;
        }

        .category-dropdown.active {
            display: flex;
            animation: megaFadeIn 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes megaFadeIn {
            from {
                opacity: 0;
                transform: translateY(15px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Mega Menu Layout */
        .mega-sidebar {
            width: 220px;
            background: #F8F9FA;
            border-right: 1px solid #EEEEEE;
            padding: 15px 0;
        }

        .sidebar-item {
            padding: 14px 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: #444;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-item i {
            width: 20px;
            text-align: center;
            color: #888;
            font-size: 16px;
        }

        .sidebar-item.active {
            background: #fff;
            color: var(--primary-orange);
        }

        .sidebar-item.active i {
            color: var(--primary-orange);
        }

        .sidebar-item.active::after {
            content: '';
            position: absolute;
            right: -1px;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--primary-orange);
        }

        .mega-content-area {
            flex: 1;
            padding: 25px;
            background: #fff;
            max-height: 450px;
            overflow-y: auto;
        }

        .section-pane {
            display: none;
        }

        .section-pane.active {
            display: block;
        }

        .boards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .board-column {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .board-title {
            font-size: 13px;
            font-weight: 700;
            color: #1A1A1A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 5px;
            border-bottom: 2px solid #F0F0F0;
            margin-bottom: 5px;
        }

        .class-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .class-link {
            font-size: 13px;
            color: #666;
            text-decoration: none !important;
            transition: all 0.2s;
            padding: 4px 8px;
            border-radius: 6px;
            margin-left: -8px;
        }

        .class-link:hover {
            background: #FFF5EE;
            color: var(--primary-orange);
            padding-left: 12px;
        }

        .category-trigger:hover {
            border-color: rgba(255, 107, 0, 0.2);
            background: #fff;
        }



        /* ============================
           Premium Unified Bottom Nav
           ============================ */
        .layout-bottom-shelf {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 58px !important;
            /* Exact height to prevent stretching */
            background: #fff !important;
            display: flex !important;
            justify-content: space-around !important;
            align-items: flex-end !important;
            /* Content stays at the bottom */
            padding-bottom: 2px !important;
            /* Tiny gap for labels */
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.08) !important;
            z-index: 9999 !important;
            border-radius: 24px 24px 0 0 !important;
            margin: 0 !important;
        }

        .layout-shelf-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            height: 52px !important;
            text-decoration: none !important;
            color: #BDBDBD;
            font-size: 10px;
            font-weight: 600;
        }

        .layout-shelf-item.active {
            color: var(--text-dark);
        }

        .layout-shelf-item i {
            font-size: 20px;
            margin-bottom: 1px;
        }

        .layout-fab-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            height: 52px !important;
            position: relative;
        }

        .layout-fab-button {
            width: 58px;
            height: 58px;
            background: var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 24px;
            box-shadow: 0 8px 20px rgba(41, 121, 255, 0.3);
            border: 4px solid #fff;
            position: absolute;
            top: -28px;
            text-decoration: none !important;
        }

        .layout-fab-label {
            font-size: 10px;
            font-weight: 700;
            color: #666;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .cart-tag {
            position: absolute;
            top: -5px;
            right: -8px;
            background: var(--primary-orange);
            color: #fff;
            font-size: 9px;
            min-width: 16px;
            height: 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            font-weight: 700;
            border: 2px solid #fff;
        }

        @media (min-width: 992px) {
            .layout-app-header {
                padding: 15px 60px;
                box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
            }

            .header-icons {
                gap: 25px;
            }

            .layout-bottom-shelf {
                display: none !important;
            }

            body {
                padding-bottom: 0 !important;
            }

            .header-search {
                margin-left: 10px;
                max-width: 500px !important;
                flex: 1;
            }
        }

        /* Fix for footer when app nav is present */
        @media (max-width: 991px) {
            .site-footer {
                margin-bottom: 0;
            }

            .layout-app-header {
                display: flex;
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: center;
                padding: 10px 12px;
                gap: 8px;
            }

            .header-logo {
                flex: 0 0 auto;
                margin: 0;
            }

            .header-logo img {
                height: 24px;
            }

            .header-search {
                flex: 1;
                margin: 0 !important;
                max-width: none !important;
            }

            .header-search input {
                height: 38px;
                font-size: 13px;
                padding: 5px 10px 5px 32px;
            }

            .header-search .search-icon {
                left: 10px;
                font-size: 12px;
            }

            .header-icons {
                flex: 0 0 auto;
                display: flex !important;
                gap: 10px;
                align-items: center;
            }

            .header-icons .icon-btn {
                font-size: 18px;
            }

            .header-icons img {
                width: 28px !important;
                height: 28px !important;
            }
        }
    </style>

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

        .submenu1>li:hover {
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

        <!-- Premium Header -->
        <header class="layout-app-header">
            <div class="header-logo">
                @if (!empty($logos))
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('uploads/logos/' . $logos->first()->logo) }}" alt="BookHub">
                    </a>
                @else
                    <h4 class="mb-0 fw-bold">BookHub</h4>
                @endif
            </div>
            <div class="category-menu-wrapper header-filters">
                <div class="category-trigger" id="categoryToggle">
                    <i class="fas fa-th-large"></i>
                    <span>Category</span>
                    <i class="fas fa-chevron-down arrow-icon" style="font-size: 10px; margin-left: 5px;"></i>
                </div>

                <div class="category-dropdown" id="categoryMenu">
                    <div class="mega-sidebar">
                        @foreach ($navFilterData as $section)
                            @php
                                $icon = 'fa-book';
                                if (str_contains(strtolower($section['name']), 'school')) {
                                    $icon = 'fa-school';
                                }
                                if (str_contains(strtolower($section['name']), 'college')) {
                                    $icon = 'fa-university';
                                }
                                if (str_contains(strtolower($section['name']), 'entrance')) {
                                    $icon = 'fa-graduation-cap';
                                }
                            @endphp
                            <div class="sidebar-item {{ $loop->first ? 'active' : '' }}"
                                data-pane="pane-{{ $loop->index }}">
                                <i class="fas {{ $icon }}"></i>
                                <span>{{ $section['name'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mega-content-area">
                        @foreach ($navFilterData as $section)
                            <div class="section-pane {{ $loop->first ? 'active' : '' }}"
                                id="pane-{{ $loop->index }}">
                                <div class="boards-grid">
                                    @foreach ($section['boards'] as $board)
                                        <div class="board-column">
                                            <div class="board-title">{{ $board['name'] }}</div>
                                            <div class="class-list">
                                                @foreach ($board['classes'] as $class)
                                                    <a href="{{ url('/search-products?section_id=' . $class['section_id'] . '&category_id=' . $class['category_id'] . '&subcategory_id=' . $class['id']) }}"
                                                        class="class-link">
                                                        {{ $class['name'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="header-search">
<input type="text" placeholder="Search books, authors, ISBN...">
<button><i class="fa fa-search"></i></button>
</div>
            <div class="header-icons">
                <a href="{{ url('/wishlist') }}" class="icon-btn desktop-only-icon">
                    <i class="far fa-heart"></i>
                    <span class="icon-badge-num totalWishlistItems">{{ $headerWishlistItemsCount ?? 0 }}</span>
                </a>
                <a href="{{ url('/cart') }}" class="icon-btn desktop-only-icon">
                    <i class="fas fa-shopping-basket"></i>
                    <span
                        class="icon-badge-num totalCartItems">{{ isset($headerCartItems) ? count($headerCartItems) : 0 }}</span>
                </a>
                @auth
                    <a href="#" class="icon-btn">
                        <i class="far fa-bell"></i>
                        <span class="badge-dot"></span>
                    </a>
                    <a href="{{ route('student.account') }}"
                        style="display: flex; align-items: center; text-decoration: none;">
                        <img src="{{ asset(Auth::user()->profile_image ?? 'assets/images/avatar.png') }}"
                            style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1.5px solid #F0F0F0;">
                        <span class="user-name-text">{{ Auth::user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('student.login') }}" class="icon-btn">
                        <i class="far fa-user-circle"></i>
                    </a>
                @endauth
            </div>
        </header>

        <div class="page-content bg-white">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="site-footer style-1">
            <!-- Footer Category -->
            {{-- <div class="footer-category">
                <div class="container">
                    <div class="category-toggle">
                        <a href="javascript:void(0);" class="toggle-btn">Books categories</a>
                        <div class="toggle-items row">
                            <div class="footer-col-book">
                                <ul>
                                    <li><a href="{{ url('/category-products') }}">All Books</a></li>
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
            </div> --}}
            <!-- Footer Category End -->

            <!-- Footer Top -->
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3 col-lg-12 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="widget widget_about">
                                <div class="footer-logo logo-white">
                                    @if (!empty($logos))
                                        <a href="{{ url('/') }}">
                                            <img src="{{ asset('uploads/logos/' . $logos->first()->logo) }}"
                                                alt="BookHub">
                                        </a>
                                    @endif

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
                                    <li>
                                        <a href="{{ url('/about') }}">About us</a>
                                    </li>

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
                                    {{-- <li><a href="#">Shop</a></li> --}}
                                </ul>
                            </div>
                        </div>
                        {{-- <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-4 wow fadeInUp" data-wow-delay="0.4s">
                            <div class="widget widget_services">
                                <h5 class="footer-title">Resources</h5>
                                <ul>
                                    <li><a href="services.html">Download</a></li>
                                    <li><a href="help-desk.html">Help Center</a></li>
                                    <li><a href="shop-cart.html">Shop Cart</a></li>
                                    <li><a href="{{ url('/user/login-register') }}">Login</a></li>
                                </ul>
                            </div>
                        </div> --}}
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
                                        <span>+91-9876543210</span>
                                    </li>
                                    <li>
                                        <i class="flaticon-email"></i>
                                        <span>support@mybookhub.in</span>
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
                            <p class="copyright-text">Bookhub Book Store Ecommerce Website - © 2026 All Rights
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

    <!-- Unified Bottom Shelf Nav -->
    <nav class="layout-bottom-shelf">
        <a href="{{ url('/') }}" class="layout-shelf-item {{ request()->is('/') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ url('/category-products') }}"
            class="layout-shelf-item {{ request()->is('category-products*') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>Category</span>
        </a>

        <div class="layout-fab-container">
            <a href="{{ Auth::check() ? route('student.index') : route('student.login') }}"
                class="layout-fab-button">
                <i class="fas fa-plus"></i>
            </a>
            <span class="layout-fab-label">Sell Old Book</span>
        </div>

        <a href="{{ url('/cart') }}" class="layout-shelf-item {{ request()->is('cart*') ? 'active' : '' }}"
            style="position:relative;">
            <i class="fas fa-shopping-basket" style="position: relative;">
                @if (isset($headerCartItems) && count($headerCartItems) > 0)
                    <span class="cart-tag">{{ count($headerCartItems) }}</span>
                @endif
            </i>
            <span>Cart</span>
        </a>
        <a href="{{ Auth::check() ? route('student.account') : route('student.login') }}"
            class="layout-shelf-item {{ request()->is('student/account*') ? 'active' : '' }}">
            <i class="far fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>
    <!-- Bottom Shelf Nav End -->

    {{-- Modals  --}}

    {{-- Login --}}
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="loginForm" method="POST" action="{{ route('student.login') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Email or Mobile -->
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="login" class="form-control"
                                placeholder="Enter 10-digit mobile" required>
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
                <form id="registerForm" method="POST" action="{{ route('student.register') }}">
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
                        <!-- Password -->
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <!-- Phone -->
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="number" name="phone" class="form-control" required>
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

    <!-- jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>



    <script>
        // Global search handler for the new premium unified header
        (function() {
            function goToSearch(term) {
                var base = "{{ url('/') }}";
                var url = base + '/search-products';
                if (term && term.trim().length) {
                    window.location.href = url + '?search=' + encodeURIComponent(term.trim());
                } else {
                    window.location.href = url;
                }
            }

            var globalSearchInput = document.getElementById('globalSearch');
            if (globalSearchInput) {
                globalSearchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        var term = this.value.trim();
                        if (term.length > 0) {
                            goToSearch(term);
                        }
                    }
                });
            }
        })();
    </script>
    <script>
        $(document).ready(function() {
            // Mega Menu Toggle
            $('#categoryToggle').on('click', function(e) {
                e.stopPropagation();
                $('#categoryMenu').toggleClass('active');
                $(this).find('.arrow-icon').toggleClass('open');
            });

            // Sidebar Switching Logic
            $('.sidebar-item').on('mouseenter click', function() {
                $('.sidebar-item').removeClass('active');
                $(this).addClass('active');

                var targetPane = $(this).data('pane');
                $('.section-pane').removeClass('active');
                $('#' + targetPane).addClass('active');
            });

            // Close on outside click
            $(document).on('click', function() {
                $('#categoryMenu').removeClass('active');
                $('#categoryToggle').find('.arrow-icon').removeClass('open');
            });

            $('#categoryMenu').on('click', function(e) {
                e.stopPropagation();
            });
        });
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
