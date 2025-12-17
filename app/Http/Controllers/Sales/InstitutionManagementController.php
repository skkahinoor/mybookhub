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
        return view('sales.institution_managements.create', compact('logos', 'headerLogo'));
    }

    public function store(Request $request)
    {

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $data = $request->validate([
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

        ]);

        $data['block_id'] = $this->prepareBlockId($data['block_id'] ?? null, $data['district_id'] ?? null);
        $data['status']   = 0;
        $salesExecutive = Auth::guard('sales')->user();
        $data['added_by'] = $salesExecutive->id;

        $institution = InstitutionManagement::create($data);

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
        return view('sales.institution_managements.index', compact('logos', 'headerLogo'));
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
        if (! $institution) {
            return response()->json(['error' => 'Institution not found'], 404);
        }

        return view('sales.institution_managements.edit')->with(compact('institution', 'logos', 'headerLogo'));
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
            // 'city_id' => 'required|string|max:255',
            'state_id'       => 'required|string|max:255',
            'pincode'        => 'required|string|max:10',
            'country_id'     => 'required|string|max:255',
            // 'status' => 'boolean',
        ];

        // Only require classes array if type is school
        if ($request->input('type') === 'school') {
            $validationRules['classes']              = 'required|array|min:1';
            $validationRules['classes.*.class_name'] = 'required|string|max:255';
            $validationRules['classes.*.strength']   = 'required|integer|min:1';
        }

        $data = $request->validate($validationRules);
        $data['block_id'] = $this->prepareBlockId($data['block_id'] ?? null, $data['district_id'] ?? null);

        $institution = InstitutionManagement::findOrFail($id);

        // $data['status'] = $request->status;

        $institution->update($data);

        // Handle institution classes
        if ($request->has('classes') && is_array($request->classes)) {
            // Delete existing classes
            $institution->institutionClasses()->delete();

            // Add new classes
            foreach ($request->classes as $classData) {
                if (! empty($classData['class_name']) && ! empty($classData['strength'])) {
                    $institution->institutionClasses()->create([
                        'class_name'     => $classData['class_name'],
                        'total_strength' => $classData['strength'],
                    ]);
                }
            }
        }

        return redirect('sales/institution-managements')->with('success_message', 'Institution has been updated successfully');
        return view('sales/institution-managements', compact('logos', 'headerLogo'));
    }

    public function destroy($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $institution = InstitutionManagement::findOrFail($id);
        $institution->delete();

        return redirect('sales/institution-managements')->with('success_message', 'Institution has been deleted successfully', compact('logos', 'headerLogo'));
    }

    public function getClasses(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'school') {
            $classes = [
                'Nursery',
                'LKG',
                'UKG',
                'Class 1',
                'Class 2',
                'Class 3',
                'Class 4',
                'Class 5',
                'Class 6',
                'Class 7',
                'Class 8',
                'Class 9',
                'Class 10',
                'Class 11',
                'Class 12',
            ];
        } else {
            $classes = [];
        }

        return response()->json($classes);
    }

    public function getLocationData(Request $request)
    {
        $pincode = $request->input('pincode');

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
