@extends('front.layout.layout3')

@section('content')
<style>
    .dz-content .title {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-all;
        line-height: 1.4;
        margin-bottom: 15px;
    }
    
    .book-info li {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
    }
    
    .book-info li span {
        font-weight: 600;
        color: #888;
        font-size: 13px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    
    .book-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }
    
    .dz-box {
        display: flex;
        gap: 40px;
        align-items: flex-start;
    }
    
    @media (max-width: 991px) {
        .dz-box {
            flex-direction: column;
            gap: 20px;
        }
        .dz-media {
            width: 100% !important;
        }
        .dz-media img {
            width: 100% !important;
            height: auto !important;
        }
    }
    
    .dz-media {
        flex: 0 0 350px;
    }
    
    .dz-content {
        flex: 1;
    }
</style>

    <section class="content-inner-1" style="background: #fff; padding: 40px 0;">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('marketplace') }}" class="btn btn-link text-muted p-0" style="text-decoration: none; font-weight: 600;">
                    <i class="fas fa-arrow-left me-2"></i> Back to Marketplace
                </a>
            </div>
            <div class="row book-grid-row style-4 m-b60">
                <div class="col">
                    <div class="dz-box">
                        <div class="dz-media">
                            <img src="{{ asset($bookDetails->book_image ?? 'front/images/product/default.jpg') }}"
                                alt="{{ $bookDetails->book_title }}" class="img-fluid rounded shadow-sm"
                                style="width: 398px; height: 572px; object-fit: cover; {{ $bookDetails->book_status == 'sold' ? 'filter: grayscale(100%);' : '' }}">
                            
                            @if($bookDetails->book_status == 'sold')
                                <div style="position: absolute; top: 20px; left: 20px; background: #ff4757; color: white; padding: 5px 15px; font-weight: 800; border-radius: 5px; transform: rotate(-10deg); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                                    SOLD OUT
                                </div>
                            @endif
                        </div>
                        <div class="dz-content">
                            <div class="dz-header">
                                <h3 class="title">{{ $bookDetails->book_title }}</h3>
                                <div class="shop-item-rating">
                                    <div class="d-lg-flex d-sm-inline-flex d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <h6 class="m-b0 me-1">4.0</h6>
                                        <span class="text-muted">(Student Marketplace)</span>
                                    </div>

                                    <div class="social-area">
                                        <ul class="dz-social-icon style-3">
                                            <li><a href="javascript:void(0);"><i class="fa-brands fa-facebook-f"></i></a></li>
                                            <li><a href="javascript:void(0);"><i class="fa-brands fa-twitter"></i></a></li>
                                            <li><a href="javascript:void(0);"><i class="fa-brands fa-whatsapp"></i></a></li>
                                            <li><a href="javascript:void(0);"><i class="fa-solid fa-envelope"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="dz-body">
                                <div class="book-detail">
                                    <style>
                                        .marketplace-book-meta {
                                            display: grid;
                                            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                                            gap: 20px;
                                            margin-bottom: 30px;
                                            list-style: none;
                                            padding: 0;
                                        }
                                        .meta-item {
                                            display: flex;
                                            flex-direction: column;
                                        }
                                        .meta-label {
                                            font-size: 11px;
                                            text-transform: uppercase;
                                            color: #888;
                                            font-weight: 700;
                                            letter-spacing: 0.5px;
                                            margin-bottom: 4px;
                                        }
                                        .meta-value {
                                            font-size: 14px;
                                            color: var(--primary-orange);
                                            font-weight: 700;
                                            word-break: break-all;
                                        }
                                    </style>
                                    <ul class="marketplace-book-meta">
                                        <li class="meta-item">
                                            <span class="meta-label">Written by</span>
                                            <span class="meta-value">{{ $bookDetails->author_name ?? 'N/A' }}</span>
                                        </li>
                                        <li class="meta-item">
                                            <span class="meta-label">Publisher</span>
                                            <span class="meta-value text-dark">{{ $bookDetails->publisher ?? 'N/A' }}</span>
                                        </li>
                                        <li class="meta-item">
                                            <span class="meta-label">Condition</span>
                                            <span class="meta-value text-info">{{ $bookDetails->book_condition }}</span>
                                        </li>
                                        <li class="meta-item">
                                            <span class="meta-label">Edition</span>
                                            <span class="meta-value text-dark">{{ $bookDetails->edition ?? 'N/A' }}</span>
                                        </li>
                                        <li class="meta-item">
                                            <span class="meta-label">Year</span>
                                            <span class="meta-value text-dark">{{ $bookDetails->year_published ?? 'N/A' }}</span>
                                        </li>
                                        <li class="meta-item">
                                            <span class="meta-label">Seller</span>
                                            <span class="meta-value text-dark">{{ $bookDetails->user->name }}</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="book-detail m-b30">
                                    <h6 class="meta-label">Description</h6>
                                    <p class="m-b0" style="color: #666; font-size: 14px; line-height: 1.6;">{{ $bookDetails->book_description ?? 'No description provided by the seller.' }}</p>
                                </div>

                                <div class="book-footer">
                                    <div class="price">
                                        <h5>₹{{ number_format($bookDetails->expected_price, 2) }}</h5>
                                    </div>
                                    
                                    <div class="product-num">
                                        @if($bookDetails->book_status != 'sold')
                                            {{-- Buy Now Button - Links to WhatsApp for direct purchase --}}
                                            <a href="https://wa.me/{{ $bookDetails->user->mobile }}?text=Hi, I am interested in buying your book '{{ $bookDetails->book_title }}' listed on BookHub Marketplace." 
                                               class="btn btn-primary btnhover2" style="padding: 15px 40px; border-radius: 10px; font-weight: 700;">
                                                <i class="fa-solid fa-cart-shopping me-2"></i> BUY NOW
                                            </a>
                                            
                                            <a href="tel:{{ $bookDetails->user->mobile }}" class="btn btn-outline-secondary ms-2" style="padding: 15px 25px; border-radius: 10px;">
                                                <i class="fas fa-phone"></i>
                                            </a>
                                        @else
                                            <span class="badge bg-danger p-3" style="font-size: 1.2rem; border-radius: 10px;">ITEM SOLD</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="product-description tabs-site-button">
                        <ul class="nav nav-tabs">
                            <li><a data-bs-toggle="tab" href="#details-tab" class="active">Details Product</a></li>
                            <li><a data-bs-toggle="tab" href="#seller-tab">Seller Info</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="details-tab" class="tab-pane show active">
                                <table class="table border book-overview">
                                    <tr>
                                        <th>Book Title</th>
                                        <td>{{ $bookDetails->book_title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Author</th>
                                        <td>{{ $bookDetails->author_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>ISBN</th>
                                        <td>{{ $bookDetails->isbn ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Condition</th>
                                        <td><span class="badge bg-info">{{ $bookDetails->book_condition }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Publisher</th>
                                        <td>{{ $bookDetails->publisher ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Edition</th>
                                        <td>{{ $bookDetails->edition ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Year Published</th>
                                        <td>{{ $bookDetails->year_published ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Listing Date</th>
                                        <td>{{ $bookDetails->created_at->format('d M, Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div id="seller-tab" class="tab-pane">
                                <div class="seller-card p-4 bg-light rounded">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ asset($bookDetails->user->profile_image ?? 'assets/images/avatar.png') }}" 
                                             class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <h5 class="mb-0">{{ $bookDetails->user->name }}</h5>
                                            <small class="text-muted">Student Member</small>
                                        </div>
                                    </div>
                                    <p>Contact this student directly to arrange pickup and payment.</p>
                                    <div class="d-flex gap-2">
                                        <a href="tel:{{ $bookDetails->user->mobile }}" class="btn btn-sm btn-dark"><i class="fas fa-phone me-1"></i> Call Seller</a>
                                        <a href="https://wa.me/{{ $bookDetails->user->mobile }}" class="btn btn-sm btn-success"><i class="fab fa-whatsapp me-1"></i> WhatsApp</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 mt-5 mt-xl-0">
                    <div class="widget">
                        <h4 class="widget-title">Statistics</h4>
                        <div class="row sp15">
                            <div class="col-6 mb-3">
                                <div class="icon-bx-wraper style-2 text-center p-3 bg-white shadow-sm rounded">
                                    <h2 class="dz-title counter m-b0" style="font-size: 1.5rem;">{{ number_format($totalUsers) }}</h2>
                                    <p class="m-b0">Users</p>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="icon-bx-wraper style-2 text-center p-3 bg-white shadow-sm rounded">
                                    <h2 class="dz-title counter m-b0" style="font-size: 1.5rem;">{{ number_format($totalProducts) }}</h2>
                                    <p class="m-b0">Books</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3" style="border-radius: 10px;">
                            <i class="fas fa-info-circle me-2"></i>
                            This is a peer-to-peer marketplace. BookHub does not handle payments for student-listed books.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature Box --}}
    <section class="content-inner">
        <div class="container">
            <div class="row sp15">
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <div class="icon-bx-wraper style-2 m-b30 text-center">
                        <div class="icon-bx-lg"><i class="fa-solid fa-users icon-cell"></i></div>
                        <div class="icon-content">
                            <h2 class="dz-title counter m-b0">{{ number_format($totalUsers) }}</h2>
                            <p class="font-20">Happy Customers</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <div class="icon-bx-wraper style-2 m-b30 text-center">
                        <div class="icon-bx-lg"><i class="fa-solid fa-book icon-cell"></i></div>
                        <div class="icon-content">
                            <h2 class="dz-title counter m-b0">{{ number_format($totalProducts) }}</h2>
                            <p class="font-20">Book Collections</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <div class="icon-bx-wraper style-2 m-b30 text-center">
                        <div class="icon-bx-lg"><i class="fa-solid fa-store icon-cell"></i></div>
                        <div class="icon-content">
                            <h2 class="dz-title counter m-b0">{{ number_format($totalVendors) }}</h2>
                            <p class="font-20">Our Stores</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <div class="icon-bx-wraper style-2 m-b30 text-center">
                        <div class="icon-bx-lg"><i class="fa-solid fa-pen icon-cell"></i></div>
                        <div class="icon-content">
                            <h2 class="dz-title counter m-b0">{{ number_format($totalAuthors) }}</h2>
                            <p class="font-20">Famous Writers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
