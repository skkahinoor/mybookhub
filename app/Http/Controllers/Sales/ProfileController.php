<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\SalesExecutive;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $user = Auth::guard('sales')->user(); // Get the User model
        $salesExecutive = $user->salesExecutive; // Get the SalesExecutive profile

        // Fetch countries for dropdown
        $countries = Country::where('status', true)->get()->toArray();

        // Get current location IDs from User model
        $currentCountryId = $user->country_id;
        $currentStateId = $user->state_id;
        $currentDistrictId = $user->district_id;
        $currentBlockId = $user->block_id;

        return view('sales.profile', compact('user', 'salesExecutive', 'logos', 'headerLogo', 'countries', 'currentCountryId', 'currentStateId', 'currentDistrictId', 'currentBlockId'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('sales')->user(); // This is the User model
        $salesExecutive = $user->salesExecutive; // Get the SalesExecutive profile
        
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        
        // Check if name/email fields are present in the request
        $hasNameEmail = $request->has('name') || $request->has('email');

        // Build validation rules
        $rules = [];

        // Name and email - only required if they're present in the request
        if ($hasNameEmail) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255', 'unique:users,email,' . $user->id];
        } else {
            // Make name/email optional when not being updated (e.g., bank details only)
            $rules['name'] = ['nullable', 'string', 'max:255'];
            $rules['email'] = ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id];
        }

        $rules = array_merge($rules, [
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'block_id' => ['nullable', 'exists:blocks,id'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:50'],
            'bank_branch' => ['nullable', 'string', 'max:150'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'confirmed', 'min:6'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated = $request->validate($rules);

        // Update User model fields
        if (isset($validated['name']) && $request->filled('name')) {
            $user->name = $validated['name'];
        }
        if (isset($validated['email']) && $request->filled('email')) {
            $user->email = $validated['email'];
        }
        if (isset($validated['phone']) && $request->filled('phone')) {
            $user->phone = $validated['phone'];
        }
        if (isset($validated['address']) && $request->filled('address')) {
            $user->address = $validated['address'];
        }
        if (isset($validated['pincode']) && $request->filled('pincode')) {
            $user->pincode = $validated['pincode'];
        }

        // Update location fields as IDs in User model
        if ($request->filled('country_id')) {
            $user->country_id = $validated['country_id'];
        }
        if ($request->filled('state_id')) {
            $user->state_id = $validated['state_id'];
        }
        if ($request->filled('district_id')) {
            $user->district_id = $validated['district_id'];
        }
        if ($request->filled('block_id')) {
            $user->block_id = $validated['block_id'];
        }

        // Handle password update
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            if (!empty($user->profile_image)) {
                if (Str::startsWith($user->profile_image, 'assets/')) {
                    File::delete(public_path($user->profile_image));
                } else {
                    Storage::disk('public')->delete($user->profile_image);
                }
            }

            $destinationPath = public_path('assets/sales/profile_pictures');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $filename = Str::uuid()->toString() . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $request->file('profile_picture')->move($destinationPath, $filename);
            $user->profile_image = 'assets/sales/profile_pictures/' . $filename;
        }

        $user->save();

        // Update SalesExecutive model (bank details only)
        if ($salesExecutive) {
            if ($request->has('bank_name')) {
                $salesExecutive->bank_name = $validated['bank_name'] ?: null;
            }
            if ($request->has('account_number')) {
                $salesExecutive->account_number = $validated['account_number'] ?: null;
            }
            if ($request->has('ifsc_code')) {
                $salesExecutive->ifsc_code = $validated['ifsc_code'] ?: null;
            }
            if ($request->has('bank_branch')) {
                $salesExecutive->bank_branch = $validated['bank_branch'] ?: null;
            }
            if ($request->has('upi_id')) {
                $salesExecutive->upi_id = $validated['upi_id'] ?: null;
            }
            
            $salesExecutive->save();
        }

        return redirect()->route('sales.profile.edit')->with('success_message', 'Profile updated successfully.');
    }

    // AJAX methods for cascading location dropdowns
    public function getStates(Request $request)
    {
        $countryId = $request->input('country');

        $states = State::where('country_id', $countryId)
            ->where('status', true)
            ->pluck('name', 'id')
            ->toArray();

        return response()->json($states);
    }

    public function getDistricts(Request $request)
    {
        $stateId = $request->input('state');

        $districts = District::where('state_id', $stateId)
            ->where('status', true)
            ->pluck('name', 'id')
            ->toArray();

        return response()->json($districts);
    }

    public function getBlocks(Request $request)
    {
        $districtId = $request->input('district');

        $blocks = Block::where('district_id', $districtId)
            ->where('status', true)
            ->pluck('name', 'id')
            ->toArray();

        return response()->json($blocks);
    }
}


