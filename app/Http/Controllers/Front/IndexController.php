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
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $sliderBanners  = Banner::where('type', 'Slider')->where('status', 1)->get()->toArray();
        $fixBanners     = Banner::where('type', 'Fix')->where('status', 1)->get()->toArray();
        $condition      = session('condition', 'new');
        $sliderProducts = Product::with(['authors', 'publisher', 'edition'])
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

        $newProducts = Product::with(['authors', 'publisher', 'edition'])
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->when(session('language') && session('language') !== 'all', function ($query) {
                $query->where('language_id', session('language'));
            })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->paginate(8);

        $slidingProducts = Product::with(['authors', 'publisher', 'edition'])
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

        $footerProducts = Product::orderBy('id', 'Desc')
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->where('status', 1)
            ->take(3)
            ->get()
            ->toArray();

        $bestSellers = Product::where([
            'is_bestseller' => 'Yes',
            'status'        => 1,
        ])
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->inRandomOrder()
            ->get()
            ->toArray();

        $discountedProducts = Product::where('product_discount', '>', 0)
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->where('status', 1)
            ->limit(6)
            ->inRandomOrder()
            ->get()
            ->toArray();

        $featuredProducts = Product::where([
            'is_featured' => 'Yes',
            'status'      => 1,
        ])
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->when(session('language') && session('language') !== 'all', function ($query) {
                $query->where('language_id', session('language'));
            })
            ->limit(10)
            ->get();

        $meta_title       = 'BookHub - The Only Hub For Students';
        $meta_description = 'The cross platform where students meets their career through books.';
        $meta_keywords    = 'eshop website, online shopping, multi vendor e-commerce';

        // Get total user count for dynamic statistics
        $totalUsers = User::count();

        // Get total vendor count for dynamic statistics
        $totalVendors = Vendor::count();

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
            return view('front.partials.new_products', compact('newProducts'))->render();
        }

        return view('front.index3', [
            'languages'        => Language::all(),
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

    public function searchProducts(Request $request)
    {
        $condition      = session('condition', 'new');
        $query          = Product::with(['publisher', 'authors'])->where('status', 1);
        $sections       = Section::all();
        $footerProducts = Product::orderBy('id', 'Desc')
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->where('status', 1)
            ->take(3)
            ->get()
            ->toArray();
        $category = Category::limit(10)->get();
        $language = Language::get();
        $logos    = HeaderLogo::first();

        // Filter by condition (default to session condition if not specified)
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        } else {
            if ($condition !== 'all') {
                $query->where('condition', $condition);
            }
        }

        // Filter by language
        if ($request->filled('language_id')) {
            if ($request->language_id !== 'all') {
                $query->where('language_id', $request->language_id);
            }
        } else {
            $query->when(session('language') && session('language') !== 'all', function ($q) {
                $q->where('language_id', session('language'));
            });
        }

        // Apply search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('product_isbn', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('category_name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter by section/category
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Get the results first
        $products = $query->get();

        // Apply price range filter using discounted prices
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minPrice = $request->filled('min_price') ? (float) $request->min_price : 0;
            $maxPrice = $request->filled('max_price') ? (float) $request->max_price : PHP_FLOAT_MAX;

            $products = $products->filter(function ($product) use ($minPrice, $maxPrice) {
                $discountedPrice = Product::getDiscountPrice($product->id);
                $finalPrice      = $discountedPrice > 0 ? $discountedPrice : $product->product_price;

                return $finalPrice >= $minPrice && $finalPrice <= $maxPrice;
            });
        }

        // Convert back to pagination
        $perPage     = 12;
        $currentPage = $request->get('page', 1);
        $products    = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($currentPage, $perPage),
            $products->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('front.products.search', compact('products', 'request', 'condition', 'sections', 'footerProducts', 'category', 'language', 'logos'), [
            'languages'        => Language::all(),
            'selectedLanguage' => Language::find(session('language')),
        ]);
    }
}
