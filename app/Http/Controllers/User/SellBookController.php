<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookType;
use App\Models\Category;
use App\Models\Section;
use App\Models\ProductsFilter;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\ProductsImage;
use App\Models\Vendor;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Edition;
use App\Models\Notification;
use App\Models\OldBookCondition;
use App\Models\HeaderLogo;
use App\Models\Subcategory;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Http;
use App\Models\Language;

class SellBookController extends Controller
{
    /**
     * List all old book products added by the logged-in student user.
     */
    public function index()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $user_id = Auth::id();

        // Fetch products that this user has added attributes for
        $userProducts = Product::whereHas('attributes', function($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->with(['attributes' => function($q) use ($user_id) {
            $q->where('user_id', $user_id);
        }, 'category'])->orderBy('created_at', 'desc')->get();

        // Assuming user.sell-book.index can handle this $userProducts variable, or we update the view too.
        return view('user.sell-book.index', compact('userProducts', 'logos', 'headerLogo'));
    }

    /**
     * Show the add book form
     */
    public function create()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        // Replicating admin data fetching
        $title = "Sell Old Book";
        $product = new Product;
        
        $publishers = Publisher::where('status', 1)->get();
        $authors = Author::where('status', 1)->get();
        $sections = Section::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();
        $subcategories = Subcategory::where('status', 1)->get();
        $subjects = Subject::where('status', 1)->get();
        $editions = Edition::get();
        $languages = Language::where('status', 1)->get();
        $bookTypes = BookType::where('status', 1)->get();
        $conditions = OldBookCondition::orderBy('percentage', 'desc')->get();

        return view('user.sell-book.add_edit', compact('title', 'product', 'categories', 'subcategories', 'subjects', 'publishers', 'authors', 'sections', 'editions', 'logos', 'headerLogo', 'languages', 'bookTypes', 'conditions'));
    }

    /**
     * Show the edit book form
     */
    public function edit($id)
    {
        $user = Auth::user();
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $title = "Edit Old Book";
        
        // Find product
        $product = Product::with(['authors'])->find($id);
        if (!$product) {
            return redirect()->back()->with('error_message', 'Product not found.');
        }

        // Must have an attribute record for this user
        $attribute = ProductsAttribute::where('product_id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$attribute) {
            return redirect()->back()->with('error_message', 'You do not have permission to edit this product.');
        }

        $product->firstAttribute = $attribute; // Inject for easier view access

        $publishers = Publisher::where('status', 1)->get();
        $authors = Author::where('status', 1)->get();
        $sections = Section::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();
        $subcategories = Subcategory::where('status', 1)->get();
        $subjects = Subject::where('status', 1)->get();
        $editions = Edition::get();
        $languages = Language::where('status', 1)->get();
        $bookTypes = BookType::where('status', 1)->get();
        $conditions = OldBookCondition::orderBy('percentage', 'desc')->get();

        return view('user.sell-book.add_edit', compact('title', 'product', 'categories', 'subcategories', 'subjects', 'publishers', 'authors', 'sections', 'editions', 'logos', 'headerLogo', 'languages', 'bookTypes', 'conditions'));
    }

    public function update(Request $request, $id)
    {
        return $this->store($request, $id);
    }

