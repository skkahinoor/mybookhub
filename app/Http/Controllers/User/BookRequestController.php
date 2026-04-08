<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\BookRequestReply;
use App\Models\HeaderLogo;
use App\Models\Notification;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Product;
use App\Models\Section;
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

    /**
     * User-side search page with "request a book" form.
     */
    public function index(Request $request)
    {
        $condition = session('condition', 'new');
        $sections  = Section::all();
        $logos     = HeaderLogo::all();
        $headerLogo = HeaderLogo::first();
        $language  = Language::get();
        $query = Product::with(['publisher', 'authors'])
            ->where('status', 1);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('product_isbn', 'like', '%' . $search . '%');
            });
        }
        $products = $query->paginate(12)->appends($request->query());
        return view('user.book.indexbookrequest', compact('products', 'logos', 'sections', 'language', 'condition', 'headerLogo'));
    }

    /**
     * Store a new book request from the user.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'book_title'     => 'required|string|max:255',
            'author_name'    => 'nullable|string|max:255',
            'publisher_name' => 'nullable|string|max:255',
            'message'        => 'nullable|string|max:1000',
            'vendor_id'      => ['required', Rule::exists('vendors', 'id')],
        ]);

        if (empty($user->pincode)) {
            return redirect()->back()->with('error', 'Please update your pincode in profile before requesting a book.');
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
            return redirect()->back()->with('error', 'Selected vendor is not available for your pincode.');
        }

        $bookRequest = BookRequest::create([
            'book_title'        => $request->book_title,
            'author_name'       => $request->author_name,
            'publisher_name'    => $request->publisher_name,
            'message'           => $request->message,
            'requested_by_user' => Auth::id(),
            'vendor_id'         => $request->vendor_id,
        ]);

        Notification::create([
            'type' => 'book_request_submitted',
            'title' => 'Book request submitted',
            'message' => 'Your request for "' . $request->book_title . '" has been submitted. We will get back to you soon.',
            'related_id' => (int) Auth::id(),
            'related_type' => User::class,
            'is_read' => false,
        ]);

        return redirect()->route('student.query.index', ['query_id' => $bookRequest->id])
            ->with('success', 'Your book request has been submitted!');
    }

    /**
     * List requests in table with inline accordion.
     */
    public function indexbookrequest()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $requestedBooks = BookRequest::where('requested_by_user', Auth::id())
            ->with(['replies', 'vendor.user', 'vendor.vendorbusinessdetails'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.book.indexbookrequest', compact('requestedBooks', 'logos', 'headerLogo'));
    }

    public function replyToQuery(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:10',
        ], [
            'message.required' => 'Reply message is required',
            'message.min' => 'Reply must be at least 10 characters',
        ]);
        $query = BookRequest::find($id);
        if (!$query) {
            return redirect()->back()->with('error', 'Query not found.');
        }

        BookRequestReply::create([
            'book_request_id' => $query->id,
            'reply_by'        => 'user',
            'message'         => $request->message,
        ]);

        return redirect()->back()->with('success', 'Reply sent successfully!');
    }

    public function indexqueries(Request $request)
    {
        $queries = BookRequest::where('requested_by_user', Auth::id())
            ->with(['replies', 'vendor.user', 'vendor.vendorbusinessdetails'])
            ->orderBy('created_at', 'desc')
            ->get();
        $selectedQueryId = (int) $request->query('query_id', 0);
        $selectedQuery = null;
        if ($queries->isNotEmpty()) {
            $selectedQuery = $selectedQueryId > 0
                ? $queries->firstWhere('id', $selectedQueryId)
                : $queries->first();
        }

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.book.myqueries', compact('queries', 'logos', 'headerLogo', 'selectedQueryId', 'selectedQuery'));
    }

    public function raiseQueryPage()
    {
        $user = Auth::user();
        $matchingVendors = $this->getMatchingVendorsForUser($user);
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        return view('user.book.raisequery', compact('matchingVendors', 'logos', 'headerLogo'));
    }
}
