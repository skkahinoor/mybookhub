<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookRequest;
use Illuminate\Support\Facades\Auth;

class BookRequestController extends Controller
{
    public function index()
    {
        // You can return a view or just a placeholder for now
        return view('front.requestedbooks.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_title' => 'required|string|max:255',
            'author_name' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
            'user_location' => 'nullable|string|max:100',
            'user_location_name' => 'nullable|string|max:255',
        ]);

        BookRequest::create([
            'book_title' => $request->book_title,
            'author_name' => $request->author_name,
            'message' => $request->message,
            'requested_by_user' => Auth::id(),
            'user_location' => $request->user_location,
            'user_location_name' => $request->user_location_name,
        ]);

        return redirect()->back()->with('success', 'Your book request has been submitted!');
    }
}

