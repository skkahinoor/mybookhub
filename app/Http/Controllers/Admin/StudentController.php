<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\User;
use App\Models\InstitutionManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'students');

        $students = User::role('student', 'web')->with('institution')->orderBy('id', 'desc')->get();

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
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|min:10|max:15',
            'institution_id' => 'nullable|exists:institution_managements,id',
            'class' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date|before:today',
            'roll_number' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        $data['password']  = Hash::make('123456');
        $data['status'] = 1;
        $data['role_id'] = 5;
        $data['added_by'] = Auth::guard('admin')->user()->id;

        User::create($data);

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
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|min:10|max:15',
            'institution_id' => 'nullable|exists:institution_managements,id',
            'class' => 'required|string|max:255',
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

        return redirect('admin/students')->with('success_message', 'Student has been updated successfully', 'logos');
        return view('admin.students.index', compact('students', 'logos', 'headerLogo'));
    }

    public function destroy($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $student = User::findOrFail($id);
        $student->delete();

        return redirect('admin/students')->with('success_message', 'Student has been deleted successfully', 'logos');
        return view('admin.students.index', compact('students', 'logos', 'headerLogo'));
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

        $student = User::role('student', 'web')->findOrFail($id);
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
}
