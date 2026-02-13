<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function languages()
    {
        if (!Auth::guard('admin')->user()->can('view_languages')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'languages');
        $languages = Language::get();
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.languages.languages')->with(compact('languages', 'logos', 'headerLogo', 'adminType'));
    }

    public function updateLanguageStatus(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('edit_languages')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {
            $data = $request->all();
            if ($data['status'] == "Active") {
                $status = 0;
            } else {
                $status = 1;
            }
            Language::where('id', $data['language_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'language_id' => $data['language_id']]);
        }
        return view('admin.languages.languages', compact('languages', 'logos', 'headerLogo'));
    }

    public function addEditLanguage(Request $request, $id = null)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($id == "") {
            if (!Auth::guard('admin')->user()->can('add_languages')) {
                abort(403, 'Unauthorized action.');
            }
            $title = "Add Language";
            $language = new Language;
            $message = "Language added successfully!";
        } else {
            if (!Auth::guard('admin')->user()->can('edit_languages')) {
                abort(403, 'Unauthorized action.');
            }
            $title = "Edit Language";
            $language = Language::find($id);
            $message = "Language updated successfully!";
        }

        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'name' => 'required',
            ];

            $customMessages = [
                'name.required' => 'Language Name is required',
            ];

            $this->validate($request, $rules, $customMessages);

            $language->name = $data['name'];
            $language->status = 1;
            $language->save();

            return redirect('admin/languages')->with('success_message', $message);
        }

        return view('admin.languages.add_edit_language')->with(compact('title', 'language', 'logos', 'headerLogo'));
    }

    public function deleteLanguage($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_languages')) {
            return redirect()->back()->with('error_message', 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Language::where('id', $id)->delete();
        return redirect()->back()->with('success_message', 'Language deleted successfully!');
        return view('admin.languages.languages', compact('languages', 'logos', 'headerLogo'));
    }
}
