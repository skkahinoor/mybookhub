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
            return response()->json(['status'=>false,'errors'=>$validator->errors()],422);
        }

        $user = auth('sanctum')->user();
        $limit = $request->limit ?? 20;
        $page = $request->page ?? 1;
        $lat = $request->lat;
        $lng = $request->lng;

        $cacheKey = $this->cacheKey($request, $user);

        $data = Cache::remember($cacheKey, 600, function () use ($request,$user,$limit,$page,$lat,$lng) {
            return $this->sqlSearch($request,$user,$limit,$page,$lat,$lng);
        });

        return response()->json([
            'status'=>true,
            'message'=>'Products fetched successfully',
            'data'=>$data
        ]);
    }

    private function cacheKey($request,$user)
    {
        return 'products_'
            .($user ? 'user_'.$user->id : 'guest')
            .'_'.md5(json_encode($request->all()));
    }

    private function sqlSearch($request,$user,$limit,$page,$lat,$lng)
    {
        $query = Product::with(['category', 'subcategory', 'vendor', 'section', 'subject'])
            ->select('products.*')
            ->join('products_attributes as pa', 'pa.product_id', '=', 'products.id')
            ->join('vendors as v', 'pa.vendor_id', '=', 'v.id')
            ->leftJoin('ratings as r', 'pa.id', '=', 'r.product_attribute_id')
            ->addSelect(
                'pa.id as attribute_id',
                'pa.stock',
                'pa.product_discount',
                'v.plan',
                'v.location',
                DB::raw('COALESCE(AVG(r.rating),0) as avg_rating'),
                DB::raw('COUNT(r.id) as total_rating')
            )
            ->where('pa.status', 1)
            ->where('products.status', 1)
            ->where('pa.stock', '>', 0)
            ->groupBy(
                'products.id', 'products.product_name', 'products.product_price', 'products.product_image',
                'products.category_id', 'products.subcategory_id', 'products.description', 'products.condition',
                'products.book_type_id', 'products.section_id', 'products.publisher_id', 'products.subject_id',
                'products.edition_id', 'products.language_id', 'products.meta_title', 'products.meta_keywords',
                'products.meta_description', 'products.status', 'products.created_at', 'products.updated_at', 'products.product_isbn',
                'pa.id', 'pa.stock', 'pa.product_discount', 'v.id', 'v.plan', 'v.location'
            );

        // Search
        if($request->search){
            $query->where('products.product_name','LIKE','%'.$request->search.'%');
        }

        if($request->section_id){
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }

        if($request->category_id){
            $query->where('products.category_id',$request->category_id);
        }

        if($request->subcategory_id){
            $query->where('products.subcategory_id',$request->subcategory_id);
        }

        if($request->min_price){
            $query->where('products.product_price','>=',$request->min_price);
        }

        if($request->max_price){
            $query->where('products.product_price','<=',$request->max_price);
        }

        // Default filter for logged in user: show only their board and class books
        // when no specific search or category or subcategory or section is requested ("1st display ... not all book")
        if ($user && empty($request->search) && empty($request->section_id) && empty($request->category_id) && empty($request->subcategory_id)) {
            $classId = optional($user->institution_class)->sub_category_id ?? null;
            $boardId = optional($user->institution)->board ?? optional(optional($user->institution)->category)->id ?? null;

            if ($boardId) {
                $query->where('products.category_id', $boardId);
            }
            if ($classId) {
                $query->where('products.subcategory_id', $classId);
            }
        }

        // Distance calculation
        if($lat && $lng){
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

        // ==========================
        // SORTING LOGIC
        // ==========================

        if($user){
            $classId = optional($user->institution_class)->sub_category_id ?? null;
            $boardId = optional($user->institution)->board ?? optional(optional($user->institution)->category)->id ?? null;

            $order = "";

            // Priority 0: Always prioritize their own Class and Board books if they searched globally
            if($boardId){
                $order .= "CASE WHEN products.category_id=$boardId THEN 1 ELSE 2 END ASC, ";
            }

            if($classId){
                $order .= "CASE WHEN products.subcategory_id=$classId THEN 1 ELSE 2 END ASC, ";
            }

            // Priority 1: Distance
            if($lat && $lng){
                $order .= "distance ASC, ";
            }

            // Priority 2: Pro Vendor
            $order .= "CASE WHEN v.plan='pro' THEN 1 ELSE 2 END ASC, ";

            // Priority 3: Extra Discount
            $order .= "pa.product_discount DESC, ";

            // Priority 4: More Stock
            $order .= "pa.stock DESC, ";

            // Fallbacks: Price & Ratings
            $order .= "products.product_price ASC, avg_rating DESC";

            $query->orderByRaw($order);

        }else{
            $order = "";

            // Priority 1: Distance
            if($lat && $lng){
                $order .= "distance ASC, ";
            }

            // Priority 2: Pro Vendor
            $order .= "CASE WHEN v.plan='pro' THEN 1 ELSE 2 END ASC, ";

            // Priority 3: Extra Discount
            $order .= "pa.product_discount DESC, ";

            // Priority 4: More Stock
            $order .= "pa.stock DESC, ";

            // Fallbacks: Price & Ratings
            $order .= "products.product_price ASC, avg_rating DESC";

            $query->orderByRaw($order);
        }

        $results = $query->paginate($limit,['*'],'page',$page);

        return [
            'total'=>$results->total(),
            'current_page'=>$results->currentPage(),
            'last_page'=>$results->lastPage(),
            // Mapping through the results to return actual relationship objects alongside the selected items
            'products'=>$results->through(function($product) {
                return [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'product_isbn' => $product->product_isbn,
                    'product_price' => $product->product_price,
                    'product_image' => $product->product_image,
                    'description' => $product->description,
                    'condition' => $product->condition,
                    'stock' => $product->stock,
                    'attribute_id' => $product->attribute_id,
                    'product_discount' => $product->product_discount,
                    'avg_rating' => $product->avg_rating,
                    'total_rating' => $product->total_rating,
                    'distance' => $product->distance ?? null,
                    'vendor_plan' => $product->plan,
                    // Foreign Relationships Data
                    'category' => $product->category,
                    'subcategory' => $product->subcategory,
                    'vendor' => $product->vendor,
                    'section' => $product->section,
                    'subject' => $product->subject
                ];
            })->items()
        ];
    }
}
