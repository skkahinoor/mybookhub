<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SubjectController extends Controller
{

    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $subjects = Subject::orderBy('id','desc')->get();
        Session::put('page', 'subjects');
        return view('admin.subject.subject', compact('subjects', 'logos', 'headerLogo'));
    }

    public function add()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        return view('admin.subject.add_subject', compact('logos', 'headerLogo'));
    }

    public function store(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $store = Subject::create([
            'name' => $request->name,
        ]);
        return redirect()->route('subject')->with('success', 'Subject inserted successfully', 'logos');
        return view('admin.subject.subject', compact('subjects', 'logos', 'headerLogo'));
    }

    public function edit($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $subjects = Subject::find($id);
        return view('admin.subject.edit_subject', compact('subjects', 'logos', 'headerLogo'));
    }

    public function update(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $update = Subject::find($request->id);
        $update->update([
            'name' => $request->name,
        ]);
        return redirect()->route('subject')->with('success', 'Subject updated successfully', 'logos');
        return view('admin.subject.subject', compact('subjects', 'logos', 'headerLogo'));
    }

    public function delete($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $delete = Subject::find($id);
        $delete->delete();
        return redirect()->back()->with('success', 'Subject deleted successfully', 'logos');
        return view('admin.subject.subject', compact('subjects', 'logos', 'headerLogo'));
    }
}
