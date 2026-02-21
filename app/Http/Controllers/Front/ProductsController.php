<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Country;
use App\Models\Coupon;

use App\Models\HeaderLogo;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\ProductsFilter;
use App\Models\Rating;
use App\Models\Section;
use App\Models\ShippingCharge;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Wishlist;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ProductsController extends Controller
{
    public function listing(Request $request)
    {

        $condition = session('condition', 'new');
        if ($request->ajax()) {
            $data = $request->all();

            $url = $data['url'];
            $_GET['sort'] = $data['sort'];
            // dd($url);
            $categoryCount = Category::where([
                'url' => $url,
                'status' => 1,
            ])->count();
            // dd($categoryCount);

            if ($categoryCount > 0) {                           // if the category entered as a URL in the browser address bar exists
                // Get the entered URL in the browser address bar category details
                $categoryDetails = Category::categoryDetails($url); // get the categories of the opened $url (get categories depending on the $url)

                $categoryProducts = Product::with('publisher')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1); // moving the paginate() method after checking for the sorting filter

                $productFilters = ProductsFilter::productFilters(); // Get all the (enabled/active) Filters    // (Another way to go is using an AJAX call to get the $productFilters!)
                foreach ($productFilters as $key => $filter) {
                    if (isset($filter['filter_column']) && isset($data[$filter['filter_column']]) && ! empty($filter['filter_column']) && ! empty($data[$filter['filter_column']])) {
                        $categoryProducts->whereIn($filter['filter_column'], $data[$filter['filter_column']]);
                    }
                }

                if (isset($_GET['sort']) && ! empty($_GET['sort'])) { // if the URL query string parameters contain '&sort=someValue'    // 'sort' is the 'name' HTML attribute of the <select> box
                    if ($_GET['sort'] == 'product_latest') {
                        $categoryProducts->orderBy('products.id', 'Desc');
                    } elseif ($_GET['sort'] == 'price_lowest') {
                        $categoryProducts->orderBy('products.product_price', 'Asc');
                    } elseif ($_GET['sort'] == 'price_highest') {
                        $categoryProducts->orderBy('products.product_price', 'Desc');
                    } elseif ($_GET['sort'] == 'name_z_a') {
                        $categoryProducts->orderBy('products.product_name', 'Desc');
                    } elseif ($_GET['sort'] == 'name_a_z') {
                        $categoryProducts->orderBy('products.product_name', 'Asc');
                    }
                }

                if (isset($data['size']) && ! empty($data['size'])) {                                                                   // coming from the AJAX call in front/js/custom.js    // example:    $data['size'] = 'Large'
                    $productIds = ProductsAttribute::select('product_id')->whereIn('size', $data['size'])->pluck('product_id')->toArray(); // fetch the products ids of the $data['size'] from the `products_attributes` table

                    $categoryProducts->whereIn('products.id', $productIds); // `products.id` means that `products` is the table name (means grab the `id` column of the `products` table)
                }

                $productIds = [];

                if (isset($data['price']) && ! empty($data['price'])) {
                    foreach ($data['price'] as $key => $price) {
                        $priceArr = explode('-', $price);                                                                                           // Example: First loop iteration: 0, 1000    then Second loop iteration: 1000, 2000, ...etc
                        if (isset($priceArr[0]) && isset($priceArr[1])) {                                                                           // Example: First loop iteration: 0, 1000    then Second loop iteration: 1000, 2000, ...etc
                            $productIds[] = Product::select('id')->whereBetween('product_price', [$priceArr[0], $priceArr[1]])->pluck('id')->toArray(); // fetch the products ids of the range $priceArr[0] and $priceArr[1] (whereBetween() method) from the `products` table    // whereBetween(): https://laravel.com/docs/9.x/queries#additional-where-clauses    // e.g.    [    [2], [4, 5], [6]    ]
                        }
                    }

                    $productIds = array_unique(\Illuminate\Support\Arr::flatten($productIds)); // Arr::flatten(): https://laravel.com/docs/9.x/helpers#method-array-flatten    // We use array_unique() function to eliminate any repeated product ids
                    $categoryProducts->whereIn('products.id', $productIds);
                }

                // Size, price, color, publisher, … are also Dynamic Filters, but won't be managed like the other Dynamic Filters, but we will manage every filter of them from the suitable respective database table, like the 'size' Filter from the `products_attributes` database table, 'color' Filter and `price` Filter from `products` table, 'publisher' Filter from `publishers` table
                // Fourth: the 'publisher' filter (from `products` and `publishers` database table)
                if (isset($data['publisher']) && ! empty($data['publisher'])) {                                            // coming from the AJAX call in front/js/custom.js    // example:    $data['publisher'] = 'Large'
                    $productIds = Product::select('id')->whereIn('publisher_id', $data['publisher'])->pluck('id')->toArray(); // fetch the products ids with `publisher_id` of $data['publisher'] from the `products` table

                    $categoryProducts->whereIn('products.id', $productIds); // `products.id` means that `products` is the table name (means grab the `id` column of the `products` table)
                }

                // Pagination (after the Sorting Filter)
                $categoryProducts = $categoryProducts->paginate(30); // Moved the pagination after checking for the sorting filter <form>

                // Dynamic SEO (HTML meta tags): Check the HTML <meta> tags and <title> tag in front/layout/layout.blade.php
                $meta_title = $categoryDetails['categoryDetails']['meta_title'];
                $meta_description = $categoryDetails['categoryDetails']['meta_description'];
                $meta_keywords = $categoryDetails['categoryDetails']['meta_keywords'];

                return view('front.products.ajax_products_listing')->with(compact('categoryDetails', 'categoryProducts', 'url', 'meta_title', 'meta_description', 'meta_keywords', 'condition'));
            } else {
                abort(404); // we will create the 404 page later on    // https://laravel.com/docs/9.x/helpers#method-abort
            }
        } else { // Sorting Filter WITHOUT AJAX (using the HTML <form> and jQuery) Or handling the website Search Form (in front/layout/header.blade.php) BOTH in front/products/listing.blade.php

            // Website Search Form (to search for all website products). Check the HTML Form in front/layout/header.blade.php
            if (isset($_REQUEST['search']) && ! empty($_REQUEST['search'])) { // If the Search Form is used, handle the Search Form submission
                // New Arrivals    // Check front/layout/header.blade.php
                if ($_REQUEST['search'] == 'new-arrivals') {
                    $search_product = $_REQUEST['search'];

                    // We fill in the $categoryDetails array MANUALLY with the same indexes/keys that come from the categoryDetails() method in Category.php model (because in either cases of the if-else statement, we pass in $categoryDetails variable to the view down below)
                    $categoryDetails['breadcrumbs'] = 'New Arrival Products';
                    $categoryDetails['categoryDetails']['category_name'] = 'New Arrival Products';
                    $categoryDetails['categoryDetails']['description'] = 'New Arrival Products';

                    // We join `products` table (at the `category_id` column) with `categoreis` table (becausee we're going to search `category_name` column in `categories` table)
                    // Note: It's best practice to name table columns with more verbose descriptive names (e.g. if the table name is `products`, then you should have a column called `product_id`, NOT `id`), and also, don't have repeated column names THROUGHOUT/ACROSS the tables of a certain (one) database (i.e. make all your database tables column names (throughout your database) UNIQUE (even columns in different tables!)). That's because of that problem that emerges when you join (JOIN clause) two tables which have the same column names, when you join them, the column names of the second table overrides the column names of the first table (similar column names override each other), leading to many problems. There are TWO ways/workarounds to tackle this problem

                    $condition = session('condition', 'new');
                    $categoryProducts = Product::select(
                        'products.id',
                        'products.section_id',
                        'products.category_id',
                        'products.publisher_id',
                        'products.vendor_id',
                        'products.product_name',
                        'products.product_price',
                        'products.product_discount',
                        'products.product_image',
                        'products.description'
                    )->with('publisher')->join( // Joins: Inner Join Clause: https://laravel.com/docs/9.x/queries#inner-join-clause    // moving the paginate() method after checking for the sorting filter <form>    // Paginating Eloquent Results: https://laravel.com/docs/9.x/pagination#paginating-eloquent-results    // Displaying Pagination Results Using Bootstrap: https://laravel.com/docs/9.x/pagination#using-bootstrap        // https://laravel.com/docs/9.x/queries#additional-where-clauses    // using the publisher() relationship method in Product.php model    // Eager Loading (using with() method): https://laravel.com/docs/9.x/eloquent-relationships#eager-loading    // 'publisher' is the relationship method name in Product.php model
                        'categories',               // `categories` table
                        'categories.id',
                        '=',
                        'products.category_id'                                 // JOIN both `products` and `categories` tables at    `categories`.`id` = `products`.`category_id`
                    )->where('products.status', 1)->orderBy('id', 'Desc'); // Show from the latest to the earliest (NEW ARRIVALS!)
                    // dd($categoryProducts);

                    // Best Sellers    // Check front/layout/header.blade.php
                } elseif ($_REQUEST['search'] == 'best-sellers') {
                    $search_product = $_REQUEST['search'];

                    $categoryDetails['breadcrumbs'] = 'Best Sellers Products';
                    $categoryDetails['categoryDetails']['category_name'] = 'Best Sellers Products';
                    $categoryDetails['categoryDetails']['description'] = 'Best Sellers Products';

                    $categoryProducts = Product::select(
                        'products.id',
                        'products.section_id',
                        'products.category_id',
                        'products.publisher_id',
                        'products.vendor_id',
                        'products.product_name',
                        'products.product_price',
                        'products.product_discount',
                        'products.product_image',
                        'products.description'
                    )->with('publisher')->join(
                        'categories',
                        'categories.id',
                        '=',
                        'products.category_id'
                    )->where('products.status', 1)->where('products.is_bestseller', 'Yes');
                    // dd($categoryProducts);

                    // Featured    // Check front/layout/header.blade.php
                } elseif ($_REQUEST['search'] == 'featured') {
                    $search_product = $_REQUEST['search'];
                    $categoryDetails['breadcrumbs'] = 'Featured Products';
                    $categoryDetails['categoryDetails']['category_name'] = 'Featured Products';
                    $categoryDetails['categoryDetails']['description'] = 'Featured Products';

                    $categoryProducts = Product::select(
                        'products.id',
                        'products.section_id',
                        'products.category_id',
                        'products.publisher_id',
                        'products.vendor_id',
                        'products.product_name',
                        'products.product_price',
                        'products.product_discount',
                        'products.product_image',
                        'products.description'
                    )->with('publisher')->join(
                        'categories', // `categories` table
                        'categories.id',
                        '=',
                        'products.category_id' // JOIN both `products` and `categories` tables at    `categories`.`id` = `products`.`category_id`
                    )->where('products.status', 1)->where('products.is_featured', 'Yes');
                    // dd($categoryProducts);

                    // Discount    // Check front/layout/header.blade.php
                } elseif ($_REQUEST['search'] == 'discounted') {
                    $search_product = $_REQUEST['search'];

                    $categoryDetails['breadcrumbs'] = 'Discounted Products';
                    $categoryDetails['categoryDetails']['category_name'] = 'Discounted Products';
                    $categoryDetails['categoryDetails']['description'] = 'Discounted Products';

                    $categoryProducts = Product::select(
                        'products.id',
                        'products.section_id',
                        'products.category_id',
                        'products.publisher_id',
                        'products.vendor_id',
                        'products.product_name',
                        'products.product_price',
                        'products.product_discount',
                        'products.product_image',
                        'products.description'
                    )->with('publisher')->join(
                        'categories', // `categories` table
                        'categories.id',
                        '=',
                        'products.category_id'
                    )->where('products.status', 1)->where('products.product_discount', '>', 0);
                } else { // The Search Bar
                    $search_product = $_REQUEST['search'];

                    $categoryDetails['breadcrumbs'] = $search_product;
                    $categoryDetails['categoryDetails']['category_name'] = $search_product;
                    $categoryDetails['categoryDetails']['description'] = 'Search Products for ' . $search_product;

                    $categoryProducts = Product::select(
                        'products.id',
                        'products.section_id',
                        'products.category_id',
                        'products.publisher_id',
                        'products.vendor_id',
                        'products.product_name',
                        'products.product_price',
                        'products.product_discount',
                        'products.product_image',
                        'products.description'
                    )->with('publisher')->join(
                        'categories', // `categories` table
                        'categories.id',
                        '=',
                        'products.category_id'
                    )->where(function ($query) use ($search_product) {

                        $query->where('products.product_name', 'like', '%' . $search_product . '%')
                            ->orWhere('products.description', 'like', '%' . $search_product . '%')
                            ->orWhere('categories.category_name', 'like', '%' . $search_product . '%');
                    })->where('products.status', 1);
                    // dd($categoryProducts);
                }

                if (isset($_REQUEST['section_id']) && ! empty($_REQUEST['section_id'])) {
                    $categoryProducts = $categoryProducts->where('products.section_id', $_REQUEST['section_id']);
                }

                $categoryProducts = $categoryProducts->get();
                // dd($categoryProducts);

                $categories = \App\Models\Category::where('status', 1)->orderBy('category_name')->get();

                return view('front.products.listing')->with(compact(
                    'categoryDetails',
                    'categoryProducts',
                    'condition',
                    'categories'
                ));
            } else {                                                                     // If the Search Form is NOT used, render the listing.blade.php page with the Sorting Filter WITHOUT AJAX (using the HTML <form> and jQuery)
                $url = \Illuminate\Support\Facades\Route::getFacadeRoot()->current()->uri(); // Accessing The Current Route: https://laravel.com/docs/9.x/routing#accessing-the-current-route    // Accessing The Current URL: https://laravel.com/docs/9.x/urls#accessing-the-current-url
                // dd($url);
                $condition = session('condition', 'new');
                $categoryCount = Category::where([
                    'url' => $url,
                    'status' => 1,
                ])->count();
                // dd($categoryCount);

                if ($categoryCount > 0) { // if the category entered as a URL in the browser address bar exists
                    // Get the entered URL in the browser address bar category details
                    $categoryDetails = Category::categoryDetails($url);
                    $categoryProducts = Product::with('publisher')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1); // moving the paginate() method after checking for the sorting filter <form>    // Paginating Eloquent Results: https://laravel.com/docs/9.x/pagination#paginating-eloquent-results    // Displaying Pagination Results Using Bootstrap: https://laravel.com/docs/9.x/pagination#using-bootstrap        // https://laravel.com/docs/9.x/queries#additional-where-clauses    // using the publisher() relationship method in Product.php

                    // Sorting Filter WITHOUT AJAX (using HTML <form> and jQuery) in front/products/listing.blade.php
                    if (isset($_GET['sort']) && ! empty($_GET['sort'])) { // if the URL query string parameters contain '&sort=someValue'    // 'sort' is the 'name' HTML attribute of the <select> box
                        if ($_GET['sort'] == 'product_latest') {
                            $categoryProducts->orderBy('products.id', 'Desc');
                        } elseif ($_GET['sort'] == 'price_lowest') {
                            $categoryProducts->orderBy('products.product_price', 'Asc');
                        } elseif ($_GET['sort'] == 'price_highest') {
                            $categoryProducts->orderBy('products.product_price', 'Desc');
                        } elseif ($_GET['sort'] == 'name_z_a') {
                            $categoryProducts->orderBy('products.product_name', 'Desc');
                        } elseif ($_GET['sort'] == 'name_a_z') {
                            $categoryProducts->orderBy('products.product_name', 'Asc');
                        }
                    }

                    // Pagination (after the Sorting Filter)
                    $categoryProducts = $categoryProducts->paginate(30); // Moved the pagination after checking for the sorting filter <form>

                    // Dynamic SEO (HTML meta tags): Check the HTML <meta> tags and <title> tag in front/layout/layout.blade.php
                    $meta_title = $categoryDetails['categoryDetails']['meta_title'];
                    $meta_description = $categoryDetails['categoryDetails']['meta_description'];
                    $meta_keywords = $categoryDetails['categoryDetails']['meta_keywords'];

                    return view('front.products.listing')->with(compact('categoryDetails', 'categoryProducts', 'url', 'meta_title', 'meta_description', 'meta_keywords', 'condition'));
                } else {
                    abort(404); // we will create the 404 page later on    // https://laravel.com/docs/9.x/helpers#method-abort
                }
            }
        }
    }

    public function categoryProducts(Request $request, $category_id = null)
    {
        $condition = session('condition', 'new');
        $sections = Section::all();
        $footerProducts = Product::orderBy('id', 'Desc')
            ->where('condition', $condition)
            ->where('status', 1)
            ->take(3)
            ->get()
            ->toArray();
        $category = Category::limit(10)->get();
        $language = Language::get();
        $logos = HeaderLogo::first();

        // Get category details
        $categoryDetails = null;
        $products = collect();

        if ($category_id) {
            // Get category by ID
            $categoryDetails = Category::find($category_id);

            if ($categoryDetails) {
                // Get all subcategories if this is a parent category
                $subCategories = Category::where('parent_id', $category_id)->pluck('id')->toArray();
                $categoryIds = array_merge([$category_id], $subCategories);

                // Get products for this category and its subcategories
                $products = Product::with(['publisher', 'authors'])
                    ->whereIn('category_id', $categoryIds)
                    ->where('status', 1);
            }
        } else {
            // If no category_id provided, show all products
            $products = Product::with(['publisher', 'authors'])
                ->where('status', 1);
        }

        // Apply filters
        if ($products instanceof \Illuminate\Database\Eloquent\Builder) {
            // Condition filter
            if ($request->filled('condition')) {
                if ($request->condition !== 'all') {
                    $products->where('condition', $request->condition);
                }
            } else {
                if ($condition !== 'all') {
                    $products->where('condition', $condition);
                }
            }

            // Language filter
            if ($request->filled('language_id')) {
                if ($request->language_id !== 'all') {
                    $products->where('language_id', $request->language_id);
                }
            } else {
                $products->when(session('language') && session('language') !== 'all', function ($query) {
                    $query->where('language_id', session('language'));
                });
            }

            // Section filter (for when no specific category is selected)
            if (! $category_id && $request->filled('section_id')) {
                $products->where('section_id', $request->section_id);
            }

            // Apply sorting from request before fetching
            if ($request->filled('sort')) {
                switch ($request->get('sort')) {
                    case 'product_latest':
                        $products = $products->orderBy('id', 'desc');
                        break;
                    case 'price_lowest':
                        $products = $products->orderBy('product_price', 'asc');
                        break;
                    case 'price_highest':
                        $products = $products->orderBy('product_price', 'desc');
                        break;
                    case 'name_a_z':
                        $products = $products->orderBy('product_name', 'asc');
                        break;
                    case 'name_z_a':
                        $products = $products->orderBy('product_name', 'desc');
                        break;
                    default:
                        $products = $products->orderBy('id', 'desc');
                        break;
                }
            } else {
                $products = $products->orderBy('id', 'desc');
            }

            // Get the results first
            $products = $products->get();

            // Apply price range filter using discounted prices
            if ($request->filled('min_price') || $request->filled('max_price')) {
                $minPrice = $request->filled('min_price') ? (float) $request->min_price : 0;
                $maxPrice = $request->filled('max_price') ? (float) $request->max_price : PHP_FLOAT_MAX;

                $products = $products->filter(function ($product) use ($minPrice, $maxPrice) {
                    $discountedPrice = Product::getDiscountPrice($product->id);
                    $finalPrice = $discountedPrice > 0 ? $discountedPrice : $product->product_price;

                    return $finalPrice >= $minPrice && $finalPrice <= $maxPrice;
                });
            }

            // Convert back to pagination
            $perPage = 12;
            $currentPage = $request->get('page', 1);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products->forPage($currentPage, $perPage),
                $products->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        // SEO meta tags
        $meta_title = $categoryDetails ? $categoryDetails->meta_title : 'All Books';
        $meta_description = $categoryDetails ? $categoryDetails->meta_description : 'Browse all books in our collection';
        $meta_keywords = $categoryDetails ? $categoryDetails->meta_keywords : 'books, literature, reading';

        $categories = \App\Models\Category::where('status', 1)->orderBy('category_name')->get();

        return view('front.products.category_products', compact(
            'products',
            'categoryDetails',
            'condition',
            'sections',
            'footerProducts',
            'category',
            'language',
            'logos',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'categories' // <-- include this
        ), [
            'languages' => Language::all(),
            'selectedLanguage' => Language::find(session('language')),
        ]);
    }

    public function detail($id, Request $request)
    {
        $condition = session('condition', 'new');

        $category = Category::limit(10)->get();
        $sections = Section::all();
        $language = Language::get();
        $logos = HeaderLogo::first();

        $footerProducts = Product::where('status', 1)
            ->where('condition', $condition)
            ->latest()
            ->take(3)
            ->get()
            ->toArray();

        $productAttribute = ProductsAttribute::with([
            'product.section',
            'product.category',
            'product.language',
            'product.publisher',
            'product.authors',
            // 'product.images',
            'vendor',
        ])
            ->where('id', $id)
            ->where('status', 1)
            ->firstOrFail();

        $product = $productAttribute->product;
        $productId = $product->id;
        $vendorId = $productAttribute->vendor_id;

        $productDetails = $product->toArray();

        $productDetails['vendor_id'] = $vendorId;
        $productDetails['attribute_id'] = $productAttribute->id;
        $productDetails['stock'] = (int) $productAttribute->stock;
        $productDetails['product_discount'] = (float) $productAttribute->product_discount;

        $basePrice = (float) $product->product_price;
        $discount = (float) $productAttribute->product_discount;

        if ($discount > 0) {
            $finalPrice = round($basePrice - ($basePrice * $discount / 100));
            $discountPercent = round($discount);
        } else {
            $finalPrice = round($basePrice);
            $discountPercent = 0;
        }

        $totalStock = (int) $productAttribute->stock;

        if (! $product->category) {
            abort(404, 'Product category not found');
        }

        $categoryDetails = Category::categoryDetails($product->category->url);
        $categoryId = $product->category->id;

        $similarProducts = ProductsAttribute::with([
            'product.publisher',
            'product.authors',
        ])
            ->where('status', 1)              // attribute active
            ->where('stock', '>', 0)          // optional but recommended
            ->whereHas('product', function ($query) use ($categoryId, $productId) {
                $query->where('status', 1)
                    ->where('category_id', $categoryId)
                    ->where('id', '!=', $productId);
            })
            ->inRandomOrder()
            ->get()
            ->groupBy('product_id')            // 🔥 remove duplicates
            ->map(fn($items) => $items->first()) // take one vendor per product
            ->take(3)
            ->values();

        $attributeId = $productAttribute->id;

        $ratings = Rating::with('user')
            ->where('product_id', $productId)
            ->where('product_attribute_id', $attributeId)
            ->where('status', 1)
            ->get();

        $ratingCount = $ratings->count();
        $ratingSum = $ratings->sum('rating');

        $avgRating = $ratingCount ? round($ratingSum / $ratingCount, 2) : 0;
        $avgStarRating = $ratingCount ? round($ratingSum / $ratingCount) : 0;

        $totalUsers = User::role('user', 'web')->count();
        $totalVendors = User::role('vendor', 'web')->count();
        $totalProducts = Product::where('status', 1)->count();
        $totalAuthors = Author::where('status', 1)->count();

        $meta_title = $product->meta_title;
        $meta_description = $product->meta_description;
        $meta_keywords = $product->meta_keywords;

        return view('front.products.detail')->with(compact(
            'productDetails',
            'productAttribute',
            'basePrice',
            'finalPrice',
            'discountPercent',
            'totalStock',
            'categoryDetails',
            'similarProducts',
            'ratings',
            'ratingCount',
            'avgRating',
            'avgStarRating',
            'condition',
            'category',
            'footerProducts',
            'sections',
            'language',
            'logos',
            'meta_title',
            'meta_description',
            'meta_keywords',
            // ✅ FIXED VARIABLES
            'totalUsers',
            'totalVendors',
            'totalProducts',
            'totalAuthors'
        ));
    }

    // The AJAX call from front/js/custom.js file, to show the the correct related `price` and `stock` depending on the selected `size` (from the `products_attributes` table)) by clicking the size <select> box in front/products/detail.blade.php
    public function getProductPrice(Request $request)
    {
        if ($request->ajax()) {  // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)

            $getDiscountAttributePrice = Product::getDiscountAttributePrice($data['product_id'], $data['size']); // $data['product_id'] and $data['size'] come from the 'data' object inside the $.ajax() method in front/js/custom.js file

            return $getDiscountAttributePrice;
        }
    }

    // Show all Vendor products in front/products/vendor_listing.blade.php    // This route is accessed from the <a> HTML element in front/products/vendor_listing.blade.php
    public function vendorListing($vendorid, Request $request)
    { // Required Parameters: https://laravel.com/docs/9.x/routing#required-parameters
        // Get vendor shop name
        $condition = $request->query('condition');
        if (! in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }

        $getVendorShop = Vendor::getVendorShop($vendorid);

        // Get all vendor products
        $vendorProducts = Product::with('publisher')->where('vendor_id', $vendorid)->where('status', 1); // Eager Loading (using with() method): https://laravel.com/docs/9.x/eloquent-relationships#eager-loading    // 'publisher' is the relationship method name in Product.php model that is being Eager Loaded

        // $vendorProducts Pagination
        $vendorProducts = $vendorProducts->paginate(30); // Paginating Eloquent Results: https://laravel.com/docs/9.x/pagination#paginating-eloquent-results

        return view('front.products.vendor_listing')->with(compact('getVendorShop', 'vendorProducts', 'condition'));
    }

    public function cartAdd(Request $request)
    {
        $condition = $request->query('condition', 'new');

        if (! $request->isMethod('post')) {
            return back();
        }

        $qty = max((int) $request->quantity, 1);

        Session::forget(['couponAmount', 'couponCode']);

        $attribute = ProductsAttribute::where('id', $request->product_attribute_id)
            ->where('status', 1)
            ->first();

        if (! $attribute) {
            return back()->with('error_message', 'Invalid product.');
        }

        if ($attribute->stock < $qty) {
            return back()->with('error_message', 'Requested quantity not available.');
        }

        $session_id = Session::get('session_id') ?? Session::getId();
        Session::put('session_id', $session_id);

        $user_id = Auth::id() ?? 0;

        $cartItem = Cart::where('product_attribute_id', $attribute->id)
            ->where(function ($q) use ($user_id, $session_id) {
                $user_id > 0
                    ? $q->where('user_id', $user_id)
                    : $q->where('session_id', $session_id);
            })
            ->first();

        if ($cartItem) {

            if ($cartItem->quantity + $qty > $attribute->stock) {
                return back()->with('error_message', 'Stock limit exceeded.');
            }

            $cartItem->increment('quantity', $qty);
        } else {

            Cart::create([
                'session_id' => $session_id,
                'user_id' => $user_id,
                'product_attribute_id' => $attribute->id,
                'product_id' => $attribute->product_id,
                'quantity' => $qty,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Product added to cart successfully',
                'totalCartItems' => Cart::totalCartItems(),
            ]);
        }

        return redirect()
            ->route('cart', ['condition' => $condition])
            ->with('success_message', 'Product added to cart successfully!');
    }

    // Render Cart page (front/products/cart.blade.php)
    public function cart(Request $request)
    {
        $condition = $request->query('condition', 'new');

        $logos = HeaderLogo::first();
        $sections = Section::all();
        $language = Language::get();

        $getCartItems = Cart::getCartItems();

        $total_price = 0;

        foreach ($getCartItems as $item) {
            $price = Product::getDiscountPriceDetailsByAttribute(
                $item['product_attribute_id']
            );

            $total_price += $price['final_price'] * $item['quantity'];
        }

        return view('front.products.cart', compact(
            'getCartItems',
            'total_price',
            'condition',
            'logos',
            'sections',
            'language'
        ));
    }

    // Update Cart Item Quantity AJAX call in front/products/cart_items.blade.php. Check front/js/custom.js
    public function cartUpdate(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->all();

            Session::forget('couponAmount');
            Session::forget('couponCode');

            $cartDetails = Cart::find($data['cartid']);
            if (! $cartDetails) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found',
                ]);
            }

            if (! isset($data['qty']) || $data['qty'] < 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid quantity',
                ]);
            }

            $availableStock = ProductsAttribute::where('id', $cartDetails->product_attribute_id)
                ->value('stock');

            if ($data['qty'] > $availableStock) {
                return response()->json([
                    'status' => false,
                    'message' => 'Stock not available',
                ]);
            }

            $cartDetails->update(['quantity' => $data['qty']]);

            $getCartItems = Cart::getCartItems();

            $subtotal = 0;
            foreach ($getCartItems as $item) {
                $priceDetails = Product::getDiscountPriceDetailsByAttribute(
                    $item['product_attribute_id']
                );
                $subtotal += $priceDetails['final_price'] * $item['quantity'];
            }

            return response()->json([
                'status' => true,
                'totalCartItems' => Cart::totalCartItems(),

                'view' => (string) view('front.products.cart_items')
                    ->with(compact('getCartItems')),

                'headerview' => (string) view('front.layout.header_cart_items')
                    ->with(compact('getCartItems')),

                'subtotal' => number_format($subtotal, 2),
                'grandTotal' => number_format($subtotal, 2), // coupon later adjusts this
            ]);
        }
    }

    // Delete a Cart Item AJAX call in front/products/cart_items.blade.php. Check front/js/custom.js
    public function cartDelete(Request $request)
    {
        $condition = $request->query('condition');
        if (! in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        if ($request->ajax()) {
            Session::forget('couponAmount');
            Session::forget('couponCode');

            $data = $request->all();
            Cart::where('id', $data['cartid'])->delete();
            $getCartItems = Cart::getCartItems();
            $totalCartItems = Cart::totalCartItems();

            return response()->json([
                'status' => true,
                'totalCartItems' => $totalCartItems,
                'view' => (string) \Illuminate\Support\Facades\View::make('front.products.cart_items')->with(compact('getCartItems')),
                'headerview' => (string) \Illuminate\Support\Facades\View::make('front.layout.header_cart_items')->with(compact('getCartItems', 'condition')),
            ]);
        }
    }

    // Render Wishlist page using resources/views/front/products/wishlist.blade.php
    public function wishlist(Request $request)
    {
        $logos = HeaderLogo::first();
        $sections = Section::all();
        $language = Language::get();

        $condition = $request->query('condition');
        if (! in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }

        $session_id = Session::get('session_id');
        if (! $session_id) {
            $session_id = Session::getId();
            Session::put('session_id', $session_id);
        }

        $getWishlistItems = Wishlist::getWishlistItems();

        $total_price = 0;

        foreach ($getWishlistItems as $item) {
            if (! isset($item['product_attribute_id'])) {
                continue;
            }

            $priceDetails = Product::getDiscountPriceDetailsByAttribute(
                $item['product_attribute_id']
            );

            $qty = $item['quantity'] ?? 1;

            $total_price += ($priceDetails['final_price'] ?? 0) * $qty;
        }

        $meta_title = 'Wishlist - Multi Vendor E-commerce';
        $meta_keywords = 'wishlist, favorites';

        $footerProducts = Product::orderBy('id', 'desc')
            ->where('status', 1)
            ->take(3)
            ->get()
            ->toArray();

        return view('front.products.wishlist', compact(
            'getWishlistItems',
            'total_price',
            'meta_title',
            'meta_keywords',
            'condition',
            'logos',
            'sections',
            'language',
            'footerProducts'
        ));
    }

    public function wishlistAdd(Request $request)
    {
        // Quantity safety
        $qty = max((int) ($request->quantity ?? 1), 1);

        // Validate attribute
        if (! $request->product_attribute_id) {
            return back()->with('error_message', 'Invalid product.');
        }

        $attribute = ProductsAttribute::with('product')
            ->where('id', $request->product_attribute_id)
            ->where('status', 1)
            ->first();

        if (! $attribute) {
            return back()->with('error_message', 'Invalid product.');
        }

        // Session for guest
        $session_id = Session::get('session_id');
        if (! $session_id) {
            $session_id = Session::getId();
            Session::put('session_id', $session_id);
        }

        $user_id = Auth::check() ? Auth::id() : 0;

        // Prevent duplicate wishlist entries
        $query = Wishlist::where('product_attribute_id', $attribute->id);

        if ($user_id > 0) {
            $query->where('user_id', $user_id);
        } else {
            $query->where('session_id', $session_id);
        }

        if ($query->exists()) {
            $query->increment('quantity', $qty);
        } else {
            Wishlist::create([
                'session_id' => $session_id,
                'user_id' => $user_id,
                'product_id' => $attribute->product_id,
                'product_attribute_id' => $attribute->id,
                'quantity' => $qty,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'totalWishlistItems' => Wishlist::totalWishlistItems(),
            ]);
        }

        return redirect()
            ->route('wishlist')
            ->with('success_message', 'Product added to wishlist successfully!');
    }

    public function wishlistRemove(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();

            Wishlist::where('id', $data['wishlist_id'])->delete();

            $getWishlistItems = Wishlist::getWishlistItems();
            $totalWishlistItems = Wishlist::totalWishlistItems();

            return response()->json([
                'status' => true,
                'message' => 'Product removed from wishlist successfully!',
                'totalWishlistItems' => $totalWishlistItems,
                'view' => (string) \Illuminate\Support\Facades\View::make('front.products.wishlist_items')->with(compact('getWishlistItems')),
            ]);
        }
    }

    // Note: For Coupons module, user must be logged in (authenticated) to be able to redeem them. Both 'admins' and 'vendors' can add Coupons. Coupons added by 'vendor' will be available for their products ONLY, but ones added by 'admins' will be available for ALL products.
    // Coupon Code redemption (Apply coupon) / Coupon Code HTML Form submission via AJAX in front/products/cart_items.blade.php, check front/js/custom.js
    public function applyCoupon(Request $request)
    {
        if ($request->ajax()) {  // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call) (through the 'data' object)

            // We need to remove/empty (forget) the 'couponAmount' and 'couponCode' Session Variables (reset the whole process of Applying the Coupon) whenever a user applies a new coupon, or updates Cart items (changes items quantity for example) or deletes items from the Cart or even Adds new items in the Cart
            Session::forget('couponAmount'); // Deleting Data: https://laravel.com/docs/9.x/session#deleting-data
            Session::forget('couponCode');   // Deleting Data: https://laravel.com/docs/9.x/session#deleting-data

            $getCartItems = Cart::getCartItems();
            $totalCartItems = Cart::totalCartItems(); // totalCartItems() function is in our custom Helpers/Helper.php file that we have registered in 'composer.json' file    // We created the CSS class 'totalCartItems' in front/layout/header.blade.php to use it in front/js/custom.js to update the total cart items via AJAX, because in pages that we originally use AJAX to update the cart items (such as when we delete a cart item in http://127.0.0.1:8000/cart using AJAX), the number doesn't change in the header automatically because AJAX is already used and no page reload/refresh has occurred

            // Check the validity of the Coupon Code
            $couponCount = Coupon::where('coupon_code', $data['code'])->count(); // $data['code'] comes from the 'data' object sent from inside the $.ajax() method in front/js/custom.js file

            if ($couponCount == 0) {  // if the submitted coupon is wrong, send error message
                return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                    'status' => false,
                    'totalCartItems' => $totalCartItems, // totalCartItems() function is in our custom Helpers/Helper.php file that we have registered in 'composer.json' file    // We created the CSS class 'totalCartItems' in front/layout/header.blade.php to use it in front/js/custom.js to update the total cart items via AJAX, because in pages that we originally use AJAX to update the cart items (such as when we delete a cart item in http://127.0.0.1:8000/cart using AJAX), the number doesn't change in the header automatically because AJAX is already used and no page reload/refresh has occurred
                    'message' => 'The coupon is invalid!',
                    // We'll use that array key 'view' as a JavaScript 'response' property to render the view (    $('#appendCartItems').html(resp.view);    ). Check front/js/custom.js
                    'view' => (string) \Illuminate\Support\Facades\View::make('front.products.cart_items')->with(compact('getCartItems')),      // View Responses: https://laravel.com/docs/9.x/responses#view-responses    // Creating & Rendering Views: https://laravel.com/docs/9.x/views#creating-and-rendering-views    // Passing Data To Views: https://laravel.com/docs/9.x/views#passing-data-to-views
                    'headerview' => (string) \Illuminate\Support\Facades\View::make('front.layout.header_cart_items')->with(compact('getCartItems')), // View Responses: https://laravel.com/docs/9.x/responses#view-responses    // Creating & Rendering Views: https://laravel.com/docs/9.x/views#creating-and-rendering-views    // Passing Data To Views: https://laravel.com/docs/9.x/views#passing-data-to-views
                ]);
            } else { // if the submitted coupon is valid, check some conditions (do some validation)
                // SUBMITTED COUPON CODE VALIDATION:

                // Get the coupon submitted (via AJAX) details
                $couponDetails = Coupon::where('coupon_code', $data['code'])->first(); // $data['code'] comes from the 'data' object sent from inside the $.ajax() method in front/js/custom.js file

                // Check if the submitted coupon code is active/inactive (enabled/disabled/activated/deactivated)
                if ($couponDetails->status == 0) {
                    $message = 'The coupon is inactive!';
                }

                // Check if the submitted coupon code is expired
                $expiry_date = $couponDetails->expiry_date;
                $current_date = date('Y-m-d'); // this date format is understandable by MySQL

                if ($expiry_date < $current_date) {
                    $message = 'The coupon is expired!';
                }

                // Managing coupon types in `coupons` table: 'Single Time' or 'Multiple Times'
                if ($couponDetails->coupon_type == 'Single Time') { // if the `coupon_type` in `coupons` table is 'Single Time'
                    // Check in the `orders` table if the currently authenticated/logged-in user really used this Coupon Code with their order
                    $couponCount = Order::where([
                        'coupon_code' => $data['code'],
                        'user_id' => Auth::user()->id, // Retrieving The Authenticated User: https://laravel.com/docs/9.x/authentication#retrieving-the-authenticated-user
                    ])->count();

                    if ($couponCount >= 1) { // if this 'Single Time' coupon code has been used/redeemed more than one single time by this user (this authenticated/logged-in user) (i.e. meaning that if that coupon code is already existing in the `orders` table and has been used/redeemed by this authenticated/logged-in user)
                        $message = 'This coupon code is already availed by you!';
                    }
                }

                // Check if the submitted coupon code belongs to the correct relevant selected categories and subcategories of the coupon in the Admin Panel (for example, if the coupon is for Smartphones Category, user can't use it while buying T-shirts)
                // Get the coupon's categories and subcategories (if any)
                $catArr = explode(',', $couponDetails->categories);

                $total_amount = 0;

                foreach ($getCartItems as $key => $item) {
                    if (! in_array($item['product']['category_id'], $catArr)) { // if the category of one of the products in the Cart doesn't belong to the Coupon's categories (the categories of the coupon selected by 'vendor' or 'admin' in the Admin Panel for the coupon)
                        $message = 'This coupon code selected categories is not for one of the selected products category!';
                    }

                    $attrPrice = Product::getDiscountAttributePrice($item['product_id'], $item['size']);
                    $total_amount = $total_amount + ($attrPrice['final_price'] * $item['quantity']);
                }

                // Check if the coupon code submitted by user is not available for that user (in case the coupon is already selected for certain specific users selected by 'admin' or 'vendor' in the Coupons tab in Admin Panel, and it's not available for all users)
                // Get the coupon's selected users
                if (isset($couponDetails->users) && ! empty($couponDetails->users)) {
                    $usersArr = explode(',', $couponDetails->users);
                    // Check if the submitted coupon code is available ONLY for some specific users (from the Coupons tab in Admin Panel in 'Select User (by email):') and check if the coupon is available or not for the user submitting the coupon code
                    if (count($usersArr)) { // if there's at least a one specific selected user for the coupon
                        // Get user ids of all the selected users that the coupon code are available for them
                        foreach ($usersArr as $key => $user) {
                            $getUserId = User::select('id')->where('email', $user)->first()->toArray();
                            $usersId[] = $getUserId['id'];
                        }

                        foreach ($getCartItems as $item) {
                            if (! in_array($item['user_id'], $usersId)) { // if the user id of one of the products in the Cart doesn't belong to the Coupon's specifically selected users (to check if the submitted coupon code is available to the user submitting it or not)
                                $message = 'This coupon code is not available for you! Try again with a valid coupon code! (The coupon code is available only for certain selected users!)';
                            }
                        }
                    }
                }

                // Check if the submitted Coupon code belongs to the Vendor of that product (in case that a vendor (not an 'admin') added that coupon code, because vendor coupon codes are available ONLY for the products of that vendor, and not available for all other products. In contrast, 'Admin' coupon codes are available for ALL products)
                // Vendor's Coupons are eligible only for that vendor's products
                if ($couponDetails->vendor_id > 0) { // Check if submitted coupon code belongs to a 'vendor' (becasue a vendor' coupon is available ONLY for that vendor's products (not all products), whereas admin's coupons are available for all products)
                    // Get all the products ids of that very vendor
                    $productIds = Product::select('id')->where('vendor_id', $couponDetails->vendor_id)->pluck('id')->toArray();

                    foreach ($getCartItems as $item) {
                        if (! in_array($item['product']['id'], $productIds)) { // if the user id of one of the products in the Cart doesn't belong to the products ids of that vendor (to check if the submitted coupon code pertains to that specific/very vendor or not)
                            $message = 'This coupon code is not available for you! Try again with a valid coupon code! (vendor validation)!. The coupon code exists but one of the products in the Cart doesn\'t belong to that specific vendor who created/owns that Coupon!';
                        }
                    }
                }

                // If there's an error message with the submitted coupon code, send this response to the AJAX call
                if (isset($message)) {
                    return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                        'status' => false,
                        'totalCartItems' => $totalCartItems, // totalCartItems() function is in our custom Helpers/Helper.php file that we have registered in 'composer.json' file    // We created the CSS class 'totalCartItems' in front/layout/header.blade.php to use it in front/js/custom.js to update the total cart items via AJAX, because in pages that we originally use AJAX to update the cart items (such as when we delete a cart item in http://127.0.0.1:8000/cart using AJAX), the number doesn't change in the header automatically because AJAX is already used and no page reload/refresh has occurred
                        'message' => $message,
                        // We'll use that array key 'view' as a JavaScript 'response' property to render the view (    $('#appendCartItems').html(resp.view);    ). Check front/js/custom.js
                        'view' => (string) \Illuminate\Support\Facades\View::make('front.products.cart_items')->with(compact('getCartItems')), // View Responses: https://laravel.com/docs/9.x/responses#view-responses    // Creating & Rendering Views: https://laravel.com/docs/9.x/views#creating-and-rendering-views    // Passing Data To Views: https://laravel.com/docs/9.x/views#passing-data-to-views

                        'headerview' => (string) \Illuminate\Support\Facades\View::make('front.layout.header_cart_items')->with(compact('getCartItems')), // View Responses: https://laravel.com/docs/9.x/responses#view-responses    // Creating & Rendering Views: https://laravel.com/docs/9.x/views#creating-and-rendering-views    // Passing Data To Views: https://laravel.com/docs/9.x/views#passing-data-to-views
                    ]);
                } else { // if the submitted coupon code is correct and passes the previous coupon code validation and passes all the previous if conditions (free of errors)

                    // Check if the submitted Coupon code Amount Type is 'Fixed' or 'Percentage'
                    if ($couponDetails->amount_type == 'Fixed') { // if the submitted coupon code Amount Type is 'Fixed'
                        $couponAmount = $couponDetails->amount;       // As is
                    } else {                                      // if the submitted coupon code Amount Type is 'Percentage'
                        $couponAmount = $total_amount * ($couponDetails->amount / 100);
                    }

                    $grand_total = $total_amount - $couponAmount;

                    // Assign the Coupon Code and $couponAmount to Session Variables
                    Session::put('couponAmount', $couponAmount);
                    Session::put('couponCode', $data['code']); // $data['code'] comes from the 'data' object sent from inside the $.ajax() method in front/js/custom.js file

                    $message = 'Coupon Code successfully applied. You are availing discount!';
                    $condition = session('condition', 'new');

                    return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                        'status' => true,
                        'totalCartItems' => $totalCartItems, // totalCartItems() function is in our custom Helpers/Helper.php file that we have registered in 'composer.json' file    // We created the CSS class 'totalCartItems' in front/layout/header.blade.php to use it in front/js/custom.js to update the total cart items via AJAX, because in pages that we originally use AJAX to update the cart items (such as when we delete a cart item in http://127.0.0.1:8000/cart using AJAX), the number doesn't change in the header automatically because AJAX is already used and no page reload/refresh has occurred
                        'couponAmount' => $couponAmount,
                        'grand_total' => $grand_total,
                        'message' => $message,
                        // We'll use that array key 'view' as a JavaScript 'response' property to render the view (    $('#appendCartItems').html(resp.view);    ). Check front/js/custom.js
                        'view' => (string) \Illuminate\Support\Facades\View::make('front.products.cart_items')->with(compact('getCartItems')),                   // View Responses: https://laravel.com/docs/9.x/responses#view-responses    // Creating & Rendering Views: https://laravel.com/docs/9.x/views#creating-and-rendering-views    // Passing Data To Views: https://laravel.com/docs/9.x/views#passing-data-to-views

                        'headerview' => (string) \Illuminate\Support\Facades\View::make('front.layout.header_cart_items')->with(compact('getCartItems', 'condition')), // View Responses: https://laravel.com/docs/9.x/responses#view-responses    // Creating & Rendering Views: https://laravel.com/docs/9.x/views#creating-and-rendering-views    // Passing Data To Views: https://laravel.com/docs/9.x/views#passing-data-to-views
                    ]);
                }
            }
        }
    }

    // Checkout page (using match() method for the 'GET' request for rendering the front/products/checkout.blade.php page or the 'POST' request for the HTML Form submission in the same page) (for submitting the user's Delivery Address and Payment Method))
    public function checkout(Request $request)
    {
        $logos = HeaderLogo::first();
        $sections = Section::all();
        $language = Language::get();
        $condition = $request->query('condition');
        if (! in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        // Fetch all of the world countries from the database table `countries`
        $countries = Country::where('status', 1)->get()->toArray(); // get the countries which have status = 1 (to ignore the blacklisted countries, in case)

        // Get the Cart Items of a cerain user (using their `user_id` if they're authenticated/logged in or their `session_id` if they're not authenticated/not logged in (guest))
        $getCartItems = Cart::getCartItems();

        // If the Cart is empty (If there're no Cart Items), don't allow opening/accessing the Checkout page (checkout.blade.php)
        if (count($getCartItems) == 0) {
            Log::info('Checkout redirect: Cart is empty');
            $message = 'Shopping Cart is empty! Please add products to your Cart to checkout';

            return redirect('cart')->with('error_message', $message); // redirect user to the cart.blade.php page, and show an error message in cart.blade.php
        }

        // Calculate the total price
        $total_price = 0;
        $total_weight = 0;

        foreach ($getCartItems as $item) {
            $attrPrice = Product::getDiscountAttributePrice($item['product_id'], $item['size']);
            $total_price = $total_price + ($attrPrice['final_price'] * $item['quantity']);

            // $product_weight = $item['product']['product_weight'];
            // $total_weight   = $total_weight + $product_weight;
        }

        // Fetch the user with location relations and prepare address data for checkout
        $user = User::with(['country', 'state', 'district', 'block'])->find(Auth::id());

        // Prepare a single "address" object for the view based on User Profile
        $userAddress = [
            'id' => $user->id,
            'name' => $user->name,
            'address' => $user->address,
            'city' => $user->district->name ?? '',
            'state' => $user->state->name ?? '',
            'country' => $user->country->name ?? '',
            'pincode' => $user->pincode,
            'mobile' => $user->phone,
            'country_id' => $user->country_id,
            'state_id' => $user->state_id,
            'district_id' => $user->district_id,
            'block_id' => $user->block_id,
        ];

        // Check if user has complete profile address before proceeding
        if (empty($userAddress['address']) || empty($userAddress['country']) || empty($userAddress['state']) || empty($userAddress['city']) || empty($userAddress['pincode']) || empty($userAddress['mobile'])) {
            return redirect('user/account')->with('error_message', 'Please update your delivery details in your profile before proceeding to checkout.');
        }

        // Calculating the Shipping Charges (depending on the 'country' of the user's Profile Address)
        $shippingCharges = ShippingCharge::getShippingCharges($total_weight, $userAddress['country']);
        $userAddress['shipping_charges'] = $shippingCharges;

        // Checking PIN code availability
        $userAddress['codpincodeCount'] = DB::table('cod_pincodes')->where('pincode', $user->pincode)->count();
        $userAddress['prepaidpincodeCount'] = DB::table('prepaid_pincodes')->where('pincode', $user->pincode)->count();

        // For backward compatibility with the view, wrap it in a collection or same structure
        $deliveryAddresses = [$userAddress];

        if ($request->isMethod('post')) { // if the <form> in front/products/checkout.blade.php is submitted (the HTML Form that the user submits to submit their Delivery Address and Payment Method)
            $data = $request->all();
            foreach ($getCartItems as $item) {
                // Get the specific attribute for this cart item
                $attribute = ProductsAttribute::with('product')
                    ->where('id', $item['product_attribute_id'])
                    ->first();

                if (! $attribute || ! $attribute->product) {
                    $message = 'One of the products in your cart is no longer available. Please remove it and try again.';

                    return redirect('/cart')->with('error_message', $message);
                }

                // Prevent 'disabled' (`status` = 0) products from being ordered
                $product_status = Product::getProductStatus($item['product_id']);
                if ($product_status == 0) {
                    $message = $attribute->product->product_name . ' is not available. Please remove it from the Cart and choose another product.';

                    return redirect('/cart')->with('error_message', $message);
                }

                // Check attribute status (must be enabled)
                if ($attribute->status == 0) {
                    $message = $attribute->product->product_name . ' with ' . ($item['size'] ?? '') . ' size is not available. Please remove it from the Cart and choose another product.';

                    return redirect('/cart')->with('error_message', $message);
                }

                // Check stock for this specific attribute
                $availableStock = (int) $attribute->stock;
                if ($availableStock == 0 || $item['quantity'] > $availableStock) {
                    $message = $attribute->product->product_name . ' with ' . ($item['size'] ?? '') . ' size stock is not available/enough. Please reduce quantity or remove it from the Cart.';

                    return redirect('/cart')->with('error_message', $message);
                }

                // Check category status
                $getCategoryStatus = Category::getCategoryStatus($attribute->product->category_id);
                if ($getCategoryStatus == 0) {
                    $message = $attribute->product->product_name . ' category is disabled. Please remove it from the Cart and choose another product.';

                    return redirect('/cart')->with('error_message', $message);
                }
            }

            if (empty($data['address_id'])) { // if the user doesn't select a Delivery Address
                $message = 'Please select Delivery Address!';

                return redirect()->back()->with('error_message', $message);
            }

            // Payment Method Validation
            if (empty($data['payment_gateway'])) { // if the user doesn't select a Delivery Address
                $message = 'Please select Payment Method!';

                return redirect()->back()->with('error_message', $message);
            }

            // Agree to T&C (Accept Terms and Conditions) Validation
            if (empty($data['accept'])) { // if the user doesn't select a Delivery Address
                $message = 'Please agree to T&C!';

                return redirect()->back()->with('error_message', $message);
            }

            $user = User::with(['country', 'state', 'district'])->find(Auth::id());
            $deliveryAddress = [
                'name' => $user->name,
                'address' => $user->address,
                'pincode' => $user->pincode,
                'mobile' => $user->phone,
                'country' => $user->country->name ?? '',
                'state' => $user->state->name ?? '',
                'city' => $user->district->name ?? '',
            ];

            if ($data['payment_gateway'] == 'COD') {
                $payment_method = 'COD';
                $order_status = 'New';
            } else {
                $payment_method = 'Prepaid';
                $order_status = 'Pending';
            }

            DB::beginTransaction();

            $total_price = 0;
            foreach ($getCartItems as $item) {
                // Use attribute-based pricing (per size) from ProductsAttribute table
                $getDiscountAttributePrice = Product::getDiscountAttributePrice(
                    $item['product_id'],
                    $item['size'] ?? null
                );
                $total_price += ($getDiscountAttributePrice['final_price'] * $item['quantity']);
            }

            // Calculate Shipping Charges `shipping_charges`
            $shipping_charges = 0;

            // Get the Shipping Charge based on the chosen Delivery Address
            $shipping_charges = ShippingCharge::getShippingCharges($total_weight, $deliveryAddress['country']);

            // Grand Total (`grand_total`)
            $grand_total = $total_price + $shipping_charges - Session::get('couponAmount');

            // Wallet Deduction Logic
            $wallet_amount = 0;
            if (isset($data['use_wallet']) && $data['use_wallet'] == 1 && Auth::user()->wallet_balance > 0) {
                $wallet_amount = min(Auth::user()->wallet_balance, 20);
                $grand_total = $grand_total - $wallet_amount;
            }

            Session::put('grand_total', $grand_total);
            $order = new Order;
            $order->user_id = Auth::user()->id; // Retrieving The Authenticated User: https://laravel.com/docs/9.x/authentication#retrieving-the-authenticated-user
            $order->name = $deliveryAddress['name'];
            $order->address = $deliveryAddress['address'];
            $order->city = $deliveryAddress['city'];
            $order->state = $deliveryAddress['state'];
            $order->country = $deliveryAddress['country'];
            $order->pincode = $deliveryAddress['pincode'];
            $order->mobile = $deliveryAddress['mobile'];
            $order->email = Auth::user()->email; // Retrieving The Authenticated User: https://laravel.com/docs/9.x/authentication#retrieving-the-authenticated-user
            $order->shipping_charges = $shipping_charges;
            $order->coupon_code = Session::get('couponCode');   // it was set inside applyCoupon() method
            $order->coupon_amount = Session::get('couponAmount'); // it was set inside applyCoupon() method
            $order->order_status = $order_status;
            $order->payment_method = $payment_method;
            $order->payment_gateway = $data['payment_gateway'];
            $order->grand_total = $grand_total;
            $order->wallet_amount = $wallet_amount;
            $order->extra_discount = 0;

            $order->save();

            if ($wallet_amount > 0) {
                // Deduct from User Balance
                $user = User::find(Auth::user()->id);
                $user->wallet_balance -= $wallet_amount;
                $user->save();

                // Create Wallet Transaction Entry
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => $wallet_amount,
                    'type' => 'debit',
                    'description' => 'Used for order #' . $order->id,
                ]);
            }
            $order_id = DB::getPdo()->lastInsertId();

            foreach ($getCartItems as $item) {
                $cartItem = new OrdersProduct;
                $cartItem->order_id = $order_id;
                $cartItem->user_id = Auth::user()->id;

                // Get the specific attribute row for this cart item
                $attribute = ProductsAttribute::with('product')
                    ->where('id', $item['product_attribute_id'])
                    ->where('status', 1)
                    ->first();

                if (! $attribute || ! $attribute->product) {
                    DB::rollBack();

                    return redirect('/cart')->with('error_message', 'One of the products in your cart is no longer available.');
                }

                // Continue filling in data into the `orders_products` table
                // Set default values if null (0 for admin products, actual vendor_id for vendor products)
                $cartItem->admin_id = $attribute->admin_id ?? 0;
                $cartItem->vendor_id = $attribute->vendor_id ?? 0;

                if ($attribute->vendor_id && $attribute->vendor_id > 0) { // if the order product's seller is a 'vendor'
                    $vendorCommission = Vendor::getVendorCommission($attribute->vendor_id);
                    $cartItem->commission = $vendorCommission;
                }

                $cartItem->product_id = $attribute->product_id;
                $cartItem->product_name = $attribute->product->product_name;

                // Price based on this attribute (size) using discount rules
                $priceDetails = Product::getDiscountAttributePrice(
                    $attribute->product_id,
                    $item['size'] ?? null
                );
                $cartItem->product_price = $priceDetails['final_price'];

                // Stock check for this specific attribute
                $availableStock = (int) $attribute->stock;
                if ($item['quantity'] > $availableStock) {
                    DB::rollBack();
                    $message = $attribute->product->product_name
                        . ' with ' . ($item['size'] ?? '') . ' size stock is not available/enough for your order. Please reduce its quantity and try again!';

                    return redirect('/cart')->with('error_message', $message);
                }

                $cartItem->product_qty = $item['quantity'];

                // Persist the order product row now that all fields are set
                $cartItem->save();
            }

            Session::put('order_id', $order_id);

            DB::commit();
            $orderDetails = Order::with('orders_products')->where('id', $order_id)->first()->toArray();

            if ($data['payment_gateway'] == 'COD') {

                $email = Auth::user()->email;
                $messageData = [
                    'email' => $email,
                    'name' => Auth::user()->name,
                    'order_id' => $order_id,
                    'orderDetails' => $orderDetails,
                ];

                try {
                    \Illuminate\Support\Facades\Mail::send('emails.order', $messageData, function ($message) use ($email) { // Sending Mail: https://laravel.com/docs/9.x/mail#sending-mail
                        $message->to($email)->subject('Order Placed - MultiVendorEcommerceApplication.com.eg');
                    });
                } catch (\Throwable $e) {
                    Log::warning('Order email failed to send: ' . $e->getMessage());
                }
            } elseif ($data['payment_gateway'] == 'Paypal') {
                // redirect the user to the PayPalController.php (after saving the order details in `orders` and `orders_products` tables)
                return redirect('/paypal');

                // redirect the user to the IyzipayController.php (after saving the order details in `orders` and `orders_products` tables)
                return redirect('/iyzipay');
            } elseif ($data['payment_gateway'] == 'Razorpay') {
                return redirect('/razorpay');
            } else {
                echo 'Other Prepaid payment methods coming soon';
            }

            Log::info('Checkout success: Order ID ' . $order_id . ' saved. Redirecting to thanks.');

            return redirect('thanks'); // redirect to front/products/thanks.blade.php page
        }

        $footerProducts = Product::orderBy('id', 'Desc')
            ->where('condition', $condition)
            ->where('status', 1)
            ->take(3)
            ->get()
            ->toArray();

        return view('front.products.checkout')->with(compact('deliveryAddresses', 'countries', 'getCartItems', 'total_price', 'condition', 'footerProducts', 'logos', 'sections', 'language'));
    }

    // Rendering Thanks page (after placing an order)
    public function thanks()
    {
        $logos = HeaderLogo::first();
        $sections = Section::all();
        $language = Language::get();
        $condition = 'new';
        // if (! in_array($condition, ['new', 'old'])) {
        //     $condition = 'new';
        // }
        if (Session::has('order_id')) {
            $order_id = Session::get('order_id');
            // Wallet Credit Logic
            \App\Models\WalletTransaction::checkAndCreditWallet($order_id);

            // We empty the Cart after placing the order
            Cart::where('user_id', Auth::user()->id)->delete();

            $orderDetails = Order::find($order_id);

            return view('front.products.thanks')->with(compact('logos', 'sections', 'language', 'condition', 'orderDetails'));
        } else {
            Log::info('Thanks redirect: order_id missing from session');

            return redirect('cart')->with('error_message', 'Order session expired. Your cart is preserved.'); // added message for debug
        }
    }

    // PIN code Availability Check: check if the PIN code of the user's Delivery Address exists in our database (in both `cod_pincodes` and `prepaid_pincodes`) or not in front/products/detail.blade.php via AJAX. Check front/js/custom.js
    public function checkPincode(Request $request)
    {
        if ($request->ajax()) {  // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)

            // Checking PIN code availability of BOTH COD and Prepaid PIN codes in BOTH `cod_pincodes` and `prepaid_pincodes` tables
            // Check if the COD PIN code of that Delivery Address of the user exists in `cod_pincodes` table
            $codPincodeCount = DB::table('cod_pincodes')->where('pincode', $data['pincode'])->count(); // $data['pincode'] comes from the 'data' object sent from inside the $.ajax() method in front/js/custom.js file

            // Check if the Prepaid PIN code of that Delivery Address of the user exists in `prepaid_pincodes` table
            $prepaidPincodeCount = DB::table('prepaid_pincodes')->where('pincode', $data['pincode'])->count(); // $data['pincode'] comes from the 'data' object sent from inside the $.ajax() method in front/js/custom.js file

            // Check if the entered PIN code exists in BOTH `cod_pincodes` and `prepaid_pincodes` tables
            if ($codPincodeCount == 0 && $prepaidPincodeCount == 0) {
                echo 'This pincode is not available for delivery';
            } else {
                echo 'This pincode is available for delivery';
            }
        }
    }

    // Static method to get header cart data for AppServiceProvider
    public static function getHeaderCartData()
    {
        $getCartItems = Cart::getCartItems();
        $totalPrice = 0;
        $cartItemsCount = 0;

        foreach ($getCartItems as $item) {
            $getDiscountPriceDetails = Product::getDiscountPriceDetails($item['product_id']);
            $totalPrice += $getDiscountPriceDetails['final_price'] * $item['quantity'];
            $cartItemsCount += $item['quantity'];
        }

        return [
            'cartItems' => $getCartItems,
            'totalPrice' => $totalPrice,
            'cartItemsCount' => $cartItemsCount,
        ];
    }

    // Static method to get header wishlist data for AppServiceProvider
    public static function getHeaderWishlistData()
    {
        $getWishlistItems = Wishlist::getWishlistItems();
        $wishlistItemsCount = 0;

        foreach ($getWishlistItems as $item) {
            $wishlistItemsCount += $item['quantity'];
        }

        // Log for debugging
        Log::info('Header Wishlist Data - Items: ' . count($getWishlistItems) . ', Count: ' . $wishlistItemsCount . ', Session ID: ' . Session::get('session_id'));

        return [
            'wishlistItems' => $getWishlistItems,
            'wishlistItemsCount' => $wishlistItemsCount,
        ];
    }

    // Get wishlist status for multiple products
    public static function getWishlistStatusForProducts(array $productIds): array
    {
        $wishlistStatus = [];
        foreach ($productIds as $productId) {
            $wishlistStatus[$productId] = Wishlist::isProductInWishlist($productId);
        }

        return $wishlistStatus;
    }
}
