<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Helpers\RoleHelper;

class UserController extends Controller
{
    // Render admin/users/users.blade.php page in the Admin Panel
    public function users(Request $request)
    {
        if (! Auth::guard('admin')->user()->can('view_users')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $data = User::with(['country', 'state', 'district', 'block'])
                ->where('role_id', RoleHelper::studentId());
            
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('city', function($row) {
                    return $row->district ? $row->district->name : '';
                })
                ->addColumn('state', function($row) {
                    return $row->state ? $row->state->name : '';
                })
                ->addColumn('country', function($row) {
                    return $row->country ? $row->country->name : '';
                })
                ->addColumn('mobile', function($row) {
                    return $row->phone ?? '';
                })
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    if ($adminType === 'vendor') {
                        return '<a class="updateUserStatus" id="user-' . $row->id . '" user_id="' . $row->id . '" data-url="' . route('vendor.updateuserstatus') . '" href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi mdi-bookmark-check" status="Active"></i>
                                </a>';
                    } else {
                        return '<a class="updateUserStatus" id="user-' . $row->id . '" user_id="' . $row->id . '" data-url="' . route('admin.updateuserstatus') . '" href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi ' . $statusIcon . '" status="' . $statusText . '"></i>
                                </a>';
                    }
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'users');

        $adminType = Auth::guard('admin')->user()->type;

        return view('admin.users.users')->with(compact('logos', 'headerLogo', 'adminType'));
    }

    // Update User Status (active/inactive) via AJAX in admin/users/users.blade.php, check admin/js/custom.js
    public function updateUserStatus(Request $request)
    {
        if (! Auth::guard('admin')->user()->can('update_users_status')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) { // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)
            // dd($data);

            if ($data['status'] == 'Active') { // $data['status'] comes from the 'data' object inside the $.ajax() method    // reverse the 'status' from (ative/inactive) 0 to 1 and 1 to 0 (and vice versa)
                $status = 0;
            } else {
                $status = 1;
            }

            User::where('id', $data['user_id'])->update(['status' => $status]); // $data['user_id'] comes from the 'data' object inside the $.ajax() method

            return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                'status' => $status,
                'user_id' => $data['user_id'],
            ]);
        }

        return view('admin.users.users', compact('users', 'logos', 'headerLogo'));
    }
}
