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
    public function index(Request $request)
    {
        Session::put('page', 'sellBookRequests');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        if ($request->ajax()) {
            $query = ProductsAttribute::with(['product', 'user', 'condition', 'vendor'])
                ->whereNotNull('old_book_condition_id')
                ->whereNotNull('user_id'); // student/user sell requests ONLY

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('type', function ($row) {
                    if ($row->admin_type === 'vendor') {
                        return '<span class="badge badge-warning">Vendor</span>';
                    } else {
                        return '<span class="badge badge-info">User</span>';
                    }
                })
                ->addColumn('seller_name', function ($row) {
                    if ($row->admin_type === 'vendor') {
                        if ($row->vendor && $row->vendor->user) {
                            return ($row->vendor->user->name ?? 'Vendor #' . $row->vendor_id) . '<br><small class="text-muted">' . ($row->vendor->user->email ?? '') . '</small>';
                        } else {
                            return 'Vendor ID: ' . $row->vendor_id;
                        }
                    } elseif ($row->user) {
                        return $row->user->name . '<br><small class="text-muted">' . $row->user->email . '</small>';
                    } else {
                        return 'N/A';
                    }
                })
                ->addColumn('book_name', function ($row) {
                    return $row->product->product_name ?? 'N/A';
                })
                ->addColumn('isbn', function ($row) {
                    return $row->product->product_isbn ?? 'N/A';
                })
                ->addColumn('book_condition', function ($row) {
                    if ($row->condition) {
                        return '<span class="badge badge-info">' . $row->condition->name . '</span>';
                    } else {
                        return 'N/A';
                    }
                })
                ->addColumn('selling_price', function ($row) {
                    $finalPrice = $row->price;
                    if (!$finalPrice && $row->product && $row->product->product_price > 0) {
                        if ($row->condition) {
                            $finalPrice = ($row->product->product_price * $row->condition->percentage) / 100;
                        } else {
                            $finalPrice = $row->product->product_price;
                        }
                    }
                    return '&#8377;' . ($finalPrice ?? 'N/A');
                })
                ->addColumn('location', function ($row) {
                    if ($row->user_location_name) {
                        $html = '<div style="font-size: 13px; line-height: 1.2; margin-bottom: 4px;">' . \Illuminate\Support\Str::limit($row->user_location_name, 40) . '</div>';
                        if ($row->user_location) {
                            $html .= '<a href="https://www.google.com/maps?q=' . $row->user_location . '" target="_blank" class="text-primary font-weight-bold" style="font-size: 12px; text-decoration: none;"><i class="mdi mdi-map-marker text-danger"></i> View Map</a>';
                        }
                        return $html;
                    } else {
                        return '<span class="text-muted italic">N/A</span>';
                    }
                })
                ->addColumn('status', function ($row) {
                    if ($row->admin_approved == 1) {
                        return '<span class="badge badge-success">Approved</span>';
                    } else {
                        return '<span class="badge badge-warning">Pending</span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $html = '<a href="' . route('admin.sell-book-requests.show', $row->id) . '" class="btn btn-sm btn-outline-primary">View</a>';
                    $html .= ' <form action="' . route('admin.sell-book-requests.reject', $row->id) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Reject and delete this request?\')">Reject</button>
                               </form>';
                    return $html;
                })
                ->rawColumns(['type', 'seller_name', 'book_condition', 'selling_price', 'location', 'status', 'actions'])
                ->make(true);
        }

        return view('admin.sell_book_requests.index', compact('logos', 'headerLogo'));
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
