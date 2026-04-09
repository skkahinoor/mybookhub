<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\AcademicProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Otp;
use App\Models\Sms;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function Login()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.auth.login', compact('logos', 'headerLogo'));
    }
    public function Register()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.auth.register', compact('logos', 'headerLogo'));
    }
    public function loginStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string|max:150', // email or phone
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->route('student.login')
                ->with('error', $validator->errors()->first());
        }

        $loginInput = trim($request->login);

        // Detect email or phone
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $credentials = [
                'email'    => $loginInput,
                'password' => $request->password,
            ];
        } else {
            $credentials = [
                'phone'    => $loginInput,
                'password' => $request->password,
            ];
        }

        if (Auth::attempt($credentials)) {
            // Merge guest cart with user cart
            $session_id = \Illuminate\Support\Facades\Session::get('session_id');
            if (empty($session_id)) {
                $session_id = \Illuminate\Support\Facades\Session::getId();
            }
            \App\Models\Cart::mergeCart($session_id, Auth::id());

            return redirect()->intended('/');
        }

        return redirect()->route('student.login')->with('error', 'Invalid credentials');
    }


    public function registerStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|min:6',
            'phone' => 'required|numeric|digits:10|unique:users,phone',
        ]);

        // App currently requires a unique, non-null email column in `users`.
        // For phone-only registration we generate an internal email based on phone.
        $generatedEmail = preg_replace('/\D+/', '', (string) $request->phone) . '@bookhub.local';

        // Determine intended role id (priority: request, then helpers, then role names)
        $roleId = $request->input('role_id');
        if (! $roleId) {
            $roleId = \App\Helpers\RoleHelper::studentId() ?? \App\Helpers\RoleHelper::userId();
        }

        // Resolve Spatie role for permission sync
        $role = null;
        if ($roleId) {
            $role = \Spatie\Permission\Models\Role::find($roleId);
        }
        if (! $role) {
            $role = \Spatie\Permission\Models\Role::whereIn('name', ['student', 'user'])->first();
            if ($role && ! $roleId) {
                $roleId = $role->id;
            }
        }

        // IMPORTANT:
        // Do NOT create the User until OTP is verified.
        // Store pending registration details in session and create the User in Front\UserController@verifyOtp().
        Session::put('pending_registration', [
            'name'           => $request->name,
            'email'          => $generatedEmail,
            'phone'          => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role_id'        => $roleId,
        ]);

        // Generate and Send OTP
        $otp = rand(100000, 999999);
        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['otp' => $otp, 'updated_at' => now()]
        );

        // Send OTP via SMS
        Sms::sendSms($request->phone, $otp);

        // Store phone in session for verification
        Session::put('registration_phone', $request->phone);

        return redirect()->route('user.verify-otp.show')->with('success_message', 'OTP sent to your mobile number. Please verify.');
    }
    public function logout(Request $request)
    {
        // Log out the user from the web guard
        Auth::logout();

        // Invalidate and regenerate session to prevent session fixation
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')->with('success', 'You are logged out');
    }
}
