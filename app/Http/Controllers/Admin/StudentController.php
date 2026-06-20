<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicProfile;
use App\Models\HeaderLogo;
use App\Models\User;
use App\Models\InstitutionManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\InstitutionClass;
use App\Helpers\RoleHelper;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'students');

        if ($request->ajax()) {
            $data = User::where('role_id', RoleHelper::studentId())->with('institution')->orderBy('id', 'desc');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function($row) {
                    return '<div style="display: flex; justify-content: center; align-items: center;"><input type="checkbox" class="select-row-checkbox select-student-checkbox" value="' . $row->id . '" style="transform: scale(1.3); cursor: pointer;"></div>';
                })
                ->addColumn('institution', function ($row) {
                    return $row->institution->name ?? 'No Institution';
                })
                ->addColumn('dob', function ($row) {
                    return $row->dob ? \Carbon\Carbon::parse($row->dob)->format('d M Y') : 'N/A';
                })
                ->addColumn('location', function ($row) {
                    if (empty($row->latitude) || empty($row->longitude)) {
                        return 'N/A';
                    }

                    // Format address name
                    $block = $row->block->name ?? null;
                    $district = $row->district->name ?? null;
                    $state = $row->state->name ?? null;
                    $country = $row->country->name ?? null;
                    $addressParts = array_filter([$row->address, $block, $district, $state, $row->pincode, $country]);
                    $locationName = !empty($addressParts) ? implode(', ', $addressParts) : 'N/A';

                    $googleMapsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $row->latitude . ',' . $row->longitude;

                    $html = '<div style="font-size: 13px; max-width: 250px; white-space: normal;">';
                    $html .= '<div style="font-weight: 600; margin-bottom: 4px; color: #1f2937;">' . e($locationName) . '</div>';
                    $html .= '<div class="d-flex align-items-center flex-wrap" style="font-size: 11px; color: #6b7280; gap: 8px;">';
                    $html .= '<span><i class="mdi mdi-target"></i> ' . $row->latitude . ',' . $row->longitude . '</span>';
                    $html .= '<a href="' . $googleMapsUrl . '" target="_blank" class="text-primary font-weight-bold" style="text-decoration: none; font-size: 11px; display: inline-flex; align-items: center; gap: 2px;">';
                    $html .= '<i class="mdi mdi-map-marker text-danger"></i> VIEW MAP';
                    $html .= '</a>';
                    $html .= '</div>';
                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('status', function ($row) {
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    return '<a class="updateStudentStatus" id="student-' . $row->id . '"
                                student_id="' . $row->id . '"
                                data-url="' . route('admin.students.updateStatus', $row->id) . '"
                                href="javascript:void(0)">
                                <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                    status="' . $statusText . '"></i>
                            </a>';
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . url('admin/students/'.$row->id.'/edit') . '"
                               class="btn btn-sm btn-success" title="Edit Student">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(' . $row->id . ')"
                                    class="btn btn-sm btn-danger" title="Delete Student">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button onclick="openCreditModal(' . $row->id . ', \'' . addslashes(e($row->name)) . '\', ' . $row->wallet_balance . ')"
                                    class="btn btn-sm btn-info text-white" title="Credit Wallet">
                                <i class="fas fa-wallet"></i>
                            </button>';
                })
                ->rawColumns(['checkbox', 'location', 'status', 'actions'])
                ->make(true);
        }

        $students = User::where('role_id', RoleHelper::studentId())->with('institution')->orderBy('id', 'desc')->get();

        return view('admin.students.index')->with(compact('students', 'logos', 'headerLogo'));
    }

    public function create()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'students');

        $institutions = InstitutionManagement::orderBy('name')->get();

        return view('admin.students.create')->with(compact('institutions', 'logos', 'headerLogo'));
    }

    public function store(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
            'father_names' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|min:10|max:15',
            'institution_id' => 'required|exists:institution_managements,id',
            'institution_classes_id' => 'required|exists:institution_classes,id',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date|before:today',
            'roll_number' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        $role = \Spatie\Permission\Models\Role::where([
            'name'       => 'student'
        ])->first();

        if (!$role) {
            throw new \Exception('Student role not found');
        }


        $data['password']  = Hash::make('123456');
        $data['status'] = 1;
        $data['role_id'] = $role->id;
        $data['added_by'] = Auth::guard('admin')->user()->id;

        $studentuser = User::create($data);
        $studentuser->assignRole($role);

        // Create academic profile entry for this student
        $institution = InstitutionManagement::find($data['institution_id']);
        AcademicProfile::create([
            'user_id' => $studentuser->id,
            'education_level_id' => is_numeric($institution?->type) ? (int) $institution->type : null,
            'board_id' => is_numeric($institution?->board) ? (int) $institution->board : null,
            'class_id' => $data['institution_classes_id'] ?? null,
        ]);

        return redirect('admin/students')->with('success_message', 'Student has been added successfully', 'logos');
        return view('admin.students.index', compact('students', 'logos', 'headerLogo'));
    }

    public function edit($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'students');

        $student = User::findOrFail($id);
        $institutions = InstitutionManagement::orderBy('name')->get();

        return view('admin.students.edit')->with(compact('student', 'institutions', 'logos', 'headerLogo'));
    }

    public function update(Request $request, $id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
            'father_names' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|min:10|max:15',
            'institution_id' => 'required|exists:institution_managements,id',
            'institution_classes_id' => 'required|exists:institution_classes,id',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date|before:today',
            'roll_number' => 'nullable|string|max:255',
            'status' => 'nullable|boolean'
        ]);

        $student = User::findOrFail($id);
        $data = $request->all();
        $data['status'] = $request->has('status')
            ? ($request->boolean('status') ? 1 : 0)
            : $student->status;

        $student->update($data);

        // Sync academic profile if institution or class changed
        $institution = InstitutionManagement::find($data['institution_id']);
        $profileData = [
            'education_level_id' => is_numeric($institution?->type) ? (int) $institution->type : null,
            'board_id' => is_numeric($institution?->board) ? (int) $institution->board : null,
            'class_id' => $data['institution_classes_id'] ?? null,
        ];

        if ($student->academicProfile) {
            $student->academicProfile->update($profileData);
        } else {
            $student->academicProfile()->create($profileData);
        }

        return redirect('admin/students')->with('success_message', 'Student has been updated successfully', 'logos');
        return view('admin.students.index', compact('students', 'logos', 'headerLogo'));
    }

    public function destroy($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $student = User::findOrFail($id);
        if ($student->academicProfile) {
            $student->academicProfile->delete();
        }
        $student->delete();

        return redirect('admin/students')->with('success_message', 'Student has been deleted successfully', 'logos');
        return view('admin.students.index', compact('students', 'logos', 'headerLogo'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No selected students found.'
            ]);
        }

        $deletedCount = 0;
        foreach ($ids as $id) {
            $student = User::where('role_id', RoleHelper::studentId())->find($id);
            if ($student) {
                if ($student->academicProfile) {
                    $student->academicProfile->delete();
                }
                $student->delete();
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Selected ' . $deletedCount . ' students deleted successfully!'
        ]);
    }

    /**
     * Return student details for AJAX (used in notifications modal).
     */
    public function details($id)
    {
        $student = User::with('institution')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $student->id,
                'name' => $student->name,
                'phone' => $student->phone,
                'class' => $student->class,
                'gender' => $student->gender,
                'dob' => $student->dob,
                'roll_number' => $student->roll_number,
                'status' => $student->status,
                'institution' => $student->institution->name ?? 'No Institution set',
                'created_at' => optional($student->created_at)->format('M d, Y h:i A'),
            ],
        ]);
    }

    /**
     * Update student status (approve/reject) from notifications modal.
     */
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $student = User::where('role_id', RoleHelper::studentId())->findOrFail($id);
        $oldStatus = $student->status;
        $student->status = (int) $data['status'];
        $student->save();

        // Credit commission if approved and was not approved before
        if ($student->status == 1 && $oldStatus != 1) {
            $salesExecutiveId = $student->added_by;
            if ($salesExecutiveId) {
                $salesExecutive = User::find($salesExecutiveId);
                // Check if they are actually a sales executive (role 3)
                if ($salesExecutive && $salesExecutive->hasRole('sales', 'web')) {
                    // Check if commission already credited for this student
                    $description = "Commission for Student: " . $student->name . " (#" . $student->id . ")";
                    $exists = \App\Models\WalletTransaction::where('user_id', $salesExecutiveId)
                        ->where('description', $description)
                        ->exists();

                    if (!$exists) {
                        $amount = \App\Models\Setting::getValue('default_income_per_target', 10);

                        $salesExecutive->wallet_balance += $amount;
                        $salesExecutive->save();

                        \App\Models\WalletTransaction::create([
                            'user_id' => $salesExecutiveId,
                            'amount' => $amount,
                            'type' => 'credit',
                            'description' => $description,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'status' => $student->status,
        ]);
    }

    public function getInstitutionBoards(Request $request)
    {
        // Board is no longer linked to classes, so we can either return all categories
        // or just return empty if not needed. Given the user's request, classes are global.
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

    public function creditWallet(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // Only superadmin and admin can access
        if (!$admin || !in_array($admin->type, ['superadmin', 'admin'])) {
            return redirect('admin/login')
                ->with('error_message', 'Unauthorized access.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $student = User::where('id', $request->user_id)
            ->where('role_id', RoleHelper::studentId())
            ->firstOrFail();

        $amount = (float) $request->amount;
        $description = $request->description ?: 'Manual credit by admin';

        // Credit balance
        $student->wallet_balance += $amount;
        $student->save();

        // Create transaction log
        \App\Models\WalletTransaction::create([
            'user_id' => $student->id,
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description,
        ]);

        // Create notification
        \App\Models\Notification::create([
            'type' => 'wallet_credit',
            'title' => 'Wallet Credited by Admin',
            'message' => '₹' . number_format($amount, 2) . ' has been manually credited to your wallet by the administrator. Reason: ' . $description,
            'related_id' => (int) $student->id,
            'related_type' => User::class,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success_message', '₹' . number_format($amount, 2) . ' has been successfully credited to ' . $student->name . '\'s wallet.');
    }
}
