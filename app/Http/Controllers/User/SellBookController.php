<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SellBookRequest;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SellBookController extends Controller
{
    /**
     * Show the sell book request form
     */
    public function create()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.sell-book.create', compact('logos', 'headerLogo'));
    }

    /**
     * Store the initial sell book request
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_title' => 'required|string|max:255',
            'author_name' => 'nullable|string|max:255',
            'request_message' => 'nullable|string|max:1000',
        ]);

        SellBookRequest::create([
            'user_id' => Auth::id(),
            'book_title' => $request->book_title,
            'author_name' => $request->author_name,
            'request_message' => $request->request_message,
            'request_status' => 'pending',
        ]);

        return redirect()->route('user.sell-book.index')
            ->with('success_message', 'Your request to sell book has been submitted. Please wait for admin approval.');
    }

    /**
     * List all sell book requests for the logged-in user
     */
    public function index()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $requests = SellBookRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.sell-book.index', compact('requests', 'logos', 'headerLogo'));
    }

    /**
     * Show a specific sell book request
     */
    public function show($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request = SellBookRequest::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('user.sell-book.show', compact('request', 'logos', 'headerLogo'));
    }

    /**
     * Show the book details form (only if request is approved)
     */
    public function edit($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request = SellBookRequest::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($request->request_status !== 'approved') {
            return redirect()->route('user.sell-book.show', $id)
                ->with('error_message', 'Your request must be approved by admin before you can fill book details.');
        }

        if ($request->hasBookDetails()) {
            return redirect()->route('user.sell-book.show', $id)
                ->with('info_message', 'Book details have already been submitted.');
        }

        return view('user.sell-book.edit', compact('request', 'logos', 'headerLogo'));
    }

    /**
     * Update book details after approval
     */
    public function update(Request $request, $id)
    {
        $sellRequest = SellBookRequest::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    
        // Check approval
        if ($sellRequest->request_status !== 'approved') {
            return redirect()->back()
                ->with('error_message', 'Your request must be approved by admin first.');
        }
    
        // Validation
        $request->validate([
            'isbn'             => 'required|string|max:255',
            'publisher'        => 'nullable|string|max:255',
            'edition'          => 'nullable|string|max:100',
            'year_published'   => 'nullable|integer|min:1900|max:' . date('Y'),
            'book_condition'   => 'required|string|in:Excellent,Good,Fair,Poor',
            'book_description' => 'nullable|string|max:2000',
            'expected_price'   => 'required|numeric|min:0|max:999999.99',
            'book_image'       => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);
    
        $data = $request->except('book_image');
    
        // ==========================
        // Image Upload (Public Folder)
        // ==========================
        if ($request->hasFile('book_image')) {
    
            $image = $request->file('book_image');
    
            if ($image->isValid()) {
    
                $imageName = 'sell-book-' . time() . '-' . rand(1000, 99999) . '.' . $image->getClientOriginalExtension();
                $relativePath = 'user/images/sell-books';
                $fullPath = public_path($relativePath);
    
                // Create directory if not exists
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }
    
                // Delete old image
                if ($sellRequest->book_image && file_exists(public_path($sellRequest->book_image))) {
                    unlink(public_path($sellRequest->book_image));
                }
    
                // Resize & Save image
                Image::make($image)
                    ->resize(500, 500, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save($fullPath . '/' . $imageName);
    
                // Save relative path to DB
                $data['book_image'] = $relativePath . '/' . $imageName;
            }
        }
    
        // Set book status after update
        $data['book_status'] = 'pending_review';
    
        // Update record
        $sellRequest->update($data);
    
        return redirect()
            ->route('user.sell-book.show', $id)
            ->with('success_message', 'Book details submitted successfully! Admin will review and update the status.');
    }
}

