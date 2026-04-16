<?php

namespace App\Services;

use App\Models\Notification as NotificationModel;
use App\Models\UserFcmToken;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase.messaging');
    }

    /**
     * Send notification to all users using the 'all_users' topic
     */
    public function sendToAll($title, $body, $data = [])
    {
        $notification = Notification::create($title, $body);
        $message = CloudMessage::withTarget('topic', 'all_users')
            ->withNotification($notification);

        if (!empty($data)) {
            $message = $message->withData($data);
        }

        try {
            $this->messaging->send($message);
            
            // Log to database for in-app notifications
            NotificationModel::create([
                'type' => 'broadcast',
                'title' => $title,
                'message' => $body,
                'related_id' => null,
                'related_type' => null,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Firebase SendToAll Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to specific users by their IDs
     */
    public function sendToUsers(array $userIds, $title, $body, $data = [])
    {
        // Always create database entries for each user first
        foreach ($userIds as $id) {
            NotificationModel::create([
                'type' => $data['type'] ?? 'targeted',
                'title' => $title,
                'message' => $body,
                'related_id' => $id,
                'related_type' => User::class,
            ]);
        }

        $tokens = UserFcmToken::whereIn('user_id', $userIds)->pluck('fcm_token')->toArray();
        
        if (empty($tokens)) {
            return true; // Still return true because we saved it to DB at least
        }

        $notification = Notification::create($title, $body);
        $chunks = array_chunk($tokens, 500);
        $successCount = 0;

        foreach ($chunks as $chunk) {
            $message = CloudMessage::new()->withNotification($notification);
            if (!empty($data)) {
                $message = $message->withData($data);
            }
            
            $report = $this->messaging->sendMulticast($message, $chunk);
            $successCount += $report->successes()->count();
        }

        return $successCount > 0;
    }

    /**
     * Subscribe a token to a topic
     */
    public function subscribeToTopic($token, $topic)
    {
        try {
            $this->messaging->subscribeToTopic($topic, $token);
            return true;
        } catch (\Exception $e) {
            \Log::error('Firebase SubscribeToTopic Error: ' . $e->getMessage());
            return false;
        }
    }
}
