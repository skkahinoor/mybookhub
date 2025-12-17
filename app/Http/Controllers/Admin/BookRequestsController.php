<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BookRequestsController extends Controller
{
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'bookRequests');
        $bookRequests = BookRequest::with('user')->get();
        return view('admin.requestedbooks.index', compact('bookRequests', 'logos', 'headerLogo'));
    }

    public function delete($id)
{
    $headerLogo = HeaderLogo::first();
    $logos = HeaderLogo::first();
    $bookRequests = BookRequest::find($id);

    if (!$bookRequests) {
        return redirect()->back()->with('error', 'Book Request not found.');
    }

    $bookRequests->delete();

    return redirect()->back()->with('success', 'Book Request deleted successfully.');
}

    public function updateStatus(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {
            $bookRequest = BookRequest::find($request->book_id);
            if ($bookRequest) {
                $bookRequest->status = $bookRequest->status == 1 ? 0 : 1;
                $bookRequest->save();
                return response()->json([
                    'status' => $bookRequest->status,
                    'book_id' => $bookRequest->id,
                    'message' => 'Status updated successfully.'
                ]);
            }
            return response()->json(['error' => 'Book Request not found.'], 404);
        }
    }
}



