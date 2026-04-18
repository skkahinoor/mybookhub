<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'image_url' => 'nullable|url',
        ]);

        $title = $request->title;
        $body = $request->body;
        $imageUrl = $request->image_url;

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '.' . $imageFile->getClientOriginalExtension();
            $relativeDir = 'admin/images/notifications';
            $destinationDir = public_path($relativeDir);

            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            $savePath = $destinationDir . DIRECTORY_SEPARATOR . $imageName;

            try {
                Image::configure(['driver' => 'gd']);
                $img = Image::make($imageFile->getRealPath());
                $img->fit(1024, 512, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($savePath);
                $imageUrl = asset($relativeDir . '/' . $imageName);
            } catch (\Throwable $e) {
                return redirect()->back()->with('error_message', "Image upload failed: " . $e->getMessage());
            }
        }

        $data = [
            'type' => 'promotional',
            'image_url' => $imageUrl,
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
