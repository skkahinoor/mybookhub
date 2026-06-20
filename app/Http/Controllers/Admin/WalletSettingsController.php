<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WalletSettingsController extends Controller
{
    /**
     * Show wallet settings management page
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        // Only superadmin and admin can access
        if (!$admin || !in_array($admin->type, ['superadmin', 'admin'])) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        // Set session page for sidebar highlighting
        Session::put('page', 'wallet_settings');

        // Get current setting for signup bonus
        $signupBonus = Setting::getValue('signup_bonus', 100);

        return view('admin.wallet_settings.index', [
            'signupBonus' => $signupBonus,
            'logos'       => $logos,
            'headerLogo'  => $headerLogo,
        ]);
    }

    /**
     * Update wallet settings
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // Only superadmin and admin can access
        if (!$admin || !in_array($admin->type, ['superadmin', 'admin'])) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $request->validate([
            'signup_bonus' => 'required|numeric|min:0',
        ]);

        Setting::setValue('signup_bonus', $request->signup_bonus);

        return redirect()->route('admin.wallet.settings')
            ->with('success_message', 'Wallet settings updated successfully.');
    }
}
