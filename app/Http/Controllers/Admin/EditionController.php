<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Edition;
use App\Models\HeaderLogo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EditionController extends Controller
{
    public function index()
    {
        $adminType = Auth::guard('admin')->user()->type;
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'edition');
        $editions = Edition::all();
        return view('admin.edition.edition', compact('editions', 'logos', 'headerLogo', 'adminType'));
    }

    public function create()
    {
        $adminType = Auth::guard('admin')->user()->type;
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        return view('admin.edition.edition_create', compact('logos','headerLogo','adminType'));
    }

    public function store(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;
        $request->validate([
            'edition' => 'required|string|max:255',
        ]);
        Edition::create($request->only('edition'));
        return redirect()->route('admin.edition.index')->with('success', 'Edition created successfully.', 'logos');
        return view('admin.edition.edition', compact('editions', 'logos', 'headerLogo', 'adminType'));
    }

    public function edit($id)
    {
        $adminType = Auth::guard('admin')->user()->type;
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $edition = Edition::findOrFail($id);
        return view('admin.edition.edition_edit', compact('edition', 'logos', 'headerLogo', 'adminType'));
    }

    public function update(Request $request, $id)
    {
        $adminType = Auth::guard('admin')->user()->type;
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $request->validate([
            'edition' => 'required|string|max:255',
        ]);
        $edition = Edition::findOrFail($id);
        $edition->update($request->only('edition'));
        return redirect()->route('admin.edition.index')->with('success', 'Edition updated successfully.', 'logos');
        return view('admin.edition.edition', compact('editions', 'logos', 'headerLogo', 'adminType'));
    }

    public function destroy($id)
    {
        $adminType = Auth::guard('admin')->user()->type;
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $edition = Edition::findOrFail($id);
        $edition->delete();
        return redirect()->route('admin.edition.index')->with('success', 'Edition deleted successfully.', 'logos');
        return view('admin.edition.edition', compact('editions', 'logos', 'headerLogo', 'adminType'));
    }
}
