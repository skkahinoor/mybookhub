<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\BookRequestReply;
use App\Models\Notification;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BookRequestController extends Controller
{
    private function getMatchingVendorsForUser($user)
    {
        if (empty($user->pincode)) {
            return collect();
        }

        return Vendor::with(['user', 'vendorbusinessdetails'])
            ->where(function ($query) use ($user) {
                $query->whereHas('vendorbusinessdetails', function ($businessQuery) use ($user) {
                    $businessQuery->where('shop_pincode', $user->pincode);
                })->orWhereHas('user', function ($userQuery) use ($user) {
                    $userQuery->where('pincode', $user->pincode);
                });
            })
            ->get();
    }

    public function getMatchingVendors(Request $request)
    {
        $user = Auth::user();
        if (!$user->pincode) {
            return response()->json([
                'status' => false,
                'message' => 'Please update your pincode in profile before requesting a book.'
            ], 200);
        }

        $vendors = $this->getMatchingVendorsForUser($user);

        return response()->json([
            'status' => true,
            'message' => 'Matching vendors fetched successfully',
            'data' => $vendors
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'book_title' => 'required|string|max:255',
            'author_name' => 'nullable|string|max:255',
            'publisher_name' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
            'vendor_id' => ['required', Rule::exists('vendors', 'id')],
        ]);

        if (empty($user->pincode)) {
            return response()->json([
                'status' => false,
                'message' => 'Please update your pincode in profile before requesting a book.'
            ], 200);
        }

        $isVendorMatchedByPincode = Vendor::where('id', $request->vendor_id)
            ->where(function ($query) use ($user) {
                $query->whereHas('vendorbusinessdetails', function ($businessQuery) use ($user) {
                    $businessQuery->where('shop_pincode', $user->pincode);
                })->orWhereHas('user', function ($userQuery) use ($user) {
                    $userQuery->where('pincode', $user->pincode);
                });
            })
            ->exists();

        if (!$isVendorMatchedByPincode) {
            return response()->json([
                'status' => false,
                'message' => 'Selected vendor is not available for your pincode.'
            ], 200);
        }

        $bookRequest = BookRequest::create([
            'book_title' => $request->book_title,
            'author_name' => $request->author_name,
            'publisher_name' => $request->publisher_name,
            'message' => $request->message,
            'requested_by_user' => Auth::id(),
            'vendor_id' => $request->vendor_id,
        ]);

        Notification::create([
            'type' => 'book_request_submitted',
            'title' => 'Book request submitted',
            'message' => 'Your request for "' . $request->book_title . '" has been submitted. We will get back to you soon.',
            'related_id' => (int) Auth::id(),
            'related_type' => User::class,
            'is_read' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Your book request has been submitted!',
            'data' => $bookRequest
        ]);
    }

    public function index()
    {
        $queries = BookRequest::where('requested_by_user', Auth::id())
            ->with(['replies', 'vendor.user', 'vendor.vendorbusinessdetails'])
            ->withCount('replies')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Book requests fetched successfully',
            'data' => $queries
        ]);
    }

    public function show($id)
    {
        $query = BookRequest::where('requested_by_user', Auth::id())
            ->with(['replies', 'vendor.user', 'vendor.vendorbusinessdetails'])
            ->find($id);

        if (!$query) {
            return response()->json([
                'status' => false,
                'message' => 'Request not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Request details fetched successfully',
            'data' => $query
        ]);
    }

    public function replyToQuery(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:10',
        ]);

        $query = BookRequest::where('requested_by_user', Auth::id())->find($id);
        if (!$query) {
            return response()->json([
                'status' => false,
                'message' => 'Request not found.'
            ], 404);
        }

        $reply = BookRequestReply::create([
            'book_request_id' => $query->id,
            'reply_by' => 'user',
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Reply sent successfully!',
            'data' => $reply
        ]);
    }
}
