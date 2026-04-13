@extends('front.layout.layout3')

@section('content')
    <style>
        .all-sellers-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 30px;
            margin: 40px 0;
            font-family: 'Inter', sans-serif;
        }

        .product-mini-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #edf2f7;
        }

        .product-mini-info img {
            width: 80px;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .product-mini-info h2 {
            font-size: 20px;
            margin: 0;
            color: #1a202c;
        }

        .product-mini-info p {
            margin: 5px 0 0;
            color: #718096;
            font-size: 14px;
        }

        .sellers-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .sellers-table th {
            text-align: left;
            color: #718096;
            font-weight: 600;
            font-size: 13px;
            padding: 10px 15px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sellers-table tr {
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }

        .sellers-table tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .sellers-table td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid #f7fafc;
            border-bottom: 1px solid #f7fafc;
        }

        .sellers-table td:first-child {
            border-left: 1px solid #f7fafc;
            border-radius: 8px 0 0 8px;
        }

        .sellers-table td:last-child {
            border-right: 1px solid #f7fafc;
            border-radius: 0 8px 8px 0;
        }

        .seller-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ebf8ff;
            color: #3182ce;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .seller-icon.blue {
            background: #e6fffa;
            color: #319795;
        }

        .seller-name-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #2d3748;
            font-weight: 500;
        }

        .price-bold {
            font-weight: 700;
            color: #1a202c;
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
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-buy-now:hover {
            background: #ed8936;
            color: #1a202c;
        }

        .btn-wishlist {
            background: #ff3366;
            color: #fff;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-wishlist:hover {
            background: #e62e5c;
            transform: scale(1.05);
        }

        .action-flex {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .sort-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .sort-links a {
            color: #718096;
            text-decoration: none;
            font-size: 14px;
            margin-left: 10px;
        }

        .sort-links a:hover {
            color: #2d3748;
        }

        @media (max-width: 768px) {
            .all-sellers-container {
                padding: 15px;
            }
            .product-mini-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .sort-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .sort-links {
                margin-left: 0;
            }
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .sellers-table th, .sellers-table td {
                padding: 10px;
                white-space: nowrap;
            }
            .product-mini-info h2 {
                font-size: 18px;
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

    <section class="content-inner-1" style="background: #F4F6FB; padding-top: 40px;">
        <div class="container">
            <div class="all-sellers-container">
                <div class="product-mini-info">
                    <img src="{{ asset('front/images/product_images/large/' . $productDetails['product_image']) }}" alt="{{ $productDetails['product_name'] }}">
                    <div>
                        <h2>{{ $productDetails['product_name'] }}</h2>
                        <p>by {{ isset($productDetails['authors']) && count($productDetails['authors']) > 0 ? collect($productDetails['authors'])->pluck('name')->join(', ') : 'Unknown Author' }}</p>
                        <a href="{{ url('product/'.$productDetails['id']) }}" style="color: #3182ce; font-size: 13px; text-decoration: none;">View Detail Page</a>
                    </div>
                </div>

                <div class="sort-header">
                    <h3>All Available Sellers</h3>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $userLat = session('user_latitude');
                            $userLng = session('user_longitude');
                        @endphp
                        @php
                            // Mirror API buy-box logic in Blade:
                            // - price/discount from Product::getDiscountPriceDetailsByAttribute(attribute_id)
                            // - sorting priority: plan(pro first) -> discount desc -> final_price asc -> stock desc -> distance asc
                            // - distance: vendor->location, fallback to user lat/lng
                            $sellerRows = collect($sortedSellers)->map(function ($attr) use ($userLat, $userLng) {
                                $priceDetails = \App\Models\Product::getDiscountPriceDetailsByAttribute($attr->id, $attr);

                                $distance = null;
                                if ($userLat && $userLng && $attr->vendor && $attr->vendor->location) {
                                    $loc = explode(',', $attr->vendor->location);
                                    if (count($loc) === 2) {
                                        $distance = \App\Helpers\Helper::getDistance($userLat, $userLng, trim($loc[0]), trim($loc[1]));
                                    }
                                }
                                if ($distance === null && $userLat && $userLng && $attr->user && $attr->user->latitude && $attr->user->longitude) {
                                    $distance = \App\Helpers\Helper::getDistance($userLat, $userLng, $attr->user->latitude, $attr->user->longitude);
                                }

                                $plan = $attr->vendor->plan ?? null;
                                $planRank = ($plan === 'pro') ? 1 : 2;

                                return (object) [
                                    'attr' => $attr,
                                    'price_details' => $priceDetails,
                                    'distance' => $distance,
                                    'plan' => $plan,
                                    'plan_rank' => $planRank,
                                    'discount' => (float) ($attr->product_discount ?? 0),
                                    'stock' => (int) ($attr->stock ?? 0),
                                ];
                            });

                            $sellerRows = $sellerRows->sort(function ($a, $b) {
                                // Primary sort requested by user UI (kept as override)
                                $sort = request('sort');
                                if ($sort === 'distance') {
                                    $ad = $a->distance;
                                    $bd = $b->distance;
                                    if ($ad === null && $bd !== null) return 1;
                                    if ($ad !== null && $bd === null) return -1;
                                    if ($ad !== null && $bd !== null && $ad != $bd) return $ad <=> $bd;
                                } elseif ($sort === 'price') {
                                    $ap = (float) ($a->price_details['final_price'] ?? 0);
                                    $bp = (float) ($b->price_details['final_price'] ?? 0);
                                    if ($ap != $bp) return $ap <=> $bp;
                                }

                                // API priorities (secondary / default)
                                if ($a->plan_rank !== $b->plan_rank) return $a->plan_rank <=> $b->plan_rank;
                                if ($a->discount != $b->discount) return $b->discount <=> $a->discount;

                                $ap = (float) ($a->price_details['final_price'] ?? 0);
                                $bp = (float) ($b->price_details['final_price'] ?? 0);
                                if ($ap != $bp) return $ap <=> $bp;

                                if ($a->stock !== $b->stock) return $b->stock <=> $a->stock;

                                $ad = $a->distance;
                                $bd = $b->distance;
                                if ($ad === null && $bd !== null) return 1;
                                if ($ad !== null && $bd === null) return -1;
                                if ($ad !== null && $bd !== null && $ad != $bd) return $ad <=> $bd;

                                return 0;
                            })->values();
                        @endphp

                        @foreach ($sellerRows as $row)
                            @php
                                $seller = $row->attr;
                                $sellerPrice = $row->price_details;
                                $distance = $row->distance;
                                $plan = $row->plan;
                                $inStock = ((int) ($seller->stock ?? 0)) > 0;
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

                                            @if($plan)
                                                <span class="ms-2 badge {{ $plan === 'pro' ? 'bg-success' : 'bg-secondary' }}" style="font-size: 10px; vertical-align: middle;">
                                                    {{ strtoupper($plan) }}
                                                </span>
                                            @endif

                                            @if(($sellerPrice['discount_percent'] ?? 0) > 0)
                                                <span class="ms-2 badge bg-warning text-dark" style="font-size: 10px; vertical-align: middle;">
                                                    -{{ (int) ($sellerPrice['discount_percent'] ?? 0) }}%
                                                </span>
                                            @endif

                                            <div class="mt-1" style="font-size: 12px; color: {{ $inStock ? '#718096' : '#b91c1c' }};">
                                                {{ $inStock ? ('Stock: ' . (int) ($seller->stock ?? 0)) : 'Out of stock' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="price-bold">
                                    ₹{{ number_format($sellerPrice['final_price'] ?? 0, 0) }}
                                    @if(($sellerPrice['discount'] ?? 0) > 0)
                                        <div style="font-size: 12px; font-weight: 600; color: #94a3b8; text-decoration: line-through;">
                                            ₹{{ number_format($sellerPrice['product_price'] ?? 0, 0) }}
                                        </div>
                                    @endif
                                </td>
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
                                    <div class="action-flex">
                                        <form action="{{ url('cart/add') }}" method="POST" style="flex: 1;">
                                            @csrf
                                            <input type="hidden" name="product_attribute_id" value="{{ $seller->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn-buy-now" {{ $inStock ? '' : 'disabled' }}>
                                                {{ $inStock ? 'Buy Now' : 'Out of Stock' }}
                                            </button>
                                        </form>
                                        <form action="{{ url('wishlist/add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_attribute_id" value="{{ $seller->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn-wishlist" title="Add to Wishlist">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
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
    </script>
@endsection
