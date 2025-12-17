<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function languages()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'languages');
        $languages = Language::get();
        return view('admin.languages.languages')->with(compact('languages', 'logos', 'headerLogo'));
    }

    public function updateLanguageStatus(Request $request)
    {
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
            return response()->json(['status' => $status, 'language_id' => $data['language_id']], 'logos');
            return view('admin.languages.languages', compact('languages', 'logos', 'headerLogo'));
        }
    }

    public function addEditLanguage(Request $request, $id = null)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($id == "") {
            $title = "Add Language";
            $language = new Language;
            $message = "Language added successfully!";
        } else {
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
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Language::where('id', $id)->delete();
        return redirect()->back()->with('success_message', 'Language deleted successfully!');
        return view('admin.languages.languages', compact('languages', 'logos', 'headerLogo'));
    }
}
