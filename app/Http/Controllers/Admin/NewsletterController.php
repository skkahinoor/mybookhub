<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Auth;

class NewsletterController extends Controller
{
    // Render admin/subscribers/subscribers.blade.php page (Show all Newsletter subscribers in the Admin Panel)
    public function subscribers()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Highlight the 'Subscribers' tab in the 'Users Management' module in the Sidebar (admin/layout/sidebar.blade.php) on the left in the Admin Panel. Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'subscribers');


        $subscribers = NewsletterSubscriber::get()->toArray();
        // dd($subscribers);
        $adminType = Auth::guard('admin')->user()->type;

        return view('admin.subscribers.subscribers')->with(compact('subscribers', 'logos', 'headerLogo', 'adminType'));
    }

    // Update Subscriber Status (active/inactive) via AJAX in admin/subscribers/subscribers.blade.php, check admin/js/custom.js
    public function updateSubscriberStatus(Request $request)
    {
        if ($request->ajax()) {

            $status = ($request->status == 'Active') ? 0 : 1;

            NewsletterSubscriber::where('id', $request->subscriber_id)
                ->update(['status' => $status]);

            return response()->json([
                'status' => $status,
                'subscriber_id' => $request->subscriber_id
            ]);
        }
    }


    // Delete a Subscriber via AJAX in admin/subscribers/subscribers.blade.php, check admin/js/custom.js
    public function deleteSubscriber($id)
    {
        NewsletterSubscriber::findOrFail($id)->delete();

        return redirect()->back()
            ->with('success_message', 'Subscriber deleted successfully!');
    }


    // Export subscribers (`newsletter_subscribers` database table) as an Excel file using Maatwebsite/Laravel Excel Package in admin/subscribers/subscribers.blade.php
    // Note: For creating/naming of the table headings i.e. the column names of the `newsletter_subscribers` table, check headings() method in app/Exports/subscribersExport
    public function exportSubscribers()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\subscribersExport, 'subscribers.xlsx'); //    'subscribers.xlsx'    is the exported Excel file name
    }
}
