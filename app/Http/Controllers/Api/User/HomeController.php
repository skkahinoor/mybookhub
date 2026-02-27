<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Section;
use App\Models\Cart;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        // Check if user logged in (optional)
        $user = auth('sanctum')->user();

        if ($user) {
            if ($user->type !== 'student') {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
        }

        $banners = Banner::where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $sections = Section::where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $cartCount = 0;

        if ($user) {
            $cartCount = Cart::where('user_id', $user->id)->count();
        }

        return response()->json([
            'status' => true,
            'message' => 'Home data fetched successfully',

            'user' => $user ? $user : null,

            'cart_count' => $cartCount,

            'data' => [
                'banners' => $banners,
                'sections' => $sections,
            ]
        ]);
    }
}
