<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SellBookRequestController extends Controller
{
    public function index()
    {
        Session::put('page', 'sellBookRequests');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        // Fetch attributes added by users listing old books (must have condition)
        $requests = ProductsAttribute::with(['product', 'user', 'condition', 'vendor'])
            ->whereNotNull('old_book_condition_id')
            ->whereNotNull('user_id')             // student/user sell requests ONLY
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
        $attribute = ProductsAttribute::with('product')->findOrFail($id);

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

        // Notify student/user who submitted the sell request
        if (!empty($attribute->user_id)) {
            $productName = $attribute->product->product_name ?? 'your book';
            Notification::create([
                'type' => 'sell_book_approved',
                'title' => 'Sell request approved',
                'message' => "Your listing for '{$productName}' has been approved and is now live.",
                'related_id' => (int) $attribute->user_id,
                'related_type' => User::class,
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success_message', 'Old book listing approved successfully!');
    }

    public function reject($id)
    {
        $attribute = ProductsAttribute::with('product')->findOrFail($id);
        $userId = $attribute->user_id;
        $productName = $attribute->product->product_name ?? 'your book';

        $attribute->delete();

        // Notify student/user who submitted the sell request
        if (!empty($userId)) {
            Notification::create([
                'type' => 'sell_book_rejected',
                'title' => 'Sell request not approved',
                'message' => "Your listing for '{$productName}' could not be approved. Please check our guidelines or contact support.",
                'related_id' => (int) $userId,
                'related_type' => User::class,
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success_message', 'Sell request rejected and removed.');
    }
}
