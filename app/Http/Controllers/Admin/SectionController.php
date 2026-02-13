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
    { // If the $id is not passed, this means Add a Section, if not, this means Edit the Section
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'sections');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        if ($id == '') { // if there's no $id is passed in the route/URL parameters, this means Add a new section
            if (!Auth::guard('admin')->user()->can('add_categories')) {
                abort(403, 'Unauthorized action.');
            }
            $title = 'Add Section';
            $section = new Section();
            // dd($section);
            $message = 'Section added successfully!';
        } else { // if the $id is passed in the route/URL parameters, this means Edit the Section
            if (!Auth::guard('admin')->user()->can('edit_categories')) {
                abort(403, 'Unauthorized action.');
            }
            $title = 'Edit Section';
            $section = Section::find($id);
            // dd($section);
            $message = 'Section updated successfully!';
        }

        if ($request->isMethod('post')) { // WHETHER Add or Update <form> submission!!
            $data = $request->all();
            // dd($data);

            // Laravel's Validation    // Customizing Laravel's Validation Error Messages: https://laravel.com/docs/9.x/validation#customizing-the-error-messages    // Customizing Validation Rules: https://laravel.com/docs/9.x/validation#custom-validation-rules
            $rules = [
                'section_name' => 'required', // only alphabetical characters and spaces
            ];

            $customMessages = [ // Specifying A Custom Message For A Given Attribute: https://laravel.com/docs/9.x/validation#specifying-a-custom-message-for-a-given-attribute
                'section_name.required' => 'Section Name is required',

            ];

            $this->validate($request, $rules, $customMessages);


            // Saving inserted/updated data    // Inserting & Updating Models: https://laravel.com/docs/9.x/eloquent#inserts AND https://laravel.com/docs/9.x/eloquent#updates
            $section->name   = $data['section_name']; // WHETHER ADDING or UPDATING
            $section->status = 1;  // WHETHER ADDING or UPDATING
            $section->save(); // Save all data in the database


            return redirect('admin/sections')->with('success_message', $message);
        }


        return view('admin.sections.add_edit_section')->with(compact('title', 'section', 'logos', 'headerLogo'));
    }
}
