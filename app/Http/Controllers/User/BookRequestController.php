<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookRequest;
use App\Models\BookRequestReply;
use App\Models\HeaderLogo;
use App\Models\Language;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;

class BookRequestController extends Controller
{
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
        $request->validate([
            'book_title'  => 'required|string|max:255',
            'author_name' => 'nullable|string|max:255',
            'message'     => 'nullable|string|max:1000',
        ]);

        BookRequest::create([
            'book_title'        => $request->book_title,
            'author_name'       => $request->author_name,
            'message'           => $request->message,
            'requested_by_user' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Your book request has been submitted!');
    }

    /**
     * List requests in table with inline accordion.
     */
    public function indexbookrequest()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $requestedBooks = BookRequest::where('requested_by_user', Auth::id())
            ->with('replies')
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

    public function indexqueries()
    {
        $queries = BookRequest::where('requested_by_user', Auth::id())
            ->with('replies')
            ->orderBy('created_at', 'desc')
            ->get();
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.book.myqueries', compact('queries', 'logos', 'headerLogo'));
    }
}
