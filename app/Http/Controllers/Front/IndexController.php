<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\HeaderLogo;
use App\Models\Language;
use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Author;
use App\Models\Cart;
use App\Models\ProductsAttribute;
use App\Models\FilterClassSubject;
use App\Models\Subcategory;
use App\Models\Subject;
use App\Models\BookType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $sliderBanners  = Banner::where('type', 'Slider')->where('status', 1)->get()->toArray();
        $fixBanners     = Banner::where('type', 'Fix')->where('status', 1)->get()->toArray();
        $condition      = session('condition', 'new');
        $sliderProductsQuery = ProductsAttribute::with([
            'product:id,product_name,product_isbn,product_image,product_price,category_id,section_id,condition',
            'product.authors',
            'vendor.vendorbusinessdetails',
            'ratings:id,product_attribute_id,rating'
        ])
            ->where('status', 1);

        if ($request->filled('section_id')) {
            $sliderProductsQuery->whereHas('product', function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }

        if ($request->filled('category_id')) {
            $sliderProductsQuery->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('subcategory_id')) {
            $sliderProductsQuery->whereHas('product', function ($q) use ($request) {
                $q->where('subcategory_id', $request->subcategory_id);
            });
        }

        if ($request->filled('condition')) {
            $sliderProductsQuery->whereHas('product', function ($q) use ($request) {
                if ($request->condition !== 'all') {
                    $q->where('condition', $request->condition);
                }
            });
        } elseif ($condition !== 'all') {
            $sliderProductsQuery->whereHas('product', function ($q) use ($condition) {
                $q->where('condition', $condition);
            });
        }

        if ($request->filled('subject_id')) {
            $sliderProductsQuery->whereHas('product', function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        // --- NEW FILTERS ---
        // 1. Book Types (Multiple)
        if ($request->filled('book_types')) {
            $bookTypeIds = is_array($request->book_types) ? $request->book_types : explode(',', (string)$request->book_types);
            $sliderProductsQuery->whereHas('product', function ($q) use ($bookTypeIds) {
                $q->whereIn('book_type_id', $bookTypeIds);
            });
        }

        // 2. Languages (Multiple)
        if ($request->filled('languages')) {
            $langIds = is_array($request->languages) ? $request->languages : explode(',', (string)$request->languages);
            $sliderProductsQuery->whereHas('product', function ($q) use ($langIds) {
                $q->whereIn('language_id', $langIds);
            });
        }

        // 3. Distance Range
        $userLat = session('user_latitude');
        $userLng = session('user_longitude');
        if ($request->filled('distance') && $userLat && $userLng) {
            $distance = (int)$request->distance;
            if ($distance < 100) { // 100+ means show all
                $sliderProductsQuery->whereHas('vendor', function ($q) use ($userLat, $userLng, $distance) {
                    $q->whereNotNull('location')
                        ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(SUBSTRING_INDEX(location, ',', 1))) * cos(radians(SUBSTRING_INDEX(location, ',', -1)) - radians(?)) + sin(radians(?)) * sin(radians(SUBSTRING_INDEX(location, ',', 1))))) <= ?", [$userLat, $userLng, $userLat, $distance]);
                });
            }
        }

        $sliderProducts = $sliderProductsQuery->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        /* SUBJECTS */
        $homeSubjects = collect([]);
        if ($request->filled('subcategory_id')) {
            $subjectIds = FilterClassSubject::where('sub_category_id', $request->subcategory_id)
                ->pluck('subject_id');
            $homeSubjects = Subject::whereIn('id', $subjectIds)->where('status', 1)->get();
        } else {
            // Default subjects for the initial load
            $homeSubjects = Subject::where('status', 1)->limit(10)->get();
        }


        // // Get 'condition' from query string (default to 'new' if not set or invalid)
        // $condition = $request->query('condition');
        // if (!in_array($condition, ['new', 'old'])) {
        //     $condition = 'new';
        // }

        $logos    = HeaderLogo::first();
        $language = Language::get();
        $sections = Section::all();
        // $newProducts = Product::with(['authors', 'publisher'])
        //     ->when($condition !== 'all', function ($query) use ($condition) {
        //         $query->where('condition', $condition);
        //     })
        //     ->when(session('language') && session('language') !== 'all', function ($query) {
        //         $query->where('language_id', session('language'));
        //     })
        //     ->where('status', 1)
        //     ->orderBy('id', 'desc')
        //     ->get();

        $newProducts = ProductsAttribute::with(['product.authors', 'product.publisher', 'product.edition'])
            ->whereHas('product', function ($query) use ($condition) {
                $query->where('status', 1)
                    ->when($condition !== 'all', function ($q) use ($condition) {
                        $q->where('condition', $condition);
                    })
                    ->when(session('language') && session('language') !== 'all', function ($q) {
                        $q->where('language_id', session('language'));
                    });
            })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->paginate(8);

        $slidingProducts = ProductsAttribute::with(['product.authors', 'product.publisher', 'product.edition'])
            ->whereHas('product', function ($query) use ($condition) {
                $query->where('status', 1)
                    ->when($condition !== 'all', function ($q) use ($condition) {
                        $q->where('condition', $condition);
                    })
                    ->when(session('language') && session('language') !== 'all', function ($q) {
                        $q->where('language_id', session('language'));
                    });
            })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $category = Category::limit(10)->get();

        $footerProducts = ProductsAttribute::with('product')
            ->whereHas('product', function ($query) use ($condition) {
                $query->where('status', 1)
                    ->when($condition !== 'all', function ($q) use ($condition) {
                        $q->where('condition', $condition);
                    });
            })
            ->where('status', 1)
            ->orderBy('id', 'Desc')
            ->take(3)
            ->get()
            ->toArray();

        // Best Sellers - now from ProductsAttribute table
        $bestSellers = ProductsAttribute::with([
            'product' => function ($query) use ($condition) {
                $query->where('status', 1)
                    ->when($condition !== 'all', function ($q) use ($condition) {
                        $q->where('condition', $condition);
                    });
            }
        ])
            ->where('is_bestseller', 'Yes')
            ->where('status', 1)
            ->whereHas('product', function ($query) use ($condition) {
                $query->where('status', 1)
                    ->when($condition !== 'all', function ($q) use ($condition) {
                        $q->where('condition', $condition);
                    });
            })
            ->inRandomOrder()
            ->get()
            ->toArray();

        // Discounted Products - now from ProductsAttribute table
        $discountedProducts = ProductsAttribute::with([
            'product.authors',
            'ratings:id,product_attribute_id,rating'
        ])
            ->where('status', 1)
            ->where('product_discount', '>=', 20)
            ->whereHas('product', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('product_discount', 'desc')
            ->get();


        // Featured Products - now from ProductsAttribute table
        $featuredProducts = ProductsAttribute::with([
            'product.authors',
            'ratings:id,product_attribute_id,rating'
        ])
            ->where('is_featured', 'Yes')
            ->where('status', 1)
            ->whereHas('product', function ($query) use ($condition) {
                $query->where('status', 1)
                    ->when($condition !== 'all', function ($q) use ($condition) {
                        $q->where('condition', $condition);
                    })
                    ->when(session('language') && session('language') !== 'all', function ($q) {
                        $q->where('language_id', session('language'));
                    });
            })
            ->limit(10)
            ->get();


        $meta_title       = 'BookHub - The Only Hub For Students';
        $meta_description = 'The cross platform where students meets their career through books.';
        $meta_keywords    = 'eshop website, online shopping, multi vendor e-commerce';

        // Get total user count for dynamic statistics
        $totalUsers = User::role('student', 'web')->count();

        // Get total vendor count for dynamic statistics
        $totalVendors = User::role('vendor', 'web')->count();

        // Get total product count for dynamic statistics
        $totalProducts = Product::where('status', 1)->count();

        // Get total author count for dynamic statistics
        $totalAuthors = Author::where('status', 1)->count();

        $getCartItems = Cart::getCartItems();

        // Calculate total price
        $total_price = 0;
        foreach ($getCartItems as $item) {
            $getDiscountPriceDetails = \App\Models\Product::getDiscountPriceDetails($item['product_id']);
            $total_price += $getDiscountPriceDetails['final_price'] * $item['quantity'];
        }

        if ($request->ajax()) {
            if ($request->has('filter_update')) {
                return response()->json([
                    'html' => view('front.partials.home_product_grid', compact('sliderProducts'))->render(),
                    'subjects_html' => view('front.partials.home_subjects', compact('homeSubjects'))->render(),
                    'info' => (isset($request->info) ? $request->info : '')
                ]);
            }
            return view('front.partials.new_products', compact('newProducts'))->render();
        }

        return view('front.index3', [
            'languages'        => Language::where('status', 1)->get(),
            'bookTypes'        => BookType::where('status', 1)->get(),
            'selectedLanguage' => Language::find(session('language')),

        ])->with(compact(
            'sliderBanners',
            'fixBanners',
            'newProducts',
            'footerProducts',
            'bestSellers',
            'discountedProducts',
            'featuredProducts',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'condition',
            'category',
            'sections',
            'language',
            'logos',
            'sliderProducts',
            'slidingProducts',
            'homeSubjects',
            'totalUsers',
            'totalVendors',
            'totalProducts',
            'totalAuthors',
            'getCartItems',
            'total_price'
        ));
    }

    public function setLanguage(Request $request)
    {
        session(['language' => $request->language]);
        return response()->json(['success' => true]);
    }

    public function setCondition(Request $request)
    {
        session(['condition' => $request->condition]);
        return response()->json(['success' => true]);
    }

    public function getFilterCategories(Request $request)
    {
        $categoryIds = FilterClassSubject::where('section_id', $request->section_id)
            ->distinct()
            ->pluck('category_id');

        $categories = Category::whereIn('id', $categoryIds)
            ->where('status', 1)
            ->get(['id', 'category_name']);

        return response()->json($categories);
    }

    public function getFilterSubcategories(Request $request)
    {
        $subcategoryIds = FilterClassSubject::where('section_id', $request->section_id)
            ->where('category_id', $request->category_id)
            ->distinct()
            ->pluck('sub_category_id');

        $subcategories = Subcategory::whereIn('id', $subcategoryIds)
            ->where('status', 1)
            ->get(['id', 'subcategory_name as category_name']); // using aliased name for frontend compatibility

        return response()->json($subcategories);
    }

    public function searchProducts(Request $request)
    {
        $condition = session('condition', 'new');

        $query = ProductsAttribute::with([
            'product.publisher',
            'product.authors',
            'product.category',
            'vendor.vendorbusinessdetails',
        ])
            ->where('status', 1)
            ->whereHas('product', fn($q) => $q->where('status', 1));

        /* CONDITION */
        if ($request->filled('condition')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('condition', $request->condition)
            );
        } elseif ($condition !== 'all') {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('condition', $condition)
            );
        }

        /* LANGUAGE */
        if ($request->filled('language_id') && $request->language_id !== 'all') {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('language_id', $request->language_id)
            );
        } elseif (session('language') && session('language') !== 'all') {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('language_id', session('language'))
            );
        }

        /* SEARCH */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('product_isbn', 'like', "%{$search}%")
                    ->orWhereHas(
                        'category',
                        fn($c) =>
                        $c->where('category_name', 'like', "%{$search}%")
                    );
            });
        }

        /* SECTION */
        if ($request->filled('section_id')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('section_id', $request->section_id)
            );
        }

        /* CATEGORY */
        if ($request->filled('category_id')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('category_id', $request->category_id)
            );
        }

        /* SUBCATEGORY (CLASS) */
        if ($request->filled('subcategory_id')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('subcategory_id', $request->subcategory_id)
            );
        }

        /* SUBJECT */
        if ($request->filled('subject_id')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('subject_id', $request->subject_id)
            );
        }

        /* FETCH */
        $products = $query->get();

        /* PRICE FILTER (VENDOR DISCOUNT SAFE) */
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minPrice = (float) ($request->min_price ?? 0);
            $maxPrice = (float) ($request->max_price ?? PHP_FLOAT_MAX);

            $products = $products->filter(function ($attr) use ($minPrice, $maxPrice) {
                $price = Product::getDiscountPrice($attr->product_id, $attr->vendor_id);
                return $price >= $minPrice && $price <= $maxPrice;
            });
        }

        /* PAGINATION */
        $perPage = 12;
        $page    = request('page', 1);

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $sections = Section::all();
        $category = Category::limit(10)->get();
        $language = Language::all();
        $logos    = HeaderLogo::first();

        $footerProducts = Product::where('status', 1)
            ->orderByDesc('id')
            ->take(3)
            ->get()
            ->toArray();

        return view(
            'front.products.search',
            compact(
                'products',
                'condition',
                'sections',
                'footerProducts',
                'category',
                'language',
                'logos'
            ),
            [
                'languages'        => Language::all(),
                'selectedLanguage' => Language::find(session('language')),
            ]
        );
    }
}
