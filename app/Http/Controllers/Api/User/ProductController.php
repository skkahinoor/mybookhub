<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;

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
            'subcategory_id' => 'nullable|integer',
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

        $cacheKey = 'products_' .
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
        $query = Product::with(['category', 'subcategory', 'section', 'subject'])
            ->select('products.*')
            ->join('products_attributes as pa', 'pa.product_id', '=', 'products.id')
            ->join('vendors as v', 'pa.vendor_id', '=', 'v.id')
            ->join('users as u', 'v.user_id', '=', 'u.id') // 🔥 Correct vendor name source
            ->addSelect(
                'pa.id as attribute_id',
                'pa.stock',
                'pa.product_discount',
                'v.id as vendor_id',
                'u.name as vendor_name',   // ✅ Correct
                'v.plan as vendor_plan',
                'v.location'
            )
            ->where('pa.status', 1)
            ->where('products.status', 1)
            ->where('pa.stock', '>', 0);

        /*
        ===============================
        FULLTEXT SEARCH
        ===============================
        */
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

        /*
        ===============================
        FILTERS
        ===============================
        */
        if ($request->section_id) {
            $query->where('products.section_id', $request->section_id);
        }

        if ($request->category_id) {
            $query->where('products.category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $query->where('products.subcategory_id', $request->subcategory_id);
        }

        if ($request->min_price) {
            $query->where('products.product_price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('products.product_price', '<=', $request->max_price);
        }

        /*
        ===============================
        DISTANCE CALCULATION
        Format: "20.27107,85.7938"
        ===============================
        */
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

        /*
        ===============================
        SORTING
        ===============================
        */

        if ($lat && $lng) {
            $query->orderBy('distance', 'asc'); // 🔥 nearest first
        }

        // Pro vendors first
        $query->orderByRaw("CASE WHEN v.plan='pro' THEN 1 ELSE 2 END ASC");

        // Higher discount
        $query->orderBy('pa.product_discount', 'desc');

        // Higher stock
        $query->orderBy('pa.stock', 'desc');

        // Lower price
        $query->orderBy('products.product_price', 'asc');

        /*
        ===============================
        PAGINATION
        ===============================
        */

        $results = $query->paginate($limit, ['*'], 'page', $page);

        return [
            'total'        => $results->total(),
            'current_page' => $results->currentPage(),
            'last_page'    => $results->lastPage(),
            'products'     => $results->through(function ($product) {

                $basePath = asset('uploads/products');

                return [
                    'id'              => $product->id,
                    'product_name'    => $product->product_name,
                    'product_isbn'    => $product->product_isbn,
                    'product_price'   => $product->product_price,
                    'image_urls' => $product->product_image ? [
                        'large'  => $basePath . '/large/' . $product->product_image,
                        'medium' => $basePath . '/medium/' . $product->product_image,
                        'small'  => $basePath . '/small/' . $product->product_image,
                    ] : null,
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
                    'category'    => $product->category,
                    'subcategory' => $product->subcategory,
                    'section'     => $product->section,
                    'subject'     => $product->subject
                ];
            })->items()
        ];
    }
}
