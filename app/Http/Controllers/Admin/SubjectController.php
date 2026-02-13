<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SubjectController extends Controller
{

    public function index()
    {
        if (!Auth::guard('admin')->user()->can('view_subjects')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;
        $subjects = Subject::orderBy('id', 'desc')->get();
        Session::put('page', 'subjects');
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.subject.subject', compact('subjects', 'logos', 'headerLogo', 'adminType'));
    }

    public function add()
    {
        if (!Auth::guard('admin')->user()->can('add_subjects')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.subject.add_subject', compact('logos', 'headerLogo', 'adminType'));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('add_subjects')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $store = Subject::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Subject inserted successfully');
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
        return view('admin.subject.edit_subject', compact('subjects', 'logos', 'headerLogo', 'adminType'));
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
        ]);
        $update = Subject::find($request->id);
        $update->update([
            'name' => $request->name,
        ]);
        return redirect()->route('admin.subject')->with('success', 'Subject updated successfully');
        return view('admin.subject.subject', compact('subjects', 'logos', 'headerLogo'));
    }

    public function delete($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_subjects')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $delete = Subject::find($id);
        $delete->delete();
        return redirect()->back()->with('success', 'Subject deleted successfully', 'logos');
        return view('admin.subject.subject', compact('subjects', 'logos', 'headerLogo'));
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
}
