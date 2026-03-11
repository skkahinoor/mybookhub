@extends('front.layout.layout3')

@section('content')
<style>
    :root {
        --orange: #FF6B00;
        --blue: #2979FF;
        --dark: #1A1A1A;
        --muted: #8E8E93;
        --bg: #F8F9FB;
    }

    .mp-page {
        background: var(--bg);
        padding-bottom: 60px;
    }

    /* ── Hero Header ── */
    .mp-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #2563eb 100%);
        padding: 55px 0 45px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .mp-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at top right, rgba(255,255,255,0.08) 0%, transparent 60%);
    }

    .mp-hero h1 {
        font-size: 34px;
        font-weight: 900;
        color: #fff;
        margin-bottom: 10px;
        position: relative;
    }

    .mp-hero h1 span {
        color: #fbbf24;
    }

    .mp-hero p {
        color: rgba(255,255,255,0.75);
        font-size: 15px;
        max-width: 560px;
        margin: 0 auto 0;
        position: relative;
    }

    .mp-hero-badges {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 24px;
        position: relative;
        flex-wrap: wrap;
    }

    .mp-hero-badge {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 30px;
        padding: 7px 18px;
        font-size: 13px;
        color: #fff;
        font-weight: 600;
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .mp-hero-badge i {
        color: #fbbf24;
    }

    /* ── Grid ── */
    .mp-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 24px;
        padding: 40px 0;
    }

    /* ── Book Card ── */
    .mp-book-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.04);
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(.2,.8,.2,1);
        position: relative;
    }

    .mp-book-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 20px 45px rgba(0,0,0,0.12);
    }

    .mp-img-wrap {
        width: 100%;
        height: 220px;
        overflow: hidden;
        position: relative;
        background: #f3f4f6;
    }

    .mp-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .mp-book-card:hover .mp-img-wrap img {
        transform: scale(1.07);
    }

    /* Sold Banner */
    .mp-sold-banner {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .mp-sold-badge {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        padding: 8px 28px;
        border-radius: 40px;
        font-weight: 900;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 8px 20px rgba(239,68,68,0.4);
    }

    /* Condition pill */
    .mp-condition {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 3;
        padding: 4px 11px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .mp-condition.excellent { background: #dcfce7; color: #16a34a; }
    .mp-condition.good      { background: #d1fae5; color: #059669; }
    .mp-condition.fair      { background: #fef3c7; color: #d97706; }
    .mp-condition.poor      { background: #fee2e2; color: #dc2626; }
    .mp-condition.default   { background: #e0e7ff; color: #4f46e5; }

    /* Card Body */
    .mp-card-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .mp-seller-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }

    .mp-seller-avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid #e5e7eb;
    }

    .mp-seller-name {
        font-size: 12px;
        color: var(--muted);
        font-weight: 600;
        flex: 1;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .mp-book-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--dark);
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 40px;
        margin-bottom: 2px;
    }

    .mp-book-subtitle {
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 12px;
    }

    .mp-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
    }

    .mp-price {
        font-size: 19px;
        font-weight: 800;
        color: var(--dark);
    }

    .mp-price small {
        font-size: 12px;
        color: var(--muted);
        font-weight: 500;
    }

    .btn-buy-now {
        background: var(--orange);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .btn-buy-now:hover {
        background: #e55d00;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-sold-text {
        font-size: 12px;
        color: #ef4444;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Empty state */
    .mp-empty {
        text-align: center;
        padding: 70px 20px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }

    .mp-empty .empty-icon {
        width: 90px;
        height: 90px;
        background: #eff6ff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: var(--blue);
        margin-bottom: 20px;
    }

    .mp-empty h3 {
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 8px;
    }

    .mp-empty p {
        color: var(--muted);
        font-size: 15px;
    }

    /* Pagination override */
    .mp-pagination {
        display: flex;
        justify-content: center;
        padding-bottom: 20px;
    }
</style>

<div class="mp-page">

    {{-- Hero --}}
    <div class="mp-hero">
        <div class="container">
            <h1>Student <span>Marketplace</span></h1>
            <p>Buy pre-loved academic books directly from fellow students at great prices. Safe, simple, peer-to-peer.</p>
            <div class="mp-hero-badges">
                <div class="mp-hero-badge"><i class="fas fa-handshake"></i> Peer to Peer</div>
                <div class="mp-hero-badge"><i class="fas fa-tag"></i> Great Deals</div>
                <div class="mp-hero-badge"><i class="fas fa-graduation-cap"></i> Academic Books</div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="mp-grid">
            @forelse($sellBookRequests as $sbook)
                @php
                    $cond = strtolower($sbook->product->condition ?? '');
                    $condClass = in_array($cond, ['excellent','good','fair','poor']) ? $cond : 'default';
                @endphp

                <div class="mp-book-card">
                    <div class="mp-img-wrap">
                        <img src="{{ $sbook->product->product_image ? asset('front/images/product_images/small/'.$sbook->product->product_image) : asset('front/images/product/default.jpg') }}"
                             alt="{{ $sbook->product->product_name }}"
                             class="{{ $sbook->stock == 0 ? 'grayscale' : '' }}"
                             style="{{ $sbook->stock == 0 ? 'filter:grayscale(80%)' : '' }}">

                        @if($sbook->stock == 0)
                            <div class="mp-sold-banner">
                                <div class="mp-sold-badge">Sold Out</div>
                            </div>
                        @endif

                        <span class="mp-condition {{ $condClass }}">{{ strtoupper($cond) }}</span>
                    </div>

                    <div class="mp-card-body">
                        <div class="mp-seller-row">
                            <img src="{{ $sbook->user->image ? asset('front/images/users/'.$sbook->user->image) : asset('assets/images/avatar.png') }}"
                                 class="mp-seller-avatar" alt="Seller">
                            <span class="mp-seller-name">{{ $sbook->user->name ?? 'Unknown' }}</span>
                        </div>

                        <div class="mp-book-title">{{ $sbook->product->product_name }}</div>
                        @if($sbook->product->authors->isNotEmpty())
                            <div class="mp-book-subtitle">{{ $sbook->product->authors->pluck('name')->implode(', ') }}</div>
                        @endif

                        <div class="mp-card-footer">
                            <div class="mp-price">
                                ₹{{ number_format($sbook->product->product_price, 0) }}
                                <small>/book</small>
                            </div>

                            @if($sbook->stock > 0)
                                <a href="{{ route('marketplace.detail', $sbook->id) }}" class="btn-buy-now">
                                    <i class="fas fa-bolt"></i> Buy Now
                                </a>
                            @else
                                <span class="btn-sold-text">Sold Out</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1;">
                    <div class="mp-empty">
                        <div class="empty-icon"><i class="fas fa-book-open"></i></div>
                        <h3>No Books Listed Yet</h3>
                        <p>No books are currently available in the student marketplace. Check back soon!</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mp-pagination">
            {{ $sellBookRequests->links() }}
        </div>
    </div>
</div>
@endsection
