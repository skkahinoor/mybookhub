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
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $sliderBanners  = Banner::where('type', 'Slider')->where('status', 1)->get()->toArray();
        $fixBanners     = Banner::where('type', 'Fix')->where('status', 1)->get()->toArray();
        $condition      = session('condition', 'all');

        $sessionSectionId = $request->cookie('bg_section_id') ?? session('bg_section_id');
        $sessionCategoryId = $request->cookie('bg_category_id') ?? session('bg_category_id');
        $sessionSubcategoryId = $request->cookie('bg_subcategory_id') ?? session('bg_subcategory_id');
        $sessionSubjectId = $request->cookie('bg_subject_id') ?? session('bg_subject_id');

        // Defaults from session or request
        $currentSectionId = $request->filled('section_id') ? $request->section_id : $sessionSectionId;
        $currentCategoryId = $request->filled('category_id') ? $request->category_id : $sessionCategoryId;
        $currentSubcategoryId = $request->filled('subcategory_id') ? $request->subcategory_id : $sessionSubcategoryId;
        $currentSubjectId = $request->filled('subject_id') ? $request->subject_id : $sessionSubjectId;

        // Overlay with Academic Profile for registered users if not explicitly specified by current request or session
        if (Auth::check() && !$request->filled('section_id') && !$sessionSectionId) {
            $profile = \App\Models\AcademicProfile::where('user_id', Auth::id())->first();
            if ($profile) {
                // Pre-fill only the fields that are currently empty in session/request
                if (!$currentSectionId) {
                    $currentSectionId = $profile->education_level_id;
                    // Update session too if we want to reflect it
                }
                if (!$currentCategoryId) {
                    $currentCategoryId = $profile->board_id;
                }
                if (!$currentSubcategoryId) {
                    $currentSubcategoryId = $profile->class_id;
                }
            }
        }

        // Persist distance in session
        if ($request->filled('distance')) {
            session(['distance' => (int)$request->distance]);
        }
        $currentDistance = session('distance', 10);

        $sliderProductsQuery = Product::with([
                'authors',
                'publisher'
            ])
            ->whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->where('status', 1);

        if ($currentSectionId) {
            $sliderProductsQuery->where('section_id', $currentSectionId);
        }

        if ($currentCategoryId) {
            $sliderProductsQuery->where('category_id', $currentCategoryId);
        }

        if ($currentSubcategoryId) {
            $sliderProductsQuery->where('subcategory_id', $currentSubcategoryId);
        }

        if ($request->filled('condition')) {
            if ($request->condition !== 'all') {
                $sliderProductsQuery->where('condition', $request->condition);
            }
        } elseif ($condition !== 'all') {
            $sliderProductsQuery->where('condition', $condition);
        }

        if ($currentSubjectId) {
            $sliderProductsQuery->where('subject_id', $currentSubjectId);
        }

        // --- NEW FILTERS ---
        // 1. Book Types (Multiple)
        if ($request->filled('book_types')) {
            $bookTypeIds = is_array($request->input('book_types')) ? $request->input('book_types') : explode(',', (string)$request->input('book_types'));
            $sliderProductsQuery->whereIn('book_type_id', $bookTypeIds);
        }

        // 2. Languages (Multiple)
        if ($request->filled('languages')) {
            $langIds = is_array($request->input('languages')) ? $request->input('languages') : explode(',', (string)$request->input('languages'));
            $sliderProductsQuery->whereIn('language_id', $langIds);
        }

        // 3. Distance Range
        $userLat = session('user_latitude');
        $userLng = session('user_longitude');
        if ($userLat && $userLng) {
            $distance = $currentDistance;
            if ($distance < 100) { // 100+ means show all
                $sliderProductsQuery->whereHas('attributes.vendor', function ($q) use ($userLat, $userLng, $distance) {
                    $q->whereNotNull('location')
                        ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(SUBSTRING_INDEX(location, ',', 1))) * cos(radians(SUBSTRING_INDEX(location, ',', -1)) - radians(?)) + sin(radians(?)) * sin(radians(SUBSTRING_INDEX(location, ',', 1))))) <= ?", [$userLat, $userLng, $userLat, $distance]);
                });
            }
        }

        $sliderProducts = $sliderProductsQuery->orderBy('id', 'desc')
            ->paginate(12);

        /* SUBJECTS */
        $homeSubjects = collect([]);
        if ($currentSectionId || $currentCategoryId || $currentSubcategoryId) {
            $subjectIdsQuery = FilterClassSubject::query();
            if ($currentSectionId) {
                $subjectIdsQuery->where('section_id', $currentSectionId);
            }
            if ($currentCategoryId) {
                $subjectIdsQuery->where('category_id', $currentCategoryId);
            }
            if ($currentSubcategoryId) {
                $subjectIdsQuery->where('sub_category_id', $currentSubcategoryId);
            }
            $subjectIds = $subjectIdsQuery->distinct()->pluck('subject_id');
            $homeSubjects = Subject::whereIn('id', $subjectIds)->where('status', 1)->get();
        } else {
            // Default subjects for the initial load
            $homeSubjects = Subject::where('status', 1)->limit(20)->get();
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

        $newProducts = Product::with(['authors', 'publisher'])
            ->whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->when(session('language') && session('language') !== 'all', function ($query) {
                $query->where('language_id', session('language'));
            })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        $slidingProducts = Product::with(['authors', 'publisher'])
            ->whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->when(session('language') && session('language') !== 'all', function ($query) {
                $query->where('language_id', session('language'));
            })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $category = Category::limit(10)->get();

        $footerProducts = Product::whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->where('status', 1)
            ->orderBy('id', 'Desc')
            ->take(3)
            ->get();

        // Best Sellers
        $bestSellers = Product::whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->when($condition !== 'all', function ($q) use ($condition) {
                $q->where('condition', $condition);
            })
            ->where('status', 1)
            ->inRandomOrder()
            ->get();

        // Discounted Products
        $discountedProducts = Product::with(['authors'])
            ->whereHas('attributes', function($q) {
                $q->where('status', 1)->where('product_discount', '>=', 20);
            })
            ->where('status', 1)
            ->when($condition !== 'all', function ($q) use ($condition) {
                $q->where('condition', $condition);
            })
            ->get();

        // Featured Products
        $featuredProducts = Product::with(['authors'])
            ->whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->where('status', 1)
            ->when($condition !== 'all', function ($q) use ($condition) {
                $q->where('condition', $condition);
            })
            ->when(session('language') && session('language') !== 'all', function ($q) {
                $q->where('language_id', session('language'));
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

        // Fetch Student Old Books for Marketplace Section
        $sellBookRequests = \App\Models\ProductsAttribute::with(['product', 'user'])
            ->where('admin_type', 'user')
            ->where('admin_approved', 1)
            ->whereHas('product', function($q) {
                $q->where('condition', 'old');
            })
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        if ($request->ajax()) {
            if ($request->has('filter_update')) {
                return response()->json([
                    'html' => view('front.partials.home_product_grid', compact('sliderProducts'))->render(),
                    'subjects_html' => view('front.partials.home_subjects', compact('homeSubjects'))->render(),
                    'has_more' => $sliderProducts->hasMorePages(),
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
            'total_price',
            'sellBookRequests',
            'currentSectionId',
            'currentCategoryId',
            'currentSubcategoryId',
            'currentSubjectId'
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

    public function setBookgenieSession(Request $request)
    {
        $minutes = 525600 * 5; // 5 years

        if ($request->has('bookgenie_shown')) {
            session(['bookgenie_shown' => true]);
            \Illuminate\Support\Facades\Cookie::queue('bookgenie_shown', 'true', $minutes);
        }

        if ($request->filled('section_id')) {
            session(['bg_section_id' => $request->section_id]);
            \Illuminate\Support\Facades\Cookie::queue('bg_section_id', (string)$request->section_id, $minutes);
        }
        if ($request->filled('category_id')) {
            session(['bg_category_id' => $request->category_id]);
            \Illuminate\Support\Facades\Cookie::queue('bg_category_id', (string)$request->category_id, $minutes);
        }
        if ($request->filled('subcategory_id')) {
            session(['bg_subcategory_id' => $request->subcategory_id]);
            \Illuminate\Support\Facades\Cookie::queue('bg_subcategory_id', (string)$request->subcategory_id, $minutes);
        }
        if ($request->filled('subject_id')) {
            session(['bg_subject_id' => $request->subject_id]);
            \Illuminate\Support\Facades\Cookie::queue('bg_subject_id', (string)$request->subject_id, $minutes);
        }

        return response()->json(['success' => true]);
    }

    public function setWelcomeSession(Request $request)
    {
        if ($request->has('welcome_shown')) {
            session(['welcome_shown' => true]);
        }
        return response()->json(['success' => true]);
    }

    public function bookgenieSearch(Request $request)
    {
        $query = trim($request->get('q', ''));

        // Relax length requirement if filters are present
        $hasFilters = $request->filled('section_id') || $request->filled('category_id') || $request->filled('subcategory_id') || $request->filled('subject_id');

        if (strlen($query) < 2 && !$hasFilters) {
            return response()->json(['results' => [], 'message' => 'Please type at least 2 characters.']);
        }

        $userLat = session('user_latitude');
        $userLng = session('user_longitude');

        $results = Product::with([
                'publisher',
                'authors',
                'category',
                'attributes.vendor.vendorbusinessdetails',
            ])
            ->where('status', 1)
            ->whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->where(function ($q) use ($query, $request) {
                if ($request->filled('section_id')) $q->where('section_id', $request->section_id);
                if ($request->filled('category_id')) $q->where('category_id', $request->category_id);
                if ($request->filled('subcategory_id')) $q->where('subcategory_id', $request->subcategory_id);
                if ($request->filled('subject_id')) $q->where('subject_id', $request->subject_id);

                if ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('product_name', 'like', "%{$query}%")
                            ->orWhere('product_isbn', 'like', "%{$query}%");
                    });
                }
            })
            ->limit(8)
            ->get();

        $formatted = $results->map(function ($product) use ($userLat, $userLng) {
            // Get best seller (winner) for this product
            $bestAttr = ProductsAttribute::where('product_id', $product->id)
                ->where('status', 1)
                ->where('stock', '>', 0)
                ->buyBox()
                ->first();

            if (!$bestAttr) {
                $bestAttr = ProductsAttribute::where('product_id', $product->id)
                    ->where('status', 1)
                    ->first();
            }

            $vendor = $bestAttr ? $bestAttr->vendor : null;

            // Calculate distance
            $distance = null;
            if ($userLat && $userLng && $vendor && $vendor->location) {
                [$vLat, $vLng] = array_pad(explode(',', $vendor->location), 2, null);
                if (is_numeric($vLat) && is_numeric($vLng)) {
                    $R  = 6371;
                    $dL = deg2rad((float)$vLat - (float)$userLat);
                    $dN = deg2rad((float)$vLng - (float)$userLng);
                    $a  = sin($dL / 2) ** 2 + cos(deg2rad((float)$userLat)) * cos(deg2rad((float)$vLat)) * sin($dN / 2) ** 2;
                    $distance = round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
                }
            }

            $finalPrice = Product::getDiscountPrice($product->id);
            $shopName   = optional($vendor?->vendorbusinessdetails)->shop_name ?? 'Individual Seller';
            $address    = optional($vendor?->vendorbusinessdetails)->shop_address ?? '';

            return [
                'id'          => $bestAttr ? $bestAttr->id : null,
                'product_id'  => $product->id,
                'name'        => $product->product_name,
                'isbn'        => $product->product_isbn,
                'image'       => $product->product_image
                    ? asset('front/images/product_images/large/' . $product->product_image)
                    : null,
                'price'       => '₹' . number_format($finalPrice, 0),
                'shop'        => $shopName,
                'address'     => $address,
                'distance'    => $distance !== null ? $distance . ' km away' : null,
                'url'         => $bestAttr ? route('front.products.detail', $bestAttr->id) : url('product/' . $product->id),
            ];
        });

        $message = $results->isEmpty()
            ? "Sorry, I couldn't find any books matching \"" . e($query) . "\". Try a different name or ISBN."
            : null;

        return response()->json(['results' => $formatted, 'message' => $message]);
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

    public function getFilterSubjects(Request $request)
    {
        $subjectIds = FilterClassSubject::where('section_id', $request->section_id)
            ->where('category_id', $request->category_id)
            ->where('sub_category_id', $request->subcategory_id)
            ->distinct()
            ->pluck('subject_id');

        $subjects = Subject::whereIn('id', $subjectIds)
            ->where('status', 1)
            ->get(['id', 'name as category_name']); // using aliased name for frontend compatibility

        return response()->json($subjects);
    }

    public function searchProducts(Request $request)
    {
        $condition = session('condition', 'new');

        $query = Product::with([
                'publisher',
                'authors',
                'category',
                'attributes.vendor.vendorbusinessdetails',
            ])
            ->whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->where('status', 1);

        /* CONDITION */
        if ($request->filled('condition')) {
            if ($request->condition !== 'all') {
                $query->where('condition', $request->condition);
            }
        } elseif ($condition !== 'all') {
            $query->where('condition', $condition);
        }

        /* LANGUAGE */
        if ($request->filled('language_id') && $request->language_id !== 'all') {
            $query->where('language_id', $request->language_id);
        } elseif (session('language') && session('language') !== 'all') {
            $query->where('language_id', session('language'));
        }

        /* SEARCH */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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
            $query->where('section_id', $request->section_id);
        }

        /* CATEGORY */
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        /* SUBCATEGORY (CLASS) */
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        /* SUBJECT */
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        /* DISTANCE RANGE */
        $userLat = session('user_latitude');
        $userLng = session('user_longitude');
        $distance = session('distance', 10);
        if ($userLat && $userLng && $distance < 100) {
            $query->whereHas('attributes.vendor', function ($q) use ($userLat, $userLng, $distance) {
                $q->whereNotNull('location')
                    ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(SUBSTRING_INDEX(location, ',', 1))) * cos(radians(SUBSTRING_INDEX(location, ',', -1)) - radians(?)) + sin(radians(?)) * sin(radians(SUBSTRING_INDEX(location, ',', 1))))) <= ?", [$userLat, $userLng, $userLat, $distance]);
            });
        }

        /* FETCH */
        $products = $query->get();

        /* PRICE FILTER (VENDOR DISCOUNT SAFE) */
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minPriceVal = (float) ($request->min_price ?? 0);
            $maxPriceVal = (float) ($request->max_price ?? PHP_FLOAT_MAX);

            $products = $products->filter(function ($product) use ($minPriceVal, $maxPriceVal) {
                $price = Product::getDiscountPrice($product->id);
                return $price >= $minPriceVal && $price <= $maxPriceVal;
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
