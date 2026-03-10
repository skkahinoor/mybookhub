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
use Razorpay\Api\Api;

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
            'language_id'  => 'nullable',
            'subcategory_id' => 'nullable|integer',
            'subject_id' => 'nullable',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'search' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user  = auth('sanctum')->user();
        $limit = $request->limit ?? 20;
        $page  = $request->page ?? 1;
        $lat   = $request->lat;
        $lng   = $request->lng;


        $cacheVersion = Cache::rememberForever('products_cache_version', function () {
            return time();
        });

        $cacheKey = 'products_v' . $cacheVersion . '_' .
            ($user ? 'user_' . $user->id : 'guest') .
            '_' . md5(json_encode($request->all()));

        $data = Cache::remember($cacheKey, 3600, function () use ($request, $user, $limit, $page, $lat, $lng) {
            return $this->sqlSearch($request, $limit, $page, $lat, $lng);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Products fetched successfully',
            'data'    => $data
        ]);
    }

    private function sqlSearch($request, $limit, $page, $lat, $lng)
    {
        $query = Product::with(['category', 'subcategory', 'section', 'subject', 'bookType', 'language'])
            ->select('products.*')
            ->join('products_attributes as pa', 'pa.product_id', '=', 'products.id')
            ->join('vendors as v', 'pa.vendor_id', '=', 'v.id')
            ->join('users as u', 'v.user_id', '=', 'u.id')
            ->addSelect(
                'pa.id as attribute_id',
                'pa.stock',
                'pa.product_discount',
                'v.id as vendor_id',
                'u.name as vendor_name',
                'v.plan as vendor_plan',
                'v.location'
            )
            ->where('pa.status', 1)
            ->where('products.status', 1)
            ->where('pa.stock', '>', 0);


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
                $q->where('products.product_isbn', $searchTerm)
                    ->orWhereRaw(
                        "MATCH(products.product_name, products.meta_title, products.meta_keywords)
                         AGAINST(? IN BOOLEAN MODE)",
                        [trim($booleanSearch)]
                    );
            });
        }

        if ($request->isbn) {
            $query->where('products.product_isbn', $request->isbn);
        }

        if ($request->section_id) {
            $query->where('products.section_id', $request->section_id);
        }

        if ($request->category_id) {
            $query->where('products.category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $query->where('products.subcategory_id', $request->subcategory_id);
        }

        if ($request->subject_id) {
            if (is_array($request->subject_id)) {
                $query->whereIn('products.subject_id', $request->subject_id);
            } else {
                $query->where('products.subject_id', $request->subject_id);
            }
        }

        if ($request->min_price) {
            $query->where('products.product_price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('products.product_price', '<=', $request->max_price);
        }

        if ($request->book_type_id) {
            if (is_array($request->book_type_id)) {
                $query->whereIn('products.book_type_id', $request->book_type_id);
            } else {
                $query->where('products.book_type_id', $request->book_type_id);
            }
        }

        if ($request->language_id) {
            if (is_array($request->language_id)) {
                $query->whereIn('products.language_id', $request->language_id);
            } else {
                $query->where('products.language_id', $request->language_id);
            }
        }


        if ($lat && $lng) {

            $query->addSelect(DB::raw("
                (6371 * acos(
                    cos(radians($lat)) *
                    cos(radians(CAST(SUBSTRING_INDEX(v.location, ',', 1) AS DECIMAL(10,6)))) *
                    cos(radians(CAST(SUBSTRING_INDEX(v.location, ',', -1) AS DECIMAL(10,6))) - radians($lng)) +
                    sin(radians($lat)) *
                    sin(radians(CAST(SUBSTRING_INDEX(v.location, ',', 1) AS DECIMAL(10,6))))
                )) AS distance
            "));
        }

        if ($lat && $lng) {
            $query->orderBy('distance', 'asc');
        }

        $query->orderByRaw("CASE WHEN v.plan='pro' THEN 1 ELSE 2 END ASC");

        $query->orderBy('pa.product_discount', 'desc');

        $query->orderBy('pa.stock', 'desc');

        $query->orderBy('products.product_price', 'asc');


        $results = $query->paginate($limit, ['*'], 'page', $page);

        return [
            'total'        => $results->total(),
            'current_page' => $results->currentPage(),
            'last_page'    => $results->lastPage(),
            'products'     => $results->through(function ($product) {

                $basePath = url('front/images/product_images');

                return [
                    'id'              => $product->id,
                    'product_name'    => $product->product_name,
                    'product_isbn'    => $product->product_isbn,
                    'product_price'   => $product->product_price,
                    'image_urls'       => [
                        'large'  => $product->product_image
                            ? $basePath . '/large/' . $product->product_image
                            : null,
                        'medium' => $product->product_image
                            ? $basePath . '/medium/' . $product->product_image
                            : null,
                        'small'  => $product->product_image
                            ? $basePath . '/small/' . $product->product_image
                            : null,
                    ],
                    'description'     => $product->description,
                    'condition'       => $product->condition,
                    'stock'           => $product->stock,
                    'attribute_id'    => $product->attribute_id,
                    'product_discount' => $product->product_discount,
                    'distance'        => isset($product->distance)
                        ? round($product->distance, 2)
                        : null,
                    'vendor' => [
                        'id'   => $product->vendor_id,
                        'name' => $product->vendor_name,
                        'plan' => $product->vendor_plan
                    ],
                    'book_type' => $product->bookType ? [
                        'id'   => $product->bookType->id,
                        'name' => $product->bookType->book_type,
                        'icon' => $product->bookType->book_type_icon,
                    ] : null,

                    'language' => $product->language ? [
                        'id'   => $product->language->id,
                        'name' => $product->language->name,
                    ] : null,
                    'category'    => $product->category,
                    'subcategory' => $product->subcategory,
                    'section'     => $product->section,
                    'subject'     => $product->subject
                ];
            })->items()
        ];
    }

    public function productDetails(Request $request, $attribute_id)
    {
        $user = auth('sanctum')->user();

        $attribute = ProductsAttribute::with(['vendor.user'])
            ->where('id', $attribute_id)
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->first();

        if (!$attribute) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
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
                    'image_urls'       => [
                        'large'  => $product->product_image
                            ? $basePath . '/large/' . $product->product_image
                            : null,
                        'medium' => $product->product_image
                            ? $basePath . '/medium/' . $product->product_image
                            : null,
                        'small'  => $product->product_image
                            ? $basePath . '/small/' . $product->product_image
                            : null,
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
                    'authors' => $product->authors,
                ],

                'vendor_offer' => [
                    'attribute_id' => $attribute->id,
                    'stock' => $attribute->stock,
                    'discount' => $attribute->product_discount,
                    'sku' => $attribute->sku,

                    'vendor' => [

                        'vendor_id' => $attribute->vendor->id ?? null,
                        'admin_id' => $attribute->vendor->admin_id ?? null,
                        'admin_type' => $attribute->vendor->admin_type ?? null,
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
                            'name' => $attribute->vendor->user->name ?? null,
                            'email' => $attribute->vendor->user->email ?? null,
                            'mobile' => $attribute->vendor->user->mobile ?? null,
                            'image' => $attribute->vendor->user->image ?? null,
                            'created_at' => $attribute->vendor->user->created_at ?? null,
                        ]
                    ]
                ],

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

        $cartItems = Cart::with(['product' => function ($q) {
            $q->select('id', 'category_id', 'product_name', 'product_image');
        }])
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
            $price = Product::getDiscountPriceDetailsByAttribute($item->product_attribute_id);

            $item->product_price = $price['product_price'] ?? 0;
            $item->final_price = $price['final_price'] ?? 0;
            $item->discount_amount = $price['discount'] ?? 0;

            $item->discount_percent = 0;
            if ($item->product_price > 0 && $item->discount_amount > 0) {
                $item->discount_percent = round(($item->discount_amount / $item->product_price) * 100);
            }

            if ($item->product) {
                $item->product->image_urls = [
                    'large'  => $item->product->product_image ? $basePath . '/large/' . $item->product->product_image : null,
                    'medium' => $item->product->product_image ? $basePath . '/medium/' . $item->product->product_image : null,
                    'small'  => $item->product->product_image ? $basePath . '/small/' . $item->product->product_image : null,
                ];
            }

            $total_price += ($price['final_price'] ?? 0) * $item->quantity;
            $total_items += $item->quantity;
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart fetched successfully',
            'data' => [
                'cart_items' => $cartItems,
                'total_price' => $total_price,
                'total_items' => $total_items
            ]
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

        $wishlists = Wishlist::with(['product' => function ($q) {
            $q->select('id', 'category_id', 'product_name', 'product_image');
        }])
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
            $priceDetails = Product::getDiscountPriceDetailsByAttribute($item->product_attribute_id);

            $item->product_price = $priceDetails['product_price'] ?? 0;
            $item->final_price = $priceDetails['final_price'] ?? 0;
            $item->discount_amount = $priceDetails['discount'] ?? 0;

            $item->discount_percent = 0;
            if ($item->product_price > 0 && $item->discount_amount > 0) {
                $item->discount_percent = round(($item->discount_amount / $item->product_price) * 100);
            }

            if ($item->product) {
                $item->product->image_urls = [
                    'large'  => $item->product->product_image ? $basePath . '/large/' . $item->product->product_image : null,
                    'medium' => $item->product->product_image ? $basePath . '/medium/' . $item->product->product_image : null,
                    'small'  => $item->product->product_image ? $basePath . '/small/' . $item->product->product_image : null,
                ];
            }

            $qty = $item->quantity ?? 1;
            $total_price += ($priceDetails['final_price'] ?? 0) * $qty;
            $total_items += $qty;
        }

        return response()->json([
            'status' => true,
            'message' => 'Wishlist fetched successfully',
            'data' => [
                'wishlist_items' => $wishlists,
                'total_price' => $total_price,
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

            if (!$attribute) continue;

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

        $user->load(['country', 'state', 'district']);

        if (empty($user->address) || empty($user->district_id) || empty($user->state_id) || empty($user->country_id) || empty($user->pincode) || empty($user->phone)) {
            return response()->json([
                'status' => false,
                'message' => 'Please update your address before checkout'
            ], 400);
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

        $shippingCountry = $user->country ? $user->country->name : 'India';
        $shipping_charges = ShippingCharge::getShippingCharges(0, $shippingCountry);

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

        $payment_method = $request->payment_gateway == 'COD' ? 'COD' : 'Prepaid';
        $order_status = $request->payment_gateway == 'COD' ? 'New' : 'Pending';

        DB::beginTransaction();

        try {

            $order = Order::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'address' => $user->address,
                'city' => $user->district->name ?? '',
                'state' => $user->state->name ?? '',
                'country' => $user->country->name ?? '',
                'pincode' => $user->pincode,
                'mobile' => $user->phone,
                'email' => $user->email,
                'shipping_charges' => $shipping_charges,
                'coupon_code' => $request->coupon_code,
                'coupon_amount' => $coupon_amount,
                'order_status' => $order_status,
                'payment_method' => $payment_method,
                'payment_gateway' => $request->payment_gateway,
                'grand_total' => $grand_total > 0 ? $grand_total : 0,
                'wallet_amount' => $wallet_amount,
                'extra_discount' => 0
            ]);

            if ($wallet_amount > 0) {

                $user->wallet_balance -= $wallet_amount;
                $user->save();

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => $wallet_amount,
                    'type' => 'debit',
                    'description' => 'Used for order #' . $order->id
                ]);
            }

            foreach ($cartItems as $item) {

                $attribute = ProductsAttribute::with('product')->find($item->product_attribute_id);

                $priceDetails = Product::getDiscountPriceDetailsByAttribute($item->product_attribute_id);

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
                    'commission' => $commission
                ]);
            }

            if ($request->payment_gateway == 'COD') {

                Cart::where('user_id', $user->id)->delete();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Order placed successfully',
                    'order_id' => $order->id,
                    'grand_total' => $order->grand_total
                ]);
            }

            if ($request->payment_gateway == 'Razorpay') {

                $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

                $razorpayOrder = $api->order->create([
                    'receipt' => 'order_' . $order->id,
                    'amount' => $order->grand_total * 100,
                    'currency' => 'INR'
                ]);

                $order->razorpay_order_id = $razorpayOrder['id'];
                $order->save();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'order_id' => $order->id,
                    'razorpay_order_id' => $razorpayOrder['id'],
                    'amount' => $order->grand_total
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Checkout failed'
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

            $order = Order::with('orders_products')->find($request->order_id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ]);
            }

            if (Payment::where('payment_id', $request->razorpay_payment_id)->exists()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payment already verified'
                ]);
            }

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

            Cart::where('user_id', $order->user_id)->delete();

            WalletTransaction::checkAndCreditWallet($order->id);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment verified successfully'
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

        $orders = Order::with(['orders_products' => function ($query) {
            // Load the associated product attribute and product to get images/details if needed
            // The orders_products table already has product_name, product_price, product_qty, etc.
        }])
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

        $order = Order::with(['logs' => function ($query) {
            $query->orderBy('id', 'asc');
        }])->where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order status fetched successfully',
            'data' => [
                'order_id' => $order->id,
                'current_status' => $order->order_status,
                'tracking_number' => $order->tracking_number,
                'courier_name' => $order->courier_name,
                'logs' => $order->logs->map(function ($log) {
                    return [
                        'status' => $log->order_status,
                        'date' => $log->created_at->format('d M Y, h:i A'),
                    ];
                })
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
                OrdersProduct::where('order_id', $id)->update(['item_status' => 'Cancelled']);
                WalletTransaction::revertWallet($id);
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
}
