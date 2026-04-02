<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Notification::query()
            ->where(function ($q) use ($userId) {
                $q->where(function ($q2) use ($userId) {
                    $q2->where('related_type', User::class)
                        ->where('related_id', $userId);
                })->orWhere(function ($q2) {
                    $q2->whereNull('related_type')
                        ->whereNull('related_id');
                });
            })
            ->latest();

        // For AJAX/JSON clients, return compact JSON payload
        if ($request->ajax() || $request->wantsJson()) {
            $notifications = $query->limit(10)->get()->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'is_read' => (bool) $n->is_read,
                    'created_at_human' => optional($n->created_at)->diffForHumans(),
                ];
            })->values();

            $unreadCount = (clone $query)->where('is_read', false)->count();

            return response()->json([
                'notifications' => $notifications,
                'unreadCount' => $unreadCount,
            ]);
        }

        // Full-page Student Notifications view
        $notifications = $query->paginate(15);
        $unreadCount = (clone $query)->where('is_read', false)->count();
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        return view('user.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'headerLogo' => $headerLogo,
            'logos' => $logos,
        ]);
    }

    public function markAsRead($id)
    {
        $userId = Auth::id();

        $notification = Notification::where('id', $id)
            ->where(function ($q) use ($userId) {
                $q->where(function ($q2) use ($userId) {
                    $q2->where('related_type', User::class)
                        ->where('related_id', $userId);
                })->orWhere(function ($q2) {
                    $q2->whereNull('related_type')
                        ->whereNull('related_id');
                });
            })
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $userId = Auth::id();

        Notification::where('is_read', false)
            ->where(function ($q) use ($userId) {
                $q->where(function ($q2) use ($userId) {
                    $q2->where('related_type', User::class)
                        ->where('related_id', $userId);
                })->orWhere(function ($q2) {
                    $q2->whereNull('related_type')
                        ->whereNull('related_id');
                });
            })
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function registerFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        \App\Models\UserFcmToken::updateOrCreate(
            ['user_id' => $userId, 'fcm_token' => $request->token],
            ['device_type' => $request->device_type, 'last_used_at' => now()]
        );

        return response()->json(['message' => 'Token stored successfully']);
    }
}

