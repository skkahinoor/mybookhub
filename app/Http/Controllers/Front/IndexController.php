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
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $sliderBanners  = Banner::where('type', 'Slider')->where('status', 1)->get()->toArray();
        $fixBanners     = Banner::where('type', 'Fix')->where('status', 1)->get()->toArray();
        $condition      = session('condition', 'new');
        $sliderProducts = ProductsAttribute::with([
            'product:id,product_name,product_isbn,product_image,product_price,category_id,section_id',
            'vendor.vendorbusinessdetails',
            'ratings:id,product_attribute_id,rating'
        ])
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
        $totalUsers = User::role('user', 'web')->count();

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
