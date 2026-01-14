<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
// Models
use App\Models\Product;
use App\Models\Publisher;
use App\Models\Subject;
use App\Models\Edition;
use App\Models\Author;
use App\Models\Language;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\ProductsAttribute;

class BookController extends Controller
{
    private function checkAccess($request)
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json([
                'status' => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        if (!in_array($admin->type, ['superadmin', 'vendor'])) {
            return response()->json([
                'status' => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        if ($admin->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        return null;
    }

    public function getProduct(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();

        $query = Product::orderBy('id', 'desc')
            ->with([
                'section:id,name',
                'category:id,category_name',
                'edition:id,edition',
                'firstAttribute:id,product_id,vendor_id,admin_id,admin_type'
            ]);


        if ($admin->type === 'vendor') {
            $query->whereHas('firstAttribute', function ($q) use ($admin) {
                $q->where('vendor_id', $admin->vendor_id);
            });
        }

        $products = $query->get();
        $basePath = url('front/images/product_images');

        $products->each(function ($product) use ($basePath) {
            $product->image_urls = [
                'large'  => $product->product_image
                    ? $basePath . '/large/' . $product->product_image
                    : null,
                'medium' => $product->product_image
                    ? $basePath . '/medium/' . $product->product_image
                    : null,
                'small'  => $product->product_image
                    ? $basePath . '/small/' . $product->product_image
                    : null,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Products fetched successfully',
            'data'    => $products
        ], 200);
    }

    public function updateProductStatus(Request $request, $productId)
    {

        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();

        $vendorId = ($admin->type === 'vendor')
            ? $admin->vendor_id
            : $request->vendor_id;

        if ($admin->type !== 'vendor' && !$vendorId) {
            return response()->json([
                'status'  => false,
                'message' => 'Vendor ID is required'
            ], 422);
        }

        $attributes = ProductsAttribute::where([
            'product_id' => $productId,
            'vendor_id'  => $vendorId
        ])->get();

        if ($attributes->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found'
            ], 404);
        }

        $newStatus = $attributes->first()->status ? 0 : 1;

        ProductsAttribute::where([
            'product_id' => $productId,
            'vendor_id'  => $vendorId
        ])->update([
            'status' => $newStatus
        ]);

        return response()->json([
            'status'           => true,
            'message'          => 'Product status updated',
            'product_id'       => $productId,
            'vendor_id'        => $vendorId,
            'attribute_status' => $newStatus
        ], 200);
    }

    public function deleteProduct(Request $request, $productId)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();

        $vendorId = ($admin->type === 'vendor')
            ? $admin->vendor_id
            : $request->vendor_id;

        if ($admin->type !== 'vendor' && !$vendorId) {
            return response()->json([
                'status'  => false,
                'message' => 'Vendor ID is required'
            ], 422);
        }

        $attributes = ProductsAttribute::where([
            'product_id' => $productId,
            'vendor_id'  => $vendorId
        ])->get();

        if ($attributes->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found'
            ], 404);
        }

        ProductsAttribute::where([
            'product_id' => $productId,
            'vendor_id'  => $vendorId
        ])->delete();

        return response()->json([
            'status'     => true,
            'message'    => 'Product deleted successfully',
            'product_id' => $productId,
            'vendor_id'  => $vendorId
        ], 200);
    }

    public function lookupByIsbn(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $request->validate([
            'query' => 'required|string|max:255'
        ]);

        $query = trim($request->input('query'));

        if (preg_match('/^[0-9\-]{10,17}$/', $query)) {

            $product = Product::with([
                'section',
                'category',
                'publisher',
                'subject',
                'edition',
                'language',
                'authors'
            ])->where('product_isbn', $query)->first();

            $basePath = url('front/images/product_images');

            $product->image_urls = [
                'large' => $product->product_image
                    ? $basePath . '/large/' . $product->product_image
                    : null,

                'medium' => $product->product_image
                    ? $basePath . '/medium/' . $product->product_image
                    : null,

                'small' => $product->product_image
                    ? $basePath . '/small/' . $product->product_image
                    : null,
            ];

            if ($product) {
                return response()->json([
                    'status' => true,
                    'source' => 'local',
                    'manual_entry' => false,
                    'data' => $product
                ]);
            }

            return $this->fetchFromIsbnDb($query);
        }


        $books = Product::where('product_name', 'LIKE', "%{$query}%")
            ->select('id', 'product_name', 'product_isbn')
            ->limit(10)
            ->get();

        if ($books->isEmpty()) {
            return response()->json([
                'status' => false,
                'manual_entry' => true,
                'message' => 'No matching books found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'source' => 'local',
            'manual_entry' => false,
            'suggestions' => $books
        ]);
    }

    public function autocomplete(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $request->validate([
            'title' => 'required|string|min:2'
        ]);

        $books = Product::where('product_name', 'LIKE', "%{$request->title}%")
            ->select('id', 'product_name', 'product_isbn')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $books
        ]);
    }

