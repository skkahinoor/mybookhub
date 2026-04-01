@extends('front.layout.layout3')

@section('content')
    <style>
        .product-card-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 30px;
            margin-bottom: 40px;
            font-family: 'Inter', sans-serif;
        }


        .product-image-box {
            text-align: center;
            position: relative;
            width: 100%;
            padding-top: 150%; /* 2/3 ratio (height is 1.5x width) */
            background: #f7fafc;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .product-image-box img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .extra-details {
            margin-top: 20px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
            border: 1px solid #edf2f7;
        }

        .extra-details-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 8px;
            color: #4a5568;
        }

        .extra-details-row span:first-child {
            font-weight: 600;
            color: #718096;
        }

        .product-info-box {
            padding-left: 20px;
        }

        .product-info-box h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 4px;
        }

        .author-text {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 12px;
        }


        .best-price-badge {
            display: inline-flex;
            align-items: center;
            background: #2d3748;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .best-price-badge .fire-icon {
            margin-left: 8px;
            color: #f6ad55;
        }

        .price-range-text {
            font-size: 15px;
            color: #4a5568;
            margin-bottom: 30px;
        }

        .sellers-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 15px;
            padding-top: 15px;
            border-top: 1px solid #edf2f7;
        }

        .sellers-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }

        .sort-links {
            font-size: 12px;
            color: #718096;
        }

        .sort-links a {
            color: #3182ce;
            text-decoration: none;
            margin-left: 5px;
        }

        .sellers-table {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
        }

        .sellers-table th {
            background: #f7fafc;
            padding: 10px 15px;
            font-size: 13px;
            font-weight: 600;
            color: #718096;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .sellers-table td {
            padding: 15px;
            font-size: 14px;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .sellers-table tr:last-child td {
            border-bottom: none;
        }

        .seller-name-cell {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }

        .seller-icon {
            width: 24px;
            height: 24px;
            background: #38a169;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            font-size: 12px;
        }

        .seller-icon.blue { background: #3182ce; }

        .price-bold {
            font-weight: 800;
            font-size: 16px;
        }

        .btn-buy-now {
            background: #f6ad55;
            color: #1a202c;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-buy-now:hover {
            background: #ed8936;
            color: #1a202c;
        }

        .view-all-sellers {
            display: block;
            text-align: center;
            padding: 12px;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            color: #3182ce;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
        }

        .view-all-sellers:hover {
            background: #edf2f7;
        }

        .overview-box {
            margin-top: 30px;
        }

        .overview-box h4 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .overview-text {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .product-card-container {
                padding: 15px;
            }
            .product-info-box {
                padding-left: 0;
                margin-top: 20px;
            }
            .sellers-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .sort-links {
                margin-left: 0;
            }
            .product-image-box {
                max-width: 250px;
                margin: 0 auto;
            }
            .table-responsive {
                border: none;
            }
            .sellers-table th, .sellers-table td {
                padding: 10px;
                white-space: nowrap;
            }
            .product-info-box h1 {
                font-size: 22px;
            }
            .extra-details-row {
                flex-direction: column;
                gap: 2px;
            }
            .extra-details-row span:last-child {
                margin-bottom: 5px;
            }
            .best-price-badge {
                font-size: 14px;
                padding: 6px 12px;
            }
        }
        .btn-directions {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: #ebf8ff;
            color: #3182ce;
            border-radius: 6px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-directions:hover {
            background: #3182ce;
            color: #fff;
        }
        .range-container {
            margin-top: 5px;
            width: 180px;
        }

        .range-labels {
            position: relative;
            height: 15px;
            margin-top: 5px;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 600;
        }

        .range-labels span {
            position: absolute;
            top: 0;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .range-labels span:nth-child(1) { left: 0; transform: none; }
        .range-labels span:nth-child(2) { left: 25%; }
        .range-labels span:nth-child(3) { left: 50%; }
        .range-labels span:nth-child(4) { left: 100%; transform: translateX(-100%); }

        .range-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 5px;
            background: #e2e8f0;
            border-radius: 5px;
            outline: none;
        }

        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            background: #f97316;
            border: 3px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(249, 115, 22, 0.3);
            transition: .2s;
        }

        .range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.1);
        }
    </style>
      <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content" style="background: #000; border: none; border-radius: 12px; overflow: hidden;">
                                        <div class="modal-header" style="border: none; position: absolute; top: 10px; right: 10px; z-index: 9999; padding: 0;">
                                            <button type="button" class="close text-white shadow-none" aria-label="Close" onclick="$('#videoModal').modal('hide');" style="background: rgba(0,0,0,0.6); border: none; font-size: 28px; width: 40px; height: 40px; border-radius: 50%; opacity: 1; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <video id="bookVideoPlayer" width="100%" height="auto" controls style="display: block; max-height: 80vh;">
                                                <source src="{{ asset('front/videos/product_videos/' . $productAttribute->video_upload) }}" type="video/{{ pathinfo($productAttribute->video_upload, PATHINFO_EXTENSION) }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                </div>
            </div>
        </div>

    <section class="content-inner-1" style="background: #efeff4; padding-top: 40px;">
        <div class="container">
            @if (Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <i class="fas fa-check-circle me-2"></i> {{ Session::get('success_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (Session::has('error_message'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); background: #fee2e2; color: #991b1b;">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ Session::get('error_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="product-card-container">
                <div class="row">
                    <!-- Left: Image -->
                    <div class="col-md-4">
                        <div class="product-image-box">
                            <img src="{{ asset('front/images/product_images/large/' . $productDetails['product_image']) }}" alt="{{ $productDetails['product_name'] }}">
                        </div>

                        @if(!empty($productAttribute->video_upload))
                            <div class="product-video-box mt-4 text-center">
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#videoModal" style="border-radius: 8px; font-weight: 600; padding: 12px;">
                                    <i class="fas fa-play-circle me-2"></i> Watch Book Video Glance
                                </button>
                            </div>

                            <!-- Video Modal -->
                          
                        @endif
                    </div>

                    <!-- Right: Details -->
                    <div class="col-md-8">
                        <div class="product-info-box">
                            <h1>{{ $productDetails['product_name'] }}</h1>
                            <div class="author-text">by {{ isset($productDetails['authors']) && count($productDetails['authors']) > 0 ? collect($productDetails['authors'])->pluck('name')->join(', ') : 'Unknown Author' }}</div>

                            @if($otherSellers->count() > 0)
                                <div class="best-price-badge">
                                    Best Price: ₹{{ number_format($finalPrice, 0) }}
                                    <span class="fire-icon">🔥</span>
                                </div>

                                <div class="price-range-text">
                                    Price Range: ₹{{ number_format($minPrice, 0) }} - ₹{{ number_format($maxPrice, 0) }}
                                </div>
                            @else
                                <div class="best-price-badge" style="background: #e53e3e;">
                                    Out of Stock
                                    <i class="fas fa-exclamation-circle" style="margin-left: 8px;"></i>
                                </div>
                                <div class="price-range-text">
                                    @if(session('distance', 10) < 100 && (session('user_latitude') && session('user_longitude')))
                                        Currently no active sellers within {{ session('distance', 10) }} km.
                                    @else
                                        Currently no active sellers for this book.
                                    @endif
                                </div>
                            @endif

                            <div class="sellers-header">
                                <h3>Available from {{ $otherSellers->count() }} Sellers</h3>
                                <div class="sort-links">
                                    Sort by: 
                                    <a href="{{ url()->current() . '?sort=price' }}" style="{{ request('sort') == 'price' ? 'font-weight:bold; color:#2d3748;' : '' }}">Lowest Price</a> | 
                                    <a href="{{ url()->current() . '?sort=distance&distance=' . session('distance', 10) }}" style="{{ request('sort') == 'distance' ? 'font-weight:bold; color:#2d3748;' : '' }}">Distance</a>
                                    <span class="ms-3 text-muted">|</span>
                                    <div class="radius-selector d-inline-block ms-4" style="vertical-align: middle;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span style="font-size: 11px; font-weight: 700; color: #64748b;">Distance Range</span>
                                            <span id="rangeValue" style="color: #f97316; font-weight: 700; font-size: 11px;">
                                                Within {{ session('distance', 10) >= 100 ? '100 km+' : session('distance', 10) . ' km' }}
                                            </span>
                                        </div>
                                        <div class="range-container">
                                            <input type="range" class="range-slider" id="distanceRange" min="1" max="100" 
                                                value="{{ session('distance', 10) }}"
                                                oninput="updateRangeText(this.value)"
                                                onchange="window.location.href = '{{ url()->current() }}?distance=' + this.value + '&sort={{ request('sort') }}'">
                                            <div class="range-labels">
                                                <span>1km</span>
                                                <span>25km</span>
                                                <span>50km</span>
                                                <span>100+</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="sellers-table">
                                <thead>
                                    <tr>
                                        <th>Seller</th>
                                        <th>Price</th>
                                        <th>Condition</th>
                                        <th>Distance</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $userLat = session('user_latitude');
                                        $userLng = session('user_longitude');
                                    @endphp
                                    @forelse ($otherSellers->take(3) as $seller)
                                        @php
                                            $sellerPrice = \App\Models\Product::getDiscountAttributePrice($productDetails['id'], null, $seller->id);
                                            
                                            $distance = null;
                                            if ($userLat && $userLng && isset($seller->vendor->location) && $seller->vendor->location) {
                                                $loc = explode(',', $seller->vendor->location);
                                                if(count($loc) == 2) {
                                                    $distance = \App\Helpers\Helper::getDistance($userLat, $userLng, $loc[0], $loc[1]);
                                                }
                                            }

                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="seller-name-cell">
                                                    <div class="seller-icon {{ $loop->index % 2 == 0 ? '' : 'blue' }}">
                                                        <i class="fas fa-{{ $seller->user_id ? 'user' : 'store' }}"></i>
                                                    </div>
                                                    <div>
                                                        @php
                                                            $sellerDisplayName = 'Individual Seller';
                                                            if ($seller->vendor) {
                                                                $sellerDisplayName = $seller->vendor->user->name ?? $seller->vendor->vendorbusinessdetails->shop_name ?? 'Vendor';
                                                            } elseif ($seller->user) {
                                                                $sellerDisplayName = $seller->user->name;
                                                            }
                                                        @endphp
                                                        {{ $sellerDisplayName }}

                                                        @if($seller->contact_details_paid == 1 && $seller->user)
                                                            <div class="mt-2" style="font-size: 12px; font-weight: normal; color: #4a5568;">
                                                                <div><i class="fas fa-map-marker-alt"></i> {{ $seller->user->address }}, {{ $seller->user->district->name ?? '' }}, {{ $seller->user->state->name ?? '' }} - {{ $seller->user->pincode }}</div>
                                                                <div><i class="fas fa-phone"></i> {{ $seller->user->phone }}</div>
                                                                <div><i class="fas fa-envelope"></i> {{ $seller->user->email }}</div>
                                                                <div class="mt-1 text-primary"><i class="fas fa-info-circle"></i> Buy Offline directly from seller</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="price-bold">₹{{ number_format($sellerPrice['final_price'], 0) }}</td>
                                            <td>{{ $seller->condition->name ?? 'New' }}</td>
                                            <td class="text-secondary">
                                                @if($distance !== null)
                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                        <span>{{ $distance < 1 ? round($distance * 1000) . ' m' : round($distance, 1) . ' km' }}</span>
                                                        <a href="https://www.google.com/maps/dir/?api=1&origin={{ $userLat }},{{ $userLng }}&destination={{ $seller->vendor->location ?? ($seller->user->latitude . ',' . $seller->user->longitude) }}" target="_blank" class="btn-directions" title="Get Directions">
                                                            <i class="fas fa-directions"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($seller->contact_details_paid == 1)
                                                    <button class="btn btn-primary btn-sm w-100" style="font-size: 13px; font-weight: 700;" disabled>Contact Above</button>
                                                @else
                                                    <form action="{{ url('cart/add') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_attribute_id" value="{{ $seller->id }}">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" class="btn-buy-now">Buy Now</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 30px; color: #718096;">
                                                <i class="fas fa-book-open" style="font-size: 24px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                                No active sellers available for this book at the moment.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                </table>
                            </div>

                            @if($otherSellers->count() > 3)
                                <a href="{{ url('product/'.$productDetails['id'].'/all-sellers') }}" class="view-all-sellers" style="display: block; margin-top: 10px; color: #4a5568; font-weight: 600; text-decoration: none;">View All {{ $otherSellers->count() }} Sellers ></a>
                            @endif

                            @if(isset($productDetails['condition']) && strtolower($productDetails['condition']) == 'old')
                            <!-- Disclaimer Card -->
                            <div class="disclaimer-card mt-4 p-3" style="background: #fff5f5; border: 1px solid #feb2b2; border-radius: 12px; box-shadow: 0 2px 8px rgba(229, 62, 62, 0.05);">
                                <h5 class="text-danger mb-2" style="font-size: 15px; font-weight: 700;">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Safety Disclaimer
                                </h5>
                                <p class="mb-0" style="font-size: 13px; font-weight: 500; line-height: 1.5; color: #742a2a;">
                                    Please view the video and explore book details carefully before payment. 
                                    If any problem occurs with the product or transaction, <strong>BookHub is not responsible.</strong> 
                                    Buying from individual sellers is at your own risk.
                                </p>
                            </div>
                            @endif

                            <div class="extra-details" style="margin-top: 30px; background: #fff; border: 1px solid #edf2f7; box-shadow: none;">
                                <h4 style="font-size: 16px; margin-bottom: 15px; border-bottom: 1px solid #edf2f7; padding-bottom: 10px;">Book Details</h4>
                                <div class="extra-details-row">
                                    <span>Condition</span>
                                    <span>{{ ucfirst($productDetails['condition'] ?? 'New') }}</span>
                                </div>
                                <div class="extra-details-row">
                                    <span>Language</span>
                                    <span>{{ $productDetails['language']['name'] ?? 'English' }}</span>
                                </div>
                                <div class="extra-details-row">
                                    <span>Publisher</span>
                                    <span>{{ $productDetails['publisher']['name'] ?? 'N/A' }}</span>
                                </div>
                                @if(isset($productDetails['edition']['edition_name']))
                                <div class="extra-details-row">
                                    <span>Edition</span>
                                    <span>{{ $productDetails['edition']['edition_name'] }}</span>
                                </div>
                                @endif
                                @if(isset($productDetails['subject']['subject_name']))
                                <div class="extra-details-row">
                                    <span>Subject</span>
                                    <span>{{ $productDetails['subject']['subject_name'] }}</span>
                                </div>
                                @endif
                                <div class="extra-details-row">
                                    <span>Category</span>
                                    <span>{{ $productDetails['category']['category_name'] ?? 'N/A' }}</span>
                                </div>
                                @if(isset($productDetails['product_isbn']))
                                <div class="extra-details-row">
                                    <span>ISBN</span>
                                    <span>{{ $productDetails['product_isbn'] }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function updateRangeText(val) {
            const rangeValue = document.getElementById('rangeValue');
            if (val >= 100) {
                rangeValue.innerText = 'Within 100 km+';
            } else {
                rangeValue.innerText = `Within ${val} km`;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var videoModal = document.getElementById('videoModal');
            if(videoModal) {
                var videoPlayer = document.getElementById('bookVideoPlayer');
                
                videoModal.addEventListener('shown.bs.modal', function () {
                    if (videoPlayer) {
                        videoPlayer.play().catch(function(error) {
                            console.log("Video autoplay prevented by browser:", error);
                        });
                    }
                });
                
                videoModal.addEventListener('hidden.bs.modal', function () {
                    if (videoPlayer) {
                        videoPlayer.pause();
                        videoPlayer.currentTime = 0;
                    }
                });
            }
        });
    </script>
@endsection
