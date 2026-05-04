<?php

namespace App\Http\Controllers\Api\Deliveryagent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DeliveryAgent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DeliveryAgentApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|unique:users,phone',
            'password' => 'required|min:6|confirmed',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'district_id' => 'required|exists:districts,id',
            'block_id' => 'nullable|exists:blocks,id',
            'vehicle_type' => 'nullable|string',
            'license_number' => 'nullable|string',
            'id_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'license_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $role = Role::firstOrCreate(
                ['name' => 'delivery_agent', 'guard_name' => 'web']
            );

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'district_id' => $request->district_id,
                'block_id' => $request->block_id,
                'role_id' => $role->id,
                'status' => 0, // Pending approval
            ]);

            $user->assignRole($role);

            $idProofName = null;
            if ($request->hasFile('id_proof')) {
                $file = $request->file('id_proof');
                $idProofName = 'id_' . time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/delivery_agents/docs'), $idProofName);
            }

            $licenseImageName = null;
            if ($request->hasFile('license_image')) {
                $file = $request->file('license_image');
                $licenseImageName = 'license_' . time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/delivery_agents/docs'), $licenseImageName);
            }

            DeliveryAgent::create([
                'user_id' => $user->id,
                'vehicle_type' => $request->vehicle_type,
                'license_number' => $request->license_number,
                'id_proof' => $idProofName,
                'license_image' => $licenseImageName,
                'status' => 0,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Registration successful! Please wait for admin approval.',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required', // Email or Phone
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $loginInput = $request->login;

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)->first();
        } else {
            $numericLogin = preg_replace('/\D/', '', $loginInput);
            $user = User::where('phone', $numericLogin)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->hasRole('delivery_agent')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Not a delivery agent account.'
            ], 403);
        }

        if ($user->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is pending approval or deactivated.'
            ], 403);
        }

        $token = $user->createToken('delivery-agent-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => $user->load('deliveryAgent')
        ]);
    }

    public function getProfile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()->load('deliveryAgent', 'country', 'state', 'district', 'block')
        ]);
    }

    public function getCountries()
    {
        $countries = \App\Models\Country::where('status', 1)->get(['id', 'name']);
        return response()->json([
            'status' => true,
            'data' => $countries
        ]);
    }

    public function getStates(Request $request)
    {
        $states = \App\Models\State::where('country_id', $request->country_id)
            ->where('status', 1)
            ->get(['id', 'name']);
        return response()->json([
            'status' => true,
            'data' => $states
        ]);
    }

    public function getDistricts(Request $request)
    {
        $districts = \App\Models\District::where('state_id', $request->state_id)
            ->where('status', 1)
            ->get(['id', 'name']);
        return response()->json([
            'status' => true,
            'data' => $districts
        ]);
    }

    public function getBlocks(Request $request)
    {
        $blocks = \App\Models\Block::where('district_id', $request->district_id)
            ->where('status', 1)
            ->get(['id', 'name']);
        return response()->json([
            'status' => true,
            'data' => $blocks
        ]);
    }
}
