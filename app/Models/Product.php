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
        // Basic info
        'product_name',
        'product_isbn',
        'description',
        'product_price',
        'product_image',
        'condition',

        // Relations
        'section_id',
        'category_id',
        'publisher_id',
        'subject_id',
        'edition_id',
        'language_id',

        // SEO
        'meta_title',
        'meta_keywords',
        'meta_description',

        // Flags
        'status'
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

    // A static method (to be able to be called directly without instantiating an object in index.blade.php) to determine the final price of a product because a product can have a discount from TWO things: either a `CATEGORY` discount or `PRODUCT` discount
    public static function getDiscountPrice($product_id, $vendor_id = null)
    {
        $product = self::select('product_price', 'category_id')->find($product_id);

        if (!$product) {
            return 0;
        }

        $originalPrice = (float) $product->product_price;

        $attribute = ProductsAttribute::when($vendor_id, function ($q) use ($vendor_id) {
            $q->where('vendor_id', $vendor_id);
        })
            ->where('product_id', $product_id)
            ->where('status', 1)
            ->first();

        $productDiscount = $attribute ? (float) $attribute->product_discount : 0;

        $categoryDiscount = Category::where('id', $product->category_id)
            ->value('category_discount') ?? 0;

        if ($productDiscount > 0) {
            $finalPrice = $originalPrice - ($originalPrice * $productDiscount / 100);
        } elseif ($categoryDiscount > 0) {
            $finalPrice = $originalPrice - ($originalPrice * $categoryDiscount / 100);
        } else {
            $finalPrice = $originalPrice;
        }

        return round($finalPrice);
    }

    public static function getDiscountPriceDetails($product_id, $vendor_id = null)
    {
        $product = self::select('product_price', 'category_id')->find($product_id);

        if (!$product) {
            return [
                'product_price' => 0,
                'final_price'   => 0,
                'discount'      => 0,
            ];
        }

        $originalPrice = (float) $product->product_price;

        $attribute = ProductsAttribute::when($vendor_id, function ($q) use ($vendor_id) {
            $q->where('vendor_id', $vendor_id);
        })
            ->where('product_id', $product_id)
            ->where('status', 1)
            ->first();

        $productDiscount = $attribute ? (float) $attribute->product_discount : 0;

        $categoryDiscount = Category::where('id', $product->category_id)
            ->value('category_discount') ?? 0;

        if ($productDiscount > 0) {
            $finalPrice = $originalPrice - ($originalPrice * $productDiscount / 100);
        } elseif ($categoryDiscount > 0) {
            $finalPrice = $originalPrice - ($originalPrice * $categoryDiscount / 100);
        } else {
            $finalPrice = $originalPrice;
        }

        return [
            'product_price' => round($originalPrice),
            'final_price'   => round($finalPrice),
            'discount'      => round($originalPrice - $finalPrice),
        ];
    }

    public static function getDiscountAttributePrice($product_id, $vendor_id)
    {
        $attribute = ProductsAttribute::where('product_id', $product_id)
            ->where('vendor_id', $vendor_id)
            ->where('status', 1)
            ->first();

        if (!$attribute) {
            return self::getDiscountPriceDetails($product_id, $vendor_id);
        }

        $originalPrice = (float) Product::where('id', $product_id)
            ->value('product_price');

        $productDiscount = (float) $attribute->product_discount;

        $categoryDiscount = Category::where(
            'id',
            Product::where('id', $product_id)->value('category_id')
        )->value('category_discount') ?? 0;

        if ($productDiscount > 0) {
            $finalPrice = $originalPrice - ($originalPrice * $productDiscount / 100);
        } elseif ($categoryDiscount > 0) {
            $finalPrice = $originalPrice - ($originalPrice * $categoryDiscount / 100);
        } else {
            $finalPrice = $originalPrice;
        }

        return [
            'product_price' => round($originalPrice),
            'final_price'   => round($finalPrice),
            'discount'      => round($originalPrice - $finalPrice),
        ];
    }

    public static function getDiscountPriceDetailsByAttribute($attribute_id)
    {
        $attribute = ProductsAttribute::with('product')->where('id', $attribute_id)
            ->where('status', 1)
            ->first();

        if (!$attribute || !$attribute->product) {
            return [
                'product_price' => 0,
                'final_price'   => 0,
                'discount'      => 0,
            ];
        }

        $originalPrice = (float) $attribute->product->product_price;
        $productDiscount = (float) $attribute->product_discount;

        // Category discount fallback
        $categoryDiscount = Category::where('id', $attribute->product->category_id)
            ->value('category_discount') ?? 0;

        if ($productDiscount > 0) {
            $finalPrice = $originalPrice - ($originalPrice * $productDiscount / 100);
        } elseif ($categoryDiscount > 0) {
            $finalPrice = $originalPrice - ($originalPrice * $categoryDiscount / 100);
        } else {
            $finalPrice = $originalPrice;
        }

        return [
            'product_price' => round($originalPrice),
            'final_price'   => round($finalPrice),
            'discount'      => round($originalPrice - $finalPrice),
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
