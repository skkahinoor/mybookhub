<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CommissionSettingsController extends Controller
{
    /**
     * Show commission settings management page
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
        Session::put('page', 'commission_settings');

        // Get current settings
        $defaultIncomePerTarget = Setting::getValue('default_income_per_target', 10);
        $defaultIncomePerVendor = Setting::getValue('default_income_per_vendor', 50);
        $defaultIncomePerProVendor = Setting::getValue('default_income_per_pro_vendor', 100);

        return view('admin.commission_settings.index', [
            'defaultIncomePerTarget'    => $defaultIncomePerTarget,
            'defaultIncomePerVendor'    => $defaultIncomePerVendor,
            'defaultIncomePerProVendor' => $defaultIncomePerProVendor,
            'logos'      => $logos,
            'headerLogo' => $headerLogo,
        ]);
    }

    /**
     * Update commission settings
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
            'default_income_per_target'    => 'required|numeric|min:0',
            'default_income_per_vendor'    => 'required|numeric|min:0',
            'default_income_per_pro_vendor' => 'required|numeric|min:0',
        ]);

        Setting::setValue('default_income_per_target',     $request->default_income_per_target);
        Setting::setValue('default_income_per_vendor',     $request->default_income_per_vendor);
        Setting::setValue('default_income_per_pro_vendor', $request->default_income_per_pro_vendor);

        return redirect()->route('admin.commission.settings')
            ->with('success_message', 'Commission settings updated successfully.');
    }
}
