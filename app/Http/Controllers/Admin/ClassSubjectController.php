<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Subcategory; // This represents Classes
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ClassSubjectController extends Controller
{
    public function index()
    {
        if (!Auth::guard('admin')->user()->can('view_class_subjects')) {
            // abort(403, 'Unauthorized action.');
            // If permissions are not yet defined, we might need to skip this or handle it.
            // But let's assume they exist or will be added.
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;

        // Fetch all classes (subcategories) that have at least one subject assigned
        $classes = Subcategory::with('subjects')->has('subjects')->get();
        // and also those that don't have any, for adding
        $allClasses = Subcategory::where('status', 1)->get();

        Session::put('page', 'class_subjects');

        return view('admin.class_subjects.index', compact('classes', 'allClasses', 'logos', 'headerLogo', 'adminType'));
    }

    public function create()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;

        $classes = Subcategory::where('status', 1)->get();
        $subjects = Subject::where('status', 1)->get();

        return view('admin.class_subjects.add_class_subject', compact('classes', 'subjects', 'logos', 'headerLogo', 'adminType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $class = Subcategory::findOrFail($request->subcategory_id);

        // Sync will handle deleting old and inserting new, ensuring no duplicates
        $class->subjects()->sync($request->subject_ids);

        return redirect()->route('admin.class_subjects.index')->with('success_message', 'Subjects assigned to Class successfully!');
    }

    public function edit($subcategory_id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;

        $class = Subcategory::with('subjects')->findOrFail($subcategory_id);
        $classes = Subcategory::where('status', 1)->get();
        $subjects = Subject::where('status', 1)->get();

        $assignedSubjectIds = $class->subjects->pluck('id')->toArray();

        return view('admin.class_subjects.edit_class_subject', compact('class', 'classes', 'subjects', 'assignedSubjectIds', 'logos', 'headerLogo', 'adminType'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $class = Subcategory::findOrFail($id);

        // Sync replaces all current assignments with the ones in the array
        $class->subjects()->sync($request->subject_ids);

        return redirect()->route('admin.class_subjects.index')->with('success_message', 'Class Subjects updated successfully!');
    }

    public function delete($subcategory_id)
    {
        $class = Subcategory::findOrFail($subcategory_id);
        $class->subjects()->detach(); // Removes all assignments for this class

        return redirect()->route('admin.class_subjects.index')->with('success_message', 'All subject assignments cleared for this class.');
    }
}
