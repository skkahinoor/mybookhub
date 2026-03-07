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

        /* Page-level body overrides */
        body {
            background-color: var(--bg-light) !important;
            font-family: 'Poppins', sans-serif !important;
        }

        .page-content {
            background-color: var(--bg-light) !important;
            padding-top: 0 !important;
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
            background: #f0f0f0;
            position: relative;
            width: 100%;
            height: auto;
            aspect-ratio: 16 / 8;
            display: block;
        }

        .banner-illustration {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        @media (min-width: 992px) {
            .banner-container {
                padding: 15px 20px 30px;
            }

            .premium-banner {
                aspect-ratio: auto;
                background: transparent;
                height: auto;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .banner-illustration {
                width: 100%;
                height: auto;
                max-height: 500px;
                object-fit: contain;
            }
        }

        /* Swiper Fixes */
        .banner-swiper {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
        }

        .banner-container .banner-pagination {
            position: absolute;
            bottom: 15px !important;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            z-index: 10;
        }

        .banner-container .swiper-pagination-bullet {
            background: rgba(255, 255, 255, 0.5);
            opacity: 1;
            width: 8px;
            height: 8px;
            margin: 0 4px !important;
        }

        .banner-container .swiper-pagination-bullet-active {
            background: #fff !important;
            width: 20px;
            border-radius: 5px;
        }

        .banner-illustration {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
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

        /* Product Grid Wall */
        .book-wall {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            padding: 20px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .book-wall {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        @media (max-width: 1000px) {
            .book-wall {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 750px) {
            .book-wall {
                grid-template-columns: repeat(3, 1fr);
                padding: 20px;
            }
        }

        @media (max-width: 500px) {
            .book-wall {
                grid-template-columns: repeat(2, 1fr);
                padding: 10px;
            }
        }

        /* CARD */
        .book-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: .25s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .book-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
        }

        /* COVER */
        .cover {
            aspect-ratio: 2/3;
            position: relative;
            overflow: hidden;
            background: #fff;
        }

        .cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: .3s;
        }

        .book-item:hover .cover img {
            transform: scale(1.05);
        }

        /* BADGE */
        .condition-badge {
            position: absolute;
            top: 6px;
            left: 6px;
            font-size: 10px;
            padding: 3px 7px;
            border-radius: 20px;
            color: white;
            z-index: 2;
            text-transform: uppercase;
            font-weight: 600;
        }

        .badge-new {
            background: #22c55e;
        }

        .badge-used {
            background: #f59e0b;
        }

        /* INFO */
        .info {
            padding: 10px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .title {
            font-size: 12px;
            font-weight: 600;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 4px;
            line-height: 1.3;
            color: #333;
            text-decoration: none !important;
        }

        .author {
            font-size: 11px;
            color: #777;
            margin-top: 2px;
        }

        /* PRICE */
        .price {
            font-size: 14px;
            font-weight: 700;
            margin-top: auto;
            color: #1a1a1a;
            padding-top: 8px;
        }

        /* BUTTON */
        .cart-btn {
            width: 100%;
            margin-top: 8px;
            background: #ff6b00;
            border: none;
            color: white;
            padding: 7px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .cart-btn:hover {
            background: #e45d00;
        }

        /* SKELETON LOADER for infinite scroll */
        .skeleton {
            animation: pulse 1.2s infinite;
            background: linear-gradient(90deg, #eee, #ddd, #eee);
            height: 300px;
            border-radius: 8px;
        }

        @keyframes pulse {
            0% {
                opacity: .8
            }

            50% {
                opacity: .4
            }

            100% {
                opacity: .8
            }
        }

        /* Load More Button */
        .load-more-container {
            display: flex;
            justify-content: center;
            padding: 30px 20px;
            width: 100%;
        }

        .btn-load-more {
            background: #fff;
            color: var(--primary-orange);
            border: 2px solid var(--primary-orange);
            padding: 12px 40px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 0, 0.1);
        }

        .btn-load-more:hover {
            background: var(--primary-orange);
            color: #fff;
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.25);
            transform: translateY(-2px);
        }

        .btn-load-more:disabled {
            border-color: #eee;
            color: #ccc;
            background: #f9f9f9;
            cursor: not-allowed;
            pointer-events: none;
            box-shadow: none;
        }

        .btn-load-more i {
            margin-right: 8px;
        }

        .discount-pill {
            background: rgba(255, 59, 48, 0.1);
            color: #FF3B30;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            margin-left: auto;
        }

        .book-info {
            padding: 12px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .book-title {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.2;
            min-height: auto;
        }

        .book-type {
            font-size: 11px;
            color: #666;
            margin: 0;
        }

        .book-category {
            font-size: 11px;
            color: #888;
            margin-bottom: 4px;
        }

        .book-price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            margin-bottom: 8px;
        }

        .current-price {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a1a;
        }

        .btn-quick-add {
            width: 100%;
            height: 38px;
            background: var(--primary-orange);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            position: static;
            /* Move back into flow if needed, but screenshot shows it at bottom */
            margin-top: 4px;
        }

        .btn-quick-add:hover {
            background: #e66000;
        }

        .book-info {
            padding: 12px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: #fff;
        }

        .book-title {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.2;
            transition: color 0.3s ease;
        }

        .book-card-v2:hover .book-title {
            color: var(--primary-orange);
        }

        .book-category {
            font-size: 11px;
            color: #888;
            margin: 0;
            display: block;
        }

        .book-category::before {
            display: none;
            /* Hide the icon to match screenshot */
        }

        .book-price-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            margin-top: 4px;
        }

        .current-price {
            font-size: 15px;
            font-weight: 800;
            color: #1a1a1a;
        }

        .old-price {
            font-size: 12px;
            color: #999;
            text-decoration: line-through;
        }

        @media (max-width: 767px) {
            .book-thumb {
                height: 210px;
            }

            .book-info {
                padding: 10px;
            }

            .btn-quick-add {
                height: 34px;
                font-size: 13px;
            }

            .book-title {
                font-size: 13px;
            }
        }

        /* (bottom-shelf nav handled by layout3.blade.php) */

        /* Filter Bottom Sheet */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
            z-index: 10000;
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
            z-index: 10001;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s;
            padding: 24px 24px calc(24px + env(safe-area-inset-bottom));
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
            margin-top: 15px;
            padding-top: 15px;
            padding-bottom: 20px;
            /* Added padding to ensure buttons are fully visible */
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



        /* Sell Book Community Section */
        .sell-book-section {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-header h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header h2 span {
            color: var(--primary-orange);
        }

        .sell-book-scroll {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 15px;
            scrollbar-width: none;
        }

        .sell-book-scroll::-webkit-scrollbar {
            display: none;
        }

        .student-book-card {
            min-width: 260px;
            background: #fff;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            gap: 12px;
            border: 1px solid rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
            position: relative;
        }

        .student-book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
        }

        .student-book-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 15px;
            background: #f8f8f8;
        }

        .sold-overlay {
            position: absolute;
            top: 25px;
            right: 0;
            background: #ff4757;
            color: white;
            padding: 4px 15px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            border-radius: 20px 0 0 20px;
            z-index: 2;
            box-shadow: 0 4px 10px rgba(255, 71, 87, 0.3);
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }

        .student-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #eee;
        }

        .student-name {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .student-book-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .student-book-price {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary-orange);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .condition-tag {
            font-size: 10px;
            padding: 3px 8px;
            background: #f0f7ff;
            color: #007aff;
            border-radius: 6px;
            font-weight: 700;
        }

        .cta-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 40px;
        }

        @media (max-width: 750px) {
            .cta-container {
                padding: 20px;
            }
        }

        @media (max-width: 500px) {
            .cta-container {
                padding: 10px;
            }
        }
    </style>




    <!-- 2. Banner -->
    <div class="banner-container">
        @if (!empty($sliderBanners) && count($sliderBanners) > 0)
            <div class="swiper banner-swiper">
                <div class="swiper-wrapper">
                    @foreach ($sliderBanners as $banner)
                        <div class="swiper-slide">
                            <a href="{{ $banner['link'] ?? '#' }}" class="premium-banner">
                                <img src="{{ $banner['image'] }}" class="banner-illustration" alt="{{ $banner['alt'] }}">
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="banner-pagination"></div>
            </div>
        @else
        @endif
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

    <!-- 6. Product Grid Wall -->
    <div class="book-wall" id="homeProductGrid">
        @include('front.partials.home_product_grid')
    </div>

    <!-- Load More Button -->
    <div class="load-more-container" id="loadMoreContainer" {!! !$sliderProducts->hasMorePages() ? 'style="display:none;"' : '' !!}>
        <button class="btn-load-more" id="loadMoreBtn">
            <i class="fas fa-sync-alt"></i> Load More Books
        </button>
    </div>

    <style>
        .sales-cta-card {
            background-color: #1e3a8a !important;
            border-radius: 28px !important;
            overflow: hidden !important;
        }

        .vendor-cta-card {
            background-color: #ff9900 !important;
            /* using fallback orange if var --primary fails */
            border-radius: 28px !important;
            overflow: hidden !important;
        }
    </style>

    <!-- 7. Sell Book Community Section -->
    @if (isset($sellBookRequests) && $sellBookRequests->count() > 0)
        <div class="sell-book-section">
            <div class="section-header">
                <h2><i class="fas fa-users" style="color: var(--primary-orange);"></i> Student <span>Marketplace</span></h2>
                <a href="javascript:void(0)" class="btn-change">View All</a>
            </div>

            <div class="sell-book-scroll">
                @foreach ($sellBookRequests as $sbook)
                    <div class="student-book-card">
                        @if ($sbook->book_status == 'sold')
                            <div class="sold-overlay">Sold</div>
                        @endif

                        <img src="{{ asset($sbook->book_image ?? 'front/images/product/default.jpg') }}"
                            class="student-book-img {{ $sbook->book_status == 'sold' ? 'grayscale' : '' }}"
                            alt="{{ $sbook->book_title }}">

                        <div class="student-info">
                            <img src="{{ asset($sbook->user->profile_image ?? 'assets/images/avatar.png') }}"
                                class="student-avatar" alt="User">
                            <span class="student-name">By {{ $sbook->user->name }}</span>
                            <span class="ms-auto condition-tag">{{ $sbook->book_condition }}</span>
                        </div>

                        <h3 class="student-book-title">{{ $sbook->book_title }}</h3>

                        <div class="student-book-price">
                            <span>₹{{ number_format($sbook->expected_price, 2) }}</span>
                            @if ($sbook->book_status != 'sold')
                                <a href="{{ route('student.sell-book.show', $sbook->id) }}" class="btn btn-sm"
                                    style="background: #f2f2f7; border-radius: 10px; font-weight: 700; font-size: 11px; color: var(--text-dark);">Details</a>
                            @else
                                <span style="font-size: 11px; color: #ff4757; font-weight: 700;">SOLD OUT</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- CTA Block -->
    <div class="cta-container pt-5 pb-4">
        <!-- Sales Card -->
        <div class="sales-cta-card shadow-lg mb-5 position-relative">

            <!-- Decorative Background Elements -->
            <div class="position-absolute rounded-circle"
                style="width: 300px; height: 300px; background: radial-gradient(circle, rgba(29,78,216,0.5) 0%, rgba(0,0,0,0) 70%); top: -100px; right: -50px; pointer-events: none;">
            </div>
            <div class="position-absolute rounded-circle"
                style="width: 200px; height: 200px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(0,0,0,0) 70%); bottom: -50px; left: -50px; pointer-events: none;">
            </div>

            <div class="position-relative z-1">
                <div class="py-5 px-4 px-md-5">
                    <div class="row align-items-center text-white g-4">
                        <div class="col-xl-7 col-lg-7 text-center text-lg-start">
                            <span class="badge bg-white text-primary mb-3 px-3 py-2 rounded-pill shadow-sm"
                                style="font-weight: 700; font-size: 0.85rem;">
                                <i class="fas fa-rocket me-1"></i> Career Opportunity
                            </span>
                            <h2 class="text-white fw-bold mb-3"
                                style="font-size: clamp(2rem, 4vw, 2.75rem); letter-spacing: -0.5px; line-height: 1.2;">
                                Become a BookHub <span style="color: #60a5fa;">Sales Executive</span>
                            </h2>
                            <p class="mb-4"
                                style="font-size: 1.1rem; opacity: 0.9; line-height: 1.6; max-width: 600px; margin: 0 auto; margin-lg-0;">
                                Help schools and institutions discover the right books while earning
                                attractive commissions, marketing support, and exclusive incentives.
                            </p>
                            <div
                                class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 gap-md-4 mt-4">
                                <div class="d-flex align-items-center bg-white bg-opacity-10 px-4 py-2 rounded-pill"
                                    style="backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                                    <span class="text-white me-2"><i class="fa-solid fa-chart-line fs-5"></i></span>
                                    <div class="text-start">
                                        <h6 class="mb-0 text-white" style="font-weight: 600; font-size: 0.95rem;">Grow
                                            network</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center bg-white bg-opacity-10 px-4 py-2 rounded-pill"
                                    style="backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                                    <span class="text-white me-2"><i class="fa-solid fa-coins fs-5"></i></span>
                                    <div class="text-start">
                                        <h6 class="mb-0 text-white" style="font-weight: 600; font-size: 0.95rem;">Earn
                                            more
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-5 mt-5 mt-lg-0">
                            <div class="card border-0 shadow-lg"
                                style="border-radius: 24px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                                <div class="card-body p-4 p-md-5 text-center">
                                    <div class="mb-4">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3"
                                            style="width: 60px; height: 60px;">
                                            <i class="fas fa-user-plus fs-4"></i>
                                        </div>
                                        <h4 class="fw-bold mb-2 text-dark" style="font-size: 1.5rem;">Register Today</h4>
                                        <p class="text-secondary small" style="font-size: 0.95rem;">Fill out a short
                                            application, and our team will get in touch with you shortly.</p>
                                    </div>
                                    <a href="{{ url('/sales') }}"
                                        class="btn w-100 py-3 d-flex align-items-center justify-content-center gap-2"
                                        style="background: var(--primary-orange); color: white; border: none; font-weight: 700; border-radius: 14px; font-size: 1.1rem; box-shadow: 0 8px 20px rgba(255, 153, 0, 0.3); transition: all 0.3s ease;"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba(255, 153, 0, 0.4)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px rgba(255, 153, 0, 0.3)';">
                                        Join As Sales Executive <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Card -->
        <div class="vendor-cta-card shadow-lg mb-5 position-relative"
            style="background: linear-gradient(135deg, #1e3a8a 0%, #db2777 100%) !important; border-radius: 28px; overflow: hidden; transform: translateY(0); transition: all 0.3s ease;">
            <!-- Decorative Elements -->
            <div class="position-absolute rounded-circle"
                style="width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(0,0,0,0) 70%); top: -50px; right: -50px; pointer-events: none;">
            </div>
            <div class="position-absolute rounded-circle"
                style="width: 150px; height: 150px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(0,0,0,0) 70%); bottom: -20px; left: -20px; pointer-events: none;">
            </div>

            <div class="position-relative z-1">
                <div class="py-5 px-4 px-md-5">
                    <div class="row align-items-center gy-5">
                        <div class="col-lg-7 text-center text-lg-start">
                            <span class="badge bg-white text-dark mb-3 px-3 py-2 rounded-pill shadow-sm"
                                style="font-weight: 700; font-size: 0.85rem;">
                                <i class="fas fa-store me-1" style="color: #ff9900;"></i> Vendor Opportunity
                            </span>
                            <h2 class="text-white fw-bold mb-3"
                                style="font-size: clamp(2rem, 4vw, 2.75rem); letter-spacing: -0.5px; line-height: 1.2;">
                                Turn Your Catalog into <span style="color: #ffe0b2;">Revenue</span>
                            </h2>
                            <p class="mb-4 text-white"
                                style="font-size: 1.1rem; opacity: 0.9; line-height: 1.6; max-width: 600px; margin: 0 auto; margin-lg-0;">
                                Upload your books once and let BookHub handle the marketing. Expand your reach with only a
                                5% promotion fee.
                            </p>

                            <div
                                class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 gap-md-4 mt-4 mb-4 mb-lg-0">
                                <div class="d-flex align-items-center bg-white bg-opacity-10 px-4 py-2 rounded-pill"
                                    style="backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center me-2"
                                        style="width: 32px; height: 32px;">
                                        <i class="fa fa-book" style="color: #ff9900; font-size: 0.85rem;"></i>
                                    </div>
                                    <h6 class="mb-0 text-white" style="font-weight: 600; font-size: 0.95rem;">Easy Uploads
                                    </h6>
                                </div>
                                <div class="d-flex align-items-center bg-white bg-opacity-10 px-4 py-2 rounded-pill"
                                    style="backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center me-2"
                                        style="width: 32px; height: 32px;">
                                        <i class="fa fa-bullhorn" style="color: #ff9900; font-size: 0.85rem;"></i>
                                    </div>
                                    <h6 class="mb-0 text-white" style="font-weight: 600; font-size: 0.95rem;">Automated
                                        Marketing</h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card border-0 shadow-lg h-100"
                                style="border-radius: 24px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                                <div
                                    class="card-body p-4 p-md-5 d-flex flex-column justify-content-center text-center text-md-start">
                                    <h4 class="fw-bold mb-3 text-dark" style="font-size: 1.5rem;">Free Plan & Pro Plan
                                    </h4>
                                    <p class="mb-4 text-secondary" style="font-size: 0.95rem;">
                                        Start free with 100 books, or switch to Pro for unlimited potential.
                                    </p>

                                    <ul class="list-unstyled mb-4 text-dark text-start mx-auto mx-md-0"
                                        style="font-size: 1rem; font-weight: 600; max-width: fit-content;">
                                        <li class="mb-3 d-flex align-items-center"><i class="fa fa-check-circle fs-5 me-3"
                                                style="color: #10b981;"></i> No technical setup needed</li>
                                        <li class="mb-3 d-flex align-items-center"><i class="fa fa-check-circle fs-5 me-3"
                                                style="color: #10b981;"></i> We drive the traffic</li>
                                        <li class="mb-3 d-flex align-items-center"><i class="fa fa-check-circle fs-5 me-3"
                                                style="color: #10b981;"></i> Transparent analytics</li>
                                    </ul>

                                    <div class="mt-auto d-flex flex-column gap-2">
                                        <a href="{{ route('vendor.register') }}"
                                            class="btn w-100 py-3 d-flex align-items-center justify-content-center gap-2"
                                            style="background: #111827; color: white; border: none; font-weight: 700; border-radius: 12px; font-size: 1.05rem; transition: all 0.3s ease;"
                                            onmouseover="this.style.background='#000'; this.style.transform='translateY(-2px)';"
                                            onmouseout="this.style.background='#111827'; this.style.transform='translateY(0)';">
                                            Register Bookstore <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                        <a href="{{ url('/contact') }}" class="btn btn-outline-dark w-100 py-3"
                                            style="border-radius: 12px; font-weight: 700; font-size: 1.05rem;">
                                            Talk To Team
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        // Load More Logic
        let currentPage = 1;
        let isLoading = false;
        let hasMore = true;

        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const loadMoreContainer = document.getElementById('loadMoreContainer');

        loadMoreBtn.addEventListener("click", () => {
            if (!isLoading && hasMore) {
                loadMoreBooks();
            }
        });

        function loadMoreBooks() {
            isLoading = true;
            loadMoreBtn.disabled = true;
            loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

            currentPage++;

            const activeChip = document.querySelector('.condition-chip.active');
            const condition = activeChip ? activeChip.dataset.condition : 'all';
            const sectionId = sectionSelect.value;
            const categoryId = categorySelect.value;
            const subcategoryId = subcategorySelect.value;
            const distance = rangeSlider.value;

            // Get selected book types & languages
            const bookTypes = Array.from(document.querySelectorAll('input[name="book_types[]"]:checked')).map(cb => cb
                .value);
            const languages = Array.from(document.querySelectorAll('input[name="languages[]"]:checked')).map(cb => cb
                .value);

            let queryParams =
                `?filter_update=1&page=${currentPage}&condition=${condition}&section_id=${sectionId}&category_id=${categoryId}&subcategory_id=${subcategoryId}&distance=${distance}`;
            if (bookTypes.length > 0) queryParams += `&book_types=${bookTypes.join(',')}`;
            if (languages.length > 0) queryParams += `&languages=${languages.join(',')}`;

            const gridContainer = document.getElementById('homeProductGrid');

            fetch(`{{ url('/') }}${queryParams}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.html.trim() === '') {
                        hasMore = false;
                        loadMoreContainer.style.display = 'none';
                    } else {
                        gridContainer.insertAdjacentHTML('beforeend', data.html);
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Load More Books';
                    }
                    isLoading = false;
                })
                .catch(err => {
                    console.error(err);
                    isLoading = false;
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Load More Books';
                });
        }

        function updateHomeGrid(subjectId = null, activeSubjectName = null) {
            currentPage = 1;
            hasMore = true;
            isLoading = false;

            const gridContainer = document.getElementById('homeProductGrid');
            const subjectsContainer = document.getElementById('homeSubjectsContainer');
            const selectionInfo = document.getElementById('currentSelectionInfo');
            const activeChip = document.querySelector('.condition-chip.active');
            const condition = activeChip ? activeChip.dataset.condition : 'all';

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
                `?filter_update=1&page=1&condition=${condition}&section_id=${sectionId}&category_id=${categoryId}&subcategory_id=${subcategoryId}&distance=${distance}`;

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

                    // Update "Load More" button based on has_more status
                    if (data.has_more) {
                        loadMoreContainer.style.display = 'flex';
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Load More Books';
                    } else {
                        loadMoreContainer.style.display = 'none';
                    }

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

        // Banner Slider Initialization
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.banner-swiper')) {
                new Swiper(".banner-swiper", {
                    loop: true,
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: ".banner-pagination",
                        clickable: true,
                    },
                    speed: 1000,
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                });
            }
        });
    </script>
@endsection
