<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\BookRequestReply;
use App\Models\HeaderLogo;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Services\FirebaseService;

class BookRequestsController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'bookRequests');
        $adminType = Auth::guard('admin')->user()->type;
        $adminVendorId = Auth::guard('admin')->user()->vendor_id;

        $bookRequestsQuery = BookRequest::with(['user', 'vendor.user', 'vendor.vendorbusinessdetails']);
        if ($adminType === 'vendor') {
            $vendor = \App\Models\Vendor::with('user')->find($adminVendorId);
            $districtId = $vendor->user->district_id ?? null;
            
            $bookRequestsQuery->where(function($q) use ($adminVendorId, $districtId) {
                $q->where('vendor_id', $adminVendorId);
                if ($districtId) {
                    $q->orWhere(function($sq) use ($districtId) {
                        $sq->whereNull('vendor_id')->where('district_id', $districtId);
                    });
                }
            })->whereDoesntHave('replies', function($q) use ($adminVendorId) {
                $q->where('vendor_id', $adminVendorId)->where('is_ended', true);
            });
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

            if (Auth::guard('admin')->user()->type === 'vendor') {
                $adminVendorId = Auth::guard('admin')->user()->vendor_id;
                $vendor = \App\Models\Vendor::with('user')->find($adminVendorId);
                $districtId = $vendor->user->district_id ?? null;

                $isAuthorized = (int) $bookRequest->vendor_id === (int) $adminVendorId || 
                              ($bookRequest->vendor_id === null && (int) $bookRequest->district_id === (int) $districtId);

                if (!$isAuthorized) {
                    return redirect()->back()->with('error_message', 'You are not authorized to reply to this request.');
                }

                // Check if conversation ended
                $isEnded = BookRequestReply::where('book_request_id', $id)
                    ->where('vendor_id', $adminVendorId)
                    ->where('is_ended', true)
                    ->exists();
                if ($isEnded) {
                    return redirect()->back()->with('error_message', 'The student has ended this conversation.');
                }
            }

            // Validation rules
            $rules = [
                'status' => 'required|in:awaiting_response,vendor_replied,available,not_available',
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

            // Update book request status and admin_reply field
            $updateData = ['status' => $data['status']];
            if (!empty($data['admin_reply'])) {
                $updateData['admin_reply'] = $data['admin_reply'];
            }

            // Only associate the vendor with the main request if they confirm it's available
            if (Auth::guard('admin')->user()->type === 'vendor' && $data['status'] === 'available') {
                $updateData['vendor_id'] = Auth::guard('admin')->user()->vendor_id;
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

                // Update status to 'vendor_replied' if it was 'awaiting_response'
                if ($bookRequest->status === 'awaiting_response') {
                    BookRequest::where('id', $id)->update(['status' => 'vendor_replied']);
                }

                // Notify the student who raised the request
                if (!empty($bookRequest->requested_by_user)) {
                    // Save to database (shows in in-app notification page)
                    Notification::create([
                        'type' => 'book_request_reply',
                        'title' => 'Book request update',
                        'message' => 'Admin replied to your book request: ' . ($bookRequest->book_title ?? 'your request'),
                        'related_id' => (int) $bookRequest->requested_by_user,
                        'related_type' => User::class,
                        'is_read' => false,
                    ]);

                    // Send FCM push notification to the user's device
                    try {
                        $shopName = 'BookHub';
                        if (Auth::guard('admin')->user()->type === 'vendor') {
                            $vendor = \App\Models\Vendor::with('vendorbusinessdetails')->find(Auth::guard('admin')->user()->vendor_id);
                            $shopName = $vendor->vendorbusinessdetails->shop_name ?? Auth::guard('admin')->user()->name;
                        }

                        $pushTitle = 'New reply for your book request';
                        $pushBody = "{$shopName} replied regarding '" . ($bookRequest->book_title ?? 'your request') . "': " . \Str::limit($data['admin_reply'], 60);

                        $this->firebaseService->sendToUsers(
                            [(int) $bookRequest->requested_by_user],
                            $pushTitle,
                            $pushBody,
                            [
                                'type' => 'book_request_reply',
                                'request_id' => (string) $bookRequest->id,
                            ],
                            true // Skip DB notification — already created above
                        );
                    } catch (\Exception $e) {
                        \Log::error('FCM push failed for book request reply: ' . $e->getMessage());
                    }
                }
            }

            if ($data['status'] == 'available' || $data['status'] == 'not_available') {
                $msg = $data['status'] == 'available' ? 'Book confirmed as available!' : 'Book marked as not available.';
                return redirect('admin/requestedbooks')->with('success_message', $msg);
            } else {
                return redirect()->back()->with('success_message', 'Reply updated successfully!');
            }
        }

        // GET request - show reply form
        $adminType = Auth::guard('admin')->user()->type;
        $adminVendorId = Auth::guard('admin')->user()->vendor_id;

        $bookRequest = BookRequest::with([
            'user',
            'replies' => function ($q) use ($adminType, $adminVendorId) {
                if ($adminType === 'vendor') {
                    $q->where(function ($sq) use ($adminVendorId) {
                        $sq->where('vendor_id', $adminVendorId)
                            ->orWhereNull('vendor_id');
                    });
                }
            },
            'replies.vendor.vendorbusinessdetails',
            'replies.vendor.user'
        ])->find($id);

        if (!$bookRequest) {
            return redirect()->back()->with('error_message', 'Book Request not found.');
        }

        if ($adminType === 'vendor') {
            $vendor = \App\Models\Vendor::with('user')->find($adminVendorId);
            $districtId = $vendor->user->district_id ?? null;

            $isAuthorized = (int) $bookRequest->vendor_id === (int) $adminVendorId || 
                          ($bookRequest->vendor_id === null && (int) $bookRequest->district_id === (int) $districtId);

            if (!$isAuthorized) {
                return redirect()->route('admin.requestedbooks.index')->with('error_message', 'You are not authorized to view this request.');
            }

            // Check if conversation has ended for this vendor
            $isEnded = collect($bookRequest->replies)->where('vendor_id', $adminVendorId)->where('is_ended', true)->count() > 0;
            if ($isEnded) {
                return redirect()->route('admin.requestedbooks.index')->with('error_message', 'The student has ended this conversation.');
            }
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
                if (Auth::guard('admin')->user()->type === 'vendor') {
                    $adminVendorId = Auth::guard('admin')->user()->vendor_id;
                    $vendor = \App\Models\Vendor::with('user')->find($adminVendorId);
                    $districtId = $vendor->user->district_id ?? null;

                    $isAuthorized = (int) $bookRequest->vendor_id === (int) $adminVendorId || 
                                  ($bookRequest->vendor_id === null && (int) $bookRequest->district_id === (int) $districtId);

                    if (!$isAuthorized) {
                        return response()->json(['error' => 'Unauthorized action.'], 403);
                    }
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

        if (Auth::guard('admin')->user()->type === 'vendor') {
            $adminVendorId = Auth::guard('admin')->user()->vendor_id;
            $vendor = \App\Models\Vendor::with('user')->find($adminVendorId);
            $districtId = $vendor->user->district_id ?? null;

            $isAuthorized = (int) $bookRequest->vendor_id === (int) $adminVendorId || 
                          ($bookRequest->vendor_id === null && (int) $bookRequest->district_id === (int) $districtId);

            if (!$isAuthorized) {
                return redirect()->back()->with('error_message', 'You are not authorized to delete this request.');
            }
        }

        // ✅ Delete replies FIRST
        BookRequestReply::where('book_request_id', $id)->delete();

        // ✅ Now delete book request
        $bookRequest->delete();

        return redirect()->back()->with('success_message', 'Book request deleted successfully!');
    }
}