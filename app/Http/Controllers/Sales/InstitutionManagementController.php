<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Country;
use App\Models\District;
use App\Models\HeaderLogo;
use App\Models\InstitutionManagement;
use App\Models\State;
use App\Models\Notification;
use App\Models\SalesExecutive;
use App\Models\Section;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class InstitutionManagementController extends Controller
{
    public function index()
    {
        $salesId = Auth::guard('sales')->id();
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $institutions = InstitutionManagement::where('added_by', $salesId)
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('sales.institution_managements.index', compact('institutions', 'logos', 'headerLogo'));
    }

    public function create()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $sections = Section::where('status', 1)->get();
        return view('sales.institution_managements.create', compact('logos', 'headerLogo', 'sections'));
    }

    public function store(Request $request)
    {

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $validationRules = [
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'board'          => 'required|string|max:255',
            'principal_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'country_id'     => 'required|integer',
            'state_id'       => 'required|integer',
            'district_id'    => 'required|integer',
            'block_id'       => 'nullable|string|max:255',
            'pincode'        => 'required|string|max:10',
        ];

        // Automatically require classes if present
        if ($request->has('classes') && is_array($request->classes)) {
            $validationRules['classes']              = 'required|array|min:1';
            $validationRules['classes.*.sub_category_id'] = 'required|integer';
            $validationRules['classes.*.strength']   = 'required|integer|min:1';
        }

        $data = $request->validate($validationRules);

        $data['block_id'] = $this->prepareBlockId($data['block_id'] ?? null, $data['district_id'] ?? null);
        $data['status']   = 0;
        $salesExecutive = Auth::guard('sales')->user();
        $data['added_by'] = $salesExecutive->id;

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

        // Create notification for admin
        Notification::create([
            'type' => 'institution_added',
            'title' => 'New Institution Added',
            'message' => "Sales executive '{$salesExecutive->name}' has added a new institution '{$institution->name}' ({$institution->type}) and is waiting for approval.",
            'related_id' => $institution->id,
            'related_type' => 'App\Models\InstitutionManagement',
            'is_read' => false,
        ]);

        return redirect()->route('sales.institution_managements.index')
            ->with('success_message', 'Institution has been added successfully');
    }

    public function show($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        Session::put('page', 'sales.institution_managements');

        $institution = InstitutionManagement::with(['institutionClasses', 'country', 'state', 'district', 'block'])
            ->findOrFail($id);

        return view('sales.institution_managements.show', compact('institution', 'logos', 'headerLogo'));
    }

    public function edit($id)
    {
        Session::put('page', 'sales.institution_managements');
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $institution = InstitutionManagement::with(['institutionClasses', 'country', 'state', 'district', 'block'])->findOrFail($id);
        $sections = Section::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();
        $subcategories = Subcategory::where('status', 1)->get();

        return view('sales.institution_managements.edit')->with(compact('institution', 'logos', 'headerLogo', 'sections', 'categories', 'subcategories'));
    }

    public function update(Request $request, $id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $validationRules = [
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'board'          => 'required|string|max:255',
            'principal_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'district_id'    => 'required|string|max:255',
            'block_id'       => 'nullable|string|max:255',
            'state_id'       => 'required|string|max:255',
            'pincode'        => 'required|string|max:10',
            'country_id'     => 'required|string|max:255',
        ];

        // Automatically require classes if present
        if ($request->has('classes') && is_array($request->classes)) {
            $validationRules['classes']              = 'required|array|min:1';
            $validationRules['classes.*.sub_category_id'] = 'required|integer';
            $validationRules['classes.*.strength']   = 'required|integer|min:1';
        }

        $data = $request->validate($validationRules);
        $data['block_id'] = $this->prepareBlockId($data['block_id'] ?? null, $data['district_id'] ?? null);

        $institution = InstitutionManagement::findOrFail($id);

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

        return redirect('sales/institution-managements')->with('success_message', 'Institution has been updated successfully');
    }

    public function destroy($id)
    {
        $institution = InstitutionManagement::findOrFail($id);
        $institution->delete();

        return redirect('sales/institution-managements')->with('success_message', 'Institution has been deleted successfully');
    }

    public function getSections()
    {
        $sections = Section::where('status', 1)->get(['id', 'name']);
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
        $category_id = $request->input('category_id');
        $subcategories = Subcategory::where('status', 1);
        if ($category_id) {
            $subcategories->where('category_id', $category_id);
        }
        return response()->json($subcategories->get(['id', 'subcategory_name']));
    }

    public function getLocationData(Request $request)
    {
        $pincode = $request->input('pincode');
        $locationData = [
            'block'    => 'Central Block',
            'district' => 'Sample District',
            'state'    => 'Sample State',
            'country'  => 'India',
        ];

        if ($pincode) {
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

        return response()->json($locationData);
    }

    public function getCountries()
    {
        $countries = Country::where('status', true)
            ->pluck('name', 'id')
            ->toArray();

        return response()->json($countries);
    }

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

    protected function prepareBlockId(?string $input, ?int $districtId): ?int
    {
        if (empty($input)) {
            return null;
        }

        $trimmed = trim($input);

        if (is_numeric($trimmed)) {
            return (int) $trimmed;
        }

        $query = Block::where('name', $trimmed);
        if ($districtId) {
            $query->where('district_id', $districtId);
        }
        $block = $query->first();

        if (! $block) {
            if (! $districtId) {
                throw ValidationException::withMessages([
                    'block_id' => 'Please select a district before entering a new block name.',
                ]);
            }

            $block = Block::create([
                'name'        => $trimmed,
                'district_id' => $districtId,
                'status'      => true,
            ]);
        }

        return $block->id;
    }
}