    public function show($id)
    {
        $product = Product::with([
            'section',
            'category',
            'publisher',
            'subject',
            'edition',
            'language',
            'authors'
        ])->find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Book not found'
            ], 404);
        }

        $basePath = url('front/images/product_images');

        $product->image_urls = [
            'large' => $product->product_image
                ? $basePath . '/large/' . $product->product_image
                : null,

            'medium' => $product->product_image
                ? $basePath . '/medium/' . $product->product_image
                : null,

            'small' => $product->product_image
                ? $basePath . '/small/' . $product->product_image
                : null,
        ];

        return response()->json([
            'status' => true,
            'data' => $product
        ]);
    }

    private function fetchFromIsbnDb($isbn)
    {
        $key = config('services.isbn.key');

        try {
            $response = Http::withHeaders([
                'Authorization' => $key
            ])->get("https://api2.isbndb.com/book/$isbn");
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'manual_entry' => true,
                'message' => 'ISBN service unavailable'
            ], 503);
        }

        if ($response->failed() || !isset($response['book'])) {
            return response()->json([
                'status' => false,
                'manual_entry' => true,
                'message' => 'Book not found'
            ], 404);
        }

        $book = $response['book'];

        $publisher_id = !empty($book['publisher'])
            ? Publisher::firstOrCreate(['name' => $book['publisher']], ['status' => 1])->id
            : null;

        $subject_id = !empty($book['subjects'][0])
            ? Subject::firstOrCreate(['name' => $book['subjects'][0]], ['status' => 1])->id
            : null;

        $edition_id = !empty($book['edition'])
            ? Edition::firstOrCreate(['edition' => $book['edition']], ['status' => 1])->id
            : null;

        $language_id = !empty($book['language'])
            ? Language::firstOrCreate(['name' => $book['language']], ['status' => 1])->id
            : null;

        $author_ids = [];
        if (!empty($book['authors'])) {
            foreach ($book['authors'] as $name) {
                $author = Author::firstOrCreate(['name' => $name], ['status' => 1]);
                $author_ids[] = $author->id;
            }
        }

        $product = Product::create([
            'product_name'  => $book['title'] ?? '',
            'product_isbn'  => $isbn,
            'description'   => $book['synopsis'] ?? '',
            'product_price' => $book['msrp'] ?? 0,
            'product_image' => $book['image'] ?? null,
            'publisher_id'  => $publisher_id,
            'subject_id'    => $subject_id,
            'edition_id'    => $edition_id,
            'language_id'   => $language_id,
            'status'        => 1
        ]);

        if (!empty($author_ids)) {
            $product->authors()->sync($author_ids);
        }

        $product->load([
            'section',
            'category',
            'publisher',
            'subject',
            'edition',
            'language',
            'authors'
        ]);

        return response()->json([
            'status' => true,
            'source' => 'isbndb',
            'manual_entry' => false,
            'data' => $product
        ], 201);
    }

    public function storeManualProduct(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $request->validate([
            'condition'     => 'required|in:new,old',
            'product_isbn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('products')->where(function ($query) use ($request) {
                    return $query->where('condition', $request->condition);
                }),
            ],
            'product_name'  => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'product_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category_id'   => 'required|exists:categories,id',
            'publisher_id'  => 'nullable|exists:publishers,id',
            'subject_id'    => 'nullable|exists:subjects,id',
            'edition_id'    => 'nullable|exists:editions,id',
            'language_id'   => 'nullable|exists:languages,id',
            'author_ids'    => 'nullable|array',
            'author_ids.*'  => 'exists:authors,id',
            'meta_title'    => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string'
        ]);

        $categoryDetails = Category::find($request->category_id);

        if (!$categoryDetails || !$categoryDetails->section_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid category or section not found'
            ], 422);
        }

        $exists = Product::where('product_isbn', $request->product_isbn)
            ->where('condition', $request->condition)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Product with same ISBN and condition already exists'
            ], 422);
        }

        $product = Product::create([
            'condition'     => $request->condition,
            'product_isbn'  => $request->product_isbn,
            'product_name'  => $request->product_name,
            'description'   => $request->description,
            'product_price' => $request->product_price,
            'section_id'    => $categoryDetails->section_id,
            'category_id'   => $request->category_id,
            'publisher_id'  => $request->publisher_id,
            'subject_id'    => $request->subject_id,
            'edition_id'    => $request->edition_id,
            'language_id'   => $request->language_id,
            'meta_title'    => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description'   => $request->meta_description,
            'status'        => 1
        ]);

        if ($request->hasFile('product_image')) {
            $image_tmp = $request->file('product_image');

            if ($image_tmp->isValid()) {
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111, 99999) . '.' . $extension;

                Image::make($image_tmp)->resize(1000, 1000)
                    ->save(public_path('front/images/product_images/large/' . $imageName));

                Image::make($image_tmp)->resize(500, 500)
                    ->save(public_path('front/images/product_images/medium/' . $imageName));

                Image::make($image_tmp)->resize(250, 250)
                    ->save(public_path('front/images/product_images/small/' . $imageName));

                $product->update([
                    'product_image' => $imageName
                ]);
            }
        }

        if ($request->filled('author_ids')) {
            $product->authors()->sync($request->author_ids);
        }

        $product->load([
            'section',
            'category',
            'publisher',
            'subject',
            'edition',
            'language',
            'authors'
        ]);

        $basePath = url('front/images/product_images');

        $product->image_urls = [
            'large'  => $product->product_image ? $basePath . '/large/' . $product->product_image : null,
            'medium' => $product->product_image ? $basePath . '/medium/' . $product->product_image : null,
            'small'  => $product->product_image ? $basePath . '/small/' . $product->product_image : null,
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Product added successfully',
            'data'    => $product
        ], 201);
    }

    public function productSummary(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate([
            'isbn' => 'required|string|max:20'
        ]);

        $isbn = $request->isbn;

        $product = Product::select(
            'id',
            'product_name',
            'product_price',
            'product_isbn',
            'product_image'
        )->where('product_isbn', $isbn)->first();

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found with this ISBN'
            ], 404);
        }

        if ($user->type === 'vendor') {

            $attribute = ProductsAttribute::where('product_id', $product->id)
                ->where('vendor_id', $user->vendor_id)
                ->where('admin_type', 'vendor')
                ->select('stock', 'product_discount')
                ->first();
        } else {

            $attribute = ProductsAttribute::where('product_id', $product->id)
                ->where('admin_id', $user->id)
                ->where('admin_type', 'admin')
                ->select('stock', 'product_discount')
                ->first();
        }

        $totalStock = $attribute?->stock ?? 0;
        $discount   = $attribute?->product_discount ?? 0;

        $basePath = url('front/images/product_images');

        return response()->json([
            'status' => true,
            'data'   => [
                'id'               => $product->id,
                'product_name'     => $product->product_name,
                'product_price'    => $product->product_price,
                'product_isbn'     => $product->product_isbn,

                // original image name
                'product_image'    => $product->product_image,

                // FULL IMAGE PATHS
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

                'available_stock'  => (int) $totalStock,

                'product_discount' => (float) $discount
            ]
        ], 200);
    }

    public function productSummaryByid(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $product = Product::select(
            'id',
            'product_name',
            'product_price',
            'product_isbn',
            'product_image'
        )->findOrFail($id);

        if ($user->type === 'vendor') {

            $query = ProductsAttribute::where('product_id', $product->id)
                ->where('vendor_id', $user->vendor_id)
                ->where('admin_type', 'vendor');
        } else {

            $query = ProductsAttribute::where('product_id', $product->id)
                ->where('admin_id', $user->id)
                ->where('admin_type', 'admin');
        }

        $totalStock = (int) $query->sum('stock');

        $discount = (float) $query->max('product_discount');

        $basePath = url('front/images/product_images');

        return response()->json([
            'status' => true,
            'data'   => [
                'id'               => $product->id,
                'product_name'     => $product->product_name,
                'product_price'    => $product->product_price,
                'product_isbn'     => $product->product_isbn,

                'product_image'    => $product->product_image,

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

                'available_stock'  => $totalStock,

                'product_discount' => $discount
            ]
        ], 200);
    }

    public function storeProductAttribute(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $request->validate([
            'product_id'       => 'required|exists:products,id',
            'total_stock'      => 'required|integer|min:0',
            'product_discount' => 'nullable|numeric|min:0'
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $product = Product::findOrFail($request->product_id);

        $attributeQuery = ProductsAttribute::where('product_id', $product->id);

        if ($user->type === 'vendor') {
            $attributeQuery->where('vendor_id', $user->vendor_id)
                ->where('admin_type', 'vendor');
        } else {
            $attributeQuery->where('admin_id', $user->id)
                ->where('admin_type', 'admin');
        }

        $existingAttribute = $attributeQuery->first();

        if (!$existingAttribute && $user->type === 'vendor') {

            $vendor = Vendor::find($user->vendor_id);

            if ($vendor && $vendor->plan === 'free') {

                $FREE_VENDOR_ATTRIBUTE_LIMIT = 10; //  CHANGE LIMIT HERE

                $currentMonthStart = now()->startOfMonth();

                $attributeCount = ProductsAttribute::where('vendor_id', $vendor->id)
                    ->where('admin_type', 'vendor')
                    ->where('created_at', '>=', $currentMonthStart)
                    ->count();

                if ($attributeCount >= $FREE_VENDOR_ATTRIBUTE_LIMIT) {
                    return response()->json([
                        'status'  => false,
                        'message' => "Free plan allows only {$FREE_VENDOR_ATTRIBUTE_LIMIT} products per month. Please upgrade to Pro plan."
                    ], 403);
                }
            }
        }

        if ($existingAttribute) {

            $existingAttribute->stock += (int) $request->total_stock;
            $existingAttribute->product_discount = $request->product_discount ?? 0;
            $existingAttribute->save();
        } else {

            $attribute = new ProductsAttribute();
            $attribute->product_id       = $product->id;
            $attribute->size             = 'Default';
            $attribute->stock            = $request->total_stock;
            $attribute->product_discount = $request->product_discount ?? 0;
            $attribute->status           = 1;

            if ($user->type === 'vendor') {
                $attribute->sku = 'BH-P' . $product->id . '-V' . $user->vendor_id;
                $attribute->admin_type = 'vendor';
                $attribute->vendor_id  = $user->vendor_id;
                $attribute->admin_id   = null;
            } else {
                $attribute->sku = 'BH-P' . $product->id . '-A' . $user->id;
                $attribute->admin_type = 'admin';
                $attribute->admin_id   = $user->id;
                $attribute->vendor_id  = null;
            }

            $attribute->save();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Stock & discount saved successfully'
        ], 201);
    }

    public function isBestSeller(Request $request, $productId)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();

        $vendorId = ($admin->type === 'vendor')
            ? $admin->vendor_id
            : $request->vendor_id;

        if ($admin->type !== 'vendor' && !$vendorId) {
            return response()->json([
                'status'  => false,
                'message' => 'Vendor ID is required'
            ], 422);
        }

        $attribute = ProductsAttribute::where([
            'product_id' => $productId,
            'vendor_id'  => $vendorId
        ])->first();

        if (!$attribute) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found'
            ], 404);
        }

        $newStatus = ($attribute->is_bestseller === 'Yes') ? 'No' : 'Yes';

        $attribute->update([
            'is_bestseller' => $newStatus
        ]);

        return response()->json([
            'status'        => true,
            'message'       => 'Best seller status updated',
            'product_id'    => $productId,
            'vendor_id'     => $vendorId,
            'is_bestseller' => $newStatus
        ], 200);
    }

    public function isFeatured(Request $request, $productId)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();

        $vendorId = ($admin->type === 'vendor')
            ? $admin->vendor_id
            : $request->vendor_id;

        if ($admin->type !== 'vendor' && !$vendorId) {
            return response()->json([
                'status'  => false,
                'message' => 'Vendor ID is required'
            ], 422);
        }

        $attribute = ProductsAttribute::where([
            'product_id' => $productId,
            'vendor_id'  => $vendorId
        ])->first();

        if (!$attribute) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found'
            ], 404);
        }

        $newStatus = ($attribute->is_featured === 'Yes') ? 'No' : 'Yes';

        $attribute->update([
            'is_featured' => $newStatus
        ]);

        return response()->json([
            'status'       => true,
            'message'      => 'Featured status updated',
            'product_id'   => $productId,
            'vendor_id'    => $vendorId,
            'is_featured'  => $newStatus
        ], 200);
    }
}
