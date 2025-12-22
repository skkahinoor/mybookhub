<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\Block;
use App\Models\Admin;
use App\Models\SalesExecutive;
use App\Models\InstitutionManagement;

class InstitutionController extends Controller
{
    public function getCountries()
    {
        return response()->json(Country::where('status', true)->get());
    }

    public function getStates($country_id)
    {
        return response()->json(State::where('country_id', $country_id)->where('status', true)->get());
    }

    public function getDistricts($state_id)
    {
        return response()->json(District::where('state_id', $state_id)->where('status', true)->get());
    }

    public function getBlocks($district_id)
    {
        return response()->json(Block::where('district_id', $district_id)->where('status', true)->get());
    }

    private function detectUserType($user)
    {
        if ($user instanceof Admin && $user->type === 'superadmin') {
            return 'superadmin';
        } elseif ($user instanceof SalesExecutive) {
            return 'sales';
        }

        return null;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can view institutions.'
            ], 403);
        }

        if ($type === 'superadmin') {
            $institutions = InstitutionManagement::with(['institutionClasses', 'country', 'state', 'district', 'block'])
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $institutions = InstitutionManagement::with(['institutionClasses', 'country', 'state', 'district', 'block'])
                ->where('added_by', $user->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' fetched institutions successfully',
            'data' => $institutions,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can add institutions.'
            ], 403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'board' => 'required|string|max:255',
            'principal_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'country_id' => 'required',
            'state_id' => 'required',
            'district_id' => 'required',
            'block_id' => 'nullable',
            'pincode' => 'required|string|max:10',
        ];

        if ($request->type === 'school') {
            $rules['classes'] = 'required|array|min:1';
            $rules['classes.*.class_name'] = 'required|string|max:255';
            $rules['classes.*.strength'] = 'required|integer|min:1';
        } else {
            $rules['branches'] = 'required|array|min:1';
            $rules['branches.*.branch_name'] = 'required|string|max:255';
            $rules['branches.*.strength'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        $validated['status']   = ($type === 'superadmin') ? 1 : 0;
        $validated['added_by'] = $user->id;

        unset($validated['classes'], $validated['branches']);

        $institution = InstitutionManagement::create($validated);

        if ($request->type === 'school') {
            foreach ($request->classes as $class) {
                $institution->institutionClasses()->create([
                    'class_name' => $class['class_name'],
                    'total_strength' => $class['strength'],
                ]);
            }
        } else {
            foreach ($request->branches as $branch) {
                $institution->institutionClasses()->create([
                    'class_name' => $branch['branch_name'],
                    'total_strength' => $branch['strength'],
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' added institution successfully',
            'data' => $institution->load('institutionClasses'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can update institutions.'
            ], 403);
        }

        $institution = InstitutionManagement::with('institutionClasses')->find($id);
        if (!$institution) {
            return response()->json([
                'status' => false,
                'message' => 'Institution not found'
            ], 404);
        }

        if ($type === 'sales' && $institution->added_by !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You can update only institutions added by you.'
            ], 403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'board' => 'required|string|max:255',
            'principal_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'district_id' => 'required|integer',
            'block_id' => 'nullable|integer',
            'pincode' => 'required|string|max:10',
        ];

        if ($request->type === 'school') {
            $rules['classes'] = 'required|array|min:1';
            $rules['classes.*.class_name'] = 'required|string|max:255';
            $rules['classes.*.strength'] = 'required|integer|min:1';
        } else {
            $rules['branches'] = 'required|array|min:1';
            $rules['branches.*.branch_name'] = 'required|string|max:255';
            $rules['branches.*.strength'] = 'required|integer|min:1';
        }

        if ($type === 'superadmin') {
            $rules['status'] = 'boolean';
        }

        $validated = $request->validate($rules);

        if ($type !== 'superadmin') {
            unset($validated['status']);
        }

        unset($validated['classes'], $validated['branches']);

        $institution->update($validated);

        $institution->institutionClasses()->delete();

        if ($request->type === 'school') {
            foreach ($request->classes as $class) {
                $institution->institutionClasses()->create([
                    'class_name' => $class['class_name'],
                    'total_strength' => $class['strength'],
                ]);
            }
        } else {
            foreach ($request->branches as $branch) {
                $institution->institutionClasses()->create([
                    'class_name' => $branch['branch_name'],
                    'total_strength' => $branch['strength'],
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' updated institution successfully',
            'data' => $institution->load('institutionClasses'),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied!'
            ], 403);
        }

        $institution = InstitutionManagement::find($id);
        if (!$institution) {
            return response()->json([
                'status' => false,
                'message' => 'Institution not found'
            ], 404);
        }

        if ($type === 'sales' && $institution->added_by !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You can delete only institutions added by you.'
            ], 403);
        }

        $institution->institutionClasses()->delete();
        $institution->delete();

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' deleted institution successfully'
        ]);
    }
}
