<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\BookRequestReply;
use App\Models\HeaderLogo;
use App\Models\Notification;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BookRequestsController extends Controller
{
    private function getVendorDistrictIdForAdmin($adminUser)
    {
        if ($adminUser->type !== 'vendor' || empty($adminUser->vendor_id)) {
            return null;
        }

        $vendor = Vendor::with('user')->find($adminUser->vendor_id);

        return $vendor?->user?->district_id;
    }

    private function canVendorAccessRequest($adminUser, BookRequest $bookRequest): bool
    {
        if ($adminUser->type !== 'vendor') {
            return true;
        }

        if ((int) $bookRequest->vendor_id === (int) $adminUser->vendor_id) {
            return true;
        }

        if (is_null($bookRequest->vendor_id)) {
            $vendorDistrictId = $this->getVendorDistrictIdForAdmin($adminUser);
            return !empty($vendorDistrictId) && (int) $bookRequest->district_id === (int) $vendorDistrictId;
        }

        return false;
    }

    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'bookRequests');
        $adminType = Auth::guard('admin')->user()->type;
        $adminVendorId = Auth::guard('admin')->user()->vendor_id;
        $vendorDistrictId = $this->getVendorDistrictIdForAdmin(Auth::guard('admin')->user());

        $bookRequestsQuery = BookRequest::with(['user', 'vendor.user', 'vendor.vendorbusinessdetails']);
        if ($adminType === 'vendor') {
            $bookRequestsQuery->where('vendor_id', $adminVendorId);
        }
        $bookRequests = $bookRequestsQuery->orderBy('id', 'desc')->get();

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

            if (Auth::guard('admin')->user()->type === 'vendor' && (int) $bookRequest->vendor_id !== (int) Auth::guard('admin')->user()->vendor_id) {
                return redirect()->back()->with('error_message', 'You are not authorized to reply to this request.');
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
                $rules['admin_reply'] = 'required|string|min:1';
                $customMessages['admin_reply.required'] = 'Reply message is required';
                $customMessages['admin_reply.min'] = 'Reply must be at least 1 character';
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
                    'vendor_id' => Auth::guard('admin')->user()->type === 'vendor' ? Auth::guard('admin')->user()->vendor_id : null,
                    'message' => $data['admin_reply'],
                ]);

                // Notify the student who raised the request
                if (!empty($bookRequest->requested_by_user)) {
                    Notification::create([
                        'type' => 'book_request_reply',
                        'title' => 'Book request update',
                        'message' => 'Admin replied to your book request: ' . ($bookRequest->book_title ?? 'your request'),
                        'related_id' => (int) $bookRequest->requested_by_user,
                        'related_type' => User::class,
                        'is_read' => false,
                    ]);
                }
            }

            if ($data['status'] == 'resolved') {
                return redirect('admin/requestedbooks')->with('success_message', 'Book request resolved successfully!');
            } else {
                return redirect()->back()->with('success_message', 'Reply updated successfully!');
            }
        }

        // GET request - show reply form
        $adminType = Auth::guard('admin')->user()->type;
        $adminVendorId = Auth::guard('admin')->user()->vendor_id;

        $bookRequest = BookRequest::with(['user', 'replies' => function($q) use ($adminType, $adminVendorId) {
            if ($adminType === 'vendor') {
                $q->where('vendor_id', $adminVendorId);
            }
        }, 'replies.vendor.vendorbusinessdetails'])->find($id);

        if (!$bookRequest) {
            return redirect()->back()->with('error_message', 'Book Request not found.');
        }
        if (Auth::guard('admin')->user()->type === 'vendor' && (int) $bookRequest->vendor_id !== (int) Auth::guard('admin')->user()->vendor_id) {
            return redirect()->route('vendor.requestbook.index')->with('error_message', 'You are not authorized to view this request.');
        }
        $query = $bookRequest->toArray();

        $adminType = Auth::guard('admin')->user()->type;

        return view('admin.requestedbooks.replay', compact('bookRequest', 'logos', 'headerLogo', 'query', 'adminType'));
    }

    public function updateStatus(Request $request)
    {
        if ($request->ajax()) {
            $bookRequest = BookRequest::find($request->book_id);
            if ($bookRequest) {
                if (Auth::guard('admin')->user()->type === 'vendor' && (int) $bookRequest->vendor_id !== (int) Auth::guard('admin')->user()->vendor_id) {
                    return response()->json(['error' => 'Unauthorized action.'], 403);
                }
                // Toggle status: if pending or 0, change to in_progress; otherwise change to pending
                if ($bookRequest->status == 'pending' || $bookRequest->status == '0' || $bookRequest->status == 0) {
                    $bookRequest->status = 'in_progress';
                    $status = 1;
                } else {
                    $bookRequest->status = 'pending';
                    $status = 0;
                }
                $bookRequest->save();

                return response()->json([
                    'status' => $status,
                    'book_id' => $bookRequest->id,
                    'message' => 'Status updated successfully.',
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

        if (Auth::guard('admin')->user()->type === 'vendor' && (int) $bookRequest->vendor_id !== (int) Auth::guard('admin')->user()->vendor_id) {
            return redirect()->back()->with('error_message', 'You are not authorized to delete this request.');
        }

        // ✅ Delete replies FIRST
        BookRequestReply::where('book_request_id', $id)->delete();

        // ✅ Now delete book request
        $bookRequest->delete();

        return redirect()->back()->with('success_message', 'Book request deleted successfully!');
    }
}
