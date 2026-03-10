@extends('front.layout.layout3')

@section('content')
    <style>
    .hero{
position:relative;
padding:80px 8%;
display:flex;
align-items:center;
justify-content:space-between;
overflow:hidden;
background:linear-gradient(180deg,#EEF5FD 0%,#E6F0FA 100%);
border-bottom-left-radius:60px;
border-bottom-right-radius:60px;
min-height: 520px;
gap:60px;
}

/* Background Circle */

.hero::before{
content:"";
position:absolute;
width:600px;
height:600px;
background:#dde7f3;
border-radius:50%;
right:-200px;
top:-150px;
z-index:0;
}

/* Floating Shape */

.hero::after{
content:"";
position:absolute;
width:90px;
height:30px;
background:#c7d9f2;
border-radius:20px;
right:420px;
top:120px;
transform:rotate(25deg);
animation:float 4s ease-in-out infinite;
}

/* Animation */

@keyframes float{
0%{transform:translateY(0px) rotate(25deg);}
50%{transform:translateY(-10px) rotate(25deg);}
100%{transform:translateY(0px) rotate(25deg);}
}

/* LEFT CONTENT */

.hero-left{
max-width:600px;
z-index:1;
}

.hero h1{
font-size:44px;
font-weight:700;
letter-spacing:-0.5px;
color:#2c3e50;
margin-bottom:20px;
line-height:1.25;
}

.hero h1 span{
color:#1b66c9;
font-size:1.2em;
font-weight:700;
}

.sub{
font-size:20px;
margin-bottom:10px;
color:#3c4a5a;
}

.save{
margin-bottom:25px;
color:#6b7b8c;
}

/* SEARCH BAR */

.search-box{
display:flex;
background:white;
border-radius:10px;
overflow:hidden;
box-shadow:0 10px 20px rgba(0,0,0,0.1);
margin-bottom:25px;
transition:.3s;
}

.search-box:hover{
transform:translateY(-2px);
box-shadow:0 12px 25px rgba(0,0,0,0.15);
}

.search-box input{
flex:1;
border:none;
padding:16px;
font-size:16px;
outline:none;
}

.search-btn{
background:#1b66c9;
color:white;
border:none;
padding:0 24px;
cursor:pointer;
font-size:18px;
}

/* BUTTONS */

.buttons{
display:flex;
gap:16px;
margin-top:25px;
}

.btn{
padding:16px 32px;
font-size:16px;
border-radius:10px;
border:none;
font-size:18px;
font-weight:600;
cursor:pointer;
transition:.3s;
}

.buy{
background:#1b66c9;
color:white;
box-shadow:0 8px 18px rgba(0,0,0,0.2);
}

.buy:hover{
transform:translateY(-3px);
}

.sell{
background:#ff6a00;
color:white;
box-shadow:0 8px 18px rgba(0,0,0,0.2);
}

.sell:hover{
transform:translateY(-3px);
}

/* RIGHT IMAGE */

.hero-right{
z-index:1;
}

.hero-right img{
max-width:450px;
height:auto;
width:450px;
animation:float2 6s ease-in-out infinite;
}

@keyframes float2{
0%{transform:translateY(0);}
50%{transform:translateY(-15px);}
100%{transform:translateY(0);}
}

/* FEATURE BAR */

.features{
margin-top:35px;

display:flex;
align-items:center;
gap:30px;

background:#ffffff;
padding:16px 32px;

border-radius:20px;
width:fit-content;

box-shadow:0 12px 28px rgba(0,0,0,0.08);

}

.feature{
display:flex;
align-items:center;
gap:10px;

font-weight:600;
font-size:15px;

color:#2c3e50;
white-space:nowrap;
}

.feature i{
color:#1b66c9;
font-size:18px;
}

.divider{
height:20px;
width:1px;
background:#e5e7eb;
}
.feature:hover{
transform:translateY(-2px);
transition:0.2s;
}

/* RESPONSIVE */

@media(max-width:900px){

.hero{
flex-direction:column;
text-align:center;
}

.hero-right img{
max-width:420px;
margin-top:40px;
}

.features{
width:100%;
justify-content:center;
flex-wrap:wrap;
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


        /* SUBJECTS */
        .subjects-strip {
    background: var(--white);
    padding: 20px 8%;
    display: flex;
    gap: 20px;
    overflow-x: auto;
    scrollbar-width: none;
    border-top: 1px solid #f1f5f9;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);

    /* smoother scrolling */
    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;
}

.subjects-strip::-webkit-scrollbar {
    display: none;
}

.subject-item-premium {
    min-width: 100px;
    text-align: center;
    text-decoration: none !important;
    scroll-snap-align: start;
    cursor: pointer;
}

.subject-circle-premium {
    width: 74px;
    height: 74px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 28px;
    transition: all .25s ease;
    box-shadow:0 2px 6px rgba(0,0,0,0.04);
}

.subject-item-premium span {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
}

/* Hover effect */
.subject-item-premium:hover .subject-circle-premium {
transform:translateY(-6px) scale(1.05);
border-color:var(--primary-blue);
background:#f1f6ff;
box-shadow:0 12px 24px rgba(0,0,0,0.12);
}
/* Active subject */
.subject-item-premium.active .subject-circle-premium {
    border: 2px solid var(--primary-blue);
    background: #eef4ff;
}
.subjects-strip{
cursor:grab;
}

.subjects-strip:active{
cursor:grabbing;
}
.subject-item-premium.active span {
    color: var(--primary-blue);
}
        /* FILTER */
        .filter-section-premium {
            padding: 30px 8% 20px;
        }

        .location-info-premium {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #94A3B8;
            margin-bottom: 20px;
        }

        .change-loc-premium {
            color: var(--primary-orange);
            text-decoration: none !important;
            font-weight: 700;
        }

        .pills-premium {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .pill-premium {
            padding: 8px 25px;
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            text-decoration: none !important;
            border-bottom: 2px solid #e2e8f0;
        }

        .pill-premium.active {
            background: var(--primary-orange);
            color: white !important;
            border-color: var(--primary-orange);
            border-bottom: 2px solid #c2410c;
        }

        .pill-premium.filter-btn-premium {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* GRID */
        .grid-premium {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            padding: 0 8% 50px;
        }

        @media (max-width: 1200px) {
            .grid-premium {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 900px) {
            .grid-premium {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 600px) {
            .grid-premium {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                padding: 0 10px 40px;
            }
        }

        .card-premium {
            background: white;
            border-radius: 12px;
            padding: 12px;
            position: relative;
            transition: 0.3s;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            height: 100%;
            text-decoration: none !important;
            color: inherit;
        }

        .card-premium:hover{
transform:translateY(-6px) scale(1.01);
box-shadow:0 18px 35px rgba(0,0,0,0.12);
}

        .p-badge-premium {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 3px;
            color: white;
            z-index: 10;
            text-transform: uppercase;
        }

        .b-bestseller-premium {
            background: #F97316;
        }

        .b-discount-premium {
            background: #F97316;
        }

        .b-used-premium {
            background: #14B8A6;
        }

        .card-img-premium {
            width: 100%;
            height:200px;
            background:#f8fafc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            overflow: hidden;
            padding: 12px;
        }

        .card-img-premium img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .card-title-premium {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 40px;
        }

        .card-stars-premium {
            color: #FBBF24;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .card-price-premium {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-orange);
            margin-bottom: 15px;
            margin-top: auto;
        }

        .btn-cart-premium {
            width: 100%;
            padding: 10px;
            background: #0056D2;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: 0.2s;
        }

        .btn-cart-premium:hover {
            background: #0042a5;
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

        /* Filter Sheet - Keep functional styles */
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

        .modal-overlay.active {
            display: block;
            opacity: 1;
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

        .filter-bottom-sheet.active {
            transform: translateY(0);
            visibility: visible;
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

        .filter-body {
            overflow-y: auto;
            padding-right: 5px;
            flex: 1;
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
            position: relative;
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
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 12px;
            margin-top: 15px;
            border-top: 1px solid #F0F0F0;
            padding-top: 20px;
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
        }

        .btn-apply {
            background: var(--primary-orange);
            color: #fff !important;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            text-align: center;
            text-decoration: none !important;
            box-shadow: 0 8px 20px rgba(255, 107, 0, 0.25);
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
      .book-context{
display:flex;
justify-content:center;
align-items:center;
gap:10px;
font-size:14px;
color:#64748b;
margin:14px 0 6px;
padding:8px 18px;
background:white;
border-radius:30px;
width:fit-content;
margin-left:auto;
margin-right:auto;
box-shadow:0 3px 10px rgba(0,0,0,0.05);
}
.change-loc-premium{
color:#ff6a00;
font-weight:600;
text-decoration:none;
cursor:pointer;
}
.change-loc-premium:hover{
text-decoration:underline;
}
.subjects-wrapper{
    position: relative;
}

/* left fade */
.subjects-wrapper::before{
    content:"";
    position:absolute;
    left:0;
    top:0;
    height:100%;
    width:40px;
    pointer-events:none;
    background:linear-gradient(to right, #ffffff, rgba(255,255,255,0));
    z-index:2;
}

/* right fade */
.subjects-wrapper::after{
    content:"";
    position:absolute;
    right:0;
    top:0;
    height:100%;
    width:40px;
    pointer-events:none;
    background:linear-gradient(to left, #ffffff, rgba(255,255,255,0));
    z-index:2;
}
    </style>
    <!-- Hero Section -->
    <section class="hero">

<div class="hero-left">

<h1>India's Smart Marketplace for <span>Books</span></h1>

<div class="sub">Buy • Sell • Rent • Print Notes</div>

<div class="save">Save up to 70% on textbooks</div>

<div class="buttons">
<button class="btn buy">Buy Books</button>
<button class="btn sell">Sell Old Books</button>
</div>

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

<div class="book-context">
    <span>Showing books for: <strong>All</strong></span>

    <a href="javascript:void(0)" class="change-loc-premium" id="openFilter">
        Change
    </a>
</div>
    <!-- Subjects Strip -->
    <div class="subjects-wrapper">
        <div class="subjects-strip" id="homeSubjectsContainer">
        @include('front.partials.home_subjects')
    </div>
</div>
    <!-- Filter Section -->
    <div class="filter-section-premium">
        

        <div class="pills-premium">
            <a href="javascript:void(0)" class="pill-premium filter-btn-premium" id="openFilterChip">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg> Filter
            </a>
            <a href="javascript:void(0)" class="pill-premium condition-chip {{ $condition == 'all' ? 'active' : '' }}"
                data-condition="all">All</a>
            <a href="javascript:void(0)" class="pill-premium condition-chip {{ $condition == 'new' ? 'active' : '' }}"
                data-condition="new">New</a>
            <a href="javascript:void(0)" class="pill-premium condition-chip {{ $condition == 'old' ? 'active' : '' }}"
                data-condition="old">Old</a>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="grid-premium" id="homeProductGrid">
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

    <!-- Student Marketplace (Community) Section -->
    @if (isset($sellBookRequests) && $sellBookRequests->count() > 0)
        <div class="filter-section-premium pb-0">
            <div class="location-info-premium mb-4">
                <h2 style="font-size: 24px; font-weight: 700; color: #1e3a8a; margin: 0;">Student <span>Marketplace</span>
                </h2>
                <a href="{{ route('marketplace') }}" class="change-loc-premium">View All</a>
            </div>
        </div>
        <div class="grid-premium" id="studentMarketplaceGrid">
            @foreach ($sellBookRequests as $sbook)
                <div class="card-premium">
                    @if ($sbook->book_status == 'sold')
                        <div class="sold-overlay">Sold</div>
                    @endif
                    <a href="{{ route('marketplace.detail', $sbook->id) }}" class="card-img-premium">
                        <img src="{{ asset($sbook->book_image ?? 'front/images/product/default.jpg') }}"
                            class="{{ $sbook->book_status == 'sold' ? 'grayscale' : '' }}"
                            alt="{{ $sbook->book_title }}">
                    </a>
                    <div class="student-info mb-2">
                        <img src="{{ asset($sbook->user->profile_image ?? 'assets/images/avatar.png') }}"
                            class="student-avatar" alt="User">
                        <span class="student-name">By {{ $sbook->user->name ?? 'not' }}</span>
                        <span class="ms-auto condition-tag">{{ strtoupper($sbook->book_condition) }}</span>
                    </div>
                    <a href="{{ route('marketplace.detail', $sbook->id) }}" class="card-title-premium text-dark"
                        style="text-decoration: none;">
                        {{ $sbook->book_title }}
                    </a>
                    <div class="card-stars-premium">★★★★★</div>
                    <div class="card-price-premium">₹{{ number_format($sbook->expected_price, 0) }}</div>

                    <a href="{{ route('marketplace.detail', $sbook->id) }}" class="btn-cart-premium"
                        style="background: {{ $sbook->book_status == 'sold' ? '#64748B' : '#0056D2' }}; text-decoration: none;">
                        {{ $sbook->book_status == 'sold' ? 'Sold Out' : 'View Details' }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Opportunity Banners -->
    <div class="banners-premium">
        <!-- Vendor Opportunity -->
        <div class="banner-premium-card v-banner-premium">
            <div class="v-text-premium">
                <span class="v-label-premium">🤝 Vendor Opportunity</span>
                <h2>Turn Your Catalog into <span>Revenue</span></h2>
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
                <h2>Become a BookHub <span>Sales Executive</span></h2>
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

    <!-- WhatsApp CTA -->
    <div class="whatsapp-premium">
        <p class="wa-text-premium">Need Books Quickly? Order on <span>WhatsApp</span></p>
        <a href="https://wa.me/91XXXXXXXXXX" target="_blank" class="wa-btn-premium">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z">
                </path>
            </svg>
            Chat on WhatsApp
        </a>
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

        function updateHomeGrid(subjectId = null, activeSubjectName = null, stayOpen = false) {
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
                    if (!stayOpen) {
                        toggleModal(false);
                    }
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
@endsection
