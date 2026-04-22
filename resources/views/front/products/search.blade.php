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

    .search-page-wrapper {
        background: var(--bg);
        min-height: 80vh;
        padding: 30px 0 60px;
    }

    /* ── Header Bar ── */
    .search-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .search-topbar h4 {
        font-size: 20px;
        font-weight: 800;
        color: var(--dark);
        margin: 0;
    }

    .search-topbar .result-sub {
        font-size: 14px;
        color: var(--muted);
        margin-top: 2px;
    }

    /* ── Premium Product Card ── */
    .prod-card {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(.2,.8,.2,1);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .prod-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.10);
    }

    .prod-card-img-wrap {
        position: relative;
        width: 100%;
        height: 210px;
        overflow: hidden;
        background: #f3f4f6;
    }

    .prod-card-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .prod-card:hover .prod-card-img-wrap img {
        transform: scale(1.06);
    }

    .prod-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .prod-badge.new {
        background: #dcfce7;
        color: #16a34a;
    }

    .prod-badge.old {
        background: #fef3c7;
        color: #d97706;
    }

    .prod-discount-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        padding: 4px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .prod-card-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .prod-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--dark);
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 40px;
        margin-bottom: 6px;
    }

    .prod-author {
        font-size: 12px;
        color: var(--orange);
        font-weight: 600;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .prod-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
    }

    .prod-price {
        display: flex;
        flex-direction: column;
    }

    .prod-price .final {
        font-size: 18px;
        font-weight: 800;
        color: var(--dark);
    }

    .prod-price .original {
        font-size: 11px;
        color: var(--muted);
        text-decoration: line-through;
    }

    .btn-add-to-cart {
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
    }

    .btn-add-to-cart:hover {
        background: #e55d00;
        color: #fff;
        transform: translateY(-1px);
    }

    /* ── Grid ── */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 22px;
    }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }

    .empty-state .empty-icon {
        width: 80px;
        height: 80px;
        background: #fff5eb;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: var(--orange);
        margin-bottom: 20px;
    }

    .empty-state h4 {
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 8px;
    }

    .empty-state p {
        color: var(--muted);
        font-size: 14px;
    }

    /* ── Request Form ── */
    .request-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.04);
    }

    .request-card-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #2979FF 100%);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .request-card-header h5 {
        color: #fff;
        margin: 0;
        font-weight: 700;
        font-size: 16px;
    }

    .request-card-header .icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }

    .request-card-body {
        padding: 24px;
    }

    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: var(--dark);
    }

    .form-control {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        font-size: 14px;
        padding: 10px 14px;
    }

    .form-control:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
    }

    .btn-submit-request {
        background: linear-gradient(135deg, #FF6B00, #ff8c00);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 700;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-submit-request:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        color: #fff;
    }

    /* Guest notice */
    .guest-notice {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }

    .guest-notice i {
        color: #3b82f6;
        font-size: 18px;
    }

    .guest-notice p {
        margin: 0;
        font-size: 13px;
        color: #1e40af;
    }

    .guest-notice a {
        color: #1d4ed8;
        font-weight: 700;
    }
</style>

<div class="search-page-wrapper">
    <div class="container">

        {{-- Guest notice --}}
        @guest
            <div class="guest-notice">
                <i class="fas fa-info-circle"></i>
                <p>Please <a href="{{ route('student.login') }}">login</a> or <a href="{{ route('student.register') }}">register</a> to request books not available in our store.</p>
            </div>
        @endguest

        {{-- Top bar --}}
        <div class="search-topbar">
            <div>
                <h4>Search Results</h4>
                <div class="result-sub">
                    Found <strong>{{ $products->total() }}</strong> result{{ $products->total() != 1 ? 's' : '' }}
                    @if(request('search'))
                        for "<strong>{{ request('search') }}</strong>"
                    @endif
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        @if($products->total() > 0)
            <div class="products-grid mb-5">
                @foreach($products as $product)
                    @php
                        $originalPrice  = (float) $product->product_price;
                        $finalPrice     = \App\Models\Product::getDiscountPrice($product->id);
                        $discountPercent = 0;
                        if ($originalPrice > 0 && $finalPrice < $originalPrice) {
                            $discountPercent = round((($originalPrice - $finalPrice) / $originalPrice) * 100);
                        }
                        $authors = $product->authors->pluck('name')->implode(', ');
                    @endphp

                    <div class="prod-card">
                        <div class="prod-card-img-wrap">
                            <a href="{{ url('product/' . $product->id) }}">
                                <img
                                    src="{{ asset('front/images/product_images/small/' . $product->product_image) }}"
                                    alt="{{ $product->product_name }}"
                                    onerror="this.src='{{ asset('front/images/product_images/small/no-image.png') }}'">
                            </a>
                            <span class="prod-badge {{ $product->condition == 'new' ? 'new' : 'old' }}">
                                {{ ucfirst($product->condition) }}
                            </span>
                            @if($discountPercent > 0)
                                <span class="prod-discount-badge">-{{ $discountPercent }}%</span>
                            @endif
                        </div>

                        <div class="prod-card-body">
                            <div class="prod-card-title">{{ $product->product_name }}</div>
                            @if($authors)
                                <div class="prod-author">{{ $authors }}</div>
                            @endif

                            <div class="prod-card-footer">
                                <div class="prod-price">
                                    <span class="final">₹{{ number_format($finalPrice, 0) }}</span>
                                    @if($discountPercent > 0)
                                        <span class="original">₹{{ number_format($originalPrice, 0) }}</span>
                                    @endif
                                </div>
                                <a href="{{ url('product/' . $product->id) }}" class="btn-add-to-cart">
                                    <i class="fas fa-shopping-cart"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-2 mb-5">
                {{ $products->links() }}
            </div>

        @else
            {{-- Empty State --}}
            <div class="empty-state mb-5">
                <div class="empty-icon"><i class="fas fa-search"></i></div>
                <h4>No books found</h4>
                <p>We couldn't find any books matching your search.
                    @auth Try requesting the book below. @else Login to request it. @endauth
                </p>
            </div>
        @endif

        {{-- Book Request Form --}}
        @auth
            @if($products->total() == 0)
                <div class="request-card mt-2">
                    <div class="request-card-header">
                        <div class="icon-wrap"><i class="fas fa-book"></i></div>
                        <h5>Can't find your book? Request it!</h5>
                    </div>
                    <div class="request-card-body">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px;">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger" style="border-radius:12px;">
                                <ul class="mb-0" style="font-size:0.9rem;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('student.book.request.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_location" id="user_location" value="{{ old('user_location') }}">
                            <input type="hidden" name="user_location_name" id="user_location_name" value="{{ old('user_location_name') }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="book_title" class="form-label">Book Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('book_title') is-invalid @enderror"
                                           id="book_title" name="book_title" placeholder="Enter book title"
                                           value="{{ old('book_title', request('search')) }}" required>
                                    @error('book_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="author_name" class="form-label">Author Name <small class="text-muted fw-normal">(Optional)</small></label>
                                    <input type="text" class="form-control @error('author_name') is-invalid @enderror"
                                           id="author_name" name="author_name" placeholder="Enter author name"
                                           value="{{ old('author_name') }}">
                                    @error('author_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="message" class="form-label">Additional Message <small class="text-muted fw-normal">(Optional)</small></label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          id="message" name="message" rows="3"
                                          placeholder="Any additional details about the book...">{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn-submit-request">
                                    <i class="fas fa-paper-plane"></i> Submit Request
                                </button>
                                <a href="{{ route('student.book.indexrequest') }}" class="text-decoration-none" style="font-size:13px; font-weight:600; color:#666;">
                                    View My Requests <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endauth

    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const locEl = document.getElementById('user_location');
        const locNameEl = document.getElementById('user_location_name');
        const statusEl = document.getElementById('location-status-search');
        if (!locEl || !locNameEl) return;

        function setLocation(lat, lng) {
            locEl.value = `${lat},${lng}`;
        }

        function setLocationName(name) {
            if (!locNameEl.value) locNameEl.value = name || '';
            if (statusEl) {
                statusEl.innerHTML = '<i class="fas fa-check-circle text-success"></i> Location detected';
                statusEl.classList.remove('text-muted');
                statusEl.classList.add('text-success');
            }
        }

        if (!('geolocation' in navigator)) {
            if (statusEl) statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> Geolocation not supported';
            return;
        }

        navigator.geolocation.getCurrentPosition(async function (pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            setLocation(lat, lng);

            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Fetch failed');
                const data = await res.json();
                if (data && data.display_name) {
                    setLocationName(data.display_name);
                } else {
                    if (statusEl) statusEl.innerHTML = '<i class="fas fa-check-circle text-success"></i> Coordinates captured';
                }
            } catch (e) {
                if (statusEl) statusEl.innerHTML = '<i class="fas fa-check-circle text-success"></i> Coordinates captured';
            }
        }, function (err) {
            if (statusEl) {
                statusEl.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Location access denied';
                statusEl.classList.replace('text-muted', 'text-danger');
            }
        }, { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 });
    })();
</script>
@endpush
