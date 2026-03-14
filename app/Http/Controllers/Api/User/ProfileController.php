<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\Notification;
use Spatie\Permission\Models\Role;

class ProfileController extends Controller
{
    public function sendSMS($phone, $otp)
    {
        $to = '91' . preg_replace('/[^0-9]/', '', $phone);

        try {
            $client = new Client();

            $payload = [
                "template_id" => env('MSG91_TEMPLATE_ID'),
                "recipients" => [
                    [
                        "mobiles" => $to,
                        "OTP" => $otp
                    ]
                ]
            ];

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

            Log::error("MSG91 ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        Cache::put('student_reg_name_' . $request->phone, $request->name, now()->addMinutes(10));
        Cache::put('student_reg_email_' . $request->phone, $request->email, now()->addMinutes(10));
        Cache::put('student_reg_phone_' . $request->phone, $request->phone, now()->addMinutes(10));
        Cache::put('student_reg_password_' . $request->phone, Hash::make($request->password), now()->addMinutes(10));

        $otp = rand(100000, 999999);

        DB::table('otps')->updateOrInsert(
            ['phone' => $request->phone],
            ['otp' => $otp, 'created_at' => now(), 'updated_at' => now()]
        );

        $sendStatus = $this->sendSMS($request->phone, $otp);

        if (!$sendStatus) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully'
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

        $name = Cache::get('student_reg_name_' . $request->phone);
        $email = Cache::get('student_reg_email_' . $request->phone);
        $phone = Cache::get('student_reg_phone_' . $request->phone);
        $password = Cache::get('student_reg_password_' . $request->phone);

        if (!$phone) {
            return response()->json([
                'status' => false,
                'message' => 'Session expired'
            ], 400);
        }

        $role = Role::where('name', 'student')->first();

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Student role not found'
            ]);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role_id' => $role->id,
            'status' => 1,
        ]);

        $user->assignRole($role);

        Notification::create([
            'type' => 'student_registration',
            'title' => 'New Student Registered',
            'message' => "Student {$name} has registered successfully.",
            'related_id' => $user->id,
            'related_type' => 'App\Models\User',
            'is_read' => false,
        ]);

        Cache::forget('student_reg_name_' . $phone);
        Cache::forget('student_reg_email_' . $phone);
        Cache::forget('student_reg_phone_' . $phone);
        Cache::forget('student_reg_password_' . $phone);

        DB::table('otps')->where('phone', $phone)->delete();

        return response()->json([
            "status" => true,
            "message" => "Student registered successfully",
            "data" => $user
        ]);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $userDetails = clone $this->getUpdatedUser($user->id);

        return response()->json([
            'status' => true,
            'message' => 'Profile details fetched successfully',
            'data' => $userDetails
        ], 200);
    }

    private function getUpdatedUser($userId)
    {
        return User::with([
            'institution.section',
            'institution.category',
            'institutionClass.subcategory',
            'academicProfile',
            'country',
            'state',
            'district',
            'block',
            'assignedRole',
            'salesExecutive',
            'vendorPersonal',
            'vendorBusiness',
            'vendorBank'
        ])->find($userId);
    }

    public function updateBasicInfo(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'father_names' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userToUpdate = User::find($user->id);

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');

            if ($userToUpdate->profile_image) {
                $oldImagePath = public_path($userToUpdate->profile_image);
                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }

            $timestamp = time();
            $extension = $image->getClientOriginalExtension();
            $fileName = "avatar_{$userToUpdate->id}_{$timestamp}.{$extension}";

            $destinationPath = public_path('asset/user');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $image->move($destinationPath, $fileName);
            $userToUpdate->profile_image = 'asset/user/' . $fileName;
        }

        $fields = ['name', 'email', 'phone', 'father_names', 'gender', 'dob'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $userToUpdate->$field = $request->input($field);
            }
        }

        $userToUpdate->save();

        return response()->json([
            'status' => true,
            'message' => 'Basic info updated successfully',
            'data' => clone $this->getUpdatedUser($user->id)
        ], 200);
    }

    public function updateAcademicInfo(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'institution_id' => 'nullable|integer|exists:institution_managements,id',
            'institution_classes_id' => 'nullable|integer|exists:institution_classes,id',
            'roll_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userToUpdate = User::find($user->id);

        $fields = ['institution_id', 'institution_classes_id', 'roll_number'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $userToUpdate->$field = $request->input($field);
            }
        }

        $userToUpdate->save();

        return response()->json([
            'status' => true,
            'message' => 'Academic info updated successfully',
            'data' => clone $this->getUpdatedUser($user->id)
        ], 200);
    }

    public function updateAddress(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'block_id' => 'nullable|integer',
            'address' => 'nullable|string',
            'pincode' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userToUpdate = User::find($user->id);

        $fields = ['country_id', 'state_id', 'district_id', 'block_id', 'address', 'pincode'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $userToUpdate->$field = $request->input($field);
            }
        }

        $userToUpdate->save();

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => clone $this->getUpdatedUser($user->id)
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors() // confirm_password fails will show up here
            ], 422);
        }

        // Check if current password matches
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password does not match',
            ], 400);
        }

        // Update password
        $userToUpdate = User::find($user->id);
        $userToUpdate->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $userToUpdate->save();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ], 200);
    }

}
