<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $userDetails = clone $this->getUpdatedUser($user->id);

        return response()->json([
            'status' => true,
            'message' => 'Profile details fetched successfully',
            'data' => $userDetails
        ], 200);
    }

    private function getUpdatedUser($userId)
    {
        return User::with([
            'institution.section',
            'institution.category',
            'institutionClass.subcategory',
            'academicProfile',
            'country',
            'state',
            'district',
            'block',
            'assignedRole',
            'salesExecutive',
            'vendorPersonal',
            'vendorBusiness',
            'vendorBank'
        ])->find($userId);
    }

    public function updateBasicInfo(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'father_names' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userToUpdate = User::find($user->id);

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            
            if ($userToUpdate->profile_image) {
                $oldImagePath = public_path($userToUpdate->profile_image);
                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }

            $timestamp = time();
            $extension = $image->getClientOriginalExtension();
            $fileName = "avatar_{$userToUpdate->id}_{$timestamp}.{$extension}";
            
            $destinationPath = public_path('asset/user');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $image->move($destinationPath, $fileName);
            $userToUpdate->profile_image = 'asset/user/' . $fileName;
        }

        $fields = ['name', 'email', 'phone', 'father_names', 'gender', 'dob'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $userToUpdate->$field = $request->input($field);
            }
        }

        $userToUpdate->save();

        return response()->json([
            'status' => true,
            'message' => 'Basic info updated successfully',
            'data' => clone $this->getUpdatedUser($user->id)
        ], 200);
    }

    public function updateAcademicInfo(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'institution_id' => 'nullable|integer|exists:institution_managements,id',
            'institution_classes_id' => 'nullable|integer|exists:institution_classes,id',
            'roll_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userToUpdate = User::find($user->id);

        $fields = ['institution_id', 'institution_classes_id', 'roll_number'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $userToUpdate->$field = $request->input($field);
            }
        }

        $userToUpdate->save();

        return response()->json([
            'status' => true,
            'message' => 'Academic info updated successfully',
            'data' => clone $this->getUpdatedUser($user->id)
        ], 200);
    }

    public function updateAddress(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'block_id' => 'nullable|integer',
            'address' => 'nullable|string',
            'pincode' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userToUpdate = User::find($user->id);

        $fields = ['country_id', 'state_id', 'district_id', 'block_id', 'address', 'pincode'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $userToUpdate->$field = $request->input($field);
            }
        }

        $userToUpdate->save();

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => clone $this->getUpdatedUser($user->id)
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors() // confirm_password fails will show up here
            ], 422);
        }

        // Check if current password matches
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password does not match',
            ], 400);
        }

        // Update password
        $userToUpdate = User::find($user->id);
        $userToUpdate->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $userToUpdate->save();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ], 200);
    }
    
}
