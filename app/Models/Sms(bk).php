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
    public static function sendSms($mobile, $otp)
    {
        // Normalize number
        $to = '91' . preg_replace('/[^0-9]/', '', $mobile);

        try {
            $client = new Client([
                'timeout' => 10,          // ✅ total timeout (10 sec)
                'connect_timeout' => 5,   // ✅ connection timeout
            ]);

            $payload = [
                "template_id" => config('services.msg91.template_id'),
                "recipients" => [
                    [
                        "mobiles" => $to,
                        "var1" => $otp
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
                'body' => $body,
            ]);

            if (isset($body['type']) && $body['type'] === 'error') {
                return false;
            }

            return true;

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error("MSG91 CONNECTION TIMEOUT: " . $e->getMessage());
            return false;

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error("MSG91 REQUEST ERROR: " . $e->getMessage());
            return false;

        } catch (\Exception $e) {
            Log::error("MSG91 GENERAL ERROR: " . $e->getMessage());
            return false;
        }
    }
}