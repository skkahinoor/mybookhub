<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookAttribute;
use App\Models\Product;
use App\Models\Edition;
use App\Models\HeaderLogo;
use App\Models\ProductsAttribute; // Add this at the top

class BookAttributeController extends Controller
{
    // Fetch editions for a product (or all editions)
    public function getEditions($productId)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
            // You can filter editions by product if needed, or return all
        $editions = Edition::all();
        return response()->json($editions, 'logos');
        return view('admin.book_attributes.get_editions', compact('editions', 'logos', 'headerLogo'));
    }

    // Store attribute and duplicate product
    public function store(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'edition_id' => 'required|exists:editions,id',
            'product_price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        // Store in book_attributes (if you want to keep this for history)
        $attribute = BookAttribute::create([
            'product_id' => $request->product_id,
            'edition_id' => $request->edition_id,
            'product_price' => $request->product_price,
            'stock' => $request->stock,
        ]);

        // Duplicate product with new edition, price (but NOT stock)
        $product = Product::findOrFail($request->product_id);
        $newProduct = $product->replicate();
        // $newProduct->product_isbn = $product->product_isbn . '-' . $request->edition_id;
        $newProduct->product_isbn = $product->product_isbn;
        $newProduct->product_price = $request->product_price;
        $newProduct->edition_id = $request->edition_id;
        $newProduct->save();

        // Create new attribute for the new product (store stock here)
        ProductsAttribute::create([
            'product_id' => $newProduct->id,
            'price' => 0,
            'stock' => $request->stock,
            'size' => 'Default',
            'sku' => $product->product_isbn . '-' . $request->edition_id,
            'status' => 1,
            // add other fields as needed
        ]);

        // Copy authors from original product to new product in author_product table
        $authorIds = $product->authors()->pluck('author_id')->toArray();
        if (!empty($authorIds)) {
            $newProduct->authors()->sync($authorIds);
        }

        return response()->json(['success' => true, 'message' => 'Attribute and product created successfully.']);
        return view('admin.book_attributes.store', compact('newProduct', 'logos', 'headerLogo'));
    }
}
