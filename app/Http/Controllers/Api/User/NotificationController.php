<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(\App\Services\FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Register FCM token from the Mobile App (React Native)
     */
    public function registerToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string', // 'android', 'ios', etc.
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Store the token for the mobile device
        UserFcmToken::updateOrCreate(
            ['user_id' => $user->id, 'fcm_token' => $request->token],
            ['device_type' => $request->device_type ?? 'android', 'last_used_at' => now()]
        );

        // Subscribe the token to the 'all_users' topic for broadcast notifications
        $this->firebaseService->subscribeToTopic($request->token, 'all_users');

        return response()->json([
            'status' => 'success',
            'message' => 'FCM Token registered and subscribed successfully'
        ]);
    }
}
