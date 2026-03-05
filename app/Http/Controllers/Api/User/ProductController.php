<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\Cart;

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
            'book_type_id' => 'nullable|integer|exists:book_types,id',
            'language_id'  => 'nullable|integer|exists:languages,id',
            'subcategory_id' => 'nullable|integer',
            'subject_id' => 'nullable|integer',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'search' => 'nullable|string|max:255',
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

            $query->whereRaw(
                "MATCH(products.product_name, products.meta_title, products.meta_keywords)
                 AGAINST(? IN BOOLEAN MODE)",
                [trim($booleanSearch)]
            );
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
            $query->where('products.subject_id', $request->subject_id);
        }

        if ($request->min_price) {
            $query->where('products.product_price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('products.product_price', '<=', $request->max_price);
        }

        if ($request->book_type_id) {
            $query->where('products.book_type_id', $request->book_type_id);
        }

        if ($request->language_id) {
            $query->where('products.language_id', $request->language_id);
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

        return response()->json([
            'status' => true,
            'message' => 'Product details fetched successfully',

            'data' => [


                'product' => [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'isbn' => $product->product_isbn,
                    'price' => $product->product_price,
                    'image' => $product->product_image,
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
}
