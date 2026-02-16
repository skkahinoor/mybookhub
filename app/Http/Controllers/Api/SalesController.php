<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\SalesExecutive;
use Illuminate\Validation\ValidationException;


class SalesController extends Controller
{
    private function checkAccess(Request $request, array $allowedRoles = ['sales'])
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 🔐 Auth check
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // 🔎 Fetch role from roles table
        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role || !in_array($role->name, $allowedRoles)) {
            return response()->json([
                'status'  => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        // 🔒 Status check
        if ($user->status != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }
        return null;
    }

    public function getProfile(Request $request)
    {
        if ($resp = $this->checkAccess($request, ['sales'])) {
            return $resp;
        }

        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => 'Profile fetched successfully.',
            'data' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'address'   => $user->address,
                'country_id' => $user->country_id,
                'state_id'  => $user->state_id,
                'district_id' => $user->district_id,
                'block_id'  => $user->block_id,
                'pincode'   => $user->pincode,
                'profile_image' => $user->profile_image ? url($user->profile_image) : null,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        if ($resp = $this->checkAccess($request, ['sales'])) {
            return $resp;
        }

        $user = auth()->user();

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'     => ['required', Rule::unique('users')->ignore($user->id)],
            'address'   => 'nullable|string|max:255',
            'pincode'   => 'nullable|string|max:20',
            'password'  => 'nullable|min:6|confirmed',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->fill($validated);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = 'assets/sales/profile_pictures/';
            $image->move(public_path($path), $filename);
            $user->profile_image = $path . $filename;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'address'   => $user->address,
                'pincode'   => $user->pincode,
                'profile_image' => $user->profile_image ? url($user->profile_image) : null,
            ]
        ]);
    }

    public function getBankDetails(Request $request)
    {
        if ($resp = $this->checkAccess($request, ['sales'])) {
            return $resp;
        }

        $user = $request->user();
        $sales = $user->salesExecutive;

        if (!$sales) {
            return response()->json([
                'status' => false,
                'message' => 'Sales profile not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Bank details fetched successfully',
            'data' => [
                'bank_name'      => $sales->bank_name,
                'account_number' => $sales->account_number,
                'ifsc_code'      => $sales->ifsc_code,
                'bank_branch'    => $sales->bank_branch,
                'upi_id'         => $sales->upi_id,
            ]
        ]);
    }

    public function updateBankDetails(Request $request)
    {
        if ($resp = $this->checkAccess($request, ['sales'])) {
            return $resp;
        }

        $user = auth()->user();
        $sales = $user->salesExecutive;

        if (!$sales) {
            return response()->json([
                'status' => false,
                'message' => 'Sales profile not found.'
            ], 404);
        }

        $validated = $request->validate([
            'bank_name'      => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:25',
            'ifsc_code'      => 'nullable|string|max:20',
            'bank_branch'    => 'nullable|string|max:255',
            'upi_id'         => 'nullable|string|max:255',
        ]);

        $sales->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Bank details updated successfully',
            'data' => [
                'bank_name'      => $sales->bank_name,
                'account_number' => $sales->account_number,
                'ifsc_code'      => $sales->ifsc_code,
                'bank_branch'    => $sales->bank_branch,
                'upi_id'         => $sales->upi_id,
            ]
        ]);
    }
}
