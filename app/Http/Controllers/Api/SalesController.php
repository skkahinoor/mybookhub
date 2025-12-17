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
    public function getProfile(Request $request)
    {
        $sales = $request->user();

        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives can access this profile.'
            ], 403);
        }

        if ($sales->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile fetched successfully.',
            'data' => [
                'id'        => $sales->id,
                'name'      => $sales->name,
                'email'     => $sales->email,
                'phone'     => $sales->phone,
                'address'   => $sales->address,
                'country'   => $sales->country,
                'state'     => $sales->state,
                'district'  => $sales->district,
                'city'      => $sales->city,
                'pincode'   => $sales->pincode,
                'profile_picture' => $sales->profile_picture ? url($sales->profile_picture) : null,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $sales = auth()->user();

        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives can update this profile.'
            ], 403);
        }

        if ($sales->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'name'      => 'required|string|max:255',
                'email'     => ['required', 'email', Rule::unique('sales_executives')->ignore($sales->id)],
                'phone'     => ['required', 'string', 'min:10', 'max:15', Rule::unique('sales_executives')->ignore($sales->id)],
                'address'   => 'nullable|string|max:255',
                'city'      => 'nullable|string|max:255',
                'district'  => 'nullable|string|max:255',
                'state'     => 'nullable|string|max:255',
                'pincode'   => 'nullable|string|max:20',
                'country'   => 'nullable|string|max:255',
                'password'  => 'nullable|min:6|confirmed',
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }


        $sales->fill($validated);

        if (!empty($validated['password'])) {
            $sales->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            $path = 'assets/sales/profile_pictures/';
            $image->move(public_path($path), $filename);

            $sales->profile_picture = $path . $filename;
        }

        $sales->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id'         => $sales->id,
                'name'       => $sales->name,
                'email'      => $sales->email,
                'phone'      => $sales->phone,
                'address'    => $sales->address,
                'city'       => $sales->city,
                'district'   => $sales->district,
                'state'      => $sales->state,
                'pincode'    => $sales->pincode,
                'country'    => $sales->country,
                'profile_picture' => $sales->profile_picture ? url($sales->profile_picture) : null,
            ]
        ], 200);
    }

    public function getBankDetails(Request $request)
    {
        $sales = $request->user();

        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives can access this.'
            ], 403);
        }

        if ($sales->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
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
        $sales = auth()->user();

        // Ensure logged-in user is Sales Executive
        if (!$sales instanceof SalesExecutive) {
            return response()->json([
                'status' => false,
                'message' => 'Only Sales Executives can update bank details.'
            ], 403);
        }

        if ($sales->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        // For PUT + form-data fix
        $request->request->add($request->all());

        $validated = $request->validate([
            'bank_name'      => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:25',
            'ifsc_code'      => 'nullable|string|max:20',
            'bank_branch'    => 'nullable|string|max:255',
            'upi_id'         => 'nullable|string|max:255',
        ]);

        $sales->bank_name      = $validated['bank_name'] ?? $sales->bank_name;
        $sales->account_number = $validated['account_number'] ?? $sales->account_number;
        $sales->ifsc_code      = $validated['ifsc_code'] ?? $sales->ifsc_code;
        $sales->bank_branch    = $validated['bank_branch'] ?? $sales->bank_branch;
        $sales->upi_id         = $validated['upi_id'] ?? $sales->upi_id;

        $sales->save();

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
        ], 200);
    }
}
