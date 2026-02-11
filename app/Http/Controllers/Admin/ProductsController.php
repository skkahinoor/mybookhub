<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Response;
use App\Models\Product;
use App\Models\ProductsImage;
use App\Models\ProductsFilter;
use App\Models\ProductsAttribute;
use App\Models\Subject;
use App\Models\Language;
use App\Models\Category;
use App\Models\Section;
use App\Models\Edition;
use App\Models\Publisher;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use App\Models\HeaderLogo;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProductsController extends Controller
{
    public function importIndex()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        return view('admin.products.importbook', compact('logos', 'headerLogo'));
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/Book-Import-Template.xlsx');

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error_message', 'Template file not found.');
        }

        return response()->download(
            $filePath,
            'Book-Import-Template.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'import' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $rows = Excel::toArray([], $request->file('import'))[0];

            // Normalize headers
            $header = array_map(fn($h) => strtolower(trim($h)), $rows[0]);
            unset($rows[0]);

            // Case-insensitive maps
            $sections   = Section::pluck('id', 'name')->mapWithKeys(fn($v, $k) => [strtolower($k) => $v])->toArray();
            $categories = Category::pluck('id', 'category_name')->mapWithKeys(fn($v, $k) => [strtolower($k) => $v])->toArray();
            $publishers = Publisher::pluck('id', 'name')->mapWithKeys(fn($v, $k) => [strtolower($k) => $v])->toArray();
            $subjects   = Subject::pluck('id', 'name')->mapWithKeys(fn($v, $k) => [strtolower($k) => $v])->toArray();
            $languages  = Language::pluck('id', 'name')->mapWithKeys(fn($v, $k) => [strtolower($k) => $v])->toArray();
            $editions   = Edition::pluck('id', 'edition')->mapWithKeys(fn($v, $k) => [strtolower($k) => $v])->toArray();
            $authorsMap = Author::pluck('id', 'name')->mapWithKeys(fn($v, $k) => [strtolower($k) => $v])->toArray();

            $inserted = 0;
            $skippedDuplicate = 0;
            $skippedInvalid = 0;

            foreach ($rows as $row) {

                if (!array_filter($row)) {
                    $skippedInvalid++;
                    continue;
                }

                $data = array_combine($header, $row);

                $productName = trim($data['product name'] ?? '');
                $isbn        = trim($data['isbn number'] ?? '');
                $condition   = strtolower(trim($data['book condition'] ?? 'new'));

                if (!$productName || !$isbn) {
                    $skippedInvalid++;
                    continue;
                }

                // ---------- SECTION ----------
                $sectionName = trim($data['section'] ?? '');
                if (!$sectionName) {
                    $skippedInvalid++;
                    continue;
                }

                if (!isset($sections[strtolower($sectionName)])) {
                    $section = Section::create([
                        'name'   => ucwords($sectionName),
                        'status' => 1
                    ]);
                    $sections[strtolower($sectionName)] = $section->id;
                }
                $sectionId = $sections[strtolower($sectionName)];

                // ---------- CATEGORY ----------
                $categoryName = trim($data['category'] ?? '');
                if (!$categoryName) {
                    $skippedInvalid++;
                    continue;
                }

                $ROOT_CATEGORY_ID = 0;

                if (!isset($categories[strtolower($categoryName)])) {
                    $category = Category::create([
                        'category_name' => ucwords($categoryName),
                        'section_id'    => $sectionId,
                        'parent_id'     => $ROOT_CATEGORY_ID,
                        'status'        => 1
                    ]);
                    $categories[strtolower($categoryName)] = $category->id;
                }
                $categoryId = $categories[strtolower($categoryName)];

                // ---------- OTHER FOREIGN KEYS ----------
                $publisherId = $this->autoCreate($publishers, Publisher::class, $data['publisher'] ?? null);
                $subjectId   = $this->autoCreate($subjects, Subject::class, $data['subject'] ?? null);
                $languageId  = $this->autoCreate($languages, Language::class, $data['language'] ?? null);
                $editionId   = $this->autoCreate($editions, Edition::class, $data['edition'] ?? null, [
                    'edition' => trim($data['edition'] ?? '')
                ]);

                // ---------- DUPLICATE CHECK ----------
                if (Product::where('product_isbn', $isbn)->where('condition', $condition)->exists()) {
                    $skippedDuplicate++;
                    continue;
                }

                // ---------- IMAGE (JUST VERIFY & SAVE NAME) ----------
                $imageName = null;

                if (!empty($data['image'])) {

                    $tempImage = trim($data['image']);

                    // Check image exists in ANY size folder (large is enough)
                    if (file_exists(public_path('front/images/product_images/large/' . $tempImage))) {
                        $imageName = $tempImage;
                    }
                }

                // ---------- PRODUCT ----------
                $product = Product::create([
                    'section_id'    => $sectionId,
                    'category_id'   => $categoryId,
                    'publisher_id'  => $publisherId,
                    'subject_id'    => $subjectId,
                    'edition_id'    => $editionId,
                    'language_id'   => $languageId,
                    'product_name'  => $productName,
                    'condition'     => $condition,
                    'product_isbn'  => $isbn,
                    'product_price' => $data['price'] ?? 0,
                    'product_image' => $imageName,
                    'status'        => 1,
                ]);

                // ---------- AUTHORS (MANY-TO-MANY) ----------
                if (!empty($data['authors'])) {
                    $authorIds = [];

                    foreach (explode(',', $data['authors']) as $authorName) {
                        $key = strtolower(trim($authorName));

                        if (!isset($authorsMap[$key])) {
                            $author = Author::create([
                                'name'   => ucwords(trim($authorName)),
                                'status' => 1
                            ]);
                            $authorsMap[$key] = $author->id;
                        }

                        $authorIds[] = $authorsMap[$key];
                    }

                    $product->authors()->sync($authorIds);
                }

                $inserted++;
            }

            DB::commit();

            return back()->with(
                'success_message',
                "Import completed ✅
        Inserted: {$inserted}
        Skipped (Duplicate): {$skippedDuplicate}
        Skipped (Invalid): {$skippedInvalid}"
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error_message', 'Import failed');
        }
    }

    public function importImages()
    {
        return view('admin.products.import_images');
    }

    public function uploadImages(Request $request)
    {
        $request->validate([
            'images'   => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {

                // ORIGINAL IMAGE NAME (NOT CHANGED)
                $imageName = $image->getClientOriginalName();
                $image_tmp = $image->getRealPath();

                // Large
                Image::make($image_tmp)->resize(1000, 1000, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save('front/images/product_images/large/' . $imageName);

                // Medium
                Image::make($image_tmp)->resize(500, 500, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save('front/images/product_images/medium/' . $imageName);

                // Small
                Image::make($image_tmp)->resize(250, 250, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save('front/images/product_images/small/' . $imageName);
            }
        }

        return redirect()->back()->with('success_message', 'Images uploaded successfully!');
    }

    private function autoCreate(&$map, $model, $name, $extra = [])
    {
        if (!$name) {
            return null;
        }

        $key = strtolower(trim($name));

        if (!isset($map[$key])) {
            $record = $model::create(array_merge([
                'name'   => ucwords(trim($name)),
                'status' => 1
            ], $extra));

            $map[$key] = $record->id;
        }

        return $map[$key];
    }

    public function products()
    {
        Session::put('page', 'products');

        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        $admin      = Auth::guard('admin')->user();
        $adminType  = $admin->type;
        $vendor_id  = $admin->vendor_id;

        // Vendor approval check
        if ($adminType === 'vendor' && $admin->status == 0) {
            return redirect('vendor/update-vendor-details/personal')
                ->with(
                    'error_message',
                    'Your Vendor Account is not approved yet. Please complete your personal, business, and bank details.'
                );
        }

        if ($adminType === 'vendor') {

            $products = ProductsAttribute::where('vendor_id', $vendor_id)
                ->where('admin_type', 'vendor')
                ->orderBy('id', 'desc')
                ->with([
                    'product:id,product_name,product_isbn,product_image,category_id,section_id,condition,publisher_id,edition_id,language_id',
                    'product.category:id,category_name',
                    'product.section:id,name',
                    'product.publisher:id,name',
                    'product.edition:id,edition',
                    'product.language:id,name',
                    'vendor:id,user_id',
                    'admin:id,name',
                ])
                ->get();
        } else {

            $products = Product::orderBy('id', 'desc')
                ->with([
                    'category:id,category_name',
                    'section:id,name',
                    'publisher:id,name',
                    'edition:id,edition',
                    'language:id,name',
                ])
                ->get();

            foreach ($products as $product) {
                $product->total_stock = ProductsAttribute::where('product_id', $product->id)
                    ->sum('stock');
            }
        }

        return view(
            'admin.products.products',
            compact('products', 'logos', 'headerLogo', 'adminType')
        );
    }


    public function updateProductStatus(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = $request->all();
                $status = ($data['status'] == 'Active') ? 0 : 1;
                $admin = Auth::guard('admin')->user();

                if ($admin->type === 'vendor') {
                    // Vendors update their own product attributes status
                    ProductsAttribute::where('id', $data['product_id'])
                        ->where('vendor_id', $admin->vendor_id)
                        ->update(['status' => $status]);
                } else {
                    // Admin/Superadmin updates the global product status
                    Product::where('id', $data['product_id'])->update(['status' => $status]);
                }

                return response()->json([
                    'status'     => $status,
                    'product_id' => $data['product_id']
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
        return redirect()->back();
    }

    public function deleteProductAttribute($id)
    {
        // Delete a specific product attribute (not the product itself)
        $attribute = ProductsAttribute::find($id);

        if ($attribute) {
            $attribute->delete();
            return redirect()->back()->with('success_message', 'Product attribute has been deleted successfully!');
        }

        return redirect()->back()->with('error_message', 'Attribute not found or already deleted.');
    }

    public function addEditProduct(Request $request, $id = null)
    {
        Session::put('page', 'products');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        if ($id == '') {
            $title = 'Add Book';
            $product = new Product();
            $message = 'Book added successfully!';
        } else {
            $title = 'Edit Book';
            $product = Product::findOrFail($id);
            $message = 'Book updated successfully!';
        }

        if ($request->isMethod('post')) {

            $data = $request->all();
            $user = Auth::guard('admin')->user();

            // Check if product with this ISBN AND condition already exists (BEFORE validation)
            $existingProduct = null;
            $skipProductSave = false;

            if ($id == null) {
                // Check for product with same ISBN AND same condition
                $existingProduct = Product::where('product_isbn', $data['product_isbn'])
                    ->where('condition', $data['condition'])
                    ->first();

                if ($existingProduct) {
                    // Check if current admin/vendor already has this product (same ISBN + same condition)
                    $existingAttributeQuery = ProductsAttribute::where('product_id', $existingProduct->id);

                    if ($user->type === 'vendor') {
                        $existingAttributeQuery->where('vendor_id', $user->vendor_id)
                            ->where('admin_type', 'vendor');
                    } else {
                        $existingAttributeQuery->where('admin_id', $user->id)
                            ->where('admin_type', 'admin');
                    }

                    $existingAttribute = $existingAttributeQuery->first();

                    if ($existingAttribute) {
                        $userType = $user->type === 'vendor' ? 'vendor' : 'admin';
                        $conditionText = $data['condition'] === 'new' ? 'new' : 'old';
                        $errorMessage = "This {$conditionText} product (ISBN: {$data['product_isbn']}) already exists in your {$userType} account. Please choose a different product or edit the existing one.";

                        // Return JSON response for AJAX requests
                        if ($request->ajax() || $request->expectsJson()) {
                            return response()->json([
                                'status'           => 'error',
                                'product_exists'   => true,
                                'message'          => $errorMessage,
                                'product_id'       => $existingProduct->id,
                                'stock'            => (int) ($existingAttribute->stock ?? 0),
                                'product_discount' => (float) ($existingAttribute->product_discount ?? 0),
                            ], 422);
                        }

                        // Return redirect for regular form submissions
                        return redirect()->back()
                            ->withInput()
                            ->with('error_message', $errorMessage);
                    }

                    // Product exists with same ISBN + condition but current user doesn't have it - we'll reuse it
                    $skipProductSave = true;
                } else {
                    // Check if product exists with same ISBN but different condition
                    // This is allowed - user can add same ISBN with different condition
                    $productWithDifferentCondition = Product::where('product_isbn', $data['product_isbn'])
                        ->where('condition', '!=', $data['condition'])
                        ->first();

                    // If product exists with different condition, it's a new product entry (not reusing)
                    // So we don't set skipProductSave = true
                }
            }

            // Conditional validation: ISBN uniqueness is now based on ISBN + condition combination
            // Since we allow same ISBN with different conditions, we need custom validation
            $isbnValidationRule = 'required|digits_between:10,13';

            if ($id == null) {
                // For new products, check if ISBN + condition combination already exists
                // We'll handle this in custom validation after checking existingProduct
                if ($existingProduct) {
                    // Product exists with same ISBN + condition - validation will pass (we already checked user doesn't have it)
                    $isbnValidationRule = 'required|digits_between:10,13';
                } else {
                    // New product - check uniqueness of ISBN + condition combination
                    $isbnValidationRule = 'required|digits_between:10,13';
                }
            } else {
                // Editing existing product - require unique ISBN + condition except for current product
                $isbnValidationRule = 'required|digits_between:10,13';
            }

            $validator = Validator::make($request->all(), [
                'category_id'   => 'required',
                'condition'     => 'required|in:new,old',
                'product_name'  => 'required',
                'product_isbn'  => $isbnValidationRule,
                'product_price' => 'required|numeric',
                'language_id'   => 'required',
            ]);

            // Custom validation: Check ISBN + condition uniqueness
            // Note: We already checked for existingProduct above, but this ensures validation consistency
            if ($id == null) {
                // For new products: if we're not reusing an existing product, check uniqueness
                if (!$existingProduct) {
                    $validator->after(function ($validator) use ($data) {
                        $exists = Product::where('product_isbn', $data['product_isbn'])
                            ->where('condition', $data['condition'])
                            ->exists();

                        if ($exists) {
                            $conditionText = $data['condition'] === 'new' ? 'new' : 'old';
                            $validator->errors()->add('product_isbn', "A {$conditionText} product with this ISBN already exists. You can reuse it by selecting the same condition, or add it as a different condition.");
                        }
                    });
                }
                // If existingProduct exists, validation passes (we already verified user doesn't have it)
            } else {
                // For editing: check ISBN + condition uniqueness except current product
                $validator->after(function ($validator) use ($data, $id) {
                    $exists = Product::where('product_isbn', $data['product_isbn'])
                        ->where('condition', $data['condition'])
                        ->where('id', '!=', $id)
                        ->exists();

                    if ($exists) {
                        $conditionText = $data['condition'] === 'new' ? 'new' : 'old';
                        $validator->errors()->add('product_isbn', "A {$conditionText} product with this ISBN already exists.");
                    }
                });
            }

            if ($validator->fails()) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // If reusing existing product, set it here
            if ($existingProduct && $id == null) {
                $product = $existingProduct;
            }

            // Only update product fields if we're not reusing an existing product
            if (!$skipProductSave) {
                if ($request->hasFile('product_image')) {
                    $image_tmp = $request->file('product_image');
                    if ($image_tmp->isValid()) {
                        $extension = $image_tmp->getClientOriginalExtension();
                        $imageName = rand(111, 99999) . '.' . $extension;

                        Image::make($image_tmp)->resize(1000, 1000)
                            ->save('front/images/product_images/large/' . $imageName);
                        Image::make($image_tmp)->resize(500, 500)
                            ->save('front/images/product_images/medium/' . $imageName);
                        Image::make($image_tmp)->resize(250, 250)
                            ->save('front/images/product_images/small/' . $imageName);

                        $product->product_image = $imageName;
                    }
                } elseif ($id == null) {
                    // No new image uploaded for a NEW product.
                    // If there is already another product with the same ISBN (any condition)
                    // and it has an image, reuse that image for this new record.
                    $imageSourceProduct = Product::where('product_isbn', $data['product_isbn'])
                        ->whereNotNull('product_image')
                        ->first();

                    if ($imageSourceProduct) {
                        $product->product_image = $imageSourceProduct->product_image;
                    }
                }

                $categoryDetails = Category::find($data['category_id']);
                $product->section_id  = $categoryDetails->section_id;
                $product->category_id = $data['category_id'];

                if (!empty($data['new_publisher'])) {
                    $publisher = new Publisher();
                    $publisher->name = $data['new_publisher'];
                    $publisher->status = 1;
                    $publisher->save();
                    $product->publisher_id = $publisher->id;
                } else {
                    $product->publisher_id = $data['publisher_id'];
                }

                $product->subject_id  = $data['subject_id'];
                $product->language_id = $data['language_id'];

                $product->condition        = $data['condition'];
                $product->product_name     = $data['product_name'];
                $product->product_isbn     = $data['product_isbn'];
                $product->product_price    = $data['product_price'];
                $product->edition_id       = $data['edition_id'];
                $product->description      = $data['description'];
                $product->meta_title       = $data['meta_title'];
                $product->meta_keywords    = $data['meta_keywords'];
                $product->meta_description = $data['meta_description'];
                $product->status           = 1;
            }

            if (!$skipProductSave) {
                $productFilters = ProductsFilter::productFilters();

                foreach ($productFilters as $filter) {
                    $filterAvailable = ProductsFilter::filterAvailable($filter['id'], $data['category_id']);
                    if ($filterAvailable == 'Yes') {
                        if (
                            isset($filter['filter_column']) &&
                            isset($data[$filter['filter_column']]) &&
                            $data[$filter['filter_column']] !== ''
                        ) {
                            $product->{$filter['filter_column']} = $data[$filter['filter_column']];
                        }
                    }
                }
            }

            // Check Free plan limit for new products (not updates)
            if ($id == null && $user->type === 'vendor' && !$skipProductSave) {
                $vendor = \App\Models\Vendor::find($user->vendor_id);
                if ($vendor && $vendor->plan === 'free') {
                    // Get dynamic limit from settings
                    $freePlanBookLimit = (int) Setting::getValue('free_plan_book_limit', 100);

                    // Count products added this month
                    $currentMonthStart = now()->startOfMonth();
                    $productsThisMonth = Product::whereHas('firstAttribute', function ($q) use ($vendor) {
                        $q->where('vendor_id', $vendor->id)
                            ->where('admin_type', 'vendor');
                    })
                        ->where('created_at', '>=', $currentMonthStart)
                        ->count();

                    if ($productsThisMonth >= $freePlanBookLimit) {
                        return redirect('vendor/products')
                            ->with('error_message', "You have reached the monthly limit of {$freePlanBookLimit} products for Free plan. Please upgrade to Pro plan for unlimited uploads.");
                    }
                }
            }

            if (!$skipProductSave) {
                $product->save();
                // Only sync authors if we're saving the product (not reusing)
                $product->authors()->sync($request->author_id ?? []);
            }

            // For new products, return JSON to show modal for stock/discount
            if ($id == null) {
                // Create notification when a new product is added
                $vendorId = null; // Always null so notification is visible to admin only
                $notificationMessage = '';

                if ($user->type === 'vendor') {
                    // Keep vendor_id as null so only admin sees this notification
                    $vendorName = $user->name;
                    $notificationMessage = "Vendor '{$vendorName}' added a new product '{$product->product_name}' (ISBN: {$product->product_isbn}).";
                } else {
                    $adminName = $user->name;
                    $notificationMessage = "Admin '{$adminName}' added a new product '{$product->product_name}' (ISBN: {$product->product_isbn}).";
                }

                Notification::create([
                    'type' => 'product_added',
                    'title' => 'New Product Added',
                    'message' => $notificationMessage,
                    'related_id' => $product->id,
                    'related_type' => 'App\Models\Product',
                    'vendor_id' => $vendorId, // null so only admin can see vendor-added product notifications
                    'is_read' => false,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'product_id' => $product->id,
                    'show_modal' => true
                ]);
            }

            // For existing products, redirect normally
            return redirect('admin/products')->with('success_message', $message);
        }

        $categories = Section::with('categories')->get()->toArray();
        $publishers = Publisher::where('status', 1)->get()->toArray();
        $authors    = Author::where('status', 1)->get();
        $subjects   = Subject::where('status', 1)->get()->toArray();
        $languages  = Language::get();
        $editions   = Edition::all();

        return view('admin.products.add_edit_product')->with(compact(
            'title',
            'product',
            'categories',
            'publishers',
            'authors',
            'subjects',
            'languages',
            'editions',
            'logos',
            'headerLogo'
        ));
    }

    public function getAuthor(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $q = $request->input('q');

        return Author::where('name', 'like', "%{$q}%")
            ->select('id', 'name')
            ->get();
    }

    public function saveProductAttributes(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0',
            'product_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = Auth::guard('admin')->user();
        $data = $request->all();

        $attributeQuery = ProductsAttribute::where('product_id', $data['product_id']);

        if ($user->type === 'vendor') {
            $attributeQuery->where('vendor_id', $user->vendor_id)
                ->where('admin_type', 'vendor');
        } else {
            $attributeQuery->where('admin_id', $user->id)
                ->where('admin_type', 'admin')
                ->orWhere('admin_type', 'superadmin');
        }

        $existingAttribute = $attributeQuery->first();

        if ($existingAttribute) {
            // Update existing attribute
            $existingAttribute->stock = (int) $data['stock'];
            $existingAttribute->product_discount = (float) ($data['product_discount'] ?? 0);
            $existingAttribute->save();
        } else {
            // Create new attribute
            $attribute = new ProductsAttribute();
            $attribute->product_id = $data['product_id'];
            $attribute->stock = (int) $data['stock'];
            $attribute->product_discount = (float) ($data['product_discount'] ?? 0);
            $attribute->sku = 'BH' . '-' . 'P' . $data['product_id'] . '-' . 'V' . $user->vendor_id;
            $attribute->status = 1;

            if ($user->type === 'vendor') {
                $attribute->admin_type = 'vendor';
                $attribute->vendor_id = $user->vendor_id;
                $attribute->admin_id = null;
            } else {
                $attribute->admin_type = 'admin';
                $attribute->admin_id = $user->id;
                $attribute->vendor_id = null;
            }

            $attribute->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product attributes saved successfully!'
        ]);
    }

    public function deleteProductImage($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $productImage = Product::select('product_image')->where('id', $id)->first();
        // dd($productImage);

        // Get the product image three paths on the server (filesystem) ('small', 'medium' and 'large' folders)
        $small_image_path  = 'front/images/product_images/small/';
        $medium_image_path = 'front/images/product_images/medium/';
        $large_image_path  = 'front/images/product_images/large/';

        // Delete the product physical actual images on server (filesystem) (from the the THREE folders)
        // First: Delete from the 'small' folder
        if (file_exists($small_image_path . $productImage->product_image)) {
            unlink($small_image_path . $productImage->product_image);
        }

        // Second: Delete from the 'medium' folder
        if (file_exists($medium_image_path . $productImage->product_image)) {
            unlink($medium_image_path . $productImage->product_image);
        }

        // Third: Delete from the 'large' folder
        if (file_exists($large_image_path . $productImage->product_image)) {
            unlink($large_image_path . $productImage->product_image);
        }


        // Delete the product image name (record) from the `products` database table (Note: We won't use delete() method because we're not deleting a complete record (entry) (we're just deleting a one column `product_image` value), we will just use update() method to update the `product_image` name to an empty string value '')
        Product::where('id', $id)->update(['product_image' => '']);

        $message = 'Book Image has been deleted successfully!';


        return redirect()->back()->with('success_message', $message, 'logos');
        return view('admin.products.products', compact('products', 'logos', 'headerLogo'));
    }

    public function addAttributes(Request $request, $id)
    {
        Session::put('page', 'products');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $product = Product::select('id', 'product_name', 'product_isbn', 'product_price', 'product_image')->with('attributes')->find($id); // with('attributes') is the relationship method name in the Product.php model

        if ($request->isMethod('post')) { // When the <form> is submitted
            $data = $request->all();
            // dd($data);

            foreach ($data['sku'] as $key => $value) { // or instead could be: $data['size'], $data['price'] or $data['stock']
                // echo '<pre>', var_dump($key), '</pre>';
                // echo '<pre>', var_dump($value), '</pre>';

                if (!empty($value)) {
                    // Validation:
                    // SKU duplicate check (Prevent duplicate SKU) because SKU is UNIQUE for every product
                    $skuCount = ProductsAttribute::where('sku', $value)->count();
                    if ($skuCount > 0) { // if there's an SKU for the product ALREADY EXISTING
                        return redirect()->back()->with('error_message', 'SKU already exists! Please add another SKU!', 'logos');
                        return view('admin.products.products', compact('products', 'logos', 'headerLogo'));
                    }

                    // Size duplicate check (Prevent duplicate Size) because Size is UNIQUE for every product
                    $sizeCount = ProductsAttribute::where(['product_id' => $id, 'size' => $data['size'][$key]])->count();
                    if ($sizeCount > 0) { // if there's an SKU for the product ALREADY EXISTING
                        return redirect()->back()->with('error_message', 'Size already exists! Please add another Size!', 'logos');
                        return view('admin.products.products', compact('products', 'logos', 'headerLogo'));
                    }


                    $attribute = new ProductsAttribute;

                    $attribute->product_id = $id; // $id is passed in up there to the addAttributes() method
                    $attribute->sku        = $value;
                    $attribute->size       = $data['size'][$key];  // $key denotes the iteration/loop cycle number (0, 1, 2, ...), e.g. $data['size'][0]
                    $attribute->price      = $data['price'][$key]; // $key denotes the iteration/loop cycle number (0, 1, 2, ...), e.g. $data['price'][0]
                    $attribute->stock      = $data['stock'][$key]; // $key denotes the iteration/loop cycle number (0, 1, 2, ...), e.g. $data['stock'][0]
                    $attribute->status     = 1;

                    $attribute->save();
                }
            }
            return redirect()->back()->with('success_message', 'Book Attributes have been addded successfully!', 'logos');
            return view('admin.products.products', compact('products', 'logos', 'headerLogo'));
        }


        return view('admin.attributes.add_edit_attributes')->with(compact('product', 'logos', 'headerLogo'));
    }

    public function updateAttributeStatus(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {
            $data = $request->all();

            if ($data['status'] == 'Active') { // $data['status'] comes from the 'data' object inside the $.ajax() method    // reverse the 'status' from (ative/inactive) 0 to 1 and 1 to 0 (and vice versa)
                $status = 0;
            } else {
                $status = 1;
            }


            ProductsAttribute::where('id', $data['attribute_id'])->update(['status' => $status]); // $data['attribute_id'] comes from the 'data' object inside the $.ajax() method

            return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                'status'       => $status,
                'attribute_id' => $data['attribute_id']
            ]);
        }
        return view('admin.attributes.add_edit_attributes', compact('product', 'logos', 'headerLogo'));
    }

    public function editAttributes(Request $request)
    {
        Session::put('page', 'products');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);

            foreach ($data['attributeId'] as $key => $attribute) {
                if (!empty($attribute)) {
                    ProductsAttribute::where([
                        'id' => $data['attributeId'][$key]
                    ])->update([
                        'price' => $data['price'][$key],
                        'stock' => $data['stock'][$key]
                    ]);
                }
            }

            return redirect()->back()->with('success_message', 'Book Attributes have been updated successfully!', 'logos');
            return view('admin.attributes.add_edit_attributes', compact('product', 'logos', 'headerLogo'));
        }
    }

    public function addImages(Request $request, $id)
    { // $id is the URL Paramter (slug) passed from the URL
        $adminType = Auth::guard('admin')->user()->type;

        Session::put('page', 'products');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $product = Product::select('id', 'product_name', 'product_isbn', 'product_price', 'product_image')->with('images')->find($id); // with('images') is the relationship method name in the Product.php model


        if ($request->isMethod('post')) { // if the <form> is submitted
            $data = $request->all();
            // dd($data);

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                // dd($images);

                foreach ($images as $key => $image) {
                    // Uploading the images:
                    // Generate Temp Image
                    $image_tmp = Image::make($image);

                    // Get image name
                    $image_name = $image->getClientOriginalName();
                    // dd($image_tmp);

                    // Get image extension
                    $extension = $image->getClientOriginalExtension();

                    // Generate a new random name for the uploaded image (to avoid that the image might get overwritten if its name is repeated)
                    $imageName = $image_name . rand(111, 99999) . '.' . $extension; // e.g. 5954.png

                    // Assigning the uploaded images path inside the 'public' folder
                    // We will have three folders: small, medium and large, depending on the images sizes
                    $largeImagePath  = 'front/images/product_images/large/'  . $imageName; // 'large'  images folder
                    $mediumImagePath = 'front/images/product_images/medium/' . $imageName; // 'medium' images folder
                    $smallImagePath  = 'front/images/product_images/small/'  . $imageName; // 'small'  images folder

                    // Upload the image using the 'Intervention' package and save it in our THREE paths (folders) inside the 'public' folder
                    Image::make($image_tmp)->resize(1000, 1000)->save($largeImagePath);  // resize the 'large'  image size then store it in the 'large'  folder
                    Image::make($image_tmp)->resize(500,   500)->save($mediumImagePath); // resize the 'medium' image size then store it in the 'medium' folder
                    Image::make($image_tmp)->resize(250,   250)->save($smallImagePath);  // resize the 'small'  image size then store it in the 'small'  folder

                    // Insert the image name in the database table `products_images`
                    $image = new ProductsImage;

                    $image->image      = $imageName;
                    $image->product_id = $id;
                    $image->status     = 1;

                    $image->save();
                }
            }

            return redirect()->back()->with('success_message', 'Book Images have been added successfully!');
        }


        return view('admin.images.add_images')->with(compact('product', 'logos', 'headerLogo', 'adminType'));
    }

    public function updateImageStatus(Request $request)
    { // Update Image Status using AJAX in add_images.blade.php
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) { // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)
            // dd($data);

            if ($data['status'] == 'Active') { // $data['status'] comes from the 'data' object inside the $.ajax() method    // reverse the 'status' from (ative/inactive) 0 to 1 and 1 to 0 (and vice versa)
                $status = 0;
            } else {
                $status = 1;
            }


            ProductsImage::where('id', $data['image_id'])->update(['status' => $status]); // $data['image_id'] comes from the 'data' object inside the $.ajax() method

            return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                'status'   => $status,
                'image_id' => $data['image_id']
            ]);
        }
        return view('admin.images.add_images', compact('product', 'logos', 'headerLogo'));
    }

    public function deleteImage($id)
    { // AJAX call from admin/js/custom.js    // Delete the product image from BOTH SERVER (FILESYSTEM) & DATABASE    // $id is passed as a Route Parameter
        // Get the product image record stored in the database

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $productImage = ProductsImage::select('image')->where('id', $id)->first();
        // dd($productImage);

        // Get the product image three paths on the server (filesystem) ('small', 'medium' and 'large' folders)
        $small_image_path  = 'front/images/product_images/small/';
        $medium_image_path = 'front/images/product_images/medium/';
        $large_image_path  = 'front/images/product_images/large/';

        // Delete the product images on server (filesystem) (from the the THREE folders)
        // First: Delete from the 'small' folder
        if (file_exists($small_image_path . $productImage->image)) {
            unlink($small_image_path . $productImage->image);
        }

        // Second: Delete from the 'medium' folder
        if (file_exists($medium_image_path . $productImage->image)) {
            unlink($medium_image_path . $productImage->image);
        }


        if (file_exists($large_image_path . $productImage->image)) {
            unlink($large_image_path . $productImage->image);
        }

        ProductsImage::where('id', $id)->delete();

        $message = 'Book Image has been deleted successfully!';

        return redirect()->back()->with('success_message', $message, 'logos');
        return view('admin.images.add_images', compact('product', 'logos', 'headerLogo'));
    }

    public function deleteAttribute($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        ProductsAttribute::where('id', $id)->delete();

        $message = 'Book Attribute has been deleted successfully!';

        return redirect()->back()->with('success_message', $message, 'logos');
        return view('admin.attributes.add_edit_attributes', compact('product', 'logos', 'headerLogo'));
    }

    public function deleteProductVideo($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();


        $message = 'Book Video has been deleted successfully!';

        return redirect()->back()->with('success_message', $message, 'logos');
        return view('admin.products.add_edit_product', compact('product', 'logos', 'headerLogo'));
    }

    public function lookupByIsbn(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|max:20'
        ]);

        $isbn = $request->isbn;

        $product = Product::with(['publisher', 'subject', 'edition', 'language', 'authors'])
            ->where('product_isbn', $isbn)
            ->first();

        if ($product) {
            return response()->json([
                "status" => true,
                "source" => "local",
                "message" => "Book found in local database",
                "data" => [
                    "product_name" => $product->product_name,
                    "category_id" => $product->category_id,
                    "description"  => $product->description,
                    "image"        => $product->product_image,
                    "product_price" => $product->product_price,
                    "product_description" => $product->description,

                    "publisher_id" => $product->publisher_id,
                    "subject_id"   => $product->subject_id,
                    "edition_id"   => $product->edition_id,
                    "language_id"  => $product->language_id,
                    "author_ids"   => $product->authors->pluck('id')->toArray(),
                ]
            ]);
        }

        $key = config('services.isbn.key');

        $response = Http::withHeaders([
            'Authorization' => $key
        ])->get("https://api2.isbndb.com/book/$isbn");

        if ($response->failed() || !isset($response['book'])) {
            return response()->json([
                "status"  => false,
                "message" => "Not found in local DB or API",
                "isbn"    => $isbn
            ], 404);
        }

        $book = $response['book'];

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

        $edition_id = null;
        if (!empty($book['edition'])) {
            $edition = Edition::firstOrCreate(['edition' => $book['edition']], ['status' => 1]);
            $edition_id = $edition->id;
        }

        $language_id = null;
        if (!empty($book['language'])) {
            $language = Language::firstOrCreate(['name' => $book['language']], ['status' => 1]);
            $language_id = $language->id;
        }

        $author_ids = [];
        if (!empty($book['authors'])) {
            foreach ($book['authors'] as $name) {
                $author = Author::firstOrCreate(['name' => $name], ['status' => 1]);
                $author_ids[] = $author->id;
            }
        }

        $product = Product::create([
            'product_name'        => $book['title'] ?? '',
            'product_isbn'        => $isbn,
            'description'         => $book['synopsis'] ?? '',
            'product_price'       => $book['msrp'] ?? 0,
            'product_image'       => $book['image'] ?? null,
            'publisher_id'        => $publisher_id,
            'subject_id'          => $subject_id,
            'edition_id'          => $edition_id,
            'language_id'         => $language_id,
            'status'              => 1
        ]);

        if (!empty($author_ids)) {
            $product->authors()->sync($author_ids);
        }

        return response()->json([
            "status"  => true,
            "source"  => "isbndb",
            "message" => "Fetched from ISBNdb",

            "data" => [
                "product_name" => $book['title'] ?? '',
                "description"  => $book['synopsis'] ?? '',
                "image"        => $book['image'] ?? '',
                "product_price" => $book['msrp'] ?? '',

                "publisher_id" => $publisher_id,
                "subject_id"   => $subject_id,
                "edition_id"   => $edition_id,
                "language_id"  => $language_id,
                "author_ids"   => $author_ids,
            ]
        ]);
    }

    public function nameSuggestions(Request $request)
    {
        try {

            $query = $request->input('query');

            if (!$query || strlen($query) < 2) {
                return response()->json([
                    'status' => true,
                    'data' => []
                ]);
            }

            $books = Product::where('product_name', 'LIKE', '%' . $query . '%')
                ->limit(10)
                ->get(['id', 'product_name', 'product_isbn']);

            return response()->json([
                'status' => true,
                'data'   => $books
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteProduct($id)
    {
        $user = Auth::guard('admin')->user();
        if (!$user->can('delete_products')) {
            return redirect()->back()->with('error_message', 'You do not have permission to delete products.');
        }

        $product = Product::findOrFail($id);

        // If vendor, check if they own this product (via attributes)
        if ($user->type === 'vendor') {
            $hasAttribute = ProductsAttribute::where('product_id', $id)
                ->where('vendor_id', $user->vendor_id)
                ->exists();
            if (!$hasAttribute) {
                return redirect()->back()->with('error_message', 'Unauthorized action for this product.');
            }
        }

        // Delete product image if exists
        $small_image_path  = 'front/images/product_images/small/';
        $medium_image_path = 'front/images/product_images/medium/';
        $large_image_path  = 'front/images/product_images/large/';

        if (!empty($product->product_image)) {
            if (file_exists($small_image_path . $product->product_image)) {
                unlink($small_image_path . $product->product_image);
            }
            if (file_exists($medium_image_path . $product->product_image)) {
                unlink($medium_image_path . $product->product_image);
            }
            if (file_exists($large_image_path . $product->product_image)) {
                unlink($large_image_path . $product->product_image);
            }
        }

        // Delete attributes and product
        ProductsAttribute::where('product_id', $id)->delete();
        $product->delete();

        return redirect()->back()->with('success_message', 'Book has been deleted successfully!');
    }
}
