<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
// Models
use App\Models\Product;
use App\Models\Publisher;
use App\Models\Subject;
use App\Models\Edition;
use App\Models\Author;
use App\Models\Language;
use App\Models\Admin;
use App\Models\Category;

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

        return response()->json([
            'status'  => true,
            'message' => 'Products fetched successfully',
            'data'    => $query->get()
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
            'isbn' => 'required|string|max:20'
        ]);

        $isbn = $request->isbn;

        $product = Product::with([
            'section',
            'category',
            'publisher',
            'subject',
            'edition',
            'language',
            'authors'
        ])->where('product_isbn', $isbn)->first();

        if ($product) {
            return response()->json([
                'status'       => true,
                'source'       => 'local',
                'manual_entry' => false,
                'message'      => 'Book found in local database',
                'data'         => $product
            ], 200);
        }

        $key = config('services.isbn.key');

        try {
            $response = Http::withHeaders([
                'Authorization' => $key
            ])->get("https://api2.isbndb.com/book/$isbn");
        } catch (\Exception $e) {
            return response()->json([
                'status'       => false,
                'manual_entry' => true,
                'message'      => 'ISBN service unavailable. Please enter details manually.'
            ], 503);
        }

        if ($response->failed() || !isset($response['book'])) {
            return response()->json([
                'status'       => false,
                'source'       => 'none',
                'manual_entry' => true,
                'message'      => 'Book not found. Please enter book details manually.',
                'isbn'         => $isbn
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
            'section_id'    => null,
            'category_id'   => null,
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
            'status'       => true,
            'source'       => 'isbndb',
            'manual_entry' => false,
            'message'      => 'Book fetched from ISBNdb and saved locally',
            'data'         => $product
        ], 201);
    }

    public function storeManualProduct(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $request->validate([
            'condition'     => 'required|in:new,old',
            'location'      => 'nullable|string|max:255',
            'product_isbn'  => 'required|string|max:20|unique:products,product_isbn',
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
            'author_ids.*'  => 'exists:authors,id'
        ]);

        $categoryDetails = Category::find($request->category_id);

        if (!$categoryDetails || !$categoryDetails->section_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid category or section not found'
            ], 422);
        }

        $product = Product::create([
            'condition'     => $request->condition,
            'location'      => $request->location,
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

        return response()->json([
            'status'  => true,
            'message' => 'Product added successfully',
            'data'    => $product
        ], 201);
    }

}
