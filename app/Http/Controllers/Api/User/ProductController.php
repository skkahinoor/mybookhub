<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\ShippingCharge;
use App\Models\WalletTransaction;
use App\Models\Payment;
use App\Models\Category;
use App\Models\User;
use App\Models\Vendor;
use App\Models\OrdersLog;
use App\Models\UserAddress;
use App\Models\Notification;
use App\Models\DeliverySetting;
use App\Models\OrderQuery;
use App\Models\OrderQueryMessage;
use Razorpay\Api\Api;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'limit' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'section_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'book_type_id' => 'nullable',
            'language_id' => 'nullable',
            'subcategory_id' => 'nullable|integer',
            'subject_id' => 'nullable',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'search' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'location_limit' => 'nullable|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth('sanctum')->user();
        $limit = $request->limit ?? 20;
        $page = $request->page ?? 1;
        $lat = $request->lat;
        $lng = $request->lng;

        // Dynamic versioning: Use the latest updated_at from the database to ensure cache freshness
        // This checks every 6 seconds if anything has changed. If not, it uses the high-performance cache.
        $cacheVersion = Cache::remember('products_sync_version', 6, function () {
            $lastAttr = DB::table('products_attributes')->max('updated_at');
            $lastProd = DB::table('products')->max('updated_at');
            return md5($lastAttr . '_' . $lastProd);
        });

        $cacheKey = 'products_v' . $cacheVersion . '_' .
            ($user ? 'user_' . $user->id : 'guest') .
            '_' . md5(json_encode($request->all()));

        $data = Cache::remember($cacheKey, 300, function () use ($request, $user, $limit, $page, $lat, $lng) {
            return $this->sqlSearch($request, $limit, $page, $lat, $lng, $request->location_limit);
        });

        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'data' => $data
        ]);
    }

    private function sqlSearch($request, $limit, $page, $lat, $lng, $location_limit = null)
    {
        $user = auth('sanctum')->user();

        $query = Product::with([
            'category:id,category_name',
            'subcategory:id,subcategory_name',
            'section:id,name',
            'subject:id,name',
            'bookType:id,book_type,book_type_icon',
            'language:id,name',
            'authors:id,name',
            'attributes' => function ($q) {
                $q->where('status', 1)
                    ->select('id', 'product_id', 'status', 'stock', 'old_book_condition_id', 'user_product_price', 'product_discount');
            },
            'attributes.condition:id,name,percentage'
        ])
            ->where('status', 1)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('products_attributes')
                    ->whereRaw('products_attributes.product_id = products.id')
                    ->where('products_attributes.status', 1)
                    ->where('products_attributes.stock', '>=', 0);
            });

        if ($lat && $lng) {
            $query->select('products.*');

            // --- SCALE-HARDENING: Bounding Box Pre-filter ---
            // Instead of running Haversine on 1M rows, we discard anything outside a square first.
            // 1 degree of latitude is ~111km.
            if ($location_limit && $location_limit < 1000) {
                $latDelta = $location_limit / 111.045;
                $lngDelta = $location_limit / (111.045 * cos(deg2rad($lat)));

                $minLat = $lat - $latDelta;
                $maxLat = $lat + $latDelta;
                $minLng = $lng - $lngDelta;
                $maxLng = $lng + $lngDelta;

                $query->whereExists(function ($q) use ($minLat, $maxLat, $minLng, $maxLng) {
                    $q->select(DB::raw(1))
                        ->from('products_attributes as pa_bb')
                        ->join('vendors as v_bb', 'pa_bb.vendor_id', '=', 'v_bb.id')
                        ->whereRaw('pa_bb.product_id = products.id')
                        ->where('pa_bb.status', 1)
                        ->where('pa_bb.stock', '>=', 0)
                        ->whereBetween(DB::raw("CAST(SUBSTRING_INDEX(v_bb.location, ',', 1) AS DECIMAL(10,6))"), [$minLat, $maxLat])
                        ->whereBetween(DB::raw("CAST(SUBSTRING_INDEX(v_bb.location, ',', -1) AS DECIMAL(10,6))"), [$minLng, $maxLng]);
                });
            }

            $query->selectRaw("(
                SELECT MIN(6371 * acos(
                    cos(radians(?)) *
                    cos(radians(CAST(SUBSTRING_INDEX(v.location, ',', 1) AS DECIMAL(10,6)))) *
                    cos(radians(CAST(SUBSTRING_INDEX(v.location, ',', -1) AS DECIMAL(10,6))) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(CAST(SUBSTRING_INDEX(v.location, ',', 1) AS DECIMAL(10,6))))
                ))
                FROM products_attributes pa
                INNER JOIN vendors v ON pa.vendor_id = v.id
                WHERE pa.product_id = products.id
                AND pa.status = 1
                AND pa.stock >= 0
            ) AS distance", [$lat, $lng, $lat]);

            if ($location_limit && $location_limit < 1000) {
                $query->having('distance', '<=', $location_limit);
                $query->orderBy('distance', 'asc');
            }
        }

        // Personalization logic for students
        if ($user && $user->hasRole('student')) {
            $user->load(['institution', 'institutionClass']);

            // Auto-apply Section (type) if not provided
            if (!$request->section_id && $user->institution && $user->institution->type) {
                $query->where('section_id', $user->institution->type);
            }
            // Auto-apply Category (board) if not provided
            if (!$request->category_id && $user->institution && $user->institution->board) {
                $query->where('category_id', $user->institution->board);
            }
            // Auto-apply Subcategory (class) if not provided
            if (!$request->subcategory_id && $user->institutionClass && $user->institutionClass->sub_category_id) {
                $query->where('subcategory_id', $user->institutionClass->sub_category_id);
            }
        }

        // Apply regular filters
        if ($request->search) {
            $searchTerm = trim($request->search);
            $words = explode(' ', $searchTerm);
            $booleanSearch = '';

            foreach ($words as $word) {
                if (trim($word) !== '') {
                    $booleanSearch .= '+' . trim($word) . '* ';
                }
            }

            $query->where(function ($q) use ($searchTerm, $booleanSearch) {
                $q->where('product_isbn', $searchTerm)
                    ->orWhereRaw(
                        "MATCH(product_name, meta_title, meta_keywords)
                         AGAINST(? IN BOOLEAN MODE)",
                        [trim($booleanSearch)]
                    );
            });
        }

        if ($request->isbn) {
            $query->where('product_isbn', $request->isbn);
        }

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        if ($request->subject_id) {
            if (is_array($request->subject_id)) {
                $query->whereIn('subject_id', $request->subject_id);
            } else {
                $query->where('subject_id', $request->subject_id);
            }
        }

        if ($request->min_price) {
            $query->where('product_price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('product_price', '<=', $request->max_price);
        }

        if ($request->book_type_id) {
            if (is_array($request->book_type_id)) {
                $query->whereIn('book_type_id', $request->book_type_id);
            } else {
                $query->where('book_type_id', $request->book_type_id);
            }
        }

        if ($request->language_id) {
            if (is_array($request->language_id)) {
                $query->whereIn('language_id', $request->language_id);
            } else {
                $query->where('language_id', $request->language_id);
            }
        }

        if ($request->condition) {
            $query->where('condition', $request->condition);
        }

        // Default sorting for product listing
        $query->orderBy('id', 'desc');

        $results = $query->paginate($limit, ['*'], 'page', $page);

        return [
            'total' => $results->total(),
            'current_page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'products' => $results->through(function ($product) {
                $basePath = url('front/images/product_images');

                // Aggregate price range and offer count from associated attributes
                $prices = $product->attributes
                    ->filter(function ($attr) {
                    return $attr->status == 1 && $attr->stock >= 0;
                })
                    ->map(function ($attr) {
                    $pDetails = Product::getDiscountPriceDetailsByAttribute($attr->id, $attr);
                    return $pDetails['final_price'];
                });

                return [
                    'id' => $product->id,
                    'distance' => isset($product->distance) ? round($product->distance, 1) : null,
                    'product_name' => $product->product_name,
                    'product_isbn' => $product->product_isbn,
                    'product_price' => round($product->product_price),
                    'min_final_price' => $prices->isNotEmpty() ? round($prices->min()) : null,
                    'max_final_price' => $prices->isNotEmpty() ? round($prices->max()) : null,
                    'offer_count' => $prices->count(),
                    'image_urls' => [
                        'large' => $product->product_image
                            ? $basePath . '/large/' . $product->product_image
                            : null,
                        'medium' => $product->product_image
                            ? $basePath . '/medium/' . $product->product_image
                            : null,
                        'small' => $product->product_image
                            ? $basePath . '/small/' . $product->product_image
                            : null,
                    ],
                    'description' => $product->description,
                    'condition' => $product->condition,
                    'book_type' => $product->bookType ? [
                        'id' => $product->bookType->id,
                        'name' => $product->bookType->book_type,
                        'icon' => $product->bookType->book_type_icon,
                    ] : null,
                    'language' => $product->language ? [
                        'id' => $product->language->id,
                        'name' => $product->language->name,
                    ] : null,
                    'category' => $product->category,
                    'subcategory' => $product->subcategory,
                    'section' => $product->section,
                    'subject' => $product->subject,
                    'author_name' => $product->authors->pluck('name')->join(', '),
                    'authors' => $product->authors->map(function ($author) {
                        return [
                            'id' => $author->id,
                            'name' => $author->name,
                        ];
                    }),
                ];
            })->items()
        ];
    }

    public function payNow(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        // Support both single ID (from URL) and multiple IDs (from body 'order_ids')
        $order_ids = $request->input('order_ids', [$id]);

        $orders = Order::whereIn('id', $order_ids)
            ->where('user_id', $user->id)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        // Verify all provided orders are Pending or New
        foreach ($orders as $order) {
            if (!in_array($order->order_status, ['Pending', 'New'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order #' . $order->id . ' is already paid or cannot be paid at this stage.'
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

            $total_grand_total = $orders->sum('grand_total');

            // Format receipt string
            $receipt_id = 'order_paynow_' . implode('_', $order_ids);
            if (strlen($receipt_id) > 40) {
                $receipt_id = substr($receipt_id, 0, 40); // Razorpay max length
            }

            $razorpayOrder = $api->order->create([
                'receipt' => $receipt_id,
                'amount' => round($total_grand_total * 100), // in paise
                'currency' => 'INR'
            ]);

            Order::whereIn('id', $order_ids)->update(['razorpay_order_id' => $razorpayOrder['id']]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Razorpay order created successfully',
                'order_id' => $order_ids[0],
                'order_ids' => $order_ids,
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $total_grand_total
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to initialize payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function vendorsproduct(Request $request, $product_id)
    {
        $user = auth('sanctum')->user();
        $lat = $request->lat;
        $lng = $request->lng;

        $product = Product::with([
            'section',
            'category',
            'subcategory',
            'publisher',
            'subject',
            'bookType',
            'language',
            'edition',
            'authors'
        ])
            ->where('id', $product_id)
            ->where('status', 1)
            ->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not available'
            ], 404);
        }

        // Fetch all active vendor offers for this product with specific sorting
        $attributesQuery = ProductsAttribute::with(['vendor.user', 'condition', 'product'])
            ->join('vendors as v', 'products_attributes.vendor_id', '=', 'v.id')
            ->join('products as p', 'products_attributes.product_id', '=', 'p.id')
            ->select('products_attributes.*')
            ->where('products_attributes.product_id', $product_id)
            ->where('products_attributes.status', 1)
            ->where('products_attributes.stock', '>=', 0);

        // Apply remaining sorting priorities
        $attributesQuery->orderByRaw("CASE WHEN v.plan='pro' THEN 1 ELSE 2 END ASC");
        $attributesQuery->orderBy('products_attributes.product_discount', 'desc');
        $attributesQuery->orderBy('p.product_price', 'asc');
        $attributesQuery->orderBy('products_attributes.stock', 'desc');

        if ($lat && $lng) {
            $attributesQuery->addSelect(DB::raw("
                (6371 * acos(
                    cos(radians($lat)) *
                    cos(radians(CAST(SUBSTRING_INDEX(v.location, ',', 1) AS DECIMAL(10,6)))) *
                    cos(radians(CAST(SUBSTRING_INDEX(v.location, ',', -1) AS DECIMAL(10,6))) - radians($lng)) +
                    sin(radians($lat)) *
                    sin(radians(CAST(SUBSTRING_INDEX(v.location, ',', 1) AS DECIMAL(10,6))))
                )) AS distance
            "));
            $attributesQuery->orderBy('distance', 'asc');
        }

        $attributes = $attributesQuery->limit(100)->get();

        // Pre-load all cart items for this user in a single query (fixes N+1)
        $cartItems = collect();
        if ($user) {
            $allAttrIds = $attributes->pluck('id');
            $cartItems = Cart::where('user_id', $user->id)
                ->whereIn('product_attribute_id', $allAttrIds)
                ->get()
                ->keyBy('product_attribute_id');
        }

        $vendorOffers = $attributes->map(function ($attr) use ($lat, $lng, $user, $cartItems) {
            $distance = isset($attr->distance) ? round($attr->distance, 2) : null;

            // Fallback for manual distance calculation if needed
            if ($distance === null && $lat && $lng && $attr->vendor && $attr->vendor->location) {
                $vendorLoc = explode(',', $attr->vendor->location);
                if (count($vendorLoc) == 2) {
                    $vLat = (float) trim($vendorLoc[0]);
                    $vLng = (float) trim($vendorLoc[1]);

                    $theta = $lng - $vLng;
                    $dist = sin(deg2rad($lat)) * sin(deg2rad($vLat)) + cos(deg2rad($lat)) * cos(deg2rad($vLat)) * cos(deg2rad($theta));
                    $dist = acos($dist);
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $distance = round($miles * 1.609344, 2);
                }
            }

            $priceDetails = Product::getDiscountPriceDetailsByAttribute($attr->id, $attr);

            // Use pre-loaded cart data instead of querying per-attribute
            $cartItem = $cartItems->get($attr->id);
            $inCart = $cartItem ? true : false;
            $cartQty = $cartItem ? $cartItem->quantity : 0;

            return [
                'attribute_id' => $attr->id,
                'stock' => $attr->stock,
                'sku' => $attr->sku,
                'price_details' => $priceDetails,
                'old_book_condition' => $attr->condition ? [
                    'id' => $attr->condition->id,
                    'name' => $attr->condition->name,
                    'percentage' => $attr->condition->percentage,
                ] : null,
                'seller_type' => 'vendor',
                'vendor' => [
                    'vendor_id' => $attr->vendor->id ?? null,
                    'name' => $attr->vendor->user->name ?? 'Verified Seller',
                    'shop_name' => $attr->vendor->shop_name ?? null,
                    'location' => $attr->vendor->location ?? null,
                    'distance' => $distance,
                    'plan' => $attr->vendor->plan ?? null
                ],
                'user' => null,
                'cart_status' => [
                    'in_cart' => $inCart,
                    'quantity' => $cartQty
                ]
            ];
        });

        // Also fetch user-listed old book sellers (students who sold books, vendor_id is NULL)
        $userAttributes = ProductsAttribute::with(['user', 'condition', 'product'])
            ->where('product_id', $product_id)
            ->where('status', 1)
            ->whereNull('vendor_id')
            ->whereNotNull('user_id')
            ->orderBy('stock', 'desc')
            ->limit(100)
            ->get();

        // Pre-load cart items for user offers in a single query
        $userCartItems = collect();
        if ($user && $userAttributes->isNotEmpty()) {
            $userCartItems = Cart::where('user_id', $user->id)
                ->whereIn('product_attribute_id', $userAttributes->pluck('id'))
                ->get()
                ->keyBy('product_attribute_id');
        }

        $userOffers = $userAttributes->map(function ($attr) use ($user, $userCartItems) {
            $priceDetails = Product::getDiscountPriceDetailsByAttribute($attr->id, $attr);

            // Use pre-loaded cart data
            $cartItem = $userCartItems->get($attr->id);
            $inCart = $cartItem ? true : false;
            $cartQty = $cartItem ? $cartItem->quantity : 0;

            return [
                'attribute_id' => $attr->id,
                'stock' => $attr->stock,
                'sku' => $attr->sku,
                'show_contact' => $attr->show_contact,
                'contact_details_paid' => $attr->contact_details_paid,
                'platform_charge' => $attr->platform_charge,
                'is_sold' => $attr->is_sold,
                'price_details' => $priceDetails,
                'old_book_condition' => $attr->condition ? [
                    'id' => $attr->condition->id,
                    'name' => $attr->condition->name,
                    'percentage' => $attr->condition->percentage,
                ] : null,
                'seller_type' => 'user',
                'vendor' => null,
                'user' => [
                    'user_id' => $attr->user->id ?? null,
                    'name' => $attr->user->name ?? 'Verified Seller',
                    'email' => $attr->user->email ?? null,
                    'mobile' => $attr->user->mobile ?? null,
                    'image' => $attr->user->image ?? null,
                    'address' => $attr->user->address ?? null,
                ],
                'user_old_book_video' => $attr->video_upload ? url('front/videos/product_videos/' . $attr->video_upload) : null,
                'cart_status' => [
                    'in_cart' => $inCart,
                    'quantity' => $cartQty
                ]
            ];
        });

        // Merge vendor offers and user offers together
        $vendorOffers = $vendorOffers->values()->concat($userOffers->values());

        $authorNames = $product->authors->pluck('name')->join(', ');
        $basePath = url('front/images/product_images');

        return response()->json([
            'status' => true,
            'message' => 'Product details and vendor offers fetched successfully',
            'best_price' => $vendorOffers->isNotEmpty() ? $vendorOffers->first()['price_details']['final_price'] : null,
            'data' => [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'isbn' => $product->product_isbn,
                    'price' => round($product->product_price),
                    'image_urls' => [
                        'large' => $product->product_image ? $basePath . '/large/' . $product->product_image : null,
                        'medium' => $product->product_image ? $basePath . '/medium/' . $product->product_image : null,
                        'small' => $product->product_image ? $basePath . '/small/' . $product->product_image : null,
                    ],
                    'description' => $product->description,
                    'condition' => $product->condition,
                    'section' => $product->section,
                    'category' => $product->category,
                    'subcategory' => $product->subcategory,
                    'publisher' => $product->publisher,
                    'subject' => $product->subject,
                    'book_type' => $product->bookType,
                    'language' => $product->language,
                    'edition' => $product->edition,
                    'author_name' => $authorNames,
                    'authors' => $product->authors->map(function ($author) {
                        return [
                            'id' => is_array($author) ? ($author['id'] ?? null) : ($author->id ?? null),
                            'name' => is_array($author) ? ($author['name'] ?? null) : ($author->name ?? null),
                        ];
                    })
                ],
                'vendor_offers' => $vendorOffers
            ]
        ]);
    }

    public function productDetails(Request $request, $attribute_id)
    {
        $user = auth('sanctum')->user();

        $attribute = ProductsAttribute::with(['vendor.user', 'user', 'condition'])
            ->where('id', $attribute_id)
            ->where('status', 1)
            ->first();

        if (!$attribute) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = null;

        if ($lat && $lng && $attribute->vendor && $attribute->vendor->location) {
            $vendorLoc = explode(',', $attribute->vendor->location);
            if (count($vendorLoc) == 2) {
                $vLat = (float) trim($vendorLoc[0]);
                $vLng = (float) trim($vendorLoc[1]);

                $theta = $lng - $vLng;
                $dist = sin(deg2rad($lat)) * sin(deg2rad($vLat)) + cos(deg2rad($lat)) * cos(deg2rad($vLat)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $distance = round($miles * 1.609344, 2);
            }
        }

        $product = Product::with([
            'section',
            'category',
            'subcategory',
            'publisher',
            'subject',
            'bookType',
            'language',
            'edition',
            'authors'
        ])
            ->where('id', $attribute->product_id)
            ->where('status', 1)
            ->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not available'
            ], 404);
        }


        $inCart = false;
        $cartQty = 0;

        if ($user) {
            $cartItem = Cart::where('user_id', $user->id)
                ->where('product_attribute_id', $attribute_id)
                ->first();

            if ($cartItem) {
                $inCart = true;
                $cartQty = $cartItem->quantity;
            }
        }


        $authorNames = $product->authors->pluck('name')->join(', ');


        $basePath = url('front/images/product_images');

        return response()->json([
            'status' => true,
            'message' => 'Product details fetched successfully',

            'data' => [

                'product' => [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'isbn' => $product->product_isbn,
                    'price' => $product->product_price,
                    'condition' => $product->condition,

                    'image_urls' => [
                        'large' => $product->product_image ? $basePath . '/large/' . $product->product_image : null,
                        'medium' => $product->product_image ? $basePath . '/medium/' . $product->product_image : null,
                        'small' => $product->product_image ? $basePath . '/small/' . $product->product_image : null,
                    ],

                    'description' => $product->description,

                    'section' => $product->section,
                    'category' => $product->category,
                    'subcategory' => $product->subcategory,
                    'publisher' => $product->publisher,
                    'subject' => $product->subject,
                    'book_type' => $product->bookType,
                    'language' => $product->language,
                    'edition' => $product->edition,

                    'author_name' => $authorNames,

                    'authors' => $product->authors->map(function ($author) {
                        return [
                            'id' => is_array($author) ? ($author['id'] ?? null) : ($author->id ?? null),
                            'name' => is_array($author) ? ($author['name'] ?? null) : ($author->name ?? null),
                        ];
                    })
                ],

                'seller_type' => $attribute->vendor_id ? 'vendor' : ($attribute->user_id ? 'user' : 'unknown'),
                'vendor_offer' => $attribute->vendor_id ? [
                    'attribute_id' => $attribute->id,
                    'stock' => $attribute->stock,
                    'discount' => $attribute->product_discount,
                    'price_details' => Product::getDiscountPriceDetailsByAttribute($attribute->id, $attribute),
                    'sku' => $attribute->sku,
                    'old_book_condition' => $attribute->condition ? [
                        'id' => $attribute->condition->id,
                        'name' => $attribute->condition->name,
                        'percentage' => $attribute->condition->percentage,
                    ] : null,

                    'vendor' => [
                        'vendor_id' => $attribute->vendor->id ?? null,
                        'name' => $attribute->vendor->user->name ?? 'Verified Seller',
                        'shop_name' => $attribute->vendor->shop_name ?? null,
                        'address' => $attribute->vendor->address ?? null,
                        'city' => $attribute->vendor->city ?? null,
                        'state' => $attribute->vendor->state ?? null,
                        'pincode' => $attribute->vendor->pincode ?? null,
                        'phone' => $attribute->vendor->phone ?? null,
                        'gst_number' => $attribute->vendor->gst_number ?? null,
                        'status' => $attribute->vendor->status ?? null,

                        'user' => [
                            'user_id' => $attribute->vendor->user->id ?? null,
                            'name' => $attribute->vendor->user->name ?? 'Verified Seller',
                            'email' => $attribute->vendor->user->email ?? null,
                            'mobile' => $attribute->vendor->user->mobile ?? null,
                            'image' => $attribute->vendor->user->image ?? null,
                            'created_at' => $attribute->vendor->user->created_at ?? null,
                        ],
                        'distance' => $distance
                    ]
                ] : null,

                'user_offer' => $attribute->user_id && !$attribute->vendor_id ? [
                    'attribute_id' => $attribute->id,
                    'stock' => $attribute->stock,
                    'discount' => $attribute->product_discount,
                    'price_details' => Product::getDiscountPriceDetailsByAttribute($attribute->id, $attribute),
                    'sku' => $attribute->sku,
                    'show_contact' => $attribute->show_contact,
                    'contact_details_paid' => $attribute->contact_details_paid,
                    'platform_charge' => $attribute->platform_charge,
                    'is_sold' => $attribute->is_sold,
                    'old_book_condition' => $attribute->condition ? [
                        'id' => $attribute->condition->id,
                        'name' => $attribute->condition->name,
                        'percentage' => $attribute->condition->percentage,
                    ] : null,
                    'user' => [
                        'user_id' => $attribute->user->id ?? null,
                        'name' => $attribute->user->name ?? 'Verified Seller',
                        'email' => $attribute->user->email ?? null,
                        'mobile' => $attribute->user->mobile ?? null,
                        'image' => $attribute->user->image ?? null,
                        'address' => $attribute->user->address ?? null,
                        'created_at' => $attribute->user->created_at ?? null,
                    ],
                    'user_old_book_video' => $attribute->video_upload ? url('front/videos/product_videos/' . $attribute->video_upload) : null,
                ] : null,

                'cart_status' => [
                    'in_cart' => $inCart,
                    'quantity' => $cartQty
                ]
            ]
        ]);
    }

    public function getCart(Request $request)
    {
        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : 0;
        $session_id = $request->header('session-id', '');

        if (!$user_id && empty($session_id)) {
            return response()->json([
                'status' => true,
                'data' => [
                    'cart_items' => [],
                    'total_price' => 0,
                    'total_items' => 0
                ]
            ]);
        }

        $cartItems = Cart::with([
            'product' => function ($q) {
                $q->select('id', 'category_id', 'product_name', 'product_image');
            },
            'attribute'
        ])
            ->where(function ($q) use ($user_id, $session_id) {
                if ($user_id > 0) {
                    $q->where('user_id', $user_id);
                } else {
                    $q->where('session_id', $session_id);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        $total_price = 0;
        $total_items = 0;
        $basePath = url('front/images/product_images');

        foreach ($cartItems as $item) {

            $price = Product::getDiscountPriceDetailsByAttribute($item->product_attribute_id, $item->attribute);

            $item->product_price = round($price['product_price'] ?? 0);
            $item->final_price = round($price['final_price'] ?? 0);
            $item->discount_amount = round($price['discount'] ?? 0);

            $item->discount_percent = 0;
            if ($item->product_price > 0 && $item->discount_amount > 0) {
                $item->discount_percent = round(($item->discount_amount / $item->product_price) * 100);
            }

            if ($item->product) {
                $item->product->image_urls = [
                    'large' => $item->product->product_image ? $basePath . '/large/' . $item->product->product_image : null,
                    'medium' => $item->product->product_image ? $basePath . '/medium/' . $item->product->product_image : null,
                    'small' => $item->product->product_image ? $basePath . '/small/' . $item->product->product_image : null,
                ];
            }

            $qty = $item->quantity ?? 1;

            $total_price += round($price['final_price'] ?? 0) * $qty;
            $total_items += $qty;
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart fetched successfully',
            'data' => [
                'cart_items' => $cartItems,
                'total_price' => round($total_price),
                'total_items' => $total_items
            ]
        ]);
    }

    public function getDeliverySettings()
    {
        $setting = DeliverySetting::first();

        return response()->json([
            'status' => true,
            'message' => 'Delivery settings fetched successfully',
            'data' => [
                'min_order_amount' => $setting->min_order_amount,
                'delivery_charge' => $setting->delivery_charge,
                'is_free_delivery' => $setting->is_free_delivery,
                'status' => $setting->status,
            ]
        ]);
    }

    public function updateDeliverySettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'min_order_amount' => 'required|numeric|min:0',
            'delivery_charge' => 'required|numeric|min:0',
            'is_free_delivery' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $setting = DeliverySetting::first();

        if (!$setting) {
            // Create new if not exists
            $setting = DeliverySetting::create([
                'min_order_amount' => $request->min_order_amount,
                'delivery_charge' => $request->delivery_charge,
                'is_free_delivery' => $request->is_free_delivery ?? false,
                'status' => $request->status ?? true,
            ]);
        } else {
            // Update existing
            $setting->update([
                'min_order_amount' => $request->min_order_amount,
                'delivery_charge' => $request->delivery_charge,
                'is_free_delivery' => $request->is_free_delivery ?? $setting->is_free_delivery,
                'status' => $request->status ?? $setting->status,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Delivery settings updated successfully',
            'data' => $setting
        ]);
    }

    public function cartAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_attribute_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $qty = $request->quantity;
        $attribute = ProductsAttribute::where('id', $request->product_attribute_id)->where('status', 1)->first();

        if (!$attribute) {
            return response()->json(['status' => false, 'message' => 'Invalid product.'], 404);
        }

        if ($attribute->stock < $qty) {
            return response()->json(['status' => false, 'message' => 'Requested quantity not available.'], 400);
        }

        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : 0;
        $session_id = $request->header('session-id', '');

        if (!$user_id && empty($session_id)) {
            $session_id = uniqid('guest_');
        }

        $cartItem = Cart::where('product_attribute_id', $attribute->id)
            ->where(function ($q) use ($user_id, $session_id) {
                if ($user_id > 0) {
                    $q->where('user_id', $user_id);
                } else {
                    $q->where('session_id', $session_id);
                }
            })
            ->first();

        if ($cartItem) {
            if ($cartItem->quantity + $qty > $attribute->stock) {
                return response()->json(['status' => false, 'message' => 'Stock limit exceeded.'], 400);
            }
            $cartItem->increment('quantity', $qty);
        } else {
            Cart::create([
                'session_id' => $session_id,
                'user_id' => $user_id,
                'product_attribute_id' => $attribute->id,
                'product_id' => $attribute->product_id,
                'quantity' => $qty,
            ]);
        }

        $totalItems = Cart::where(function ($q) use ($user_id, $session_id) {
            if ($user_id > 0) {
                $q->where('user_id', $user_id);
            } else {
                $q->where('session_id', $session_id);
            }
        })->sum('quantity');

        return response()->json([
            'status' => true,
            'message' => 'Product added to cart successfully',
            'session_id' => $session_id,
            'totalCartItems' => $totalItems
        ]);
    }

    public function cartUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cartid' => 'required|integer',
            'qty' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $cartDetails = Cart::find($request->cartid);
        if (!$cartDetails) {
            return response()->json(['status' => false, 'message' => 'Cart item not found'], 404);
        }

        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : 0;
        $session_id = $request->header('session-id', '');

        if ($user_id > 0 && $cartDetails->user_id != $user_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } elseif ($user_id == 0 && $cartDetails->session_id != $session_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $availableStock = ProductsAttribute::where('id', $cartDetails->product_attribute_id)->value('stock');

        if ($request->qty > $availableStock) {
            return response()->json(['status' => false, 'message' => 'Stock not available'], 400);
        }

        $cartDetails->update(['quantity' => $request->qty]);

        return $this->getCart($request);
    }

    public function cartDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cartid' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $cartDetails = Cart::find($request->cartid);
        if (!$cartDetails) {
            return response()->json(['status' => false, 'message' => 'Cart item not found'], 404);
        }

        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : 0;
        $session_id = $request->header('session-id', '');

        if ($user_id > 0 && $cartDetails->user_id != $user_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } elseif ($user_id == 0 && $cartDetails->session_id != $session_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartDetails->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart'
        ]);
    }

    public function wishlist(Request $request)
    {
        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : 0;
        $session_id = $request->header('session-id', '');

        if (!$user_id && empty($session_id)) {
            return response()->json([
                'status' => true,
                'data' => [
                    'wishlist_items' => [],
                    'total_price' => 0,
                    'total_items' => 0
                ]
            ]);
        }

        $wishlists = Wishlist::with([
            'attribute.vendor',
            'attribute.product',
            'attribute.condition',
            'product' => function ($q) {
                $q->select(
                    'id',
                    'category_id',
                    'product_name',
                    'product_image',
                    'product_isbn',
                    'subcategory_id',
                    'subject_id',
                    'language_id',
                    'book_type_id'
                )->with([
                            'subcategory',
                            'subject',
                            'language',
                            'bookType'
                        ]);
            }
        ])
            ->where(function ($q) use ($user_id, $session_id) {
                if ($user_id > 0) {
                    $q->where('user_id', $user_id);
                } else {
                    $q->where('session_id', $session_id);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        $total_price = 0;
        $total_items = 0;
        $basePath = url('front/images/product_images');

        foreach ($wishlists as $item) {

            $priceDetails = Product::getDiscountPriceDetailsByAttribute($item->product_attribute_id, $item->attribute);

            $item->product_price = round($priceDetails['product_price'] ?? 0);
            $item->final_price = round($priceDetails['final_price'] ?? 0);
            $item->discount_amount = round($priceDetails['discount'] ?? 0);

            $item->discount_percent = 0;
            if ($item->product_price > 0 && $item->discount_amount > 0) {
                $item->discount_percent = round(($item->discount_amount / $item->product_price) * 100);
            }

            if ($item->product) {
                $item->product->image_urls = [
                    'large' => $item->product->product_image ? $basePath . '/large/' . $item->product->product_image : null,
                    'medium' => $item->product->product_image ? $basePath . '/medium/' . $item->product->product_image : null,
                    'small' => $item->product->product_image ? $basePath . '/small/' . $item->product->product_image : null,
                ];
            }

            $qty = $item->quantity ?? 1;

            $total_price += round($priceDetails['final_price'] ?? 0) * $qty;
            $total_items += $qty;
        }

        return response()->json([
            'status' => true,
            'message' => 'Wishlist fetched successfully',
            'data' => [
                'wishlist_items' => $wishlists,
                'total_price' => round($total_price),
                'total_items' => $total_items
            ]
        ]);
    }

    public function wishlistAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_attribute_id' => 'required|integer',
            'quantity' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $qty = $request->quantity ?? 1;
        $attribute = ProductsAttribute::where('id', $request->product_attribute_id)->where('status', 1)->first();

        if (!$attribute) {
            return response()->json(['status' => false, 'message' => 'Invalid product.'], 404);
        }

        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : 0;
        $session_id = $request->header('session-id', '');

        if (!$user_id && empty($session_id)) {
            $session_id = uniqid('guest_');
        }

        $query = Wishlist::where('product_attribute_id', $attribute->id)
            ->where(function ($q) use ($user_id, $session_id) {
                if ($user_id > 0) {
                    $q->where('user_id', $user_id);
                } else {
                    $q->where('session_id', $session_id);
                }
            });

        if ($query->exists()) {
            $query->increment('quantity', $qty);
        } else {
            Wishlist::create([
                'session_id' => $session_id,
                'user_id' => $user_id,
                'product_id' => $attribute->product_id,
                'product_attribute_id' => $attribute->id,
                'quantity' => $qty,
            ]);
        }

        $totalItems = Wishlist::where(function ($q) use ($user_id, $session_id) {
            if ($user_id > 0) {
                $q->where('user_id', $user_id);
            } else {
                $q->where('session_id', $session_id);
            }
        })->sum('quantity');

        return response()->json([
            'status' => true,
            'message' => 'Product added to wishlist successfully',
            'session_id' => $session_id,
            'totalWishlistItems' => $totalItems
        ]);
    }

    public function wishlistRemove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wishlist_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $wishlistDetails = Wishlist::find($request->wishlist_id);
        if (!$wishlistDetails) {
            return response()->json(['status' => false, 'message' => 'Wishlist item not found'], 404);
        }

        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : 0;
        $session_id = $request->header('session-id', '');

        if ($user_id > 0 && $wishlistDetails->user_id != $user_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } elseif ($user_id == 0 && $wishlistDetails->session_id != $session_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $wishlistDetails->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item removed from wishlist'
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'cart_items' => 'required|array',
            'cart_items.*.product_attribute_id' => 'required|integer',
            'cart_items.*.quantity' => 'required|integer|min:1'
        ]);

        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : 0;

        $couponCode = $request->code;
        $cartItems = $request->cart_items;

        /** Get Coupon */
        $coupon = Coupon::where('coupon_code', $couponCode)
            ->where('status', 1)
            ->whereDate('expiry_date', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired coupon'
            ]);
        }

        /** Single Time Coupon Check */
        if ($coupon->coupon_type == 'Single Time' && $user_id > 0) {

            $used = Order::where([
                'coupon_code' => $couponCode,
                'user_id' => $user_id
            ])->exists();

            if ($used) {
                return response()->json([
                    'status' => false,
                    'message' => 'This coupon already used by you'
                ]);
            }
        }

        /** Calculate Cart Subtotal and Discountable Amount */
        $subTotal = 0;
        $discountableSubTotal = 0;

        foreach ($cartItems as $item) {

            $attribute = ProductsAttribute::with('product')
                ->where('id', $item['product_attribute_id'])
                ->first();

            if (!$attribute)
                continue;

            $product = $attribute->product;

            $priceDetails = Product::getDiscountPriceDetailsByAttribute($item['product_attribute_id']);

            $itemPrice = $priceDetails['final_price'];

            $itemTotal = $itemPrice * $item['quantity'];

            $subTotal += $itemTotal;

            // Check if this item is eligible for the coupon
            $isEligible = true;

            /** Category Restriction */
            if (!empty($coupon->categories)) {
                $catArr = explode(',', $coupon->categories);
                if (!in_array($product->category_id, $catArr)) {
                    $isEligible = false;
                }
            }

            /** Vendor Restriction */
            if ($coupon->vendor_id && $attribute->vendor_id != $coupon->vendor_id) {
                $isEligible = false;
            }

            if ($isEligible) {
                $discountableSubTotal += $itemTotal;
            }
        }

        if ($discountableSubTotal <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'This coupon is not valid for any items in your cart.'
            ]);
        }

        $subTotal = round($subTotal);
        $discountableSubTotal = round($discountableSubTotal);

        /** Coupon Calculation */
        if ($coupon->amount_type === 'Percentage') {
            $couponDiscount = round(($discountableSubTotal * $coupon->amount) / 100);
        } else {
            $couponDiscount = round($coupon->amount);
        }

        /** Prevent Over Discount */
        $couponDiscount = min($couponDiscount, $discountableSubTotal);

        $grandTotal = $subTotal - $couponDiscount;

        return response()->json([
            'status' => true,
            'data' => [
                'coupon_code' => $coupon->coupon_code,
                'amount_type' => $coupon->amount_type,
                'amount' => $coupon->amount,
                'sub_total' => $subTotal,
                'coupon_discount' => $couponDiscount,
                'grand_total' => $grandTotal
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_gateway' => 'required|string',
            'accept_terms' => 'required|boolean',
            'coupon_code' => 'nullable|string',
            'coupon_amount' => 'nullable|numeric|min:0',
            'use_wallet' => 'nullable|boolean',
            'address_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        if ($request->address_id) {
            $selectedAddress = UserAddress::with(['country', 'state', 'district', 'block'])
                ->where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$selectedAddress) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selected address not found'
                ], 404);
            }

            $orderName = $selectedAddress->name;
            $orderAddress = $selectedAddress->address;
            $orderCity = $selectedAddress->district->name ?? '';
            $orderState = $selectedAddress->state->name ?? '';
            $orderCountry = $selectedAddress->country->name ?? '';
            $orderPincode = $selectedAddress->pincode;
            $orderMobile = $selectedAddress->mobile;
            $orderEmail = $user->email;
        } else {
            $user->load(['country', 'state', 'district']);

            if (empty($user->address) || empty($user->district_id) || empty($user->state_id) || empty($user->country_id) || empty($user->pincode) || empty($user->phone)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please update your address before checkout'
                ], 400);
            }

            $orderName = $user->name;
            $orderAddress = $user->address;
            $orderCity = $user->district->name ?? '';
            $orderState = $user->state->name ?? '';
            $orderCountry = $user->country->name ?? '';
            $orderPincode = $user->pincode;
            $orderMobile = $user->phone;
            $orderEmail = $user->email;
        }

        $cartItems = Cart::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        $total_price = 0;
        $discountableSubTotal = 0;

        foreach ($cartItems as $item) {

            $attribute = ProductsAttribute::with('product')
                ->where('id', $item->product_attribute_id)
                ->first();

            if (!$attribute || !$attribute->product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product no longer available'
                ], 400);
            }

            if ($attribute->product->status == 0 || $attribute->status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => $attribute->product->product_name . ' not available'
                ], 400);
            }

            if ($attribute->stock < $item->quantity) {
                return response()->json([
                    'status' => false,
                    'message' => $attribute->product->product_name . ' stock insufficient'
                ], 400);
            }

            $priceDetails = Product::getDiscountPriceDetailsByAttribute($item->product_attribute_id);
            $itemPrice = $priceDetails['final_price'];
            $itemTotal = $itemPrice * $item->quantity;

            $total_price += $itemTotal;

            /** PRE-CHCEK ELIGIBILITY FOR COUPON RE-CALCULATION */
            if ($request->coupon_code) {
                $coupon = Coupon::where('coupon_code', $request->coupon_code)
                    ->where('status', 1)
                    ->whereDate('expiry_date', '>=', now())
                    ->first();

                if ($coupon) {
                    $isEligible = true;

                    // Category Restriction
                    if (!empty($coupon->categories)) {
                        $catArr = explode(',', $coupon->categories);
                        if (!in_array($attribute->product->category_id, $catArr)) {
                            $isEligible = false;
                        }
                    }

                    // Vendor Restriction
                    if ($coupon->vendor_id && $attribute->vendor_id != $coupon->vendor_id) {
                        $isEligible = false;
                    }

                    if ($isEligible) {
                        $discountableSubTotal += $itemTotal;
                    }
                }
            }
        }

        $deliverySetting = \App\Models\DeliverySetting::first();
        $min_order_amount = $deliverySetting ? $deliverySetting->min_order_amount : 499;
        $delivery_fee = $deliverySetting ? $deliverySetting->delivery_charge : 20;

        $shipping_charges = 0;
        if ($request->payment_gateway != 'PICKUP') {
            if ($total_price >= $min_order_amount) {
                $shipping_charges = 0;
            } else {
                $shipping_charges = $delivery_fee;
            }
        }

        // SECURE COUPON CALCULATION (Don't trust $request->coupon_amount)
        $coupon_amount = 0;
        if ($request->coupon_code && $discountableSubTotal > 0) {
            $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();
            if ($coupon) {
                if ($coupon->amount_type === 'Percentage') {
                    $coupon_amount = round(($discountableSubTotal * $coupon->amount) / 100);
                } else {
                    $coupon_amount = round($coupon->amount);
                }
                $coupon_amount = min($coupon_amount, $discountableSubTotal);
            }
        }

        $grand_total = $total_price + $shipping_charges - $coupon_amount;

        $wallet_amount = 0;

        if ($request->use_wallet && $user->wallet_balance > 0 && $grand_total > 150) {

            $wallet_amount = min($user->wallet_balance, 20);

            $grand_total -= $wallet_amount;
        }

        if ($request->payment_gateway == 'COD') {
            $payment_method = 'COD';
            $order_status = 'New';
        } elseif ($request->payment_gateway == 'PICKUP') {
            $payment_method = 'Pickup from store';
            $order_status = 'Pending';
        } else {
            $payment_method = 'Prepaid';
            $order_status = 'Pending';
        }

        DB::beginTransaction();

        try {
            $order_ids = [];
            $eligible_item_ids = [];
            if ($coupon) {
                foreach ($cartItems as $item) {
                    $attribute = ProductsAttribute::with('product')->find($item->product_attribute_id);
                    if (!$attribute)
                        continue;
                    $product = $attribute->product;

                    $isEligible = true;
                    if (!empty($coupon->categories)) {
                        $catArr = explode(',', $coupon->categories);
                        if (!in_array($product->category_id, $catArr)) {
                            $isEligible = false;
                        }
                    }
                    if ($coupon->vendor_id && $attribute->vendor_id != $coupon->vendor_id) {
                        $isEligible = false;
                    }

                    if ($isEligible) {
                        $eligible_item_ids[] = $item->product_attribute_id;
                    }
                }
            }

            foreach ($cartItems as $item) {
                $attribute = ProductsAttribute::with('product')->find($item->product_attribute_id);
                $priceDetails = Product::getDiscountPriceDetailsByAttribute($item->product_attribute_id);
                $item_price = $priceDetails['final_price'] * $item->quantity;

                // Proportional distribution
                $proportion = ($total_price > 0) ? ($item_price / $total_price) : 0;
                $item_shipping = round($shipping_charges * $proportion, 2);

                // Coupon Logic: Only apply if item is eligible
                $item_coupon = 0;
                if ($coupon && in_array($item->product_attribute_id, $eligible_item_ids) && $discountableSubTotal > 0) {
                    $coupon_proportion = $item_price / $discountableSubTotal;
                    $item_coupon = round($coupon_amount * $coupon_proportion, 2);
                }

                $item_wallet = round($wallet_amount * $proportion, 2);

                $item_grand_total = max(0, $item_price + $item_shipping - $item_coupon - $item_wallet);

                $order = Order::create([
                    'user_id' => $user->id,
                    'name' => $orderName,
                    'address' => $orderAddress,
                    'city' => $orderCity,
                    'state' => $orderState,
                    'country' => $orderCountry,
                    'pincode' => $orderPincode,
                    'mobile' => $orderMobile,
                    'email' => $orderEmail,
                    'shipping_charges' => $item_shipping,
                    'coupon_code' => ($item_coupon > 0) ? $request->coupon_code : null,
                    'coupon_amount' => $item_coupon,
                    'order_status' => $order_status,
                    'payment_method' => $payment_method,
                    'payment_gateway' => $request->payment_gateway,
                    'grand_total' => $item_grand_total,
                    'wallet_amount' => $item_wallet,
                    'extra_discount' => 0
                ]);

                $order_ids[] = $order->id;

                $commission = 0;
                if ($attribute->vendor_id && $attribute->vendor_id > 0) {
                    $commission = Vendor::getVendorCommission($attribute->vendor_id);
                }

                OrdersProduct::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'admin_id' => $attribute->admin_id ?? 0,
                    'vendor_id' => $attribute->vendor_id ?? 0,
                    'product_id' => $attribute->product_id,
                    'product_attribute_id' => $item->product_attribute_id,
                    'product_name' => $attribute->product->product_name,
                    'product_price' => $priceDetails['final_price'],
                    'product_qty' => $item->quantity,
                    'item_status' => $order_status,
                    'commission' => $commission
                ]);

                if (($attribute->vendor_id ?? 0) > 0) {
                    Notification::create([
                        'type' => 'order_placed',
                        'title' => 'New Order Received',
                        'message' => 'A customer placed an order containing your product: ' . ($attribute->product->product_name ?? 'Product') . '.',
                        'related_id' => $order->id,
                        'related_type' => 'App\Models\Order',
                        'vendor_id' => $attribute->vendor_id,
                        'is_read' => false,
                    ]);
                }
            }

            if ($wallet_amount > 0) {
                $user->wallet_balance -= $wallet_amount;
                $user->save();

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order_ids[0],
                    'amount' => $wallet_amount,
                    'type' => 'debit',
                    'description' => 'Used for API orders: ' . implode(', ', $order_ids)
                ]);
            }

            if ($request->payment_gateway == 'COD') {

                // Reduce stock for COD orders immediately
                foreach ($cartItems as $item) {
                    ProductsAttribute::where('id', $item->product_attribute_id)
                        ->decrement('stock', $item->quantity);
                }

                // Cashback to wallet
                $total_cashback = 0;
                foreach ($order_ids as $id) {
                    $total_cashback += WalletTransaction::checkAndCreditWallet($id);
                }

                Cart::where('user_id', $user->id)->delete();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Order placed successfully',
                    'order_id' => $order_ids[0],
                    'order_ids' => $order_ids,
                    'grand_total' => $total_price + $shipping_charges - $coupon_amount - $wallet_amount,
                    'cashback_amount' => $total_cashback,
                ]);
            }

            if ($request->payment_gateway == 'PICKUP') {

                // Reduce stock for PICKUP orders immediately
                foreach ($cartItems as $item) {
                    ProductsAttribute::where('id', $item->product_attribute_id)
                        ->decrement('stock', $item->quantity);
                }

                // Cashback to wallet
                $total_cashback = 0;
                foreach ($order_ids as $id) {
                    $total_cashback += WalletTransaction::checkAndCreditWallet($id);
                }

                Cart::where('user_id', $user->id)->delete();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Pickup order placed successfully. Visit the store to collect your order.',
                    'order_id' => $order_ids[0],
                    'order_ids' => $order_ids,
                    'grand_total' => $grand_total,
                    'cashback_amount' => $total_cashback,
                ]);
            }

            if ($request->payment_gateway == 'Razorpay') {

                $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

                $total_grand_total = Order::whereIn('id', $order_ids)->sum('grand_total');

                $razorpayOrder = $api->order->create([
                    'receipt' => 'order_' . implode('_', $order_ids),
                    'amount' => round($total_grand_total * 100),
                    'currency' => 'INR'
                ]);

                Order::whereIn('id', $order_ids)->update(['razorpay_order_id' => $razorpayOrder['id']]);

                DB::commit();

                return response()->json([
                    'status' => true,
                    'order_id' => $order_ids[0],
                    'order_ids' => $order_ids,
                    'razorpay_order_id' => $razorpayOrder['id'],
                    'amount' => $total_grand_total
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Checkout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyRazorpayPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ]);

            $order_ids = is_array($request->order_id) ? $request->order_id : [$request->order_id];
            $orders = Order::with('orders_products')->whereIn('id', $order_ids)->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Orders not found'
                ], 404);
            }

            if (Payment::where('payment_id', $request->razorpay_payment_id)->exists()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payment already verified'
                ]);
            }

            foreach ($orders as $order) {
                $order->update([
                    'payment_status' => 'Paid',
                    'order_status' => 'Paid'
                ]);

                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'payment_id' => $request->razorpay_payment_id,
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_signature' => $request->razorpay_signature,
                    'payer_id' => $order->user_id,
                    'payer_email' => $order->email,
                    'amount' => $order->grand_total,
                    'currency' => 'INR',
                    'payment_status' => 'Captured'
                ]);

                foreach ($order->orders_products as $item) {
                    ProductsAttribute::where('id', $item->product_attribute_id)
                        ->decrement('stock', $item->product_qty);
                }

                WalletTransaction::checkAndCreditWallet($order->id);
            }

            Cart::where('user_id', $orders->first()->user_id)->delete();

            $total_cashback = 0;
            foreach ($order_ids as $id) {
                // Since checkAndCreditWallet might return the added amount
                $total_cashback += WalletTransaction::checkAndCreditWallet($id);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment verified successfully',
                'cashback_amount' => $total_cashback
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }
    }

    public function orders(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User must be logged in to view orders'
            ], 401);
        }

        $orders = Order::with([
            'orders_products' => function ($query) {
                // Load the associated product attribute and product to get images/details if needed
                // The orders_products table already has product_name, product_price, product_qty, etc.
            }
        ])
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(10); // Paginate the results, 10 orders per page

        // Format the orders to include full details
        $formattedOrders = $orders->through(function ($order) {

            $basePath = url('front/images/product_images');

            // Format order products
            $products = $order->orders_products->map(function ($item) use ($basePath) {
                // Fetch product details for image
                $product = Product::find($item->product_id);
                $imageUrl = null;

                if ($product && $product->product_image) {
                    $imageUrl = $basePath . '/small/' . $product->product_image;
                }

                return [
                    'order_product_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_attribute_id' => $item->product_attribute_id,
                    'product_name' => $item->product_name,
                    'product_price' => $item->product_price,
                    'product_qty' => $item->product_qty,
                    'product_image' => $imageUrl,
                    'vendor_id' => $item->vendor_id,
                    'admin_id' => $item->admin_id,
                ];
            });

            return [
                'order_id' => $order->id,
                'date' => $order->created_at->format('Y-m-d H:i:s'),
                'name' => $order->name,
                'address' => $order->address,
                'city' => $order->city,
                'state' => $order->state,
                'country' => $order->country,
                'pincode' => $order->pincode,
                'mobile' => $order->mobile,
                'email' => $order->email,
                'shipping_charges' => $order->shipping_charges,
                'coupon_code' => $order->coupon_code,
                'coupon_amount' => $order->coupon_amount,
                'order_status' => $order->order_status,
                'payment_method' => $order->payment_method,
                'payment_gateway' => $order->payment_gateway,
                'payment_status' => $order->payment_status,
                'grand_total' => $order->grand_total,
                'wallet_amount' => $order->wallet_amount,
                'products' => $products
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Orders fetched successfully',
            'data' => [
                'total' => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'orders' => $formattedOrders
            ]
        ]);
    }

    public function orderStatus(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User must be logged in to view order status'
            ], 401);
        }

        $order = Order::with([
            'logs' => function ($query) {
                $query->orderBy('id', 'asc');
            },
            'orders_products.product'
        ])->where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $basePath = url('front/images/product_images');

        return response()->json([
            'status' => true,
            'message' => 'Order status fetched successfully',
            'data' => [
                'order_id' => $order->id,
                'date' => $order->created_at->format('d M Y, h:i A'),
                'grand_total' => $order->grand_total,
                'shipping_charges' => $order->shipping_charges,
                'wallet_amount' => $order->wallet_amount,
                'coupon_amount' => $order->coupon_amount,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'payment_gateway' => $order->payment_gateway,
                'name' => $order->name,
                'address' => $order->address,
                'city' => $order->city,
                'state' => $order->state,
                'pincode' => $order->pincode,
                'mobile' => $order->mobile,
                'current_status' => $order->order_status,
                'tracking_number' => $order->tracking_number,
                'courier_name' => $order->courier_name,
                'delivered_at' => $order->delivered_at,
                'return_status' => $order->return_status,
                'return_reason' => $order->return_reason,
                'products' => $order->orders_products->map(function ($item) use ($basePath) {
                    $imageUrl = null;
                    if ($item->product && $item->product->product_image) {
                        $imageUrl = $basePath . '/small/' . $item->product->product_image;
                    }
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name,
                        'product_image' => $imageUrl,
                        'product_price' => $item->product_price,
                        'product_qty' => $item->product_qty
                    ];
                }),
                'logs' => $order->logs->map(function ($log) {
                    return [
                        'status' => $log->order_status,
                        'date' => $log->created_at->format('d M Y, h:i A'),
                    ];
                })
            ]
        ]);
    }

    public function orderReceipt(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User must be logged in to view order receipt'
            ], 401);
        }

        // Fetch order with all needed relationships for a receipt
        $order = Order::with([
            'orders_products.product',
            'orders_products.vendor_details.vendorbusinessdetails'
        ])->where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Format itemized items with original price and vendor info
        $items = $order->orders_products->map(function ($item) {
            $originalPrice = $item->product->product_price ?? $item->product_price;

            // Collect Vendor details with robust fallback
            $vendorDetail = $item->vendor_details->vendorbusinessdetails ?? null;
            $vendorInfo = null;

            if ($vendorDetail) {
                $vendorInfo = [
                    'shop_name' => $vendorDetail->shop_name,
                    'address' => $vendorDetail->shop_address,
                    'city' => $vendorDetail->shop_city,
                    'state' => $vendorDetail->shop_state,
                    'pincode' => $vendorDetail->shop_pincode,
                    'mobile' => $vendorDetail->shop_mobile,
                    'email' => $vendorDetail->shop_email,
                ];
            } else {
                // Check if we can find vendor info from the vendor User relationship
                $vendorUser = $item->vendor; // Points to User model
                if ($vendorUser && $vendorUser->vendor && $vendorUser->vendor->vendorbusinessdetails) {
                    $vDetail = $vendorUser->vendor->vendorbusinessdetails;
                    $vendorInfo = [
                        'shop_name' => $vDetail->shop_name,
                        'address' => $vDetail->shop_address,
                        'city' => $vDetail->shop_city,
                        'state' => $vDetail->shop_state,
                        'pincode' => $vDetail->shop_pincode,
                        'mobile' => $vDetail->shop_mobile,
                        'email' => $vDetail->shop_email,
                    ];
                }
            }

            // Final Fallback: If still no vendor info found, use Admin Store
            if (!$vendorInfo) {
                $vendorInfo = [
                    'shop_name' => 'BookHub Official Store',
                    'address' => 'Rairangpur, Mayurbhanj',
                    'city' => 'Mayurbhanj',
                    'state' => 'Odisha',
                    'pincode' => '757043',
                    'mobile' => '7008101416',
                    'email' => 'support@mybookhub.in',
                ];
            }

            return [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'original_price' => round($originalPrice),
                'selling_price' => round($item->product_price),
                'quantity' => $item->product_qty,
                'total_item_amount' => round($item->product_price * $item->product_qty),
                'vendor' => $vendorInfo
            ];
        });

        // Calculate Subtotal (sum of selling prices * qty)
        $subtotal = $items->sum('total_item_amount');

        return response()->json([
            'status' => true,
            'message' => 'Order receipt details fetched successfully',
            'data' => [
                'order_details' => [
                    'order_id' => $order->id,
                    'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                    'order_status' => $order->order_status,
                    'payment_method' => $order->payment_method,
                ],
                'customer_details' => [
                    'name' => $order->name,
                    'address' => $order->address,
                    'city' => $order->city,
                    'state' => $order->state,
                    'country' => $order->country,
                    'pincode' => $order->pincode,
                    'mobile' => $order->mobile,
                    'email' => $order->email,
                ],
                'items' => $items,
                'billing_summary' => [
                    'total_item_cost' => round($subtotal),
                    'coupon_discount' => round($order->coupon_amount ?? 0),
                    'wallet_discount' => round($order->wallet_amount ?? 0),
                    'delivery_charges' => round($order->shipping_charges ?? 0),
                    'total_amount' => round($order->grand_total),
                ]
            ]
        ]);
    }

    public function cancelOrder(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $order = Order::where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $allowedStatuses = ['new', 'pending', 'in progress'];
        if (!in_array(strtolower($order->order_status), $allowedStatuses)) {
            return response()->json([
                'status' => false,
                'message' => 'Only ' . implode(', ', array_map('ucwords', $allowedStatuses)) . ' orders can be cancelled. Current status is ' . $order->order_status
            ], 400);
        }

        $orderProductId = $request->order_product_id;

        DB::beginTransaction();

        try {
            if ($orderProductId) {
                // PARTIAL CANCELLATION: Only cancel specific item
                $productItem = OrdersProduct::where('id', $orderProductId)
                    ->where('order_id', $id)
                    ->first();

                if (!$productItem) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Order item not found'
                    ], 404);
                }

                $productItem->update(['item_status' => 'Cancelled']);

                // Notify vendor about cancelled item
                if (($productItem->vendor_id ?? 0) > 0) {
                    Notification::create([
                        'type' => 'order_cancelled',
                        'title' => 'Order Item Cancelled',
                        'message' => 'An item "' . ($productItem->product_name ?? 'Product') . '" in order #' . $order->id . ' was cancelled by the customer.',
                        'related_id' => $order->id,
                        'related_type' => 'App\Models\Order',
                        'vendor_id' => $productItem->vendor_id,
                        'is_read' => false,
                    ]);
                }

                // Check if ALL items are now cancelled
                $activeItemsCount = OrdersProduct::where('order_id', $id)
                    ->where('item_status', '!=', 'Cancelled')
                    ->count();

                if ($activeItemsCount == 0) {
                    $order->update(['order_status' => 'Cancelled']);
                    WalletTransaction::revertWallet($id);
                }

                $message = 'Item "' . $productItem->product_name . '" has been cancelled successfully.';
            } else {
                // FULL CANCELLATION: Mark everything as cancelled
                $order->update(['order_status' => 'Cancelled']);
                $orderItems = OrdersProduct::where('order_id', $id)->get();
                OrdersProduct::where('order_id', $id)->update(['item_status' => 'Cancelled']);
                WalletTransaction::revertWallet($id);

                // Notify all vendors involved in this order (unique vendor_ids)
                $vendorIds = $orderItems->pluck('vendor_id')->filter()->unique();
                foreach ($vendorIds as $vendorId) {
                    Notification::create([
                        'type' => 'order_cancelled',
                        'title' => 'Order Cancelled',
                        'message' => 'Order #' . $order->id . ' containing your products was cancelled by the customer.',
                        'related_id' => $order->id,
                        'related_type' => 'App\Models\Order',
                        'vendor_id' => $vendorId,
                        'is_read' => false,
                    ]);
                }

                $message = 'Entire order #' . $id . ' has been cancelled successfully.';
            }

            // Log status
            $log = new OrdersLog;
            $log->order_id = $id;
            $log->order_status = 'Cancelled';
            if ($orderProductId) {
                $log->order_item_id = $orderProductId;
            }
            $log->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to cancel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function returnOrder(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'return_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth('sanctum')->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        // Check if order is eligible for return
        if ($order->order_status != 'Delivered' || !$order->delivered_at) {
            return response()->json([
                'status' => false,
                'message' => 'Order must be delivered before initiating a return.'
            ], 400);
        }

        $deliveredDate = \Carbon\Carbon::parse($order->delivered_at);
        if ($deliveredDate->addDays(7)->isPast()) {
            return response()->json([
                'status' => false,
                'message' => 'Return period (7 days) has expired for this order.'
            ], 400);
        }

        // Update Return Status
        $order->order_status = 'Return Requested';
        $order->return_status = 'Return Requested';
        $order->return_reason = $request->return_reason;
        $order->save();

        // Also update all order items and notify vendors
        $items = OrdersProduct::where('order_id', $id)->get();
        OrdersProduct::where('order_id', $id)->update(['item_status' => 'Return Requested']);

        $vendorIds = $items->pluck('vendor_id')->filter()->unique();
        foreach ($vendorIds as $vendorId) {
            Notification::create([
                'type' => 'order_return_requested',
                'title' => 'Order Return Requested',
                'message' => "Customer '" . $user->name . "' has requested a return for Order #" . $id . ". Reason: " . $request->return_reason,
                'related_id' => $id,
                'related_type' => Order::class,
                'vendor_id' => $vendorId,
                'is_read' => false,
            ]);
        }

        // Notify Admin
        Notification::create([
            'type' => 'order_return_requested',
            'title' => 'Order Return Requested',
            'message' => "Customer '" . $user->name . "' has requested a return for Order #" . $id . ". Reason: " . $request->return_reason,
            'related_id' => $id,
            'related_type' => Order::class,
            'is_read' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Return request has been submitted successfully. Our team will review it and get back to you.'
        ]);
    }

    public function raiseQuery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'order_product_id' => 'required|exists:orders_products,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth('sanctum')->user();
        $order = Order::find($request->order_id);

        if ($order->user_id != $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $product = OrdersProduct::find($request->order_product_id);

        // Generate Ticket ID
        $ticket_id = 'TKT-' . strtoupper(Str::random(6));

        OrderQuery::create([
            'ticket_id' => $ticket_id,
            'order_id' => $request->order_id,
            'order_product_id' => $request->order_product_id,
            'user_id' => $user->id,
            'vendor_id' => $product->vendor_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Notify Admin
        Notification::create([
            'type' => 'order_query',
            'title' => 'New Order Query Raised',
            'message' => "Customer '" . $user->name . "' raised a query for Order #" . $request->order_id . " regarding '" . $product->product_name . "'. Ticket ID: " . $ticket_id,
            'related_id' => $request->order_id,
            'related_type' => Order::class,
            'is_read' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Query raised successfully. Your Ticket ID is ' . $ticket_id . '. Our team will get back to you soon.',
            'ticket_id' => $ticket_id
        ]);
    }

    public function orderQueries(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $queries = OrderQuery::with(['order', 'orderProduct', 'messages'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $formattedQueries = $queries->through(function ($query) {
            return [
                'id' => $query->id,
                'ticket_id' => $query->ticket_id,
                'order_id' => $query->order_id,
                'product_name' => $query->orderProduct->product_name ?? 'N/A',
                'subject' => $query->subject,
                'status' => strtolower($query->status),
                'date' => $query->created_at->format('M d, Y h:i A'),
                'messages_count' => $query->messages->count(),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Order queries fetched successfully',
            'data' => [
                'total' => $queries->total(),
                'current_page' => $queries->currentPage(),
                'last_page' => $queries->lastPage(),
                'queries' => $formattedQueries
            ]
        ]);
    }

    public function queryDetails($id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $query = OrderQuery::with(['order', 'orderProduct', 'messages.user', 'messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$query) {
            return response()->json(['status' => false, 'message' => 'Query not found.'], 404);
        }

        $messages = $query->messages->map(function ($msg) {
            $attachmentUrl = null;
            if ($msg->attachment) {
                // Determine if it already has full URL or just relative path
                $attachmentUrl = str_starts_with($msg->attachment, 'http') ? $msg->attachment : url($msg->attachment);
            }

            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_type' => $msg->sender_type,
                'attachment' => $attachmentUrl,
                'created_at' => $msg->created_at->format('d M Y, h:i A'),
                'user_name' => $msg->user ? $msg->user->name : 'Support Team',
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Query details fetched successfully',
            'data' => [
                'id' => $query->id,
                'ticket_id' => $query->ticket_id,
                'order_id' => $query->order_id,
                'product_name' => $query->orderProduct->product_name ?? 'N/A',
                'subject' => $query->subject,
                'message' => $query->message,
                'status' => strtolower($query->status),
                'date' => $query->created_at->format('d M Y, h:i A'),
                'messages' => $messages
            ]
        ]);
    }

    public function postQueryReply(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi,pdf|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $query = OrderQuery::where('user_id', $user->id)->where('id', $id)->first();

        if (!$query) {
            return response()->json(['status' => false, 'message' => 'Query not found.'], 404);
        }

        if (strtolower($query->status) == 'closed') {
            return response()->json(['status' => false, 'message' => 'This ticket is closed and cannot be replied to.'], 400);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('attachments/order_queries'), $filename);
            $attachmentPath = 'attachments/order_queries/' . $filename;
        }

        $newMessage = OrderQueryMessage::create([
            'order_query_id' => $id,
            'user_id' => $user->id,
            'message' => $request->message,
            'attachment' => $attachmentPath,
            'sender_type' => 'student',
        ]);

        // Optional: Notify Admin/Vendor
        Notification::create([
            'type' => 'order_query',
            'title' => 'New Reply for Ticket #' . $query->ticket_id,
            'message' => "Customer '" . $user->name . "' replied to Ticket #" . $query->ticket_id,
            'related_id' => $query->order_id,
            'related_type' => Order::class,
            'is_read' => false,
        ]);

        $fullAttachmentUrl = $attachmentPath ? url($attachmentPath) : null;

        return response()->json([
            'status' => true,
            'message' => 'Reply sent successfully.',
            'data' => [
                'id' => $newMessage->id,
                'message' => $newMessage->message,
                'sender_type' => $newMessage->sender_type,
                'attachment' => $fullAttachmentUrl,
                'created_at' => $newMessage->created_at->format('d M Y, h:i A'),
                'user_name' => $user->name,
            ]
        ]);
    }
}
