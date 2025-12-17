<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\SalesExecutive;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',   // email OR mobile/phone
            'password' => 'required',
        ]);

        $loginInput = $request->login;   // can be email or mobile/phone

        // All user types and their models
        $userTypes = [
            'superadmin' => Admin::class,
            'vendor'     => Admin::class,
            'sales'      => SalesExecutive::class,
            'user'       => User::class,
        ];

        foreach ($userTypes as $type => $model) {

            // Determine the login field based on model
            if ($model === Admin::class) {
                $user = $model::where('email', $loginInput)
                    ->orWhere('mobile', $loginInput)
                    ->first();
            } elseif ($model === SalesExecutive::class) {
                $user = $model::where('email', $loginInput)
                    ->orWhere('phone', $loginInput)
                    ->first();
            } elseif ($model === User::class) {
                $user = $model::where('email', $loginInput)
                    ->orWhere('mobile', $loginInput)
                    ->first();
            } else {
                $user = null;
            }

            // If user found and password matches
            if ($user && Hash::check($request->password, $user->password)) {

                // Admin has type field → superadmin / vendor
                if (in_array($type, ['superadmin', 'vendor'])) {
                    if ($user->type !== $type) {
                        continue; // Skip wrong type
                    }
                }

                // Check active/inactive
                if (isset($user->status) && $user->status == 0) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Your account is inactive. Please contact admin.',
                    ], 403);
                }

                // Create token
                $token = $user->createToken("{$type}-token")->plainTextToken;

                return response()->json([
                    'status'  => true,
                    'message' => ucfirst($type) . ' login successful',
                    'type'    => $type,
                    'token'   => $token,
                    'data'    => $user,
                ]);
            }
        }

        return response()->json([
            'status'  => false,
            'message' => 'Invalid email/phone or password.',
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout successful',
        ]);
    }

    public function validateToken(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token',
            ], 401);
        }


        $type = 'user';
        if ($user instanceof Admin) {
            $type = $user->type;
        } elseif ($user instanceof SalesExecutive) {
            $type = 'sales';
        }

        return response()->json([
            'status'  => true,
            'message' => 'Token is valid',
            'type'    => $type,
            'data'    => $user,
        ]);
    }

    public function sendSMS($phone, $otp)
    {
        $to = '91' . preg_replace('/[^0-9]/', '', $phone);

        try {
            $client = new Client();

            // payload must match MSG91 flow template variables
            $payload = [
                "template_id" => env('MSG91_TEMPLATE_ID'),
                "recipients" => [
                    [
                        "mobiles" => $to,
                        "OTP" => $otp // This MUST match your template variable
                    ]
                ]
            ];

            Log::info("MSG91 Payload", $payload);

            $response = $client->post("https://control.msg91.com/api/v5/flow/", [
                'json' => $payload,
                'headers' => [
                    'accept' => 'application/json',
                    'authkey' => env('MSG91_AUTH_KEY'),
                    'content-type' => 'application/json'
                ],
            ]);

            Log::info("MSG91 Response", [
                'status' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("MSG91 ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:sales_executives,email',
            'phone' => 'required|string|max:20|unique:sales_executives,phone',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        Cache::put('reg_name_' . $request->phone, $request->name, now()->addMinutes(10));
        Cache::put('reg_email_' . $request->phone, $request->email, now()->addMinutes(10));
        Cache::put('reg_phone_' . $request->phone, $request->phone, now()->addMinutes(10));
        Cache::put('reg_password_' . $request->phone, Hash::make($request->password), now()->addMinutes(10));

        $otp = rand(100000, 999999);

        DB::table('otps')->updateOrInsert(
            ['phone' => $request->phone],
            ['otp' => $otp, 'created_at' => now(), 'updated_at' => now()]
        );

        $sendStatus = $this->sendSMS($request->phone, $otp);

        if (!$sendStatus) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP. Try again.'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully via MSG91.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        $otpRecord = DB::table('otps')
            ->where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                "status" => false,
                "message" => "Invalid OTP"
            ], 400);
        }

        $name = Cache::get('reg_name_' . $request->phone);
        $email = Cache::get('reg_email_' . $request->phone);
        $phone = Cache::get('reg_phone_' . $request->phone);
        $password = Cache::get('reg_password_' . $request->phone);

        if (!$phone) {
            return response()->json([
                'status' => false,
                'message' => 'Registration session expired'
            ], 400);
        }

        $sales = SalesExecutive::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'status' => '0',
            'password' => $password,
        ]);

        // Create notification for admin
        \App\Models\Notification::create([
            'type' => 'sales_executive_registration',
            'title' => 'New Sales Executive Registration',
            'message' => "A new sales executive '{$name}' has registered and is waiting for approval.",
            'related_id' => $sales->id,
            'related_type' => 'App\Models\SalesExecutive',
            'is_read' => false,
        ]);

        Cache::forget('reg_name_' . $phone);
        Cache::forget('reg_email_' . $phone);
        Cache::forget('reg_phone_' . $phone);
        Cache::forget('reg_password_' . $phone);

        DB::table('otps')->where('phone', $phone)->delete();

        $this->sendRegistrationSuccessSMS($phone);

        return response()->json([
            "status" => true,
            "message" => "Registration successful!",
            "data" => $sales
        ]);
    }

    public function sendRegistrationSuccessSMS($phone)
    {
        $to = '91' . preg_replace('/[^0-9]/', '', $phone);

        try {
            $client = new Client();

            $payload = [
                "template_id" => env('MSG91_REG_SUCCESS_TEMPLATE_ID'), // NEW TEMPLATE ID
                "recipients" => [
                    [
                        "mobiles" => $to
                    ]
                ]
            ];

            Log::info("Registration Success SMS Payload", $payload);

            $response = $client->post("https://control.msg91.com/api/v5/flow/", [
                'json' => $payload,
                'headers' => [
                    'accept' => 'application/json',
                    'authkey' => env('MSG91_AUTH_KEY'),
                    'content-type' => 'application/json'
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Registration Success SMS ERROR: " . $e->getMessage());
            return false;
        }
    }


    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255|unique:sales_executives,email',
    //         'phone' => 'required|string|max:20',
    //         'password' => 'required|min:6| ',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $sales = SalesExecutive::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'status' => '0',
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Sales Executive registered successfully. Please log in to continue.',
    //         'data' => $sales,
    //     ], 201);
    // }
}
