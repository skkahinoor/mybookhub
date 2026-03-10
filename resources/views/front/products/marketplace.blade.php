@extends('front.layout.layout3')

@section('content')
<style>
    :root {
        --primary-orange: #FF6B00;
        --text-dark: #1A1A1A;
        --text-muted: #8E8E93;
        --bg-light: #F8F9FB;
    }

    body {
        background-color: var(--bg-light) !important;
    }

    .marketplace-header {
        padding: 60px 0 40px;
        text-align: center;
        background: white;
        margin-bottom: 40px;
        border-radius: 0 0 40px 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }

    .marketplace-header h1 {
        font-size: 36px;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .marketplace-header p {
        color: var(--text-muted);
        font-size: 16px;
        max-width: 600px;
        margin: 0 auto;
    }

    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        padding-bottom: 60px;
    }

    .student-book-card {
        background: #fff;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        gap: 15px;
        border: 1px solid rgba(0,0,0,0.02);
        transition: all 0.3s ease;
        position: relative;
        height: 100%;
    }

    .student-book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.08);
    }

    .student-book-img-wrapper {
        width: 100%;
        height: 220px;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }

    .student-book-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #f8f8f8;
        transition: transform 0.5s ease;
    }

    .student-book-card:hover .student-book-img {
        transform: scale(1.1);
    }

    .sold-overlay {
        position: absolute;
        top: 20px;
        right: 0;
        background: #ff4757;
        color: white;
        padding: 6px 18px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        border-radius: 20px 0 0 20px;
        z-index: 2;
        box-shadow: 0 4px 10px rgba(255, 71, 87, 0.3);
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .student-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #eee;
    }

    .student-name {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .condition-tag {
        font-size: 11px;
        padding: 4px 10px;
        background: #f0f7ff;
        color: #007aff;
        border-radius: 8px;
        font-weight: 700;
    }

    .student-book-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 46px;
    }

    .student-book-price {
        font-size: 20px;
        font-weight: 800;
        color: var(--primary-orange);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }

    .btn-details {
        background: #f2f2f7;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13px;
        color: var(--text-dark);
        padding: 8px 16px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-details:hover {
        background: var(--text-dark);
        color: white;
    }
    
    .grayscale {
        filter: grayscale(100%);
    }
</style>

<div class="marketplace-header">
    <div class="container">
        <h1>Student <span>Marketplace</span></h1>
        <p>Buy directly from other students within your community. Find great deals on second-hand academic books and novels.</p>
    </div>
</div>

<div class="container">
    <div class="book-grid">
        @forelse($sellBookRequests as $sbook)
        <div class="student-book-card">
            @if($sbook->book_status == 'sold')
                <div class="sold-overlay">Sold Out</div>
            @endif
            
            <div class="student-book-img-wrapper">
                <img src="{{ asset($sbook->book_image ?? 'front/images/product/default.jpg') }}" 
                     class="student-book-img {{ $sbook->book_status == 'sold' ? 'grayscale' : '' }}" 
                     alt="{{ $sbook->book_title }}">
            </div>
            
            <div class="student-info">
                <img src="{{ asset($sbook->user->profile_image ?? 'assets/images/avatar.png') }}" class="student-avatar" alt="User">
                <span class="student-name">By {{ $sbook->user->name ?? 'Unknown' }}</span>
                <span class="ms-auto condition-tag">{{ $sbook->book_condition }}</span>
            </div>
            
            <h3 class="student-book-title">{{ $sbook->book_title }}</h3>
            
            <div class="student-book-price">
                <span>₹{{ number_format($sbook->expected_price, 2) }}</span>
                @if($sbook->book_status != 'sold')
                    <a href="{{ route('marketplace.detail', $sbook->id) }}" class="btn-details">Buy Now</a>
                @else
                    <span style="font-size: 13px; color: #ff4757; font-weight: 700;">SOLD OUT</span>
                @endif
            </div>
        </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
                <h3 class="text-muted">No books available in the marketplace currently.</h3>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mb-5">
        {{ $sellBookRequests->links() }}
    </div>
</div>

@endsection
