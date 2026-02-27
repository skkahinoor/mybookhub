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
use App\Models\InstitutionClass;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class StudentApiController extends Controller
{

    public function getBoards($institution_id)
    {
        $classes = InstitutionClass::with('subcategory.category')
            ->where('institution_id', $institution_id)
            ->get();

        $boards = collect();

        foreach ($classes as $class) {
            if ($class->subcategory && $class->subcategory->category) {

                if (!$boards->contains('id', $class->subcategory->category->id)) {
                    $boards->push([
                        'id' => $class->subcategory->category->id,
                        'name' => $class->subcategory->category->category_name
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'data' => $boards->values()
        ]);
    }

    public function getClasses(Request $request)
    {
        $institution_id = $request->institution_id;
        $board_id = $request->board_id;

        $classes = InstitutionClass::with('subcategory')
            ->where('institution_id', $institution_id)
            ->whereHas('subcategory', function ($q) use ($board_id) {
                $q->where('category_id', $board_id);
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->subcategory->subcategory_name ?? null,
                    'strength' => $item->total_strength
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $classes
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
        $type = $this->detectUserType($user);

        if (!in_array($type, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only admin or Sales can view students.'
            ], 403);
        }

        if ($type === 'admin') {
            $students = User::with('institution', 'institutionClass')->orderBy('id', 'desc')->get();
        } else {
            $students = User::with('institution', 'institutionClass')
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

        if (!in_array($type, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied!'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'father_names' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'required|string|min:10|max:15|unique:users,phone',
            'institution_id' => 'required|exists:institution_managements,id',
            'institution_classes_id' => 'required|exists:institution_classes,id',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:today',
            'roll_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users')
                    ->where('institution_id', $request->institution_id)
            ],
        ]);

        // Check institution active
        $institution = InstitutionManagement::find($validated['institution_id']);

        if (!$institution || $institution->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Institution inactive.'
            ], 403);
        }

        // Ensure selected class belongs to selected institution
        $classExists = InstitutionClass::where('id', $validated['institution_classes_id'])
            ->where('institution_id', $validated['institution_id'])
            ->exists();

        if (!$classExists) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid class selection.'
            ], 422);
        }

        $role = \Spatie\Permission\Models\Role::where('name', 'student')->first();

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Student role not found.'
            ], 500);
        }

        $validated['role_id'] = $role->id;
        $validated['status'] = ($type === 'admin') ? 1 : 0;
        $validated['added_by'] = $user->id;
        $validated['password'] = Hash::make('123456');

        $student = User::create($validated);
        $student->assignRole($role);

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
            'data' => $student->load(['institution', 'institutionClass'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $type = strtolower($this->detectUserType($user));

        if (!in_array($type, ['admin', 'sales'])) {
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
                Rule::unique('users')->ignore($student->id)
            ],
            'phone' => [
                'required',
                Rule::unique('users')->ignore($student->id)
            ],
            'institution_id' => 'required|exists:institution_managements,id',
            'institution_classes_id' => 'required|exists:institution_classes,id',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:today',
            'roll_number' => Rule::unique('users')
                ->where('institution_id', $request->institution_id)
                ->ignore($student->id),
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Validate class belongs to institution
        $classExists = InstitutionClass::where('id', $validated['institution_classes_id'])
            ->where('institution_id', $validated['institution_id'])
            ->exists();

        if (!$classExists) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid class selection.'
            ], 422);
        }

        // IMAGE UPLOAD
        if ($request->hasFile('profile_image')) {

            $oldPath = public_path('assets/sales/profile_pictures/' . $student->profile_image);

            if ($student->profile_image && file_exists($oldPath)) {
                unlink($oldPath);
            }

            $file = $request->file('profile_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/sales/profile_pictures'), $filename);

            $validated['profile_image'] = $filename;
        }

        if ($type !== 'admin') {
            unset($validated['status']);
        }

        $student->update($validated);

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' updated student successfully.',
            'data' => $student->load(['institution', 'institutionClass'])
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $type = $this->detectUserType($user);


        if (!in_array($type, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only admin or Sales can delete students.'
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

        if (!in_array($type, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied!'
            ], 403);
        }

        $query = User::with(['institution', 'institutionClass.subcategory']);

        if ($type === 'sales') {
            $query->where('added_by', $user->id);
        }

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->filled('institution_classes_id')) {
            $query->where('institution_classes_id', $request->institution_classes_id);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('roll_number')) {
            $query->where('roll_number', $request->roll_number);
        }

        $students = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'count' => $students->count(),
            'data' => $students
        ]);
    }
}
