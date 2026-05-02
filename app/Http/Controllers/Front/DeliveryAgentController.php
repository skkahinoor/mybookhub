<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DeliveryAgent;
use App\Models\District;
use App\Models\HeaderLogo;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DeliveryAgentController extends Controller
{
    public function showRegister()
    {
        $logos = HeaderLogo::first();
        $countries = \App\Models\Country::where('status', 1)->get();
        return view('front.delivery_agent.register', compact('logos', 'countries'));
    }

    public function getStates(Request $request)
    {
        $states = \App\Models\State::where('country_id', $request->country_id)
            ->where('status', 1)
            ->get(['id', 'name']);
        return response()->json($states);
    }

    public function getDistricts(Request $request)
    {
        $districts = \App\Models\District::where('state_id', $request->state_id)
            ->where('status', 1)
            ->get(['id', 'name']);
        return response()->json($districts);
    }

    public function getBlocks(Request $request)
    {
        $blocks = \App\Models\Block::where('district_id', $request->district_id)
            ->where('status', 1)
            ->get(['id', 'name']);
        return response()->json($blocks);
    }

    public function register(Request $request)
    {
        $request->validate([
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
        ]);

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

            DeliveryAgent::create([
                'user_id' => $user->id,
                'vehicle_type' => $request->vehicle_type,
                'license_number' => $request->license_number,
                'status' => 0,
            ]);

            DB::commit();
            return redirect()->back()->with('success_message', 'Registration successful! Please wait for admin approval.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error_message', 'Something went wrong. Please try again.');
        }
    }
}
