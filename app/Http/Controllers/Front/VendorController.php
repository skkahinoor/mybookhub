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
use App\Models\Admin;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Section;
use App\Models\Setting;
use Intervention\Image\Facades\Image;
use GuzzleHttp\Client;

class VendorController extends Controller
{
    public function loginRegister(Request $request) {
        $condition = session('condition', 'new');
        $logos     = HeaderLogo::all();
        $sections  = Section::all();
        $language  = Language::get();
        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        return view('front.vendors.login_register',compact('condition', 'logos', 'sections', 'language'));
    }

    public function vendorRegister(Request $request) {
        $condition = session('condition', 'new');

        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [

                            'name'          => 'required',
                            'email'         => 'required|email|unique:admins|unique:vendors',
                            'mobile'        => 'required|min:10|numeric|unique:admins|unique:vendors',
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


            $vendor = new Vendor; // Vendor.php model which models (represents) the `vendors` database table

            $vendor->name   = $data['name'];
            $vendor->mobile = $data['mobile'];
            $vendor->email  = $data['email'];
            $vendor->status = 0;
            date_default_timezone_set('Africa/Cairo'); // https://www.php.net/manual/en/timezones.php and https://www.php.net/manual/en/timezones.africa.php
            $vendor->created_at = date('Y-m-d H:i:s'); // enter `created_at` MANUALLY!    // Formatting the date for MySQL: https://www.php.net/manual/en/function.date.php
            $vendor->updated_at = date('Y-m-d H:i:s'); // enter `updated_at` MANUALLY!

            $vendor->save();


            $vendor_id = DB::getPdo()->lastInsertId();
            $admin = new Admin;

            $admin->type      = 'vendor';
            $admin->vendor_id = $vendor_id; // take the generated `id` of the `vendors` table to store it a `vendor_id` in the `admins` table
            $admin->name      = $data['name'];
            $admin->mobile    = $data['mobile'];
            $admin->email     = $data['email'];
            $admin->password  = bcrypt($data['password']);
            $admin->status    = 0;

            date_default_timezone_set('Africa/Cairo');
            $admin->created_at = date('Y-m-d H:i:s');
            $admin->updated_at = date('Y-m-d H:i:s'); // enter `updated_at` MANUALLY!

            $admin->save();


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
                "template_id" => env('MSG91_TEMPLATE_ID'),
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
                    'authkey' => env('MSG91_AUTH_KEY'),
                    'content-type' => 'application/json',
                ],
            ]);

            Log::info("Vendor MSG91 Response:", [
                'status' => $response->getStatusCode(),
                'body'   => $response->getBody()->getContents(),
            ]);

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
            'email'  => 'required|email|unique:admins,email|unique:vendors,email',
            'mobile' => 'required|digits:10|unique:admins,mobile|unique:vendors,mobile',
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

    public function confirmVendor($email,Request $request) {
        $condition = session('condition', 'new');

        $email = base64_decode($email);
        $vendorCount = Vendor::where('email', $email)->count();
        if ($vendorCount > 0) { // if the vendor email exists
            // Check if the vendor is alreay active
            $vendorDetails = Vendor::where('email', $email)->first();
            if ($vendorDetails->confirm == 'Yes') { // if the vendor is already confirmed

                // Redirect vendor to vendor Login/Register page with an 'error' message
                $message = 'Your Vendor Account is already confirmed. You can login';
                return redirect('vendor/login-register')->with('error_message', $message);

            } else {

                Admin::where( 'email', $email)->update(['confirm' => 'Yes']);
                Vendor::where('email', $email)->update(['confirm' => 'Yes']);

                $messageData = [
                    'email'  => $email,
                    'name'   => $vendorDetails->name,
                    'mobile' => $vendorDetails->mobile
                ];
                \Illuminate\Support\Facades\Mail::send('emails.vendor_confirmed', $messageData, function ($message) use ($email) {
                    $message->to($email)->subject('You Vendor Account Confirmed');
                });

                $message = 'Your Vendor Email account is confirmed. You can login and add your personal, business and bank details to activate your Vendor Account to add products';
                return redirect('vendor/login-register')->with('success_message', $message);
            }
        } else {
            abort(404);
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
                'email'  => 'required|email|unique:admins,email,' . ($adminId ?? '') . ',id',
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
            $adminData = [
                'name'   => $data['name'],
                'email'  => $data['email'],
                'mobile' => $data['mobile'],
                'type'   => 'vendor',
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
                ];

                // Set plan dates
                if ($selectedPlan === 'pro') {
                    $vendorData['plan_started_at'] = now();
                    // If this is a trial (from setting or invite), set expiry date
                    if ($giveNewUsersProPlan || $isInvitePro) {
                        $vendorData['plan_expires_at'] = now()->addDays($proPlanTrialDurationDays);
                    } else {
                        // Regular Pro plan - will be set after payment
                        $vendorData['plan_expires_at'] = null;
                    }
                } else {
                    // Free plan
                    $vendorData['plan_started_at'] = now();
                    $vendorData['plan_expires_at'] = null;
                }

                $vendorId = Vendor::insertGetId($vendorData);

                // Insert Admin
                $adminData['vendor_id'] = $vendorId;
                $adminData['password']  = Hash::make($data['password']);
                $adminData['confirm']   = 'Yes';
                $adminData['status']    = isset($data['status']) ? 1 : 0;

                Admin::insert($adminData);

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

                $admin = Admin::where('id', $adminId)->first();
                Admin::where('id', $adminId)->update($adminData);

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
            $admin = Admin::where('id', $id)->first()->toArray();
            return view('admin/login', compact('admin'));
        }

        return view('admin/login');
    }

}