    /**
     * Store or update the book
     */
    public function store(Request $request, $id = null)
    {
        try {
            $user = Auth::user();
        
        if ($id == "") {
            $product = new Product;
            $message = "Product added successfully!";
        } else {
            $product = Product::find($id);
            $message = "Product updated successfully!";
        }

        $request->validate([
            'section_id' => 'required|integer|exists:sections,id',
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'old_book_condition_id' => 'required|exists:old_book_conditions,id',
            'language_id' => 'required|exists:languages,id',
        ]);

        $data = $request->all();

        // Clean ISBN if provided
        $cleanIsbn = null;
        if (!empty($data['product_isbn'])) {
            $cleanIsbn = preg_replace('/[^0-9X]/i', '', $data['product_isbn']);
            if (strlen($cleanIsbn) != 10 && strlen($cleanIsbn) != 13) {
                if ($request->ajax()) {
                    return response()->json(['status' => false, 'message' => 'Invalid ISBN. It must be 10 or 13 characters.'], 422);
                }
                return redirect()->back()->with('error_message', 'Invalid ISBN. It must be 10 or 13 characters.')->withInput();
            }
        }

        // ==========================================
        // DEDUPLICATION: Check if product already exists globally
        // ==========================================
        $existingProduct = null;
        if (!empty($cleanIsbn) && empty($id)) {
            $existingProduct = Product::whereRaw("REPLACE(REPLACE(product_isbn, ' ', ''), '-', '') = ?", [$cleanIsbn])->first();
        }

        if ($existingProduct && empty($id)) {
            // ==========================================
            // AUTO-FILL CASE: Re-use existing global product
            // ==========================================
            $product = $existingProduct;
            $message = "Old book added successfully from existing records!";

            // Check if user already added this product
            $userHasIt = ProductsAttribute::where('product_id', $product->id)
                ->where('user_id', $user->id)
                ->first();

            if ($userHasIt) {
                if ($request->ajax()) {
                    return response()->json(['status' => false, 'message' => 'You have already added this old book.'], 422);
                }
                return redirect()->back()->with('error_message', 'You have already added this old book.');
            }
        } else {
            // ==========================================
            // MANUAL ENTRY OR UPDATE CASE
            // ==========================================
            // Only update product details if we are creating a new one or if we're allowed to edit
            $product->section_id       = $data['section_id'] ?? null;
            $product->category_id      = $data['category_id'] ?? null;
            $product->subcategory_id   = $data['subcategory_id'] ?? null;
            $product->subject_id       = $data['subject_id'] ?? null;
            $product->language_id      = $data['language_id'] ?? null;
            $product->publisher_id     = $data['publisher_id'] ?? null;
            
            // Image handling
            if ($request->hasFile('product_image')) {
                $image_tmp = $request->file('product_image');
                if ($image_tmp->isValid()) {
                    $image_name = pathinfo($image_tmp->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $image_tmp->getClientOriginalExtension();
                    $imageName = $image_name . '-' . rand(111, 99999) . '.' . $extension;
                    $largeImagePath = public_path('front/images/product_images/large/' . $imageName);
                    $mediumImagePath = public_path('front/images/product_images/medium/' . $imageName);
                    $smallImagePath = public_path('front/images/product_images/small/' . $imageName);
                    Image::make($image_tmp)->resize(1000, 1000)->save($largeImagePath);
                    Image::make($image_tmp)->resize(500, 500)->save($mediumImagePath);
                    Image::make($image_tmp)->resize(250, 250)->save($smallImagePath);
                    $product->product_image = $imageName;
                }
            }

            $product->condition        = 'old';
            $product->product_name     = $data['product_name'];
            $product->product_isbn     = $data['product_isbn'] ?? null;
            $product->product_price    = $data['product_price']; // This is the BASE/MRP price
            $product->edition_id       = $data['edition_id'] ?? null;
            $product->description      = $data['description'] ?? null;
            $product->meta_title       = $data['meta_title'] ?? null;
            $product->meta_keywords    = $data['meta_keywords'] ?? null;
            $product->meta_description = $data['meta_description'] ?? null;
            $product->book_type_id     = $data['book_type_id'] ?? null;
            $product->status           = 0; // Requires admin verification
            
            $product->save();
            
            if (!empty($request->author_id)) {
                $product->authors()->sync($request->author_id);
            }
        }

        // ==========================================
        // CREATE/UPDATE PRODUCT ATTRIBUTE FOR USER
        // ==========================================
        $attribute = ProductsAttribute::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attribute) {
            $attribute = new ProductsAttribute();
            $attribute->product_id = $product->id;
            $attribute->user_id = $user->id;
            $attribute->admin_type = 'user';
            $attribute->sku = 'BH-P' . $product->id . '-U' . $user->id;
        }

        $attribute->old_book_condition_id = $data['old_book_condition_id'] ?? null;
        $attribute->stock = 1;
        $attribute->product_discount = 0;
        $attribute->admin_approved = 0; // Requires verification
        $attribute->status = 0; // Hidden until approved

        // Calculate Price based on condition percentage
        if (!empty($data['old_book_condition_id'])) {
            $condition = OldBookCondition::find($data['old_book_condition_id']);
            if ($condition && $product->product_price > 0) {
                $attribute->price = ($product->product_price * $condition->percentage) / 100;
            } else {
                $attribute->price = $product->product_price;
            }
        } else {
            $attribute->price = $product->product_price;
        }

        $attribute->save();

        // Notification for admin
        if ($id == null) {
            Notification::create([
                'type' => 'product_added',
                'title' => 'New Product Added by User',
                'message' => "Student '{$user->name}' added a new old book '{$product->product_name}' (ISBN: {$product->product_isbn}).",
                'related_id' => $product->id,
                'related_type' => 'App\Models\Product',
                'vendor_id' => null,
                'is_read' => false,
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->route('student.sell-book.index')->with('success_message', $message . ' Awaiting admin verification.');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error_message', $e->getMessage())->withInput();
        }
    }

    /**
     * AJAX: Add publisher for student (no admin permission needed)
     */
    public function addPublisherAjax(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            // Check if already exists
            $existing = Publisher::where('name', $request->name)->first();
            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Publisher already exists.'
                ]);
            }

            $publisher = new Publisher();
            $publisher->name = $request->name;
            $publisher->status = 1; 
            $publisher->save();

            return response()->json([
                'status' => 'success',
                'id' => $publisher->id,
                'name' => $publisher->name
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid request.']);
    }

    public function getBookByIsbn(Request $request)
    {
        $isbn = $request->input('isbn');
        if (empty($isbn)) {
             return response()->json(['status' => false, 'message' => 'ISBN is required']);
        }
        $cleanSearch = preg_replace('/[^0-9X]/i', '', $isbn);
        
        $product = Product::with(['publisher', 'edition', 'authors', 'category', 'subcategory', 'subject'])
            ->where('product_isbn', $isbn)
            ->first();

        if (!$product && strlen($cleanSearch) > 0) {
            $product = Product::with(['publisher', 'edition', 'authors', 'category', 'subcategory', 'subject'])
                ->where(function($query) use ($isbn, $cleanSearch) {
                    $query->where('product_isbn', 'like', "%{$isbn}%")
                          ->orWhere('product_isbn', 'like', "%{$cleanSearch}%")
                          ->orWhereRaw("REPLACE(REPLACE(product_isbn, ' ', ''), '-', '') = ?", [$cleanSearch]);
                })
                ->first();
        }

        if ($product) {
            return response()->json([
                'status' => true,
                'source' => 'local',
                'data' => [
                    'product_name' => $product->product_name,
                    'author_ids' => $product->authors->pluck('id'),
                    'publisher_id' => $product->publisher_id ?? '',
                    'edition_id' => $product->edition_id ?? '',
                    'subject_id' => $product->subject_id,
                    'subject_name' => $product->subject->name ?? '',
                    'language_id' => $product->language_id,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category->category_name ?? '',
                    'subcategory_id' => $product->subcategory_id,
                    'subcategory_name' => $product->subcategory->subcategory_name ?? '',
                    'section_id' => $product->section_id,
                    'book_type_id' => $product->book_type_id,
                    'product_price' => $product->product_price,
                    'description' => $product->description,
                    'image' => $product->product_image,
                ]
            ]);
        }

        // External API Lookup (ISBNDB)
        $key = config('services.isbn.key');
        if (!$key) {
             return response()->json(['status' => false, 'message' => 'Book not found and API key missing.']);
        }

        try {
            $response = Http::withHeaders(['Authorization' => $key])
                ->get("https://api2.isbndb.com/book/$isbn");

            if ($response->successful() && isset($response['book'])) {
                $book = $response['book'];

                // Prepare master record
                $publisher_id = null;
                if (!empty($book['publisher'])) {
                    $publisher = Publisher::firstOrCreate(['name' => $book['publisher']], ['status' => 1]);
                    $publisher_id = $publisher->id;
                }

                $subject_id = null;
                if (!empty($book['subjects'][0])) {
                    $subject = Subject::firstOrCreate(['name' => $book['subjects'][0]], ['status' => 1]);
                    $subject_id = $subject->id;
                }

                $author_ids = [];
                if (!empty($book['authors'])) {
                    foreach ($book['authors'] as $name) {
                        $author = Author::firstOrCreate(['name' => $name], ['status' => 1]);
                        $author_ids[] = $author->id;
                    }
                }

                // We don't necessarily want to create the product here if we want the student to confirm,
                // but if we want autofill to work smoothly like admin, we can provide this data.
                return response()->json([
                    'status' => true,
                    'source' => 'isbndb',
                    'data' => [
                        'product_name' => $book['title'] ?? '',
                        'product_isbn' => $isbn,
                        'description'  => $book['synopsis'] ?? '',
                        'product_price' => $book['msrp'] ?? 0,
                        'image_url'     => $book['image'] ?? null,
                        'publisher_id'  => $publisher_id,
                        'subject_id'    => $subject_id,
                        'author_ids'    => $author_ids,
                    ]
                ]);
            }
        } catch (\Exception $e) {
            // Log or ignore
        }

        return response()->json(['status' => false, 'message' => 'Book not found. Please enter details manually.']);
    }

    public function nameSuggestions(Request $request)
    {
        $query = $request->input('query');
        if (!$query || strlen($query) < 2) {
             return response()->json(['status' => true, 'data' => []]);
        }

        $books = Product::where('product_name', 'LIKE', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'product_name', 'product_isbn']);

        return response()->json([
            'status' => true,
            'data'   => $books
        ]);
    }

    /**
     * AJAX: Get boards (categories) for a given education level (section).
     */
    public function getBoards(Request $request)
    {
        $sectionId = $request->query('section_id');
        if (!$sectionId) return response()->json([]);

        $boards = Category::where('status', 1)
            ->where('section_id', $sectionId)
            ->select('id', 'category_name')
            ->orderBy('category_name')
            ->get();

        return response()->json($boards);
    }

    /**
     * AJAX: Get classes (subcategories) for given section + board.
     */
    public function getClasses(Request $request)
    {
        $sectionId  = $request->query('section_id');
        $categoryId = $request->query('category_id');

        if (!$sectionId || !$categoryId) return response()->json([]);

        $classes = DB::table('filter_class_subject')
            ->join('subcategories', 'filter_class_subject.sub_category_id', '=', 'subcategories.id')
            ->where('filter_class_subject.section_id', $sectionId)
            ->where('filter_class_subject.category_id', $categoryId)
            ->select('subcategories.id', 'subcategories.subcategory_name')
            ->distinct()
            ->orderBy('subcategories.subcategory_name')
            ->get();

        return response()->json($classes);
    }

    /**
     * AJAX: Get subjects for given section + board + class.
     */
    public function getSubjects(Request $request)
    {
        $sectionId     = $request->query('section_id');
        $categoryId    = $request->query('category_id');
        $subCategoryId = $request->query('sub_category_id');

        if (!$sectionId || !$categoryId || !$subCategoryId) return response()->json([]);

        $subjects = DB::table('filter_class_subject')
            ->join('subjects', 'filter_class_subject.subject_id', '=', 'subjects.id')
            ->where('filter_class_subject.section_id', $sectionId)
            ->where('filter_class_subject.category_id', $categoryId)
            ->where('filter_class_subject.sub_category_id', $subCategoryId)
            ->select('subjects.id', 'subjects.name')
            ->distinct()
            ->orderBy('subjects.name')
            ->get();

        return response()->json($subjects);
    }
}
