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
    private function getUserActiveDistrictId($user)
    {
        // 1. Try to get default address
        $defaultAddress = $user->addresses()->where('is_default', 1)->first();
        if ($defaultAddress && $defaultAddress->district_id) {
            return $defaultAddress->district_id;
        }

        // 2. Try to get latest active address
        $latestAddress = $user->addresses()->latest()->first();
        if ($latestAddress && $latestAddress->district_id) {
            return $latestAddress->district_id;
        }

        // 3. Fallback to user model district_id
        return $user->district_id;
    }

    private function getMatchingVendorsForDistrict($districtId)
    {
        if (empty($districtId)) {
            return collect();
        }

        return Vendor::with(['user', 'vendorbusinessdetails'])
            ->whereHas('user', function ($userQuery) use ($districtId) {
                $userQuery->where('district_id', $districtId);
            })
            ->get();
    }

    public function getMatchingVendors(Request $request)
    {
        $user = Auth::user();
        $districtId = $this->getUserActiveDistrictId($user);

        if (!$districtId) {
            return response()->json([
                'status' => false,
                'message' => 'Please update your address or district in your profile before requesting a book.'
            ], 200);
        }

        $vendors = $this->getMatchingVendorsForDistrict($districtId);

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
        ]);

        $districtId = $this->getUserActiveDistrictId($user);

        if (empty($districtId)) {
            return response()->json([
                'status' => false,
                'message' => 'Please update your address or district in your profile before requesting a book.'
            ], 200);
        }

        $bookRequest = BookRequest::create([
            'book_title' => $request->book_title,
            'author_name' => $request->author_name,
            'publisher_name' => $request->publisher_name,
            'message' => $request->message,
            'requested_by_user' => Auth::id(),
            'district_id' => $districtId,
            'vendor_id' => null, // Broadcast to all vendors in district
        ]);

        Notification::create([
            'type' => 'book_request_submitted',
            'title' => 'Book request broadcasted',
            'message' => 'Your request for "' . $request->book_title . '" has been sent to all vendors in your district.',
            'related_id' => (int) Auth::id(),
            'related_type' => User::class,
            'is_read' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Your book request has been broadcasted to all vendors in your district!',
            'data' => $bookRequest
        ]);
    }

    public function index()
    {
        $queries = BookRequest::where('requested_by_user', Auth::id())
            ->with(['replies.vendor.user', 'replies.vendor.vendorbusinessdetails'])
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
            ->with(['replies.vendor.user', 'replies.vendor.vendorbusinessdetails'])
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
            'message' => 'required|string|min:1',
            'vendor_id' => 'nullable|exists:vendors,id',
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
            'vendor_id' => $request->vendor_id,
            'reply_by' => 'user',
            'message' => $request->message,
            'is_ended' => $request->is_ended ?? false,
        ]);

        // If the user marks the conversation as ended, update the status to available (meaning resolved)
        if ($request->is_ended) {
            $query->update(['status' => 'available']);
        } else {
            // If it's a student reply, we might want to set status back to awaiting_response
            // but only if it's currently vendor_replied
            if ($query->status === 'vendor_replied') {
                $query->update(['status' => 'awaiting_response']);
            }
        }

        // Notify the specific vendor if targeted
        if ($request->vendor_id) {
            $vendor = Vendor::find($request->vendor_id);
            if ($vendor && $vendor->user_id) {
                Notification::create([
                    'type' => 'book_request_reply',
                    'title' => 'New reply from student',
                    'message' => 'Student replied to your message for: ' . ($query->book_title ?? 'requested book'),
                    'related_id' => (int) $vendor->user_id,
                    'related_type' => User::class,
                    'is_read' => false,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Reply sent successfully!',
            'data' => $reply
        ]);
    }
}
