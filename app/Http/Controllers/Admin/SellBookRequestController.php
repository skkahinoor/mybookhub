<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductsAttribute;
use App\Models\Product;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SellBookRequestController extends Controller
{
    public function index()
    {
        Session::put('page', 'sellBookRequests');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        // Fetch attributes added by users OR vendors listing old books (must have condition)
        $requests = ProductsAttribute::with(['product', 'user', 'condition', 'vendor'])
            ->whereNotNull('old_book_condition_id')
            ->where(function ($q) {
                $q->whereNotNull('user_id')             // student/user sell requests
                  ->orWhere('admin_type', 'vendor');    // vendor old book listings
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.sell_book_requests.index', compact('requests', 'logos', 'headerLogo'));
    }

    public function show($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        
        $requestData = ProductsAttribute::with(['product', 'user', 'condition', 'vendor'])
            ->findOrFail($id);

        return view('admin.sell_book_requests.show', compact('requestData', 'logos', 'headerLogo'));
    }

    public function approve(Request $request, $id)
    {
        $attribute = ProductsAttribute::findOrFail($id);
        
        // Approve the attribute
        $attribute->admin_approved = 1;
        $attribute->status = 1;
        $attribute->save();

        // Also approve the product if it's currently unapproved
        $product = Product::find($attribute->product_id);
        if ($product && $product->status == 0) {
            $product->status = 1;
            $product->save();
        }

        return redirect()->route('admin.sell-book-requests.index')->with('success_message', 'Old book listing approved successfully!');
    }

    public function reject($id)
    {
        $attribute = ProductsAttribute::findOrFail($id);
        $attribute->delete(); // Or update status to rejected

        return redirect()->route('admin.sell-book-requests.index')->with('success_message', 'Sell request rejected and removed.');
    }
}
