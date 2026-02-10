<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Vendor;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\VendorsBusinessDetail;
use App\Models\VendorsBankDetail;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Illuminate\Validation\Rule;


class VendorController extends Controller
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
        $request->validate([
            'name'     => 'required|string|max:255|regex:/^[\pL\s\-&.,\'()\/]+$/u',
            'email'    => 'required|email|unique:users,email',
            'mobile'   => 'required|string|min:10|max:15|unique:users,phone',
            'password' => 'required|min:6|confirmed',
            'location' => [
                'required',
                'regex:/^-?\d{1,3}\.\d+,\s*-?\d{1,3}\.\d+$/'
            ],
        ]);

        $phone = $request->mobile;

        Cache::put("reg_name_$phone", $request->name, now()->addMinutes(10));
        Cache::put("reg_email_$phone", $request->email, now()->addMinutes(10));
        Cache::put("reg_password_$phone", Hash::make($request->password), now()->addMinutes(10));
        Cache::put("reg_location_$phone", $request->location, now()->addMinutes(10));

        $otp = rand(100000, 999999);

        DB::table('otps')->updateOrInsert(
            ['phone' => $phone],
            ['otp' => $otp, 'created_at' => now(), 'updated_at' => now()]
        );

        if (!$this->sendSMS($phone, $otp)) {
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
            'otp'   => 'required'
        ]);

        $otpRecord = DB::table('otps')
            ->where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        $phone    = $request->phone;
        $name     = Cache::get("reg_name_$phone");
        $email    = Cache::get("reg_email_$phone");
        $password = Cache::get("reg_password_$phone");
        $location = Cache::get("reg_location_$phone");

        if (!$name || !$email || !$password || !$location) {
            return response()->json([
                'status' => false,
                'message' => 'Registration session expired'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // 🔹 Settings
            $givePro   = (bool) Setting::getValue('give_new_users_pro_plan', 0);
            $trialDays = (int) Setting::getValue('pro_plan_trial_duration_days', 30);

            // 🔹 Fetch vendor role (Spatie)
            $role = \Spatie\Permission\Models\Role::where([
                'name'       => 'vendor'
            ])->first();

            if (!$role) {
                throw new \Exception('Vendor role not found');
            }

            // 🔹 Create User
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone,
                'password' => $password,
                'status'   => 0,
                'role_id'  => $role->id
            ]);

            // 🔹 Assign Spatie Role
            $user->assignRole($role);

            // 🔹 Create Vendor (ONLY vendor fields)
            $vendor = Vendor::create([
                'user_id'          => $user->id,
                'location'         => $location,
                'plan'             => $givePro ? 'pro' : 'free',
                'plan_started_at'  => now(),
                'plan_expires_at'  => $givePro ? now()->addDays($trialDays) : null,
                'status'           => 0,
                'confirm'          => 'No'
            ]);

            // 🔹 Cleanup
            DB::table('otps')->where('phone', $phone)->delete();
            Cache::forget("reg_name_$phone");
            Cache::forget("reg_email_$phone");
            Cache::forget("reg_password_$phone");
            Cache::forget("reg_location_$phone");

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Registration successful',
                'data'    => [
                    'user'   => $user,
                    'vendor' => $vendor
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor Registration Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Registration failed'
            ], 500);
        }
    }

    public function getprofile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role || $role->name !== 'vendor') {
            return response()->json([
                'status'  => false,
                'message' => 'Only Vendor can access this profile.'
            ], 403);
        }

        // 🔒 Account status check
        if ($user->status != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        // 🔗 Fetch Vendor with User relation
        $vendor = Vendor::with('user')
            ->where('user_id', $user->id)
            ->first();

        if (!$vendor) {
            return response()->json([
                'status'  => false,
                'message' => 'Profile details not found'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Profile details fetched successfully',
            'data'    => [
                'vendor'   => $vendor,
                'user'     => [
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'phone'    => $user->phone,
                    'address'  => $user->address,
                    'pincode'  => $user->pincode,
                ],
                'country'  => $user->country?->name,
                'state'    => $user->state?->name,
                'district' => $user->district?->name,
                'block'    => $user->block?->name,
                'image'    => $user->profile_image
                    ? url('admin/images/photos/' . $user->profile_image)
                    : null
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role || $role->name !== 'vendor') {
            return response()->json([
                'status'  => false,
                'message' => 'Only Vendor can access this profile.'
            ], 403);
        }

        // 🔒 Status check
        if ($user->status != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        // 🔗 Vendor linked to user
        $vendor = Vendor::where('user_id', $user->id)->first();
        if (!$vendor) {
            return response()->json([
                'status'  => false,
                'message' => 'Vendor not found.'
            ], 404);
        }

        // ✅ Validation
        $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'nullable|string',
            'country_id'  => 'nullable|integer',
            'state_id'    => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'block_id'    => 'nullable|integer',
            'pincode'     => 'nullable|string|max:10',

            'location' => [
                'nullable',
                'regex:/^-?\d{1,3}\.\d+,\s*-?\d{1,3}\.\d+$/'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'mobile' => [
                'required',
                'string',
                'min:10',
                'max:15',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],

            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 Update USER (identity + location)
            $user->update([
                'name'        => $request->name,
                'email'       => $request->email,
                'phone'       => $request->mobile,
                'address'     => $request->address,
                'country_id'  => $request->country_id,
                'state_id'    => $request->state_id,
                'district_id' => $request->district_id,
                'block_id'    => $request->block_id,
                'pincode'     => $request->pincode,
            ]);

            // 🔹 Update VENDOR (vendor-only fields)
            if ($request->filled('location')) {
                $vendor->update([
                    'location' => $request->location,
                ]);
            }

            // 🖼 Image Upload
            if ($request->hasFile('image')) {
                $path = public_path('admin/images/photos');

                if ($user->profile_image && file_exists($path . '/' . $user->profile_image)) {
                    unlink($path . '/' . $user->profile_image);
                }

                $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                $request->image->move($path, $imageName);

                $user->update(['profile_image' => $imageName]);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Profile updated successfully',
                'data'    => [
                    'vendor'   => $vendor->fresh(),
                    'user'     => $user->fresh(),
                    'country'  => $user->country?->name,
                    'state'    => $user->state?->name,
                    'district' => $user->district?->name,
                    'block'    => $user->block?->name,
                    'image'    => $user->profile_image
                        ? url('admin/images/photos/' . $user->profile_image)
                        : null
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile Update Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Profile update failed'
            ], 500);
        }
    }

    public function saveBusinessDetails(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // 🔐 Dynamic role check via roles table
        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role || $role->name !== 'vendor') {
            return response()->json([
                'status'  => false,
                'message' => 'Only Vendor can access this profile.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        $vendor = Vendor::where('user_id', $user->id)->first();
        if (!$vendor) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor not found.'
            ], 404);
        }

        // ✅ Validation (updated)
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'required|string',
            'shop_city' => 'required|string|max:100',
            'shop_state' => 'required|string|max:100',
            'shop_country' => 'required|string|max:100',
            'shop_pincode' => 'required|string|max:10',
            'shop_mobile' => 'required|string|min:10|max:15',
            'shop_email' => 'nullable|email',
            'shop_website' => 'nullable|url',

            'address_proof' => 'nullable|string|max:255',
            'address_proof_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'business_license_number' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:20',
        ]);

        // 🖼 Handle Address Proof Image Upload
        $addressProofImage = null;
        if ($request->hasFile('address_proof_image')) {
            $path = public_path('admin/images/proofs');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $addressProofImage = time() . '_' . uniqid() . '.' . $request->address_proof_image->extension();
            $request->address_proof_image->move($path, $addressProofImage);
        }

        // 💾 Save / Update Business Details
        $businessDetail = VendorsBusinessDetail::updateOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'shop_name' => $request->shop_name,
                'shop_address' => $request->shop_address,
                'shop_city' => $request->shop_city,
                'shop_state' => $request->shop_state,
                'shop_country' => $request->shop_country,
                'shop_pincode' => $request->shop_pincode,
                'shop_mobile' => $request->shop_mobile,
                'shop_email' => $request->shop_email,
                'shop_website' => $request->shop_website,
                'address_proof' => $request->address_proof,
                'address_proof_image' => $addressProofImage,
                'business_license_number' => $request->business_license_number,
                'gst_number' => $request->gst_number,
                'pan_number' => $request->pan_number,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Business details saved successfully',
            'data' => $businessDetail
        ]);
    }


    public function saveBankDetails(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role || $role->name !== 'vendor') {
            return response()->json([
                'status'  => false,
                'message' => 'Only Vendor can access this profile.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        $vendor = Vendor::where('user_id', $user->id)->first();
        if (!$vendor) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor not found.'
            ], 404);
        }

        $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:30',
            'bank_ifsc_code' => 'required|string|max:20',
        ]);

        $bankDetail = VendorsBankDetail::updateOrCreate(
            ['vendor_id' => $vendor->id],
            $request->only([
                'account_holder_name',
                'bank_name',
                'account_number',
                'bank_ifsc_code',
            ])
        );

        return response()->json([
            'status' => true,
            'message' => 'Bank details saved successfully',
            'data' => $bankDetail
        ]);
    }


    public function getBusinessDetails(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role || $role->name !== 'vendor') {
            return response()->json([
                'status'  => false,
                'message' => 'Only Vendor can access this profile.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        $vendor = Vendor::where('user_id', $user->id)->first();
        if (!$vendor) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor not found.'
            ], 404);
        }

        $businessDetail = VendorsBusinessDetail::where('vendor_id', $vendor->id)->first();

        if (!$businessDetail) {
            return response()->json([
                'status' => false,
                'message' => 'Business details not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Business details fetched successfully',
            'data' => $businessDetail
        ]);
    }


    public function getBankDetails(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role || $role->name !== 'vendor') {
            return response()->json([
                'status'  => false,
                'message' => 'Only Vendor can access this profile.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        $vendor = Vendor::where('user_id', $user->id)->first();
        if (!$vendor) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor not found.'
            ], 404);
        }

        $bankDetail = VendorsBankDetail::where('vendor_id', $vendor->id)->first();

        if (!$bankDetail) {
            return response()->json([
                'status' => false,
                'message' => 'Bank details not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Bank details fetched successfully',
            'data' => $bankDetail
        ]);
    }
}
