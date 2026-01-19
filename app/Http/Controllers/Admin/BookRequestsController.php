<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\BookRequestReply;
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
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.requestedbooks.index', compact('bookRequests', 'logos', 'headerLogo', 'adminType'));
    }

    public function reply(Request $request, $id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        if ($request->isMethod('post')) {
            $data = $request->all();

            // Get current book request
            $bookRequest = BookRequest::find($id);
            if (!$bookRequest) {
                return redirect()->back()->with('error_message', 'Book Request not found.');
            }

            // Validation rules
            $rules = [
                'status' => 'required|in:pending,in_progress,resolved',
            ];

            $customMessages = [
                'status.required' => 'Status is required',
            ];

            // Only require admin_reply if status is being changed to resolved or if providing a new reply
            if (!empty($data['admin_reply'])) {
                $rules['admin_reply'] = 'required|string|min:10';
                $customMessages['admin_reply.required'] = 'Reply message is required';
                $customMessages['admin_reply.min'] = 'Reply must be at least 10 characters';
            }

            $this->validate($request, $rules, $customMessages);

            // Update book request status and admin_reply field (for backward compatibility)
            $updateData = ['status' => $data['status']];
            if (!empty($data['admin_reply'])) {
                $updateData['admin_reply'] = $data['admin_reply'];
            }
            BookRequest::where('id', $id)->update($updateData);

            // Save admin reply to conversation thread if provided
            if (!empty($data['admin_reply'])) {
                BookRequestReply::create([
                    'book_request_id' => $id,
                    'reply_by' => 'admin',
                    'message' => $data['admin_reply'],
                ]);
            }

            if ($data['status'] == 'resolved') {
                return redirect('admin/requestedbooks')->with('success_message', 'Book request resolved successfully!');
            } else {
                return redirect()->back()->with('success_message', 'Reply updated successfully!');
            }
        }

        // GET request - show reply form
        $bookRequest = BookRequest::with('user', 'replies')->find($id);
        if (!$bookRequest) {
            return redirect()->back()->with('error_message', 'Book Request not found.');
        }
        $query = $bookRequest->toArray();
        return view('admin.requestedbooks.replay', compact('bookRequest', 'logos', 'headerLogo', 'query'));
    }

    public function updateStatus(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {
            $bookRequest = BookRequest::find($request->book_id);
            if ($bookRequest) {
                // Toggle between pending and in_progress
                if ($bookRequest->status == 'pending') {
                    $bookRequest->status = 'in_progress';
                } elseif ($bookRequest->status == 'in_progress') {
                    $bookRequest->status = 'pending';
                } else {
                    // If resolved, toggle back to pending
                    $bookRequest->status = 'pending';
                }
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
    public function delete($id)
    {
        $bookRequest = BookRequest::find($id);

        if (!$bookRequest) {
            return redirect()->back()->with('error_message', 'Book request not found!');
        }

        // ✅ Delete replies FIRST
        BookRequestReply::where('book_request_id', $id)->delete();

        // ✅ Now delete book request
        $bookRequest->delete();

        return redirect()->back()->with('success_message', 'Book request deleted successfully!');
    }
}
