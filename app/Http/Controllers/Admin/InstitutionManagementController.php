<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Category;
use App\Models\Country;
use App\Models\District;
use App\Models\HeaderLogo;
// use App\Models\City;
use App\Models\InstitutionManagement;
use App\Models\Section;
use App\Models\State;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class InstitutionManagementController extends Controller
{
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();
        Session::put('page', 'institution_managements');
        $id           = Auth::guard('admin')->user()->name;
        $institutions = InstitutionManagement::with('institutionClasses')->orderBy('id', 'desc')->get();
        $sections     = Section::where('status', 1)
            ->whereNotIn('name', [
                'Religious Book', 'Religious',
                'Technical Book', 'Technical',
                'Novel & Story Book', 'Novel & Story',
                'Competitive Books', 'Competitive'
            ])->get();

        return view('admin.institution_managements.index')->with(compact('institutions', 'id', 'logos', 'headerLogo', 'sections'));
    }

    public function create()
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();
        Session::put('page', 'institution_managements');
        $id = Auth::guard('admin')->user()->name;
        $sections = Section::where('status', 1)
            ->whereNotIn('name', [
                'Religious Book', 'Religious',
                'Technical Book', 'Technical',
                'Novel & Story Book', 'Novel & Story',
                'Competitive Books', 'Competitive'
            ])->get();
        $categories = Category::where('status', 1)->get();
        $subcategories = Subcategory::where('status', 1)->get();
        return view('admin.institution_managements.create')->with(compact('id', 'logos', 'headerLogo', 'sections', 'categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        $headerLogo      = HeaderLogo::first();
        $logos           = HeaderLogo::first();
        $validationRules = [
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'board'          => 'required|string|max:255',
            'principal_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'country_id'     => 'required|string|max:255',
            'state_id'       => 'required|string|max:255',
            'district_id'    => 'required|string|max:255',
            // 'city_id' => 'required|string|max:255',
            'block_id'       => 'nullable|string|max:255',
            'pincode'        => 'required|string|max:10',
            'status'         => 'boolean',
        ];

        // Automatically require classes if subcategories are selected or type is filled (assuming type is section_id now)
        if ($request->has('classes') && is_array($request->classes)) {
            $validationRules['classes']              = 'required|array|min:1';
            $validationRules['classes.*.sub_category_id'] = 'required|integer';
            $validationRules['classes.*.strength']   = 'required|integer|min:1';
        }

        $data             = $request->validate($validationRules);
        $data['block_id'] = $data['block_id'] ?? null;

        $data['status']   = 1;
        $data['added_by'] = Auth::guard('admin')->user()->id;

        // Store IDs directly as they are already in the correct format

        $institution = InstitutionManagement::create($data);

        // Handle institution classes
        if ($request->has('classes') && is_array($request->classes)) {
            foreach ($request->classes as $classData) {
                if (! empty($classData['sub_category_id']) && ! empty($classData['strength'])) {
                    $institution->institutionClasses()->create([
                        'sub_category_id' => $classData['sub_category_id'],
                        'total_strength'  => $classData['strength'],
                    ]);
                }
            }
        }

        return redirect('admin/institution-managements')->with('success_message', 'Institution has been added successfully', 'logos');
        return view('admin.institution_managements.index', compact('institutions', 'logos', 'headerLogo'));
    }

    public function show($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();
        Session::put('page', 'institution_managements');

        $institution = InstitutionManagement::with(['institutionClasses', 'country', 'state', 'district', 'block'])->findOrFail($id);

        return view('admin.institution_managements.show')->with(compact('institution', 'logos', 'headerLogo'));
    }

    public function edit($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();
        Session::put('page', 'institution_managements');

        $institution = InstitutionManagement::with(['institutionClasses', 'country', 'state', 'district', 'block'])->findOrFail($id);
        $sections = Section::where('status', 1)
            ->whereNotIn('name', [
                'Religious Book', 'Religious',
                'Technical Book', 'Technical',
                'Novel & Story Book', 'Novel & Story',
                'Competitive Books', 'Competitive'
            ])->get();
        $categories = Category::where('status', 1)->get();
        $subcategories = Subcategory::where('status', 1)->get();

        return view('admin.institution_managements.edit')->with(compact('institution', 'logos', 'headerLogo', 'sections', 'categories', 'subcategories'));
    }

    public function update(Request $request, $id)
    {
        $headerLogo      = HeaderLogo::first();
        $logos           = HeaderLogo::first();
        $validationRules = [
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'board'          => 'required|string|max:255',
            'principal_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'district_id'    => 'required|string|max:255',
            'block_id'       => 'nullable|string|max:255',
            // 'city_id' => 'required|string|max:255',
            'state_id'       => 'required|string|max:255',
            'pincode'        => 'required|string|max:10',
            'country_id'     => 'required|string|max:255',
            'status'         => 'boolean',
        ];

        // Automatically require classes if present
        if ($request->has('classes') && is_array($request->classes)) {
            $validationRules['classes']              = 'required|array|min:1';
            $validationRules['classes.*.sub_category_id'] = 'required|integer';
            $validationRules['classes.*.strength']   = 'required|integer|min:1';
        }

        $data             = $request->validate($validationRules);
        $data['block_id'] = $data['block_id'] ?? null;

        $institution = InstitutionManagement::findOrFail($id);

        $data['status'] = $request->status;

        $institution->update($data);

        // Handle institution classes
        if ($request->has('classes') && is_array($request->classes)) {
            // Delete existing classes
            $institution->institutionClasses()->delete();

            // Add new classes
            foreach ($request->classes as $classData) {
                if (! empty($classData['sub_category_id']) && ! empty($classData['strength'])) {
                    $institution->institutionClasses()->create([
                        'sub_category_id' => $classData['sub_category_id'],
                        'total_strength'  => $classData['strength'],
                    ]);
                }
            }
        }

        return redirect('admin/institution-managements')->with('success_message', 'Institution has been updated successfully', 'logos');
        return view('admin.institution_managements.index', compact('institutions', 'logos', 'headerLogo'));
    }

    public function destroy($id)
    {
        $headerLogo  = HeaderLogo::first();
        $logos       = HeaderLogo::first();
        $institution = InstitutionManagement::findOrFail($id);
        $institution->delete();

        return redirect('admin/institution-managements')->with('success_message', 'Institution has been deleted successfully');
        return view('admin.institution_managements.index', compact('institutions', 'logos', 'headerLogo'));
    }

    public function getSections()
    {
        $sections = Section::where('status', 1)
        ->whereNotIn('name', [
            'Religious Book', 'Religious',
            'Technical Book', 'Technical',
            'Novel & Story Book', 'Novel & Story',
            'Competitive Books', 'Competitive'
        ])->get(['id', 'name']);
        return response()->json($sections);
    }

    public function getCategories(Request $request)
    {
        $section_id = $request->input('section_id');
        $categories = Category::where('status', 1);
        if ($section_id) {
            $categories->where('section_id', $section_id);
        }
        return response()->json($categories->get(['id', 'category_name']));
    }

    public function getClasses(Request $request)
    {
        $subcategories = Subcategory::where('status', 1)->get(['id', 'subcategory_name']);
        return response()->json($subcategories);
    }

    public function getLocationData(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();
        $pincode    = $request->input('pincode');

        // Sample location data based on pincode patterns - you can replace this with actual API calls or database queries
        $locationData = [
            'block'    => 'Central Block',
            'district' => 'Sample District',
            // 'city' => 'Sample City',
            'state'    => 'Sample State',
            'country'  => 'India',
        ];

        // You can implement actual location lookup here
        // For example, using a postal code API like India Post API or database lookup
        // For now, we'll provide sample data based on pincode patterns

        if ($pincode) {
            // Simple pincode-based location mapping (you can expand this)
            $pincodeData = [
                '110001' => ['block' => 'New Delhi Block', 'district' => 'New Delhi', 'state' => 'Delhi'],
                '400001' => ['block' => 'Mumbai Block', 'district' => 'Mumbai', 'state' => 'Maharashtra'],
                '560001' => ['block' => 'Bangalore Block', 'district' => 'Bangalore', 'state' => 'Karnataka'],
                '700001' => ['block' => 'Kolkata Block', 'district' => 'Kolkata', 'state' => 'West Bengal'],
                '600001' => ['block' => 'Chennai Block', 'district' => 'Chennai', 'state' => 'Tamil Nadu'],
            ];

            if (isset($pincodeData[$pincode])) {
                $locationData = array_merge($locationData, $pincodeData[$pincode]);
            }
        }

        return response()->json($locationData, 'logos');
        return view('admin.institution_managements.index', compact('institutions', 'logos', 'headerLogo'));
    }

    public function getCountries()
    {
        $countries = Country::where('status', true)
            ->pluck('name', 'id');

        return response()->json($countries);
    }

    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country)
            ->where('status', true)
            ->whereRaw('LOWER(name) = ?', ['odisha'])
            ->pluck('name', 'id');

        return response()->json($states);
    }

    public function getDistricts(Request $request)
    {
        $districts = District::where('state_id', $request->state)
            ->where('status', true)
            ->pluck('name', 'id');

        return response()->json($districts);
    }

    public function getBlocks(Request $request)
    {
        $blocks = Block::where('district_id', $request->district)
            ->where('status', 1)
            ->pluck('name', 'id');

        return response()->json($blocks);
    }


    // protected function prepareBlockId(?string $input, ?int $districtId): ?int
    // {
    //     if (empty($input)) {
    //         return null;
    //     }

    //     $trimmed = trim($input);

    //     if (is_numeric($trimmed)) {
    //         return (int) $trimmed;
    //     }

    //     $query = Block::where('name', $trimmed);
    //     if ($districtId) {
    //         $query->where('district_id', $districtId);
    //     }
    //     $block = $query->first();

    //     if (! $block) {
    //         if (! $districtId) {
    //             throw ValidationException::withMessages([
    //                 'block_id' => 'Please select a district before entering a new block name.',
    //             ]);
    //         }

    //         $block = Block::create([
    //             'name'        => $trimmed,
    //             'district_id' => $districtId,
    //             'status'      => true,
    //         ]);
    //     }

    //     return $block->id;
    // }

    public function updateStatus(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $data = $request->validate([
            'institution_id' => 'required|exists:institution_managements,id',
            'status'         => 'required|in:0,1',
        ]);

        $institution         = InstitutionManagement::findOrFail($data['institution_id']);
        $institution->status = (int) $data['status'];
        $institution->save();

        return response()->json([
            'success'        => true,
            'status'         => (int) $institution->status,
            'institution_id' => $institution->id,
            'message'        => 'Institution status updated successfully.',
        ]);
    }

    /**
     * Get institution details for modal (AJAX)
     */
    public function getDetails($id)
    {
        $institution = InstitutionManagement::with(['country', 'state', 'district', 'block', 'institutionClasses'])
            ->findOrFail($id);

        $classes = $institution->institutionClasses->map(function ($class) {
            return [
                'class_name'     => $class->subcategory ? $class->subcategory->subcategory_name : 'N/A',
                'total_strength' => $class->total_strength,
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $institution->id,
                'name'           => $institution->name,
                'type'           => \App\Models\Section::find($institution->type)->name ?? ucfirst($institution->type),
                'board'          => \App\Models\Category::find($institution->board)->category_name ?? $institution->board,
                'contact_number' => $institution->contact_number,
                'country'        => $institution->country ? $institution->country->name : 'N/A',
                'state'          => $institution->state ? $institution->state->name : 'N/A',
                'district'       => $institution->district ? $institution->district->name : 'N/A',
                'block'          => $institution->block ? $institution->block->name : 'N/A',
                'pincode'        => $institution->pincode,
                'status'         => $institution->status,
                'classes'        => $classes,
                'created_at'     => $institution->created_at->format('M d, Y h:i A'),
            ],
        ]);
    }
}
