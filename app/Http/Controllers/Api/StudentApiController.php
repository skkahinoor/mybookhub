<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InstitutionManagement;
use App\Models\Notification;
use App\Models\InstitutionClass;
use App\Models\FilterClassSubject;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Helpers\RoleHelper;

class StudentApiController extends Controller
{

    public function getBoards($institution_id)
    {
        // Get unique sub_category_ids from InstitutionClass for this institution
        $subCategoryIds = InstitutionClass::where('institution_id', $institution_id)
            ->distinct()
            ->pluck('sub_category_id')
            ->filter();

        // Map those classes to their unique Boards (Categories) via FilterClassSubject
        $boards = FilterClassSubject::whereIn('sub_category_id', $subCategoryIds)
            ->with('category:id,category_name')
            ->get()
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->sortBy('category_name')
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->category_name
                ];
            })
            ->values();

        return response()->json([
            'status' => true,
            'data' => $boards
        ]);
    }

    public function getClasses(Request $request)
    {
        $institution_id = $request->institution_id;
        $board_id = $request->board_id;

        // Find all classes (sub_category_id) that are mapped to the selected Board (Category)
        $mappedSubCategoryIds = FilterClassSubject::where('category_id', $board_id)
            ->distinct()
            ->pluck('sub_category_id');

        $classes = InstitutionClass::with('subcategory')
            ->where('institution_id', $institution_id)
            ->whereIn('sub_category_id', $mappedSubCategoryIds)
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



    public function index(Request $request)
    {
        $user = $request->user();
        $type = $user->type;

        if (!in_array($type, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Admin or Sales can view students.'
            ], 403);
        }

        $query = User::where('role_id', RoleHelper::studentId())->with(['institution', 'institutionClass.subcategory']);

        if ($type === 'sales') {
            $query->where('added_by', $user->id);
        }

        $students = $query->orderBy('id', 'desc')->paginate(15);

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' fetched students successfully.',
            'data' => $students
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $type = $user->type;

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
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Check if institution is active
        $institution = InstitutionManagement::find($validated['institution_id']);
        if (!$institution || $institution->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Selected institution is inactive or not found.'
            ], 403);
        }

        // Ensure selected class belongs to selected institution
        $classExists = InstitutionClass::where('id', $validated['institution_classes_id'])
            ->where('institution_id', $validated['institution_id'])
            ->exists();

        if (!$classExists) {
            return response()->json([
                'status' => false,
                'message' => 'The selected class does not belong to the selected institution.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // IMAGE UPLOAD
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/sales/profile_pictures'), $filename);
                $validated['profile_image'] = $filename;
            }

            // 🔹 Fetch student role (Spatie)
            $role = \Spatie\Permission\Models\Role::where([
                'name' => 'student'
            ])->first();

            if (!$role) {
                throw new \Exception('Student role not found');
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

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Student added successfully.',
                'data' => $student->load(['institution', 'institutionClass.subcategory'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add student: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $type = $user->type;

        if (!in_array($type, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied!'
            ], 403);
        }

        $student = User::where('role_id', RoleHelper::studentId())->find($id);

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found.'
            ], 404);
        }

        if ($type === 'sales' && $student->added_by !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You can update only students added by you.'
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
                'message' => 'The selected class does not belong to the selected institution.'
            ], 422);
        }

        DB::beginTransaction();
        try {
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
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Student updated successfully.',
                'data' => $student->load(['institution', 'institutionClass.subcategory'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update student: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $type = $user->type;

        if (!in_array($type, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied! Only Admin or Sales can delete students.'
            ], 403);
        }

        $student = User::where('role_id', RoleHelper::studentId())->find($id);

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
            'message' => 'Student deleted successfully.'
        ], 200);
    }

    public function getStudentByClass(Request $request)
    {
        $user = $request->user();
        $type = $user->type;

        if (!in_array($type, ['admin', 'sales'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied!'
            ], 403);
        }

        $query = User::where('role_id', RoleHelper::studentId())->with(['institution', 'institutionClass.subcategory']);

        if ($type === 'sales') {
            $query->where('added_by', $user->id);
        }

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->filled('institution_classes_id')) {
            $query->where('institution_classes_id', $request->institution_classes_id);
        } elseif ($request->filled('class')) {
            $query->where('institution_classes_id', $request->class);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('roll_number')) {
            $query->where('roll_number', $request->roll_number);
        }

        $students = $query->orderBy('id', 'desc')->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $students
        ]);
    }
}
