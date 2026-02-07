<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\SalesExecutive;
use App\Models\User;
use App\Models\Country;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SalesExecutiveController extends Controller
{
    public function index()
    {
        if (!Auth::guard('admin')->user()->can('view_sales')) {
            abort(403, 'Unauthorized action.');
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'view_sales');
        $title = 'Sales Executives';
        
        // Fetch Users with role 'sales' from the 'web' guard
        $salesExecutives = User::role('sales', 'web')->with('salesExecutive')->get();
        // Map to structure expected by view if necessary (or update view to use user->salesExecutive relationship)
        // Assuming view uses $sales->name, $sales->email, $sales->status directly on the object iterated.
        // User has name, email, status. So it should be fine.
        // But some fields like 'city', 'total_target' are in profile.

        return view('admin.salesexecutives.index', compact('salesExecutives', 'title', 'logos', 'headerLogo'));
    }

    public function addEdit(Request $request, $id = null)
    {
        Session::put('page', 'view_sales');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        $salesExecutive = null;
        $user = null;
        $isEdit = false;
        
        if (! empty($id)) {
            if (!Auth::guard('admin')->user()->can('edit_sales')) {
                abort(403, 'Unauthorized action.');
            }
            // ID passed is likely User ID or SalesExecutive ID?
            // Since we list Users, the edit link will likely pass User ID.
            // Let's assume ID is User ID.
            $user = User::findOrFail($id);
            $salesExecutive = $user->salesExecutive; // Get profile
            $isEdit = true;
        } else {
            if (!Auth::guard('admin')->user()->can('add_sales')) {
                abort(403, 'Unauthorized action.');
            }
        }

        if ($request->isMethod('post')) {
            $data = $request->all();
            
            // Validate User Email
            $userId = $isEdit ? $user->id : null;
            $emailRule = 'required|email|unique:users,email';
            if ($userId) {
                $emailRule .= ',' . $userId;
            }

            $rules = [
                'name'  => 'required|string|max:255',
                'email' => $emailRule,
                'phone' => 'required|numeric',
            ];

            if (! $isEdit) {
                $rules['password'] = 'required|min:6|confirmed';
            }

            $request->validate($rules);

            // 1. Create/Update User
            $userData = [
                'name'   => $data['name'],
                'email'  => $data['email'],
                'phone'  => $data['phone'],
                'address' => $data['address'] ?? null,
                'country_id' => $data['country_id'] ?? null,
                'state_id' => $data['state_id'] ?? null,
                'district_id' => $data['district_id'] ?? null,
                'block_id' => $data['block_id'] ?? null,
                'pincode' => $data['pincode'] ?? null,
                'status' => 1, // Default active? Or $data['status']
            ];
            


            if (!$isEdit) {
                $userData['password'] = Hash::make($data['password']);
                
                // Dynamic Role Fetching
                $role = Role::where('name', 'sales')->where('guard_name', 'web')->first();
                $userData['role_id'] = $role ? $role->id : 3;

                $user = User::create($userData);
                if ($role) {
                    $user->assignRole($role);
                }
            } else {
                if (!empty($data['password'])) {
                    $userData['password'] = Hash::make($data['password']);
                }
                $user->update($userData);
            }

            // 2. Create/Update SalesExecutive Profile (only bank and target data)
            $profileData = [
                'bank_name' => $data['bank_name'] ?? null,
                'account_number' => $data['account_number'] ?? null,
                'ifsc_code' => $data['ifsc_code'] ?? null,
                'bank_branch' => $data['bank_branch'] ?? null,
                'upi_id' => $data['upi_id'] ?? null,
                'total_target' => $data['total_target'] ?? null,
                'completed_target' => $data['completed_target'] ?? null,
                'income_per_target' => $data['income_per_target'] ?? 10,
                'status' => 1,
                'user_id' => $user->id,
            ];

            // Check if profile exists
            $profile = SalesExecutive::where('user_id', $user->id)->first();
            if ($profile) {
                $profile->update($profileData);
            } else {
                SalesExecutive::create($profileData);
            }

            return redirect()->route('salesexecutives.index')->with('success_message', 'Sales Executive saved successfully!');
        }

        $countries = Country::orderBy('name', 'asc')->get();
        return view('admin.salesexecutives.add_edit', compact('salesExecutive', 'user', 'logos', 'headerLogo', 'countries'));
    }

    public function delete($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_sales')) {
            abort(403, 'Unauthorized action.');
        }

        // ID is User ID
        $user = User::findOrFail($id);
        
        // Delete profile
        SalesExecutive::where('user_id', $id)->delete();
        
        // Delete user
        $user->delete();
        
        return redirect()->back()->with('success_message', 'Sales Executive deleted successfully!');
    }

    public function updateStatus(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('update_sales_status')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }

        if (! $request->ajax()) {
            abort(400, 'Invalid request');
        }

        $data = $request->validate([
            'status'              => 'required|string|in:Active,Inactive',
            'sales_executive_id'  => 'required|integer', // This is User ID now
        ]);

        $newStatus = $data['status'] === 'Active' ? 1 : 0;
        $userId = $data['sales_executive_id'];

        // Update User
        User::where('id', $userId)->update(['status' => $newStatus]);
        
        // Update Profile
        SalesExecutive::where('user_id', $userId)->update(['status' => $newStatus]);

        return response()->json([
            'status'             => $newStatus,
            'sales_executive_id' => $userId,
        ]);
    }

    /**
     * Get sales executive details for modal (AJAX)
     */
    public function getDetails($id)
    {
        // ID is User ID
        $user = User::with('salesExecutive')->findOrFail($id);
        $profile = $user->salesExecutive;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $profile ? $profile->address : '',
                'city' => $profile ? $profile->city : '',
                'district' => $profile ? $profile->district : '',
                'state' => $profile ? $profile->state : '',
                'pincode' => $profile ? $profile->pincode : '',
                'country' => $profile ? $profile->country : '',
                'status' => $user->status,
                'created_at' => $user->created_at->format('M d, Y h:i A'),
            ]
        ]);
    }
}


