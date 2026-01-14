<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PlanSettingsController extends Controller
{
    /**
     * Show plan settings management page
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
        Session::put('page', 'plan_settings');

        // Get current settings
        $proPlanPrice = Setting::getValue('pro_plan_price', 49900);
        $freePlanBookLimit = Setting::getValue('free_plan_book_limit', 100);
        $giveNewUsersProPlan = Setting::getValue('give_new_users_pro_plan', 0);
        $proPlanTrialDurationDays = Setting::getValue('pro_plan_trial_duration_days', 30);
        $inviteProToken = Setting::getValue('invite_pro_token');

        if (!$inviteProToken) {
            $inviteProToken = Str::random(32);
            Setting::setValue('invite_pro_token', $inviteProToken);
        }

        $inviteProLink = url('vendor/register?invite=' . $inviteProToken);

        return view('admin.plan_settings.index', [
            'proPlanPrice' => $proPlanPrice,
            'freePlanBookLimit' => $freePlanBookLimit,
            'giveNewUsersProPlan' => $giveNewUsersProPlan,
            'proPlanTrialDurationDays' => $proPlanTrialDurationDays,
            'inviteProLink' => $inviteProLink,
            'inviteProToken' => $inviteProToken,
            'logos' => $logos,
            'headerLogo' => $headerLogo,
        ]);
    }

    /**
     * Update plan settings
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
            'pro_plan_price' => 'required|numeric|min:1',
            'free_plan_book_limit' => 'required|integer|min:1',
            'give_new_users_pro_plan' => 'nullable|boolean',
            'pro_plan_trial_duration_days' => 'required|integer|min:1|max:365',
        ]);


        Setting::setValue('pro_plan_price', $request->pro_plan_price);
        Setting::setValue('free_plan_book_limit', $request->free_plan_book_limit);
        Setting::setValue('give_new_users_pro_plan', $request->give_new_users_pro_plan ? 1 : 0);
        Setting::setValue('pro_plan_trial_duration_days', $request->pro_plan_trial_duration_days);

        return redirect()->route('admin.plan.settings')
            ->with('success_message', 'Plan settings updated successfully.');
    }

    /**
     * Regenerate invite link token
     */
    public function regenerateInviteLink(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin || !in_array($admin->type, ['superadmin', 'admin'])) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $newToken = Str::random(32);
        Setting::setValue('invite_pro_token', $newToken);

        return redirect()->route('admin.plan.settings')
            ->with('success_message', 'Invite Pro link regenerated.')
            ->with('inviteProLink', url('vendor/register?invite=' . $newToken));
    }
}
