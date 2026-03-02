<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\AcademicProfile;
use App\Models\HeaderLogo;
use App\Models\InstitutionManagement;
use App\Models\InstitutionClass;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Helpers\RoleHelper;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'students');

        $students = User::where('added_by', Auth::guard('sales')->user()->id)->where('role_id', RoleHelper::studentId())->with('institution')->orderBy('id', 'desc')->get();

        return view('sales.students.index')->with(compact('students', 'logos', 'headerLogo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $salesId = Auth::guard('sales')->id();
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'students');

        $institutions = InstitutionManagement::where('status', 1)->where('added_by', $salesId)->orderBy('name')->get();
        return view('sales.students.create')->with(compact('institutions', 'logos', 'headerLogo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|min:10|max:15',
            'institution_id' => 'required|exists:institution_managements,id',
            'institution_classes_id' => 'required|exists:institution_classes,id',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date|before:today',
            'roll_number' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['status'] = 0;
        $data['added_by'] = Auth::guard('sales')->user()->id;
        $data['password'] = Hash::make('12345678');
        $data['role_id'] = RoleHelper::studentId();
        $user = User::create($data);
        $user->assignRole('student'); // So admin list (User::role('student','web')) shows this user

        // Create academic profile entry for this student
        $institution = InstitutionManagement::find($data['institution_id']);
        AcademicProfile::create([
            'user_id' => $user->id,
            'education_level_id' => $institution?->type,
            'board_id' => $institution?->board,
            'class_id' => $data['institution_classes_id'] ?? null,
        ]);

        // User::create([
        //     'name'     => $data['name'],
        //     'email'    => $data['email'] ?? null,
        //     'mobile'   => $data['phone'],
        //     'password' => Hash::make('12345678'),
        // ]);

        // Create notification for admin
        Notification::create([
            'type' => 'student_added',
            'title' => 'New Student Added',
            'message' => "Sales executive '" . Auth::guard('sales')->user()->name . "' has added a new student '{$data['name']}' and is waiting for approval.",
            'related_id' => $user->id,
            'related_type' => 'App\Models\User',
            'is_read' => false,
        ]);

        return redirect('sales/students')->with('success_message', 'Student has been added successfully');
    }

    /**
     * Display the specified resource.
    */

    public function show(string $id)
    {
        Session::put('page', 'students');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $student = User::where('role_id', RoleHelper::studentId())->findOrFail($id);

        return view('sales.students.show')->with(compact('student', 'logos', 'headerLogo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        Session::put('page', 'students');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $student = User::where('role_id', RoleHelper::studentId())->findOrFail($id);
        $institutions = InstitutionManagement::where('status', 1)->orderBy('name')->get();

        return view('sales.students.edit')->with(compact('student', 'institutions', 'headerLogo', 'logos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|min:10|max:15',
            'institution_id' => 'required|exists:institution_managements,id',
            'institution_classes_id' => 'required|exists:institution_classes,id',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date|before:today',
            'roll_number' => 'nullable|string|max:255',
        ]);

        $student = User::where('role_id', RoleHelper::studentId())->findOrFail($id);
        $data = $request->all();

        $student->update($data);

        // Sync academic profile if institution or class changed
        $institution = InstitutionManagement::find($data['institution_id']);
        $profileData = [
            'education_level_id' => $institution?->type,
            'board_id' => $institution?->board,
            'class_id' => $data['institution_classes_id'] ?? null,
        ];

        if ($student->academicProfile) {
            $student->academicProfile->update($profileData);
        } else {
            $student->academicProfile()->create($profileData);
        }

        return redirect('sales/students')->with('success_message', 'Student has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = User::where('role_id', RoleHelper::studentId())->findOrFail($id);
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $student->delete();

        return redirect('sales/students')->with('success_message', 'Student has been deleted successfully');
    }

    /**
     * Provide the full postal address for an institution so the front-end can derive coordinates.
     */
    // public function getInstitutionAddress(int $institutionId)
    // {
    //     $institution = InstitutionManagement::with(['state', 'district', 'block'])->findOrFail($institutionId);

    //     $addressParts = array_filter([
    //         $institution->name,
    //         optional($institution->block)->name,
    //         optional($institution->district)->name,
    //         optional($institution->state)->name,
    //         $institution->pincode,
    //     ]);

    //     return response()->json([
    //         'address' => implode(', ', $addressParts),
    //     ]);
    // }

    /**
     * Persist the current sales executive location in the session.
     */
    // public function storeUserLocation(Request $request)
    // {
    //     $data = $request->validate([
    //         'latitude'  => 'required|numeric|between:-90,90',
    //         'longitude' => 'required|numeric|between:-180,180',
    //     ]);

    //     Session::put('user_latitude', $data['latitude']);
    //     Session::put('user_longitude', $data['longitude']);

    //     return response()->json(['status' => 'ok']);
    // }

    /**
     * Persist the selected institution coordinates in the session.
     */
    // public function storeInstitutionLocation(Request $request)
    // {
    //     $data = $request->validate([
    //         'institution_id' => 'required|exists:institution_managements,id',
    //         'latitude'       => 'required|numeric|between:-90,90',
    //         'longitude'      => 'required|numeric|between:-180,180',
    //     ]);

    //     Session::put('selected_institution_id', $data['institution_id']);
    //     Session::put('selected_institution_latitude', $data['latitude']);
    //     Session::put('selected_institution_longitude', $data['longitude']);

    //     return response()->json(['status' => 'ok']);
    // }

    /**
     * Return classes/streams for a given institution belonging to the current sales executive.
     */
    public function getInstitutionBoards(Request $request)
    {
        $categories = \App\Models\Category::where('status', 1)->get(['id', 'category_name']);
        return response()->json($categories);
    }

    public function getInstitutionClasses(Request $request)
    {
        $institution_id = $request->input('institution_id');

        if (!$institution_id) {
            return response()->json([]);
        }

        $classes = InstitutionClass::with(['subcategory'])
            ->where('institution_id', $institution_id)
            ->get();

        return response()->json($classes);
    }
}
