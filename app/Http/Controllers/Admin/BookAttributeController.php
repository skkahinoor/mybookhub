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

    // Store attribute (stock and discount) for existing product
    public function store(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0',
            'product_discount' => 'required|numeric|min:0|max:100',
        ]);

        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        $product = Product::findOrFail($request->product_id);

        // Check if attribute already exists for this product
        // First, try to find by product_id and user-specific filters
        $attributeQuery = ProductsAttribute::where('product_id', $request->product_id);

        if ($user->type === 'vendor') {
            $attributeQuery->where('vendor_id', $user->vendor_id)
                ->where('admin_type', 'vendor');
        } else {
            // For admin users, check for both 'admin' and 'superadmin' types, or just by admin_id
            $attributeQuery->where(function($q) use ($user) {
                $q->where('admin_id', $user->id)
                  ->whereIn('admin_type', ['admin', 'superadmin']);
            });
        }

        $existingAttribute = $attributeQuery->first();

        // If not found with user-specific filters, try to find any attribute for this product
        if (!$existingAttribute) {
            $existingAttribute = ProductsAttribute::where('product_id', $request->product_id)->first();
        }

        if ($existingAttribute) {
            // Update existing attribute - add to stock and update discount
            $existingAttribute->stock = $existingAttribute->stock + (int) $request->stock;
            $existingAttribute->product_discount = (float) $request->product_discount;
            
            // Update vendor/admin info if needed (in case it was missing)
            if ($user->type === 'vendor') {
                $existingAttribute->admin_type = 'vendor';
                $existingAttribute->vendor_id = $user->vendor_id;
                $existingAttribute->admin_id = null;
            } else {
                $existingAttribute->admin_type = in_array($user->type, ['superadmin', 'admin']) ? $user->type : 'admin';
                $existingAttribute->admin_id = $user->id;
                $existingAttribute->vendor_id = null;
            }
            
            $existingAttribute->save();
        } else {
            // Create new attribute
            $attribute = new ProductsAttribute();
            $attribute->product_id = $request->product_id;
            $attribute->stock = (int) $request->stock;
            $attribute->product_discount = (float) $request->product_discount;
            $attribute->sku = 'null';
            $attribute->status = 1;

            if ($user->type === 'vendor') {
                $attribute->admin_type = 'vendor';
                $attribute->vendor_id = $user->vendor_id;
                $attribute->admin_id = null;
            } else {
                // Use the actual user type (superadmin, admin, etc.)
                $attribute->admin_type = in_array($user->type, ['superadmin', 'admin']) ? $user->type : 'admin';
                $attribute->admin_id = $user->id;
                $attribute->vendor_id = null;
            }

            $attribute->save();
        }

        return response()->json([
            'success' => true, 
            'message' => 'Product attributes (stock and discount) saved successfully!'
        ]);
    }
}
