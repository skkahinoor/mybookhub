@extends('front.layout.layout3')

@section('content')
    <style>
        /* Modern Design System */
        :root {
            --primary-orange: #FF6B00;
            --primary-blue: #2979FF;
            --bg-light: #F8F9FB;
            --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
            --header-bg: #FFFFFF;
            --text-dark: #1A1A1A;
            --text-muted: #8E8E93;
        }

        /* Responsive Layout Logic */
        body {
            background-color: var(--bg-light) !important;
            font-family: 'Poppins', sans-serif !important;
            padding-bottom: 90px !important;
            /* Mobile padding for bottom nav */
        }

        .page-content {
            background-color: var(--bg-light) !important;
            padding-top: 0 !important;
        }

        /* Desktop View (PC) */
        @media (min-width: 992px) {
            body {
                padding-bottom: 0 !important;
            }

            .app-header,
            .bottom-shelf {
                display: none !important;
            }

            /* Standard layout headers/footers should be visible on PC */
            .site-header,
            .site-footer,
            .footer-top,
            .footer-bottom,
            .scroltop {
                display: block !important;
            }

            /* REMOVE / HIDE the sticky sub-header bar on PC (Home, Condition, Language) */
            .sticky-header,
            .main-bar-wraper {
                display: none !important;
            }

            /* OPTIONAL: Refine the main header bar for a cleaner look */
            .header-info-bar {
                padding: 15px 0 !important;
                border-bottom: 1px solid #f0f0f0;
            }
        }

        /* Mobile View */
        @media (max-width: 991px) {

            .site-header,
            .site-footer,
            .footer-top,
            .footer-bottom,
            .scroltop {
                display: none !important;
            }
        }

        /* Custom App Header (Mobile Only) */
        .app-header {
            background: #fff;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
        }

        .header-logo img {
            height: 32px;
            object-fit: contain;
        }

        .header-search {
            flex: 1;
            margin: 0 15px;
            position: relative;
        }

        .header-search input {
            width: 100%;
            background: #F2F2F7;
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 10px 15px 10px 42px;
            font-size: 14px;
            color: var(--text-dark);
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
            font-size: 16px;
        }

        .header-icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .header-icons .icon-btn {
            position: relative;
            color: var(--text-dark);
            font-size: 20px;
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

        /* Premium Banner Slider */
        .banner-container {
            padding: 15px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .premium-banner {
            border-radius: 24px;
            overflow: hidden;
            background: linear-gradient(135deg, #3A8DFF 0%, #1E70E0 100%);
            color: white;
            position: relative;
            min-height: 180px;
            display: flex;
            align-items: center;
        }

        @media (min-width: 992px) {
            .premium-banner {
                min-height: 350px;
            }

            .banner-title {
                font-size: 48px !important;
            }

            .banner-subtitle {
                font-size: 20px !important;
            }

            .banner-content {
                padding: 80px !important;
            }
        }

        .banner-content {
            padding: 25px;
            width: 65%;
            position: relative;
            z-index: 2;
        }

        .banner-title {
            font-weight: 800;
            font-size: 24px;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .banner-subtitle {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 20px;
            font-weight: 400;
        }

        .banner-btn {
            background: white;
            color: #1E70E0;
            padding: 10px 28px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            display: inline-block;
            text-decoration: none !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .banner-illustration {
            position: absolute;
            right: 0px;
            bottom: -5px;
            width: 45%;
            height: 110%;
            object-fit: contain;
            z-index: 1;
        }

        /* Info Bar */
        .selection-bar {
            padding: 5px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .selection-info {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .btn-change {
            color: var(--primary-orange);
            font-weight: 700;
            font-size: 14px;
            text-decoration: none !important;
        }

        /* Categories */
        .category-wrapper {
            margin-bottom: 25px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .category-scroll {
            display: flex;
            overflow-x: auto;
            padding: 5px 20px 10px;
            gap: 18px;
            scrollbar-width: none;
        }

        @media (min-width: 992px) {
            .category-scroll {
                justify-content: center;
                gap: 45px;
                padding: 30px;
            }

            .category-icon {
                width: 90px !important;
                height: 90px !important;
            }

            .category-label {
                font-size: 14px !important;
                margin-top: 5px;
            }
        }

        .category-scroll::-webkit-scrollbar {
            display: none;
        }

        .category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 68px;
            text-decoration: none !important;
        }

        .category-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 2px solid #fff;
        }

        .category-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .category-item.active .category-icon {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 2px var(--primary-orange);
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        .category-item.active .category-label {
            color: var(--primary-orange);
        }

        .category-label {
            font-size: 12px;
            color: #444;
            font-weight: 600;
        }

        /* Filter Chips */
        .tab-filters {
            padding: 0 20px;
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            overflow-x: auto;
            scrollbar-width: none;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        @media (min-width: 992px) {
            .tab-filters {
                justify-content: center;
                gap: 15px;
                margin-bottom: 40px;
            }
        }

        .tab-filters::-webkit-scrollbar {
            display: none;
        }

        .filter-chip {
            background: #fff;
            border: 1px solid #EDEDED;
            padding: 10px 24px;
            border-radius: 14px;
            font-size: 14px;
            color: var(--text-dark);
            font-weight: 600;
            text-decoration: none !important;
            white-space: nowrap;
        }

        .filter-chip.active {
            background: var(--primary-orange);
            color: #fff;
            border-color: var(--primary-orange);
            box-shadow: 0 8px 20px rgba(255, 107, 0, 0.25);
        }

        .filter-chip.icon-chip {
            padding-left: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Product Grid */
        .book-grid {
            padding: 0 18px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .book-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 992px) {
            .book-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 24px;
            }
        }

        @media (min-width: 1400px) {
            .book-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        .book-card-v2 {
            background: #fff;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .book-card-v2:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }

        .book-thumb {
            position: relative;
            padding-top: 105%;
            background: #F2F2F7;
        }

        .book-thumb img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .condition-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            color: #fff;
            text-transform: uppercase;
        }

        .badge-new {
            background: #34C759;
        }

        .badge-used {
            background: #007AFF;
        }

        .btn-quick-add {
            position: absolute;
            bottom: -20px;
            right: 15px;
            width: 40px;
            height: 40px;
            background: var(--primary-orange);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            box-shadow: 0 6px 15px rgba(255, 107, 0, 0.4);
            border: 3px solid #fff;
            z-index: 10;
            cursor: pointer;
        }

        .book-info {
            padding: 28px 15px 18px;
        }

        .book-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 2px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.35;
            min-height: 38px;
        }

        .book-category {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 10px;
            font-weight: 500;
        }

        .book-price-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .current-price {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-dark);
        }

        /* Bottom Nav (Mobile Only) */
        .bottom-shelf {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            display: flex;
            justify-content: space-around;
            padding: 12px 0 28px;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            border-radius: 28px 28px 0 0;
        }

        .shelf-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none !important;
            color: #BDBDBD;
            font-size: 11px;
            font-weight: 600;
        }

        .shelf-item.active {
            color: var(--text-dark);
        }

        .shelf-item i {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .fab-container {
            position: relative;
            top: -40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .fab-button {
            width: 62px;
            height: 62px;
            background: var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 28px;
            box-shadow: 0 10px 25px rgba(41, 121, 255, 0.35);
            border: 4px solid #fff;
        }

        .fab-label {
            margin-top: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #666;
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

        /* Filter Bottom Sheet */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
            z-index: 2000;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .filter-bottom-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            border-radius: 28px 28px 0 0;
            z-index: 2001;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s;
            padding: 24px;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.15);
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            visibility: hidden;
        }

        .filter-body {
            overflow-y: auto;
            padding-right: 5px;
            flex: 1;
        }

        .filter-body::-webkit-scrollbar {
            width: 4px;
        }

        .filter-body::-webkit-scrollbar-thumb {
            background: #EEE;
            border-radius: 10px;
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .filter-header h2 {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            color: var(--text-dark);
        }

        .filter-close {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            cursor: pointer;
            font-size: 18px;
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-group label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .filter-select {
            width: 100%;
            background: #F8F9FB;
            border: 1px solid #EDEDED;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 15px;
            color: var(--text-dark);
            font-weight: 500;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
        }

        /* Range Slider */
        .range-container {
            padding: 10px 5px;
        }

        .range-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            background: #EEE;
            outline: none;
            border-radius: 10px;
        }

        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 22px;
            height: 22px;
            background: var(--primary-orange);
            cursor: pointer;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(255, 107, 0, 0.3);
        }

        .range-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 11px;
            color: #999;
            font-weight: 600;
        }

        /* Checkbox Group */
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-item input {
            display: none;
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            border: 2.5px solid #DDD;
            border-radius: 6px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .checkbox-item input:checked+.checkbox-custom {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
        }

        .checkbox-item input:checked+.checkbox-custom::after {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #fff;
            font-size: 10px;
        }

        .checkbox-label {
            font-size: 14px;
            font-weight: 600;
            color: #444;
        }

        .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 12px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #F0F0F0;
        }

        .btn-cancel {
            background: #fff;
            color: #444;
            border: 1px solid #DDD;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            text-align: center;
            text-decoration: none !important;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #F8F9FA;
            border-color: #CCC;
        }

        .btn-apply {
            background: var(--primary-orange);
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            text-align: center;
            text-decoration: none !important;
            box-shadow: 0 8px 20px rgba(255, 107, 0, 0.25);
        }

        .modal-overlay.active {
            display: block;
            opacity: 1;
        }

        .filter-bottom-sheet.active {
            transform: translateY(0);
            visibility: visible;
        }

        @media (min-width: 992px) {
            .filter-bottom-sheet {
                max-width: 450px;
                left: 50%;
                transform: translateX(-50%) translateY(100%);
                border-radius: 28px;
                bottom: 20px;
            }

            .filter-bottom-sheet.active {
                transform: translateX(-50%) translateY(0);
                visibility: visible;
            }
        }
    </style>


    <!-- 1. Header (Mobile Only) -->
    <header class="app-header">
        <div class="header-logo">
            @if (!empty($logos))
                <img src="{{ asset('uploads/logos/' . $logos->first()->logo) }}" alt="BookHub">
            @else
                <h4 class="mb-0 fw-bold">BookHub</h4>
            @endif
        </div>
        <div class="header-search">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Search" id="globalSearch">
            <i class="fas fa-expand scan-icon"></i>
        </div>
        <div class="header-icons">
            <a href="#" class="icon-btn">
                <i class="far fa-bell"></i>
                <span class="badge-dot"></span>
            </a>
            @auth
                <a href="{{ route('student.account') }}">
                    <img src="{{ asset(Auth::user()->profile_image ?? 'assets/images/avatar.png') }}"
                        style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1.5px solid #F0F0F0;">
                </a>
            @else
                <a href="{{ route('student.login') }}" class="icon-btn">
                    <i class="far fa-user-circle"></i>
                </a>
            @endauth
        </div>
    </header>

    <!-- 2. Banner -->
    <div class="banner-container">
        <div class="premium-banner">
            <div class="banner-content">
                <h2 class="banner-title">Student Special</h2>
                <p class="banner-subtitle">Extra 20% discount for students</p>
                <a href="#" class="banner-btn">Shop Now</a>
            </div>
            <img src="https://img.freepik.com/free-vector/hand-drawn-book-stack-illustration_23-2149341819.jpg?w=740"
                class="banner-illustration" alt="Featured">
        </div>
    </div>

    <!-- 3. Selection Info Bar -->
    <div class="selection-bar">
        <div class="selection-info" id="currentSelectionInfo">
            Showing Books for <strong>All</strong>
        </div>
        <a href="javascript:void(0)" class="btn-change" id="openFilter">Change</a>
    </div>

    <!-- 4. Categories -->
    <div class="category-wrapper" id="homeSubjectsContainer">
        @include('front.partials.home_subjects')
    </div>

    <div class="tab-filters">
        <a href="javascript:void(0)" class="filter-chip icon-chip" id="openFilterChip">
            <i class="fas fa-sliders-h"></i> Filter
        </a>
        <a href="javascript:void(0)" class="filter-chip condition-chip {{ $condition == 'all' ? 'active' : '' }}"
            data-condition="all">All</a>
        <a href="javascript:void(0)" class="filter-chip condition-chip {{ $condition == 'new' ? 'active' : '' }}"
            data-condition="new">New</a>
        <a href="javascript:void(0)" class="filter-chip condition-chip {{ $condition == 'old' ? 'active' : '' }}"
            data-condition="old">Old</a>
    </div>

    <!-- 6. Product Grid -->
    <div class="book-grid" id="homeProductGrid">
        @include('front.partials.home_product_grid')
    </div>

    <!-- Extra Spacing (Mobile Only) -->
    <div class="py-4 d-lg-none"></div>

    <!-- CTA Block -->
    <div class="container py-5">
        <!-- Sales Card -->
        <div class="card border-0 shadow-lg mb-5"
            style="background-image: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%); border-radius: 28px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="py-5 px-4 px-md-5">
                    <div class="row align-items-center text-white g-4">
                        <div class="col-xl-7 col-lg-7">
                            <h2 class="display-6 text-white fw-semibold mb-3">Become a BookHub Sales Executive</h2>
                            <p class="lead mb-4">Help schools and institutions discover the right books while earning
                                attractive commissions, marketing support, and exclusive incentives.</p>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="d-flex align-items-center">
                                    <span class="bg-white bg-opacity-10 rounded-circle p-3 me-3"><i
                                            class="fa-solid fa-chart-line fs-4"></i></span>
                                    <div>
                                        <h5 class="mb-1 text-white">Grow network</h5>
                                        <small class="text-white-50">Local reach.</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="bg-white bg-opacity-10 rounded-circle p-3 me-3"><i
                                            class="fa-solid fa-coins fs-4"></i></span>
                                    <div>
                                        <h5 class="mb-1 text-white">Earn more</h5>
                                        <small class="text-white-50">Payouts.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-5">
                            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #fff;">
                                <div class="card-body p-4 text-center text-md-start">
                                    <h4 class="fw-semibold mb-2 text-primary">Register today</h4>
                                    <p class="mb-4 text-muted small">Fill out a short application and our team will connect
                                        with you.</p>
                                    <a href="{{ url('/sales') }}" class="btn btn-primary w-100 py-3"
                                        style="background: var(--primary-orange); border: none; font-weight: bold; border-radius: 12px;">
                                        Join As Sales Executive <i class="fa-solid fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Card -->
        <div class="card border-0 shadow-lg mb-5"
            style="background: var(--primary); border-radius: 28px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="py-5 px-4 px-md-5">
                    <div class="row align-items-center gy-4">
                        <div class="col-lg-7 text-white">
                            <span class="badge bg-light text-primary mb-3 px-3 py-2 rounded-pill shadow-sm">
                                Vendor Opportunity • Limited Effort, High Reach
                            </span>
                            <h2 class="fw-semibold mb-3 text-dark" style="font-weight: 800; font-size: 2.5rem;">
                                Turn Your Catalog into Revenue
                            </h2>
                            <p class="mb-4 text-dark opacity-75">
                                Upload your books once and let BookHub handle the marketing. Only 5% promotion fee.
                            </p>
                            <div class="d-flex flex-wrap gap-4 mb-4">
                                <div class="d-flex align-items-center text-dark fw-bold">
                                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center me-3"
                                        style="width:40px;height:40px;">
                                        <i class="fa fa-book"></i>
                                    </div>
                                    <span>Easy Uploads</span>
                                </div>
                                <div class="d-flex align-items-center text-dark fw-bold">
                                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center me-3"
                                        style="width:40px;height:40px;">
                                        <i class="fa fa-bullhorn"></i>
                                    </div>
                                    <span>Marketing</span>
                                </div>
                            </div>
                            <a href="{{ route('vendor.register') }}" class="btn btn-dark px-4 py-3"
                                style="border-radius: 12px; font-weight: bold;">
                                Register Bookstore
                            </a>
                        </div>
                        <div class="col-lg-5">
                            <div class="bg-white bg-opacity-10 border border-white border-opacity-25 p-4 shadow-sm"
                                style="border: 1px solid rgba(255,255,255,0.4) !important; border-radius: 20px;">
                                <h5 class="fw-bold mb-3 text-dark">Free Plan & Pro Plan</h5>
                                <p class="mb-3 text-dark opacity-75 small">
                                    Start free with 100 books, or switch to Pro.
                                </p>
                                <ul class="list-unstyled mb-4 text-dark fw-bold" style="font-size: 0.9rem;">
                                    <li class="mb-2"><i class="fa fa-check text-success me-2"></i> No website required
                                    </li>
                                    <li class="mb-2"><i class="fa fa-check text-success me-2"></i> We handle traffic
                                    </li>
                                    <li><i class="fa fa-check text-success me-2"></i> Transparent dashboard</li>
                                </ul>
                                <a href="{{ url('/contact') }}" class="btn btn-outline-dark w-100 py-2"
                                    style="border-radius: 8px; font-weight: bold;">Talk To Team</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="position-absolute rounded-circle bg-white opacity-10"
                style="width:200px;height:200px;top:-50px;right:-50px;"></div>
        </div>
    </div>

    <!-- 7. Bottom Navigation (Mobile Only) -->
    <nav class="bottom-shelf">
        <a href="{{ url('/') }}" class="shelf-item active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="#" class="shelf-item">
            <i class="fas fa-th-large"></i>
            <span>Category</span>
        </a>

        <div class="fab-container">
            <a href="{{ Auth::check() ? route('student.index') : route('student.login') }}" class="fab-button"
                style="text-decoration: none !important;">
                <i class="fas fa-plus"></i>
            </a>
            <span class="fab-label">Sell Old Book</span>
        </div>

        <a href="{{ url('/cart') }}" class="shelf-item">
            <i class="fas fa-shopping-basket" style="position: relative;">
                @if (isset($headerCartItems) && count($headerCartItems) > 0)
                    <span class="cart-tag">{{ count($headerCartItems) }}</span>
                @endif
            </i>
            <span>Cart</span>
        </a>
        <a href="{{ Auth::check() ? route('student.account') : route('student.login') }}" class="shelf-item">
            <i class="far fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Filter Modal Backdrop -->
    <div class="modal-overlay" id="modalOverlay"></div>

    <!-- Filter Bottom Sheet -->
    <div class="filter-bottom-sheet" id="filterSheet">
        <div class="filter-header">
            <h2>Filters</h2>
            <div class="filter-close" id="closeFilter">
                <i class="fas fa-times"></i>
            </div>
        </div>

        <div class="filter-body">
            <!-- Basic Filters (Triggered by Change) -->
            <div id="basicFiltersContent">
                <div class="filter-group">
                    <label>Category</label>
                    <select class="filter-select" id="filterSection">
                        <option value="">Select Category</option>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>Sub Category</label>
                    <select class="filter-select" id="filterCategory">
                        <option value="">Select Sub Category</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Class/Stream</label>
                    <select class="filter-select" id="filterSubcategory">
                        <option value="">Select Class</option>
                    </select>
                </div>
            </div>

            <!-- Advanced Filters (Triggered by Filter Chip) -->
            <div id="advancedFiltersContent">
                <div class="filter-group">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <label class="mb-0" style="color: var(--text-dark); font-weight: 700;">Location
                                Range</label>
                            <i class="fas fa-location-crosshairs ms-2"
                                style="color: var(--primary-orange); cursor: pointer;" onclick="detectLocation()"
                                title="Detect my location"></i>
                        </div>
                        <span id="rangeValue"
                            style="color: var(--primary-orange); font-weight: 700; font-size: 13px;">Within 100 km+</span>
                    </div>
                    <div class="range-container">
                        <input type="range" class="range-slider" id="distanceRange" min="1" max="100"
                            value="100">
                        <div class="range-labels">
                            <span>1 km</span>
                            <span>25 km</span>
                            <span>50 km</span>
                            <span>100 km+</span>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Book Type</label>
                    <div class="checkbox-group">
                        @if (isset($bookTypes))
                            @foreach ($bookTypes as $bt)
                                <label class="checkbox-item">
                                    <input type="checkbox" name="book_types[]" value="{{ $bt->id }}" checked>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-label">{{ $bt->book_type }}</span>
                                </label>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="filter-group">
                    <label>Language</label>
                    <div class="checkbox-group">
                        @if (isset($languages))
                            @foreach ($languages as $lang)
                                <label class="checkbox-item">
                                    <input type="checkbox" name="languages[]" value="{{ $lang->id }}" checked>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-label">{{ $lang->name }}</span>
                                </label>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-actions">
            <a href="javascript:void(0)" class="btn-cancel" id="resetBtn">Reset</a>
            <a href="javascript:void(0)" class="btn-apply" id="applyBtn">Apply Filters</a>
        </div>
    </div>

    <script>
        const openBtn = document.getElementById('openFilter');
        const closeBtn = document.getElementById('closeFilter');
        const resetBtn = document.getElementById('resetBtn');
        const overlay = document.getElementById('modalOverlay');
        const sheet = document.getElementById('filterSheet');
        const rangeSlider = document.getElementById('distanceRange');
        const rangeValue = document.getElementById('rangeValue');

        // Location Detection
        function detectLocation() {
            if (navigator.geolocation) {
                const rangeLabel = document.querySelector('label[class="mb-0"]');
                const originalText = rangeLabel ? rangeLabel.innerText : 'Location Range';
                if (rangeLabel) rangeLabel.innerText = "Detecting...";

                navigator.geolocation.getCurrentPosition(position => {
                    const {
                        latitude,
                        longitude
                    } = position.coords;
                    fetch(`{{ url('set-location-session') }}?latitude=${latitude}&longitude=${longitude}`)
                        .then(() => {
                            if (rangeLabel) rangeLabel.innerText = originalText;
                            console.log('Location updated');
                        })
                        .catch(err => {
                            if (rangeLabel) rangeLabel.innerText = originalText;
                            console.error('Location error:', err);
                        });
                }, error => {
                    if (rangeLabel) rangeLabel.innerText = originalText;
                    console.warn('Geolocation error:', error.message);
                });
            }
        }

        // Auto detect on load
        window.addEventListener('load', detectLocation);

        rangeSlider.addEventListener('input', function() {
            const val = this.value;
            if (val >= 100) {
                rangeValue.innerText = 'Within 100 km+';
            } else {
                rangeValue.innerText = `Within ${val} km`;
            }
        });

        const sectionSelect = document.getElementById('filterSection');
        const categorySelect = document.getElementById('filterCategory');
        const subcategorySelect = document.getElementById('filterSubcategory');

        function toggleModal(show, mode = 'advanced') {
            const h2 = sheet.querySelector('.filter-header h2');
            const basic = document.getElementById('basicFiltersContent');
            const advanced = document.getElementById('advancedFiltersContent');
            const resetBtn = document.getElementById('resetBtn');
            const applyBtn = document.getElementById('applyBtn');

            if (show) {
                if (mode === 'basic') {
                    h2.innerText = 'Select Filters';
                    basic.style.display = 'block';
                    advanced.style.display = 'none';
                    resetBtn.innerText = 'Cancel';
                    applyBtn.innerText = 'Apply';
                    resetBtn.dataset.mode = 'basic';
                } else {
                    h2.innerText = 'Filters';
                    basic.style.display = 'none';
                    advanced.style.display = 'block';
                    resetBtn.innerText = 'Reset';
                    applyBtn.innerText = 'Apply Filters';
                    resetBtn.dataset.mode = 'advanced';
                }
                overlay.classList.add('active');
                sheet.classList.add('active');
                document.body.style.overflow = 'hidden';
            } else {
                overlay.classList.remove('active');
                sheet.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        openBtn.addEventListener('click', () => toggleModal(true, 'basic'));
        document.getElementById('openFilterChip').addEventListener('click', () => toggleModal(true, 'advanced'));
        closeBtn.addEventListener('click', () => toggleModal(false));

        resetBtn.addEventListener('click', () => {
            if (resetBtn.dataset.mode === 'basic') {
                toggleModal(false);
                return;
            }
            // Reset selects (Actually for advanced we don't necessarily reset basic selects unless wanted)
            // But we can reset all for true "Reset"
            sectionSelect.value = '';
            categorySelect.innerHTML = '<option value="">Select Sub Category</option>';
            subcategorySelect.innerHTML = '<option value="">Select Class</option>';

            // Reset slider
            rangeSlider.value = 100;
            rangeValue.innerText = 'Within 100 km+';

            // Reset checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);

            updateHomeGrid();
        });

        overlay.addEventListener('click', () => toggleModal(false));

        function updateHomeGrid(subjectId = null, activeSubjectName = null) {
            const gridContainer = document.getElementById('homeProductGrid');
            const subjectsContainer = document.getElementById('homeSubjectsContainer');
            const selectionInfo = document.getElementById('currentSelectionInfo');
            const activeChip = document.querySelector('.condition-chip.active');
            const condition = activeChip ? activeChip.dataset.condition : 'all';

            const sectionSelect = document.getElementById('filterSection');
            const categorySelect = document.getElementById('filterCategory');
            const subcategorySelect = document.getElementById('filterSubcategory');

            const sectionId = sectionSelect.value;
            const categoryId = categorySelect.value;
            const subcategoryId = subcategorySelect.value;

            // Get selected book types
            const bookTypes = Array.from(document.querySelectorAll('input[name="book_types[]"]:checked')).map(cb => cb
                .value);
            // Get selected languages
            const languages = Array.from(document.querySelectorAll('input[name="languages[]"]:checked')).map(cb => cb
                .value);
            // Get distance
            const distance = rangeSlider.value;

            gridContainer.style.opacity = '0.5';
            subjectsContainer.style.opacity = '0.5';

            let queryParams =
                `?filter_update=1&condition=${condition}&section_id=${sectionId}&category_id=${categoryId}&subcategory_id=${subcategoryId}&distance=${distance}`;

            if (bookTypes.length > 0) queryParams += `&book_types=${bookTypes.join(',')}`;
            if (languages.length > 0) queryParams += `&languages=${languages.join(',')}`;

            if (subjectId) {
                queryParams += `&subject_id=${subjectId}`;
            }

            fetch(`{{ url('/') }}${queryParams}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    gridContainer.innerHTML = data.html;
                    // Only update subjects if we are NOT filtering BY subject (to keep current subjects list)
                    if (!subjectId) {
                        subjectsContainer.innerHTML = data.subjects_html;
                    }

                    gridContainer.style.opacity = '1';
                    subjectsContainer.style.opacity = '1';

                    // Update selection info text
                    const sectionText = sectionSelect.options[sectionSelect.selectedIndex].text;
                    const categoryText = categorySelect.options[categorySelect.selectedIndex].text;
                    const subcategoryText = subcategorySelect.options[subcategorySelect.selectedIndex].text;

                    let displayText = 'Showing ';
                    if (condition !== 'all') {
                        displayText += (condition === 'new' ? 'New ' : 'old ');
                    }

                    if (activeSubjectName) {
                        displayText += `<strong>${activeSubjectName}</strong> Books `;
                    } else {
                        displayText += 'Books ';
                    }

                    displayText += 'for <strong>';

                    let parts = [];
                    if (sectionId) parts.push(sectionText);
                    if (categoryId) parts.push(categoryText);
                    if (subcategoryId) parts.push(subcategoryText);

                    if (parts.length > 0) {
                        displayText += parts.join(', ');
                    } else {
                        displayText += 'All';
                    }
                    displayText += '</strong>';
                    selectionInfo.innerHTML = displayText;

                    // Close the sheet (if open)
                    toggleModal(false);
                })
                .catch(err => {
                    console.error(err);
                    gridContainer.style.opacity = '1';
                });
        }

        document.querySelectorAll('.condition-chip').forEach(chip => {
            chip.addEventListener('click', function() {
                document.querySelectorAll('.condition-chip').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                updateHomeGrid();
            });
        });

        document.getElementById('applyBtn').addEventListener('click', () => updateHomeGrid());

        function filterBySubject(subjectId) {
            let activeSubjectName = '';
            // Highlight active subject
            document.querySelectorAll('.subject-filter-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.subjectId == subjectId) {
                    btn.classList.add('active');
                    activeSubjectName = btn.querySelector('.category-label').innerText;
                }
            });
            updateHomeGrid(subjectId, activeSubjectName);
        }
        sectionSelect.addEventListener('change', function() {
            const sectionId = this.value;
            categorySelect.innerHTML = '<option value="">Loading...</option>';
            subcategorySelect.innerHTML = '<option value="">Select Class</option>';

            if (sectionId) {
                fetch(`{{ url('get-filter-categories') }}?section_id=${sectionId}`)
                    .then(res => res.json())
                    .then(data => {
                        categorySelect.innerHTML = '<option value="">Select Sub Category</option>';
                        data.forEach(cat => {
                            categorySelect.innerHTML +=
                                `<option value="${cat.id}">${cat.category_name}</option>`;
                        });
                    });
            }
        });

        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            const sectionId = sectionSelect.value;
            subcategorySelect.innerHTML = '<option value="">Loading...</option>';

            if (categoryId) {
                fetch(`{{ url('get-filter-subcategories') }}?category_id=${categoryId}&section_id=${sectionId}`)
                    .then(res => res.json())
                    .then(data => {
                        subcategorySelect.innerHTML = '<option value="">Select Class</option>';
                        data.forEach(sub => {
                            subcategorySelect.innerHTML +=
                                `<option value="${sub.id}">${sub.category_name}</option>`;
                        });
                    });
            }
        });
    </script>
@endsection
