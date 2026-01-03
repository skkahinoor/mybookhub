<?php

namespace App\Models;

use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        // add other fillable fields as needed
        'location',
    ];

    // Every 'product' belongs to a 'section'
    public function section()
    {
        return $this->belongsTo('App\Models\Section', 'section_id'); // 'section_id' is the foreign key
    }

    // Every 'product' belongs to a 'category'
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id'); // 'category_id' is the foreign key
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function edition()
    {
        return $this->belongsTo(Edition::class, 'edition_id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_product');
    }

    // public function attributes()
    // {
    //     return $this->hasMany('App\Models\ProductsAttribute');
    // }
    public function attributes()
    {
        return $this->hasMany(ProductsAttribute::class, 'product_id');
    }

    public function firstAttribute()
    {
        return $this->hasOne(ProductsAttribute::class, 'product_id')->latest();
    }

    // Every product has many images
    public function images()
    {
        return $this->hasMany('App\Models\ProductsImage');
    }

    // Relationship of a Product `products` table with Vendor `vendors` table (every product belongs to a vendor)
    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor', 'vendor_id')->with('vendorbusinessdetails'); // 'vendor_id' is the Foreign Key of the Relationship
    }

    // A static method (to be able to be called directly without instantiating an object in index.blade.php) to determine the final price of a product because a product can have a discount from TWO things: either a `CATEGORY` discount or `PRODUCT` discout
    public static function getDiscountPrice($product_id)
    { // this method is called in front/index.blade.php
        // Get the product PRICE, DISCOUNT and CATEGORY ID
        $productDetails = Product::select('product_price', 'product_discount', 'category_id')->where('id', $product_id)->first();

        if (! $productDetails) {
            return 0; // Return 0 if product not found
        }

        $productDetails = json_decode(json_encode($productDetails), true); // convert the object to an array

        // Get the product category discount `category_discount` from `categories` table using its `category_id` in `products` table
        $categoryDetails = Category::select('category_discount')->where('id', $productDetails['category_id'])->first();

        if (! $categoryDetails) {
            $categoryDetails = ['category_discount' => 0];
        } else {
            $categoryDetails = json_decode(json_encode($categoryDetails), true); // convert the object to an array
        }

        $originalPrice    = $productDetails['product_price'];
        $productDiscount  = $productDetails['product_discount'] ?? 0;
        $categoryDiscount = $categoryDetails['category_discount'] ?? 0;

        // Calculate the highest discount (product discount takes precedence over category discount)
        if ($productDiscount > 0) {
            // if there's a PRODUCT discount on the product itself
            $discounted_price = $originalPrice - ($originalPrice * $productDiscount / 100);
        } else if ($categoryDiscount > 0) {
            // if there's NO a PRODUCT discount, but there's a CATEGORY discount
            $discounted_price = $originalPrice - ($originalPrice * $categoryDiscount / 100);
        } else {
            // there's no discount on neither `product_discount` (in `products` table) nor `category_discount` (in `categories` table)
            $discounted_price = 0;
        }

        return round($discounted_price, 2); // Round to 2 decimal places
    }

    public static function getDiscountAttributePrice($product_id, $size = null)
    {
        // If a size is provided and there is an attribute-level price, prefer it.
        $attribute = null;
        if (!empty($size)) {
            $attribute = \App\Models\ProductsAttribute::where([
                'product_id' => $product_id,
                'size'       => $size,
            ])->first();
        }

        // If attribute exists, compute discount against attribute price using product/category discounts
        if ($attribute) {
            $attributePrice = (float) $attribute->price;

            $productDetails = Product::select('product_discount', 'category_id')->where('id', $product_id)->first();
            $productDiscount = $productDetails ? (float) ($productDetails->product_discount ?? 0) : 0;

            $categoryDiscount = 0;
            if ($productDetails) {
                $categoryDiscount = (float) (Category::where('id', $productDetails->category_id)->value('category_discount') ?? 0);
            }

            if ($productDiscount > 0) {
                $finalPrice = $attributePrice - ($attributePrice * $productDiscount / 100);
            } elseif ($categoryDiscount > 0) {
                $finalPrice = $attributePrice - ($attributePrice * $categoryDiscount / 100);
            } else {
                $finalPrice = $attributePrice;
            }

            $discount = max(0, $attributePrice - $finalPrice);

            return [
                'product_price' => round($attributePrice, 2),
                'final_price'   => round($finalPrice, 2),
                'discount'      => round($discount, 2),
            ];
        }

        // Fallback: when size not provided or no attribute exists, use the product-level price rules
        $details = self::getDiscountPriceDetails($product_id);
        return [
            'product_price' => $details['product_price'] ?? 0,
            'final_price'   => $details['final_price'] ?? 0,
            'discount'      => $details['discount'] ?? 0,
        ];
    }

    public static function getDiscountPriceDetails($product_id)
    {
        $product = Product::select('product_price', 'product_discount', 'category_id')->where('id', $product_id)->first();
        if (!$product) {
            return [
                'product_price' => 0,
                'final_price'   => 0,
                'discount'      => 0,
            ];
        }
        $product = $product->toArray();
        $category = Category::select('category_discount')->where('id', $product['category_id'])->first();
        $category_discount = $category ? $category->category_discount : 0;

        $original_price = $product['product_price'];
        $product_discount = $product['product_discount'] ?? 0;

        if ($product_discount > 0) {
            $final_price = $original_price - ($original_price * $product_discount / 100);
            $discount = $original_price - $final_price;
        } elseif ($category_discount > 0) {
            $final_price = $original_price - ($original_price * $category_discount / 100);
            $discount = $original_price - $final_price;
        } else {
            $final_price = $original_price;
            $discount = 0;
        }

        return [
            'product_price' => $original_price,
            'final_price'   => round($final_price, 2),
            'discount'      => round($discount, 2),
        ];
    }


    public static function isProductNew($product_id)
    {
        // Get the last (latest) three 3 added products ids
        $productIds = Product::select('id')->where('status', 1)->orderBy('id', 'Desc')->limit(3)->pluck('id');
        $productIds = json_decode(json_encode($productIds, true));

        if (in_array($product_id, $productIds)) { // if the passed in $product_id is in the array of the last (latest) 3 added products ids
            $isProductNew = 'Yes';
        } else {
            $isProductNew = 'No';
        }

        return $isProductNew;
    }

    public static function getProductImage($product_id)
    {
        $getProductImage = Product::select('product_image')->where('id', $product_id)->first();

        if (!$getProductImage) {
            return '';
        }

        $getProductImage = $getProductImage->toArray();

        return $getProductImage['product_image'] ?? '';
    }


    // Note: We need to prevent orders (upon checkout and payment) of the 'disabled' products (`status` = 0), where the product ITSELF can be disabled in admin/products/products.blade.php (by checking the `products` database table) or a product's attribute (`stock`) can be disabled in 'admin/attributes/add_edit_attributes.blade.php' (by checking the `products_attributes` database table). We also prevent orders of the out of stock / sold-out products (by checking the `products_attributes` database table)
    public static function getProductStatus($product_id)
    {
        $getProductStatus = Product::select('status')->where('id', $product_id)->first();

        return $getProductStatus->status;
    }

    // Delete a product from Cart if it's 'disabled' (`status` = 0) or it's out of stock (sold out)
    public static function deleteCartProduct($product_id)
    {
        Cart::where('product_id', $product_id)->delete();
    }
}
