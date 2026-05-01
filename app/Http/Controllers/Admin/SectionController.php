<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\Section;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function sections(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('view_sections')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $data = Section::orderBy('id', 'desc');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if (!empty($row->image)) {
                        return '<img src="' . $row->image . '" width="60" height="60" style="object-fit: cover; border-radius: 6px; border:1px solid #ddd;">';
                    }
                    return '<img src="' . asset('admin/images/no-image.png') . '" width="60" height="60" style="object-fit: cover; border-radius: 6px; border:1px solid #ddd;">';
                })
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $prefix = $adminType === 'vendor' ? 'vendor' : 'admin';
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    return '<a class="updateSectionStatus" id="education-level-' . $row->id . '"
                                education_level_id="' . $row->id . '"
                                data-url="' . route($prefix . '.updateeducationstatus') . '"
                                href="javascript:void(0)">
                                <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                    status="' . $statusText . '"></i>
                            </a>';
                })
                ->addColumn('actions', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $editRoute = $adminType === 'vendor' ? 'vendor.add_edit_education_level' : 'admin.add_edit_education_level';
                    $deleteRoute = $adminType === 'vendor' ? 'vendor.delete_education' : 'admin.delete_education';
                    
                    return '<a href="' . route($editRoute, $row->id) . '">
                                <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                            </a>
                            <a href="javascript:void(0)" class="confirmDelete"
                                data-module="Education Level"
                                data-url="' . route($deleteRoute, $row->id) . '">
                                <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                            </a>';
                })
                ->rawColumns(['image', 'status', 'actions'])
                ->make(true);
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'education-levels');

        $adminType = Auth::guard('admin')->user()->type;

        return view('admin.sections.sections')->with(compact('logos', 'headerLogo', 'adminType'));
    }

    public function updateSectionStatus(Request $request)
    { // Update Section Status using AJAX in sections.blade.php
        if (!Auth::guard('admin')->user()->can('edit_categories')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {
            $data = $request->all();

            if ($data['status'] == 'Active') {
                $status = 0;
            } else {
                $status = 1;
            }


            Section::where('id', $data['education_level_id'])->update(['status' => $status]);

            return response()->json([
                'status'     => $status,
                'education_level_id' => $data['education_level_id']
            ]);
        }

        return view('admin.sections.sections', compact('sections', 'logos', 'headerLogo'));
    }

    public function deleteSection($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_categories')) {
            return redirect()->back()->with('error_message', 'Unauthorized action.');
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Section::where('id', $id)->delete();

        $message = 'Education Level has been deleted successfully!';

        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType === 'vendor' ? 'vendor.education_levels' : 'admin.education_levels';
        return redirect()->route($route)->with('success_message', $message);
    }

    public function addEditSection(Request $request, $id = null)
    {
        Session::put('page', 'education-levels');

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        if ($id == "") {

            if (!Auth::guard('admin')->user()->can('add_categories')) {
                abort(403, 'Unauthorized action.');
            }

            $title = "Add Education Level";
            $section = new Section();
            $message = "Education Level added successfully!";
        } else {

            if (!Auth::guard('admin')->user()->can('edit_categories')) {
                abort(403, 'Unauthorized action.');
            }

            $title = "Edit Education Level";
            $section = Section::findOrFail($id);
            $message = "Education Level updated successfully!";
        }

        if ($request->isMethod('post')) {

            $rules = [
                'education_level_name'  => 'required|string|max:255',
                'section_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];

            $customMessages = [
                'education_level_name.required' => 'Education Level Name is required',
                'section_image.image'   => 'File must be an image',
                'section_image.mimes'   => 'Only jpg, jpeg and png allowed',
                'section_image.max'     => 'Image size must be less than 2MB',
            ];

            $this->validate($request, $rules, $customMessages);

            $section->name   = $request->education_level_name;
            $section->status = 1;

            if ($request->hasFile('section_image')) {

                $image_tmp = $request->file('section_image');

                if ($image_tmp->isValid()) {

                    if (!empty($section->image)) {

                        $oldImagePath = public_path('admin/images/section/' . $section->image);

                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $extension = $image_tmp->getClientOriginalExtension();
                    $imageName = time() . '_' . rand(111, 99999) . '.' . $extension;

                    $uploadPath = public_path('admin/images/section/');

                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    $image_tmp->move($uploadPath, $imageName);

                    $section->image = $imageName;
                }
            }

            $section->save();

            $redirectRoute = Auth::guard('admin')->user()->type == 'vendor' ? 'vendor.education_levels' : 'admin.education_levels';
            return redirect()->route($redirectRoute)->with('success_message', $message);
        }

        return view('admin.sections.add_edit_section')
            ->with(compact('title', 'section', 'logos', 'headerLogo'));
    }
}
