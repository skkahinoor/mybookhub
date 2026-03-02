<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Subcategory; // This represents Classes
use App\Models\Subject;
use App\Models\Section;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ClassSubjectController extends Controller
{
    public function index()
    {
        if (!Auth::guard('admin')->user()->can('view_class_subjects')) {
            // abort(403, 'Unauthorized action.');
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;

        // Fetch all assignments from the filter_class_subject table
        // We'll join with other tables to show names
        // Group by class to show all subjects for a class in one row
        $assignments = DB::table('filter_class_subject')
            ->join('sections', 'filter_class_subject.section_id', '=', 'sections.id')
            ->join('categories', 'filter_class_subject.category_id', '=', 'categories.id')
            ->join('subcategories', 'filter_class_subject.sub_category_id', '=', 'subcategories.id')
            ->join('subjects', 'filter_class_subject.subject_id', '=', 'subjects.id')
            ->select(
                'filter_class_subject.sub_category_id',
                'sections.name as section_name',
                'categories.category_name',
                'subcategories.subcategory_name',
                DB::raw('GROUP_CONCAT(subjects.name SEPARATOR ", ") as subject_names')
            )
            ->groupBy('filter_class_subject.sub_category_id', 'sections.name', 'categories.category_name', 'subcategories.subcategory_name')
            ->get();

        Session::put('page', 'class_subjects');

        return view('admin.class_subjects.index', compact('assignments', 'logos', 'headerLogo', 'adminType'));
    }

    public function create()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;

        $sections = Section::where('status', 1)->get();
        // Categories will be loaded via AJAX based on Section
        $subcategories = Subcategory::where('status', 1)->get();
        $subjects = Subject::where('status', 1)->get();

        return view('admin.class_subjects.add_class_subject', compact('sections', 'subcategories', 'subjects', 'logos', 'headerLogo', 'adminType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $subCategory = Subcategory::findOrFail($request->subcategory_id);

        $syncData = [];
        foreach ($request->subject_ids as $subject_id) {
            $syncData[$subject_id] = [
                'section_id' => $request->section_id,
                'category_id' => $request->category_id
            ];
        }

        // We use attach or sync. Sync is better but we need to handle the fact that
        // a subcategory might be assigned to multiple categories.
        // If we want it to be unique per (Subcat, Subject), sync is fine.
        $subCategory->subjects()->sync($syncData);

        return redirect()->route('admin.class_subjects.index')->with('success_message', 'Subjects assigned successfully!');
    }

    public function edit($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;

        // Since it's a pivot entry, we might want to edit a specific one or all for a subcat
        // For simplicity, let's assume we edit based on sub_category_id as before but show the others
        $subCategory = Subcategory::with('subjects')->findOrFail($id);

        $sections = Section::where('status', 1)->get();

        // Get current values from one of the assignments
        $firstAssignment = DB::table('filter_class_subject')->where('sub_category_id', $id)->first();
        $currentSectionId = $firstAssignment ? $firstAssignment->section_id : null;
        $currentCategoryId = $firstAssignment ? $firstAssignment->category_id : null;

        $categories = $currentSectionId ? Category::where('section_id', $currentSectionId)->get() : [];
        $subcategories = Subcategory::where('status', 1)->orWhere('id', $id)->get();
        $subjects = Subject::where('status', 1)->get();

        $assignedSubjectIds = $subCategory->subjects->pluck('id')->toArray();

        return view('admin.class_subjects.edit_class_subject', compact('subCategory', 'sections', 'categories', 'subcategories', 'subjects', 'assignedSubjectIds', 'currentSectionId', 'currentCategoryId', 'logos', 'headerLogo', 'adminType'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $subCategory = Subcategory::findOrFail($id);

        $syncData = [];
        foreach ($request->subject_ids as $subject_id) {
            $syncData[$subject_id] = [
                'section_id' => $request->section_id,
                'category_id' => $request->category_id
            ];
        }

        $subCategory->subjects()->sync($syncData);

        return redirect()->route('admin.class_subjects.index')->with('success_message', 'Class Subjects updated successfully!');
    }

    public function delete($id)
    {
        // $id is now sub_category_id from the grouped index
        DB::table('filter_class_subject')->where('sub_category_id', $id)->delete();

        return redirect()->route('admin.class_subjects.index')->with('success_message', 'Class Subject assignments removed successfully.');
    }
}
