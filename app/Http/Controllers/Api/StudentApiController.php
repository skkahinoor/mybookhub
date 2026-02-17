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
        if (!$user) {
            return null;
        }

        // Fetch role using role_id → roles table
        $role = \Spatie\Permission\Models\Role::find($user->role_id);

        if (!$role) {
            return null;
        }

        // Preserve old behavior
        if ($role->name === 'superadmin') {
            return 'superadmin';
        }

        if ($role->name === 'sales') {
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
            $students = User::with('institution')->orderBy('id', 'desc')->get();
        } else {
            $students = User::with('institution')
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'father_names' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'required|string|min:10|max:15|unique:users,phone',
            'institution_id' => 'required|exists:institution_managements,id',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date|before:today',
            'roll_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'roll_number')
                    ->where('institution_id', $request->institution_id)
            ],
        ]);

        $institution = InstitutionManagement::find($validated['institution_id']);

        if (!$institution || $institution->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'This institution is inactive.'
            ], 403);
        }

        if ($institution->type === 'school') {
            $request->validate([
                'class' => 'required|string|max:255',
            ]);
            $validated['class'] = $request->class;
        } else {
            $request->validate([
                'branch' => 'required|string|max:255',
            ]);
            $validated['class'] = $request->branch;
        }

        $validated['role_id'] = 5; // student role
        $validated['status']   = ($type === 'superadmin') ? 1 : 0;
        $validated['added_by'] = $user->id;
        $validated['password'] = Hash::make('12345678');

        $student = User::create($validated);

        Notification::create([
            'type' => 'student_added',
            'title' => 'New Student Added',
            'message' => "User '{$user->name}' added student '{$student->name}'.",
            'related_id' => $student->id,
            'related_type' => User::class,
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
        $type = strtolower($this->detectUserType($user));

        if (!in_array($type, ['superadmin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied!'
            ], 403);
        }

        $student = User::find($id);
        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found.'
            ], 404);
        }

        if ($type === 'sales' && $student->added_by !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You can update only your students.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'father_names' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student->id)
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                Rule::unique('users', 'phone')->ignore($student->id)
            ],
            'institution_id' => 'required|exists:institution_managements,id',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date|before:today',
            'roll_number' => Rule::unique('users')
                ->where('institution_id', $request->institution_id)
                ->ignore($student->id),
        ]);

        $institution = InstitutionManagement::find($validated['institution_id']);
        if (!$institution || $institution->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Institution inactive.'
            ], 403);
        }

        // Conditional mapping
        if ($institution->type === 'school') {
            $request->validate(['class' => 'required|string|max:255']);
            $validated['class'] = $request->class;
        } else {
            $request->validate(['branch' => 'required|string|max:255']);
            $validated['class'] = $request->branch;
        }

        if ($type !== 'superadmin') {
            unset($validated['status']);
        }

        $student->update($validated);

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' updated student successfully.',
            'data' => $student
        ]);
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

        $users = User::find($id);

        if (!$users) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found.'
            ], 404);
        }


        if ($type === 'sales' && $users->added_by !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! You can only delete students added by you.'
            ], 403);
        }

        $users->delete();

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

        $query = User::query();

        if ($type === 'superadmin') {
        } elseif ($type === 'sales') {
            $query->where('added_by', $user->id);
        }

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
