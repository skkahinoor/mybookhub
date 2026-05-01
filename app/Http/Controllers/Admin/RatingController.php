<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\Rating;
use App\Models\HeaderLogo;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{

    public function ratings(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('view_ratings')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        Session::put('page', 'ratings');

        if ($request->ajax()) {
            $query = Rating::with(['user', 'product', 'productAttribute.product', 'productAttribute.product.vendor']);

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    if (!empty($row->productAttribute)) {
                        $url = url('product/' . $row->product_attribute_id);
                        $name = $row->productAttribute->product->product_name ?? 'View Product';
                        return '<a target="_blank" href="' . $url . '">' . $name . '</a>';
                    }
                    return '<span class="text-danger">Product attribute not found</span>';
                })
                ->addColumn('user_email', function ($row) {
                    return $row->user->email ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $url = ($adminType === 'vendor') ? route('vendor.updateratingstatus') : route('admin.updateratingstatus');
                    $status = $row->status == 1 ? 'Active' : 'Inactive';
                    $icon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    
                    return '<a class="updateRatingStatus" id="rating-' . $row->id . '" rating_id="' . $row->id . '" data-url="' . $url . '" href="javascript:void(0)">
                                <i style="font-size: 25px" class="mdi ' . $icon . '" status="' . $status . '"></i>
                            </a>';
                })
                ->addColumn('actions', function ($row) {
                    $url = route('admin.deleteRating', $row->id);
                    return '<a href="javascript:void(0)" class="confirmDelete" data-module="rating" data-url="' . $url . '">
                                <i style="font-size:25px" class="mdi mdi-file-excel-box"></i>
                            </a>';
                })
                ->rawColumns(['product_name', 'status', 'actions'])
                ->make(true);
        }

        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.ratings.ratings')->with(compact('logos', 'headerLogo', 'adminType'));
    }


    public function updateRatingStatus(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('update_ratings_status')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }
        if (!$request->ajax()) {
            return response()->json(['error' => true], 400);
        }

        $rating = Rating::findOrFail($request->rating_id);

        // toggle status
        $rating->status = $rating->status == 1 ? 0 : 1;
        $rating->save();

        return response()->json([
            'status'     => $rating->status,
            'rating_id' => $rating->id
        ]);
    }


    public function deleteRating($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_ratings')) {
            abort(403, 'Unauthorized action.');
        }
        Rating::findOrFail($id)->delete();

        return redirect()->back()->with('success_message', 'Rating deleted successfully!');
    }
}
