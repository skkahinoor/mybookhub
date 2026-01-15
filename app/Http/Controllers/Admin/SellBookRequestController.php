<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellBookRequest;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SellBookRequestController extends Controller
{
    /**
     * Display all sell book requests
     */
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'sellBookRequests');
        
        $requests = SellBookRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.sell-book-requests.index', compact('requests', 'logos', 'headerLogo'));
    }

    /**
     * Show a specific sell book request
     */
    public function show($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        
        $request = SellBookRequest::with('user')->findOrFail($id);
        
        return view('admin.sell-book-requests.show', compact('request', 'logos', 'headerLogo'));
    }

    /**
     * Approve or reject the initial request
     */
    public function updateRequestStatus(Request $request, $id)
    {
        $sellRequest = SellBookRequest::findOrFail($id);
        
        $request->validate([
            'request_status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $sellRequest->update([
            'request_status' => $request->request_status,
            'admin_notes' => $request->admin_notes,
        ]);

        $statusText = ucfirst($request->request_status);
        return redirect()->back()
            ->with('success_message', "Request status updated to {$statusText} successfully!");
    }

    /**
     * Update book status (after book details are submitted)
     */
    public function updateBookStatus(Request $request, $id)
    {
        $sellRequest = SellBookRequest::findOrFail($id);
        
        $request->validate([
            'book_status' => 'required|in:pending_review,approved,rejected,sold',
            'final_admin_notes' => 'nullable|string|max:1000',
        ]);

        $sellRequest->update([
            'book_status' => $request->book_status,
            'final_admin_notes' => $request->final_admin_notes,
        ]);

        $statusText = ucfirst(str_replace('_', ' ', $request->book_status));
        return redirect()->back()
            ->with('success_message', "Book status updated to {$statusText} successfully!");
    }

    /**
     * Delete a sell book request
     */
    public function destroy($id)
    {
        $sellRequest = SellBookRequest::findOrFail($id);
        
        // Delete book image if exists
        if ($sellRequest->book_image && file_exists(public_path($sellRequest->book_image))) {
            unlink(public_path($sellRequest->book_image));
        }
        
        $sellRequest->delete();
        
        return redirect()->route('admin.sell-book-requests.index')
            ->with('success_message', 'Sell book request deleted successfully!');
    }
}

