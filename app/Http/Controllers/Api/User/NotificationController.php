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
        \Log::info('Registering FCM Token for User ID: ' . $user->id . ' | Token: ' . substr($request->token, 0, 15) . '...');
        
        try {
            UserFcmToken::updateOrCreate(
                ['user_id' => $user->id, 'fcm_token' => $request->token],
                ['device_type' => $request->device_type ?? 'android', 'last_used_at' => now()]
            );

            // Subscribe the token to the 'all_users' topic for broadcast notifications
            $this->firebaseService->subscribeToTopic($request->token, 'all_users');
            \Log::info('Successfully registered and subscribed FCM token for User: ' . $user->id);
        } catch (\Exception $e) {
            \Log::error('Failed to register FCM token for User: ' . $user->id . ' | Error: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'FCM Token registered and subscribed successfully'
        ]);
    }

    /**
     * Get user notification history
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notifications = \App\Models\Notification::where('related_id', $user->id)
            ->where('related_type', \App\Models\User::class)
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data'   => $notifications
        ]);
    }

    /**
     * Mark all notifications as read for the user
     */
    public function markAllRead()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        \App\Models\Notification::where('related_id', $user->id)
            ->where('related_type', \App\Models\User::class)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete all notifications for the user
     */
    public function clearAll()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        \App\Models\Notification::where('related_id', $user->id)
            ->where('related_type', \App\Models\User::class)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications cleared'
        ]);
    }
}
