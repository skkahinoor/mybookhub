<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\User;
use App\Models\HeaderLogo;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Render admin/users/users.blade.php page in the Admin Panel
    public function users() {
        if (!Auth::guard('admin')->user()->can('view_users')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'users');

        // Get all users (excluding admins) and load relationships
        $users = User::with(['country', 'state', 'district', 'block'])
            ->where(function($query) {
                $query->whereNull('role_id')
                      ->orWhere('role_id', '!=', 1);
            })
            ->get()
            ->map(function($user) {
                // Transform data to match view expectations
                return [
                    'id' => $user->id,
                    'name' => $user->name ?? '',
                    'address' => $user->address ?? '',
                    'city' => $user->district ? $user->district->name : '', // Using district as city
                    'state' => $user->state ? $user->state->name : '',
                    'country' => $user->country ? $user->country->name : '',
                    'pincode' => $user->pincode ?? '',
                    'mobile' => $user->phone ?? '', // phone field mapped to mobile for view
                    'email' => $user->email ?? '',
                    'status' => $user->status ?? 0,
                ];
            })
            ->toArray();
        $adminType = Auth::guard('admin')->user()->type;

        return view('admin.users.users')->with(compact('users', 'logos', 'headerLogo', 'adminType'));
    }



    // Update User Status (active/inactive) via AJAX in admin/users/users.blade.php, check admin/js/custom.js
    public function updateUserStatus(Request $request) {
        if (!Auth::guard('admin')->user()->can('update_users_status')) {
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
                'status'  => $status,
                'user_id' => $data['user_id']
            ]);
        }
        return view('admin.users.users', compact('users', 'logos', 'headerLogo'));
    }
}
