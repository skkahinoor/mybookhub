<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Admin;
use App\Models\User;
use App\Models\SalesExecutive;
use Illuminate\Support\Facades\Auth;
use App\Models\InstitutionManagement;
use App\Models\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class StudentApiController extends Controller
{
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
                'message' => 'Access denied! Only Superadmin or Sales can view students.'
            ], 403);
        }

        if ($type === 'superadmin') {
            $students = Student::with('institution')->orderBy('id', 'desc')->get();
        } else {
            $students = Student::with('institution')
                ->where('added_by', $user->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' fetched students successfully.',
            'data' => $students
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $type = strtolower(trim($this->detectUserType($user)));

        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can add students.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:students,email|unique:users,email',
                'phone' => 'required|string|min:10|max:15|unique:students,phone|unique:users,mobile',
                'institution_id' => 'nullable|exists:institution_managements,id',
                'class' => 'required|string|max:255',
                'gender' => 'required|string|in:male,female,other',
                'dob' => 'required|date|before:today',

                // 🔥 Unique roll number for same institution_id
                'roll_number' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('students', 'roll_number')
                        ->where('institution_id', $request->institution_id)
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // Institution must be active
        if (!empty($validated['institution_id'])) {
            $institution = InstitutionManagement::find($validated['institution_id']);
            if ($institution && $institution->status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'This institution is inactive. You cannot add students.'
                ], 403);
            }
        }

        // Status: superadmin=1, sales=0
        $studentStatus = ($type === 'superadmin') ? 1 : 0;
        $validated['status']   = $studentStatus;
        $validated['added_by'] = $user->id;

        // Save student
        $student = Student::create($validated);

        // Create user login entry
        User::create([
            'name'   => $validated['name'],
            'email'  => $validated['email'] ?? null,      // email not required
            'mobile' => $validated['phone'],              // ALWAYS INSERT MOBILE
            'status' => $studentStatus,
            'password' => Hash::make('12345678'),
        ]);

        // Create notification for admin
        Notification::create([
            'type' => 'student_added',
            'title' => 'New Student Added',
            // 'message' => "Sales executive '" . Auth::guard('sales')->user()->name . "' has added a new student '{$validated['name']}' and is waiting for approval.",
            'message' => "User '" . ($user->name ?? 'Unknown') . "' has added a new student '{$validated['name']}' and is waiting for approval.",
            'related_id' => $student->id,
            'related_type' => 'App\Models\Student',
            'is_read' => false,
        ]);
        

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' added student successfully.',
            'data' => $student
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can update students.'
            ], 403);
        }

        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found.'
            ], 404);
        }

        // Sales can edit only their students
        if ($type === 'sales' && $student->added_by !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! You can only update students added by you.'
            ], 403);
        }

        // Get linked user account
        $linkedUserID = User::where('mobile', $student->phone)
            ->orWhere('email', $student->email)
            ->value('id');

        // Validation rules
        $validationRules = [
            'name'         => 'required|string|max:255',
            'father_name'  => 'required|string|max:255', // NEW
            'email'        => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
                Rule::unique('users', 'email')->ignore($linkedUserID)
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                Rule::unique('students', 'phone')->ignore($student->id),
                Rule::unique('users', 'mobile')->ignore($linkedUserID)
            ],
            'institution_id' => 'nullable|exists:institution_managements,id',
            'class'          => 'required|string|max:255',
            'gender'         => 'required|string|in:male,female,other',
            'dob'            => 'required|date|before:today',
            // 'roll_number' => [
            //     'nullable',
            //     'string',
            //     'max:255',
            //     Rule::unique('students', 'roll_number')
            //         ->ignore($student->id)
            //         ->where(function ($q) use ($request) {
            //             return $q->where('institution_id', $request->institution_id);
            //         }),
            // ],
            'roll_number' => Rule::unique('students')
                ->where('institution_id', $request->institution_id)
                ->ignore($student->id),
        ];

        // Superadmin can update status
        if ($type === 'superadmin') {
            $validationRules['status'] = 'boolean';
        }

        $validated = $request->validate($validationRules);

        // Institution must be active
        if (!empty($validated['institution_id'])) {
            $institution = InstitutionManagement::find($validated['institution_id']);
            if ($institution && $institution->status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'This institution is inactive. You cannot update students.'
                ], 403);
            }
        }

        // Sales cannot update status
        if ($type !== 'superadmin') {
            unset($validated['status']);
        }

        // Update student
        $student->update($validated);

        // Update linked user record
        if ($linkedUserID) {
            $userRecord = User::find($linkedUserID);

            if ($userRecord) {
                $userRecord->update([
                    'name'   => $validated['name'],
                    'email'  => $validated['email'] ?? $userRecord->email,
                    'mobile' => $validated['phone'],   // Always update mobile
                    'status' => $validated['status'] ?? $userRecord->status,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' updated student successfully.',
            'data' => $student
        ], 200);
    }




    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);


        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can delete students.'
            ], 403);
        }

        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found.'
            ], 404);
        }


        if ($type === 'sales' && $student->added_by !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! You can only delete students added by you.'
            ], 403);
        }

        $student->delete();

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' deleted student successfully.'
        ], 200);
    }
    public function getStudentByClass(Request $request)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);

        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Superadmin or Sales can view students.'
            ], 403);
        }

        $query = Student::query();

        // 🔐 Superadmin = view all
        if ($type === 'superadmin') {
            // no restrictions
        }
        // 🔐 Sales = only view his own added students
        elseif ($type === 'sales') {
            $query->where('added_by', $user->id);
        }

        // -------------------------------
        // 🔍 Filters
        // -------------------------------

        // Institution filter
        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        // Class filter
        if ($request->filled('class')) {
            $query->where('class', $request->class);
        }

        // Name search
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Roll Number search
        if ($request->filled('roll_number')) {
            $query->where('roll_number', $request->roll_number);
        }

        $students = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Students fetched successfully',
            'count' => $students->count(),
            'data' => $students
        ], 200);
    }
}
