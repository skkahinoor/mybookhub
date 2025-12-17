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

    // ✅ GET all institutions
    public function index(Request $request)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        // ✅ Allow only Superadmin or Sales
        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can view institutions.'
            ], 403);
        }

        // ✅ Superadmin: can view all institutions
        if ($type === 'superadmin') {
            $institutions = InstitutionManagement::with(['institutionClasses', 'country', 'state', 'district', 'block'])
                ->orderBy('id', 'desc')
                ->get();
        } else {
            // ✅ Sales: can only view institutions they created
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


    // ✅ ADD institution
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

        $validationRules = [
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
            'status' => 'boolean',
        ];

        if ($request->input('type') === 'school') {
            $validationRules['classes'] = 'required|array|min:1';
            $validationRules['classes.*.class_name'] = 'required|string|max:255';
            $validationRules['classes.*.strength'] = 'required|integer|min:1';
        }

        $request->validate($validationRules);

        $data = $request->all();
        $types = $this->detectUserType($user);
        $data['status'] = ($types === 'superadmin') ? 1 : 0;
        $data['added_by'] = $user->id;

        $institution = InstitutionManagement::create($data);

        // Create notification for admin if added by sales executive
        if ($types === 'sales') {
            \App\Models\Notification::create([
                'type' => 'institution_added',
                'title' => 'New Institution Added',
                'message' => "Sales executive '{$user->name}' has added a new institution '{$institution->name}' ({$institution->type}) and is waiting for approval.",
                'related_id' => $institution->id,
                'related_type' => 'App\Models\InstitutionManagement',
                'is_read' => false,
            ]);
        }

        if ($request->has('classes') && is_array($request->classes)) {
            foreach ($request->classes as $classData) {
                if (!empty($classData['class_name']) && !empty($classData['strength'])) {
                    $institution->institutionClasses()->create([
                        'class_name' => $classData['class_name'],
                        'total_strength' => $classData['strength'],
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' added institution successfully',
            'data' => $institution->load('institutionClasses'),
        ]);
    }

    // ✅ UPDATE institution
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        // ✅ Restrict access
        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can update institutions.'
            ], 403);
        }

        // ✅ Find institution
        $institution = InstitutionManagement::with('institutionClasses')->find($id);
        if (!$institution) {
            return response()->json([
                'status' => false,
                'message' => 'Institution not found'
            ], 404);
        }

        // ✅ Restrict sales to only their own institutions
        if ($type === 'sales' && $institution->added_by !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! You can only update institutions added by you.'
            ], 403);
        }

        // ✅ Validation rules
        $validationRules = [
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

        // ✅ If type = school, validate classes
        if ($request->input('type') === 'school') {
            $validationRules['classes'] = 'required|array|min:1';
            $validationRules['classes.*.id'] = 'nullable|integer|exists:institution_classes,id';
            $validationRules['classes.*.class_name'] = 'required|string|max:255';
            $validationRules['classes.*.strength'] = 'required|integer|min:1';
        }


        if ($type === 'superadmin') {
            $validationRules['status'] = 'boolean';
        }

        $validated = $request->validate($validationRules);
        $updateData = $validated;

        if ($type !== 'superadmin') {
            unset($updateData['status']);
        }


        $institution->update($updateData);


        if ($request->has('classes')) {
            $institution->institutionClasses()->delete();

            foreach ($request->classes as $class) {
                $institution->institutionClasses()->create([
                    'class_name' => $class['class_name'],
                    'total_strength' => $class['strength']
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' updated institution successfully',
            'data' => $institution->load('institutionClasses'),
        ]);
    }


    // ✅ DELETE institution
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        // ✅ Restrict access
        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can delete institutions.'
            ], 403);
        }

        // ✅ Find institution
        $institution = InstitutionManagement::find($id);

        if (!$institution) {
            return response()->json([
                'status' => false,
                'message' => 'Institution not found'
            ], 404);
        }

        // ✅ Sales can delete only their own institutions
        if ($type === 'sales' && $institution->added_by !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! You can only delete institutions added by you.'
            ], 403);
        }

        // ✅ Delete institution and its related classes
        $institution->institutionClasses()->delete();
        $institution->delete();

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' deleted institution successfully'
        ]);
    }
}
