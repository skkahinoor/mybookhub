<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Sms extends Model
{
    use HasFactory;

    /**
     * Send SMS using MSG91
     */
    public static function sendSms($mobile, $otp) {
        // Normalize to Indian format with country code 91
        $to = '91' . preg_replace('/[^0-9]/', '', $mobile);

        try {
            $client = new Client();

            $payload = [
                "template_id" => config('services.msg91.template_id'),
                "recipients"  => [
                    [
                        "mobiles" => $to,
                        "var1"    => $otp
                    ],
                ],
            ];

            Log::info("MSG91 Payload:", $payload);

            $response = $client->post("https://control.msg91.com/api/v5/flow/", [
                'json' => $payload,
                'headers' => [
                    'accept' => 'application/json',
                    'authkey' => config('services.msg91.key'),
                    'content-type' => 'application/json',
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            Log::info("MSG91 Response:", [
                'status' => $response->getStatusCode(),
                'body'   => $body,
            ]);

            if (isset($body['type']) && $body['type'] === 'error') {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("MSG91 ERROR: " . $e->getMessage());
            return false;
        }
    }
}