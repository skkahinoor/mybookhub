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
@if ($logos && $logos->first() && $logos->first()->favicon)
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
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        .header-logo img {
            height: 35px;
            width: auto;
            object-fit: contain;
        }

        .header-search {
            display: flex;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            flex: 1;
            max-width: 450px;
            margin: 0 20px;
            border: 1.5px solid #F0F0F0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            height: 40px;
        }

        .header-search input {
            flex: 1;
            border: none;
            padding: 0 15px;
            outline: none;
            height: 100%;
            font-size: 14px;
        }

        .header-search button {
            background: #2f6fc2;
            color: white;
            border: none;
            padding: 0 16px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .2s;
            flex-shrink: 0;
        }

        .header-search button:hover {
            background: #235aa8;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
        }

        .header-icons .icon {
            position: relative;
            cursor: pointer;
        }

        .header-icons .badge {
            position: absolute;
            top: -6px;
            right: -8px;
            background: #ff6b00;
            color: white;
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 50%;
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

        .header-text-link {
            font-size: 13px;
            font-weight: 600;
            color: #4A4A4A;
            text-decoration: none !important;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .header-text-link:hover {
            color: var(--primary-orange);
        }

        @media (max-width: 991px) {

            .desktop-only-icon,
            .user-name-text,
            .header-filters,
            .header-text-link {
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
                padding: 5px 50px;
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
                max-width: 420px !important;
                flex: 1;
            }
        }

        /* Fix for footer when app nav is present */
        @media (max-width: 991px) {
            .site-footer {
                margin-bottom: 0;
            }

            .layout-app-header {
                padding: 10px 10px;
                height: 60px;
                gap: 8px;
            }

            .header-logo img {
                height: 24px;
            }

            .header-search {
                margin: 0 4px;
                height: 38px;
            }

            .header-search input {
                font-size: 13px;
                padding: 0 8px;
            }

            .header-search button {
                padding: 0 10px;
            }

            .header-icons {
                gap: 8px;
            }

            .header-icons .icon-btn {
                font-size: 18px;
            }
        }

        /* Support for very small screens */
        @media (max-width: 480px) {
            .layout-app-header {
                padding: 8px 10px;
                height: 60px;
                gap: 8px;
            }

            .header-logo img {
                height: 22px;
            }

            .header-search {
                height: 34px;
            }

            .header-search button {
                padding: 0 10px;
            }

            .header-icons {
                gap: 8px;
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

        /* Notification Dropdown Custom Styles */
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            display: none;
            z-index: 3001;
            border: 1px solid rgba(0,0,0,0.05);
            padding: 0;
            margin-top: 10px;
        }
        .notification-dropdown.active {
            display: block;
            animation: megaFadeIn 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        .notification-header {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notification-title {
            font-size: 14px;
            font-weight: 700;
            margin: 0;
        }
        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f8f8f8;
            display: flex;
            gap: 12px;
            text-decoration: none !important;
            transition: background 0.2s;
        }
        .notification-item:hover {
            background: #f9f9f9;
        }
        .notification-item.unread {
            background: #f0f7ff;
        }
        .notification-icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #eef5fd;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .notification-icon-wrap.unread {
            background: #2979ff;
            color: #fff;
        }
        .notification-content {
            flex: 1;
        }
        .notification-item-title {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 2px;
            display: block;
        }
        .notification-item-msg {
            font-size: 12px;
            color: #666;
            line-height: 1.4;
            display: block;
        }
        .notification-item-time {
            font-size: 11px;
            color: #999;
            margin-top: 4px;
            display: block;
        }
        .notification-footer {
            padding: 10px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        .notification-footer a {
            font-size: 12px;
            font-weight: 600;
            color: #2979ff;
            text-decoration: none;
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
                @php
                    $logo = $logos ? $logos->first() : null;
                @endphp

                @if ($logo && $logo->logo)
                    <img src="{{ asset('uploads/logos/' . $logo->logo) }}" alt="BookHub">
                @else
                    <h4>BookHub</h4>
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

            <form class="header-search" id="headerSearchForm" action="{{ url('/search-products') }}" method="GET"
                onsubmit="return handleHeaderSearch(event)">
                <input type="text" id="globalSearch" name="search" placeholder="Search books, authors, ISBN...">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
            <div class="header-icons">
                <a href="{{ url('vendor/login') }}" class="header-text-link desktop-only-icon">
                    Vendor Login
                </a>
                <a href="{{ Auth::check() ? route('student.sell-book.index') : route('student.login') }}" class="header-text-link desktop-only-icon">
                    Sell Book
                </a>
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
                    <div class="icon-btn" style="position: relative;">
                        <a href="javascript:void(0);" id="notificationToggleBtn"
                            style="color: inherit; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                            <i class="far fa-bell"></i>
                            @if (($navUnreadCount ?? 0) > 0)
                                <span class="icon-badge-num">{{ $navUnreadCount }}</span>
                            @endif
                        </a>

                        <div class="notification-dropdown" id="notificationDropdownMain">
                            <div class="notification-header">
                                <h6 class="notification-title">Notifications</h6>

                            </div>
                            <div class="notification-list">
                                @forelse(($navNotifications ?? []) as $n)
                                    @php
                                        $isUnread = !((bool) ($n->is_read ?? false));
                                    @endphp
                                    <a href="#"
                                        class="notification-item js-front-notification {{ $isUnread ? 'unread' : '' }}"
                                        data-id="{{ $n->id }}" data-read="{{ $isUnread ? '0' : '1' }}">
                                        <div class="notification-icon-wrap {{ $isUnread ? 'unread' : '' }}">
                                            <i class="fas {{ $isUnread ? 'fa-bell' : 'fa-check' }}"></i>
                                        </div>
                                        <div class="notification-content">
                                            <span class="notification-item-title">{{ $n->title }}</span>
                                            <span
                                                class="notification-item-msg">{{ \Illuminate\Support\Str::limit($n->message, 50) }}</span>
                                            <span class="notification-item-time">{{ $n->created_at?->diffForHumans() }}</span>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-muted" style="font-size: 13px;">
                                        No notifications yet.
                                    </div>
                                @endforelse
                            </div>
                            <div class="notification-footer">
                                <a href="{{ route('student.notifications.index') }}">View All Notifications</a>
                            </div>
                        </div>
                    </div>
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
                                    @php
                                        $logo = $logos ? $logos->first() : null;
                                    @endphp

                                    @if ($logo && $logo->logo)
                                        <a href="{{ url('/') }}">
                                            <img src="{{ asset('uploads/logos/' . $logo->logo) }}" alt="BookHub">
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
                <form id="registerForm" method="POST" action="{{ route('student.registerstore') }}">
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
        function handleHeaderSearch(e) {
            e.preventDefault();
            var input = document.getElementById('globalSearch');
            var term = input ? input.value.trim() : '';
            var base = "{{ url('/') }}";
            var url = base + '/search-products';
            if (term.length > 0) {
                window.location.href = url + '?search=' + encodeURIComponent(term);
            } else {
                window.location.href = url;
            }
            return false;
        }
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

    {{-- ✨ BookGenie AI Floating Widget --}}
    <style>
        /* BookGenie FAB Button */
        .bg-fab {
            position: fixed;
            bottom: 60px;
            left: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            box-shadow: 0 8px 28px rgba(59, 130, 246, 0.45);
            cursor: pointer;
            z-index: 99990;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: bg-pulse 2.5s infinite;
        }

        .bg-fab:hover {
            transform: scale(1.12);
            box-shadow: 0 12px 36px rgba(59, 130, 246, 0.6);
        }

        @keyframes bg-pulse {

            0%,
            100% {
                box-shadow: 0 8px 28px rgba(59, 130, 246, 0.45);
            }

            50% {
                box-shadow: 0 8px 40px rgba(59, 130, 246, 0.75);
            }
        }

        .bg-fab-img {
            width: 32px;
            height: 32px;
            font-size: 22px;
            line-height: 1;
        }

        .bg-fab-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 18px;
            height: 18px;
            background: #ff6b00;
            border-radius: 50%;
            border: 2px solid #fff;
            font-size: 10px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* BookGenie Panel */
        .bg-panel {
            position: fixed;
            bottom: 150px;
            left: 20px;
            width: 340px;
            max-height: 480px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
            z-index: 99991;
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: slideUpPanel 0.3s ease;
        }

        .bg-panel.open {
            display: flex;
        }

        @keyframes slideUpPanel {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bg-panel-header {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .bg-panel-header-icon {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .bg-panel-header-text h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: white;
        }

        .bg-panel-header-text p {
            margin: 0;
            font-size: 11px;
            opacity: 0.85;
        }

        .bg-panel-close {
            margin-left: auto;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            transition: background 0.2s;
            flex-shrink: 0;
        }

        .bg-panel-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .bg-panel-body {
            padding: 18px;
            overflow-y: auto;
            flex: 1;
        }

        .bg-msg.bot {
            background: #f0f4ff;
            color: #1e3a8a;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.05);
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        .bg-msg.user {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bg-typing-dots {
            display: inline-flex;
            gap: 4px;
            padding: 4px 8px;
        }

        .bg-typing-dots span {
            width: 6px;
            height: 6px;
            background: #3b82f6;
            border-radius: 50%;
            animation: bgTyping 1s infinite alternate;
        }

        .bg-typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .bg-typing-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes bgTyping {
            from { opacity: 0.3; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1.2); }
        }

        .bg-quick-actions {
            display: flex;
            gap: 12px;
            padding: 15px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }

        .bg-quick-actions .bg-action-btn {
            margin: 0;
            flex: 1;
            font-size: 11px;
            padding: 10px 5px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bg-chips-scroll {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 10px 15px;
            scrollbar-width: none;
            -ms-overflow-style: none;
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
        }
        .bg-chips-scroll::-webkit-scrollbar { display: none; }

        .bg-chip {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            color: #1e3a8a;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .bg-chip:hover {
            border-color: #3b82f6;
            background: #f0f7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .bg-panel-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 18px 20px;
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
        }

        .bg-action-btn {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: none;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .bg-action-btn.primary {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
        }

        .bg-action-btn.primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .bg-action-btn.secondary {
            background: #ffffff;
            color: #1e3a8a;
            border: 1.5px solid #e2e8f0;
        }

        .bg-action-btn.secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .bg-personalize-form {
            display: none;
        }

        .bg-personalize-form.open {
            display: block;
        }

        .bg-form-group {
            margin-bottom: 12px;
        }

        .bg-form-group label {
            font-size: 12px;
            font-weight: 700;
            color: #555;
            margin-bottom: 4px;
            display: block;
        }

        .bg-form-group select {
            width: 100%;
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 9px 12px;
            font-size: 13px;
            color: #333;
            background: #f8faff;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.2s;
        }

        .bg-form-group select:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .bg-current-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #eef3ff;
            border: 1px solid #c7d9ff;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 12px;
            color: #1e3a8a;
            font-weight: 600;
            margin: 2px;
        }

        /* Chat Styles */
        .bg-messages {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 15px;
        }

        .bg-msg {
            max-width: 85%;
            padding: 10px 14px;
            font-size: 13px;
            line-height: 1.5;
            border-radius: 14px;
        }

        .bg-msg.bot {
            background: #f0f4ff;
            color: #1e3a8a;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        .bg-msg.user {
            background: #1e3a8a;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .bg-inline-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .bg-chip-mini {
            background: #fff;
            border: 1px solid #3b82f6;
            color: #3b82f6;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .bg-chip-mini:hover {
            background: #3b82f6;
            color: white;
        }

        .bg-results-scroll {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 5px 0 10px;
            margin-top: 8px;
            scrollbar-width: thin;
        }

        .bg-product-card {
            flex: 0 0 160px;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 10px;
            text-decoration: none !important;
            color: inherit !important;
            transition: transform 0.2s;
        }

        .bg-product-card:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
        }

        .bg-product-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .bg-product-name {
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 30px;
        }

        .bg-product-price {
            color: #ff6b00;
            font-weight: 700;
            font-size: 13px;
        }

        .bg-product-vendor {
            font-size: 10px;
            color: #888;
            margin-top: 4px;
        }

        .bg-product-distance {
            font-size: 10px;
            color: #3b82f6;
            font-weight: 600;
        }

        .bg-chat-footer {
            padding: 12px 18px;
            border-top: 1px solid #eee;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bg-chat-input-wrapper {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
        }

        .bg-chat-input {
            width: 100%;
            border: 2px solid #f0f0f0;
            border-radius: 25px;
            padding: 10px 45px 10px 15px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            background: #f9fafb;
        }

        .bg-chat-input:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .bg-voice-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s;
        }

        .bg-voice-btn:hover {
            color: #3b82f6;
        }

        .bg-chat-send {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(30, 58, 138, 0.2);
        }

        .bg-chat-send:hover {
            transform: scale(1.05) rotate(-10deg);
            box-shadow: 0 6px 15px rgba(30, 58, 138, 0.3);
        }

        /* Mobile adjustments */
        @media (max-width: 480px) {
            .bg-panel {
                left: 10px;
                right: 10px;
                width: auto;
                bottom: 140px;
            }

            .bg-fab {
                bottom: 60px;
                left: 16px;
            }
        }
    </style>

    <!-- BookGenie Floating Button -->
    <button class="bg-fab" id="bgFab" title="BookGenie AI">
        <span class="bg-fab-img">✨</span>
        <span class="bg-fab-badge" id="bgFabBadge" style="display:none;">!</span>
    </button>

    <!-- BookGenie Panel -->
    <div class="bg-panel" id="bgPanel">
        <div class="bg-panel-header">
            <div class="bg-panel-header-icon">✨</div>
            <div class="bg-panel-header-text">
                <h5 style="margin:0; font-size:16px; font-weight:700;">BookGenie AI</h5>
                <p style="margin:0; font-size:11px; opacity:0.8;">Your personal helper</p>
            </div>
            <button class="bg-panel-close" id="bgPanelClose" style="background:none; border:none; color:white; font-size:18px; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <!-- Main Actions -->
        <div class="bg-quick-actions">
             <button class="bg-action-btn primary" style="margin:0; flex:1; font-size:12px; padding: 10px;" onclick="document.dispatchEvent(new CustomEvent('bgOpenForm'))">
                <i class="fas fa-magic"></i> Personalize Feed
             </button>
             <a href="{{ url('student/sell-book') }}" class="bg-action-btn secondary" style="margin:0; flex:1; font-size:12px; padding: 10px; text-decoration:none;">
                <i class="fas fa-plus-circle"></i> Sell My Book
             </a>
        </div>

        <div class="bg-chips-scroll" id="bgChips">
            <div class="bg-chip" data-query="explore_sections">🔍 Browse by Board</div>
            @php
                $trendingCats = \App\Models\Category::where('status', 1)->limit(5)->get();
            @endphp
            @foreach($trendingCats as $tc)
                <div class="bg-chip" data-query="{{ $tc->category_name }}">📙 {{ $tc->category_name }}</div>
            @endforeach
        </div>

        <div class="bg-panel-body" id="bgPanelBody" style="position: relative; height: 320px; overflow-y: auto;">
            <div class="bg-messages" id="bgChatMessages" style="padding: 0 15px 15px;">
                <!-- Messages will be injected here via JS -->
            </div>
            <!-- Personalization form (overlay hidden by default) -->
            <div class="bg-personalize-form" id="bgPersonalizeForm"
                style="display:none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: white; z-index: 100; padding: 20px; border-radius: 20px;">
                <h6
                    style="font-weight: 700; margin-bottom: 20px; color: #1e3a8a; display: flex; justify-content: space-between; align-items: center;">
                    <span>✨ Personalize</span>
                    <button id="bgWidgetCancel"
                        style="border: none; background: none; color: #888; cursor: pointer;"><i
                            class="fas fa-times"></i></button>
                </h6>
                <div class="bg-form-group">
                    <label>Education Level (Section)</label>
                    <select id="bgWidgetSection">
                        <option value="">Select Education Level</option>
                        @foreach (\App\Models\Section::all() as $sec)
                            <option value="{{ $sec->id }}"
                                {{ session('bg_section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-form-group">
                    <label>Board / Domain (Category)</label>
                    <select id="bgWidgetCategory">
                        <option value="">Select Board</option>
                    </select>
                </div>
                <div class="bg-form-group">
                    <label>Class / Stream</label>
                    <select id="bgWidgetSubcategory">
                        <option value="">Select Class</option>
                </div>
                <div class="bg-form-group">
                    <label>Subject</label>
                    <select id="bgWidgetSubject">
                        <option value="">Select Subject</option>
                    </select>
                </div>
                <div style="margin-top: 20px;">
                    <button class="bg-action-btn primary" id="bgWidgetApply" style="margin:0; width: 100%;">
                        <i class="fas fa-check"></i> Apply Preferences
                    </button>
                </div>
            </div>
        </div>

        <!-- Chat Footer -->
        <div class="bg-chat-footer">
            <div class="bg-chat-input-wrapper">
                <input type="text" class="bg-chat-input" id="bgChatInput" placeholder="Ask me anything...">
                <button class="bg-voice-btn" id="bgVoiceBtn" title="Voice Search"><i class="fas fa-microphone"></i></button>
            </div>
            <button class="bg-chat-send" id="bgChatSend">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <script>
        (function() {
            const fab = document.getElementById('bgFab');
            const panel = document.getElementById('bgPanel');
            const closeBtn = document.getElementById('bgPanelClose');
            const personalizeForm = document.getElementById('bgPersonalizeForm');
            const cancelBtn = document.getElementById('bgWidgetCancel');
            const applyBtn = document.getElementById('bgWidgetApply');
            const chatInput = document.getElementById('bgChatInput');
            const chatSend = document.getElementById('bgChatSend');
            const chatMessages = document.getElementById('bgChatMessages');
            const panelBody = document.getElementById('bgPanelBody');

            const wSection = document.getElementById('bgWidgetSection');
            const wCategory = document.getElementById('bgWidgetCategory');
            const wSubcategory = document.getElementById('bgWidgetSubcategory');
            const wSubject = document.getElementById('bgWidgetSubject');

            // --- UI Controls ---
            if (fab && panel) {
                fab.addEventListener('click', (e) => {
                    e.stopPropagation();
                    panel.classList.toggle('open');
                    if (panel.classList.contains('open') && chatMessages && chatMessages.children.length === 0) {
                        initChat();
                    }
                });

                document.addEventListener('click', (e) => {
                    if (panel.classList.contains('open') && !panel.contains(e.target) && !fab.contains(e.target)) {
                        panel.classList.remove('open');
                    }
                });
            }

            if (closeBtn && panel) {
                closeBtn.addEventListener('click', () => panel.classList.remove('open'));
            }

            function showBotTyping() {
                const typingDiv = document.createElement('div');
                typingDiv.className = 'bg-msg bot typing-indicator';
                typingDiv.id = 'bgBotTyping';
                typingDiv.innerHTML = `<div class="bg-typing-dots"><span></span><span></span><span></span></div>`;
                chatMessages.appendChild(typingDiv);
                panelBody.scrollTop = panelBody.scrollHeight;
            }

            function hideBotTyping() {
                const indicator = document.getElementById('bgBotTyping');
                if (indicator) indicator.remove();
            }

            function initChat() {
                const name = '@auth {{ Auth::user()->name }} @else friend @endauth';
                showBotTyping();
                setTimeout(() => {
                    hideBotTyping();
                    const welcomeMsg = `👋 Hi <strong>${name.trim()}</strong>! I'm BookGenie. I can help you find any book, personalize your feed, or even help you sell your old ones. What's on your mind?`;
                    appendMessage('bot', welcomeMsg);

                    // Show WhatsApp option
                    setTimeout(() => {
                        const waMsg = `Need urgent help? You can also <a href="https://wa.me/91XXXXXXXXXX" target="_blank" style="color:#25D366; font-weight:700; text-decoration:none;"><i class="fab fa-whatsapp"></i> Chat with us on WhatsApp</a>`;
                        appendMessage('bot', waMsg);
                    }, 500);
                }, 1000);
            }

            document.addEventListener('bgOpenForm', () => {
                personalizeForm.style.display = 'block';
            });

            cancelBtn.addEventListener('click', () => personalizeForm.style.display = 'none');

            function appendMessage(role, content, results = null) {
                const msgDiv = document.createElement('div');
                msgDiv.className = `bg-msg ${role}`;
                msgDiv.style.marginBottom = '12px';
                msgDiv.innerHTML = content;
                chatMessages.appendChild(msgDiv);

                if (results && results.length > 0) {
                    const scrollDiv = document.createElement('div');
                    scrollDiv.className = 'bg-results-scroll';
                    results.forEach(item => {
                        const card = `
                                <a href="${item.url}" class="bg-product-card">
                                    <img src="${item.image || '/front/images/product_images/large/no-image.png'}" class="bg-product-img">
                                    <div class="bg-product-name">${item.name}</div>
                                    <div class="bg-product-price">${item.price}</div>
                                    <div class="bg-product-vendor">${item.shop}</div>
                                    ${item.distance ? `<div class="bg-product-distance"><i class="fas fa-map-marker-alt"></i> ${item.distance}</div>` : ''}
                                </a>
                            `;
                        scrollDiv.innerHTML += card;
                    });
                    chatMessages.appendChild(scrollDiv);

                    // Add Apply button
                    if (window.lastBgFilters) {
                        const actionDiv = document.createElement('div');
                        actionDiv.className = 'bg-bot-actions';
                        actionDiv.innerHTML = `<button class="bg-apply-main-btn" onclick="window.applyBgFilters()">✨ View all on Main Screen</button>`;
                        chatMessages.appendChild(actionDiv);
                    }
                }
                panelBody.scrollTop = panelBody.scrollHeight;
            }

            function handleChatSearch(input = null) {
                let query = '';
                let extraParams = '';
                let activeFilters = {};

                if (typeof input === 'string') {
                    query = input;
                } else if (input && typeof input === 'object' && input.q !== undefined) {
                    query = input.q;
                    if (input.filters) {
                        activeFilters = input.filters;
                        Object.keys(input.filters).forEach(k => {
                            extraParams += `&${k}=${input.filters[k]}`;
                        });
                    }
                    // For filter selections, the message already exists or we want to show the selected name
                    appendMessage('user', query);
                } else {
                    query = chatInput.value.trim();
                }

                if (!query && !extraParams) return;
                if (query === '[object PointerEvent]' || query === '[object KeyboardEvent]') return;

                if (typeof input !== 'string' && (!input || input.q === undefined)) {
                    appendMessage('user', query);
                    chatInput.value = '';
                }

                window.lastBgFilters = activeFilters;

                // If filters are present, we don't need text search for the subject name
                let actualSearchQuery = Object.keys(activeFilters).length > 0 ? '' : query;

                showBotTyping();
                fetch(`{{ url('bookgenie-search') }}?q=${encodeURIComponent(actualSearchQuery)}${extraParams}`)
                    .then(r => r.json())
                    .then(data => {
                        hideBotTyping();
                        if (data.results && data.results.length > 0) {
                            appendMessage('bot', `I found some matches for your selection:`, data.results);
                        } else {
                            appendMessage('bot', `I couldn't find a direct match for this combination. Try exploring another class or subject.`);
                        }
                    })
                    .catch(() => {
                        hideBotTyping();
                        appendMessage('bot', "Connection error. Please try again.");
                    });
            }

            window.applyBgFilters = function() {
                if (!window.lastBgFilters) return;

                const params = new URLSearchParams(window.lastBgFilters);
                params.append('bookgenie_shown', 'true');

                fetch(`{{ url('set-bookgenie-session') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(window.lastBgFilters)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Close panel and reload page to reflect filters on main screen
                        panel.classList.remove('open');
                        window.location.reload();
                    }
                });
            };

            window.bgFlow = function(type, id, name) {
                appendMessage('user', name);
                showBotTyping();

                let url = '';
                if (type === 'category') {
                    currentFlowFilters = { section_id: id };
                    url = `{{ url('get-filter-categories') }}?section_id=${id}`;
                } else if (type === 'subcategory') {
                    currentFlowFilters.category_id = id;
                    url = `{{ url('get-filter-subcategories') }}?section_id=${currentFlowFilters.section_id}&category_id=${id}`;
                } else if (type === 'subject') {
                    currentFlowFilters.subcategory_id = id;
                    url = `{{ url('get-filter-subjects') }}?section_id=${currentFlowFilters.section_id}&category_id=${currentFlowFilters.category_id}&subcategory_id=${id}`;
                }

                fetch(url)
                    .then(r => r.json())
                    .then(data => {
                        hideBotTyping();

                        let nextType = '';
                        let prompt = '';
                        if (type === 'category') { nextType = 'subcategory'; prompt = 'Awesome! Now select your Board:'; }
                        else if (type === 'subcategory') { nextType = 'subject'; prompt = 'Almost there! Which Class?'; }
                        else if (type === 'subject') { nextType = 'finish'; prompt = 'Finally, select your Subject:'; }

                        if (data.length > 0) {
                            let html = `<p>${prompt}</p><div class="bg-inline-chips">`;
                            data.forEach(item => {
                                const itemName = item.category_name || item.name;
                                if (nextType === 'finish') {
                                    const filters = {
                                        section_id: currentFlowFilters.section_id,
                                        category_id: currentFlowFilters.category_id,
                                        subcategory_id: currentFlowFilters.subcategory_id,
                                        subject_id: item.id
                                    };
                                    // Use single quotes for attribute and double quotes for JSON, escaping double quotes for compatibility
                                    const filterStr = JSON.stringify(filters).replace(/"/g, '&quot;');
                                    html += `<div class="bg-chip-mini" onclick="handleChatSearch({q: '${itemName.replace(/'/g, "\\'")}', filters: ${filterStr}})">${itemName}</div>`;
                                } else {
                                    html += `<div class="bg-chip-mini" onclick="window.bgFlow('${nextType}', ${item.id}, '${itemName.replace(/'/g, "\\")}')">${itemName}</div>`;
                                }
                            });
                            html += '</div>';
                            appendMessage('bot', html);
                        } else {
                           if (type === 'subcategory') {
                                // If no subcategories found for Category, search by category
                                appendMessage('bot', `I didn't find specific classes for this board, but I can show you all books in ${name}:`);
                                handleChatSearch({
                                    q: name,
                                    filters: { section_id: currentFlowFilters.section_id, category_id: id }
                                });
                           } else if (type === 'subject') {
                                // If no subjects found for Class, search by class
                                appendMessage('bot', `I didn't find specific subjects for ${name}, but I found these books for you:`);
                                handleChatSearch({
                                    q: name,
                                    filters: {
                                        section_id: currentFlowFilters.section_id,
                                        category_id: currentFlowFilters.category_id,
                                        subcategory_id: id
                                    }
                                });
                           } else {
                                appendMessage('bot', "No options found for this selection. Try searching manually or browsing another board.");
                           }
                        }
                    })
                    .catch(err => {
                        console.error('Flow error:', err);
                        hideBotTyping();
                        appendMessage('bot', "Oops! Something went wrong while loading options. Please try again.");
                    });
            };

            // Chips click handling
            document.querySelectorAll('.bg-chip').forEach(chip => {
                chip.addEventListener('click', () => {
                    const q = chip.dataset.query;
                    appendMessage('user', q);
                    handleChatSearch(q);
                });
            });

            // --- Voice Search Flow ---
            const voiceBtn = document.getElementById('bgVoiceBtn');
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (voiceBtn && SpeechRecognition) {
                const recognition = new SpeechRecognition();
                recognition.lang = 'en-IN';
                recognition.interimResults = false;
                recognition.maxAlternatives = 1;

                voiceBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    if (voiceBtn.classList.contains('listening')) {
                        recognition.stop();
                    } else {
                        // Small delay to ensure browser handles the interaction appropriately
                        setTimeout(() => {
                            try {
                                recognition.start();
                            } catch (err) {
                                console.error('Voice recovery:', err);
                                // If already started, just stop and restart
                                recognition.stop();
                                setTimeout(() => recognition.start(), 100);
                            }
                        }, 50);
                    }
                });

                recognition.onstart = () => {
                    voiceBtn.classList.add('listening');
                    chatInput.placeholder = "Listening... speak now";
                    chatInput.style.borderColor = '#3b82f6';
                    chatInput.style.boxShadow = '0 0 12px rgba(59, 130, 246, 0.4)';
                    // Help the user notice the permission prompt
                    if (!localStorage.getItem('bgVoiceHintShown')) {
                        appendMessage('bot', "Please click 'Allow' if your browser asks for microphone access. 🎤");
                        localStorage.setItem('bgVoiceHintShown', 'true');
                    }
                };

                recognition.onresult = (event) => {
                    const transcript = event.results[0][0].transcript;
                    if (transcript) {
                        chatInput.value = transcript;
                        appendMessage('user', transcript);
                        handleChatSearch(transcript);
                    }
                };

                recognition.onend = () => {
                    voiceBtn.classList.remove('listening');
                    chatInput.placeholder = "Ask me anything...";
                    chatInput.style.borderColor = '';
                    chatInput.style.boxShadow = '';
                };

                recognition.onerror = (event) => {
                    voiceBtn.classList.remove('listening');
                    if (event.error === 'no-speech') return; // Ignore silent timeouts

                    let errorMsg = "Sorry, I couldn't hear you.";
                    if (event.error === 'not-allowed') {
                        errorMsg = "🎤 Mic access blocked! Please click the icon in your address bar to allow microphone access.";
                    } else if (event.error === 'network') {
                        errorMsg = "Network error. Please check your connection.";
                    }

                    appendMessage('bot', errorMsg);
                };
            } else if (voiceBtn) {
                voiceBtn.addEventListener('click', () => {
                   appendMessage('bot', "Voice search is not supported in this browser. Please use Chrome or Edge for the best experience.");
                });
            }

            if (chatSend) {
                chatSend.addEventListener('click', () => handleChatSearch());
            }
            if (chatInput) {
                chatInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') handleChatSearch();
                });
            }

            // --- Form Cascades ---
            wSection.addEventListener('change', function() {
                const secId = this.value;
                if (secId) {
                    fetch(`{{ url('get-filter-categories') }}?section_id=${secId}`)
                        .then(r => r.json())
                        .then(data => {
                            wCategory.innerHTML = '<option value="">Select Board</option>';
                            data.forEach(c => wCategory.innerHTML +=
                                `<option value="${c.id}">${c.category_name}</option>`);
                        });
                }
            });
            wCategory.addEventListener('change', function() {
                const catId = this.value;
                const secId = wSection.value;
                if (catId) {
                    fetch(`{{ url('get-filter-subcategories') }}?category_id=${catId}&section_id=${secId}`)
                        .then(r => r.json())
                        .then(data => {
                            wSubcategory.innerHTML = '<option value="">Select Class</option>';
                            data.forEach(s => wSubcategory.innerHTML +=
                                `<option value="${s.id}">${s.category_name}</option>`);
                        });
                }
            });

            wSubcategory.addEventListener('change', function() {
                const subId = this.value;
                const catId = wCategory.value;
                const secId = wSection.value;
                if (subId) {
                    fetch(`{{ url('get-filter-subjects') }}?subcategory_id=${subId}&category_id=${catId}&section_id=${secId}`)
                        .then(r => r.json())
                        .then(data => {
                            wSubject.innerHTML = '<option value="">Select Subject</option>';
                            data.forEach(s => wSubject.innerHTML +=
                                `<option value="${s.id}">${s.category_name}</option>`);
                        });
                }
            });

            applyBtn.addEventListener('click', function() {
                const secId = wSection.value;
                const catId = wCategory.value;
                const subId = wSubcategory.value;
                const subjId = wSubject.value;

                if (!secId) {
                    alert('Please select at least an Education Level.');
                    return;
                }

                // Hide the form
                personalizeForm.style.display = 'none';

                let filters = { section_id: secId };
                let names = [wSection.options[wSection.selectedIndex].text];

                if (catId) {
                    filters.category_id = catId;
                    names.push(wCategory.options[wCategory.selectedIndex].text);
                }
                if (subId) {
                    filters.subcategory_id = subId;
                    names.push(wSubcategory.options[wSubcategory.selectedIndex].text);
                }
                if (subjId) {
                    filters.subject_id = subjId;
                    names.push(wSubject.options[wSubject.selectedIndex].text);
                }

                const queryName = names.join(', ');

                // Show in chat
                document.getElementById('bgChatInput').value = '';
                handleChatSearch({
                    q: queryName,
                    filters: filters
                });
            });
        })();

        // Notification Toggle
        (function() {
            const notificationToggleBtn = document.getElementById('notificationToggleBtn');
            const notificationDropdownMain = document.getElementById('notificationDropdownMain');
            const categoryMenu = document.getElementById('categoryMenu');

            if (notificationToggleBtn && notificationDropdownMain) {
                notificationToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    notificationDropdownMain.classList.toggle('active');
                    // Hide other dropdowns if any
                    if (categoryMenu) categoryMenu.classList.remove('active');
                });

                document.addEventListener('click', function(e) {
                    if (!notificationDropdownMain.contains(e.target) && e.target !== notificationToggleBtn) {
                        notificationDropdownMain.classList.remove('active');
                    }
                });
            }

            // Mark as read front-end
            var items = document.querySelectorAll('.js-front-notification');
            if (items.length) {
                items.forEach(function(el) {
                    el.addEventListener('click', function(e) {
                        e.preventDefault();
                        var id = el.getAttribute('data-id');
                        var isRead = el.getAttribute('data-read') === '1';
                        if (isRead || !id) return;

                        fetch("{{ url('/student/notifications') }}/" + id + "/read", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                "Accept": "application/json"
                            }
                        }).then(function() {
                            el.classList.remove('unread');
                            var iconWrap = el.querySelector('.notification-icon-wrap');
                            if (iconWrap) {
                                iconWrap.classList.remove('unread');
                                var icon = iconWrap.querySelector('i');
                                if (icon) icon.className = 'fas fa-check';
                            }
                            el.setAttribute('data-read', '1');

                            var badge = document.querySelector(
                                '#notificationToggleBtn .icon-badge-num');
                            if (badge) {
                                var n = parseInt(badge.textContent || '0', 10);
                                n = isNaN(n) ? 0 : Math.max(0, n - 1);
                                if (n === 0) badge.remove();
                                else badge.textContent = String(n);
                            }

                            var headerBadge = document.querySelector(
                                '.notification-header .badge');
                            if (headerBadge) {
                                var n = parseInt(headerBadge.textContent || '0', 10);
                                n = isNaN(n) ? 0 : Math.max(0, n - 1);
                                if (n === 0) headerBadge.remove();
                                else headerBadge.textContent = n + ' New';
                            }
                        }).catch(function() {});
                    });
                });
            }
        })();
    </script>

    @yield('scripts')
</body>

</html>
