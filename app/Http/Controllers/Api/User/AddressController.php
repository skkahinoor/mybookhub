<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Get all addresses for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $addresses = UserAddress::with(['country', 'state', 'district', 'block'])
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Addresses fetched successfully',
            'data' => $addresses
        ]);
    }

    /**
     * Store a newly created address.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'mobile' => 'required|string|max:20',
            'pincode' => 'required|string|max:10',
            'country_id' => 'nullable|string',
            'state_id' => 'nullable|string',
            'district_id' => 'nullable|string',
            'block_id' => 'nullable|string',
            'is_default' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // If this is set as default, unset other defaults
        if ($request->is_default) {
            UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
        }

        // If this is the first address, make it default
        $isFirst = UserAddress::where('user_id', $user->id)->count() === 0;

        $address = UserAddress::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'mobile' => $request->mobile,
            'pincode' => $request->pincode,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'block_id' => $request->block_id,
            'is_default' => ($request->is_default || $isFirst) ? 1 : 0,
            'status' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address added successfully',
            'data' => $address
        ], 201);
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, $id)
    {
        $address = UserAddress::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'mobile' => 'sometimes|string|max:20',
            'pincode' => 'sometimes|string|max:10',
            'country_id' => 'nullable|string',
            'state_id' => 'nullable|string',
            'district_id' => 'nullable|string',
            'block_id' => 'nullable|string',
            'is_default' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('is_default') && $request->is_default) {
            UserAddress::where('user_id', $request->user()->id)->update(['is_default' => 0]);
        }

        $address->update($request->only([
            'name', 'address', 'mobile', 'pincode', 'country_id', 'state_id', 'district_id', 'block_id', 'is_default'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    /**
     * Display the specified address.
     */
    public function show(Request $request, $id)
    {
        $address = UserAddress::with(['country', 'state', 'district', 'block'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Address fetched successfully',
            'data' => $address
        ]);
    }

    /**
     * Set an address as default.
     */
    public function setDefault(Request $request, $id)
    {
        $user = $request->user();
        
        $address = UserAddress::where('id', $id)->where('user_id', $user->id)->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
        $address->update(['is_default' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Default address updated'
        ]);
    }

    /**
     * Remove the specified address.
     */
    public function destroy(Request $request, $id)
    {
        $address = UserAddress::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully'
        ]);
    }
}
