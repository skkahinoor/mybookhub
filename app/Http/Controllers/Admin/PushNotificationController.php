<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class PushNotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(\App\Services\FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function create()
    {
        Session::put('page', 'push_notifications');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $title = 'Create Push Notification';
        $users = User::all();

        return view('admin.push_notifications.create', compact('headerLogo', 'logos', 'title', 'users'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'image_url' => 'nullable|url',
        ]);

        $title = $request->title;
        $body = $request->body;
        $data = [
            'type' => 'promotional',
            'image_url' => $request->image_url,
        ];

        if ($request->has('user_ids') && !empty($request->user_ids)) {
            // Send to specific users
            $success = $this->firebaseService->sendToUsers($request->user_ids, $title, $body, $data);
        } else {
            // Send to all using 'all_users' topic
            $success = $this->firebaseService->sendToAll($title, $body, $data);
        }

        if ($success) {
            return redirect()->back()->with('success_message', "Notification has been sent successfully!");
        } else {
            return redirect()->back()->with('error_message', "Failed to send notification. Please check logs.");
        }
    }
}
