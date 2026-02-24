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
    public function sections()
    {
        if (!Auth::guard('admin')->user()->can('view_sections')) {
            abort(403, 'Unauthorized action.');
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'sections');


        // $sections = Section::get(); // Eloquent Collection

        $sections = Section::orderBy('id', 'desc')->get()->toArray(); // Plain PHP array
        // dd($sections);
        $adminType = Auth::guard('admin')->user()->type;

        return view('admin.sections.sections')->with(compact('sections', 'logos', 'headerLogo', 'adminType'));
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


            Section::where('id', $data['section_id'])->update(['status' => $status]);

            return response()->json([
                'status'     => $status,
                'section_id' => $data['section_id']
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

        $message = 'Section has been deleted successfully!';

        return redirect()->back()->with('success_message', $message, 'logos');
        return view('admin.sections.sections', compact('sections', 'logos', 'headerLogo'));
    }

    public function addEditSection(Request $request, $id = null)
    {
        Session::put('page', 'sections');

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        if ($id == "") {

            if (!Auth::guard('admin')->user()->can('add_categories')) {
                abort(403, 'Unauthorized action.');
            }

            $title = "Add Section";
            $section = new Section();
            $message = "Section added successfully!";
        }

        else {

            if (!Auth::guard('admin')->user()->can('edit_categories')) {
                abort(403, 'Unauthorized action.');
            }

            $title = "Edit Section";
            $section = Section::findOrFail($id);
            $message = "Section updated successfully!";
        }

        if ($request->isMethod('post')) {

            $rules = [
                'section_name'  => 'required|string|max:255',
                'section_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];

            $customMessages = [
                'section_name.required' => 'Section Name is required',
                'section_image.image'   => 'File must be an image',
                'section_image.mimes'   => 'Only jpg, jpeg and png allowed',
                'section_image.max'     => 'Image size must be less than 2MB',
            ];

            $this->validate($request, $rules, $customMessages);

            $section->name   = $request->section_name;
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

            return redirect('admin/sections')->with('success_message', $message);
        }

        return view('admin.sections.add_edit_section')
            ->with(compact('title', 'section', 'logos', 'headerLogo'));
    }
}
