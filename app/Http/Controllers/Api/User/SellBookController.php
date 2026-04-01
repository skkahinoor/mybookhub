<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\BookType;
use App\Models\Category;
use App\Models\Edition;
use App\Models\Language;
use App\Models\Notification;
use App\Models\OldBookCondition;
use App\Models\Mov;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\Publisher;
use App\Models\Section;
use App\Models\Subcategory;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class SellBookController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $basePath = url('front/images/product_images');

        $userProducts = Product::whereHas('attributes', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with([
                    'attributes' => function ($q) use ($user) {
                        $q->where('user_id', $user->id)->with('condition');
                    },
                    'category',
                    'subcategory',
                    'authors'
                ])->orderBy('created_at', 'desc')->get();

        $products = $userProducts->map(function ($product) use ($basePath) {
            $attribute = $product->attributes->first();

            return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'product_isbn' => $product->product_isbn,
                'product_price' => round($product->product_price),
                'user_product_price' => $attribute ? round($attribute->user_product_price) : null,
                'condition' => $product->condition,
                'old_book_condition' => $attribute && $attribute->condition ? [
                    'id' => $attribute->condition->id,
                    'name' => $attribute->condition->name,
                    'percentage' => $attribute->condition->percentage,
                ] : null,
                'status' => $attribute ? $attribute->status : 0,
                'admin_approved' => $attribute ? $attribute->admin_approved : 0,
                'stock' => $attribute ? $attribute->stock : 0,
                'show_contact' => $attribute ? $attribute->show_contact : 0,
                'contact_details_paid' => $attribute ? $attribute->contact_details_paid : 0,
                'platform_charge' => $attribute ? $attribute->platform_charge : 0,
                'is_sold' => $attribute ? $attribute->is_sold : 0,
                'image_urls' => [
                    'large' => $product->product_image ? $basePath . '/large/' . $product->product_image : null,
                    'medium' => $product->product_image ? $basePath . '/medium/' . $product->product_image : null,
                    'small' => $product->product_image ? $basePath . '/small/' . $product->product_image : null,
                ],
                'user_old_book_image' => $attribute && $attribute->user_old_book_image
                    ? $basePath . '/large/' . $attribute->user_old_book_image
                    : null,
                'user_old_book_video' => $attribute && $attribute->video_upload
                    ? url('front/videos/product_videos/' . $attribute->video_upload)
                    : null,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->category_name,
                ] : null,
                'subcategory' => $product->subcategory ? [
                    'id' => $product->subcategory->id,
                    'name' => $product->subcategory->subcategory_name,
                ] : null,
                'authors' => $product->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
                'created_at' => $product->created_at,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Sell books fetched successfully',
            'data' => $products,
        ]);
    }

    public function formData()
    {
        return response()->json([
            'status' => true,
            'message' => 'Form data fetched successfully',
            'data' => [
                'sections' => Section::where('status', 1)->get(['id', 'name']),
                'categories' => Category::where('status', 1)->get(['id', 'category_name', 'section_id']),
                'subcategories' => Subcategory::where('status', 1)->get(['id', 'subcategory_name']),
                'subjects' => Subject::where('status', 1)->get(['id', 'name']),
                'publishers' => Publisher::where('status', 1)->get(['id', 'name']),
                'authors' => Author::where('status', 1)->get(['id', 'name']),
                'editions' => Edition::get(['id', 'edition']),
                'languages' => Language::where('status', 1)->get(['id', 'name']),
                'book_types' => BookType::where('status', 1)->get(['id', 'book_type']),
                'conditions' => OldBookCondition::orderBy('percentage', 'desc')->get(['id', 'name', 'percentage']),
            ],
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $basePath = url('front/images/product_images');

        $product = Product::with(['authors', 'category', 'subcategory', 'subject', 'publisher', 'edition', 'section', 'bookType', 'language'])->find($id);

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found.'], 404);
        }

        $attribute = ProductsAttribute::where('product_id', $id)
            ->where('user_id', $user->id)
            ->with('condition')
            ->first();

        if (!$attribute) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to view this product.'], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product details fetched successfully',
            'data' => [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'product_isbn' => $product->product_isbn,
                'product_price' => round($product->product_price),
                'description' => $product->description,
                'condition' => $product->condition,
                'image_urls' => [
                    'large' => $product->product_image ? $basePath . '/large/' . $product->product_image : null,
                    'medium' => $product->product_image ? $basePath . '/medium/' . $product->product_image : null,
                    'small' => $product->product_image ? $basePath . '/small/' . $product->product_image : null,
                ],
                'section' => $product->section ? [
                    'id' => $product->section->id,
                    'name' => $product->section->name,
                ] : null,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->category_name,
                ] : null,
                'subcategory' => $product->subcategory ? [
                    'id' => $product->subcategory->id,
                    'name' => $product->subcategory->subcategory_name,
                ] : null,
                'subject' => $product->subject ? [
                    'id' => $product->subject->id,
                    'name' => $product->subject->name,
                ] : null,
                'publisher' => $product->publisher ? [
                    'id' => $product->publisher->id,
                    'name' => $product->publisher->name,
                ] : null,
                'edition' => $product->edition ? [
                    'id' => $product->edition->id,
                    'name' => $product->edition->edition,
                ] : null,
                'language' => $product->language ? [
                    'id' => $product->language->id,
                    'name' => $product->language->name,
                ] : null,
                'book_type' => $product->bookType ? [
                    'id' => $product->bookType->id,
                    'name' => $product->bookType->book_type,
                ] : null,
                'authors' => $product->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
                'attribute' => [
                    'id' => $attribute->id,
                    'old_book_condition_id' => $attribute->old_book_condition_id,
                    'user_product_price' => round($attribute->user_product_price),
                    'stock' => $attribute->stock,
                    'status' => $attribute->status,
                    'admin_approved' => $attribute->admin_approved,
                    'show_contact' => $attribute->show_contact,
                    'contact_details_paid' => $attribute->contact_details_paid,
                    'platform_charge' => $attribute->platform_charge,
                    'is_sold' => $attribute->is_sold,
                    'user_old_book_image' => $attribute->user_old_book_image
                        ? $basePath . '/large/' . $attribute->user_old_book_image
                        : null,
                    'user_old_book_video' => $attribute->video_upload
                        ? url('front/videos/product_videos/' . $attribute->video_upload)
                        : null,
                    'old_book_condition' => $attribute->condition ? [
                        'id' => $attribute->condition->id,
                        'name' => $attribute->condition->name,
                        'percentage' => $attribute->condition->percentage,
                    ] : null,
                    'price_details' => Product::getDiscountPriceDetailsByAttribute($attribute->id),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'section_id' => 'required|integer|exists:sections,id',
                'category_id' => 'required|integer|exists:categories,id',
                'subcategory_id' => 'required|integer|exists:subcategories,id',
                'product_name' => 'required|string|max:255',
                'product_price' => 'required|numeric|min:0',
                'user_product_price' => 'nullable|numeric|min:0',
                'old_book_condition_id' => 'required|exists:old_book_conditions,id',
                'language_id' => 'required|exists:languages,id',
                'user_old_book_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'video_upload' => 'nullable|mimes:mp4,mov,avi,wmv|max:51200',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
            }

            $data = $request->all();

            // Clean ISBN
            $cleanIsbn = null;
            if (!empty($data['product_isbn'])) {
                $cleanIsbn = preg_replace('/[^0-9X]/i', '', $data['product_isbn']);
                if (strlen($cleanIsbn) < 10 || strlen($cleanIsbn) > 13) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid ISBN. It must be between 10 and 13 characters.',
                    ], 422);
                }
            }

            // Deduplication: Check if product already exists
            $existingProducts = collect();
            if (!empty($cleanIsbn)) {
                $existingProducts = Product::whereRaw("REPLACE(REPLACE(product_isbn, ' ', ''), '-', '') = ?", [$cleanIsbn])->get();
            }

            $existingOldProduct = $existingProducts->firstWhere('condition', 'old');
            $existingNewProduct = $existingProducts->firstWhere('condition', 'new');

            if ($existingOldProduct) {
                $product = $existingOldProduct;
                $message = 'Old book added successfully from existing records!';

                // Ensure MRP is not downgraded. Only update if current is 0 or incoming is higher.
                if (isset($data['product_price'])) {
                    $newPrice = (float) $data['product_price'];
                    if ($product->product_price <= 0 || $newPrice > $product->product_price) {
                        $product->product_price = $newPrice;
                        $product->save();
                    }
                }

                $userHasIt = ProductsAttribute::where('product_id', $product->id)
                    ->where('user_id', $user->id)
                    ->first();

                if ($userHasIt) {
                    return response()->json(['status' => false, 'message' => 'You have already added this old book.'], 422);
                }
            } else {
                $product = new Product;
                $message = 'Product added successfully!';

                $product->section_id = $data['section_id'] ?? null;
                $product->category_id = $data['category_id'] ?? null;
                $product->subcategory_id = $data['subcategory_id'] ?? null;
                $product->subject_id = $data['subject_id'] ?? null;
                $product->language_id = $data['language_id'] ?? null;
                $product->publisher_id = $data['publisher_id'] ?? null;

                // Image handling
                if ($request->hasFile('user_old_book_image')) {
                    $image_tmp = $request->file('user_old_book_image');
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

                $product->condition = 'old';
                $product->product_name = $data['product_name'];
                $product->product_isbn = $data['product_isbn'] ?? null;
                // Initialise MRP. If the incoming price is suspected to be a discounted one (e.g. 300), 
                // the user should correct it later.
                $product->product_price = (float) ($data['product_price'] ?? 0);
                $product->edition_id = $data['edition_id'] ?? null;
                $product->description = $data['description'] ?? null;
                $product->meta_title = $data['meta_title'] ?? null;
                $product->meta_keywords = $data['meta_keywords'] ?? null;
                $product->meta_description = $data['meta_description'] ?? null;
                $product->book_type_id = $data['book_type_id'] ?? null;
                $product->status = 0; // Requires admin verification

                $product->save();

                $authorIds = $request->authors ?? $request->author_id;
                if (!empty($authorIds)) {
                    $product->authors()->sync((array) $authorIds);
                }
            }

            // Create/Update Product Attribute for user
            $attribute = ProductsAttribute::where('product_id', $product->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$attribute) {
                $attribute = new ProductsAttribute;
                $attribute->product_id = $product->id;
                $attribute->user_id = $user->id;
                $attribute->admin_type = 'user';
                $attribute->sku = 'BH-P' . $product->id . '-U' . $user->id;
            }

            $attribute->old_book_condition_id = $data['old_book_condition_id'] ?? null;
            $attribute->stock = 1;
            $attribute->product_discount = 0; // Default, will be updated below if condition exists
            $attribute->admin_approved = 0;
            $attribute->status = 0;

            if ($request->hasFile('user_old_book_image')) {
                $image_tmp = $request->file('user_old_book_image');
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
                    $attribute->user_old_book_image = $imageName;
                }
            }

            // Video Upload handling
            if ($request->hasFile('video_upload')) {
                $video_tmp = $request->file('video_upload');
                if ($video_tmp->isValid()) {
                    $video_name = pathinfo($video_tmp->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $video_tmp->getClientOriginalExtension();
                    $videoName = $video_name . '-' . rand(111, 99999) . '.' . $extension;
                    $videoPath = public_path('front/videos/product_videos/');
                    if (!file_exists($videoPath)) {
                        mkdir($videoPath, 0755, true);
                    }
                    $tempPath = $video_tmp->getPathname();
                    $destinationPath = $videoPath . $videoName;

                    // Attempt compression, fallback to simple move if it fails (e.g. ffmpeg not found)
                    if (!Helper::compressVideo($tempPath, $destinationPath)) {
                        $video_tmp->move($videoPath, $videoName);
                    }
                    $attribute->video_upload = $videoName;
                }
            }

            // Explicitly calculate selling price (user_product_price) based on condition
            if (!empty($data['old_book_condition_id'])) {
                $condition = OldBookCondition::find($data['old_book_condition_id']);
                if ($condition) {
                    $attribute->product_discount = $condition->percentage; // Store condition percentage

                    // Priority: Use the price provided by the frontend if available, else calculate it
                    if (isset($data['user_product_price'])) {
                        $attribute->user_product_price = round((float) $data['user_product_price']);
                    } else {
                        // Crucial: Use the HIGHEST known MRP to avoid "double calculation" (600 -> 300 -> 150)
                        $mrp = max((float) $product->product_price, (float) ($data['product_price'] ?? 0));
                        if ($mrp > 0) {
                            $attribute->user_product_price = round(($mrp * $condition->percentage) / 100);
                        } else {
                            $attribute->user_product_price = 0;
                        }
                    }
                } else {
                    $attribute->user_product_price = $data['user_product_price'] ?? $product->product_price;
                    $attribute->product_discount = 100; // Default to 100% of MRP if condition not found
                }
            } else {
                $attribute->user_product_price = $data['user_product_price'] ?? $product->product_price;
                $attribute->product_discount = 100;
            }

            $attribute->save();

            // Notifications
            Notification::create([
                'type' => 'product_added',
                'title' => 'New Product Added by User',
                'message' => "Student '{$user->name}' added a new old book '{$product->product_name}' (ISBN: {$product->product_isbn}).",
                'related_id' => $product->id,
                'related_type' => 'App\Models\Product',
                'vendor_id' => null,
                'is_read' => false,
            ]);

            Notification::create([
                'type' => 'sell_book_submitted',
                'title' => 'Sell request submitted',
                'message' => "Your listing for '{$product->product_name}' has been submitted and is under review. We will notify you once it is approved.",
                'related_id' => (int) $user->id,
                'related_type' => \App\Models\User::class,
                'is_read' => false,
            ]);

            return response()->json([
                'status' => true,
                'message' => $message . ' Awaiting admin verification.',
                'data' => [
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $product = Product::find($id);
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found.'], 404);
            }

            $attribute = ProductsAttribute::where('product_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$attribute) {
                return response()->json(['status' => false, 'message' => 'You do not have permission to edit this product.'], 403);
            }

            $validator = Validator::make($request->all(), [
                'section_id' => 'required|integer|exists:sections,id',
                'category_id' => 'required|integer|exists:categories,id',
                'subcategory_id' => 'required|integer|exists:subcategories,id',
                'product_name' => 'required|string|max:255',
                'product_price' => 'required|numeric|min:0',
                'user_product_price' => 'nullable|numeric|min:0',
                'old_book_condition_id' => 'required|exists:old_book_conditions,id',
                'language_id' => 'required|exists:languages,id',
                'user_old_book_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'video_upload' => 'nullable|mimes:mp4,mov,avi,wmv|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
            }

            $data = $request->all();

            // Clean ISBN
            if (!empty($data['product_isbn'])) {
                $cleanIsbn = preg_replace('/[^0-9X]/i', '', $data['product_isbn']);
                if (strlen($cleanIsbn) < 10 || strlen($cleanIsbn) > 13) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid ISBN. It must be between 10 and 13 characters.',
                    ], 422);
                }
            }

            // Update product details
            $product->section_id = $data['section_id'] ?? null;
            $product->category_id = $data['category_id'] ?? null;
            $product->subcategory_id = $data['subcategory_id'] ?? null;
            $product->subject_id = $data['subject_id'] ?? null;
            $product->language_id = $data['language_id'] ?? null;
            $product->publisher_id = $data['publisher_id'] ?? null;
            $product->product_name = $data['product_name'];
            $product->product_isbn = $data['product_isbn'] ?? null;
            // Only update MRP if the new value is higher than the existing one (prevents 600 -> 300)
            $newPrice = (float) $data['product_price'];
            if ($product->product_price <= 0 || $newPrice > $product->product_price) {
                $product->product_price = $newPrice;
            }
            $product->edition_id = $data['edition_id'] ?? null;
            $product->description = $data['description'] ?? null;
            $product->meta_title = $data['meta_title'] ?? null;
            $product->meta_keywords = $data['meta_keywords'] ?? null;
            $product->meta_description = $data['meta_description'] ?? null;
            $product->book_type_id = $data['book_type_id'] ?? null;

            // Image handling for product
            if ($request->hasFile('user_old_book_image')) {
                $image_tmp = $request->file('user_old_book_image');
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

            $product->save();

            $authorIds = $request->authors ?? $request->author_id;
            if (!empty($authorIds)) {
                $product->authors()->sync((array) $authorIds);
            }

            // Update attribute
            $attribute->old_book_condition_id = $data['old_book_condition_id'] ?? null;
            $attribute->admin_approved = 0; // Re-submit for approval
            $attribute->status = 0;

            if ($request->hasFile('user_old_book_image')) {
                $image_tmp = $request->file('user_old_book_image');
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
                    $attribute->user_old_book_image = $imageName;
                }
            }

            // Video Upload handling
            if ($request->hasFile('video_upload')) {
                $video_tmp = $request->file('video_upload');
                if ($video_tmp->isValid()) {
                    $video_name = pathinfo($video_tmp->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $video_tmp->getClientOriginalExtension();
                    $videoName = $video_name . '-' . rand(111, 99999) . '.' . $extension;
                    $videoPath = public_path('front/videos/product_videos/');
                    if (!file_exists($videoPath)) {
                        mkdir($videoPath, 0755, true);
                    }
                    $tempPath = $video_tmp->getPathname();
                    $destinationPath = $videoPath . $videoName;

                    // Attempt compression, fallback to simple move if it fails (e.g. ffmpeg not found)
                    if (!Helper::compressVideo($tempPath, $destinationPath)) {
                        $video_tmp->move($videoPath, $videoName);
                    }
                    $attribute->video_upload = $videoName;
                }
            }

            // Explicitly calculate final selling price (user_product_price) based on condition
            if (!empty($data['old_book_condition_id'])) {
                $condition = OldBookCondition::find($data['old_book_condition_id']);
                if ($condition) {
                    $attribute->product_discount = $condition->percentage; // Store condition percentage

                    // Priority: Use provided selling price if available
                    if (isset($data['user_product_price'])) {
                        $attribute->user_product_price = round((float) $data['user_product_price']);
                    } else {
                        // Use the most accurate MRP available for fallback calculation
                        $mrp = max((float) $product->product_price, (float) ($data['product_price'] ?? 0));
                        if ($mrp > 0) {
                            $attribute->user_product_price = round(($mrp * $condition->percentage) / 100);
                        } else {
                            $attribute->user_product_price = 0;
                        }
                    }
                } else {
                    $attribute->user_product_price = $data['user_product_price'] ?? $product->product_price;
                    $attribute->product_discount = 100; // Default to 100% if condition not found
                }
            } else {
                $attribute->user_product_price = $data['user_product_price'] ?? $product->product_price;
                $attribute->product_discount = 100;
            }

            $attribute->save();

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully! Awaiting admin verification.',
                'data' => [
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $attribute = ProductsAttribute::where('product_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attribute) {
            return response()->json(['status' => false, 'message' => 'Product not found or you do not have permission.'], 404);
        }

        $attribute->delete();

        return response()->json([
            'status' => true,
            'message' => 'Your sell book listing has been removed successfully.',
        ]);
    }

    public function addPublisher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $existing = Publisher::where('name', $request->name)->first();
        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'Publisher already exists.',
                'data' => ['id' => $existing->id, 'name' => $existing->name],
            ], 422);
        }

        $publisher = Publisher::create([
            'name' => $request->name,
            'status' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Publisher added successfully.',
            'data' => ['id' => $publisher->id, 'name' => $publisher->name],
        ]);
    }

    public function getBookByIsbn(Request $request)
    {
        $isbn = $request->input('isbn');
        if (empty($isbn)) {
            return response()->json(['status' => false, 'message' => 'ISBN is required'], 422);
        }

        $cleanSearch = preg_replace('/[^0-9X]/i', '', $isbn);
        $basePath = url('front/images/product_images');

        $relations = ['publisher', 'edition', 'authors', 'category', 'subcategory', 'subject', 'section', 'bookType', 'language'];

        $product = Product::with($relations)
            ->where('product_isbn', $isbn)
            ->first();

        if (!$product && strlen($cleanSearch) > 0) {
            $product = Product::with($relations)
                ->where(function ($query) use ($isbn, $cleanSearch) {
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
                    'product_isbn' => $product->product_isbn,
                    'product_price' => $product->product_price,
                    'description' => $product->description,
                    'image_urls' => [
                        'large' => $product->product_image ? $basePath . '/large/' . $product->product_image : null,
                        'medium' => $product->product_image ? $basePath . '/medium/' . $product->product_image : null,
                        'small' => $product->product_image ? $basePath . '/small/' . $product->product_image : null,
                    ],
                    'section' => $product->section ? [
                        'id' => $product->section->id,
                        'name' => $product->section->name,
                    ] : null,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->category_name,
                    ] : null,
                    'subcategory' => $product->subcategory ? [
                        'id' => $product->subcategory->id,
                        'name' => $product->subcategory->subcategory_name,
                    ] : null,
                    'subject' => $product->subject ? [
                        'id' => $product->subject->id,
                        'name' => $product->subject->name,
                    ] : null,
                    'publisher' => $product->publisher ? [
                        'id' => $product->publisher->id,
                        'name' => $product->publisher->name,
                    ] : null,
                    'edition' => $product->edition ? [
                        'id' => $product->edition->id,
                        'name' => $product->edition->edition,
                    ] : null,
                    'language' => $product->language ? [
                        'id' => $product->language->id,
                        'name' => $product->language->name,
                    ] : null,
                    'book_type' => $product->bookType ? [
                        'id' => $product->bookType->id,
                        'name' => $product->bookType->book_type,
                    ] : null,
                    'authors' => $product->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
                ],
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

                $publisher = null;
                if (!empty($book['publisher'])) {
                    $publisher = Publisher::firstOrCreate(['name' => $book['publisher']], ['status' => 1]);
                }

                $subject = null;
                if (!empty($book['subjects'][0])) {
                    $subject = Subject::firstOrCreate(['name' => $book['subjects'][0]], ['status' => 1]);
                }

                $authors = [];
                if (!empty($book['authors'])) {
                    foreach ($book['authors'] as $name) {
                        $author = Author::firstOrCreate(['name' => $name], ['status' => 1]);
                        $authors[] = ['id' => $author->id, 'name' => $author->name];
                    }
                }

                return response()->json([
                    'status' => true,
                    'source' => 'isbndb',
                    'data' => [
                        'product_name' => $book['title'] ?? '',
                        'product_isbn' => $isbn,
                        'description' => $book['synopsis'] ?? '',
                        'product_price' => $book['msrp'] ?? 0,
                        'image_url' => $book['image'] ?? null,
                        'publisher' => $publisher ? ['id' => $publisher->id, 'name' => $publisher->name] : null,
                        'subject' => $subject ? ['id' => $subject->id, 'name' => $subject->name] : null,
                        'authors' => $authors,
                    ],
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
            'data' => $books,
        ]);
    }

    public function calculateCashback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $product_price = $request->product_price;
        $mov = Mov::where('price', '<=', $product_price)->orderBy('price', 'desc')->first();

        if ($mov) {
            $cashback = ($product_price * $mov->cashback_percentage) / 100;
            return response()->json([
                'status' => true,
                'message' => 'Cashback calculated successfully',
                'data' => [
                    'cashback_amount' => round($cashback),
                    'cashback_percentage' => $mov->cashback_percentage,
                    'applied_threshold' => $mov->price
                ]
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cashback not eligible for this price.',
            'data' => [
                'cashback_amount' => 0
            ]
        ]);
    }

    public function toggleSellFaster(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attribute_id' => 'required|exists:products_attributes,id',
            'show_contact' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $attributeId = $request->attribute_id;
        $showContact = $request->show_contact;

        $attribute = ProductsAttribute::where('user_id', Auth::id())->find($attributeId);
        if (!$attribute) {
            return response()->json(['status' => false, 'message' => 'Attribute not found.'], 404);
        }

        $attribute->show_contact = $showContact;

        // Calculate platform charge if enabling and not yet paid
        if ($showContact == 1 && $attribute->contact_details_paid == 0) {
            $commission = \App\Models\OldBookCommission::first();
            $percentage = $commission ? $commission->percentage : 0;
            $originalPrice = $attribute->product->product_price ?? 0;
            $attribute->platform_charge = round(($originalPrice * $percentage) / 100);
        }

        $attribute->save();

        return response()->json([
            'status' => true,
            'message' => $showContact == 1 ? 'Sell faster option enabled. Please pay the platform charge.' : 'Sell faster option disabled.',
            'platform_charge' => number_format($attribute->platform_charge, 2),
        ]);
    }

    public function createRazorpayOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attribute_id' => 'required|exists:products_attributes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $attributeId = $request->attribute_id;
        $attribute = ProductsAttribute::with('product')->where('user_id', Auth::id())->find($attributeId);

        if (!$attribute) {
            return response()->json(['status' => false, 'message' => 'Attribute not found.'], 404);
        }

        if ($attribute->platform_charge <= 0) {
            return response()->json(['status' => false, 'message' => 'Invalid platform charge amount.'], 400);
        }

        try {
            $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

            $razorpayOrder = $api->order->create([
                'receipt' => 'platform_charge_' . $attribute->id,
                'amount' => round($attribute->platform_charge * 100), // in paise
                'currency' => 'INR'
            ]);

            // Save Order ID
            $attribute->razorpay_order_id = $razorpayOrder['id'];
            $attribute->save();

            return response()->json([
                'status' => true,
                'order_id' => $razorpayOrder['id'],
                'amount' => $attribute->platform_charge,
                'key' => env('RAZORPAY_KEY_ID'),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone,
                'description' => "Platform Charge for " . $attribute->product->product_name
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Razorpay Error: ' . $e->getMessage()], 500);
        }
    }

    public function verifyPlatformChargePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attribute_id' => 'required|exists:products_attributes,id',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $attributeId = $request->attribute_id;
        $razorpay_payment_id = $request->razorpay_payment_id;
        $razorpay_order_id = $request->razorpay_order_id;
        $razorpay_signature = $request->razorpay_signature;

        $attribute = ProductsAttribute::where('user_id', Auth::id())->find($attributeId);

        if (!$attribute) {
            return response()->json(['status' => false, 'message' => 'Attribute not found.'], 404);
        }

        // Verify Signature
        try {
            $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
            $attributes = [
                'razorpay_order_id' => $razorpay_order_id,
                'razorpay_payment_id' => $razorpay_payment_id,
                'razorpay_signature' => $razorpay_signature
            ];
            $api->utility->verifyPaymentSignature($attributes);

            // Payment verified successfully
            $attribute->contact_details_paid = 1;
            $attribute->razorpay_payment_id = $razorpay_payment_id;
            $attribute->save();

            return response()->json(['status' => true, 'message' => 'Payment verified and platform charge paid successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Payment verification failed: ' . $e->getMessage()], 400);
        }
    }

    public function markAsSold(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attribute_id' => 'required|exists:products_attributes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $attributeId = $request->attribute_id;
        $attribute = ProductsAttribute::where('user_id', Auth::id())->find($attributeId);

        if (!$attribute) {
            return response()->json(['status' => false, 'message' => 'Attribute not found.'], 404);
        }

        // Delete video file if exists
        if (!empty($attribute->video_upload)) {
            $videoPath = public_path('front/videos/product_videos/' . $attribute->video_upload);
            if (file_exists($videoPath)) {
                unlink($videoPath);
            }
            $attribute->video_upload = null;
        }

        $attribute->is_sold = 1;
        $attribute->status = 0; // Hide from frontend after sold
        $attribute->save();

        return response()->json(['status' => true, 'message' => 'Book marked as sold!']);
    }
}
