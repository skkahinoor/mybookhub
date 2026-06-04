<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeaderLogo;
use App\Models\Subcategory;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SubjectController extends Controller
{

    public function index(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('view_subjects')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $data = Subject::orderBy('id', 'desc');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('subject_icon', function ($row) {
                    if (!empty($row->subject_icon)) {
                        return '<img src="' . asset($row->subject_icon) . '" style="width: 50px; height: 50px;">';
                    }
                    return 'No Icon';
                })
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    if ($adminType === 'vendor') {
                        return '<a class="updateSubjectStatus" id="subject-' . $row->id . '"
                                    subject_id="' . $row->id . '"
                                    data-url="' . route('vendor.updatesubjectstatus') . '"
                                    href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                        status="' . $statusText . '"></i>
                                </a>';
                    } else {
                        return '<a class="updateSubjectStatus" id="subject-' . $row->id . '"
                                    subject_id="' . $row->id . '"
                                    data-url="' . route('admin.updatesubjectstatus') . '"
                                    href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                        status="' . $statusText . '"></i>
                                </a>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    if ($adminType === 'vendor') {
                        $editUrl = route('vendor.edit.subject', $row->id);
                        $deleteUrl = route('vendor.delete.subject', $row->id);
                    } else {
                        $editUrl = route('admin.edit.subject', $row->id);
                        $deleteUrl = route('admin.delete.subject', $row->id);
                    }
                    return '<a href="' . $editUrl . '">
                                <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                            </a>
                            <a href="javascript:void(0)" class="confirmDelete"
                                data-module="subject"
                                data-url="' . $deleteUrl . '">
                                <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                            </a>';
                })
                ->rawColumns(['subject_icon', 'status', 'actions'])
                ->make(true);
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'subjects');
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.subject.subject', compact('logos', 'headerLogo', 'adminType'));
    }

    public function add()
    {
        if (!Auth::guard('admin')->user()->can('add_subjects')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;
        $subcategories = Subcategory::where('status', 1)->get()->toArray();
        return view('admin.subject.add_subject', compact('logos', 'headerLogo', 'adminType', 'subcategories'));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('add_subjects')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'subject_icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ],
        [
            'name.unique' => 'Subject name already exists',
        ]);

        $subject_icon = null;
        if ($request->hasFile('subject_icon')) {
            $image_tmp = $request->file('subject_icon');
            if ($image_tmp->isValid()) {
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = time() . '_' . rand(111, 99999) . '.' . $extension;
                $uploadPath = public_path('admin/images/subject_icons/');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image_tmp->move($uploadPath, $imageName);
                $subject_icon = $imageName;
            }
        }

        $store = Subject::create([
            'name' => $request->name,
            'subject_icon' => $subject_icon,
        ]);

        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType === 'vendor' ? 'vendor.subject' : 'admin.subject';

        return redirect()->route($route)->with('success_message', 'Subject inserted successfully');
    }

    public function edit($id)
    {
        if (!Auth::guard('admin')->user()->can('edit_subjects')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $subjects = Subject::find($id);
        $adminType = Auth::guard('admin')->user()->type;
        $subcategories = Subcategory::where('status', 1)->get()->toArray();

        return view('admin.subject.edit_subject', compact('subjects', 'logos', 'headerLogo', 'adminType', 'subcategories'));
    }

    public function update(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('edit_subjects')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
            'subject_icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);
        $update = Subject::find($request->id);

        $subject_icon = $update->subject_icon;
        if ($request->hasFile('subject_icon')) {
            $image_tmp = $request->file('subject_icon');
            if ($image_tmp->isValid()) {
                // Delete old icon if exists
                if (!empty($update->subject_icon)) {
                    $oldPath = public_path('admin/images/subject_icons/' . $update->subject_icon);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = time() . '_' . rand(111, 99999) . '.' . $extension;
                $uploadPath = public_path('admin/images/subject_icons/');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image_tmp->move($uploadPath, $imageName);
                $subject_icon = $imageName;
            }
        }

        $update->update([
            'name' => $request->name,
            'subject_icon' => $subject_icon,
        ]);
        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType === 'vendor' ? 'vendor.subject' : 'admin.subject';

        return redirect()->route($route)->with('success_message', 'Subject updated successfully');
    }

    public function delete($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_subjects')) {
            abort(403, 'Unauthorized action.');
        }

        $subject = Subject::find($id);

        // If subject not found, just redirect with an error instead of 404
        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType === 'vendor' ? 'vendor.subject' : 'admin.subject';

        if (!$subject) {
            return redirect()
                ->route($route)
                ->with('error_message', 'Subject not found or already deleted.');
        }

        $subject->delete();

        return redirect()
            ->route($route)
            ->with('success_message', 'Subject deleted successfully');
    }


    public function updateStatus(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('edit_subjects')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }
        if (!$request->ajax()) {
            abort(404);
        }

        $data = $request->validate([
            'status'     => 'required|in:Active,Inactive,0,1',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $current = $data['status'];
        $status = ($current === 'Active' || $current === '1' || $current === 1) ? 0 : 1;

        Subject::where('id', $data['subject_id'])->update(['status' => $status]);

        return response()->json([
            'status'     => $status,
            'subject_id' => $data['subject_id'],
        ]);
    }

    public function duplicateSubjects()
    {
        // Fetch all subject names that are duplicate
        $duplicateNames = Subject::select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(name) > 1')
            ->pluck('name');

        // Fetch duplicate subjects with their products and classSubjectMappings counts
        $subjects = Subject::whereIn('name', $duplicateNames)
            ->withCount(['products', 'classSubjectMappings'])
            ->orderBy('name')
            ->orderBy('id', 'asc')
            ->get();

        // Group them by name for easier display in UI
        $groupedSubjects = $subjects->groupBy('name');

        return view('admin.subject.duplicate_subjects', compact('groupedSubjects'));
    }

    public function mergeSubjects(Request $request)
    {
        $request->validate([
            'target_id' => 'required|exists:subjects,id',
            'duplicate_name' => 'required|string',
        ]);

        $targetId = $request->target_id;
        $name = $request->duplicate_name;

        // Find all duplicates with this name
        $duplicates = Subject::where('name', $name)->get();

        // Separate target from others
        $target = $duplicates->firstWhere('id', $targetId);
        if (!$target) {
            return redirect()->back()->with('error_message', 'Target subject not found in duplicates.');
        }

        $otherIds = $duplicates->where('id', '!=', $targetId)->pluck('id');

        if ($otherIds->isEmpty()) {
            return redirect()->back()->with('error_message', 'No duplicate subjects found to merge.');
        }

        \DB::beginTransaction();
        try {
            // Update products to point to target subject
            \App\Models\Product::whereIn('subject_id', $otherIds)->update(['subject_id' => $targetId]);

            // Update or merge filter_class_subject mappings
            // To prevent key constraint / duplicate issues, read the existing mappings for target
            $targetMappings = \App\Models\FilterClassSubject::where('subject_id', $targetId)->get();

            // We iterate over the mappings of other duplicate subjects
            $otherMappings = \App\Models\FilterClassSubject::whereIn('subject_id', $otherIds)->get();

            foreach ($otherMappings as $mapping) {
                // Check if target already has an identical mapping
                $exists = $targetMappings->contains(function ($tMapping) use ($mapping) {
                    return $tMapping->section_id == $mapping->section_id &&
                           $tMapping->category_id == $mapping->category_id &&
                           $tMapping->sub_category_id == $mapping->sub_category_id;
                });

                if ($exists) {
                    // Redundant mapping, delete it
                    $mapping->delete();
                } else {
                    // Update to target_id
                    $mapping->update(['subject_id' => $targetId]);
                }
            }

            // Finally, delete other subjects
            Subject::whereIn('id', $otherIds)->delete();

            \DB::commit();

            return redirect()->back()->with('success_message', 'Subjects merged successfully into ID: ' . $targetId);
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error_message', 'Merge failed: ' . $e->getMessage());
        }
    }

    public function quickStore(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('add_subjects')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $name = trim($request->name);
        if (empty($name)) {
            return response()->json([
                'status' => false,
                'message' => 'Subject name is required.'
            ], 422);
        }

        // Case-insensitive duplicate check
        $subject = Subject::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
        if ($subject) {
            if ($subject->status == 0) {
                // If it exists but is inactive, activate it and return it
                $subject->update(['status' => 1]);
                return response()->json([
                    'status' => true,
                    'message' => 'Subject activated and added successfully!',
                    'subject' => $subject
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Subject already exists.'
            ], 422);
        }

        // Create new subject
        $newSubject = Subject::create([
            'name' => $name,
            'status' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Subject created successfully!',
            'subject' => $newSubject
        ]);
    }
}


