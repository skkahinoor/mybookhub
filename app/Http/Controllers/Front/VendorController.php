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
    private function formatWhatsappPhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if (!$digits) {
            return null;
        }

        // If local 10-digit Indian mobile, prefix country code.
        if (strlen($digits) === 10) {
            return '91' . $digits;
        }

        // If already in 91XXXXXXXXXX format, keep as-is.
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return $digits;
        }

        return null;
    }

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
        $checkrole = role('vendor', 'web')->id;
        dd($checkrole);
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
            // $role = Role::where('name', 'vendor')->first();
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'phone'     => $data['mobile'],
                'password'  => bcrypt($data['password']),
                'role_id'   => role('vendor', 'web')->id,
                'status'    => 0,
            ]);

            // if ($role) {
            //     $user->assignRole($role);
            // }

            // Create vendor record with only vendor-specific fields
            $vendor = new Vendor;
            $vendor->user_id = $user->id;
            $vendor->location = $data['location'] ?? null;
            date_default_timezone_set('Africa/Cairo');
            $vendor->created_at = date('Y-m-d H:i:s');
            $vendor->updated_at = date('Y-m-d H:i:s');
            $vendor->save();


            DB::commit();
            $message = 'Thanks for registering as Vendor. Please wait for admin approval to activate your account.';
            return redirect()->back()->with('success_message', $message);
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
            'pincode' => 'required|digits:6',
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
            'vendor_reg_pincode' => $request->pincode,
            'vendor_reg_location' => $request->location,
        ]);

        $sent = \App\Models\Sms::sendSms($request->mobile, $otp);

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



    public function showRegister(Request $request)
    {
        $proPlanPrice = (int) Setting::getValue('pro_plan_price', 49900);
        $freePlanBookLimit = (int) Setting::getValue('free_plan_book_limit', 100);
        $giveNewUsersProPlan = (bool) Setting::getValue('give_new_users_pro_plan', 0);
        $proPlanTrialDurationDays = (int) Setting::getValue('pro_plan_trial_duration_days', 30);
        $inviteToken = $request->query('invite');
        $storedInviteToken = Setting::getValue('invite_pro_token');
        $isInvitePro = $inviteToken && $storedInviteToken && hash_equals($storedInviteToken, $inviteToken);

        // Sales referral and plan pre-selection
        $ref = $request->query('ref');
        $plan = $request->query('plan', 'free');

        return view('admin.register-vendor', compact(
            'proPlanPrice',
            'freePlanBookLimit',
            'giveNewUsersProPlan',
            'proPlanTrialDurationDays',
            'inviteToken',
            'isInvitePro',
            'ref',
            'plan'
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
                'mobile' => 'required|digits:10',
                'pincode' => 'required|numeric|digits:6',
                'location' => ['required', 'regex:/^-?\d{1,3}\.\d+,\s*-?\d{1,3}\.\d+$/'],
                'whatsapp_opt_in' => 'required|accepted',
            ];

            $customMessages = [
                'name.required'   => 'Name is required',
                'name.regex'      => 'Valid Name is required',
                'email.required'  => 'Email is required',
                'email.email'     => 'Valid Email is required',
                'email.unique'    => 'Email already exists',
                'mobile.required' => 'Mobile is required',
                'mobile.digits'   => 'Mobile must be exactly 10 digits',
                'whatsapp_opt_in.required' => 'WhatsApp consent is required',
                'whatsapp_opt_in.accepted' => 'Please allow WhatsApp updates to continue',
            ];

            // Add mode password validation
            if (empty($adminId)) {
                $rules['password'] = 'required|min:6|confirmed';
                $rules['password_confirmation'] = 'required';
                $rules['otp'] = 'required';
                $rules['plan'] = 'required|in:free,pro';
            }

            $this->validate($request, $rules, $customMessages);

            $whatsappPhone = $this->formatWhatsappPhone($data['mobile'] ?? null);
            if ($whatsappPhone === null) {
                return back()
                    ->withErrors(['mobile' => 'Enter a valid Indian mobile number (10 digits).'])
                    ->withInput();
            }

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
                'pincode' => $data['pincode'] ?? null,
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

                // Insert User first (vendor basics are in users table).
                $role = Role::where('name', 'vendor')->first();
                $user = User::create([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'phone'    => $data['mobile'],
                    'pincode'  => $data['pincode'] ?? null,
                    'password' => Hash::make($data['password']),
                    'role_id'  => $role->id,
                    'status'   => isset($data['status']) ? 1 : 0,
                    'added_by' => $data['ref'] ?? null,
                ]);

                if ($role) {
                    $user->assignRole($role);
                }

                // Insert Vendor with vendor-specific fields only.
                $vendor = Vendor::create([
                    'user_id' => $user->id,
                    'location' => $data['location'],
                    'whatsapp_opt_in' => true,
                    'whatsapp_phone' => $whatsappPhone,
                    'whatsapp_opt_in_at' => now(),
                    'plan' => $selectedPlan,
                    'plan_started_at' => now(),
                    'plan_expires_at' => ($selectedPlan === 'pro' && ($giveNewUsersProPlan || $isInvitePro))
                        ? now()->addDays($proPlanTrialDurationDays)
                        : null,
                ]);
                $vendorId = $vendor->id;



                // Clear OTP and session after successful registration
                DB::table('otps')->where('phone', $data['mobile'])->delete();
                session()->forget(['vendor_reg_name', 'vendor_reg_email', 'vendor_reg_mobile', 'vendor_reg_pincode', 'vendor_reg_location',]);

                $wasTrialProPlan = ($giveNewUsersProPlan || $isInvitePro) && $selectedPlan === 'pro';

                // If Pro plan selected explicitly (not from trial/invite), redirect to payment
                if ($selectedPlan === 'pro' && !$wasTrialProPlan) {
                    return redirect()->route('vendor.payment.create', ['vendor_id' => $vendorId])
                        ->with('vendor_id', $vendorId);
                }

                // Trial Pro plan (setting or invite) or Free plan - just redirect to login
                $planMessage = $wasTrialProPlan
                    ? "Vendor registered successfully! You have been given Pro plan access for {$proPlanTrialDurationDays} days. Please wait for admin approval before logging in."
                    : 'Vendor registered successfully! Please wait for admin approval before logging in.';

                return redirect('vendor/login')
                    ->with('success_message', $planMessage);
            } else {
                // ================= EDIT MODE =================

                $admin = User::where('id', $adminId)->first();
                User::where('id', $adminId)->update($userData);

                if ($admin && $admin->type === 'vendor' && $admin->vendor_id) {
                    Vendor::where('id', $admin->vendor_id)->update([
                        'location' => $data['location'],
                        'whatsapp_opt_in' => $request->boolean('whatsapp_opt_in'),
                        'whatsapp_phone' => $whatsappPhone,
                        'whatsapp_opt_in_at' => $request->boolean('whatsapp_opt_in') ? now() : null,
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
