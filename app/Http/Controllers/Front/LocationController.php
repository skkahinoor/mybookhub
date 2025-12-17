<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function setLocationSession(Request $request)
    {
        session([
            'user_latitude' => $request->latitude,
            'user_longitude' => $request->longitude,
        ]);
        return response()->json(['success' => true]);
    }
}
