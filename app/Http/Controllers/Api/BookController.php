<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Models
use App\Models\Product;
use App\Models\Publisher;
use App\Models\Subject;
use App\Models\Edition;
use App\Models\Author;
use App\Models\Language;

class BookController extends Controller
{
    public function lookupByIsbn(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|max:20',
        ]);

        $isbn = $request->isbn;

        /* ======================================================
         * 1️⃣ First check in LOCAL DATABASE (products table)
         * ====================================================== */
        $product = Product::with(['publisher','subject','edition','language','authors'])
            ->where('product_isbn', $isbn)
            ->first();

        if ($product) {
            return response()->json([
                "status" => true,
                "message" => "Book found in local database",
                "source" => "local",
                "data" => [
                    "product_id"    => $product->id,
                    "title"         => $product->product_name,
                    "description"   => $product->description,
                    "image"         => $product->product_image,
                    "price"         => $product->product_price,

                    "publisher_id"  => $product->publisher_id,
                    "subject_id"    => $product->subject_id,
                    "edition_id"    => $product->edition_id,
                    "language_id"   => $product->language_id,
                    "author_ids"    => $product->authors->pluck('id'),
                ]
            ]);
        }

        /* ======================================================
         * 2️⃣ Fetch from ISBNdb API if not found locally
         * ====================================================== */
        $key  = config('services.isbn.key');

        $response = Http::withHeaders([
            'Authorization' => $key
        ])->get("https://api2.isbndb.com/book/$isbn");

        if ($response->failed() || !isset($response['book'])) {
            return response()->json([
                "status" => false,
                "message" => "Book not found in local DB or external API",
                "isbn" => $isbn
            ], 404);
        }

        $book = $response['book'];

        /* ======================================================
         * 3️⃣ Auto-create or Auto-find related tables
         * ====================================================== */

        // 🔹 Publisher
        $publisher_id = null;
        if (!empty($book['publisher'])) {
            $publisher = Publisher::firstOrCreate(
                ['name' => $book['publisher']],
                ['status' => 1]
            );
            $publisher_id = $publisher->id;
        }

        // 🔹 Subject
        $subject_id = null;
        if (!empty($book['subjects'][0])) {
            $subject = Subject::firstOrCreate(
                ['name' => $book['subjects'][0]],
                ['status' => 1]
            );
            $subject_id = $subject->id;
        }

        // 🔹 Edition
        $edition_id = null;
        if (!empty($book['edition'])) {
            $edition = Edition::firstOrCreate(
                ['edition' => $book['edition']],
                ['status' => 1]
            );
            $edition_id = $edition->id;
        }

        // 🔹 Language
        $language_id = null;
        if (!empty($book['language'])) {
            $language = Language::firstOrCreate(
                ['name' => $book['language']],
                ['status' => 1]
            );
            $language_id = $language->id;
        }

        // 🔹 Authors
        $author_ids = [];
        if (!empty($book['authors'])) {
            foreach ($book['authors'] as $name) {
                $author = Author::firstOrCreate(
                    ['name' => $name],
                    ['status' => 1]
                );
                $author_ids[] = $author->id;
            }
        }

        /* ======================================================
         * 4️⃣ Return structured result for App Developer
         * ====================================================== */
        return response()->json([
            "status" => true,
            "message" => "Book found from ISBNdb",
            "source" => "isbndb",

            "data" => [
                "title"       => $book['title'] ?? null,
                "description" => $book['synopsis'] ?? null,
                "image"       => $book['image'] ?? null,
                "pages"       => $book['pages'] ?? null,
                "published_date" => $book['date_published'] ?? null,
                "price"       => $book['msrp'] ?? null,

                // IDs for your products table
                "publisher_id" => $publisher_id,
                "subject_id"   => $subject_id,
                "edition_id"   => $edition_id,
                "language_id"  => $language_id,
                "author_ids"   => $author_ids,
            ],

            "raw_api" => $book
        ]);
    }
}
