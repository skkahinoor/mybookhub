<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::orderBy('id','desc')->get();
        Session::put('page', 'authors');
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('admin.authors.author', compact('authors', 'logos', 'headerLogo'));
    }

    public function add()
    {

        return view('admin.authors.add_author', compact('logos', 'headerLogo'));
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
        return redirect()->route('author')->with('success', 'Author name inserted successfully!!', 'logos', 'headerLogo');
        return view('admin.authors.author', compact('authors', 'logos', 'headerLogo'));
    }

    public function edit($id)
    {
        $authors = Author::find($id);
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        return view('admin.authors.edit_author', compact('authors', 'logos', 'headerLogo'));
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
        return redirect()->route('author')->with('success', 'Author name updated successfully!!', 'logos', 'headerLogo');
        return view('admin.authors.author', compact('authors', 'logos', 'headerLogo'));
        
    }

    public function delete($id){
        $delete=Author::find($id);
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $delete->delete();
        return redirect()->back()->with('success', 'Author name deleted successfully!!', 'logos');
        return view('admin.authors.author', compact('authors', 'logos', 'headerLogo'));
    }
}
