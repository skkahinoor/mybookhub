<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:vendors,email|unique:admins,email',
            'mobile'   => 'required|string|min:10|max:15|unique:vendors,mobile|unique:admins,mobile',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vendor = Vendor::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'mobile'  => $request->mobile,
                'status'  => 0,
                'confirm' => 'No',
            ]);

            $admin = Admin::create([
                'name'      => $request->name,
                'type'      => 'vendor',
                'vendor_id' => $vendor->id,
                'email'     => $request->email,
                'mobile'    => $request->mobile,
                'password'  => Hash::make($request->password),
                'status'    => 0,
                'confirm'   => 'No',
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Vendor registered successfully ! plz wait for admin approval',
                'data'    => $vendor,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
