<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::orderBy('id','desc')->get();
        Session::put('page', 'authors');
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.authors.author', compact('authors', 'logos', 'headerLogo', 'adminType'));
    }

    public function add()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.authors.add_author', compact('logos', 'headerLogo', 'adminType'));
    }

    public function store(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $store = Author::create([
            'name' => $request->name,
        ]);
        return redirect()->back()->with('success', 'Author name inserted successfully!!');
        // return view('admin.authors.author', compact('authors', 'logos', 'headerLogo', 'adminType'));
    }

    public function edit($id)
    {
        $authors = Author::find($id);
        $adminType = Auth::guard('admin')->user()->type;
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        return view('admin.authors.edit_author', compact('authors', 'logos', 'headerLogo', 'adminType'));
    }

    public function update(Request $request){
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $update = Author::find($request->id);
        $update->update([
            'name'=> $request->name,
        ]);
        return redirect()->route('admin.author')->with('success', 'Author name updated successfully!!');
        return view('admin.authors.author', compact('authors', 'logos', 'headerLogo', 'adminType'));
        
    }

    public function delete($id){
        $delete=Author::find($id);
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $delete->delete();
        return redirect()->back()->with('success', 'Author name deleted successfully!!', 'logos');
        return view('admin.authors.author', compact('authors', 'logos', 'headerLogo', 'adminType'));
    }

    /**
     * Toggle author status (active/inactive) via AJAX.
     */
    public function updateStatus(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $data = $request->validate([
            'status'    => 'required|in:Active,Inactive,0,1',
            'author_id' => 'required|exists:authors,id',
        ]);

        $current = $data['status'];
        $status = ($current === 'Active' || $current === '1' || $current === 1) ? 0 : 1;

        Author::where('id', $data['author_id'])->update(['status' => $status]);

        return response()->json([
            'status'    => $status,
            'author_id' => $data['author_id'],
        ]);
    }
}
