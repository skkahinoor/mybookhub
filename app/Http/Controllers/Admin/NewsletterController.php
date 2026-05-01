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
    public function subscribers(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'subscribers');

        if ($request->ajax()) {
            $data = NewsletterSubscriber::query();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return date('F j, Y, g:i a', strtotime($row->created_at));
                })
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $prefix = $adminType === 'vendor' ? 'vendor' : 'admin';
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    return '<a class="updateSubscriberStatus" id="subscriber-' . $row->id . '"
                                subscriber_id="' . $row->id . '"
                                data-url="' . route($prefix . '.updatesubscriberstatus') . '"
                                href="javascript:void(0)">
                                <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                    status="' . $statusText . '"></i>
                            </a>';
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="javascript:void(0)" class="confirmDelete"
                                data-module="subscriber"
                                data-url="' . url('admin/delete-subscriber/' . $row->id) . '">
                                <i style="font-size:25px" class="mdi mdi-file-excel-box"></i>
                            </a>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.subscribers.subscribers')->with(compact('logos', 'headerLogo', 'adminType'));
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
