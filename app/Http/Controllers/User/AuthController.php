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

    public function showForgotPassword()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.auth.forgot-password', compact('logos', 'headerLogo'));
    }
    public function loginStore(Request $request)
    {
        // Student module: phone + password ONLY (no email login)
        $validator = Validator::make($request->all(), [
            'login'    => 'required|digits:10',
            'password' => 'required|min:6',
        ], [
            'login.digits' => 'Mobile number must be 10 digits.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('student.login')
                ->with('error', $validator->errors()->first());
        }

        $phone = preg_replace('/\D+/', '', (string) $request->login);
        $credentials = [
            'phone'    => $phone,
            'password' => (string) $request->password,
            'status'   => 1, // only allow verified/active users
        ];

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

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return redirect()->route('student.forgot-password.form')
                ->withErrors($validator, 'forgotPassword');
        }

        $user = User::where('phone', $request->phone)->first();
        if (! $user) {
            return redirect()->route('student.forgot-password.form')
                ->withErrors(['phone' => 'User not found with this phone number.'], 'forgotPassword');
        }

        $otp = rand(100000, 999999);
        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['otp' => $otp, 'created_at' => now(), 'updated_at' => now()]
        );

        $smsStatus = Sms::sendSms($request->phone, $otp);
        if (! $smsStatus) {
            return redirect()->route('student.forgot-password.form')
                ->withErrors(['phone' => 'Failed to send OTP. Please try again later.'], 'forgotPassword');
        }

        return redirect()->route('student.forgot-password.form')
            ->with('forgot_success', 'OTP sent successfully for password reset.')
            ->with('reset_phone', $request->phone);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:10',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('student.forgot-password.form')
                ->withErrors($validator, 'resetPassword')
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $otpRecord = Otp::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (! $otpRecord) {
            return redirect()->route('student.forgot-password.form')
                ->withErrors(['otp' => 'Invalid OTP.'], 'resetPassword')
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        if (now()->diffInMinutes($otpRecord->created_at) > 10) {
            return redirect()->route('student.forgot-password.form')
                ->withErrors(['otp' => 'OTP has expired.'], 'resetPassword')
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $user = User::where('phone', $request->phone)->first();
        if (! $user) {
            return redirect()->route('student.forgot-password.form')
                ->withErrors(['phone' => 'User not found.'], 'resetPassword');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Otp::where('phone', $request->phone)->delete();

        return redirect()->route('student.login')->with('success', 'Password reset successful. You can now login with your new password.');
    }


    public function registerStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|min:6',
            'phone' => 'required|numeric|digits:10|unique:users,phone',
        ]);

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
            // Student module: no email concept
            'email'          => null,
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
