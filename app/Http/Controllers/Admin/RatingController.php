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

    public function ratings()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        Session::put('page', 'ratings');

        $ratings = Rating::with(['user', 'product', 'productAttribute.product', 'productAttribute.product.vendor'])->get()->toArray();

        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.ratings.ratings')->with(compact('ratings', 'logos', 'headerLogo', 'adminType'));
    }


    public function updateRatingStatus(Request $request)
    {
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
        Rating::findOrFail($id)->delete();

        return redirect()->back()->with('success_message', 'Rating deleted successfully!');
    }
}
