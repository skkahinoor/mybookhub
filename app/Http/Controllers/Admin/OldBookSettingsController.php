<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OldBookSettingsController extends Controller
{
    /**
     * Show Old Book Settings management page
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        // Only superadmin and admin can access, or check if they have permission
        if (!$admin || (!in_array($admin->type, ['superadmin', 'admin']) && !$admin->can('view_old_book_conditions'))) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        // Set session page for sidebar highlighting
        Session::put('page', 'old_book_settings');

        // Get current setting
        $sellBookConceptEnabled = Setting::getValue('sell_book_concept_enabled', 1);

        return view('admin.old_book_settings.index', [
            'sellBookConceptEnabled' => $sellBookConceptEnabled,
            'logos'                  => $logos,
            'headerLogo'             => $headerLogo,
        ]);
    }

    /**
     * Update Old Book settings
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // Only superadmin and admin can access, or check if they have permission
        if (!$admin || (!in_array($admin->type, ['superadmin', 'admin']) && !$admin->can('view_old_book_conditions'))) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $request->validate([
            'sell_book_concept_enabled' => 'required|in:0,1',
        ]);

        Setting::setValue('sell_book_concept_enabled', $request->sell_book_concept_enabled);

        return redirect()->route('admin.old-book-settings.index')
            ->with('success_message', 'Old Book settings updated successfully.');
    }
}
