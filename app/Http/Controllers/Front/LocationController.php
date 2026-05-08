<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function setLocationSession(Request $request)
    {
        $oldLat = session('user_latitude');
        $oldLng = session('user_longitude');

        $newLat = $request->input('latitude');
        $newLng = $request->input('longitude');

        $updated = false;

        // Check if there is a meaningful difference to avoid excessive reloads
        if (empty($oldLat) || empty($oldLng) || 
            abs((float)$oldLat - (float)$newLat) > 0.0001 || 
            abs((float)$oldLng - (float)$newLng) > 0.0001) {
            
            session([
                'user_latitude' => $newLat,
                'user_longitude' => $newLng,
            ]);
            $updated = true;
        }

        return response()->json([
            'success' => true,
            'updated' => $updated
        ]);
    }
}
