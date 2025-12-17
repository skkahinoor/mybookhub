<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    /**
     * Get notifications for dropdown (AJAX)
     */
    public function getNotifications()
    {
        $notifications = Notification::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'related_id' => $notification->related_id,
                    'related_type' => $notification->related_type,
                    'is_read' => (bool) $notification->is_read,
                    'created_at' => $notification->created_at->toDateTimeString(),
                ];
            })
            ->values()
            ->toArray();

        $unreadCount = Notification::where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Show all notifications page
     */
    public function index()
    {
        Session::put('page', 'notifications');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $title = 'All Notifications';

        $notifications = Notification::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications', 'title', 'logos', 'headerLogo'));
    }
}
