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
    private function getUserActivePincode($user)
    {
        // 1. Try to get default address
        $defaultAddress = $user->addresses()->where('is_default', 1)->first();
        if ($defaultAddress) {
            return $defaultAddress->pincode;
        }

        // 2. Try to get latest active address
        $latestAddress = $user->addresses()->latest()->first();
        if ($latestAddress) {
            return $latestAddress->pincode;
        }

        // 3. Fallback to user model pincode (legacy)
        return $user->pincode;
    }

    private function getMatchingVendorsForPincode($pincode)
    {
        if (empty($pincode)) {
            return collect();
        }

        return Vendor::with(['user', 'vendorbusinessdetails'])
            ->where(function ($query) use ($pincode) {
                $query->whereHas('vendorbusinessdetails', function ($businessQuery) use ($pincode) {
                    $businessQuery->where('shop_pincode', $pincode);
                })->orWhereHas('user', function ($userQuery) use ($pincode) {
                    $userQuery->where('pincode', $pincode);
                });
            })
            ->get();
    }

    public function getMatchingVendors(Request $request)
    {
        $user = Auth::user();
        $pincode = $this->getUserActivePincode($user);

        if (!$pincode) {
            return response()->json([
                'status' => false,
                'message' => 'Please update your address or pincode in your profile before requesting a book.'
            ], 200);
        }

        $vendors = $this->getMatchingVendorsForPincode($pincode);

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

        $pincode = $this->getUserActivePincode($user);

        if (empty($pincode)) {
            return response()->json([
                'status' => false,
                'message' => 'Please update your address or pincode in your profile before requesting a book.'
            ], 200);
        }

        $isVendorMatchedByPincode = Vendor::where('id', $request->vendor_id)
            ->where(function ($query) use ($pincode) {
                $query->whereHas('vendorbusinessdetails', function ($businessQuery) use ($pincode) {
                    $businessQuery->where('shop_pincode', $pincode);
                })->orWhereHas('user', function ($userQuery) use ($pincode) {
                    $userQuery->where('pincode', $pincode);
                });
            })
            ->exists();

        if (!$isVendorMatchedByPincode) {
            return response()->json([
                'status' => false,
                'message' => 'Selected vendor is not available for your area.'
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
