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
use App\Models\Section;
use App\Models\Category;
use App\Models\Subcategory;

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

    public function getSection()
    {
        $sections = Section::where('status', 1)
            ->whereNotIn('name', [
                'Religious Book', 'Religious',
                'Technical Book', 'Technical',
                'Novel & Story Book', 'Novel & Story',
                'Competitive Books', 'Competitive'
            ])->get();

        return response()->json([
            'status' => true,
            'message' => 'Sections fetched successfully',
            'data' => $sections
        ]);
    }

    public function getCategoriesBySection($section_id)
    {
        $categories = Category::where('section_id', $section_id)
            ->where('status', 1)
            ->select('id', 'category_name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $categories
        ]);
    }

    public function getSubcategories()
    {
        $subcategories = Subcategory::where('status', 1)->select('id', 'subcategory_name')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Subcategories fetched successfully',
            'data'    => $subcategories
        ]);
    }

    private function detectUserType($user)
    {
        if (!$user) {
            return null;
        }

        // Fetch role using role_id → roles table
        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role) {
            return null;
        }

        // Preserve old behavior
        if ($role->name === 'admin') {
            return 'admin';
        }

        if ($role->name === 'sales') {
            return 'sales';
        }

        return null;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $role = $this->detectUserType($user);

        if (!in_array($role, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied!'
            ], 403);
        }

        $query = InstitutionManagement::with([
            'section:id,name',
            'category:id,category_name',
            'institutionClasses.subcategory:id,subcategory_name',
            'country',
            'state',
            'district',
            'block'
        ])->orderBy('id', 'desc');

        if ($role === 'sales') {
            $query->where('added_by', $user->id);
        }

        $institutions = $query->get();

        return response()->json([
            'status' => true,
            'message' => ucfirst($role) . ' fetched institutions successfully',
            'data' => $institutions,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $role = $this->detectUserType($user);

        if (!in_array($role, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied!'
            ], 403);
        }

        $rules = [
            'name' => 'required|string|max:255',

            // type = section_id
            'type' => 'required|integer',

            // board = category_id
            'board' => 'required|integer',

            'principal_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'district_id' => 'required|integer',
            'block_id' => 'nullable|integer',
            'pincode' => 'required|string|max:10',

            // classes = subcategories
            'classes' => 'required|array|min:1',
            'classes.*.class_name' => 'required|integer|exists:subcategories,id',
            'classes.*.strength' => 'required|integer|min:1',
        ];

        $validated = $request->validate($rules);

        // Create institution (keep same column names)
        $institution = InstitutionManagement::create([
            'name' => $request->name,
            'type' => $request->type,     // section_id
            'board' => $request->board,   // category_id
            'principal_name' => $request->principal_name,
            'contact_number' => $request->contact_number,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'block_id' => $request->block_id,
            'pincode' => $request->pincode,
            'status' => ($role === 'admin') ? 1 : 0,
            'added_by' => $user->id,
        ]);

        // Save subcategories into institution_classes
        foreach ($request->classes as $class) {
            $institution->institutionClasses()->create([
                'sub_category_id' => $class['class_name'], // subcategory_id
                'total_strength' => $class['strength'],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Institution created successfully',
            'data' => $institution->load('institutionClasses'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $institution = InstitutionManagement::find($id);

        if (!$institution) {
            return response()->json([
                'status' => false,
                'message' => 'Institution not found'
            ], 404);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|integer|exists:sections,id',
            'board' => 'required|integer|exists:categories,id',
            'principal_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'district_id' => 'required|integer',
            'block_id' => 'nullable|integer',
            'pincode' => 'required|string|max:10',
            'classes' => 'required|array|min:1',
            'classes.*.class_name' => 'required|integer|exists:subcategories,id',
            'classes.*.strength' => 'required|integer|min:1',
        ];

        $validated = $request->validate($rules);

        $institution->update([
            'name' => $request->name,
            'type' => $request->type,
            'board' => $request->board,
            'principal_name' => $request->principal_name,
            'contact_number' => $request->contact_number,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'block_id' => $request->block_id,
            'pincode' => $request->pincode,
        ]);

        // delete old subcategories
        $institution->institutionClasses()->delete();

        foreach ($request->classes as $class) {
            $institution->institutionClasses()->create([
                'sub_category_id' => $class['class_name'],
                'total_strength' => $class['strength'],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Institution updated successfully',
            'data' => $institution->load('institutionClasses'),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        if (!in_array($type, ['admin', 'sales'])) {
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
