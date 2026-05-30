<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReferSettingsController extends Controller
{
    /**
     * Show refer settings management page
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
        Session::put('page', 'refer_settings');

        // Get current setting
        $referralAmount = Setting::getValue('referral_amount', 50);

        return view('admin.refer_settings.index', [
            'referralAmount' => $referralAmount,
            'logos'          => $logos,
            'headerLogo'     => $headerLogo,
        ]);
    }

    /**
     * Update refer settings
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
            'referral_amount' => 'required|numeric|min:0',
        ]);

        Setting::setValue('referral_amount', $request->referral_amount);

        return redirect()->route('admin.refer.settings')
            ->with('success_message', 'Referral settings updated successfully.');
    }
}
