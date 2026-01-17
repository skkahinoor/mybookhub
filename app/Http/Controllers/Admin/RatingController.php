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
       
    public function ratings() {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        
        Session::put('page', 'ratings');

        $ratings = Rating::with(['user', 'product'])->get()->toArray();

        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.ratings.ratings')->with(compact('ratings', 'logos', 'headerLogo', 'adminType'));
    }

       
    public function updateRatingStatus(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => true], 400);
        }
    
        $status = $request->status === 'Active' ? 0 : 1;
    
        Rating::where('id', $request->rating_id)
            ->update(['status' => $status]);
    
        return response()->json([
            'status'    => $status,
            'rating_id'=> $request->rating_id
        ]);
    }
      
    public function deleteRating($id) { 
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Rating::where('id', $id)->delete();

        $message = 'Rating has been deleted successfully!';
        

        return redirect()->back()->with('success_message', $message);
        return view('admin.ratings.ratings', compact('ratings', 'logos', 'headerLogo'));
    }

}