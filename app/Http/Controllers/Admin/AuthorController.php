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
    public function index(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('view_authors')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $data = Author::orderBy('id', 'desc');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    if ($adminType === 'vendor') {
                        return '<a class="updateAuthorStatus" id="author-' . $row->id . '"
                                    author_id="' . $row->id . '"
                                    data-url="' . route('vendor.updateauthorstatus') . '"
                                    href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                        status="' . $statusText . '"></i>
                                </a>';
                    } else {
                        return '<a class="updateAuthorStatus" id="author-' . $row->id . '"
                                    author_id="' . $row->id . '"
                                    data-url="' . route('admin.updateauthorstatus') . '"
                                    href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                        status="' . $statusText . '"></i>
                                </a>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    if ($adminType === 'vendor') {
                        $editUrl = route('vendor.edit.author', $row->id);
                        $deleteUrl = route('vendor.delete.author', $row->id);
                    } else {
                        $editUrl = route('admin.edit.author', $row->id);
                        $deleteUrl = route('admin.delete.author', $row->id);
                    }
                    return '<a href="' . $editUrl . '">
                                <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                            </a>
                            <a href="javascript:void(0)" class="confirmDelete"
                                data-module="author"
                                data-url="' . $deleteUrl . '">
                                <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                            </a>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        Session::put('page', 'authors');
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.authors.author', compact('logos', 'headerLogo', 'adminType'));
    }

    public function add()
    {
        if (!Auth::guard('admin')->user()->can('add_authors')) {
            abort(403, 'Unauthorized action.');
        }
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.authors.add_author', compact('logos', 'headerLogo', 'adminType'));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('add_authors')) {
            abort(403, 'Unauthorized action.');
        }
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $store = Author::create([
            'name' => $request->name,
        ]);
        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType === 'vendor' ? 'vendor.author' : 'admin.author';
        return redirect()->route($route)->with('success_message', 'Author name inserted successfully!!');
        // return view('admin.authors.author', compact('authors', 'logos', 'headerLogo', 'adminType'));
    }

    public function edit($id)
    {
        if (!Auth::guard('admin')->user()->can('edit_authors')) {
            abort(403, 'Unauthorized action.');
        }
        $authors = Author::find($id);
        $adminType = Auth::guard('admin')->user()->type;
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        return view('admin.authors.edit_author', compact('authors', 'logos', 'headerLogo', 'adminType'));
    }

    public function update(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('edit_authors')) {
            abort(403, 'Unauthorized action.');
        }
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $update = Author::find($request->id);
        $update->update([
            'name' => $request->name,
        ]);
        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType === 'vendor' ? 'vendor.author' : 'admin.author';
        return redirect()->route($route)->with('success_message', 'Author name updated successfully!!');
        return view('admin.authors.author', compact('authors', 'logos', 'headerLogo', 'adminType'));
    }

    public function delete($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_authors')) {
            abort(403, 'Unauthorized action.');
        }

        $author = Author::find($id);

        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType === 'vendor' ? 'vendor.author' : 'admin.author';

        // If author not found, just redirect with an error instead of 404
        if (!$author) {
            return redirect()
                ->route($route)
                ->with('error_message', 'Author not found or already deleted.');
        }

        $author->delete();

        return redirect()
            ->route($route)
            ->with('success_message', 'Author name deleted successfully!!');
    }
    /**
     * Toggle author status (active/inactive) via AJAX.
     */
    public function updateStatus(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('edit_authors')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }
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
