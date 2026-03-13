@extends('front.layout.layout3')

@section('content')
    <style>
        .hero {
            position: relative;
            padding: 15px 8%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            background: linear-gradient(180deg, #EEF5FD 0%, #E6F0FA 100%);
            border-bottom-left-radius: 60px;
            border-bottom-right-radius: 60px;
            gap: 60px;
        }


        /* Background Circle */

        .hero::before {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            background: #dde7f3;
            border-radius: 50%;
            right: -200px;
            top: -150px;
            z-index: 0;
        }

        /* Floating Shape */

        .hero::after {
            content: "";
            position: absolute;
            width: 90px;
            height: 30px;
            background: #c7d9f2;
            border-radius: 20px;
            right: 420px;
            top: 120px;
            transform: rotate(25deg);
            animation: float 4s ease-in-out infinite;
        }

        /* Animation */

        @keyframes float {
            0% {
                transform: translateY(0px) rotate(25deg);
            }

            50% {
                transform: translateY(-10px) rotate(25deg);
            }

            100% {
                transform: translateY(0px) rotate(25deg);
            }
        }

        /* LEFT CONTENT */

        .hero-left {
            max-width: 600px;
            z-index: 1;
        }

        .hero h1 {
            font-size: 44px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #2c3e50;
            margin-bottom: 20px;
            line-height: 1.25;
        }

        .hero h1 span {
            color: #1b66c9;
            font-size: 1.2em;
            font-weight: 700;
        }

        .sub {
            font-size: 20px;
            margin-bottom: 10px;
            color: #3c4a5a;
        }

        .save {
            margin-bottom: 25px;
            color: #6b7b8c;
        }

        /* SEARCH BAR */

        .search-box {
            display: flex;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            transition: .3s;
        }

        .search-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .search-box input {
            flex: 1;
            border: none;
            padding: 16px;
            font-size: 16px;
            outline: none;
        }

        .search-btn {
            background: #1b66c9;
            color: white;
            border: none;
            padding: 0 24px;
            cursor: pointer;
            font-size: 18px;
        }

        /* BUTTONS */

        .buttons {
            display: flex;
            gap: 16px;
            margin-top: 25px;
        }

        .btn {
            padding: 16px 32px;
            font-size: 16px;
            border-radius: 10px;
            border: none;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
        }

        .buy {
            background: #1b66c9;
            color: white;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
        }

        .buy:hover {
            transform: translateY(-3px);
        }

        .sell {
            background: #ff6a00;
            color: white;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
        }

        .sell:hover {
            transform: translateY(-3px);
        }

        /* RIGHT IMAGE */

        .hero-right {
            z-index: 1;
        }

        .hero-right img {
            max-width: 450px;
            height: auto;
            width: 450px;
            animation: float2 6s ease-in-out infinite;
        }

        @keyframes float2 {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0);
            }
        }

        /* FEATURE BAR */

        .features {
            margin-top: 35px;

            display: flex;
            align-items: center;
            gap: 30px;

            background: #ffffff;
            padding: 16px 32px;

            border-radius: 20px;
            width: fit-content;

            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);

        }

        .feature {
            display: flex;
            align-items: center;
            gap: 10px;

            font-weight: 600;
            font-size: 15px;

            color: #2c3e50;
            white-space: nowrap;
        }

        .feature i {
            color: #1b66c9;
            font-size: 18px;
        }

        .divider {
            height: 20px;
            width: 1px;
            background: #e5e7eb;
        }

        .feature:hover {
            transform: translateY(-2px);
            transition: 0.2s;
        }

        /* RESPONSIVE */

        @media(max-width:900px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding: 40px 20px;
                gap: 30px;
                border-bottom-left-radius: 40px;
                border-bottom-right-radius: 40px;
            }

            .hero h1 {
                font-size: 28px;
            }

            .hero-right img {
                max-width: 100%;
                width: 280px;
                margin-top: 20px;
            }

            .features {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 15px;
                padding: 12px 20px;
            }

            .feature {
                font-size: 13px;
            }
        }

        :root {
            --primary-blue: #0056D2;
            --secondary-blue: #1E3A8A;
            --primary-orange: #F97316;
            --text-dark: #1E293B;
            --text-muted: #64748B;
            --bg-light: #F8FAFC;
            --white: #FFFFFF;
        }

        /* Page-level body overrides */
        body {
            background-color: var(--bg-light) !important;
            font-family: 'Inter', sans-serif !important;
        }

        .page-content {
            background-color: var(--bg-light) !important;
            padding-top: 0 !important;
        }

        /* HERO */


        .btn-premium {
            padding: 14px 40px;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none !important;
            font-size: 16px;
            transition: 0.2s;
            text-align: center;
            display: inline-block;
        }

        .btn-blue {
            background: #0056d2;
            color: white !important;
            border-bottom: 4px solid #0042a5;
        }

        .btn-orange {
            background: var(--primary-orange);
            color: white !important;
            border-bottom: 4px solid #c2410c;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }

        .trust-badges {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        @media (max-width: 991px) {
            .trust-badges {
                justify-content: center;
            }
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            background: #F1F5F9;
            padding: 8px 15px;
            border-radius: 30px;
        }


        /* FILTER / SUBJECTS NAV BAR */
        .filter-nav-bar {
            padding: 10px 8%;
            display: flex;
            align-items: center;
            gap: 15px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            margin-bottom: 20px;
        }

        .filter-nav-bar::-webkit-scrollbar {
            display: none;
        }

        .filter-btn-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid #E2E8F0;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: .2s;
        }

        .filter-btn-trigger:hover {
            border-color: #1b66c9;
            background: #f8fbff;
        }

        .subject-tablet {
            padding: 8px 20px;
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            white-space: nowrap;
            transition: .2s;
            text-decoration: none !important;
        }

        .subject-tablet:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .subject-tablet.active {
            background: #1b66c9;
            color: #fff !important;
            border-color: #1b66c9;
        }

        .showing-context {
            padding: 15px 8% 5px;
            font-size: 14px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        @media (max-width: 768px) {
            .showing-context {
                padding: 10px 15px;
                flex-direction: column;
                gap: 8px;
                border-bottom: 1px solid #f1f5f9;
            }

            .context-left {
                width: 100%;
                justify-content: center;
                flex-direction: row;
                flex-wrap: wrap;
                font-size: 13px;
                gap: 4px;
            }

            .sort-by-container {
                width: 100%;
                justify-content: center;
                padding-top: 6px;
                border-top: 1px dashed #e2e8f0;
                gap: 6px;
                font-size: 13px;
            }

            .sort-select-premium {
                padding: 0 8px;
                height: 30px;
                font-size: 12px;
                min-width: 110px;
            }
        }

        .context-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .showing-context strong {
            color: #1e293b;
            font-weight: 700;
        }

        .change-link {
            color: #1b66c9;
            font-weight: 600;
            text-decoration: none !important;
            cursor: pointer;
            margin-left: 5px;
            font-size: 13px;
        }

        .sort-by-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sort-label {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
        }

        .sort-select-premium {
            border: 1.5px solid #e2e8f0;
            background: #fff;
            padding: 5px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            outline: none;
            cursor: pointer;
            transition: .2s;
            min-width: 130px;
        }

        .sort-select-premium:focus {
            border-color: #1b66c9;
            box-shadow: 0 0 0 3px rgba(27, 102, 201, 0.08);
        }

        /* SIDEBAR FILTER (DRAWER) */
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 99998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .drawer-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .filter-drawer {
            position: fixed;
            top: 0;
            left: 0;
            width: 350px;
            height: 100%;
            background: #fff;
            z-index: 99999;
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 20px 0 50px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .filter-drawer.active {
            transform: translateX(0);
        }

        .drawer-header {
            padding: 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .drawer-header h2 {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            color: #1e293b;
            letter-spacing: -0.5px;
        }

        .drawer-close {
            cursor: pointer;
            color: #94a3b8;
            font-size: 20px;
            padding: 8px;
            line-height: 1;
            transition: .2s;
        }

        .drawer-close:hover {
            color: #1e293b;
            background: #f8fafc;
            border-radius: 50%;
        }

        .drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        /* Filter Groups */
        .filter-group {
            margin-bottom: 32px;
        }

        .filter-group label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .filter-select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #f1f5f9;
            border-radius: 12px;
            font-size: 15px;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            transition: .2s;
            font-weight: 500;
        }

        .filter-select:focus {
            border-color: #1b66c9;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(27, 102, 201, 0.08);
        }

        /* Range Slider */
        .range-container {
            padding-top: 10px;
        }

        .range-labels {
            position: relative;
            height: 20px;
            margin-top: 10px;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 600;
        }

        .range-labels span {
            position: absolute;
            top: 0;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .range-labels span:nth-child(1) {
            left: 0;
            transform: none;
        }

        .range-labels span:nth-child(2) {
            left: 25%;
        }

        .range-labels span:nth-child(3) {
            left: 50%;
        }

        .range-labels span:nth-child(4) {
            left: 100%;
            transform: translateX(-100%);
        }

        .range-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 5px;
            outline: none;
        }

        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 22px;
            height: 22px;
            background: #f97316;
            border: 4px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3);
            transition: .2s;
        }

        .range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.1);
        }

        /* Checkbox Group */
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 4px 0;
        }

        .checkbox-item input {
            width: 18px;
            height: 18px;
            border-radius: 6px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            accent-color: #1b66c9;
        }

        .checkbox-label {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            transition: .2s;
        }

        .checkbox-item:hover .checkbox-label {
            color: #1e293b;
        }

        /* Drawer Actions */
        .filter-actions {
            padding: 24px;
            border-top: 1px solid #f1f5f9;
            background: #f8fafc;
            display: flex;
            gap: 12px;
        }

        .btn-apply {
            flex: 2;
            background: #f97316;
            color: #fff !important;
            text-align: center;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none !important;
            transition: .2s;
            font-size: 15px;
            border: none;
            box-shadow: 0 8px 16px rgba(249, 115, 22, 0.2);
        }

        .btn-apply:hover {
            background: #ea580c;
            transform: translateY(-2px);
        }

        .btn-cancel {
            flex: 1;
            background: #fff;
            color: #64748b !important;
            text-align: center;
            padding: 14px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none !important;
            transition: .2s;
            font-size: 15px;
        }

        .btn-cancel:hover {
            background: #f1f5f9;
            color: #1e293b !important;
        }

        /* CARD BADGE */
        .condition {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .condition.new {
            background: #10b981;
        }

        .condition.used {
            background: #f59e0b;
        }

        /* GRID */
        .book-wall {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            padding: 0 8% 50px;
        }

        /* RESPONSIVE */

        @media(max-width:1200px) {
            .book-wall {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        @media(max-width:1000px) {
            .book-wall {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media(max-width:750px) {
            .book-wall {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media(max-width:500px) {
            .book-wall {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                padding: 0 10px 40px;
            }
        }

        /* CARD */

        .book-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: .25s;
            text-decoration: none !important;
            color: inherit;
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

        .condition {
            position: absolute;
            top: 6px;
            left: 6px;
            font-size: 10px;
            padding: 3px 7px;
            border-radius: 20px;
            color: white;
            z-index: 10;
            font-weight: 600;
            text-transform: uppercase;
        }

        .new {
            background: #22c55e;
        }

        .used {
            background: #f59e0b;
        }

        /* INFO */

        .info {
            padding: 10px;
        }

        .title {
            font-size: 12px;
            font-weight: 600;
            color: #1A1A1A;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-decoration: none !important;
            line-height: 1.4;
            /* height: 33px; */
        }

        .title:hover {
            color: #ff6b00;
        }

        .author {
            font-size: 11px;
            color: #777;
            margin-top: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* PRICE */

        .price {
            font-size: 14px;
            font-weight: 700;
            margin-top: 6px;
            color: #1A1A1A;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .original-price {
            text-decoration: line-through;
            color: #94A3B8;
            font-weight: 500;
            font-size: 0.9em;
        }

        .final-price {
            color: #1A1A1A;
        }

        .final-price.text-danger {
            color: #ef4444 !important;
        }

        /* BUTTON */

        .cart-btn {
            width: 100%;
            margin-top: 8px;
            background: #ff6b00;
            border: none;
            color: white;
            padding: 6px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
        }

        .cart-btn:hover {
            background: #e45d00;
        }

        /* SELL SECTION */
        .sell-hero-premium {
            background: white;
            padding: 60px 8%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        @media (max-width: 991px) {
            .sell-hero-premium {
                flex-direction: column;
                text-align: center;
            }

            .sell-steps-premium {
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        .sell-content-premium {
            max-width: 500px;
        }

        .sell-content-premium h2 {
            font-size: 32px;
            color: #1e3a8a;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .sell-content-premium h2 span {
            color: var(--primary-orange);
        }

        .sell-content-premium .steps-head-premium {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 30px;
            color: var(--text-muted);
        }

        .sell-steps-premium {
            display: flex;
            gap: 10px;
            margin-bottom: 35px;
        }

        .sell-step-premium {
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #1e40af;
            white-space: nowrap;
        }

        .sell-hero-premium img {
            max-width: 480px;
            width: 100%;
            border-radius: 20px;
        }

        /* BANNERS */
        .banners-premium {
            padding: 40px 8% 60px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .banner-premium-card {
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            min-height: 380px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 991px) {
            .banner-premium-card {
                flex-direction: column;
                height: auto;
            }

            .v-text-premium,
            .v-card-premium,
            .s-card-premium {
                max-width: 100% !important;
                width: 100% !important;
            }
        }

        .v-banner-premium {
            background: linear-gradient(135deg, #1e3a8a 0%, #9d174d 100%);
            padding: 50px;
            color: white;
            position: relative;
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        .v-text-premium {
            max-width: 60%;
        }

        .v-label-premium {
            background: white;
            color: var(--primary-orange);
            font-size: 11px;
            font-weight: 800;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        .v-text-premium h2 {
            font-size: 44px;
            margin-bottom: 20px;
            line-height: 1.1;
            font-weight: 700;
        }

        .v-text-premium h2 span {
            color: #facc15;
        }

        .v-points-premium {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .v-point-premium {
            background: rgba(255, 255, 255, 0.15);
            padding: 12px 20px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
        }

        .v-card-premium {
            background: white;
            border-radius: 20px;
            width: 340px;
            padding: 40px;
            color: var(--text-dark);
            text-align: left;
        }

        .v-card-premium h3 {
            font-size: 22px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .v-card-premium p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        .check-item-premium {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 13px;
            color: #475569;
        }

        .check-icon-premium {
            color: #10B981;
            font-weight: 800;
        }

        .btn-reg-premium {
            width: 100%;
            background: #0f172a;
            color: white !important;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            margin-top: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none !important;
        }

        .s-banner-premium {
            background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);
            padding: 50px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .s-card-premium {
            background: white;
            border-radius: 20px;
            width: 380px;
            padding: 40px;
            color: var(--text-dark);
            text-align: center;
        }

        .s-icon-premium {
            width: 70px;
            height: 70px;
            background: #FFEDD5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }

        .btn-join-premium {
            width: 100%;
            background: #EA580C;
            color: white !important;
            padding: 16px;
            border: none;
            border-radius: 35px;
            font-weight: 700;
            margin-top: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 15px;
            text-decoration: none !important;
        }

        /* TESTIMONIALS */
        .testimonials-premium {
            padding: 60px 8%;
            text-align: center;
        }

        .t-title-premium {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 40px;
        }

        .t-title-premium span {
            color: var(--primary-blue);
        }

        .t-grid-premium {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 768px) {
            .t-grid-premium {
                grid-template-columns: 1fr;
            }
        }

        .t-card-premium {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: left;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .t-card-premium p {
            font-size: 15px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 15px;
        }

        .t-stars-premium {
            color: var(--primary-orange);
            margin-bottom: 12px;
            font-size: 14px;
            letter-spacing: 2px;
        }

        .t-author-premium {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 600;
        }

        /* WHATSAPP */
        .whatsapp-premium {
            padding: 40px 8%;
            text-align: center;
            border-top: 1px dashed #cbd5e1;
        }

        .wa-text-premium {
            font-size: 15px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 20px;
        }

        .wa-text-premium span {
            color: #22C55E;
        }

        .wa-btn-premium {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #22C55E;
            color: white !important;
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none !important;
            font-weight: 700;
            font-size: 16px;
            transition: 0.3s;
        }

        .wa-btn-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(34, 197, 94, 0.2);
        }

        /* STUDENT MARKETPLACE SPECIFIC */
        .student-avatar {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 6px;
            border: 1px solid #e2e8f0;
        }

        .student-info {
            display: flex;
            align-items: center;
        }

        .sold-overlay {
            position: absolute;
            top: 15px;
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

        .grayscale {
            filter: grayscale(100%);
            opacity: 0.7;
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
        }

        .btn-load-more:hover {
            background: var(--primary-orange);
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-load-more:disabled {
            border-color: #eee;
            color: #ccc;
            background: #f9f9f9;
            cursor: not-allowed;
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

        .book-context {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #64748b;
            margin: 14px 0 6px;
            padding: 8px 18px;
            background: white;
            border-radius: 30px;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .change-loc-premium {
            color: #ff6a00;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .change-loc-premium:hover {
            text-decoration: underline;
        }

        .subjects-wrapper {
            position: relative;
        }

        /* left fade */
        .subjects-wrapper::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 40px;
            pointer-events: none;
            background: linear-gradient(to right, #ffffff, rgba(255, 255, 255, 0));
            z-index: 2;
        }

        /* right fade */
        .subjects-wrapper::after {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 40px;
            pointer-events: none;
            background: linear-gradient(to left, #ffffff, rgba(255, 255, 255, 0));
            z-index: 2;
        }
    </style>
    <!-- Hero Section -->
    <section class="hero">

        <div class="hero-left">

            <h1>India's Smart Marketplace for <span>Books</span></h1>

            <div class="save">Find the right book in seconds - Sell the old book in minutes.</div>

            <div class="features">

                <div class="feature">
                    <i class="fa-solid fa-check-circle"></i>
                    50,000+ Books
                </div>

                <div class="divider"></div>

                <div class="feature">
                    <i class="fa-solid fa-box"></i>
                    Used Books Up to <span style="color:#1b66c9;">70% Off</span>
                </div>

                <div class="divider"></div>

                <div class="feature">
                    <i class="fa-solid fa-truck"></i>
                    Free Delivery on Orders ₹499+
                </div>

            </div>

        </div>

        <div class="hero-right">
            <img src="{{ asset('uploads/logos/9176205_6615.svg') }}">
        </div>

    </section>

    <div class="showing-context">
        <div class="context-left">
            <span>Showing books for:</span>
            <strong id="currentSelectionInfo">
                {{ $currentSectionId ? $sections->find($currentSectionId)?->name ?? 'All Categories' : 'All Categories' }}
                @if (isset($currentSubCategoryId) && $currentSubCategoryId)
                    > {{ \App\Models\Category::find($currentSubCategoryId)?->category_name }}
                @endif
            </strong>
            <a href="javascript:void(0)" class="change-link" id="openFilter"> (Change)</a>
        </div>

        <div class="sort-by-container">
            <span class="sort-label">Sort by:</span>
            <select class="sort-select-premium" id="homeSortBy">
                <option value="all">All Books</option>
                <option value="new">New Books</option>
                <option value="used">Old Books</option>
            </select>
        </div>
    </div>

    <!-- Filter Nav Bar -->
    <div class="filter-nav-bar">
        <button class="filter-btn-trigger" id="openFilterChip">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="21" x2="4" y2="14"></line>
                <line x1="4" y1="10" x2="4" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12" y2="3"></line>
                <line x1="20" y1="21" x2="20" y2="16"></line>
                <line x1="20" y1="12" x2="20" y2="3"></line>
                <line x1="1" y1="14" x2="7" y2="14"></line>
                <line x1="9" y1="8" x2="15" y2="8"></line>
                <line x1="17" y1="16" x2="23" y2="16"></line>
            </svg>
            Filter
        </button>

        <a href="javascript:void(0)" class="subject-tablet active subject-filter-btn" data-subject-id="all"
            onclick="filterBySubject('all')">All</a>

        <div id="homeSubjectsContainer" style="display: contents;">
            @include('front.partials.home_subjects')
        </div>
    </div>

    <!-- Product Grid -->
    <div class="book-wall" id="homeProductGrid">
        @include('front.partials.home_product_grid')
    </div>

    <!-- Load More Button -->
    <div class="load-more-container" id="loadMoreContainer" {!! !$sliderProducts->hasMorePages() ? 'style="display:none;"' : '' !!}>
        <button class="btn-load-more" id="loadMoreBtn">
            <i class="fas fa-sync-alt"></i> Load More Books
        </button>
    </div>

    <!-- Sell Hero Section -->
    <section class="sell-hero-premium">
        <div class="sell-content-premium">
            <h2>Turn Your <span>Old Books into Cash</span></h2>
            <p class="steps-head-premium">Sell Used Books in 3 Easy Steps:</p>
            <div class="sell-steps-premium">
                <div class="sell-step-premium">📁 Upload Book Photo</div>
                <div class="sell-step-premium">💰 Set Your Price</div>
                <div class="sell-step-premium">💵 Get Paid</div>
            </div>
            <div class="hero-btns">
                <a href="{{ url('/search-products') }}" class="btn-premium btn-orange">Sell Your Books</a>
                <a href="{{ url('/marketplace') }}" class="btn-premium btn-blue">Browse Used Books</a>
            </div>
        </div>
        <img src="{{ asset('front/theme_illustrations/sell_books.png') }}" alt="Sell Illustration">
    </section>


    <!-- Opportunity Banners -->
    <div class="banners-premium">
        <!-- Vendor Opportunity -->
        <div class="banner-premium-card v-banner-premium">
            <div class="v-text-premium">
                <span class="v-label-premium">🤝 Vendor Opportunity</span>
                <h2 style="color: #ffffffff;">Turn Your Catalog into <span>Revenue</span></h2>
                <p style="opacity: 0.9; margin-bottom: 25px; line-height: 1.6;">Upload your books once and let BookHub
                    handle the marketing. Expand your reach with only a 5% promotion fee.</p>
                <div class="v-points-premium">
                    <div class="v-point-premium">📄 Easy Uploads</div>
                    <div class="v-point-premium">⚡ Automated Marketing</div>
                </div>
            </div>
            <div class="v-card-premium">
                <h3>Free Plan & Pro Plan</h3>
                <p>Start free with 100 books, or switch to Pro for unlimited potential.</p>
                <div class="check-list-premium">
                    <div class="check-item-premium"><span class="check-icon-premium">✓</span> No technical setup needed
                    </div>
                    <div class="check-item-premium"><span class="check-icon-premium">✓</span> We drive the traffic</div>
                    <div class="check-item-premium"><span class="check-icon-premium">✓</span> Transparent analytics</div>
                </div>
                <a href="{{ url('/vendors') }}" class="btn-reg-premium">Register Bookstore →</a>
                <a href="{{ url('/contact') }}" class="btn-reg-premium"
                    style="background: transparent; color: #64748b !important; border: 1px solid #e2e8f0;">Talk To Team</a>
            </div>
        </div>

        <!-- Sales Executive Opportunity -->
        <div class="banner-premium-card s-banner-premium">
            <div class="v-text-premium">
                <span class="v-label-premium" style="background: rgba(255,255,255,0.2); color: white;">🚀 Career
                    Opportunity</span>
                <h2 style="color: #ffffffff;">Become a BookHub <span>Sales Executive</span></h2>
                <p style="opacity: 0.9; margin-bottom: 25px; line-height: 1.6;">Help schools and institutions discover the
                    right books while earning attractive commissions, marketing support, and exclusive incentives.</p>
                <div class="v-points-premium">
                    <div class="v-point-premium">🌱 Grow network</div>
                    <div class="v-point-premium">👤 Earn more</div>
                </div>
            </div>
            <div class="s-card-premium">
                <div class="s-icon-premium">👤</div>
                <h3 style="margin-bottom: 10px;">Register Today</h3>
                <p>Fill out a short application, and our team will be in touch with you shortly.</p>
                <a href="{{ url('/sales') }}" class="btn-join-premium">Join As Sales Executive →</a>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <section class="testimonials-premium">
        <h2 class="t-title-premium">What Our <span>Students</span> Say</h2>
        <div class="t-grid-premium">
            <div class="t-card-premium">
                <p>"Got my NCERT books at half price!"</p>
                <div class="t-stars-premium">★★★★★</div>
                <div class="t-author-premium">– Ananya, Class 10</div>
            </div>
            <div class="t-card-premium">
                <p>"Sold my old engineering books easily."</p>
                <div class="t-stars-premium">★★★★★</div>
                <div class="t-author-premium">– Rahul, B.Tech Student</div>
            </div>
        </div>
    </section>



    <!-- Filter Drawer (Left Sidebar) -->
    <div class="drawer-overlay" id="modalOverlay"></div>
    <div class="filter-drawer" id="filterSheet">
        <div class="drawer-header">
            <h2>Select Filters</h2>
            <div class="drawer-close" id="closeFilter">
                <i class="fas fa-times"></i>
            </div>
        </div>

        <div class="drawer-body">
            <!-- Basic Filters (Triggered by Change) -->
            <div id="basicFiltersContent">
                <div class="filter-group">
                    <label>Category</label>
                    <select class="filter-select" id="filterSection">
                        <option value="">Select Category</option>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->id }}" {{ $currentSectionId == $sec->id ? 'selected' : '' }}>
                                {{ $sec->name }}</option>
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

        // Pre-populate filter dropdowns from saved BookGenie session on page load
        (function restoreBookGenieFilters() {
            const savedSectionId = '{{ $currentSectionId ?? '' }}';
            const savedCategoryId = '{{ $currentCategoryId ?? '' }}';
            const savedSubId = '{{ $currentSubcategoryId ?? '' }}';

            if (!savedSectionId) return;

            // Category
            if (savedCategoryId) {
                fetch(`{{ url('get-filter-categories') }}?section_id=${savedSectionId}`)
                    .then(r => r.json())
                    .then(data => {
                        const cat = document.getElementById('filterCategory');
                        cat.innerHTML = '<option value="">Select Sub Category</option>';
                        data.forEach(c => {
                            cat.innerHTML +=
                                `<option value="${c.id}" ${c.id == savedCategoryId ? 'selected' : ''}>${c.category_name}</option>`;
                        });

                        // Subcategory
                        if (savedSubId) {
                            fetch(
                                    `{{ url('get-filter-subcategories') }}?category_id=${savedCategoryId}&section_id=${savedSectionId}`
                                )
                                .then(r => r.json())
                                .then(subs => {
                                    const sub = document.getElementById('filterSubcategory');
                                    sub.innerHTML = '<option value="">Select Class</option>';
                                    subs.forEach(s => {
                                        sub.innerHTML +=
                                            `<option value="${s.id}" ${s.id == savedSubId ? 'selected' : ''}>${s.category_name}</option>`;
                                    });
                                });
                        }
                    });
            }
        })();

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
            const h2 = sheet.querySelector('.drawer-header h2');
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

            document.getElementById('homeSortBy').value = 'all';

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

            const condition = document.getElementById('homeSortBy').value;
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

        function updateHomeGrid(subjectId = null, activeSubjectName = null, stayOpen = false) {
            currentPage = 1;
            hasMore = true;
            isLoading = false;

            const gridContainer = document.getElementById('homeProductGrid');
            const subjectsContainer = document.getElementById('homeSubjectsContainer');
            const selectionInfo = document.getElementById('currentSelectionInfo');
            const condition = document.getElementById('homeSortBy').value;

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

                    const sectionText = sectionSelect.options[sectionSelect.selectedIndex].text;
                    const categoryText = categorySelect.options[categorySelect.selectedIndex].text;
                    const subcategoryText = subcategorySelect.options[subcategorySelect.selectedIndex].text;

                    let displayParts = [];
                    if (sectionId && sectionId !== '') displayParts.push(sectionText);
                    if (categoryId && categoryId !== '') displayParts.push(categoryText);
                    if (subcategoryId && subcategoryId !== '') displayParts.push(subcategoryText);

                    let displayText = displayParts.length > 0 ? displayParts.join(' > ') : 'All Categories';

                    if (activeSubjectName && activeSubjectName !== '') {
                        displayText = activeSubjectName + ' in ' + displayText;
                    }

                    // Add condition prefix if not 'all'
                    if (condition === 'new') displayText = 'New ' + displayText;
                    if (condition === 'used') displayText = 'Old ' + displayText;

                    if (selectionInfo) selectionInfo.innerText = displayText;

                    // Close the sheet (if open)
                    if (!stayOpen) {
                        toggleModal(false);
                    }
                })
                .catch(err => {
                    console.error(err);
                    gridContainer.style.opacity = '1';
                });
        }

        document.getElementById('homeSortBy').addEventListener('change', function() {
            updateHomeGrid();
        });

        document.getElementById('applyBtn').addEventListener('click', () => updateHomeGrid());

        function filterBySubject(subjectId) {
            let activeSubjectName = '';
            // Highlight active subject
            document.querySelectorAll('.subject-tablet').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.subjectId == subjectId) {
                    btn.classList.add('active');
                    activeSubjectName = btn.innerText;
                } else if (subjectId === 'all' && btn.dataset.subjectId === 'all') {
                    btn.classList.add('active');
                }
            });
            updateHomeGrid(subjectId === 'all' ? null : subjectId, activeSubjectName);
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
            updateHomeGrid(null, null, true);
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
            updateHomeGrid(null, null, true);
        });

        subcategorySelect.addEventListener('change', function() {
            updateHomeGrid(null, null, true);
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

    @if (!Auth::check() && !session()->has('bookgenie_shown'))
        <div class="modal fade" id="bookGenieModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:24px; border:none; overflow:hidden;">
                    <div
                        style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); padding: 30px; text-align: center; color: white;">
                        <h3 style="font-weight: 800; color: white; margin-bottom: 10px;">✨ BookGenie AI</h3>
                        <p style="opacity: 0.9; margin: 0; font-size: 14px;">Help BookGenie AI to personalize and display
                            books that perfectly fit you!</p>
                    </div>
                    <div class="modal-body" style="padding: 30px;">
                        <div class="mb-4">
                            <label style="font-weight: 700; color: #333; margin-bottom: 8px;">Education Level
                                (Section)</label>
                            <select id="bgSectionSelect" class="form-select"
                                style="border-radius: 12px; border: 2px solid #eee; padding: 12px; height: 50px;">
                                <option value="">Select Education Level</option>
                                @foreach ($sections as $sec)
                                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label style="font-weight: 700; color: #333; margin-bottom: 8px;">Board / Domain
                                (Category)</label>
                            <select id="bgCategorySelect" class="form-select"
                                style="border-radius: 12px; border: 2px solid #eee; padding: 12px; height: 50px;" disabled>
                                <option value="">Select Board</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label style="font-weight: 700; color: #333; margin-bottom: 8px;">Class / Stream (Sub
                                Category)</label>
                            <select id="bgSubcategorySelect" class="form-select"
                                style="border-radius: 12px; border: 2px solid #eee; padding: 12px; height: 50px;" disabled>
                                <option value="">Select Class</option>
                            </select>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="button" class="btn btn-light w-50" id="closeBookGenie"
                                style="border-radius: 12px; font-weight: 700; padding: 12px;">Skip</button>
                            <button type="button" class="btn text-white w-50" id="applyBookGenie"
                                style="background: var(--primary-orange); border-radius: 12px; font-weight: 700; padding: 12px;">Personalize
                                <i class="fas fa-magic ms-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Show modal automatically
                var bookGenieModal = new bootstrap.Modal(document.getElementById('bookGenieModal'));
                bookGenieModal.show();

                // Cascading selects for BookGenie
                const bgSection = document.getElementById('bgSectionSelect');
                const bgCategory = document.getElementById('bgCategorySelect');
                const bgSubcategory = document.getElementById('bgSubcategorySelect');

                bgSection.addEventListener('change', function() {
                    const sectionId = this.value;
                    if (sectionId) {
                        bgCategory.innerHTML = '<option value="">Loading...</option>';
                        bgCategory.disabled = true;
                        fetch(`{{ url('get-filter-categories') }}?section_id=${sectionId}`)
                            .then(res => res.json())
                            .then(data => {
                                bgCategory.innerHTML = '<option value="">Select Board</option>';
                                data.forEach(cat => {
                                    bgCategory.innerHTML +=
                                        `<option value="${cat.id}">${cat.category_name}</option>`;
                                });
                                bgCategory.disabled = false;
                            });
                    } else {
                        bgCategory.innerHTML = '<option value="">Select Board</option>';
                        bgCategory.disabled = true;
                        bgSubcategory.innerHTML = '<option value="">Select Class</option>';
                        bgSubcategory.disabled = true;
                    }
                });

                bgCategory.addEventListener('change', function() {
                    const categoryId = this.value;
                    const sectionId = bgSection.value;
                    if (categoryId) {
                        bgSubcategory.innerHTML = '<option value="">Loading...</option>';
                        bgSubcategory.disabled = true;
                        fetch(
                                `{{ url('get-filter-subcategories') }}?category_id=${categoryId}&section_id=${sectionId}`
                            )
                            .then(res => res.json())
                            .then(data => {
                                bgSubcategory.innerHTML = '<option value="">Select Class</option>';
                                data.forEach(sub => {
                                    bgSubcategory.innerHTML +=
                                        `<option value="${sub.id}">${sub.category_name}</option>`;
                                });
                                bgSubcategory.disabled = false;
                            });
                    } else {
                        bgSubcategory.innerHTML = '<option value="">Select Class</option>';
                        bgSubcategory.disabled = true;
                    }
                });

                function dismissBookGenie(withData = false) {
                    bookGenieModal.hide();

                    let payload = {
                        bookgenie_shown: true
                    };

                    if (withData) {
                        payload.section_id = bgSection.value;
                        payload.category_id = bgCategory.value;
                        payload.subcategory_id = bgSubcategory.value;
                    }

                    fetch(`{{ url('set-bookgenie-session') }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                }

                document.getElementById('closeBookGenie').addEventListener('click', function() {
                    dismissBookGenie(false);
                });

                document.getElementById('applyBookGenie').addEventListener('click', function() {
                    // Set the hidden filter bottom sheet values
                    document.getElementById('filterSection').value = bgSection.value;

                    document.getElementById('filterCategory').innerHTML = bgCategory.innerHTML;
                    document.getElementById('filterCategory').value = bgCategory.value;

                    document.getElementById('filterSubcategory').innerHTML = bgSubcategory.innerHTML;
                    document.getElementById('filterSubcategory').value = bgSubcategory.value;

                    // Perform AJAX update
                    if (typeof updateHomeGrid === 'function') {
                        updateHomeGrid(null, null, true);
                    }

                    dismissBookGenie(true);
                });
            });
        </script>
    @endif
@endsection
