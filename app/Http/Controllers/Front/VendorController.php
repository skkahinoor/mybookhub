<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\HeaderLogo;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Language;
use Spatie\Permission\Models\Role;
use App\Models\Notification;
use App\Models\Section;
use App\Models\Setting;
use Intervention\Image\Facades\Image;
use GuzzleHttp\Client;

class VendorController extends Controller
{
    public function loginRegister(Request $request)
    {
        $condition = session('condition', 'new');
        $logos     = HeaderLogo::all();
        $sections  = Section::all();
        $language  = Language::get();
        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        return view('front.vendors.login_register', compact('condition', 'logos', 'sections', 'language'));
    }

    public function vendorRegister(Request $request)
    {
        $condition = session('condition', 'new');

        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'name'          => 'required',
                'email'         => 'required|email|unique:users,email',
                'mobile'        => 'required|min:10|numeric|unique:users,phone',
                'accept'        => 'required'
            ];

            $customMessages = [
                'name.required'             => 'Name is required',
                'email.required'            => 'Email is required',
                'email.unique'              => 'Email alreay exists',
                'mobile.required'           => 'Mobile is required',
                'mobile.unique'             => 'Mobile alreay exists',
                'accept.required'           => 'Please accept Terms & Conditions',
            ];

            $validator = Validator::make($data, $rules, $customMessages);
            if ($validator->fails()) {
                return \Illuminate\Support\Facades\Redirect::back()->withErrors($validator);
            }

            DB::beginTransaction();

