<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\SalesExecutive;
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
        $sales = Auth::guard('sales')->user();

        // Fetch countries for dropdown
        $countries = Country::where('status', true)->get()->toArray();

        // Try to find current location IDs from stored names
        $currentCountryId = null;
        $currentStateId = null;
        $currentDistrictId = null;
        $currentBlockId = null;

        if ($sales->country) {
            $country = Country::where('name', $sales->country)->where('status', true)->first();
            $currentCountryId = $country ? $country->id : null;
        }

        if ($sales->state && $currentCountryId) {
            $state = State::where('name', $sales->state)
                ->where('country_id', $currentCountryId)
                ->where('status', true)
                ->first();
            $currentStateId = $state ? $state->id : null;
        }

        if ($sales->district && $currentStateId) {
            $district = District::where('name', $sales->district)
                ->where('state_id', $currentStateId)
                ->where('status', true)
                ->first();
            $currentDistrictId = $district ? $district->id : null;
        }

        if ($sales->block && $currentDistrictId) {
            $block = Block::where('name', $sales->block)
                ->where('district_id', $currentDistrictId)
                ->where('status', true)
                ->first();
            $currentBlockId = $block ? $block->id : null;
        }

        return view('sales.profile', compact('sales', 'logos', 'headerLogo', 'countries', 'currentCountryId', 'currentStateId', 'currentDistrictId', 'currentBlockId'));
    }

    public function update(Request $request)
    {
        $sales = Auth::guard('sales')->user();

        // Check if name/email fields are present in the request
        $hasNameEmail = $request->has('name') || $request->has('email');

        // Build validation rules
        $rules = [];

        // Name and email - only required if they're present in the request
        if ($hasNameEmail) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255', 'unique:sales_executives,email,' . $sales->id];
        } else {
            // Make name/email optional when not being updated (e.g., bank details only)
            $rules['name'] = ['nullable', 'string', 'max:255'];
            $rules['email'] = ['nullable', 'email', 'max:255', 'unique:sales_executives,email,' . $sales->id];
        }

        $rules = array_merge($rules, [
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
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

        // Update profile fields only if they're provided
        if (isset($validated['name']) && $request->filled('name')) {
            $sales->name = $validated['name'];
        }
        if (isset($validated['email']) && $request->filled('email')) {
            $sales->email = $validated['email'];
        }
        if (isset($validated['phone']) && $request->filled('phone')) {
            $sales->phone = $validated['phone'];
        }
        if (isset($validated['address']) && $request->filled('address')) {
            $sales->address = $validated['address'];
        }
        if (isset($validated['city']) && $request->filled('city')) {
            $sales->city = $validated['city'];
        }
        if (isset($validated['pincode']) && $request->filled('pincode')) {
            $sales->pincode = $validated['pincode'];
        }

        // Update location fields from IDs
        if ($request->filled('country_id')) {
            $country = Country::find($validated['country_id']);
            $sales->country = $country ? $country->name : null;
        }
        if ($request->filled('state_id')) {
            $state = State::find($validated['state_id']);
            $sales->state = $state ? $state->name : null;
        }
        if ($request->filled('district_id')) {
            $district = District::find($validated['district_id']);
            $sales->district = $district ? $district->name : null;
        }
        if ($request->filled('block_id')) {
            $block = Block::find($validated['block_id']);
            $sales->block = $block ? $block->name : null;
        }

        // Update bank details - always update if present in request
        if ($request->has('bank_name')) {
            $sales->bank_name = $validated['bank_name'] ?: null;
        }
        if ($request->has('account_number')) {
            $sales->account_number = $validated['account_number'] ?: null;
        }
        if ($request->has('ifsc_code')) {
            $sales->ifsc_code = $validated['ifsc_code'] ?: null;
        }
        if ($request->has('bank_branch')) {
            $sales->bank_branch = $validated['bank_branch'] ?: null;
        }
        if ($request->has('upi_id')) {
            $sales->upi_id = $validated['upi_id'] ?: null;
        }

        // Handle password update
        if (!empty($validated['password'])) {
            $sales->password = Hash::make($validated['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            if (!empty($sales->profile_picture)) {
                if (Str::startsWith($sales->profile_picture, 'assets/')) {
                    File::delete(public_path($sales->profile_picture));
                } else {
                    Storage::disk('public')->delete($sales->profile_picture);
                }
            }

            $destinationPath = public_path('assets/sales/profile_pictures');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $filename = Str::uuid()->toString() . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $request->file('profile_picture')->move($destinationPath, $filename);
            $sales->profile_picture = 'assets/sales/profile_pictures/' . $filename;
        }

        $sales->save();

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


