<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendTemplate(string $to, string $template, array $params = [], ?string $language = null): bool
    {
        $token = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_id');
        $langCode = $language ?: config('services.whatsapp.language', 'en');

        if (empty($token) || empty($phoneId)) {
            Log::warning('WhatsApp config missing. Skipping send.', [
                'has_token' => !empty($token),
                'has_phone_id' => !empty($phoneId),
            ]);
            return false;
        }

        $formattedTo = preg_replace('/\D+/', '', $to);
        if (empty($formattedTo)) {
            return false;
        }

        $components = [];
        if (!empty($params)) {
            $components[] = [
                'type' => 'body',
                'parameters' => collect($params)->map(function ($param) {
                    return [
                        'type' => 'text',
                        'text' => (string) $param,
                    ];
                })->values()->all(),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $formattedTo,
            'type' => 'template',
            'template' => [
                'name' => $template,
                'language' => ['code' => $langCode],
                'components' => $components,
            ],
        ];

        try {
            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/v18.0/{$phoneId}/messages", $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('WhatsApp template send failed', [
                'status' => $response->status(),
                'response' => $response->json(),
                'to' => $formattedTo,
                'template' => $template,
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp template exception', [
                'message' => $e->getMessage(),
                'to' => $formattedTo,
                'template' => $template,
            ]);
        }

        return false;
    }
}
