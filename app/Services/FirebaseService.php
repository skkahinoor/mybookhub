<?php

namespace App\Services;

use App\Models\Notification as NotificationModel;
use App\Models\UserFcmToken;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;

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
        $imageUrl = $data['image_url'] ?? null;
        $notification = Notification::create($title, $body, $imageUrl);
        
        $data['image'] = $imageUrl; // Compatibility

        $message = CloudMessage::withTarget('topic', 'all_users')
            ->withNotification($notification);

        if ($imageUrl) {
            // Android Config
            $androidConfig = AndroidConfig::fromArray([
                'priority' => 'high',
                'notification' => [
                    'image' => $imageUrl,
                    'sound' => 'default',
                ],
            ]);
            $message = $message->withAndroidConfig($androidConfig);

            // WebPush Config
            $webPushConfig = [
                'notification' => [
                    'image' => $imageUrl,
                ]
            ];
            $message = $message->withWebPushConfig($webPushConfig);
        }

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
        // Store the notification in the database for each user so it shows in their in-app list
        foreach ($userIds as $id) {
            NotificationModel::create([
                'type' => $data['type'] ?? 'targeted',
                'title' => $title,
                'message' => $body,
                'related_id' => $id,
                'related_type' => \App\Models\User::class,
                'is_read' => false,
            ]);
        }

        $tokens = UserFcmToken::whereIn('user_id', $userIds)->pluck('fcm_token')->toArray();
        \Log::info('Firebase SendToUsers: Found ' . count($tokens) . ' tokens.');
        
        if (empty($tokens)) {
            \Log::warning('Firebase SendToUsers: No FCM tokens found for user IDs: ' . implode(',', $userIds));
            return true;
        }

        $imageUrl = $data['image_url'] ?? null;
        $notification = Notification::create($title, $body, $imageUrl);
        
        // Android specific config for high priority and "Heads-up" display
        $androidConfigData = [
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'channel_id' => 'default', 
                'visibility' => 'public',
                'notification_priority' => 'PRIORITY_MAX',
            ],
        ];

        if ($imageUrl) {
            $androidConfigData['notification']['image'] = $imageUrl;
            $data['image'] = $imageUrl; // Compatibility
        }

        $androidConfig = AndroidConfig::fromArray($androidConfigData);

        // Apns Config for iOS images
        $apnsConfig = null;
        if ($imageUrl) {
            $apnsConfig = ApnsConfig::fromArray([
                'payload' => [
                    'aps' => [
                        'mutable-content' => 1,
                    ],
                ],
                'fcm_options' => [
                    'image' => $imageUrl,
                ],
            ]);
        }

        $chunks = array_chunk($tokens, 500);
        $successCount = 0;

        foreach ($chunks as $chunk) {
            try {
                $message = CloudMessage::new()
                    ->withNotification($notification)
                    ->withAndroidConfig($androidConfig);

                if ($apnsConfig) {
                    $message = $message->withApnsConfig($apnsConfig);
                }

                if (!empty($data)) {
                    $message = $message->withData($data);
                }
                
                $report = $this->messaging->sendMulticast($message, $chunk);
                $successCount += $report->successes()->count();
                
                if ($report->failures()->count() > 0) {
                    \Log::warning('Firebase SendToUsers: ' . $report->failures()->count() . ' failures out of ' . count($chunk));
                }
            } catch (\Exception $e) {
                \Log::error('Firebase SendToUsers Exception: ' . $e->getMessage());
            }
        }

        \Log::info('Firebase SendToUsers: Successfully sent ' . $successCount . ' notifications.');
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