            // Create user first
            $role = Role::where('name', 'vendor')->first();
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'phone'     => $data['mobile'],
                'password'  => bcrypt($data['password']),
                'role_id'   => $role ? $role->id : null,
                'status'    => 0,
                'confirm'   => 'No',
            ]);

            if ($role) {
                $user->assignRole($role);
            }

            // Create vendor record with only vendor-specific fields
            $vendor = new Vendor;
            $vendor->user_id = $user->id;
            $vendor->location = $data['location'] ?? null;
            $vendor->status = 0;
            $vendor->confirm = 'No';
            date_default_timezone_set('Africa/Cairo');
            $vendor->created_at = date('Y-m-d H:i:s');
            $vendor->updated_at = date('Y-m-d H:i:s');
            $vendor->save();


            // Send the Confirmation Email to the new vendor who has just registered
            $email = $data['email']; // the vendor's email

            // The email message data/variables that will be passed in to the email view
            $messageData = [
                'email' => $data['email'],
                'name'  => $data['name'],
                'code'  => base64_encode($data['email'])
            ];

            \Illuminate\Support\Facades\Mail::send('emails.vendor_confirmation', $messageData, function ($message) use ($email) {
                $message->to($email)->subject('Confirm your Vendor Account');
            });


            DB::commit();
            $message = 'Thanks for registering as Vendor. Please confirm your email to activate your account.';
            return redirect()->back()->with('success_message', $message);
        }
    }

    /**
     * Send SMS using MSG91 (same pattern as Sales OTP)
     */
    public function sendSMS($phone, $otp)
    {
        // Normalize to Indian format with country code 91 (same as sales)
        $to = '91' . preg_replace('/[^0-9]/', '', $phone);

        try {
            $client = new Client();

            $payload = [
                "template_id" => config('services.msg91.template_id'),
                "recipients"  => [
                    [
                        "mobiles" => $to,
                        "OTP"     => $otp,
                    ],
                ],
            ];

            Log::info("Vendor MSG91 Payload:", $payload);

            $response = $client->post("https://control.msg91.com/api/v5/flow/", [
                'json' => $payload,
                'headers' => [
                    'accept' => 'application/json',
                    'authkey' => config('services.msg91.key'),
                    'content-type' => 'application/json',
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            Log::info("Vendor MSG91 Response:", [
                'status' => $response->getStatusCode(),
                'body'   => $body,
            ]);

            if (isset($body['type']) && $body['type'] === 'error') {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Vendor MSG91 ERROR: " . $e->getMessage());
            return false;
        }
    }

    /**
     * AJAX: Send OTP for vendor registration (like sales.register)
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:150|regex:/^[\pL\s\-&.,\'()\/]+$/u',
            'email'  => 'required|email|unique:users,email',
            'mobile' => 'required|digits:10|unique:users,phone',
            'location' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $otp = rand(100000, 999999);

        DB::table('otps')->updateOrInsert(
            ['phone' => $request->mobile],
            ['otp' => $otp, 'created_at' => now(), 'updated_at' => now()]
        );

        Log::info("Stored OTP for vendor registration", [
            'phone' => $request->mobile,
            'otp'   => $otp
        ]);

        // Store basic data in session (optional, similar to sales)
        session([
            'vendor_reg_name'   => $request->name,
            'vendor_reg_email'  => $request->email,
            'vendor_reg_mobile' => $request->mobile,
            'vendor_reg_location' => $request->location,
        ]);

        $sent = $this->sendSMS($request->mobile, $otp);

        if (!$sent) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP failed to send. Try again.',
            ], 500);
        }

        return response()->json([
            'status'  => true,
            'message' => 'OTP sent successfully!',
        ]);
    }

    public function confirmVendor($email, Request $request)
    {
        $condition = session('condition', 'new');

        $email = base64_decode($email);

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            abort(404);
        }

        // Find vendor by user_id
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            abort(404);
        }

        if ($vendor->confirm == 'Yes') { // if the vendor is already confirmed
            // Redirect vendor to vendor Login/Register page with an 'error' message
            $message = 'Your Vendor Account is already confirmed. You can login';
            return redirect('vendor/login-register')->with('error_message', $message);
        } else {
            User::where('email', $email)->update(['confirm' => 'Yes']);
            $vendor->update(['confirm' => 'Yes']);

            $messageData = [
                'email'  => $email,
                'name'   => $user->name,
                'mobile' => $user->phone
            ];
            \Illuminate\Support\Facades\Mail::send('emails.vendor_confirmed', $messageData, function ($message) use ($email) {
                $message->to($email)->subject('You Vendor Account Confirmed');
            });

            $message = 'Your Vendor Email account is confirmed. You can login and add your personal, business and bank details to activate your Vendor Account to add products';
            return redirect('vendor/login-register')->with('success_message', $message);
        }
    }

    public function showRegister(Request $request)
    {
        $proPlanPrice = (int) Setting::getValue('pro_plan_price', 49900);
        $freePlanBookLimit = (int) Setting::getValue('free_plan_book_limit', 100);
        $giveNewUsersProPlan = (bool) Setting::getValue('give_new_users_pro_plan', 0);
        $proPlanTrialDurationDays = (int) Setting::getValue('pro_plan_trial_duration_days', 30);
        $inviteToken = $request->query('invite');
        $storedInviteToken = Setting::getValue('invite_pro_token');
        $isInvitePro = $inviteToken && $storedInviteToken && hash_equals($storedInviteToken, $inviteToken);

        return view('admin.register-vendor', compact(
            'proPlanPrice',
            'freePlanBookLimit',
            'giveNewUsersProPlan',
            'proPlanTrialDurationDays',
            'inviteToken',
            'isInvitePro'
        ));
    }

    public function register(Request $request, $id = null)
    {

        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);
            $adminId = $data['admin_id'] ?? $id ?? null;

            // Validation
            $rules = [
                'name'   => 'required|regex:/^[\pL\s\-&.,\'()\/]+$/u',
                'email'  => 'required|email|unique:users,email,' . ($adminId ?? '') . ',id',
                'mobile' => 'required|numeric',
                'location' => ['required', 'regex:/^-?\d{1,3}\.\d+,\s*-?\d{1,3}\.\d+$/']
            ];

            $customMessages = [
                'name.required'   => 'Name is required',
                'name.regex'      => 'Valid Name is required',
                'email.required'  => 'Email is required',
                'email.email'     => 'Valid Email is required',
                'email.unique'    => 'Email already exists',
                'mobile.required' => 'Mobile is required',
                'mobile.numeric'  => 'Valid Mobile is required',
            ];

            // Add mode password validation
            if (empty($adminId)) {
                $rules['password'] = 'required|min:6|confirmed';
                $rules['password_confirmation'] = 'required';
                $rules['otp'] = 'required';
                $rules['plan'] = 'required|in:free,pro';
            }

            $this->validate($request, $rules, $customMessages);

            // If ADD MODE with OTP, verify the OTP (only when coming from register-vendor page)
            if (empty($adminId)) {
                $otpRecord = DB::table('otps')
                    ->where('phone', $data['mobile'])
                    ->where('otp', $data['otp'])
                    ->first();

                if (!$otpRecord) {
                    return back()
                        ->withErrors(['otp' => 'Invalid OTP'])
                        ->withInput();
                }
            }

            // Prepare Admin Data (NO IMAGE)
            $userData = [
                'name'   => $data['name'],
                'email'  => $data['email'],
                'phone'  => $data['mobile'],
            ];

            if (empty($id)) {
                // ================= ADD MODE =================
                $selectedPlan = $data['plan'] ?? 'free';

                // Check settings and invite link
                $giveNewUsersProPlan = (bool) Setting::getValue('give_new_users_pro_plan', 0);
                $proPlanTrialDurationDays = (int) Setting::getValue('pro_plan_trial_duration_days', 30);
                $storedInviteToken = Setting::getValue('invite_pro_token');
                $requestInviteToken = $data['invite_token'] ?? null;
                $isInvitePro = $requestInviteToken && $storedInviteToken && hash_equals($storedInviteToken, $requestInviteToken);

                // If setting is enabled OR invite link used, force Pro (trial, no payment)
                if ($giveNewUsersProPlan || $isInvitePro) {
                    $selectedPlan = 'pro';
                }

                // Insert Vendor with plan
                $vendorData = [
                    'name'    => $data['name'],
                    'email'   => $data['email'],
                    'mobile'  => $data['mobile'],
                    'location' => $data['location'],
                    'confirm' => 'Yes',
                    'status'  => isset($data['status']) ? 1 : 0,
                    'plan'    => $selectedPlan,
                    'plan_started_at' => now(),
                    'plan_expires_at' => ($selectedPlan === 'pro' && ($giveNewUsersProPlan || $isInvitePro)) ? now()->addDays($proPlanTrialDurationDays) : null,
                ];

                $vendor = Vendor::create($vendorData);
                $vendorId = $vendor->id;

                // Insert User instead of Admin
                $role = Role::where('name', 'vendor')->first();
                $user = User::create([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'phone'    => $data['mobile'],
                    'password' => Hash::make($data['password']),
                    'role_id'  => $role ? $role->id : null,
                    'status'   => isset($data['status']) ? 1 : 0,
                    // If we still need to support legacy Admin table/field:
                    // 'type'     => 'vendor', 
                ]);

                if ($role) {
                    $user->assignRole($role);
                }

                // Link Vendor to User
                $vendor->update(['user_id' => $user->id]);



                // Clear OTP and session after successful registration
                DB::table('otps')->where('phone', $data['mobile'])->delete();
                session()->forget(['vendor_reg_name', 'vendor_reg_email', 'vendor_reg_mobile', 'vendor_reg_location',]);

                $wasTrialProPlan = ($giveNewUsersProPlan || $isInvitePro) && $selectedPlan === 'pro';

                // If Pro plan selected explicitly (not from trial/invite), redirect to payment
                if ($selectedPlan === 'pro' && !$wasTrialProPlan) {
                    return redirect()->route('vendor.payment.create', ['vendor_id' => $vendorId])
                        ->with('vendor_id', $vendorId);
                }

                // Trial Pro plan (setting or invite) or Free plan - just redirect to login
                $planMessage = $wasTrialProPlan
                    ? "Vendor registered successfully! You have been given Pro plan access for {$proPlanTrialDurationDays} days."
                    : 'Vendor registered successfully with Free plan!';

                return redirect('admin/login')
                    ->with('success_message', $planMessage);
            } else {
                // ================= EDIT MODE =================

                $admin = User::where('id', $adminId)->first();
                User::where('id', $adminId)->update($userData);

                if ($admin && $admin->type === 'vendor' && $admin->vendor_id) {
                    Vendor::where('id', $admin->vendor_id)->update([
                        'name'   => $data['name'],
                        'email'  => $data['email'],
                        'mobile' => $data['mobile'],
                        'location' => $data['location'],
                    ]);
                }

                return redirect('admin/admins')
                    ->with('success_message', 'Vendor updated successfully!');
            }
        }

        // GET request
        if (!empty($id)) {
            $admin = User::where('id', $id)->first()->toArray();
            return view('admin/login', compact('admin'));
        }

        return view('admin/login');
    }
}
