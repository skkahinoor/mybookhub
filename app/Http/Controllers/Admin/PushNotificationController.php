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

        $messaging = app('firebase.messaging');
        $title = $request->title;
        $body = $request->body;
        $imageUrl = $request->image_url;

        $notification = Notification::create($title, $body, $imageUrl);

        if ($request->has('user_ids') && !empty($request->user_ids)) {
            $tokens = UserFcmToken::whereIn('user_id', $request->user_ids)->pluck('fcm_token')->toArray();
        } else {
            $tokens = UserFcmToken::pluck('fcm_token')->toArray();
        }

        if (empty($tokens)) {
            return redirect()->back()->with('error_message', 'No registered devices found.');
        }

        // Send to chunks of 500 tokens (Firebase limit for multicast)
        $chunks = array_chunk($tokens, 500);
        $totalSent = 0; $totalFailed = 0;

        foreach ($chunks as $chunk) {
            $message = CloudMessage::new()->withNotification($notification);
            $report = $messaging->sendMulticast($message, $chunk);
            $totalSent += $report->successes()->count();
            $totalFailed += $report->failures()->count();
        }

        return redirect()->back()->with('success_message', "Notification sent! Success: $totalSent, Failed: $totalFailed");
    }
}
